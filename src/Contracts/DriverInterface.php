<?php

declare(strict_types=1);

namespace Obelaw\SystemOne\Contracts;

use Obelaw\SystemOne\DecisionRequest;

interface DriverInterface
{
    /**
     * Execute the decision request with the underlying AI driver.
     */
    public function decide(DecisionRequest $request): DecisionResponseInterface;

    /**
     * Alias for decide().
     */
    public function run(DecisionRequest $request): DecisionResponseInterface;
}
