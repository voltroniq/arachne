# Short Async Examples

This file collects a few short examples that you can cut-and-paste into controllers or CLI scripts.

## Example: fire-and-forget background task

```php
$scheduler->enqueue(function() {
    // run email send, logging or other non-blocking work
});
```

## Example: cooperatively yielding tasks

```php
$scheduler->create(function($s) {
    // work
    $s->yieldControl();
    // resumed work
});
$scheduler->run();
```