<?php
namespace Arachne\Async;

use Fiber;
use RuntimeException;

final class Scheduler
{
    private array $fibers = [];

    /** @var array<int, callable> Optional beginner-friendly task queue */
    private array $tasks = [];

    // ---------- Existing fiber methods ----------

    public function create(callable $task): int
    {
        $fiber = new Fiber(function () use ($task) {
            return $task($this);
        });

        $id = spl_object_id($fiber);
        $this->fibers[$id] = $fiber;
        return $id;
    }

    public function run(): void
    {
        // Run beginner-friendly tasks first
        foreach ($this->tasks as $task) {
            $task();
        }
        $this->tasks = [];

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

    public function yieldControl(): void
    {
        Fiber::suspend();
    }

    public function tasksCount(): int
    {
        return count($this->fibers);
    }

    // ---------- New beginner-friendly enqueue method ----------
    public function enqueue(callable $task): void
    {
        $this->tasks[] = $task;
    }
}