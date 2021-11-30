<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes\Reflect;

/**
 * List of PHP's class-esque types.
 *
 * All of these use ReflectionClass, but are still somewhat different.
 */
enum StructType
{
    case Class;
    case Interface;
    case Trait;
    case AnonymousClass;
}
