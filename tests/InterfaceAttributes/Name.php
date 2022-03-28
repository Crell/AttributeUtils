<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\InterfaceAttributes;

use Crell\AttributeUtils\Multivalue;

interface Name extends Multivalue
{
    public function fullName(): string;
}
