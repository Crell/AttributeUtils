<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

interface ParseConstants
{
    public function setConstants(array $constants): void;

    public function includeConstantsByDefault(): bool;

    public function constantAttribute(): string;
}
