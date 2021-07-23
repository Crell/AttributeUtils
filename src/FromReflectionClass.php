<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

interface FromReflectionClass
{
    public function fromReflection(\ReflectionClass $subject): void;
}
