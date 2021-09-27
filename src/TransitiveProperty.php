<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

/**
 * Marker interface for a transitive property attribute.
 *
 * When a property is scanned for an attribute that is not specified
 * but implements this interface, and the property is typed for a class/interface,
 * then that class/interface will be checked for the attribute as well.
 *
 * Note that this only works if the attribute in question is legal on both
 * properties and classes.
 */
interface TransitiveProperty
{

}
