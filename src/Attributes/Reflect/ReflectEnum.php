<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes\Reflect;

use Crell\AttributeUtils\FromReflectionEnum;
use Crell\AttributeUtils\ParseClassConstants;
use Crell\AttributeUtils\ParseEnumCases;
use Crell\AttributeUtils\ParseMethods;
use Crell\AttributeUtils\ParseStaticMethods;

#[\Attribute(\Attribute::TARGET_CLASS)]
class ReflectEnum implements FromReflectionEnum, ParseMethods, ParseStaticMethods, ParseClassConstants, ParseEnumCases
{
    use CollectClassConstants;
    use CollectMethods;
    use CollectStaticMethods;
    use CollectEnumCases;

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

        // getName() is a valid method on ReflectionType, even if the
        // stubs in PHP are outdated.
        // @phpstan-ignore-next-line
        $this->backingType = $this->isBacked ? $subject->getBackingType()?->getName() : null;

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

    public function constantAttribute(): string
    {
        return ReflectClassConstant::class;
    }

    public function methodAttribute(): string
    {
        return ReflectMethod::class;
    }

    public function staticMethodAttribute(): string
    {
        return ReflectMethod::class;
    }

    public function caseAttribute(): string
    {
        return ReflectEnumCase::class;
    }
}
