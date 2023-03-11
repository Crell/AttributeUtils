<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

interface ReadsClass
{
    public function fromClassAttribute(object $class): void;
}
