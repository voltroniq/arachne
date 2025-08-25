<?php
use FastRoute\RouteCollector;

return function(RouteCollector $r) {
    $r->get('/', [\Arachne\Controllers\HomeController::class, 'index']);
    // Add more routes...
};