<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\ParseClassConstants;

#[Attribute(Attribute::TARGET_CLASS)]
class ClassWithClassConstants implements ParseClassConstants
{
    public array $constants;

    public function setConstants(array $constants): void
    {
        $this->constants = $constants;
    }

    public function includeConstantsByDefault(): bool
    {
        return true;
    }

    public function constantAttribute(): string
    {
        return ClassConstant::class;
    }
}
