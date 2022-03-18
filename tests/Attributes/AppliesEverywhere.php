<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\Attributes\Reflect\CollectClassConstants;
use Crell\AttributeUtils\Attributes\Reflect\CollectEnumCases;
use Crell\AttributeUtils\Attributes\Reflect\CollectMethods;
use Crell\AttributeUtils\Attributes\Reflect\CollectParameters;
use Crell\AttributeUtils\Attributes\Reflect\CollectProperties;
use Crell\AttributeUtils\FromReflectionClass;
use Crell\AttributeUtils\FromReflectionClassConstant;
use Crell\AttributeUtils\FromReflectionEnumCase;
use Crell\AttributeUtils\FromReflectionMethod;
use Crell\AttributeUtils\FromReflectionParameter;
use Crell\AttributeUtils\FromReflectionProperty;
use Crell\AttributeUtils\Inheritable;
use Crell\AttributeUtils\ParseClassConstants;
use Crell\AttributeUtils\ParseMethods;
use Crell\AttributeUtils\ParseParameters;
use Crell\AttributeUtils\ParseProperties;

#[Attribute]
class AppliesEverywhere implements
    Inheritable,
    FromReflectionClass,
    FromReflectionProperty,
    FromReflectionMethod,
    FromReflectionParameter,
    FromReflectionClassConstant,
    FromReflectionEnumCase,
    ParseParameters,
    ParseMethods,
    ParseProperties,
    ParseClassConstants
{
    use CollectParameters;
    use CollectMethods;
    use CollectProperties;
    use CollectEnumCases;
    use CollectClassConstants;

    public string $phpName;

    public function __construct(
        public int $a = 0,
    ) {}

    public function fromReflection(\ReflectionClass|\ReflectionMethod|\ReflectionProperty|\ReflectionClassConstant|\ReflectionParameter $subject): void
    {
        $this->phpName = $subject->getName();
    }

    public function caseAttribute(): string
    {
        return __CLASS__;
    }

    public function methodAttribute(): string
    {
        return __CLASS__;
    }

    public function parameterAttribute(): string
    {
        return __CLASS__;
    }

    public function propertyAttribute(): string
    {
        return __CLASS__;
    }

    public function constantAttribute(): string
    {
        return __CLASS__;
    }
}
