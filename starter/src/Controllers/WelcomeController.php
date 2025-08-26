<?php
declare(strict_types=1);

namespace Starter\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Nyholm\Psr7\Response;
use Arachne\Async\Scheduler;

final class WelcomeController
{
    public function index(ServerRequestInterface $request, ?Scheduler $scheduler = null): Response
    {
        if ($scheduler) {
            $scheduler->enqueue(function() {
                // background task example
            });
        }

        return new Response(200, [], '<h1>Starter App — Welcome</h1>');
    }
}