<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;

/**
 * Generic class for parsing properties.
 *
 * This class attribute has no data of its own. All it's good for
 * is specifying how to parse properties in the class. You can use
 * this attribute if you just care about the properties but don't want
 * to bother making your own class-level attribute.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class GenericProperty
{

}
