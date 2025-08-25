<?php
declare(strict_types=1);

namespace Arachne\Controllers;

use Psr\Http\Message\ServerRequestInterface;
use Arachne\Async\Scheduler;
use Nyholm\Psr7\Response;

final class HomeController
{
    /**
     * Homepage
     *
     * @param ServerRequestInterface $request
     * @param Scheduler|null $scheduler Optional async scheduler
     * @return Response
     */
    public function index(ServerRequestInterface $request, ?Scheduler $scheduler = null): Response
    {
        // Example async task
        if ($scheduler) {
            $scheduler->enqueue(fn() => error_log('Async task running'));
        }

        return new Response(
            200,
            [],
            '<h1>Welcome to Arachne</h1>'
        );
    }
}
