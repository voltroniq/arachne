<?php
use FastRoute\RouteCollector;
use Arachne\Controllers\HomeController;

return function(RouteCollector $r) {
    // Homepage route
    $r->addRoute('GET', '/', [HomeController::class, 'index']);

    // Async demo route
    $r->addRoute('GET', '/async', [HomeController::class, 'asyncExample']);
};