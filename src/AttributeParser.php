<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

use function Crell\fp\afilter;
use function Crell\fp\all;
use function Crell\fp\amap;
use function Crell\fp\firstValue;
use function Crell\fp\method;
use function Crell\fp\pipe;

class AttributeParser
{
    /**
     * @param array<string|null> $scopes
     *   The scopes for which this analysis should run.
     */
    public function __construct(private readonly array $scopes = []) {}

    /**
     * Returns a single attribute of a given type from a target, or null if not found.
     */
    public function getAttribute(\Reflector $target, string $name): ?object
    {
        return $this->getAttributes($target, $name)[0] ?? null;
    }

    /**
     * Get all attributes of a given type from a target.
     *
     * Unfortunately PHP has no common interface for "reflection objects that support attributes",
     * and enumerating them manually is stupidly verbose and clunky. Instead just refer
     * to any reflectable thing and hope for the best.
     *
     * @return array<object>
     */
    public function getAttributes(\Reflector $target, string $name): array
    {
        // @phpstan-ignore-next-line.
        $attribs =  pipe($target->getAttributes($name, \ReflectionAttribute::IS_INSTANCEOF),
            amap(method('newInstance')),
        );

        if (is_a($name, SupportsScopes::class, true)) {
            $attribs = $this->filterForScopes($attribs, $this->scopes);
        }

        return array_values($attribs);
    }

    /**
     * Filters a list of attributes based on their declared scope.
     *
     * If the current scope list is empty, meaning no scope, then only
     * attributes that also have no scope at all (their scope list is `[null]`)
     * will be retained.
     *
     * If the current scope is non-empty, and there is at least one in-scope
     * attribute, then only attributes that match the current scope are returned.
     *
     * If there are only unscoped attributes, then those will be returned. That
     * allows a scoped lookup to still include all attributes that are not scope-specific.
     * To explicitly exclude an attribute in unscoped or scoped requests, implement
     * `Excludable` and mark it excluded in the appropriate scope.
     *
     * @param SupportsScopes[] $attribs
     *   An array of loaded attribute objects.
     * @param array<string|null> $currentScopes
     *   The scopes being requested.
     * @return SupportsScopes[]
     */
    protected function filterForScopes(array $attribs, array $currentScopes): array
    {
        // If the request is for "unscoped" attributes, filter out
        // any that specify a scope other than unscoped.
        if (empty($currentScopes)) {
            return pipe($attribs,
                afilter($this->hasNoScope(...)),
            );
        }

        // If both a scoped and unscoped version of an attribute is present,
        // and the request is for that scope, we want the scoped version, NOT
        // the unscoped version, regardless of lexical order.  That requires
        // this extra song-and-dance to see if we should fall back to the unscoped
        // version or not.

        // Attributes that are in the current scope OR unscoped.
        $attribs = \array_filter($attribs, $this->inScopeOrUnscoped($currentScopes));

        // If there are any attributes with a scope, filter out the
        // ones that have no scope.
        $attribsWithNonDefaultScope = \array_filter($attribs, $this->hasAnyScope(...));
        if (count($attribsWithNonDefaultScope)) {
            $attribs = \array_filter($attribs, $this->matchesScopes($currentScopes));
        }

        return $attribs;
    }

    /**
     * Determines if an attribute has no scope specified at all.
     *
     * @param SupportsScopes $attr
     * @return bool
     */
    protected function hasNoScope(SupportsScopes $attr): bool
    {
        return $attr->scopes() === [null];
    }

    /**
     * Determines if an attribute has any scope specified.
     *
     * @param SupportsScopes $attr
     * @return bool
     */
    protected function hasAnyScope(SupportsScopes $attr): bool
    {
        return !$this->hasNoScope($attr);
    }

    /**
     * Builds a pipe-friendly closure that determines if an attribute is in a scope.
     *
     * @param array<string|null> $scopes
     *   The name of the scope, or null if checking for the unscoped case.
     * @return \Closure
     */
    protected function matchesScopes(array $scopes): \Closure
    {
        return static fn (SupportsScopes $attr): bool
            => (bool)array_intersect($scopes, $attr->scopes());
    }

    /**
     * Builds a pipe-friendly closure that determines if an attribute is in a scope, or supports unscoped cases.
     *
     * This is a performance optimization of matchesScopes($scopes) || hasNoScope($attr).
     *
     * @param array<string|null> $scopes
     */
    protected function inScopeOrUnscoped(array $scopes): \Closure
    {
        return static function (SupportsScopes $attr) use ($scopes): bool {
            $attrScopes = $attr->scopes();
            return $attrScopes === [null]
                || array_intersect($attrScopes, $scopes);
        };
    }

    /**
     * Retrieves a single attribute from a class element, including opt-in inheritance and transitiveness.
     *
     * @see getInheritedAttributes()
     */
    public function getInheritedAttribute(\Reflector $target, string $name): ?object
    {
        return $this->getInheritedAttributes($target, $name)[0] ?? null;
    }

    /**
     * Retrieves multiple attributes from a class element, including opt-in inheritance and transitiveness.
     *
     * If the attribute in question implements Inheritable, then parent classes
     * will also be checked for the attribute.
     *
     * If the element is a property that is typed for a class and implements
     * TransitiveProperty, then the class pointed at by the property will also be
     * checked. If it implements both interfaces, then parents of the class
     * pointed to by the property will be checked as well.
     *
     * @param \Reflector $target
     *   The property from which to get an attribute.
     * @param string $name
     * @return array<object>
     */
    public function getInheritedAttributes(\Reflector $target, string $name): array
    {
        $attributes = pipe($this->attributeInheritanceTree($target, $name),
            firstValue(fn ($r): array => $this->getAttributes($r, $name))
        );

        if ($attributes) {
            return $attributes;
        }

        // Transitivity is only supported on properties at this time.
        // It's not clear that it makes any sense on methods or constants.
        if ($target instanceof \ReflectionProperty
            && is_a($name, TransitiveProperty::class, true)
            && $class = $this->getPropertyClass($target))
        {
            return pipe($this->classAncestors($class),
                firstValue(fn (string $c): array => $this->getAttributes(new \ReflectionClass($c), $name)),
            ) ?? [];
        }

        return [];
    }

    /**
     * A generator to produce reflections of all the ancestors of a reflectable.
     *
     * The property itself will be included first, and parents will only be
     * scanned if the attribute implements the Inheritable interface.
     *
     * @return iterable<\Reflector>
     * @see Inheritable
     */
    protected function attributeInheritanceTree(\Reflector $subject, string $attributeType): iterable
    {
        // Check the subject itself, first.
        yield $subject;

        if (is_a($attributeType, Inheritable::class, true)) {
            // PHPStan doesn't understand that this is guaranteed type safe, so
            // ignore it.
            yield from match(get_class($subject)) {
                // @phpstan-ignore-next-line
                \ReflectionClass::class => $this->classInheritanceTree($subject),
                // @phpstan-ignore-next-line
                \ReflectionObject::class => $this->classInheritanceTree($subject),
                // @phpstan-ignore-next-line
                \ReflectionProperty::class => $this->classElementInheritanceTree($subject),
                // @phpstan-ignore-next-line
                \ReflectionMethod::class => $this->classElementInheritanceTree($subject),
                // @phpstan-ignore-next-line
                \ReflectionClassConstant::class => $this->classElementInheritanceTree($subject),
                // @phpstan-ignore-next-line
                \ReflectionParameter::class => $this->parameterInheritanceTree($subject),
                // If it's an enum, there's nothing to inherit so just stub that out.
                \ReflectionEnum::class => [],
            };
        }
    }

    /**
     * Returns all the ReflectionClasses in a subject's inheritance tree.
     *
     * This includes both classes and interfaces.
     *
     * @param \ReflectionClass $subject
     *   The reflection of the class for which we want the ancestors.
     * @return iterable<\ReflectionClass>
     * @throws \ReflectionException
     */
    protected function classInheritanceTree(\ReflectionClass $subject): iterable
    {
        $ancestors = $this->classAncestors($subject->getName(), false);
        foreach ($ancestors as $ancestor) {
            yield new \ReflectionClass($ancestor);
        }
    }

    /**
     * Returns all of the ReflectionParameters in a subject's inheritance tree.
     *
     * That is, it returns the reflection of the parent class's copy of a
     * parameter on the same method, if defined.
     *
     * @param \ReflectionParameter $subject
     *   The reflection of the Parameter for which we want the ancestors.
     * @return \ReflectionParameter[]
     * @throws \ReflectionException
     */
    protected function parameterInheritanceTree(\ReflectionParameter $subject): iterable
    {
        $parameterName = $subject->getName();
        $methodName = $subject->getDeclaringFunction()->name;

        foreach ($this->classAncestors($subject->getDeclaringClass()->name) as $class) {
            $rClass = new \ReflectionClass($class);
            if ($rClass->hasMethod($methodName)) {
                $rMethod = $rClass->getMethod($parameterName);
                foreach ($rMethod->getParameters() as $rParam) {
                    if ($rParam->name === $parameterName) {
                        yield $rParam;
                        break;
                    }
                }
            }
        }
    }

    /**
     * Returns all of the reflections in a subject's inheritance tree.
     *
     * This method works for the "basic" class elements: Properties, methods, and constants.
     *
     * For other types, see their respective methods.
     *
     * @param \ReflectionProperty|\ReflectionMethod|\ReflectionClassConstant $subject
     *   The reflection of the component for which we want the ancestors.
     * @return iterable<\Reflector>
     * @throws \ReflectionException
     */
    protected function classElementInheritanceTree(\ReflectionProperty|\ReflectionMethod|\ReflectionClassConstant $subject): iterable
    {
        $subjectName = $subject->getName();

        [$hasMethod, $getMethod] = match(get_class($subject)) {
            \ReflectionProperty::class => ['hasProperty', 'getProperty'],
            \ReflectionMethod::class => ['hasMethod', 'getMethod'],
            \ReflectionClassConstant::class => ['hasConstant', 'getReflectionConstant'],
        };

        foreach ($this->classAncestors($subject->getDeclaringClass()->name) as $class) {
            $rClass = new \ReflectionClass($class);
            if ($rClass->$hasMethod($subjectName)) {
                yield $rClass->$getMethod($subjectName);
            }
        }
    }

    /**
     * Returns a list of all class and interface parents of a class.
     *
     * @return array<class-string>
     */
    public function classAncestors(string $class, bool $includeClass = true): array
    {
        // These methods both return associative arrays, making + safe.
        $ancestors = class_parents($class) + class_implements($class);
        return $includeClass
            ? [$class => $class] + $ancestors
            : $ancestors
        ;
    }

    /**
     * Returns the class or interface a given property is typed for, or null if it's not so typed.
     *
     * @param \ReflectionProperty $rProperty
     *   The property to check
     * @return string|null
     *   The class/interface name, or null.
     */
    protected function getPropertyClass(\ReflectionProperty $rProperty): ?string
    {
        $rType = $rProperty->getType();
        if ($rType instanceof \ReflectionNamedType && (class_exists($rType->getName()) || interface_exists($rType->getName()))) {
            return $rType->getName();
        }
        return null;
    }
}
