<?php

// middleware maps routes (FastRoute dispatcher) to a PSR-15 compatible callable.
// Keeps the router/mapping logic separate and PSR-15-compatible.

namespace Arachne\Http\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use FastRoute\Dispatcher;
use Nyholm\Psr7\Response;
use Psr\Container\ContainerInterface;

/**
 * Middleware for routing requests using FastRoute.
 * It dispatches the request based on URI path and HTTP method,
 * and invokes the corresponding controller or handler.
 */
final class FastRouteMiddleware implements MiddlewareInterface
{
    /** @var Dispatcher $dispatcher FastRoute's Dispatcher for route dispatching */
    private Dispatcher $dispatcher;

    /** @var ContainerInterface $container The DI container to resolve controller dependencies */
    private ContainerInterface $container;

    /** @var ?\Arachne\Async\Scheduler $scheduler Optional scheduler for asynchronous tasks */
    private ?\Arachne\Async\Scheduler $scheduler;

    /**
     * FastRouteMiddleware constructor.
     * Initializes dispatcher, container, and optional scheduler.
     * 
     * @param Dispatcher $dispatcher FastRoute dispatcher to handle routing.
     * @param ContainerInterface $container DI container to resolve controller dependencies.
     * @param ?\Arachne\Async\Scheduler $scheduler Optional scheduler to handle async tasks.
     */
    public function __construct(Dispatcher $dispatcher, ContainerInterface $container, ?\Arachne\Async\Scheduler $scheduler = null)
    {
        $this->dispatcher = $dispatcher; // Assign dispatcher for routing requests
        $this->container = $container; // Assign container for fetching controllers
        $this->scheduler = $scheduler; // Optionally assign scheduler for async processing
    }

    /**
     * Processes the incoming request and dispatches it to the appropriate route handler.
     * 
     * @param ServerRequestInterface $request The HTTP request to be processed.
     * @param RequestHandlerInterface $handler The request handler that handles the request.
     * 
     * @return ResponseInterface Returns a response object with the result of the route handling.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uri = $request->getUri()->getPath();
        $method = $request->getMethod();

        $routeInfo = $this->dispatcher->dispatch($method, rawurldecode($uri));

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                return new Response(404, [], 'Not Found');

            case Dispatcher::METHOD_NOT_ALLOWED:
                return new Response(405, [], 'Method Not Allowed');

            case Dispatcher::FOUND:
                // <-- handlerDef is defined HERE
                $handlerDef = $routeInfo[1]; // e.g., [ControllerClass, 'method']
                $vars = $routeInfo[2];

                if (is_array($handlerDef) && is_string($handlerDef[0])) {
                    $controller = $this->container->get($handlerDef[0]);
                    $methodName = $handlerDef[1];

                    // Pass optional Scheduler
                    return $controller->$methodName($request, $this->scheduler);
                }

                if (is_callable($handlerDef)) {
                    return $handlerDef($request, $vars);
                }

                return new Response(500, [], 'Invalid route handler');
        }

        return new Response(500, [], 'Unexpected routing error');
    }
}