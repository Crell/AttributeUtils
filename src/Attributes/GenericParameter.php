<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;

/**
 * Generic class for parsing parameters.
 *
 * Use this attribute when you want to know what parameters exist,
 * but don't really care about them beyond that.
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
class GenericParameter
{

}
