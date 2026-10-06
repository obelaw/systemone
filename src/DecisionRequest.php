<?php

declare(strict_types=1);

namespace Obelaw\SystemOne;

use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;
use JsonSerializable;
use Obelaw\SystemOne\Contracts\DecisionResponseInterface;
use Obelaw\SystemOne\Contracts\DriverInterface;
use Obelaw\SystemOne\Contracts\PrimitiveInterface;

class DecisionRequest implements Arrayable, JsonSerializable
{
    protected mixed $state = null;
    protected ?string $model = null;
    protected string|DriverInterface|null $driver = null;

    /**
     * @var array<string, PrimitiveInterface|array<string, mixed>>
     */
    protected array $questions = [];

    public function __construct(
        protected ?SystemOneManager $manager = null
    ) {
    }

    /**
     * Create a new DecisionRequest instance.
     */
    public static function make(?SystemOneManager $manager = null): static
    {
        return new static($manager ?? (function_exists('app') && app()->bound('systemone') ? app('systemone') : null));
    }

    /**
     * Set the decision context / state data.
     */
    public function state(mixed $state): static
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get the decision context / state.
     */
    public function getState(): mixed
    {
        return $this->state;
    }

    /**
     * Add a question to the decision schema.
     */
    public function ask(string $key, PrimitiveInterface|array $question): static
    {
        $this->questions[$key] = $question;

        return $this;
    }

    /**
     * Alias for ask().
     */
    public function question(string $key, PrimitiveInterface|array $question): static
    {
        return $this->ask($key, $question);
    }

    /**
     * Bulk register questions.
     *
     * @param array<string, PrimitiveInterface|array<string, mixed>> $questions
     */
    public function questions(array $questions): static
    {
        foreach ($questions as $key => $question) {
            $this->ask($key, $question);
        }

        return $this;
    }

    /**
     * Get registered questions.
     *
     * @return array<string, PrimitiveInterface|array<string, mixed>>
     */
    public function getQuestions(): array
    {
        return $this->questions;
    }

    /**
     * Specify the target AI model.
     */
    public function model(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get the specified AI model.
     */
    public function getModel(): ?string
    {
        return $this->model;
    }

    /**
     * Set or switch the execution driver (by name or instance).
     */
    public function driver(string|DriverInterface|null $driver): static
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * Get the configured driver.
     */
    public function getDriver(): string|DriverInterface|null
    {
        return $this->driver;
    }

    /**
     * Resolve the driver instance.
     */
    public function resolveDriver(?DriverInterface $explicitDriver = null): DriverInterface
    {
        if ($explicitDriver !== null) {
            return $explicitDriver;
        }

        if ($this->driver instanceof DriverInterface) {
            return $this->driver;
        }

        if (is_string($this->driver) && $this->manager !== null) {
            return $this->manager->driver($this->driver);
        }

        if ($this->manager !== null) {
            return $this->manager->driver();
        }

        if (function_exists('app') && app()->bound('systemone')) {
            /** @var SystemOneManager $manager */
            $manager = app('systemone');
            return is_string($this->driver) ? $manager->driver($this->driver) : $manager->driver();
        }

        throw new InvalidArgumentException('No decision driver configured or resolved.');
    }

    /**
     * Serialize request into schema payload.
     *
     * @return array{state: mixed, questions: array<string, array<string, mixed>>}
     */
    public function toPayload(): array
    {
        $serializedQuestions = [];

        foreach ($this->questions as $key => $question) {
            $serializedQuestions[$key] = $question instanceof Arrayable
                ? $question->toArray()
                : $question;
        }

        return [
            'state' => $this->state instanceof Arrayable
                ? $this->state->toArray()
                : $this->state,
            'questions' => $serializedQuestions,
        ];
    }

    /**
     * Convert the request to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_merge($this->toPayload(), [
            'model' => $this->model,
            'driver' => $this->driver instanceof DriverInterface ? get_class($this->driver) : $this->driver,
        ]);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Execute the decision request using the resolved driver.
     */
    public function run(string|DriverInterface|null $driver = null): DecisionResponseInterface
    {
        if ($driver instanceof DriverInterface) {
            return $driver->decide($this);
        }

        if (is_string($driver)) {
            $this->driver($driver);
        }

        return $this->resolveDriver()->decide($this);
    }

    /**
     * Alias for run().
     */
    public function decide(string|DriverInterface|null $driver = null): DecisionResponseInterface
    {
        return $this->run($driver);
    }
}
