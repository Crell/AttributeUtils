<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records\Reflect;

class ClassUsesTrait
{
    use SampleTrait;

    public function localMethod(): int
    {

    }

}
