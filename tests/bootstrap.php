<?php

declare(strict_types=1);

// Autoload dependencies
require_once __DIR__ . '/../vendor/autoload.php';

// Enable Bypass Finals
DG\BypassFinals::enable();

// Set up testing environment
defined('APP_ENV') || define('APP_ENV', 'testing');

// Additional setup if needed for Laravel or other frameworks
// For example, initializing Laravel application for testing
// if (file_exists(__DIR__ . '/../vendor/laravel/framework/src/Illuminate/Foundation/Application.php')) {
//     $app = require_once __DIR__ . '/../bootstrap/app.php';
//     $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
// }