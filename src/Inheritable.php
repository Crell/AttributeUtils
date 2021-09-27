<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

/**
 * Marker interface for inheritable attributes.
 *
 * Attributes should implement this interface if you want a missing
 * attribute to check parent classes/interfaces for the attribute instead.
 *
 * This applies to both class and property attributes.
 */
interface Inheritable
{

}
