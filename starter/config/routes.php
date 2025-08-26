<?php

use FastRoute\RouteCollector;
use Starter\Controllers\WelcomeController;

return function(RouteCollector $r) {
    $r->addRoute('GET', '/', [WelcomeController::class, 'index']);
};