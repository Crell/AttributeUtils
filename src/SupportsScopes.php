<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

interface SupportsScopes
{
    /**
     * The scopes this attribute is part of.
     */
    public function scopes(): array;
}
