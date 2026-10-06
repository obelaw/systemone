<?php

declare(strict_types=1);

namespace Obelaw\SystemOne\Primitives;

class Choice extends Primitive
{
    /**
     * @param string $instructions
     * @param array<int|string, string> $criteria
     */
    public function __construct(
        protected string $instructions,
        protected array $criteria = []
    ) {
    }

    /**
     * Create a new Choice primitive instance.
     *
     * @param string $instructions
     * @param array<int|string, string> $criteria
     */
    public static function make(string $instructions, array $criteria = []): static
    {
        return new static($instructions, $criteria);
    }

    /**
     * Get the primitive type name.
     */
    public function getType(): string
    {
        return 'choice';
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
     * Set the available criteria options.
     *
     * @param array<int|string, string> $criteria
     */
    public function criteria(array $criteria): static
    {
        $this->criteria = $criteria;

        return $this;
    }

    /**
     * Add an individual criterion to the choice options.
     */
    public function criterion(string $keyOrCriterion, ?string $description = null): static
    {
        if ($description !== null) {
            $this->criteria[$keyOrCriterion] = $description;
        } else {
            $this->criteria[] = $keyOrCriterion;
        }

        return $this;
    }

    /**
     * Get the criteria options.
     *
     * @return array<int|string, string>
     */
    public function getCriteria(): array
    {
        return array_is_list($this->criteria) ? array_values($this->criteria) : $this->criteria;
    }

    /**
     * Convert the primitive into the decision schema array.
     *
     * @return array{type: string, instructions: string, criteria: array<int|string, string>}
     */
    public function toArray(): array
    {
        return [
            'type' => $this->getType(),
            'instructions' => $this->instructions,
            'criteria' => $this->getCriteria(),
        ];
    }
}
