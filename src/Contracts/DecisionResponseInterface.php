<?php

declare(strict_types=1);

namespace Obelaw\SystemOne\Contracts;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

interface DecisionResponseInterface extends Arrayable, JsonSerializable
{
    /**
     * Get the raw underlying response payload from the driver.
     *
     * @return array<string, mixed>
     */
    public function raw(): array;

    /**
     * Get all parsed decision answers keyed by question name.
     *
     * @return array<string, mixed>
     */
    public function answers(): array;

    /**
     * Retrieve the parsed answer structure for a specific question.
     */
    public function answer(string $question): mixed;

    /**
     * Get the resolved value (probability float, categorical choice string, or numerical score).
     */
    public function value(string $question): mixed;

    /**
     * Get the confidence score for a specific question.
     */
    public function confidence(string $question): ?float;

    /**
     * Get probability distribution mapping for choices.
     *
     * @return array<string, float>
     */
    public function probabilities(string $question): array;

    /**
     * Check if a binary / likelihood answer exceeds the positive decision threshold.
     */
    public function isPositive(string $question, float $threshold = 0.5): bool;

    /**
     * Check if a binary / likelihood answer is below or equal to the decision threshold.
     */
    public function isNegative(string $question, float $threshold = 0.5): bool;
}
