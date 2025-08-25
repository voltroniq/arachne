<?php

use FastRoute\RouteCollector;
use Arachne\Controllers\HomeController;

return function (RouteCollector $r) {
    $r->addRoute('GET', '/', [HomeController::class, 'index']);
};
