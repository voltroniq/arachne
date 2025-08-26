# Getting Started

Welcome to **Arachne** — a lightweight async-first PHP micro-framework built with PHP Fibers (PHP 8.3+).

This guide helps you create a simple Arachne app, run it locally, and understand the core pieces.

## Requirements

- PHP 8.3 or higher
- Composer 2.x

## Quick setup

```bash
git clone git@github.com:voltroniq/arachne.git myapp
cd myapp
composer install
php -S 127.0.0.1:8000 -t public
```

Open http://127.0.0.1:8000 — you should see the welcome page.

## Key files

- `public/index.php` — front controller, boots the framework
- `config/routes.php` — route definitions
- `src/Controllers/*` — application controllers
- `src/Async/Scheduler.php` — Fiber-based scheduler

## Create a route and controller (example)

See `config/routes.php` and `src/Controllers/HomeController.php` in the repo. The `asyncExample` method demonstrates Fiber usage.

## Running tests

```bash
vendor/bin/phpunit --configuration phpunit.xml
```