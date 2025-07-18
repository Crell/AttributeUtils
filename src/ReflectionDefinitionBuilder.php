<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

use function Crell\fp\afilter;
use function Crell\fp\amap;
use function Crell\fp\indexBy;
use function Crell\fp\method;
use function Crell\fp\pipe;

/**
 * Derives the definition for a component's attributes.
 */
class ReflectionDefinitionBuilder
{
    public function __construct(
        protected readonly AttributeParser $parser,
        protected readonly ?Analyzer $analyzer = null,
    ) {}

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
     * @return array<string, object>
     *   An array of attributes across all items of the applicable type.
     */
    public function getDefinitions(array $reflections, callable $deriver): array
    {
        return pipe($reflections,
            // The Reflector interface is insufficient, but getName() is defined
            // on all types we care about. This is a reflection API limitation.
            indexBy(method('getName')),
            amap($deriver),
            afilter(static fn (?object $attr): bool => $attr && !($attr instanceof Excludable && $attr->exclude())),
        );
    }

    /**
     * Returns the attribute definition for a class component.
     */
    public function getComponentDefinition(\Reflector $reflection, string $attributeType, bool $includeByDefault, string $reflectionInterface, object $classDef): ?object
    {
        // @todo This is a problem. IF an attribute supports scopes, and is excluded,
        // then we do NOT want to have a default empty added, regardless of $includeByDefault.
        // I think?  But we don't know about scopes at this point, which means we don't know
        // what we should do here. I don't know how to solve this.
        $def = $this->parser->getInheritedAttribute($reflection, $attributeType)
            ?? ($includeByDefault ?  new $attributeType() : null);

        if ($def instanceof $reflectionInterface) {
            // This is just too dynamic for PHPstan to handle.
            // @phpstan-ignore-next-line
            $def->fromReflection($reflection);
        }

        $this->loadSubAttributes($def, $reflection);

        if ($def instanceof CustomAnalysis && $this->analyzer) {
            $def->customAnalysis($this->analyzer);
        }

        if ($def instanceof ReadsClass) {
            $def->fromClassAttribute($classDef);
        }

        if ($def instanceof Finalizable) {
            $def->finalize();
        }

        return $def;
    }

    /**
     * Returns the attribute definition for a method.
     *
     * Methods can't just reuse getComponentDefinition() because they
     * also have parameters of their own to parse.
     */
    public function getMethodDefinition(\ReflectionMethod $reflection, string $attributeType, bool $includeByDefault, object $classDef): ?object
    {
        $def = $this->parser->getInheritedAttribute($reflection, $attributeType)
            ?? ($includeByDefault ?  new $attributeType() : null);

        if ($def instanceof FromReflectionMethod) {
            $def->fromReflection($reflection);
        }

        $this->loadSubAttributes($def, $reflection);

        if ($def instanceof ParseParameters) {
            $parameters = $this->getDefinitions(
                $reflection->getParameters(),
                fn (\ReflectionParameter $p)
                    => $this->getComponentDefinition($p, $def->parameterAttribute(), $def->includeParametersByDefault(), FromReflectionParameter::class, $classDef)
            );
            $def->setParameters($parameters);
        }

        if ($def instanceof CustomAnalysis && $this->analyzer) {
            $def->customAnalysis($this->analyzer);
        }

        if ($def instanceof ReadsClass) {
            $def->fromClassAttribute($classDef);
        }

        if ($def instanceof Finalizable) {
            $def->finalize();
        }

        return $def;
    }

    /**
     * Loads sub-attributes onto an attribute, if appropriate.
     */
    public function loadSubAttributes(?object $attribute, \Reflector $reflection): void
    {
        if ($attribute instanceof HasSubAttributes) {
            foreach ($attribute->subAttributes() as $type => $callback) {
                if ($this->isMultivalueAttribute($type)) {
                    $subs = $this->parser->getInheritedAttributes($reflection, $type);
                    foreach ($subs as $sub) {
                        $this->applySubattributeFeatures($sub, $reflection);
                    }
                    if ($callback instanceof \Closure) {
                        $callback($subs);
                    } else {
                        $attribute->$callback($subs);
                    }
                } else {
                    $sub = $this->parser->getInheritedAttribute($reflection, $type);
                    if ($sub) {
                        $this->applySubattributeFeatures($sub, $reflection);
                    }
                    if ($callback instanceof \Closure) {
                        $callback($sub);
                    } else {
                        $attribute->$callback($sub);
                    }
                }
            }
        }
    }

    protected function applySubattributeFeatures(object $attribute, \Reflector $reflection): void
    {
        // For each possible type, check for a FromReflection interface.
        if ($reflection instanceof \ReflectionClass && ! $reflection instanceof \ReflectionEnum && $attribute instanceof FromReflectionClass) {
            /** @var \ReflectionClass<object> $reflection */
            $attribute->fromReflection($reflection);
        }
        if ($reflection instanceof \ReflectionEnum && $attribute instanceof FromReflectionEnum) {
            $attribute->fromReflection($reflection);
        }
        if ($reflection instanceof \ReflectionFunction && $attribute instanceof FromReflectionFunction) {
            $attribute->fromReflection($reflection);
        }
        if ($reflection instanceof \ReflectionProperty && $attribute instanceof FromReflectionProperty) {
            $attribute->fromReflection($reflection);
        }
        if ($reflection instanceof \ReflectionMethod && $attribute instanceof FromReflectionMethod) {
            $attribute->fromReflection($reflection);
        }
        if ($reflection instanceof \ReflectionClassConstant && $attribute instanceof FromReflectionClassConstant) {
            $attribute->fromReflection($reflection);
        }
        if ($reflection instanceof \ReflectionParameter && $attribute instanceof FromReflectionParameter) {
            $attribute->fromReflection($reflection);
        }

        if ($attribute instanceof Finalizable) {
            $attribute->finalize();
        }
        // Call recursively to allow sub-attributes on sub-attributes. (Yo Dawg.)
        $this->loadSubAttributes($attribute, $reflection);
    }

    /**
     * Determines if a given attribute class allows repeating.
     *
     * This is only meaningful for attributes used as sub-attributes.
     */
    protected function isMultivalueAttribute(string $attributeType): bool
    {
        return is_a($attributeType, Multivalue::class, true);
    }
}
