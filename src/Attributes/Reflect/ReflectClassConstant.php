<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes\Reflect;

use Crell\AttributeUtils\FromReflectionClassConstant;

class ReflectClassConstant implements FromReflectionClassConstant
{
    use HasVisibility;

    /**
     * The name of the constant, as PHP defines it.
     */
    public string $phpName;

    /**
     * The value of the constant.
     */
    public int|string|array|object $value;

    /**
     * True if this is a final constant, false otherwise.
     */
    public bool $isFinal;

    public function fromReflection(\ReflectionClassConstant $subject): void
    {
        $this->phpName = $subject->getName();
        $this->value = $subject->getValue();

        $this->parseVisibility($subject);

        $this->isFinal = $subject->isFinal();

        // @todo Do we include doc comment, or the declaring class?
    }
}
