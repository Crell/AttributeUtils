<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

/**
 * Marks a class-targeting attribute as having constants to parse.
 *
 * If a class attribute has this interface, then after it is created
 * the analyzer will check all constants of the class for the attribute
 * returned by constantAttribute().  If one is absent (once inheritance
 * and transtivity is taken into account), then it will be omitted if
 * includeConstantsByDefault() is false, or will be created with no constructor
 * arguments if it returns true.  The whole list of resulting constant
 * attribute objects will be passed to the setConstants() method for
 * the class attribute to save and then use however it wishes.
 *
 * Note that it is an error if the constant attribute has required
 * arguments, includeConstantsByDefault() is true, and any constant is missing
 * an attribute.
 */
interface ParseClassConstants
{
    /**
     * @param array<string, object> $constants
     *   The attribute objects on the constants.
     */
    public function setConstants(array $constants): void;

    public function includeConstantsByDefault(): bool;

    public function constantAttribute(): string;
}
