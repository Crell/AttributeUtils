<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes;

use Attribute;
use Crell\AttributeUtils\ClassAnalyzer;
use Crell\AttributeUtils\CustomAnalysis;
use Crell\AttributeUtils\FromReflectionProperty;

#[Attribute(Attribute::TARGET_PARAMETER)]
class CustomProcessingProperty implements CustomAnalysis, FromReflectionProperty
{
    public GenericClass $target;

    public string $phpType;

    public GenericClass $targetDef;

    public function fromReflection(\ReflectionProperty $subject): void
    {
        $this->phpType = $subject->getType()?->getName();
    }


    public function customAnalysis(ClassAnalyzer $analyzer): void
    {
        if (class_exists($this->phpType) || interface_exists($this->phpType)) {
            /** @var GenericClass $def */
            $def = $analyzer->analyze($this->phpType, GenericClass::class);
            $this->targetDef = $def;
        }
    }
}
