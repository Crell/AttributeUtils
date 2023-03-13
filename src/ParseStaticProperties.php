<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

/**
 * Marks a class-targeting attribute as having static properties to parse.
 *
 * If a class attribute has this interface, then after it is created
 * the analyzer will check all static properties of the class for the attribute
 * returned by propertyAttribute().  If one is absent (once inheritance
 * and transtivity is taken into account), then it will be omitted if
 * includeStaticPropertiesByDefault() is false, or will be created with no constructor
 * arguments if it returns true.  The whole list of resulting property
 * attribute objects will be passed to the setStaticProperties() method for
 * the class attribute to save and then use however it wishes.
 *
 * Note that it is an error if the property attribute has required
 * arguments, includeStaticPropertiesByDefault() is true, and any property is missing
 * an attribute.
 */
interface ParseStaticProperties
{
    /**
     * @param array<string, object> $properties
     *   The attribute objects on the properties.
     */
    public function setStaticProperties(array $properties): void;

    public function includeStaticPropertiesByDefault(): bool;

    public function staticPropertyAttribute(): string;
}
