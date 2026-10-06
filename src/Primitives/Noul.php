<?php

declare(strict_types=1);

namespace Obelaw\SystemOne\Primitives;

class Noul extends Primitive
{
    public function __construct(
        protected string $instructions
    ) {
    }

    /**
     * Create a new Noul (binary/likelihood) primitive instance.
     */
    public static function make(string $instructions): static
    {
        return new static($instructions);
    }

    /**
     * Get the primitive type name.
     */
    public function getType(): string
    {
        return 'noul';
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
     * Convert the primitive into the decision schema array.
     *
     * @return array{type: string, instructions: string}
     */
    public function toArray(): array
    {
        return [
            'type' => $this->getType(),
            'instructions' => $this->instructions,
        ];
    }
}
