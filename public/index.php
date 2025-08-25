<?php

// Autoload all Composer-managed libraries and classes
require __DIR__ . '/../vendor/autoload.php';

// Load environment variables from a `.env` file using phpdotenv
// This keeps sensitive information like API keys and credentials out of your code.
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Placeholder for starting app logic
// Typically launches router, handle requests, etc.
echo "Hello Spiders from Arachne!";