<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\ParseConstants;

#[Attribute(Attribute::TARGET_CLASS)]
class ClassWithConstants implements ParseConstants
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
