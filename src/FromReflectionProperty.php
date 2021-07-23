<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

interface FromReflectionProperty
{
    public function fromReflection(\ReflectionProperty $subject): void;
}
