# Scheduler & Async Tasks

Arachne's Scheduler is a simple cooperative scheduler built on PHP Fibers. It can:

- create fibers (`create(callable)`)
- enqueue beginner tasks (`enqueue(callable)`)
- run tasks and fibers (`run()`)
- yield control inside a fiber (`yieldControl()`)

### Basic usage

```php
$scheduler = new \Arachne\Async\Scheduler();

// schedule a fiber
$scheduler->create(function($s) {
    echo "Hello from fiber\n";
    $s->yieldControl(); // suspend
    echo "Fiber resumed\n";
});

// enqueue a simple callback task
$scheduler->enqueue(function() { echo "Simple task\n"; });

// run everything
$scheduler->run();
```

### Notes
- Tasks added with `enqueue()` run before fibers in the simple scheduler implementation.
- Use `yieldControl()` within a fiber to cooperatively suspend and be resumed by the scheduler.