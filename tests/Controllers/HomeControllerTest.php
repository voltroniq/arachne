<?php
declare(strict_types=1);

namespace Arachne\Tests\Controllers;

use PHPUnit\Framework\TestCase;
use Arachne\Controllers\HomeController;
use Arachne\Async\Scheduler;
use Nyholm\Psr7\ServerRequest;

final class HomeControllerTest extends TestCase
{
    public function testIndexReturnsWelcomeResponse(): void
    {
        // Arrange
        $scheduler = new Scheduler();
        $controller = new HomeController();
        $request = new ServerRequest('GET', '/');

        // Act
        $response = $controller->index($request, $scheduler);

        // Assert
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('Welcome', (string) $response->getBody());
    }
}