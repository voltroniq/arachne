<?php

// A simple error handler that catches exceptions and converts them to PSR-7 responses.

namespace Arachne\Http\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Nyholm\Psr7\Response;
use Psr\Log\LoggerInterface;

final class ErrorMiddleware implements MiddlewareInterface
{
    private ?LoggerInterface $logger;

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (\Throwable $e) {
            if ($this->logger) {
                $this->logger->error('Unhandled exception: ' . $e->getMessage(), ['exception' => $e]);
            }
            $body = sprintf("Internal Server Error: %s", $e->getMessage());
            return new Response(500, ['Content-Type' => 'text/plain'], $body);
        }
    }
}
