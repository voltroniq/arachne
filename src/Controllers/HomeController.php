<?php
namespace Arachne\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Nyholm\Psr7\Response;
use Arachne\Async\Scheduler;

class HomeController
{
    public function index(ServerRequestInterface $request, Scheduler $scheduler)
    {
        $scheduler->create(function(Scheduler $s) {
            // do small chunk of work, then yield
            // Example: compute, log or wait for some IO adapter
            // Simulate stepping:
            // first step
            // -> yield
            $s->yieldControl();

            // continued second step
            // -> yield again
            $s->yieldControl();

            return 'done';
        });

        // If you want tasks to run immediately in this request:
        $scheduler->run();

        return new Response(200, [], 'Hello from Arachne + Fibers (demo)');
    }
}