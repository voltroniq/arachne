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
        // Example async task (background job, logging, etc.)
        if ($scheduler) {
            $scheduler->enqueue(function () {
                // You could run background async tasks here
            });
        }

        return new Response(200, [], "Welcome to Arachne!");
    }

    /**
     * Async demo route to showcase native PHP Fibers.
     *
     * Schedules two tasks that yield control back to the scheduler,
     * proving cooperative multitasking.
     */
    public function asyncExample(ServerRequestInterface $request, ?Scheduler $scheduler = null): Response
    {
        $log = [];

        if ($scheduler) {
            // Task 1
            $scheduler->create(function(Scheduler $s) use (&$log) {
                $log[] = 'task1-start';
                $s->yieldControl();
                $log[] = 'task1-end';
            });

            // Task 2
            $scheduler->create(function(Scheduler $s) use (&$log) {
                $log[] = 'task2-start';
                $s->yieldControl();
                $log[] = 'task2-end';
            });

            // Run all scheduled fibers
            $scheduler->run();
        } else {
            $log[] = 'scheduler-missing';
        }

        $body = "<h1>Async Demo</h1><pre>" . htmlspecialchars(implode("\n", $log)) . "</pre>";

        return new Response(200, [], $body);
    }
}