<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes\Reflect;

trait CollectParameters
{
    /** @var ReflectParameter[] */
    public readonly array $parameters;

    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    public function includeParametersByDefault(): bool
    {
        return true;
    }

    abstract public function parameterAttribute(): string;
}
