<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

interface ParseParameters
{
    public function setParameters(array $parameters): void;

    public function includeParametersByDefault(): bool;

    public function parameterAttribute(): string;
}
