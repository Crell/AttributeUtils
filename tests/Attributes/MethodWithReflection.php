<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\FromReflectionMethod;
use Crell\AttributeUtils\ParseParameters;

#[Attribute(Attribute::TARGET_METHOD)]
class MethodWithReflection implements FromReflectionMethod, ParseParameters
{
    /** @var ParameterWithReflection[] */
    public array $parameters;

    public function __construct(
        public string $a = 'a',
        public string $b = 'b',
        public ?string $name = null,
    ) {}

    public function fromReflection(\ReflectionMethod $subject): void
    {
        $this->name ??= $subject->getName();
    }

    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    public function includeParametersByDefault(): bool
    {
        return true;
    }

    public function parameterAttribute(): string
    {
        return ParameterWithReflection::class;
    }
}
