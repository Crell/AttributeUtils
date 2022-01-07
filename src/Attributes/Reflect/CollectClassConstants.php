<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes\Reflect;

trait CollectClassConstants
{
    /** @var ReflectClassConstant[] */
    public readonly array $constants;

    public function setConstants(array $constants): void
    {
        $this->constants = $constants;
    }

    public function includeConstantsByDefault(): bool
    {
        return true;
    }

    abstract public function constantAttribute(): string;
}
