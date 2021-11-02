<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

/**
 * Marks an attribute as excludable.
 *
 * When an attribute is to be included by default, it may opt-out
 * of doing so by implementing this interface and returning true
 * from exclude().  If the attribute does not implement this
 * interface, it cannot be skipped when analyzing a class.
 */
interface Excludable
{
    public function exclude(): bool;
}
