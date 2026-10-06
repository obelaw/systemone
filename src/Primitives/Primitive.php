<?php

declare(strict_types=1);

namespace Obelaw\SystemOne\Primitives;

use Obelaw\SystemOne\Contracts\PrimitiveInterface;

abstract class Primitive implements PrimitiveInterface
{
    /**
     * Specify data which should be serialized to JSON.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
