<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes\Reflect;

trait CollectMethods
{
    /** @var ReflectMethod[] */
    public readonly array $methods;

    public function setMethods(array $methods): void
    {
        $this->methods = $methods;
    }

    public function includeMethodsByDefault(): bool
    {
        return true;
    }

    abstract public function methodAttribute(): string;
}
