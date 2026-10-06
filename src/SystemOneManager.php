<?php

declare(strict_types=1);

namespace Obelaw\SystemOne;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use InvalidArgumentException;
use Obelaw\SystemOne\Contracts\DriverInterface;
use Obelaw\SystemOne\Contracts\PrimitiveInterface;

class SystemOneManager
{
    /**
     * The registered custom driver creators.
     *
     * @var array<string, Closure>
     */
    protected array $customCreators = [];

    /**
     * The array of resolved driver instances.
     *
     * @var array<string, DriverInterface>
     */
    protected array $drivers = [];

    public function __construct(
        protected mixed $app = null
    ) {
    }

    /**
     * Get a driver instance by name.
     */
    public function driver(?string $name = null): DriverInterface
    {
        $name = $name ?: $this->getDefaultDriver();

        if (!isset($this->drivers[$name])) {
            $this->drivers[$name] = $this->createDriver($name);
        }

        return $this->drivers[$name];
    }

    /**
     * Create a new driver instance.
     */
    protected function createDriver(string $name): DriverInterface
    {
        if (isset($this->customCreators[$name])) {
            return $this->customCreators[$name]($this->app);
        }

        if ($this->app && $this->app->bound("systemone.driver.{$name}")) {
            return $this->app->make("systemone.driver.{$name}");
        }

        throw new InvalidArgumentException("Driver [{$name}] is not supported or configured.");
    }

    /**
     * Register a custom driver creator Closure.
     */
    public function extend(string $driver, Closure $callback): static
    {
        $this->customCreators[$driver] = $callback;

        return $this;
    }

    /**
     * Get the default driver name.
     */
    public function getDefaultDriver(): string
    {
        if ($this->app && $this->app->bound('config')) {
            return (string) $this->app['config']->get('systemone.default', 'clef');
        }

        return 'clef';
    }

    /**
     * Start a new fluent DecisionRequest with the given state.
     */
    public function state(mixed $state): DecisionRequest
    {
        return (new DecisionRequest($this))->state($state);
    }

    /**
     * Start a new fluent DecisionRequest with a question.
     */
    public function ask(string $key, PrimitiveInterface|array $question): DecisionRequest
    {
        return (new DecisionRequest($this))->ask($key, $question);
    }

    /**
     * Create a new DecisionRequest.
     */
    public function request(): DecisionRequest
    {
        return new DecisionRequest($this);
    }
}
