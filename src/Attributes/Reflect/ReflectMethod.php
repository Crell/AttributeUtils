<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Attributes\Reflect;

use Crell\AttributeUtils\FromReflectionMethod;
use Crell\AttributeUtils\ParseParameters;
use Crell\AttributeUtils\TypeDef;

#[\Attribute(\Attribute::TARGET_METHOD)]
class ReflectMethod implements FromReflectionMethod, ParseParameters
{
    use HasVisibility;

    /** @var ReflectParameter[] */
    public readonly array $parameters;

    /**
     * The name of the method, as PHP defines it.
     */
    public readonly string $phpName;

    /**
     * True if this method is defined by an extension, false if in userspace PHP code.
     */
    public readonly bool $isInternal;

    /**
     * True if this method is a generator (contains yield), false otherwise.
     */
    public readonly bool $isGenerator;

    /**
     * True if this method has an explicit variadic parameter, false otherwise.
     */
    public readonly bool $isVariadic;

    /**
     * True if this method returns a value by reference, false otherwise.
     */
    public readonly bool $returnsReference;

    /**
     * True if there is an explicit return type defined, false otherwise.
     */
    public readonly bool $hasReturnType;

    /**
     * True if this is an abstract method, false otherwise.
     */
    public readonly bool $isAbstract;

    /**
     * True if this is a final method, false otherwise.
     */
    public readonly bool $isFinal;

    /**
     * True if this is a static method, false otherwise.
     */
    public readonly bool $isStatic;

    public readonly MethodType $methodType;

    /**
     * The return type of this method.
     *
     * A missing type declaration will be treated as "mixed".
     *
     * If you need to know whether a return type was specified at all,
     * check the $hasReturnType property.
     */
    public TypeDef $returnType;

    public function fromReflection(\ReflectionMethod $subject): void
    {
        $this->phpName = $subject->getName();

        // @todo I'm not convinced isDeprecated() is useful, so skipping that.

        $this->isInternal = $subject->isInternal();
        // isUserDefined() is the inverse of isInternal, so no need to cache that.

        $this->isGenerator = $subject->isGenerator();
        $this->isVariadic = $subject->isVariadic();
        $this->returnsReference = $subject->returnsReference();
        $this->isAbstract = $subject->isAbstract();
        $this->isFinal = $subject->isFinal();
        $this->isStatic = $subject->isStatic();

        $this->parseVisibility($subject);

        $this->methodType = match (true) {
            $subject->isConstructor() => MethodType::Constructor,
            $subject->isDestructor() => MethodType::Destructor,
            default => MethodType::Normal,
        };

        // @todo Skipping extension info, file lines, doc comment, etc.

        // @todo getNumberOfParameters and getNumberOfRequiredParameters seem redundant with having the parameters available.

        $this->returnType = new TypeDef($subject->getReturnType());
    }

    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    public function includeParametersByDefault(): bool
    {
        return true;
    }

    public function parameterAttribute(): string
    {
        return ReflectParameter::class;
    }
}
