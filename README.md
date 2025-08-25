# Arachne — lightweight async PHP micro-framework

Arachne is a tiny async-first micro-framework built on PHP Fibers (PHP 8.1+). It is:
- PSR-7/15/11 compatible
- Fiber-based core (cooperative scheduler)
- Extensible: optional adapters for Amp, ReactPHP, Swoole, Revolt
- MIT licensed

## Quickstart

```bash
git clone git@github.com:voltroniq/arachne.git
cd arachne
composer install
php -S localhost:8000 -t public
# visit http://localhost:8000