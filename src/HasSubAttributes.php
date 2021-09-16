<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

interface HasSubAttributes
{
    /**
     * @return array<string, string>
     *   A mapping of attribute class name to the callback method that should be called with it.
     */
    public function subAttributes(): array;
}
