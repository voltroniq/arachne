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

// ------------------ Scheduler ------------------
$scheduler = new Scheduler();

// ------------------ Container ------------------
$container = ContainerFactory::create([]);

// ------------------ PSR-7 Request ------------------
$psr17Factory = new Psr17Factory();
$creator = new ServerRequestCreator(
    $psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory
);
$request = $creator->fromGlobals();

// ------------------ Routes ------------------
$dispatcher = simpleDispatcher(function(RouteCollector $r) {
    $routes = require __DIR__ . '/../config/routes.php';
    $routes($r);
});

// ------------------ Middleware ------------------
$middlewareQueue = [
    new ErrorMiddleware(null),
    new FastRouteMiddleware($dispatcher, $container, $scheduler)
];

// ------------------ Kernel ------------------
$kernel = new Kernel($container, $middlewareQueue);

// ------------------ Handle Request ------------------
$response = $kernel->handle($request);

// ------------------ Emit Response ------------------
$emitter = new SapiEmitter();
$emitter->emit($response);