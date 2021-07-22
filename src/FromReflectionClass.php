<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer;

interface FromReflectionClass
{
    public function fromReflection(\ReflectionClass|\ReflectionObject $subject): void;
}
