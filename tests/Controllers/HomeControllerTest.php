<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Arachne\Controllers\HomeController;
use Arachne\Async\Scheduler;
use Nyholm\Psr7\ServerRequest;

final class HomeControllerTest extends TestCase
{
    public function testIndexReturnsWelcomeResponse(): void
    {
        $scheduler = new Scheduler();
        $controller = new HomeController();
        $request = new ServerRequest('GET', '/');

        $response = $controller->index($request, $scheduler);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('Welcome', (string) $response->getBody());
    }
}
