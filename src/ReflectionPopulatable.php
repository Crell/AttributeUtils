<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer;

/**
 * @todo This is a hideous name. Come up with something better.
 */
interface ReflectionPopulatable
{
    public function fromReflection(\Reflector $subject): void;
}
