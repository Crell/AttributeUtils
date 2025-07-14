<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\SubattributeReflection;

use Crell\AttributeUtils\FromReflectionClass;
use Crell\AttributeUtils\FromReflectionClassConstant;
use Crell\AttributeUtils\FromReflectionEnum;
use Crell\AttributeUtils\FromReflectionEnumCase;
use Crell\AttributeUtils\FromReflectionFunction;
use Crell\AttributeUtils\FromReflectionMethod;
use Crell\AttributeUtils\FromReflectionParameter;
use Crell\AttributeUtils\FromReflectionProperty;

#[\Attribute]
class SubAttributeReflect implements
    FromReflectionClass,
    FromReflectionProperty,
    FromReflectionMethod,
    FromReflectionParameter,
    FromReflectionClassConstant,
    FromReflectionEnum,
    FromReflectionEnumCase,
    FromReflectionFunction
{
    public string $name;

    public function fromReflection(\ReflectionClass|\ReflectionMethod|\ReflectionProperty|\ReflectionClassConstant|\ReflectionParameter|\ReflectionFunction|\ReflectionEnum $subject): void
    {
        $this->name = $subject->getName();
    }
}
