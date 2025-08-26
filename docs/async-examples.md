# Async Examples

This page contains small examples that demonstrate native async using Fibers and the Scheduler.

## Example: simple fiber

```php
$scheduler = new \Arachne\Async\Scheduler();

$scheduler->create(function($s) {
    echo "A start\n";
    $s->yieldControl();
    echo "A end\n";
});

$scheduler->create(function($s) {
    echo "B start\n";
    $s->yieldControl();
    echo "B end\n";
});

$scheduler->run();
```

Output typically shows both starts and then ends, demonstrating cooperative scheduling.

## Example: HTTP handler that does background work

```php
public function heavyWork(ServerRequestInterface $req, ?Scheduler $scheduler = null)
{
    if ($scheduler) {
        // run a long background job without blocking the request
        $scheduler->enqueue(function() {
            // write to a log, perform push notifications, etc.
        });
    }

    return new \Nyholm\Psr7\Response(202, [], 'Accepted');
}
```