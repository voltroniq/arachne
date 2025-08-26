<?php
declare(strict_types=1);

namespace Tests\Async;

use PHPUnit\Framework\TestCase;
use Arachne\Async\Scheduler;

final class SchedulerTest extends TestCase
{
    public function testCreateAndRunFibers(): void
    {
        $scheduler = new Scheduler();
        $log = [];

        // Task A
        $scheduler->create(function($s) use (&$log) {
            $log[] = 'a-start';
            $s->yieldControl();
            $log[] = 'a-end';
        });

        // Task B
        $scheduler->create(function($s) use (&$log) {
            $log[] = 'b-start';
            $s->yieldControl();
            $log[] = 'b-end';
        });

        // Run scheduler
        $scheduler->run();

        // After run we expect 4 entries (both tasks started and finished)
        $this->assertCount(4, $log);
        $this->assertSame(0, $scheduler->tasksCount());
        $this->assertContains('a-start', $log);
        $this->assertContains('b-end', $log);
    }

    public function testEnqueueRunsQueuedTasks(): void
    {
        $scheduler = new Scheduler();
        $log = [];

        // Enqueue a beginner-friendly task
        $scheduler->enqueue(function () use (&$log) {
            $log[] = 'simple-task';
        });

        $scheduler->run();

        $this->assertSame(['simple-task'], $log);
        $this->assertSame(0, $scheduler->tasksCount());
    }

    public function testFiberBasedTask(): void
    {
        $scheduler = new Scheduler();
        $log = [];

        $scheduler->create(function ($s) use (&$log) {
            $log[] = 'fiber-step-1';
            $s->yieldControl();
            $log[] = 'fiber-step-2';
        });

        $scheduler->run();

        $this->assertSame(
            ['fiber-step-1', 'fiber-step-2'],
            $log
        );
    }
}