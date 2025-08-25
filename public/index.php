<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Arachne\Container\ContainerFactory;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;
use Arachne\Http\Kernel;
use Arachne\Http\Middleware\FastRouteMiddleware;
use Arachne\Http\Middleware\ErrorMiddleware;
use Arachne\Async\Scheduler;

// Create container
$container = \Arachne\Container\ContainerFactory::create([
    // override or add definitions if needed
]);

// Build PSR-7 ServerRequest from globals
$psr17Factory = new Psr17Factory();
$creator = new ServerRequestCreator(
    $psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory
);
$request = $creator->fromGlobals();

// Build FastRoute dispatcher
$dispatcher = simpleDispatcher(function(RouteCollector $r) {
    $routes = require __DIR__ . '/../config/routes.php';
    $routes($r);
});

// Register Dispatcher and middleware in container (simple closure defs)
$container->set(FastRoute\Dispatcher::class, $dispatcher);

// Minimum middleware queue
$middlewareQueue = [
    new ErrorMiddleware(null), // or $container->get(LoggerInterface::class)
    new FastRouteMiddleware($dispatcher, $container)
];

// Kernel
$kernel = new Kernel($container, $middlewareQueue);

// Now handle request
$response = $kernel->handle($request);

// Emit response
$emitter = new SapiEmitter();
$emitter->emit($response);