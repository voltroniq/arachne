<?php
namespace Arachne\Async;

use Fiber;
use RuntimeException;

final class Scheduler
{
    /** 
     * @var array<int, Fiber> Stores Fiber instances, keyed by their object ID.
     * This helps manage and track all the fibers created in this scheduler.
     */
    private array $fibers = [];

    /**
     * Creates a new fiber task and registers it with the scheduler.
     * 
     * @param callable $task A callable (function or closure) that will be executed inside the fiber.
     *                       This callable may call `Scheduler::yieldControl()` to yield control back to the scheduler.
     * @return int The ID of the fiber (unique identifier for the fiber).
     */
    public function create(callable $task): int
    {
        // Create a new Fiber. A Fiber is a lightweight thread of execution that can be paused and resumed.
        $fiber = new Fiber(function () use ($task) {
            // Call the provided task (the user-defined callable) and pass this scheduler instance
            // to allow it to call yieldControl() if needed.
            return $task($this);
        });

        // Get a unique identifier for this fiber using spl_object_id. This will allow us to track the fiber.
        $id = spl_object_id($fiber);

        // Register the fiber in the $fibers array using its object ID as the key.
        $this->fibers[$id] = $fiber;

        // Return the unique ID of the fiber.
        return $id;
    }

    /**
     * Run all the fiber tasks until all fibers are terminated.
     * 
     * The scheduler continuously checks all fibers, starts or resumes them, and removes them
     * from the list when they are finished.
     */
    public function run(): void
    {
        // Keep looping while there are still fibers to run.
        while (!empty($this->fibers)) {
            // Loop through all registered fibers.
            foreach ($this->fibers as $id => $fiber) {
                // If the fiber has not started yet, start it.
                if (!$fiber->isStarted()) {
                    $fiber->start();
                }
                // If the fiber is running but not finished, resume it.
                elseif (!$fiber->isTerminated()) {
                    $fiber->resume();
                }

                // Once the fiber has finished (terminated), remove it from the list.
                if ($fiber->isTerminated()) {
                    unset($this->fibers[$id]);
                }
            }
        }
    }

    /**
     * Yield execution back to the scheduler from inside a fiber.
     * 
     * This method suspends the current fiber and returns control to the scheduler.
     * The scheduler will resume this fiber when it is ready.
     */
    public function yieldControl(): void
    {
        // Suspend the currently running fiber. The scheduler will resume it later.
        // The Fiber::suspend() function temporarily halts the execution of the fiber and 
        // gives control back to the Scheduler::run() loop.
        Fiber::suspend();
    }

    /**
     * Returns the number of tasks (fibers) currently managed by the scheduler.
     * 
     * @return int The number of fibers currently in the scheduler.
     */
    public function tasksCount(): int
    {
        // Return the total number of fibers that are currently being managed by the scheduler.
        return count($this->fibers);
    }
}