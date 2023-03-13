<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

/**
 * Marks a class-targeting attribute as having static methods to parse.
 *
 * If a class attribute has this interface, then after it is created
 * the analyzer will check all methods of the class for the attribute
 * returned by methodAttribute().  If one is absent (once inheritance
 * and transtivity is taken into account), then it will be omitted if
 * includeStaticMethodsByDefault() is false, or will be created with no constructor
 * arguments if it returns true.  The whole list of resulting method
 * attribute objects will be passed to the setMethods() method for
 * the class attribute to save and then use however it wishes.
 *
 * Note that it is an error if the method attribute has required
 * arguments, includeStaticMethodsByDefault() is true, and any method is missing
 * an attribute.
 */
interface ParseStaticMethods
{
    /**
     * @param array<string, object> $methods
     *   The attribute objects on the methods.
     */
    public function setStaticMethods(array $methods): void;

    public function includeStaticMethodsByDefault(): bool;

    public function staticMethodAttribute(): string;
}
