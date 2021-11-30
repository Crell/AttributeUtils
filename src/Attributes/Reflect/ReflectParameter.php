<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes\Reflect;

use Crell\AttributeUtils\FromReflectionParameter;

class ReflectParameter implements FromReflectionParameter
{
    /**
     * The name of the parameter, as PHP defines it.
     */
    public string $phpName;

    /**
     * True if this parameter is passed by reference, false if not.
     */
    public bool $isPassByReference;

    /**
     * The position of the parameter, 0-based.
     */
    public int $position;

    /**
     * True if this parameter is optional, false otherwise.
     */
    public bool $isOptional;

    /**
     * True if this parameter is variadic, false otherwise.
     */
    public bool $isVariadic;

    public function fromReflection(\ReflectionParameter $subject): void
    {
        $this->phpName = $subject->getName();
        $this->isPassByReference = $subject->isPassedByReference();
        $this->position = $subject->getPosition();
        $this->isOptional = $subject->isOptional();
        $this->isVariadic = $subject->isVariadic();

        // @todo All the type shenanigans.
    }

}
