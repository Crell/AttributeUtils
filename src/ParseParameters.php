<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

/**
 * Marks a method-targeting attribute as having parameters to parse.
 *
 * If a method attribute has this interface, then after it is created
 * the analyzer will check all parameters of the method for the attribute
 * returned by parameterAttribute().  If one is absent (once inheritance
 * and transtivity is taken into account), then it will be omitted if
 * includeParametersByDefault() is false, or will be created with no constructor
 * arguments if it returns true.  The whole list of resulting paraemter
 * attribute objects will be passed to the setParameters() method for
 * the method attribute to save and then use however it wishes.
 *
 * Note that it is an error if the parameter attribute has required
 * arguments, includeParametersByDefault() is true, and any property is missing
 * an attribute.
 */
interface ParseParameters
{
    /**
     * @param array<string, object> $parameters
     *   The attribute objects on the parameters.
     */
    public function setParameters(array $parameters): void;

    public function includeParametersByDefault(): bool;

    public function parameterAttribute(): string;
}
