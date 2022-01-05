<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

use function Crell\fp\afilter;
use function Crell\fp\amap;
use function Crell\fp\indexBy;
use function Crell\fp\method;
use function Crell\fp\pipe;

class Analyzer implements ClassAnalyzer
{
    public function __construct(protected ?AttributeParser $parser = null)
    {
        $this->parser ??= new AttributeParser();
    }

    public function analyze(string|object $class, string $attribute): object
    {
        // Everything is easier if we normalize to a class first.
        // Because anon classes have generated internal class names, they work, too.
        $class = is_string($class) ? $class : $class::class;

        // I don't love this special casing, but it's the only way to handle
        // enums themselves being special cases.
        if (function_exists('enum_exists') && enum_exists($class)) {
            $subject = new \ReflectionEnum($class);
        } else {
            $subject = new \ReflectionClass($class);
        }

        try {
            $classDef = $this->parser->getInheritedAttribute($subject, $attribute) ?? new $attribute;

            if ($classDef instanceof FromReflectionClass) {
                $classDef->fromReflection($subject);
            }

            if ($classDef instanceof FromReflectionEnum) {
                $classDef->fromReflection($subject);
            }

            $this->loadSubAttributes($classDef, $subject);

            if ($classDef instanceof ParseProperties) {
                $properties = $this->getDefinitions(
                    $subject->getProperties(),
                    fn (\ReflectionProperty $r) => $this->getPropertyDefinition($r, $classDef->propertyAttribute(), $classDef->includePropertiesByDefault())
                );
                $classDef->setProperties($properties);
            }

            if ($classDef instanceof ParseMethods) {
                $methods = $this->getDefinitions(
                    $subject->getMethods(),
                    fn (\ReflectionMethod $r) => $this->getMethodDefinition($r, $classDef->methodAttribute(), $classDef->includeMethodsByDefault()),
                );
                $classDef->setMethods($methods);
            }

            // Enum cases have to come before constants, because
            // constants will include enums cases.  It's up to the
            // implementing attribute class to filter out the enums
            // from the constants.  Sadly, there is no better API for it.
            if ($classDef instanceof ParseEnumCases) {
                $cases = $this->getDefinitions(
                    $subject->getCases(),
                    fn (\ReflectionEnumUnitCase $r) => $this->getCaseDefinition($r, $classDef->caseAttribute(), $classDef->includeCasesByDefault()),
                );
                $classDef->setCases($cases);
            }

            if ($classDef instanceof ParseClassConstants) {
                $constants = $this->getDefinitions(
                    $subject->getReflectionConstants(),
                    fn (\ReflectionClassConstant $r) => $this->getConstantDefinition($r, $classDef->constantAttribute(), $classDef->includeConstantsByDefault()),
                );
                $classDef->setConstants($constants);
            }

            if ($classDef instanceof CustomAnalysis) {
                $classDef->customAnalysis($this);
            }

            return $classDef;
        } catch (\ArgumentCountError $e) {
            $this->translateArgumentCountError($e);
            // This line is unreachable. It's here only to make phpstan
            // happy that this method always returns an object.
            // There is probably a much better way.
            // @phpstan-ignore-next-line
            return new \stdClass();
        }
    }

    /**
     * Gets all applicable attribute definitions of a given class element type.
     *
     * Eg, gets all property attributes, or all method attributes.
     *
     * @param \Reflector[] $reflections
     *   The reflection objects to turn into attributes.
     * @param callable $deriver
     *   Callback for turning a reflection object into the corresponding attribute.
     *   It must already have closed over the attribute type to retrieve.
     * @return array
     *   An array of attributes across all items of the applicable type.
     */
    protected function getDefinitions(array $reflections, callable $deriver): array
    {
        return pipe($reflections,
            // The Reflector interface is insufficient, but getName() is defined
            // on all types we care about. This is a reflection API limitation.
            // @phpstan-ignore-next-line
            indexBy(method('getName')),
            amap($deriver),
            afilter(static fn (?object $attr): bool => $attr && !($attr instanceof Excludable && $attr->exclude())),
        );
    }

    /**
     * Returns the attribute definition for an enum case.
     */
    protected function getCaseDefinition(\ReflectionEnumUnitCase $rCase, string $attributeType, bool $includeByDefault): ?object
    {
        $caseDef = $this->parser->getInheritedAttribute($rCase, $attributeType)
            ?? ($includeByDefault ?  new $attributeType() : null);

        if ($caseDef instanceof FromReflectionEnumCase) {
            $caseDef->fromReflection($rCase);
        }

        $this->loadSubAttributes($caseDef, $rCase);

        if ($caseDef instanceof CustomAnalysis) {
            $caseDef->customAnalysis($this);
        }

        return $caseDef;
    }

    /**
     * Returns the attribute definition for a class constant.
     */
    protected function getConstantDefinition(\ReflectionClassConstant $rConstant, string $attributeType, bool $includeByDefault): ?object
    {
        $constDef = $this->parser->getInheritedAttribute($rConstant, $attributeType)
            ?? ($includeByDefault ?  new $attributeType() : null);

        if ($constDef instanceof FromReflectionClassConstant) {
            $constDef->fromReflection($rConstant);
        }

        $this->loadSubAttributes($constDef, $rConstant);

        if ($constDef instanceof CustomAnalysis) {
            $constDef->customAnalysis($this);
        }

        return $constDef;
    }

    /**
     * Returns the attribute definition for a method.
     */
    protected function getMethodDefinition(\ReflectionMethod $rMethod, string $attributeType, bool $includeByDefault): ?object
    {
        $methodDef = $this->parser->getInheritedAttribute($rMethod, $attributeType)
            ?? ($includeByDefault ?  new $attributeType() : null);

        if ($methodDef instanceof FromReflectionMethod) {
            $methodDef->fromReflection($rMethod);
        }

        $this->loadSubAttributes($methodDef, $rMethod);

        if ($methodDef instanceof ParseParameters) {
            $parameters = $this->getDefinitions(
                $rMethod->getParameters(),
                fn (\ReflectionParameter $p) => $this->getParameterDefinition($p, $methodDef->parameterAttribute(), $methodDef->includeParametersByDefault())
            );
            $methodDef->setParameters($parameters);
        }

        if ($methodDef instanceof CustomAnalysis) {
            $methodDef->customAnalysis($this);
        }

        return $methodDef;
    }

    /**
     * Returns the attribute definition for a method parameter.
     */
    protected function getParameterDefinition(\ReflectionParameter $rParameter, string $attributeType, bool $includeByDefault): ?object
    {
        $paramDef = $this->parser->getInheritedAttribute($rParameter, $attributeType)
            ?? ($includeByDefault ?  new $attributeType() : null);

        if ($paramDef instanceof FromReflectionParameter) {
            $paramDef->fromReflection($rParameter);
        }

        $this->loadSubAttributes($paramDef, $rParameter);

        if ($paramDef instanceof CustomAnalysis) {
            $paramDef->customAnalysis($this);
        }

        return $paramDef;
    }

    /**
     * Returns the attribute definition for a class property.
     */
    protected function getPropertyDefinition(\ReflectionProperty $rProperty, string $attributeType, bool $includeByDefault): ?object
    {
        $propDef = $this->parser->getInheritedAttribute($rProperty, $attributeType)
            ?? ($includeByDefault ?  new $attributeType() : null);

        if ($propDef instanceof FromReflectionProperty) {
            $propDef->fromReflection($rProperty);
        }

        $this->loadSubAttributes($propDef, $rProperty);

        if ($propDef instanceof CustomAnalysis) {
            $propDef->customAnalysis($this);
        }

        return $propDef;
    }

    /**
     * Loads sub-attributes onto an attribute, if appropriate.
     */
    protected function loadSubAttributes(?object $attribute, \Reflector $reflection): void
    {
        if ($attribute instanceof HasSubAttributes) {
            foreach ($attribute->subAttributes() as $type => $callback) {
                if ($this->isMultivalueAttribute($type)) {
                    $attribute->$callback($this->parser->getInheritedAttributes($reflection, $type));
                } else {
                    $attribute->$callback($this->parser->getInheritedAttribute($reflection, $type));
                }
            }
        }
    }

    /**
     * Determines if a given attribute class allows repeating.
     *
     * If passed a non-attribute class, it will return false.
     */
    protected function isMultivalueAttribute(string $attributeType): bool
    {
        $rAttribs = (new \ReflectionClass($attributeType))
            ->getAttributes(\Attribute::class);
        if (!isset($rAttribs[0])) {
            return false;
        }

        return (bool)($rAttribs[0]->newInstance()->flags & \Attribute::IS_REPEATABLE);
    }

    /**
     * Throws a domain-specific exception based on an ArgumentCountError.
     *
     * This is absolutely hideous, but this is what happens when your throwable
     * puts all the useful information in the message text rather than as useful
     * properties or methods or something.
     *
     * Conclusion: Write better, more debuggable exceptions than PHP does.
     *
     * @todo In PHP 8.1, the return type can be `never`.
     */
    protected function translateArgumentCountError(\ArgumentCountError $error): void
    {
        $message = $error->getMessage();
        // PHPStan doesn't understand this syntax style of sscanf(), so skip it.
        // @phpstan-ignore-next-line
        [$classAndMethod, $passedCount, $file, $line, $expectedCount] = sscanf(
            // @phpstan-ignore-next-line
            string: $message,
            format: "Too few arguments to function %s::%s, %d passed in %s on line %d and exactly %d expected"
        );
        [$className, $methodName] = \explode('::', $classAndMethod);

        throw RequiredAttributeArgumentsMissing::create($className, $error);
    }
}
