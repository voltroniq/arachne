<?php
declare(strict_types=1);

namespace Arachne\Async;

use Fiber;

final class Scheduler
{
    /** @var array<int, Fiber> Stores all fibers to be executed */
    private array $fibers = [];

    /** @var array<int, callable> Optional beginner-friendly task queue */
    private array $tasks = [];

    /**
     * Create a new fiber and register it with the scheduler.
     *
     * @param callable $task Task to execute inside the fiber.
     * @return int Fiber ID
     */
    public function create(callable $task): int
    {
        $fiber = new Fiber(function () use ($task) {
            return $task($this);
        });

        $id = spl_object_id($fiber);
        $this->fibers[$id] = $fiber;
        return $id;
    }

    /**
     * Add a beginner-friendly task to the queue.
     */
    public function enqueue(callable $task): void
    {
        $this->tasks[] = $task;
    }

    /**
     * Run all queued tasks and fibers.
     */
    public function run(): void
    {
        // Run beginner-friendly tasks first
        foreach ($this->tasks as $task) {
            $task();
        }
        $this->tasks = [];

        // Run fibers until none remain
        while (!empty($this->fibers)) {
            foreach ($this->fibers as $id => $fiber) {
                if (!$fiber->isStarted()) {
                    $fiber->start();
                } elseif (!$fiber->isTerminated()) {
                    $fiber->resume();
                }

                if ($fiber->isTerminated()) {
                    unset($this->fibers[$id]);
                }
            }
        }
    }

    /**
     * Yield execution back to the scheduler.
     */
    public function yieldControl(): void
    {
        Fiber::suspend();
    }

    /**
     * Get the number of running fibers.
     */
    public function tasksCount(): int
    {
        return count($this->fibers);
    }
}