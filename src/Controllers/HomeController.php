<?php
declare(strict_types=1);

namespace Arachne\Controllers;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface;
use Arachne\Async\Scheduler;

final class HomeController
{
    /**
     * Example index action.
     */
    public function index(ServerRequestInterface $request, ?Scheduler $scheduler = null): Response
    {
        // Example async task
        if ($scheduler) {
            $scheduler->enqueue(function () {
                // You could run background async tasks here
            });
        }

        return new Response(200, [], "Welcome to Arachne!");
    }
}