<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes\Reflect;

use Crell\AttributeUtils\FromReflectionProperty;

class ReflectProperty implements FromReflectionProperty
{
    use HasVisibility;

    /**
     * The name of the property, as PHP defines it.
     */
    public string $phpName;

    /**
     * True if this is a static property, false otherwise.
     *
     * @todo Urk, do we want to break static properties and methods out to their own type??
     */
    public bool $isStatic;

    /**
     * True if this is a dynamic property. False if it was declared in the source code.
     */
    public bool $isDynamic;

    /**
     * True if this property was declared via constructor promotion, false otherwise.
     */
    public bool $isPromoted;

    public function fromReflection(\ReflectionProperty $subject): void
    {
        $this->phpName = $subject->getName();

        // @todo Do we want to capture getValue() or no?

        $this->parseVisibility($subject);

        $this->isStatic = $subject->isStatic();

        // This naming makes more sense than "default".
        $this->isDynamic = !$subject->isDefault();

        // @todo Declaring class?  DocComment?

        $this->isPromoted = $subject->isPromoted();

        // @todo Do we want the default value and such?

        // @todo All the fun type bits.  Oy.

    }
}
