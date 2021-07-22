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

        // @todo Catch an error/exception here and wrap it in a better one,
        // if the attribute has required fields but isn't specified.
        $classDef = $this->getAttribute($subject, $attribute) ?? new $attribute;

        if ($classDef instanceof ReflectionPopulatable) {
            $classDef->fromReflection($subject);
        }

        if ($classDef instanceof Fieldable) {
            $fields = $this->getPropertyDefinitions($subject, $classDef::propertyAttribute(), $classDef->includeByDefault());
            $classDef->setFields($fields);
        }

        return $classDef;
    }

    protected function getPropertyDefinitions(\ReflectionObject|\ReflectionClass $subject, string $propertyAttribute, bool $includeByDefault): array
    {
        // @todo This needs a pipe.
        $rProperties = $subject->getProperties();
        $props = $this->indexBy($rProperties, fn (\ReflectionProperty $r) => $r->getName());
        $properties = array_map(fn(\ReflectionProperty $p) => $this->getPropertyDefinition($p, $propertyAttribute, $includeByDefault), $props);
        $properties = array_filter($properties);
        $properties = array_filter($properties, static fn (object $prop) => !($prop->exclude ?? false));
        //$fields = array_filter($fields, fn(Field $f): bool => !$f->skip);
        return $properties;
    }

    /**
     * @todo Break this out to a utility function.
     */
    protected function indexBy(array $arr, callable $keyMaker): array
    {
        $ret = [];
        foreach ($arr as $v) {
            $ret[$keyMaker($v)] = $v;
        }
        return $ret;
    }

    protected function getPropertyDefinition(\ReflectionProperty $property, string $propertyAttribute, bool $includeByDefault): ?object
    {
        // @todo Catch an error/exception here and wrap it in a better one,
        // if the attribute has required fields but isn't specified.
        $propDef = $this->getAttribute($property, $propertyAttribute)
            ?? ($includeByDefault ?  new $propertyAttribute() : null);
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
        return array_map(static fn (\ReflectionAttribute $attrib)
        => $attrib->newInstance(), $target->getAttributes($name, \ReflectionAttribute::IS_INSTANCEOF));
    }
}
