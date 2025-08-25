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
        // Get the URI path from the incoming request (e.g., "/home")
        $uri = $request->getUri()->getPath();

        // Get the HTTP method (e.g., "GET", "POST") from the incoming request
        $method = $request->getMethod();

        // Dispatch the request to the route dispatcher to match a route
        $routeInfo = $this->dispatcher->dispatch($method, rawurldecode($uri));

        // Handle the different outcomes of the route dispatch
        switch ($routeInfo[0]) {
            // Route not found: If no matching route is found, return a 404 error
            case Dispatcher::NOT_FOUND:
                return new Response(404, [], 'Not Found');
            
            // Method not allowed: If the method (GET/POST) is not allowed on this route, return a 405 error
            case Dispatcher::METHOD_NOT_ALLOWED:
                return new Response(405, [], 'Method Not Allowed');
            
            // Route found: If a matching route is found, process the handler
            case Dispatcher::FOUND:
                // The route handler could be a controller method (e.g., [ControllerClass, 'method'])
                $handlerDef = $routeInfo[1]; // [ControllerClass, 'method']
                $vars = $routeInfo[2];       // Route parameters (like {id: 42})

                // If the handler is a controller method, resolve the controller from the container
                if (is_array($handlerDef) && is_string($handlerDef[0])) {
                    // Fetch the controller from the container
                    $controller = $this->container->get($handlerDef[0]);

                    // Get the method name to call on the controller
                    $methodName = $handlerDef[1];

                    // Call the controller method with the request and scheduler as arguments
                    // If no scheduler is passed, it will be null
                    return $controller->$methodName($request, $this->scheduler);
                }

                // If the handler is a callable (e.g., closure), invoke it directly
                if (is_callable($handlerDef)) {
                    return $handlerDef($request, $vars);
                }

                // If the handler is invalid (neither a controller nor callable), return a 500 error
                return new Response(500, [], 'Invalid route handler');
        }

        // If an unexpected error occurs during routing, return a 500 error response
        return new Response(500, [], 'Unexpected routing error');
    }
}