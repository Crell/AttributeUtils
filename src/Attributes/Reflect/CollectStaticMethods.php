<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes\Reflect;

trait CollectStaticMethods
{
    /** @var ReflectMethod[] */
    public readonly array $staticMethods;

    public function setStaticMethods(array $methods): void
    {
        $this->staticMethods = $methods;
    }

    public function includeStaticMethodsByDefault(): bool
    {
        return true;
    }

    abstract public function staticMethodAttribute(): string;
}
