<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes\Reflect;

use Crell\AttributeUtils\FromReflectionParameter;
use Crell\AttributeUtils\TypeDef;

#[\Attribute(\Attribute::TARGET_PARAMETER)]
class ReflectParameter implements FromReflectionParameter
{
    /**
     * The name of the parameter, as PHP defines it.
     */
    public readonly string $phpName;

    /**
     * True if this parameter is passed by reference, false if not.
     */
    public readonly bool $isPassByReference;

    /**
     * The position of the parameter, 0-based.
     */
    public readonly int $position;

    /**
     * True if this parameter is optional, false otherwise.
     */
    public readonly bool $isOptional;

    /**
     * True if this parameter is variadic, false otherwise.
     */
    public readonly bool $isVariadic;

    /**
     * The type of this parameter.
     *
     * A missing type declaration will be treated as "mixed".
     */
    public readonly TypeDef $type;

    public function fromReflection(\ReflectionParameter $subject): void
    {
        $this->phpName = $subject->getName();
        $this->isPassByReference = $subject->isPassedByReference();
        $this->position = $subject->getPosition();
        $this->isOptional = $subject->isOptional();
        $this->isVariadic = $subject->isVariadic();

        $this->type = new TypeDef($subject->getType());
    }

}
