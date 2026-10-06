<?php

spl_autoload_register(function ($class) {
    $prefix = 'Obelaw\\SystemOne\\Tests\\';
    if (str_starts_with($class, $prefix)) {
        $relativeClass = substr($class, strlen($prefix));
        $file = __DIR__ . '/' . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }

    $prefix = 'Obelaw\\SystemOne\\';
    if (str_starts_with($class, $prefix)) {
        $relativeClass = substr($class, strlen($prefix));
        $file = dirname(__DIR__) . '/src/' . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

use Obelaw\SystemOne\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);
