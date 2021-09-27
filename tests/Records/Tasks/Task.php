<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records\Tasks;

use Crell\AttributeUtils\Attributes\TransitivePropertyAttribute;

#[TransitivePropertyAttribute(beep: 'boop')]
class Task
{
    public string $name;
}
