<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

use Crell\AttributeUtils\Components\Component;

interface ReadsComponents
{
    /**
     * @return iterable<Component>
     */
    public function components(): iterable;
}
