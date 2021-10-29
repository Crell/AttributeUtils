<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records;

use Crell\AttributeUtils\Attributes\GenericClass;
use Crell\AttributeUtils\Attributes\TransitivePropertyAttribute;
use Crell\AttributeUtils\Attributes\TransitivePropertySubAttribute;
use Crell\AttributeUtils\Records\Tasks\BigTask;
use Crell\AttributeUtils\Records\Tasks\SmallTask;
use Crell\AttributeUtils\Records\Tasks\Task;

#[GenericClass(propertyAttribute: TransitivePropertyAttribute::class)]
class TransitiveFieldClass
{
    public Task $task;

    public SmallTask $small;

    #[TransitivePropertySubAttribute(title: 'biggie')]
    public BigTask $big;
}
