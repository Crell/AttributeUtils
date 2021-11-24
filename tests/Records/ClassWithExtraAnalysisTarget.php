<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records;

use Crell\AttributeUtils\Attributes\BasicProperty;
use Crell\AttributeUtils\Attributes\GenericClass;

#[GenericClass(propertyAttribute: BasicProperty::class)]
class ClassWithExtraAnalysisTarget
{
    public function __construct(
        public string $a = '',
        public string $b = '',
    ) {}
}
