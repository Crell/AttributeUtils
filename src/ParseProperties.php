<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

/**
 * Marks a class-targeting attribute as having properties to parse.
 *
 * If a class attribute has this interface, then after it is created
 * the analyzer will check all properties of the class for the attribute
 * returned by propertyAttribute().  If one is absent (once inheritance
 * and transtivity is taken into account), then it will be omitted if
 * includePropertiesByDefault() is false, or will be created with no constructor
 * arguments if it returns true.  The whole list of resulting property
 * attribute objects will be passed to the setProperties() method for
 * the class attribute to save and then use however it wishes.
 *
 * Note that it is an error if the property attribute has required
 * arguments, includePropertiesByDefault() is true, and any property is missing
 * an attribute.
 */
interface ParseProperties
{
    /**
     * @param array<string, object> $properties
     *   The attribute objects on the properties.
     */
    public function setProperties(array $properties): void;

    public function includePropertiesByDefault(): bool;

    public function propertyAttribute(): string;
}
