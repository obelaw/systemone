<?php

declare(strict_types=1);

namespace Obelaw\SystemOne;

use Obelaw\SystemOne\Contracts\DecisionResponseInterface;

class DecisionResponse implements DecisionResponseInterface
{
    /**
     * @param array<string, mixed> $answers
     * @param array<string, mixed> $raw
     */
    public function __construct(
        protected array $answers = [],
        protected array $raw = []
    ) {
    }

    /**
     * Get the raw payload returned by the driver.
     *
     * @return array<string, mixed>
     */
    public function raw(): array
    {
        return $this->raw;
    }

    /**
     * Get all parsed decision answers.
     *
     * @return array<string, mixed>
     */
    public function answers(): array
    {
        return $this->answers;
    }

    /**
     * Retrieve the parsed answer structure for a specific question.
     */
    public function answer(string $question): mixed
    {
        return $this->answers[$question] ?? null;
    }

    /**
     * Get the resolved value (probability float, choice string, or score number).
     */
    public function value(string $question): mixed
    {
        $ans = $this->answer($question);

        if (is_array($ans)) {
            return $ans['value'] ?? $ans['answer'] ?? null;
        }

        return $ans;
    }

    /**
     * Get the confidence score for a question.
     */
    public function confidence(string $question): ?float
    {
        $ans = $this->answer($question);

        if (is_array($ans) && isset($ans['confidence'])) {
            return (float) $ans['confidence'];
        }

        return null;
    }

    /**
     * Get the probability distribution for choices.
     *
     * @return array<string, float>
     */
    public function probabilities(string $question): array
    {
        $ans = $this->answer($question);

        if (is_array($ans) && isset($ans['probabilities']) && is_array($ans['probabilities'])) {
            return $ans['probabilities'];
        }

        return [];
    }

    /**
     * Check if a binary / likelihood answer exceeds the threshold.
     */
    public function isPositive(string $question, float $threshold = 0.5): bool
    {
        $val = $this->value($question);

        if (is_numeric($val)) {
            return (float) $val > $threshold;
        }

        if (is_bool($val)) {
            return $val;
        }

        return false;
    }

    /**
     * Check if a binary / likelihood answer is below or equal to the threshold.
     */
    public function isNegative(string $question, float $threshold = 0.5): bool
    {
        return !$this->isPositive($question, $threshold);
    }

    /**
     * Convert the response into an array.
     *
     * @return array{answers: array<string, mixed>, raw: array<string, mixed>}
     */
    public function toArray(): array
    {
        return [
            'answers' => $this->answers,
            'raw' => $this->raw,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
