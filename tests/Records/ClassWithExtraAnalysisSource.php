<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records;

use Crell\AttributeUtils\Attributes\CustomProcessingProperty;
use Crell\AttributeUtils\Attributes\GenericClass;

#[GenericClass(propertyAttribute: CustomProcessingProperty::class)]
class ClassWithExtraAnalysisSource
{
    public function __construct(
        public int $one = 1,
        public ?ClassWithExtraAnalysisTarget $target = null,
    ) {}
}
