<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

interface SupportsGroups
{
    /**
     * The group this attribute is part of.
     */
    public function groups(): array;
}
