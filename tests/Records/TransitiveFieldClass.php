<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records;

use Crell\AttributeUtils\Records\Tasks\SmallTask;
use Crell\AttributeUtils\Records\Tasks\Task;

class TransitiveFieldClass
{
    public Task $task;

    public SmallTask $small;
}
