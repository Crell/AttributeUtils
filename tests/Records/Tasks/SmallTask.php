<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records\Tasks;

use Crell\AttributeUtils\Attributes\TransitivePropertyAttribute;
use Crell\AttributeUtils\Attributes\TransitivePropertySubAttribute;

#[TransitivePropertyAttribute(beep: 'SmallTask')]
#[TransitivePropertySubAttribute(title: 'smallie')]
class SmallTask extends Task
{
    public string $name;
}
