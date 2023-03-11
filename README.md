# Attribute Utilities

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

AttributeUtils provides utilities to simplify working with and reading Attributes in PHP 8.1 and later.

Its primary tool is the Class Analyzer, which allows you to analyze a given class or enum with respect to some attribute class.  Attribute classes may implement various interfaces in order to opt-in to additional behavior, as described below.  The overall intent is to provide a simple but powerful framework for reading metadata off of a class, including with reflection data.

## Install

Via Composer

``` bash
$ composer require crell/attributeutils
```

## Usage

### Basic usage

The most important class in the system is `Analyzer`, which implements the `ClassAnalyzer` interface.

```php

#[MyAttribute(a: 1, b: 2)]
class Point
{
    public int $x;
    public int $y;
    public int $z;
}

$analyzer = new Crell\AttributeUtils\Analyzer();

$attrib = $analyzer->analyze(Point::class, MyAttribute::class);

// $attrib is now an instance of MyAttribute.
print $attrib->a . PHP_EOL; // Prints 1
print $attrib->b . PHP_EOL; // Prints 2
```

All interaction with the reflection system is abstracted away by the `Analyzer`.

You may analyze any class with respect to any attribute.  If the attribute is not found, a new instance of the attribute class will be created with no arguments, that is, using whatever it's default argument values are.  If any arguments are required, a `RequiredAttributeArgumentsMissing` exception will be thrown.

The net result is that you can analyze a class with respect to any attribute class you like, as long as it has no required arguments.

The most important part of `Analyzer`, though, is that it lets attributes opt-in to additional behavior to become a complete class analysis and reflection framework.

### Reflection

If a class attribute implements [`Crell\AttributeUtils\FromReflectionClass`](src/FromReflectionClass.php), then once the attribute has been instantiated the `ReflectionClass` representation of the class being analyzed will be passed to the `fromReflection()` method.  The attribute may then save whatever reflection information it needs, however it needs.  For example, if you want the attribute object to know the name of the class it came from, you can save `$reflection->getName()` and/or `$reflection->getShortName()` to non-constructor properties on the object.  Or, you can save them if and only if certain constructor arguments were not provided.

If you are saving a reflection value literally, it is *strongly recommended* that you use a property name consistent with those in the [`ReflectClass`](src/Attributes/Reflect/ReflectClass.php) attribute.  That way, the names are consistent across all attributes, even different libraries, and the resulting code is easier for other developers to read and understand.  (We'll cover `ReflectClass` more later.)

In the following example, an attribute accepts a `$name` argument.  If one is not provided, the class's short-name will be used instead.

```php
#[\Attribute]
class AttribWithName implements FromReflectionClass 
{
    public readonly string $name;
    
    public function __construct(?string $name = null) 
    {
        if ($name) {
            $this->name = $name;
        }
    }
    
    public function fromReflection(\ReflectionClass $subject): void
    {
        $this->name ??= $subject->getShortName();
    }
}
```

The reflection object itself should *never ever* be saved to the attribute object.  Reflection objects cannot be cached, so saving it would render the attribute object uncacheable.  It's also wasteful, as any data you need can be retrieved from the reflection object and saved individually.

There are similarly [`FromReflectionProperty`](src/FromReflectionProperty.php), [`FromReflectionMethod`](src/FromReflectionMethod.php), [`FromReflectionClassConstant`](src/FromReflectionClassConstant.php), and [`FromReflectionParameter`](src/FromReflectionParameter.php) interfaces that do the same for their respective bits of a class.

### Additional class components

The class attribute may also opt-in to analyzing various portions of the class, such as its properties, methods, and constants.  It does so by implementing the [`ParseProperties`](src/ParseProperties.php), [`ParseStaticProperties`](src/ParseStaticProperties.php), [`ParseMethods`](src/ParseMethods.php), [`ParseStaticMethods`](src/ParseStaticMethods.php), or [`ParseClassConstants`](src/ParseClassConstants.php) interfaces, respectively.  They all work the same way, so we'll look at properties in particular.

An example is the easiest way to explain it:

```php
#[\Attribute(\Attribute::TARGET_CLASS)]
class MyClass implements ParseProperties
{
    public readonly array $properties;

    public function propertyAttribute(): string
    {
        return MyProperty::class;
    }

    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }

    public function includePropertiesByDefault(): bool
    {
        return true;
    }
}

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class MyProperty
{
    public function __construct(
        public readonly string $column = '',
    ) {}
}

#[MyClass]
class Something
{
    #[MyProperty(column: 'beep')]
    protected property $foo;
    
    private property $bar;
}

$attrib = $analyzer->analyze(Something::class, MyClass::class);
```

In this example, the `MyClass` attribute will first be instantiated. It has no arguments, which is fine.  However, the interface methods specify that the Analyzer should then parse `Something`'s properties with respect to `MyProperty`.  If a property has no such attribute, it should be included anyway and instantiated with no arguments.

The Analyzer will dutifully create an array of two `MyProperty` instances, one for `$foo` and one for `$bar`; the former having the `column` value `beep`, and the latter having the default empty string value.  That array will then be passed to `MyClass::setProperties()` for `MyClass` to save, or parse, or filter, or do whatever it wants.

If `includePropertiesByDefault()` returned `false`, then the array would have only one value, from `$foo`.  `$bar` would be ignored.

Note: The array that is passed to `setProperties` is indexed by the name of the property already, so you do not need to do so yourself.

The property-targeting attribute (`MyProperty`) may also implement `FromReflectionProperty` to get the corresponding `ReflectionProperty` passed to it, just as the class attribute can.

The Analyzer includes only object level properties in `ParseProperties`.  If you want static properties, use the `ParseStaticProperties` interface, which works the exact same way.  Both interfaces may be implemented at the same time.

The `ParseClassConstant` interface works the same way as `ParseProperties`.

### Methods

`ParseMethods` works the same way as `ParseProperties` (and also has a corresponding `ParseStaticMethods` interface for static methods).  However, a method-targeting attribute may also itself implement [`ParseParameters`](src/ParseParameters.php) in order to examine parameters on that method.  `ParseParameters` repeats the same pattern as `ParseProperties` above, with the methods suitably renamed.

### Class-referring components

A component-targeting attribute may also implement [`ReadsClass`](src/ReadsClass.php).  If so, then the class's attribute will be passed to the `fromClassAttribute()` method after all other setup has been done.  That allows the attribute to inherit default values from the class, or otherwise vary its behavior based on properties set on the class attribute.

### Excluding values

When parsing components of a class, whether they are included depends on a number of factors.  The `includePropertiesByDefault()`, `includeMethodsByDefault()`, etc. methods on the various `Parse*` interfaces determine whether components that lack an attribute should be included with a default value, or excluded entirely.

If the `include*()` method returns true, it is still possible to exclude a specific component if desired.  The attribute for that component may implement the [`Excludable`](src/Excludable.php) interface, with has a single method, `exclude()`.

What then happens is the Analyzer will load all attributes of that type, then filter out the ones that return `true` from that method.  That allows individual properties, methods, etc. to opt-out of being parsed.  You may use whatever logic you wish for `exclude()`, although the most common approach will be something like this:

```php
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class MyProperty implements Excludable
{
    public function __construct(
        public readonly bool $exclude = false,
    ) {}
    
    public function exclude(): bool
    {
        return $this->exclude;
    }
}

class Something
{
    #[MyProperty(exclude: true)]
    private int $val;
}
```

If you are taking this manual approach, it is strongly recommended that you use the naming convention here for consistency.

### Attribute inheritance

By default, attributes in PHP are not inheritable.  That is, if class `A` has an attribute on it, and `B` extends `A`, then asking reflection what attributes `B` has will find none.  Sometimes that's OK, but other times it is highly annoying to have to repeat values.

`Analyzer` addresses that limitation by letting attributes opt-in to being inherited.  Any attribute &mdash; for a class, property, method, constant, or parameter &mdash; may also implement the [`Inheritable`](src/Inheritable.php) marker interface.  This interface has no methods, but signals to the system that it should itself check parent classes and interfaces for an attribute if it is not found.

For example:

```php
#[\Attribute(\Attribute::TARGET_CLASS)]
class MyClass implements Inheritable
{
    public function __construct(public string $name = '') {}
}

#[MyClass(name: 'Jorge')]
class A {}

class B extends A {}

$attrib = $analyzer->analyze(B::class, MyClass::class);

print $attrib->name . PHP_EOL; // prints Jorge
```

Because `MyClass` is inheritable, the Analyzer notes that it is absent on `B` so checks class `A` instead.  All attribute components may be inheritable if desired just by implementing the interface.

When checking for inherited attributes, ancestor classes are all checked first, then implemented interfaces, in the order returned by `class_implements()`.  Properties will not check for interfaces, of course, as interfaces cannot have properties.

### Attribute child classes

When checking for an attribute, the Analyzer uses an `instanceof` check in Reflection.  That means a child class, or even a class implementing an interface, of what you specify will still be found and included.  That is true for all attribute types.

### Sub-attributes

`Analyzer` can only handle a single attribute on each target.  However, it also supports the concept of "sub-attributes."  Sub-attributes work similarly to the way a class can opt-in to parsing properties or methods, but for sibling attributes instead of child components.  That way, any number of attributes on the same component can be folded together into a single attribute object.  Any attribute for any component may opt-in to sub-attributes by implementing [`HasSubAttributes`](src/HasSubAttributes.php).

The following example should make it clearer:

```php
#[\Attribute(\Attribute::TARGET_CLASS)]
class MainAttrib implements HasSubAttributes
{
    public readonly int $age;

    public function __construct(
        public readonly string name = 'none',
    ) {}

    public function subAttributes(): array
    {
        return [Age::class => 'fromAge'];
    }
    
    public function fromAge(?ClassSubAttribute $sub): void
    {
        $this->age = $sub?->age ?? 0;
    }
}

#[\Attribute(\Attribute::TARGET_CLASS)]
class Age
{
    public function __construct(public readonly int $age = 0) {}
}

#[MainAttrib(name: 'Larry')]
#[Age(21)]
class A {}

class B {}

$attribA = $analyzer->analyze(A::class, MainAttrib::class);

print "$attribA->name, $attribA->age\n"; // prints "Larry, 21"

$attribB = $analyzer->analyze(B::class, MainAttrib::class);

print "$attribB->name, $attribB->age\n"; // prints "none, 0"
```

The `subAttributes()` method returns an associative array of attribute class names mapped to methods to call.  After the `MainAttrib` is loaded, the Analyzer will look for any of the listed sub-attributes, and then pass their result to the corresponding method.  The main attribute can then save the whole sub-attribute, or pull pieces out of it to save, or whatever else it wants to do.

An attribute may have any number of sub-attributes it wishes.

Note that if the sub-attribute is missing, `null` will be passed to the method.  That is to allow a sub-attribute to have required parameters if and only if it is specified, while keeping the sub-attribute itself optional.  You therefore *must* make the callback method's argument nullable.

Sub-attributes may also be `Inheritable`.

### Multi-value sub-attributes

By default, PHP attributes can only be placed on a given target once.  However, they may be marked as "repeatable," in which case multiple of the same attribute may be placed on the same target.  (Class, property, method, etc.)

The Analyzer does not support multi-value attributes, but it does support multi-value sub-attributes.  If the sub-attribute implements the [`Multivalue`](src/Multivalue.php) marker interface, then an array of sub-attributes will be passed to the callback instead.

For example:

```php
#[\Attribute(\Attribute::TARGET_CLASS)]
class MainAttrib implements HasSubAttributes
{
    public readonly array $knows;

    public function __construct(
        public readonly string name = 'none',
    ) {}

    public function subAttributes(): array
    {
        return [Knows::class => 'fromKnows'];
    }
    
    public function fromKnows(array $knows): void
    {
        $this->knows = $knows;
    }
}

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class Knows
{
    public function __construct(public readonly string $name) {}
}

#[MainAttrib(name: 'Larry')]
#[Knows('Kai')]
#[Knows('Molly')]
class A {}

class B {}
```

In this case, any number of `Knows` attributes may be included, including zero, but if included the `$name` argument is required.  The `fromKnows()` method will be called with a (possibly empty, in the case of `B`) array of `Knows` objects, and can do what it likes with it.  In this example the objects are saved in their entirety, but they could also be mushed into a single array or used to set some other value if desired.

Note that if a multi-value sub-attribute is `Inheritable`, ancestor classes will only be checked if there are no local sub-attributes.  If there is at least one, it will take precedence and the ancestors will be ignored.

Note: In order to make use of multi-value sub-attributes, the attribute class itself must be marked as "repeatable" as in the example above or PHP will generate an error.  However, that is not sufficient for the Analyzer to parse it as multi-value.  That's because attributes may also be multi-value when implementing scopes, but still only single-value from the Analzyer's point of view.  See the section on Scopes below.

### Caching

The main `Analyzer` class does no caching whatsoever.  However, it implements a `ClassAnalyzer` interface which allows it to be easily wrapped in other implementations that provide a caching layer.

For example, the [`MemoryCacheAnalyzer`](src/MemoryCacheAnalyzer.php) class provides a simple wrapper that caches results in a static variable in memory.  You should almost always use this wrapper for performance.

```php
$analyzer = new MemoryCacheAnalyzer(new Analyzer());
```

A PSR-6 cache bridge is also included, allowing the Analyzer to be used with any PSR-6 compatible cache pool.

```php
$anaylzer = new Psr6CacheAnalyzer(new Analyzer(), $somePsr6CachePoolObject);
```

Wrappers may also compose each other, so the following would be an entirely valid and probably good approach:

```php
$analyzer = new MemoryCacheAnalyzer(new Psr6CacheAnalyzer(new Analyzer(), $psr6CachePool));
```

## Finalizing an attribute

Attributes that opt-in to several functional interfaces may not always have an easy time of knowing when to do default handling.  It may not be obvious when the attribute setup is "done."  Attribute classes may therefore opt in to the [`Finalizable`](src/Finalizable.php) interface.  If specified, it is guaranteed to be the last method called on the attribute.  The attribute may then do whatever final preparation is appropriate to consider the object "ready."

## Advanced features

There are a couple of other advanced features also available.  These are less frequently used, but in the right circumstances they can be very helpful.

### Scopes

Attributes may opt-in to supporting "scopes".  "Scopes" allow you to specify alternate versions of the same attribute to use in different contexts.  Examples include different serialization groups or different languages.  Often, scopes will be hidden behind some other name in another library (like language), which is fine.

If an attribute implements [`SupportsScopes`](src/SupportsScopes.php), then when looking for attributes additional filtering will be performed.  The exact logic also interacts with exclusion and whether a class attribute specifies a component should be loaded by default if missing, leading to a highly robust set of potential rules for what attribute to use when.

As an example, let's consider providing alternate language versions of a property attribute.  The logic is identical for any component, as well as for sub-attributes.

```php
#[\Attribute(\Attribute::TARGET_CLASS)]
class Labeled implements ParseProperties
{
    public readonly array $properties;

    public function setProperties(array $properties): void
    {
        $this->properties ??= $properties;
    }

    public function includePropertiesByDefault(): bool
    {
        return true;
    }

    public function propertyAttribute(): string
    {
        return Label::class;
    }
}

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Label implements SupportsScopes, Excludable
{
    public function __construct(
        public readonly string $name = 'Untitled',
        public readonly ?string $language = null,
        public readonly bool $exclude = false,
    ) {}

    public function scopes(): array
    {
        return [$this->language];
    }

    public function exclude(): bool
    {
        return $this->exclude;
    }
}

#[Labeled]
class App
{
    #[Label(name: 'Installation')]
    #[Label(name: 'Instalación', language: 'es')]
    public string $install;

    #[Label(name: 'Setup')]
    #[Label(name: 'Configurar', language: 'es')]
    #[Label(name: 'Einrichten', language: 'de')]
    public string $setup;

    #[Label(name: 'Einloggen', language: 'de')]
    #[Label(language: 'fr', exclude: true)]
    public string $login;

    public string $customization;
}
```

The `Labeled` attribute on the class is nothing we haven't seen before.  The `Label` attribute for properties is both excludable and supports scopes, although it exposes it with the name `language`.

Calling the Analyzer as we've seen before will ignore the scoped versions, and result in an array of `Label`s with names "Installation", "Setup", "Untitled", and "Untitled".  However, it may also be invoked with a specific scope:

```php
$labels = $analyzer->analyze(App::class, Labeled::class, scopes: ['es']);
```

Now, `$labels` will contain an array of `Label`s with names "Instalación", "Configurar", "Untitled", and "Untitled".  On `$stepThree`, there is no `es` scoped version so it falls back to the default.  Similarly, a scope of `de` will result in "Installation", "Einrichten", "Einloggen", and "Untitled" (as "Installation" is spelled the same in both English and German).

A scope of `fr` will result in the default (English) for each case, except for `$stepThree` which will be omitted entirely.  The `exclude` directive is applicable only in that scope.  The result will therefore be "Installation", "Setup", "Untitled".

(If you were doing this for real, it would make sense to derive a default `name` off of the property name itself via `FromReflectionProperty` rather than a hard-coded "Untitled.")

By contrast, if `Labeled::includePropertiesByDefault()` returns false, then `$customization` will not be included in any scope.  `$login` will be included in `de` only, and in no other scope at all.  That's because there is no default-scope option specified, and so in any scope other than `de` no default will be created.  A lookup for scope `fr` will be empty.

A useful way to control what properties are included is to make the class-level attribute scope-aware as well, and control `includePropertiesByDefault()` via an argument. That way, for example, `includePropertiesByDefault()` can return true in the unscoped case, but false when a scope is explicitly specified; that way, properties will only be included in a scope if they explicitly opt-in to being in that scope, while in the unscoped case all properties are included.

Note that the `scopes()` method returns an array.  That means an attribute being part of multiple scopes is fully supported.  How you populate the return of that method (whether an array argument or something else) is up to you.

Additionally, scopes are looked up as an ORed array.  That is, the following command:

```php
$labels = $analyzer->analyze(SomeClass::class, AnAttribute::class, scopes: ['One', 'Two']);
```

will retrieve any attributes that return *either* `One` or `Two` from their `scopes()` method.  If multiple attributes on the same component match that rule (say, one returns `['One']` and another returns `['Two']`), the lexically first will be used.

### Transitivity

Transitivity applies only to attributes on properties, and only if the attribute in question can target both properties and classes.  It is an alternate form of inheritance.  Specifically, if a property is typed to a class or interface, and the attribute in question implements `TransitiveProperty`, and the property does not have that attribute on it, then instead of looking up the inheritance tree the analyzer will first look at the class the property is typed for.

That's a lot of conditionals, so here's an example to make it clearer:

```php

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class MyClass implements ParseProperties
{
    public readonly array $properties;

    public function setProperties(array $properties): void
    {
        $this->properties = $properties;
    }
    
    public function includePropertiesByDefault(): bool { return true; }

    public function propertyAttribute(): string { return FancyName::class; }
}


#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_CLASS)]
class FancyName implements Transitive
{
    public function __construct(public readonly string $name = '') {}
}

class Stuff
{
    #[FancyName('A happy little integer')]
    protected int $foo;

    protected string $bar;
    
    protected Person $personOne;
    
    #[FancyName('Her Majesty Queen Elizabeth II')]
    protected Person $personTwo;
}

#[FancyName('I am not an object, I am a free man!')]
class Person
{
}

$attrib = $analyzer->analyze(Stuff::class, MyClass::class);

print $attrib->properties['foo']->name . PHP_EOL; // prints "A happy little integer"
print $attrib->properties['bar']->name . PHP_EOL; // prints ""
print $attrib->properties['personOne']->name . PHP_EOL; // prints "I am not an object, I am a free man!"
print $attrib->properties['personTwo']->name . PHP_EOL; // prints "Her Majesty Queen Elizabeth II"
```

Because `$personTwo` has a `FancyName` attribute, it behaves as normal.  However, `$personOne` does not, so it jumps over to the `Person` class to look for the attribute and finds it there.

If an attribute implements both `Inheritable` and `Transitive`, then first the class being analyzed will be checked, then its ancestor classes, then its implemented interfaces, then the transitive class for which it is typed, and then that class's ancestors until it finds an appropriate attribute.

Both main attributes and sub-attributes may be declared `Transitive`.

### Custom analysis

As a last resort, an attribute may also implement the [`CustomAnalysis`](src/CustomAnalysis.php) interface.  If it does so, the analyzer itself will be passed to the `customAnalysis()` method of the attribute, which may then take whatever actions it wishes.  This feature is intended as a last resort only, and it's possible to create unpleasant infinite loops if you are not careful.  99% of the time you should use some other, any other mechanism.  But it's there if you need it.

### Dependency Injection

The Analyzer is designed to be usable on its own without any setup.  Making it available via a Dependency Injection Container is recommended.  An appropriate cache wrapper should also be included in the DI configuration.

## The Reflect library

One of the many uses for `Analyzer` is to extract reflection information from a class.  Sometimes you only need some of it, but there's no reason you can't grab all of it.  The result is an attribute that can carry all the same information as reflection, but can be cached if desired while reflection objects cannot be.

A complete set of such attributes is provided in the [`Attributes/Reflect`](src/Attributes/Reflect) directory.  They cover all components of a class.  As none of them have any arguments, there is no need to put them on any class.  The default "empty" version of each will get used, which will then self-populate using the `FromReflection*` interfaces.

The net result is that a full reflection summary of any arbitrary class may be obtained by calling:

```php
use Crell\AttributeUtls\Attributes\Reflect\ReflectClass;

$reflect = $analyzer->analyze($someClass, ReflectClass::class);
```

`$reflect` now contains a complete copy of the class, properties, constants, methods, and parameters reflection information, in well-defined, easily cacheable objects.  See each class's docblocks for a complete list of all available information.

To analyze an Enum, use `ReflectEnum::class` instead.

Even if you do not need to use the entire Reflect tree, it's worth studying as an example of how to really leverage the Analyzer.  Additionally, if you are saving any reflection values as-is onto your attribute you are encouraged to use the same naming conventions as those classes, for consistency.

A number of traits are included as well that handle the common case of collecting all of a given class component.  Feel free to use them in your own classes if you wish.

## Advanced tricks

The following are a collection of advanced and fancy uses of the Analyzer, mostly to help demonstrate just how powerful it can be when used appropriately.

### Multi-value attributes

As noted, the Analyzer supports only a single main attribute on each component.  However, sub-attributes may be multi-value, and an omitted attribute can be filled in with a default "empty" attribute.  That leads to the following way to simulate multi-value attributes.  It works on any component, although for simplicity we'll show it on classes.

```php
#[\Attribute(Attribute::TARGET_CLASS)]
class Names implements HasSubAttributes, IteratorAggregate, ArrayAccess
{
    protected readonly array $names;

    public function subAttributes(): array
    {
        return [Alias::class => 'fromAliases'];
    }

    public function fromAliases(array $aliases): void
    {
        $this->names = $aliases;
    }

    public function getIterator(): \ArrayIterator
    {
        return new ArrayIterator($this->names);
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->names);
    }

    public function offsetGet(mixed $offset): Alias
    {
        return $this->names[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new InvalidArgumentException();
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new InvalidArgumentException();
    }
}

#[\Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class Alias implements Multivalue
{
    public function __construct(
        public readonly string $first,
        public readonly string $last,
    ) {}

    public function fullName(): string
    {
        return "$this->first $this->last";
    }
}

#[Alias(first: 'Bruce', last: 'Wayne')]
#[Alias(first: 'Bat', last: 'Man')]
class Something
{
}

$names = $analyzer->analyze(Something::class, Names::class);

foreach ($names as $name) {
    print $name->fullName() . PHP_EOL;
}

// Output:
Bruce Wayne
Bat Man
```

The `IteratorAggregate` and `ArrayAccess` interfaces are optional; I include them here just to show that you can do it if you want.  Here, the `Names` attribute is never put on a class directly.  However, by analyzing a class "with respect to" `Names`, you can collect all the multi-value sub-attributes that it has, giving the impression of a multi-value attribute.

Note that `Alias` needs to implement `Multivalue` so the analyzer knows to expect more than one of them.

## Interface attributes

Normally, attributes do not inherit.  That means an attribute on an interface has no bearing on classes that implement that interface.  However, attributes may opt-in to inheriting via the Analzyer.

A good use for that is sub-attributes, which may also be specified as an interface.  For example, consider this modified version of the example above:

```php

#[\Attribute(\Attribute::TARGET_CLASS)]
class Names implements HasSubAttributes, IteratorAggregate, ArrayAccess
{
    protected readonly array $names;

    public function subAttributes(): array
    {
        return [Name::class => 'fromNames'];
    }

    public function fromNames(array $names): void
    {
        $this->names = $names;
    }

    // The same ArrayAccess and IteratorAggregate code as above.
}

interface Name extends Multivalue
{
    public function fullName(): string;
}

#[\Attribute(\Attribute::TARGET_CLASS)]
class RealName implements Name
{
    public function __construct(
        public readonly string $first,
        public readonly string $last,
    ) {}

    public function fullName(): string
    {
        return "$this->first $this->last";
    }
}

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class Alias implements Name
{
    public function __construct(public readonly string $name) {}

    public function fullName(): string
    {
        return $this->name;
    }
}

#[RealName(first: 'Bruce', last: 'Wayne')]
#[Alias('Batman')]
#[Alias('The Dark Knight')]
#[Alias('The Caped Crusader')]
class Hero
{
}
```

You can now mix and match `RealName` and `Alias` on the same class.  Only one `RealName` is allowed, but any number of `Alias` attributes are allowed.  All are `Name` according to the `Names` main attribute, and so all will get picked up and made available.

Note that the interface must be marked `Multivalue` so that `Analyzer` will allow more than one attribute of that type.  However, the `RealName` attribute is not marked as repeatable, so PHP will prevent more than one `RealName` being used at once while `Alias` may be used any number of times.

### One of many options

In a similar vein, it's possible to use sub-attributes to declare that a component may be marked with one of a few attributes, but only one of them.

```php
interface DisplayType
{
}

#[\Attribute(\Attribute::TARGET_CLASS)]
class Screen implements DisplayType
{
    public function __construct(public readonly string $color) {}
}

#[\Attribute(\Attribute::TARGET_CLASS)]
class Audio implements DisplayType
{
    public function __construct(public readonly int $volume) {}
}

#[\Attribute(Attribute::TARGET_CLASS)]
class DisplayInfo implements HasSubAttributes
{
    public readonly ?DisplayType $type;

    public function subAttributes(): array
    {
        return [DisplayType::class => 'fromDisplayType'];
    }

    public function fromDisplayType(?DisplayType $type): void
    {
        $this->type = $type;
    }
}

#[Screen('#00AA00')]
class A {}

#[Audio(10)]
class B {}

class C {}

$displayInfoA = $analyzer->analzyer(A::class, DisplayInfo::class);
$displayInfoB = $analyzer->analzyer(B::class, DisplayInfo::class);
$displayInfoC = $analyzer->analzyer(C::class, DisplayInfo::class);
```

In this case, a class may be marked with either `Screen` or `Audio`, but not both.  If both are specified, only the first one listed will be used; the others will be ignored.

In this example, `$displayInfoA->type` will be an instance of `Screen`, `$displayInfoB->type` will be an instance of `Audio`, and `$displayInfoC->type` will be `null`.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email larry at garfieldtech dot com instead of using the issue tracker.

## Credits

- [Larry Garfield][link-author]
- [All Contributors][link-contributors]

Development of this library is sponsored by [TYPO3 GmbH](https://typo3.com/).

## License

The Lesser GPL version 3 or later. Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/Crell/AttributeUtils.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/License-LGPLv3-green.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/Crell/AttributeUtils.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/Crell/AttributeUtils
[link-downloads]: https://packagist.org/packages/Crell/AttributeUtils
[link-author]: https://github.com/Crell
[link-contributors]: ../../contributors
