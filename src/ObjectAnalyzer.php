<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer;

class ObjectAnalyzer
{

    public function analyze(string|object $class, string $attribute): object
    {
        $subject = match (is_string($class)) {
            true => new \ReflectionClass($class),
            false => new \ReflectionObject($class),
        };

        $classDef = $this->getAttribute($subject, $attribute) ?? new $attribute;

        if ($classDef instanceof ReflectionPopulatable) {
            $classDef->fromReflection($subject);
        }

        if ($classDef instanceof Fieldable) {
            $fields = $this->getPropertyDefinitions($subject, $classDef::propertyAttribute());
            $classDef->setFields($fields);
        }

        return $classDef;
    }

    protected function getPropertyDefinitions(\ReflectionObject|\ReflectionClass $subject, string $propertyAttribute): array
    {
        $rProperties = $subject->getProperties();
        // @todo Convert to first-class-callables when those are merged.
        $properties = array_map(fn(\ReflectionProperty $p) => $this->getPropertyDefinition($p, $propertyAttribute), $rProperties);
        //$fields = array_filter($fields, fn(Field $f): bool => !$f->skip);
        return $properties;
    }

    protected function getPropertyDefinition(\ReflectionProperty $property, string $propertyAttribute): object
    {
        $propDef = $this->getAttribute($property, $propertyAttribute) ?? new $propertyAttribute();
        if ($propDef instanceof ReflectionPopulatable) {
            $propDef->fromReflection($property);
        }

        return $propDef;
    }

    protected function getAttribute(\Reflector $target, string $name): ?object
    {
        return $this->getAttributes($target, $name)[0] ?? null;
    }

    protected function getAttributes(\Reflector $target, string $name): array
    {
        return array_map(static fn(\ReflectionAttribute $attrib)
        => $attrib->newInstance(), $target->getAttributes($name, \ReflectionAttribute::IS_INSTANCEOF));
    }
}
