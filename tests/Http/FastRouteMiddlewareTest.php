<?php
declare(strict_types=1);

namespace Tests\Http;

use PHPUnit\Framework\TestCase;
use function FastRoute\simpleDispatcher;
use FastRoute\RouteCollector;
use Nyholm\Psr7\ServerRequest;
use Nyholm\Psr7\Response;
use Arachne\Http\Middleware\FastRouteMiddleware;
use Arachne\Async\Scheduler;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Tests FastRouteMiddleware dispatching and controller invocation.
 */
final class FastRouteMiddlewareTest extends TestCase
{
    public function testControllerIsInvokedAndReturnsResponse(): void
    {
        // Create a FastRoute dispatcher with a route for Test\DummyController::index
        $dispatcher = simpleDispatcher(function (RouteCollector $r) {
            $r->addRoute('GET', '/dummy', ['Test\\DummyController', 'index']);
        });

        // Create a minimal container that returns an object with an index(...) method.
        // We return an anonymous controller with the expected signature.
        $container = new class implements ContainerInterface {
            public function get($id)
            {
                return new class {
                    public function index(ServerRequestInterface $request, ?\Arachne\Async\Scheduler $scheduler = null)
                    {
                        return new \Nyholm\Psr7\Response(200, [], 'dummy-ok');
                    }
                };
            }

            // PSR-11 requires has($id): bool
            public function has($id): bool
            {
                return true;
            }
        };

        // Create scheduler (optional for the controller)
        $scheduler = new Scheduler();

        // Instantiate the middleware with dispatcher, container and scheduler
        $middleware = new FastRouteMiddleware($dispatcher, $container, $scheduler);

        // Build a PSR-7 ServerRequest for the route
        $request = new ServerRequest('GET', '/dummy');

        // A request handler that should NOT be invoked by this middleware (fails the test if called)
        $handler = new class implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new Response(500, [], 'not-called');
            }
        };

        // Execute middleware
        $response = $middleware->process($request, $handler);

        // Assertions
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('dummy-ok', (string) $response->getBody());
    }
}