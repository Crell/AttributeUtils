<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Crell\AttributeUtils\FromReflectionProperty;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class PropertySubAttributeWithReflection implements FromReflectionProperty
{
    public string $name;

    public function fromReflection(\ReflectionProperty $subject): void
    {
        $this->name = $subject->getName();
    }
}
