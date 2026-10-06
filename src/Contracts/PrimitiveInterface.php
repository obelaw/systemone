<?php

declare(strict_types=1);

namespace Obelaw\SystemOne\Contracts;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

interface PrimitiveInterface extends Arrayable, JsonSerializable
{
    /**
     * Get the primitive type name (e.g., 'noul', 'choice', 'score').
     */
    public function getType(): string;

    /**
     * Get the instruction string for the question.
     */
    public function getInstructions(): string;

    /**
     * Convert the primitive into the AI decision engine schema array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
