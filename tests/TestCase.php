<?php

declare(strict_types=1);

namespace Obelaw\SystemOne\Tests;

use Obelaw\SystemOne\SystemOneServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Facade::setFacadeApplication($this->app);
    }

    protected function getPackageProviders($app): array
    {
        return [
            SystemOneServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app): array
    {
        return [
            'SystemOne' => \Obelaw\SystemOne\Facades\SystemOne::class,
        ];
    }
}
