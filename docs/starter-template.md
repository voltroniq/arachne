# Starter Template

This starter template shows a minimal app structure and files to boot a small Arachne app.

```
starter/
├─ public/
│  └─ index.php
├─ src/
│  └─ Controllers/
│     └─ WelcomeController.php
├─ config/
│  └─ routes.php
```

## public/index.php (example)

The starter `public/index.php` should be a lightweight copy of the main repo `public/index.php` that wires the scheduler, container, routes and middleware.

## WelcomeController (example)

A simple controller that returns HTML and demonstrates scheduler usage.