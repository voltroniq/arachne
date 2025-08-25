# Arachne — Lightweight Async PHP Micro-Framework

Arachne is a **lightweight, async-first PHP micro-framework** built on **PHP Fibers** (PHP 8.3+). It provides a modern, developer-friendly platform for building high-performance web applications, microservices, and async tasks, while remaining fully **PSR-compliant**.

---

## Requirements

- **PHP 8.3 or higher**  
- Composer 2.x

---

## Key Features

- **Async-first**: Native Fiber-based scheduler for cooperative multitasking.  
- **PSR-compliant**: Follows PSR-4 (autoloading), PSR-7/15 (HTTP handling), PSR-11 (dependency injection).  
- **Lightweight & Fast**: Minimal overhead and dependencies.  
- **Modular & Extensible**: Optional adapters for AMPHP, ReactPHP, Swoole, Revolt.  
- **Dependency Injection**: Clean, minimal DI container using PHP-DI.  
- **Middleware Support**: Easily add custom request/response handling.  
- **Open-source MIT License**: Free to use, with optional monetization possibilities (starter kits, support).  

---

## Advantages

- Simplifies **async PHP development** with Fibers.  
- Provides a **modern, clean developer experience** with minimal boilerplate.  
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

## Installation & Quickstart

```bash
# Clone the repository
git clone git@github.com:voltroniq/arachne.git
cd arachne

# Install dependencies
composer install

# Run the built-in PHP server
php -S localhost:8000 -t public

# Visit in your browser
http://localhost:8000
```

## Contributing

We welcome contributions from the community! Please follow these steps:

1. **Fork the repository**.  
2. **Create a new feature branch**:  
   ```bash
   git checkout -b feature/YourFeature
   ```
3. **Commit your changes:**:
   ```bash
   git commit -m "Add your feature"
   ```
4. **Push to your branch:**:
   ```bash
   git push origin feature/YourFeature
   ```
5. **Open a Pull Request on GitHub**.
Contributions that improve async examples, documentation, or starter templates are especially appreciated.

## License

MIT License — free to use, modify, and distribute.

## Contact

For questions, support, or feedback, reach out to [Voltroniq](mailto:voltroniq.dev@gmail.com).