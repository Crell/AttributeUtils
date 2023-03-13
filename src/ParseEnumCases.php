<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

/**
 * Marks a enum-targeting attribute as having cases to parse.
 *
 * If an enum attribute has this interface, then after it is created
 * the analyzer will check all cases of the class for the attribute
 * returned by casesAttribute().  If one is absent (once inheritance
 * and transtivity is taken into account), then it will be omitted if
 * includeCasesByDefault() is false, or will be created with no constructor
 * arguments if it returns true.  The whole list of resulting case
 * attribute objects will be passed to the setCases() method for
 * the enum attribute to save and then use however it wishes.
 *
 * Note that it is an error if the enum attribute has required
 * arguments, includeCasesByDefault() is true, and any case is missing
 * an attribute.
 */
interface ParseEnumCases
{
    /**
     * @param array<string, object> $cases
     *   The attribute objects on the enum cases.
     */
    public function setCases(array $cases): void;

    public function includeCasesByDefault(): bool;

    public function caseAttribute(): string;
}
