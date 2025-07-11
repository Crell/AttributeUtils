<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes\Reflect;

// @phpstan-ignore trait.unused (I don't know why PHPStan thinks this is unused. It very much is used.)
trait CollectParameters
{
    /** @var ReflectParameter[] */
    public readonly array $parameters;

    /**
     * @param ReflectParameter[] $parameters
     */
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
