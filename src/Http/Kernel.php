<?php

// ServerRequestInterface and dispatches it through middleware (Relay).
// It pulls middleware definitions from config or from the DI container.

namespace Arachne\Http;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Container\ContainerInterface;
use Relay\Relay;

final class Kernel
{
    private ContainerInterface $container;
    private array $middlewareQueue;

    public function __construct(ContainerInterface $container, array $middlewareQueue = [])
    {
        $this->container = $container;
        $this->middlewareQueue = $middlewareQueue;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // Resolve middleware entries (strings/classes) into instances
        $resolved = [];
        foreach ($this->middlewareQueue as $mw) {
            if (is_string($mw) && $this->container->has($mw)) {
                $resolved[] = $this->container->get($mw);
            } elseif (is_callable($mw) || ($mw instanceof \Psr\Http\Server\MiddlewareInterface)) {
                $resolved[] = $mw;
            } else {
                // try to get from container or throw
                if (is_string($mw)) {
                    $resolved[] = $this->container->get($mw);
                } else {
                    throw new \InvalidArgumentException('Invalid middleware entry');
                }
            }
        }

        $relay = new Relay($resolved);
        return $relay->handle($request);
    }
}