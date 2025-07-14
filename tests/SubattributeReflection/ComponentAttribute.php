<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\SubattributeReflection;

use Crell\AttributeUtils\HasSubAttributes;
use Crell\AttributeUtils\ParseParameters;

#[\Attribute(\Attribute::TARGET_PROPERTY|\Attribute::TARGET_METHOD|\Attribute::TARGET_PARAMETER|\Attribute::TARGET_CLASS_CONSTANT)]
class ComponentAttribute implements HasSubAttributes, ParseParameters
{
    public ?SubAttributeReflect $sub;

    // Only used when this attribute is placed on a method.
    public array $parameters;

    public function __construct(
        public string $a = 'A',
    ) {}

    public function subAttributes(): array
    {
        return [
            SubAttributeReflect::class => fn(?SubAttributeReflect $sub) => $this->sub = $sub,
        ];
    }

    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    public function includeParametersByDefault(): bool
    {
        return false;
    }

    public function parameterAttribute(): string
    {
        return __CLASS__;
    }
}
