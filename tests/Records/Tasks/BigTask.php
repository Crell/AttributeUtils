<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records\Tasks;

class BigTask extends Task
{
    public function __construct(public string $name) {}
}
