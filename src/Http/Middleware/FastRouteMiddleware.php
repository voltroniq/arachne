<?php
declare(strict_types=1);

namespace Arachne\Http\Middleware;

use FastRoute\Dispatcher;
use Nyholm\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Arachne\Async\Scheduler;

/**
 * Middleware for routing HTTP requests using FastRoute.
 */
final class FastRouteMiddleware implements MiddlewareInterface
{
    private Dispatcher $dispatcher;
    private ContainerInterface $container;
    private ?Scheduler $scheduler;

    public function __construct(
        Dispatcher $dispatcher,
        ContainerInterface $container,
        ?Scheduler $scheduler = null
    ) {
        $this->dispatcher = $dispatcher;
        $this->container = $container;
        $this->scheduler = $scheduler;
    }

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
                $handlerDef = $routeInfo[1]; // e.g., [ControllerClass, 'method']
                $vars = $routeInfo[2];       // route parameters

                if (is_array($handlerDef) && is_string($handlerDef[0])) {
                    $controller = $this->container->get($handlerDef[0]);
                    $methodName = $handlerDef[1];

                    // Call controller method with $request and optional $scheduler
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