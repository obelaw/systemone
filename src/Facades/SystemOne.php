<?php

declare(strict_types=1);

namespace Obelaw\SystemOne\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Obelaw\SystemOne\DecisionRequest state(mixed $state)
 * @method static \Obelaw\SystemOne\DecisionRequest ask(string $key, \Obelaw\SystemOne\Contracts\PrimitiveInterface|array $question)
 * @method static \Obelaw\SystemOne\DecisionRequest request()
 * @method static \Obelaw\SystemOne\Contracts\DriverInterface driver(?string $name = null)
 * @method static \Obelaw\SystemOne\SystemOneManager extend(string $driver, \Closure $callback)
 *
 * @see \Obelaw\SystemOne\SystemOneManager
 */
class SystemOne extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'systemone';
    }
}
