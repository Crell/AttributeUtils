<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes\Reflect;

use Crell\AttributeUtils\FromReflectionClass;
use Crell\AttributeUtils\ParseClassConstants;
use Crell\AttributeUtils\ParseMethods;
use Crell\AttributeUtils\ParseProperties;
use Crell\AttributeUtils\ClassType;

class ReflectClass implements FromReflectionClass, ParseMethods, ParseProperties, ParseClassConstants
{
    /** @var ReflectProperty[] */
    public readonly array $properties;

    /** @var ReflectMethod[] */
    public readonly array $methods;

    /** @var ReflectClassConstant[] */
    public readonly array $constants;

    /**
     * The full of the class, including namespace.
     */
    public readonly string $phpName;

    /**
     * The short name of the class, without namespace.
     */
    public readonly string $shortName;

    /**
     * The namespace of the class.
     */
    public readonly string $namespace;

    /**
     * True if this class is defined by an extension, false if in userspace PHP code.
     */
    public readonly bool $isInternal;

    /**
     * True if the class can be instantiated, false otherwise.
     *
     * @todo Not sure if this is worth capturing.
     */
    public readonly bool $isInstantiable;

    /**
     * True if this class may be cloned, false otherwise.
     */
    public readonly bool $isCloneable;

    /**
     * True if this class can be iterated (is Traversable), false otherwise.
     */
    public readonly bool $isIterable;

    /**
     * True for a final class, false otherwise.
     */
    public readonly bool $isFinal;

    /**
     * The type of struct-y type this is.
     */
    public readonly ClassType $structType;

    public function fromReflection(\ReflectionClass $subject): void
    {
        $this->phpName = $subject->getName();
        $this->shortName = $subject->getShortName();
        $this->namespace = $subject->getNamespaceName();
        $this->isInternal = $subject->isInternal();
        // isUserDefined() is the inverse of isInternal, so no need to cache that.
        $this->isInstantiable = $subject->isInstantiable();
        $this->isCloneable = $subject->isCloneable();

        $this->structType = match (true) {
            $subject->isInterface() => ClassType::Interface,
            $subject->isTrait() => ClassType::Trait,
            $subject->isAnonymous() => ClassType::AnonymousClass,
            default => ClassType::NormalClass,
        };

        // @todo getFileName, getStartLine, getEndLine - Needed or no? Should they go in a separate struct?
        // @todo do we want getDocComment, or is that too much data to cache?

        // @todo getTraits(), getTraitNames(), Do we include traits or not?

        $this->isFinal = $subject->isFinal();

        // @todo getParentClass() returns a ReflectionClass.  What do we do with that?

        $this->isIterable = $subject->isIterable();

        // @todo We're ignoring extension information for now.
    }

    public function setConstants(array $constants): void
    {
        $this->constants = $constants;
    }

    public function includeConstantsByDefault(): bool
    {
        return true;
    }

    public function constantAttribute(): string
    {
        return ReflectClassConstant::class;
    }

    public function setMethods(array $methods): void
    {
        $this->methods = $methods;
    }

    public function includeMethodsByDefault(): bool
    {
        return true;
    }

    public function methodAttribute(): string
    {
        return ReflectMethod::class;
    }

    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    public function includePropertiesByDefault(): bool
    {
        return true;
    }

    public function propertyAttribute(): string
    {
        return ReflectProperty::class;
    }
}
