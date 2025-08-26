<?php
declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

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

// Create scheduler
$scheduler = new Scheduler();

// Create container
$container = ContainerFactory::create([]);

// Build request
$psr17Factory = new Psr17Factory();
$creator = new ServerRequestCreator(
    $psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory
);
$request = $creator->fromGlobals();

// Routes
$dispatcher = simpleDispatcher(function(RouteCollector $r) {
    $routes = require __DIR__ . '/../../config/routes.php';
    $routes($r);
});

$middlewareQueue = [
    new ErrorMiddleware(null),
    new FastRouteMiddleware($dispatcher, $container, $scheduler),
];

$kernel = new Kernel($container, $middlewareQueue);
$response = $kernel->handle($request);
$emitter = new SapiEmitter();
$emitter->emit($response);