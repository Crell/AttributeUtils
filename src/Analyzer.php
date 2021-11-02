<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

use function Crell\fp\afilter;
use function Crell\fp\amap;
use function Crell\fp\indexBy;
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

        $subject = new \ReflectionClass($class);

        try {
            $classDef = $this->parser->getInheritedAttribute($subject, $attribute) ?? new $attribute;

            if ($classDef instanceof FromReflectionClass) {
                $classDef->fromReflection($subject);
            }

            $this->loadSubAttributes($classDef, $subject);

            if ($classDef instanceof ParseProperties) {
                $properties = $this->getPropertyDefinitions($subject, $classDef->propertyAttribute(), $classDef->includePropertiesByDefault());
                $classDef->setProperties($properties);
            }

            if ($classDef instanceof ParseMethods) {
                $methods = $this->getMethodDefinitions($subject, $classDef->methodAttribute(), $classDef->includeMethodsByDefault());
                $classDef->setMethods($methods);
            }

            if ($classDef instanceof ParseConstants) {
                $methods = $this->getConstantDefinitions($subject, $classDef->constantAttribute(), $classDef->includeConstantsByDefault());
                $classDef->setConstants($methods);
            }

            return $classDef;
        } catch (\ArgumentCountError $e) {
            $this->translateArgumentCountError($e);
        }
    }

    protected function getConstantDefinitions(\ReflectionClass $subject, string $methodAttribute, bool $includeByDefault): array
    {
        return pipe(
            $subject->getReflectionConstants(),
            indexBy(static fn (\ReflectionClassConstant $r): string => $r->getName()),
            amap(fn (\ReflectionClassConstant $r) => $this->getConstantDefinition($r, $methodAttribute, $includeByDefault)),
            afilter(),
            afilter(static fn (object $attr): bool => !($attr instanceof Excludable && $attr->exclude())),
        );
    }

    protected function getConstantDefinition(\ReflectionClassConstant $rConstant, string $methodAttribute, bool $includeByDefault): ?object
    {
        $constDef = $this->parser->getInheritedAttribute($rConstant, $methodAttribute)
            ?? ($includeByDefault ?  new $methodAttribute() : null);

        if ($constDef instanceof FromReflectionConstant) {
            $constDef->fromReflection($rConstant);
        }

        $this->loadSubAttributes($constDef, $rConstant);

        return $constDef;
    }

    protected function getMethodDefinitions(\ReflectionClass $subject, string $methodAttribute, bool $includeByDefault): array
    {
        return pipe(
            $subject->getMethods(),
            indexBy(static fn (\ReflectionMethod $r): string => $r->getName()),
            amap(fn (\ReflectionMethod $r) => $this->getMethodDefinition($r, $methodAttribute, $includeByDefault)),
            afilter(),
            afilter(static fn (object $attr):bool => !($attr instanceof Excludable && $attr->exclude())),
        );
    }

    protected function getMethodDefinition(\ReflectionMethod $rMethod, string $methodAttribute, bool $includeByDefault): ?object
    {
        $methodDef = $this->parser->getInheritedAttribute($rMethod, $methodAttribute)
            ?? ($includeByDefault ?  new $methodAttribute() : null);

        if ($methodDef instanceof FromReflectionMethod) {
            $methodDef->fromReflection($rMethod);
        }

        $this->loadSubAttributes($methodDef, $rMethod);

        if ($methodDef instanceof ParseParameters) {
            $parameters = $this->getParameterDefinitions($rMethod, $methodDef->parameterAttribute(), $methodDef->includeParametersByDefault());
            $methodDef->setParameters($parameters);
        }

        return $methodDef;
    }

    protected function getParameterDefinitions(\ReflectionMethod $subject, string $propertyAttribute, bool $includeByDefault): array
    {
        return pipe(
            $subject->getParameters(),
            indexBy(static fn (\ReflectionParameter $r): string => $r->getName()),
            amap(fn (\ReflectionParameter $p) => $this->getParameterDefinition($p, $propertyAttribute, $includeByDefault)),
            afilter(),
            afilter(static fn (object $attr):bool => !($attr instanceof Excludable && $attr->exclude())),
        );
    }

    protected function getParameterDefinition(\ReflectionParameter $rParameter, string $propertyAttribute, bool $includeByDefault): ?object
    {
        $paramDef = $this->parser->getInheritedAttribute($rParameter, $propertyAttribute)
            ?? ($includeByDefault ?  new $propertyAttribute() : null);

        if ($paramDef instanceof FromReflectionParameter) {
            $paramDef->fromReflection($rParameter);
        }

        $this->loadSubAttributes($paramDef, $rParameter);

        return $paramDef;
    }

    protected function getPropertyDefinitions(\ReflectionClass $subject, string $propertyAttribute, bool $includeByDefault): array
    {
        return pipe(
            $subject->getProperties(),
            indexBy(static fn (\ReflectionProperty $r): string => $r->getName()),
            amap(fn (\ReflectionProperty $p) => $this->getPropertyDefinition($p, $propertyAttribute, $includeByDefault)),
            afilter(),
            afilter(static fn (object $attr):bool => !($attr instanceof Excludable && $attr->exclude())),
        );
    }

    protected function getPropertyDefinition(\ReflectionProperty $rProperty, string $propertyAttribute, bool $includeByDefault): ?object
    {
        $propDef = $this->parser->getInheritedAttribute($rProperty, $propertyAttribute)
            ?? ($includeByDefault ?  new $propertyAttribute() : null);

        if ($propDef instanceof FromReflectionProperty) {
            $propDef->fromReflection($rProperty);
        }

        $this->loadSubAttributes($propDef, $rProperty);

        return $propDef;
    }

    protected function loadSubAttributes(?object $attribute, \ReflectionProperty|\ReflectionMethod|\ReflectionParameter|\ReflectionClass|\ReflectionClassConstant $reflection): void
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

        return (bool)($rAttribs[0]?->newInstance()?->flags & \Attribute::IS_REPEATABLE);
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
        // This is absolutely hideous, but this is what happens when your throwable
        // puts all the useful information in the message text rather than as useful
        // properties or methods or something.
        // Conclusion: Write better, more debuggable exceptions than PHP does.
        $message = $error->getMessage();
        [$classAndMethod, $passedCount, $file, $line, $expectedCount] = sscanf(
            string: $message,
            format: "Too few arguments to function %s::%s, %d passed in %s on line %d and exactly %d expected"
        );
        [$className, $methodName] = \explode('::', $classAndMethod);

        throw RequiredAttributeArgumentsMissing::create($className, $error);
    }
}
