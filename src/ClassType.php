<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

/**
 * List of PHP's class-esque types.
 *
 * All of these use ReflectionClass, but are still somewhat different.
 */
enum ClassType
{
    case NormalClass;
    case Interface;
    case Trait;
    case AnonymousClass;
}
