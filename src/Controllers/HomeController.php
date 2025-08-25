<?php
namespace Arachne\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Nyholm\Psr7\Response;
use Arachne\Async\Scheduler;

final class HomeController
{
    public function index(ServerRequestInterface $request, Scheduler $scheduler = null)
    {
        // If Scheduler is not auto-injected, create local.
        if ($scheduler === null) {
            $scheduler = new Scheduler();
        }

        // Demo: schedule a small fiber task
        $scheduler->create(function(Scheduler $s) {
            // do step 1
            $s->yieldControl();
            // do step 2
            $s->yieldControl();
            return "ok";
        });

        $scheduler->run();

        return new Response(200, ['Content-Type' => 'text/plain'], "Welcome to Arachne!");
    }
}