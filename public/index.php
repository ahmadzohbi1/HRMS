<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Register the auto loader
require __DIR__ . '/../vendor/autoload.php';

// Boot the Laravel application
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Handle the request through the kernel
$kernel = $app->make(Kernel::class);

$response = tap($kernel->handle(
    $request = Request::capture()
))->send();

$kernel->terminate($request, $response);
