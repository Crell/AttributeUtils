<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

/**
 * Marks an attribute as having sub-attributes that should also be parsed.
 *
 * A sub-attribute is an attribute that applies to the same target, but
 * is listed separately.  Because the analyzer only analyzes a single attribute
 * on a class or property, a sub-attribute allows for a secondary block of data
 * to be incorporated into the attribute after it is created.
 *
 * Sub-attributes may also inherit or be transitive independently of the
 * main attribute.
 */
interface HasSubAttributes
{
    /**
     * @return array<string, string>
     *   A mapping of attribute class name to the callback method that should be called with it.
     */
    public function subAttributes(): array;
}
