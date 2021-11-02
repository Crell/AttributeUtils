<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

use function Crell\fp\afilter;
use function Crell\fp\amap;
use function Crell\fp\indexBy;
use function Crell\fp\pipe;

class Analyzer implements ClassAnalyzer
{
    protected AttributeParser $parser;

    public function __construct()
    {
        $this->parser = new AttributeParser();
    }

    public function analyze(string|object $class, string $attribute): object
    {
        // Everything is easier if we normalize to a class first.
        // Because anon classes have generated internal class names, they work, too.
        $class = is_string($class) ? $class : $class::class;

        $subject = new \ReflectionClass($class);

        // @todo Catch an error/exception here and wrap it in a better one,
        // if the attribute has required fields but isn't specified.
        $classDef = $this->parser->getInheritedAttribute($subject, $attribute) ?? new $attribute;

        if ($classDef instanceof FromReflectionClass) {
            $classDef->fromReflection($subject);
        }

        $classDef = $this->loadSubAttributes($classDef, $subject);

        if ($classDef instanceof ParseProperties) {
            $properties = $this->getPropertyDefinitions($subject, $classDef->propertyAttribute(), $classDef->includePropertiesByDefault());
            $classDef->setProperties($properties);
        }

        if ($classDef instanceof ParseMethods) {
            $methods = $this->getMethodDefinitions($subject, $classDef->methodAttribute(), $classDef->includeMethodsByDefault());
            $classDef->setMethods($methods);
        }

        // @todo Add support for parsing methods, maybe constants?

        return $classDef;
    }

    protected function getMethodDefinitions(\ReflectionClass $subject, string $methodAttribute, bool $includeByDefault): array
    {
        return pipe(
            $subject->getMethods(),
            indexBy(static fn (\ReflectionMethod $r): string => $r->getName()),
            amap(fn (\ReflectionMethod $r) => $this->getMethodDefinition($r, $methodAttribute, $includeByDefault)),
            afilter(),
            afilter(static fn (object $prop):bool => !($prop->exclude ?? false)),
        );
    }

    protected function getMethodDefinition(\ReflectionMethod $rMethod, string $methodAttribute, bool $includeByDefault): ?object
    {
        // @todo Catch an error/exception here and wrap it in a better one,
        // if the attribute has required fields but isn't specified.
        $methodDef = $this->parser->getInheritedAttribute($rMethod, $methodAttribute)
            ?? ($includeByDefault ?  new $methodAttribute() : null);

        if ($methodDef instanceof FromReflectionMethod) {
            $methodDef->fromReflection($rMethod);
        }

        $methodDef = $this->loadSubAttributes($methodDef, $rMethod);

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
            afilter(static fn (object $prop):bool => !($prop->exclude ?? false)),
        );
    }

    protected function getParameterDefinition(\ReflectionParameter $rParameter, string $propertyAttribute, bool $includeByDefault): ?object
    {
        // @todo Catch an error/exception here and wrap it in a better one,
        // if the attribute has required fields but isn't specified.
        $paramDef = $this->parser->getInheritedAttribute($rParameter, $propertyAttribute)
            ?? ($includeByDefault ?  new $propertyAttribute() : null);

        if ($paramDef instanceof FromReflectionParameter) {
            $paramDef->fromReflection($rParameter);
        }

        $paramDef = $this->loadSubAttributes($paramDef, $rParameter);

        return $paramDef;
    }

    protected function getPropertyDefinitions(\ReflectionClass $subject, string $propertyAttribute, bool $includeByDefault): array
    {
        return pipe(
            $subject->getProperties(),
            indexBy(static fn (\ReflectionProperty $r): string => $r->getName()),
            amap(fn (\ReflectionProperty $p) => $this->getPropertyDefinition($p, $propertyAttribute, $includeByDefault)),
            afilter(),
            afilter(static fn (object $prop):bool => !($prop->exclude ?? false)),
        );
    }

    protected function getPropertyDefinition(\ReflectionProperty $rProperty, string $propertyAttribute, bool $includeByDefault): ?object
    {
        // @todo Catch an error/exception here and wrap it in a better one,
        // if the attribute has required fields but isn't specified.
        $propDef = $this->parser->getInheritedAttribute($rProperty, $propertyAttribute)
            ?? ($includeByDefault ?  new $propertyAttribute() : null);

        if ($propDef instanceof FromReflectionProperty) {
            $propDef->fromReflection($rProperty);
        }

        $propDef = $this->loadSubAttributes($propDef, $rProperty);

        return $propDef;
    }

    protected function loadSubAttributes(?object $attribute, \ReflectionProperty|\ReflectionMethod|\ReflectionParameter|\ReflectionClass $reflection): ?object
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

        return $attribute;
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
}
