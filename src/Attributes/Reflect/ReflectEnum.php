<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes\Reflect;

use Crell\AttributeUtils\FromReflectionClass;
use Crell\AttributeUtils\FromReflectionEnum;
use Crell\AttributeUtils\ParseClassConstants;
use Crell\AttributeUtils\ParseEnumCases;
use Crell\AttributeUtils\ParseMethods;
use Crell\AttributeUtils\ParseProperties;
use Crell\AttributeUtils\ClassType;

class ReflectEnum implements FromReflectionEnum, ParseMethods, ParseClassConstants, ParseEnumCases
{
    /** @var ReflectMethod[] */
    public readonly array $methods;

    /** @var ReflectClassConstant[] */
    public readonly array $constants;

    /** @var ReflectEnumCase[] */
    public readonly array $cases;

    /**
     * The full of the enum, including namespace.
     */
    public readonly string $phpName;

    /**
     * The short name of the enum, without namespace.
     */
    public readonly string $shortName;

    /**
     * The namespace of the enum.
     */
    public readonly string $namespace;

    /**
     * True if this enum is defined by an extension, false if in userspace PHP code.
     */
    public readonly bool $isInternal;

    /**
     * True if this enum can be iterated (is Traversable), false otherwise.
     */
    public readonly bool $isIterable;

    /**
     * True if this is a backed enum, false otherwise.
     */
    public readonly bool $isBacked;

    /**
     * The type of backing value, int or string. null if not a Backed Enum.
     */
    public readonly ?string $backingType;

    public function fromReflection(\ReflectionEnum $subject): void
    {
        $this->phpName = $subject->getName();
        $this->shortName = $subject->getShortName();
        $this->namespace = $subject->getNamespaceName();
        $this->isInternal = $subject->isInternal();
        // isUserDefined() is the inverse of isInternal, so no need to cache that.

        $this->isBacked = $subject->isBacked();

        $this->backingType = $this->isBacked ? $subject->getBackingType()->getName() : null;

        // @todo getFileName, getStartLine, getEndLine - Needed or no? Should they go in a separate struct?
        // @todo do we want getDocComment, or is that too much data to cache?

        // @todo getTraits(), getTraitNames(), Do we include traits or not?

        $this->isIterable = $subject->isIterable();

        // @todo We're ignoring extension information for now.
    }

    public function setConstants(array $constants): void
    {
        // There's no way to tell a constant apart from a case in advance,
        // so we have to accept all of them and then filter.  Yes, this is gross.
        $this->constants = array_diff_key($constants, $this->cases);
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

    public function setCases(array $cases): void
    {
        $this->cases = $cases;
    }

    public function includeCasesByDefault(): bool
    {
        return true;
    }

    public function caseAttribute(): string
    {
        return ReflectEnumCase::class;
    }

}
