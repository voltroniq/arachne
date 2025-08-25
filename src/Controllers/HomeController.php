<?php
declare(strict_types=1);

namespace Arachne\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Arachne\Async\Scheduler;
use Nyholm\Psr7\Response;

final class HomeController
{
    public function index(ServerRequestInterface $request, ?Scheduler $scheduler = null): Response
    {
        // Beginner-friendly async example
        if ($scheduler) {
            $scheduler->enqueue(fn() => error_log('Async task running!'));
        }

        return new Response(
            200,
            [],
            '<h1>Welcome to Arachne</h1>'
        );
    }
}