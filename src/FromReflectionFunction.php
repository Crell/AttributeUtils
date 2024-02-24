<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

/**
 * Marks a function-targeting attribute as wanting reflection information.
 *
 * If a function-targeting attribute implements this interface, then after it
 * is instantiated the reflection object for the function will be passed to this
 * method.  The attribute may then extract whatever information it desires
 * and save it to object however it likes.
 *
 * Note that the attribute MUST NOT save the reflection object itself. That
 * would make the attribute object unserializable, and thus uncacheable.
 */
interface FromReflectionFunction
{
    public function fromReflection(\ReflectionFunction $subject): void;
}
