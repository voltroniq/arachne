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

final class FastRouteMiddleware implements MiddlewareInterface
{
    private Dispatcher $dispatcher;
    private ContainerInterface $container;

    public function __construct(Dispatcher $dispatcher, ContainerInterface $container)
    {
        $this->dispatcher = $dispatcher;
        $this->container = $container;
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
                $handlerDef = $routeInfo[1]; // typically [ControllerClass, 'method']
                $vars = $routeInfo[2];

                if (is_array($handlerDef) && is_string($handlerDef[0])) {
                    // resolve controller from container
                    $controller = $this->container->get($handlerDef[0]);
                    $methodName = $handlerDef[1];

                    // convention: controller method signature -> (ServerRequestInterface $request, array $args = [])
                    return $controller->$methodName($request, $vars);
                }

                if (is_callable($handlerDef)) {
                    return $handlerDef($request, $vars);
                }

                return new Response(500, [], 'Invalid route handler');
        }

        return new Response(500, [], 'Unexpected routing error');
    }
}
