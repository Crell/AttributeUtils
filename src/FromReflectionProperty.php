<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer;

interface FromReflectionProperty
{
    public function fromReflection(\ReflectionProperty $subject): void;
}
