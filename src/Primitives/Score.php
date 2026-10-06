<?php

declare(strict_types=1);

namespace Obelaw\SystemOne\Primitives;

class Score extends Primitive
{
    protected int|float $min = 1;
    protected int|float $max = 10;
    protected array $criteria = [];

    /**
     * @param string $instructions
     * @param array<int|string, string>|int|float $criteriaOrMin
     * @param int|float $max
     */
    public function __construct(
        protected string $instructions,
        array|int|float $criteriaOrMin = 1,
        int|float $max = 10
    ) {
        if (is_array($criteriaOrMin)) {
            $this->criteria = $criteriaOrMin;
            $this->min = 1;
            $this->max = count($criteriaOrMin);
        } else {
            $this->min = $criteriaOrMin;
            $this->max = $max;
        }
    }

    /**
     * Create a new Score primitive instance.
     *
     * @param string $instructions
     * @param array<int|string, string>|int|float $criteriaOrMin
     * @param int|float $max
     */
    public static function make(string $instructions, array|int|float $criteriaOrMin = 1, int|float $max = 10): static
    {
        return new static($instructions, $criteriaOrMin, $max);
    }

    /**
     * Create a bounded numerical range Score primitive.
     */
    public static function range(string $instructions, int|float $min, int|float $max): static
    {
        return new static($instructions, $min, $max);
    }

    /**
     * Create an ordered rubric Score primitive.
     *
     * @param array<int|string, string> $criteria
     */
    public static function rubric(string $instructions, array $criteria): static
    {
        return new static($instructions, $criteria);
    }

    /**
     * Get the primitive type name.
     */
    public function getType(): string
    {
        return 'score';
    }

    /**
     * Get the instruction string.
     */
    public function getInstructions(): string
    {
        return $this->instructions;
    }

    /**
     * Set the instruction string fluently.
     */
    public function instructions(string $instructions): static
    {
        $this->instructions = $instructions;

        return $this;
    }

    /**
     * Set the ordered rubric criteria for the score.
     *
     * @param array<int|string, string> $criteria
     */
    public function criteria(array $criteria): static
    {
        $this->criteria = $criteria;

        return $this;
    }

    /**
     * Add an individual criterion to the score rubric.
     */
    public function criterion(string $criterion): static
    {
        $this->criteria[] = $criterion;

        return $this;
    }

    /**
     * Get the criteria rubric.
     *
     * @return array<int|string, string>
     */
    public function getCriteria(): array
    {
        return array_is_list($this->criteria) ? array_values($this->criteria) : $this->criteria;
    }

    /**
     * Set the minimum score bound.
     */
    public function min(int|float $min): static
    {
        $this->min = $min;

        return $this;
    }

    /**
     * Set the maximum score bound.
     */
    public function max(int|float $max): static
    {
        $this->max = $max;

        return $this;
    }

    /**
     * Set both minimum and maximum score bounds.
     */
    public function setRange(int|float $min, int|float $max): static
    {
        $this->min = $min;
        $this->max = $max;

        return $this;
    }

    /**
     * Get the minimum value.
     */
    public function getMin(): int|float
    {
        return $this->min;
    }

    /**
     * Get the maximum value.
     */
    public function getMax(): int|float
    {
        return $this->max;
    }

    /**
     * Convert the primitive into the decision schema array.
     *
     * @return array{type: string, instructions: string, criteria?: array<int|string, string>, min?: int|float, max?: int|float}
     */
    public function toArray(): array
    {
        if (!empty($this->criteria)) {
            return [
                'type' => $this->getType(),
                'instructions' => $this->instructions,
                'criteria' => $this->getCriteria(),
            ];
        }

        return [
            'type' => $this->getType(),
            'instructions' => $this->instructions,
            'min' => $this->min,
            'max' => $this->max,
        ];
    }
}
