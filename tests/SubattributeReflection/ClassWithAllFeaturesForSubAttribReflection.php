<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\SubattributeReflection;

#[ClassAllFeaturesForSubAttrib]
#[SubAttributeReflect]
class ClassWithAllFeaturesForSubAttribReflection
{
    #[ComponentAttribute]
    #[SubAttributeReflect]
    public const AConstant = 5;

    public function __construct(
        #[ComponentAttribute]
        #[SubAttributeReflect]
        public string $classA = 'A',
    ) {}

    #[ComponentAttribute]
    #[SubAttributeReflect]
    public function method(
        #[ComponentAttribute]
        #[SubAttributeReflect]
        string $parameter = 'A'
    ): string {
        return $parameter;
    }
}
