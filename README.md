# Arachne — Lightweight Async PHP Micro-Framework

Arachne is a **lightweight, async-first PHP micro-framework** built on **PHP Fibers** (PHP 8.1+). It provides a modern, developer-friendly platform for building high-performance web applications, microservices, and async tasks, while remaining fully PSR-compliant.

---

## Key Features

- **Async-first**: Native Fiber-based scheduler for cooperative multitasking.
- **PSR-compliant**: Follows PSR-4 (autoloading), PSR-7/15 (HTTP handling), PSR-11 (dependency injection) standards.
- **Lightweight & Fast**: Minimal overhead and dependencies.
- **Modular & Extensible**: Optional adapters for AMPHP, ReactPHP, Swoole, Revolt.
- **Dependency Injection**: Clean, minimal DI container using PHP-DI.
- **Middleware Support**: Easily add custom request/response handling.
- **Open-source MIT License**: Free to use, with optional monetization possibilities (starter kits, support).

---

## Advantages

- Simplifies **async PHP development** with Fibers.
- Provides a **clean, modern developer experience** with minimal boilerplate.
- Fully **compatible with other PHP frameworks** and PSR-based libraries.
- Encourages **modular and reusable code**.
- Ideal for **learning async PHP**, microservices, and high-performance APIs.

---

## Use Cases

- Async **REST APIs** or microservices.
- **Background jobs** and lightweight concurrent tasks.
- **Realtime applications** (chat, notifications) without Node.js.
- Rapid prototyping for small to medium projects.
- Learning **Fibers and modern PHP 8+ async patterns**.

---

## Quickstart

```bash
git clone git@github.com:voltroniq/arachne.git
cd arachne
composer install

# Run the built-in PHP server
php -S localhost:8000 -t public
# visit http://localhost:8000