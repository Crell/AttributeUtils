<?php

namespace Crell\AttributeUtils\Attributes\Functions;

use Crell\AttributeUtils\ParseParameters;

#[\Attribute(\Attribute::TARGET_FUNCTION)]
class HasParameters implements ParseParameters
{
    public readonly array $parameters;

    public function __construct(
        public readonly string $parameter,
        public readonly bool $parseParametersByDefault = true) {}

    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    public function includeParametersByDefault(): bool
    {
        return $this->parseParametersByDefault;
    }

    public function parameterAttribute(): string
    {
        return $this->parameter;
    }


}
