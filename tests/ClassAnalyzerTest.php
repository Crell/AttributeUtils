<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

use Crell\AttributeUtils\Attributes\AppliesEverywhere;
use Crell\AttributeUtils\Attributes\BasicClass;
use Crell\AttributeUtils\Attributes\BasicProperty;
use Crell\AttributeUtils\Attributes\ClassMethodsProperties;
use Crell\AttributeUtils\Attributes\ClassWithClassConstants;
use Crell\AttributeUtils\Attributes\ClassWithOwnSubAttributes;
use Crell\AttributeUtils\Attributes\ClassWithProperties;
use Crell\AttributeUtils\Attributes\ClassWithPropertiesWithSubAttributes;
use Crell\AttributeUtils\Attributes\ClassWithReflection;
use Crell\AttributeUtils\Attributes\ClassWithSubSubAttributes;
use Crell\AttributeUtils\Attributes\GenericClass;
use Crell\AttributeUtils\Attributes\Labeled;
use Crell\AttributeUtils\Attributes\ScopedClass;
use Crell\AttributeUtils\Attributes\InheritableClassAttributeMain;
use Crell\AttributeUtils\Attributes\ScopedClassNoDefaultInclude;
use Crell\AttributeUtils\Records\AttributesInheritChild;
use Crell\AttributeUtils\Records\ClassWithConstantsChild;
use Crell\AttributeUtils\Records\ClassWithCustomizedFields;
use Crell\AttributeUtils\Records\ClassWithCustomizedPropertiesExcludeByDefault;
use Crell\AttributeUtils\Records\ClassWithDefaultFields;
use Crell\AttributeUtils\Records\ClassWithExcludedProperties;
use Crell\AttributeUtils\Records\ClassWithExtraAnalysisSource;
use Crell\AttributeUtils\Records\ClassWithScopes;
use Crell\AttributeUtils\Records\ClassWithInterface;
use Crell\AttributeUtils\Records\ClassWithMethodsAndProperties;
use Crell\AttributeUtils\Records\ClassWithPropertiesWithReflection;
use Crell\AttributeUtils\Records\ClassWithRecursiveSubAttributes;
use Crell\AttributeUtils\Records\ClassWithScopesNotDefault;
use Crell\AttributeUtils\Records\ClassWithSubAttributes;
use Crell\AttributeUtils\Records\LabeledApp;
use Crell\AttributeUtils\Records\MissingPropertyAttributeArguments;
use Crell\AttributeUtils\Records\MultiuseClass;
use Crell\AttributeUtils\Records\NoProps;
use Crell\AttributeUtils\Records\NoPropsOverride;
use Crell\AttributeUtils\Records\Point;
use Crell\AttributeUtils\Records\PropertiesWithMultipleSubattributes;
use Crell\AttributeUtils\Records\TransitiveFieldClass;
use Crell\AttributeUtils\TypeDef\Suit;
use PHPUnit\Framework\TestCase;

class ClassAnalyzerTest extends TestCase
{

    /**
     * @test
     * @dataProvider attributeTestProvider()
     */
    public function analyze_classes(string $subject, string $attribute, callable $test): void
    {
        $analyzer = new Analyzer();

        $classDef = $analyzer->analyze($subject, $attribute);

        $test($classDef);
    }

    /**
     * @test
     */
    public function missing_required_fields(): void
    {
        $this->expectException(RequiredAttributeArgumentsMissing::class);
        $analyzer = new Analyzer();

        $classDef = $analyzer->analyze(MissingPropertyAttributeArguments::class, GenericClass::class);

    }

    /**
     * @test
     * @dataProvider attributeObjectTestProvider()
     */
    public function analyze_objects(object $subject, string $attribute, callable $test): void
    {
        $analyzer = new Analyzer();

        $classDef = $analyzer->analyze($subject, $attribute);

        $test($classDef);
    }

    /**
     * @test
     */
    public function analyze_anonymous_objects(): void
    {
        $subject = new class {
            #[BasicProperty]
            public string $foo;
        };

        $analyzer = new Analyzer();

        $classDef = $analyzer->analyze($subject, ClassWithProperties::class);

        static::assertEquals('a', $classDef->properties['foo']->a);
        static::assertEquals('b', $classDef->properties['foo']->b);
    }

    /**
     * @see analyze_classes()
     */
    public function attributeTestProvider(): \Generator
    {
        yield 'Generic' => [
            'subject' => Point::class,
            'attribute' => BasicClass::class,
            'test' => static function(BasicClass $classDef) {
                static::assertEquals(0, $classDef->a);
                static::assertEquals(0, $classDef->b);
            },
        ];

        yield 'Reflectable with no override value' => [
            'subject' => NoProps::class,
            'attribute' => ClassWithReflection::class,
            'test' => static function(ClassWithReflection $classDef) {
                static::assertEquals(1, $classDef->a);
                static::assertEquals(0, $classDef->b);
                static::assertEquals('NoProps', $classDef->name);
            },
        ];

        yield 'Reflectable with an override value' => [
            'subject' => NoPropsOverride::class,
            'attribute' => ClassWithReflection::class,
            'test' => static function(ClassWithReflection $classDef) {
                static::assertEquals(1, $classDef->a);
                static::assertEquals(0, $classDef->b);
                static::assertEquals('Overridden', $classDef->name);
            },
        ];

        yield 'Fieldable with default properties' => [
            'subject' => ClassWithDefaultFields::class,
            'attribute' => ClassWithProperties::class,
            'test' => static function(ClassWithProperties $classDef) {
                static::assertEquals('a', $classDef->properties['i']->a);
                static::assertEquals('b', $classDef->properties['i']->b);
                static::assertEquals('a', $classDef->properties['s']->a);
                static::assertEquals('b', $classDef->properties['s']->b);
                static::assertEquals('a', $classDef->properties['f']->a);
                static::assertEquals('b', $classDef->properties['f']->b);
            },
        ];

        yield 'Fieldable with customized properties' => [
            'subject' => ClassWithCustomizedFields::class,
            'attribute' => ClassWithProperties::class,
            'test' => static function(ClassWithProperties $classDef) {
                static::assertEquals('a', $classDef->properties['i']->a);
                static::assertEquals('b', $classDef->properties['i']->b);
                static::assertEquals('A', $classDef->properties['s']->a);
                static::assertEquals('b', $classDef->properties['s']->b);
                static::assertEquals('a', $classDef->properties['f']->a);
                static::assertEquals('B', $classDef->properties['f']->b);
            },
        ];

        yield 'Fieldable default no-include fields' => [
            'subject' => ClassWithCustomizedPropertiesExcludeByDefault::class,
            'attribute' => ClassWithProperties::class,
            'test' => static function(ClassWithProperties $classDef) {
                static::assertArrayNotHasKey('i', $classDef->properties);
                static::assertEquals('A', $classDef->properties['s']->a);
                static::assertEquals('b', $classDef->properties['s']->b);
                static::assertEquals('a', $classDef->properties['f']->a);
                static::assertEquals('B', $classDef->properties['f']->b);
            },
        ];

        yield 'Fieldable reflectable fields' => [
            'subject' => ClassWithPropertiesWithReflection::class,
            'attribute' => GenericClass::class,
            'test' => static function(GenericClass $classDef) {
                static::assertEquals('i', $classDef->properties['i']->name);
                static::assertEquals('beep', $classDef->properties['s']->name);
                static::assertEquals('f', $classDef->properties['f']->name);
            },
        ];

        yield 'Fieldable with sub-attributes' => [
            'subject' => ClassWithSubAttributes::class,
            'attribute' => ClassWithPropertiesWithSubAttributes::class,
            'test' => static function(ClassWithPropertiesWithSubAttributes $classDef) {
                static::assertEquals('C', $classDef->c);
                static::assertEquals('A', $classDef->properties['hasSub']->a);
                static::assertEquals('B', $classDef->properties['hasSub']->b);
                static::assertEquals('A', $classDef->properties['noSub']->a);
                static::assertEquals('B', $classDef->properties['noSub']->b);
            },
        ];

        yield 'Attributes and sub-attributes inherit' => [
            'subject' => AttributesInheritChild::class,
            'attribute' => InheritableClassAttributeMain::class,
            'test' => static function(InheritableClassAttributeMain $classDef) {
                static::assertEquals(2, $classDef->a);
                static::assertEquals('baz', $classDef->sub->foo);
                static::assertEquals(4, $classDef->properties['test']->a);
                static::assertEquals(1, $classDef->properties['added']->a);
            },
        ];

        yield 'Transitive fields inherit from the target class' => [
            'subject' => TransitiveFieldClass::class,
            'attribute' => GenericClass::class,
            'test' => static function(GenericClass $classDef) {
                static::assertEquals('Task', $classDef->properties['task']->beep);
                static::assertEquals('SmallTask', $classDef->properties['small']->beep);
                static::assertEquals('Task', $classDef->properties['big']->beep);
                static::assertEquals('biggie', $classDef->properties['big']->sub->title);
                static::assertEquals('smallie', $classDef->properties['small']->sub->title);
                static::assertNull($classDef->properties['task']->sub);
            },
        ];

        yield 'Property with multiple matching subattributes' => [
            'subject' => PropertiesWithMultipleSubattributes::class,
            'attribute' => GenericClass::class,
            'test' => static function(GenericClass $classDef) {
                static::assertEquals('Main', $classDef->properties['name']->name);
                static::assertEquals('first', $classDef->properties['name']->subs[0]->name);
                static::assertEquals('second', $classDef->properties['name']->subs[1]->name);
            },
        ];

        yield 'Class with methods and properties' => [
            'subject' => ClassWithMethodsAndProperties::class,
            'attribute' => ClassMethodsProperties::class,
            'test' => static function(ClassMethodsProperties $classDef) {
                static::assertCount(3, $classDef->properties);

                static::assertEquals('z', $classDef->methods['methodOne']->a);
                static::assertEquals('y', $classDef->methods['methodOne']->b);
                static::assertEquals('beep', $classDef->methods['methodOne']->name);
                static::assertEquals('a', $classDef->methods['methodTwo']->a);
                static::assertEquals('b', $classDef->methods['methodTwo']->b);
                static::assertEquals('methodTwo', $classDef->methods['methodTwo']->name);
                static::assertEquals('__construct', $classDef->methods['__construct']->name);

                static::assertCount(2, $classDef->methods['methodOne']->parameters);
                static::assertEquals(1, $classDef->methods['methodOne']->parameters['one']->x);
                static::assertEquals('one', $classDef->methods['methodOne']->parameters['one']->name);
                static::assertEquals(3, $classDef->methods['methodOne']->parameters['two']->x);
                static::assertEquals('beep', $classDef->methods['methodOne']->parameters['two']->name);

                static::assertCount(2, $classDef->methods['methodTwo']->parameters);
                static::assertEquals(5, $classDef->methods['methodTwo']->parameters['three']->x);
                static::assertEquals('three', $classDef->methods['methodTwo']->parameters['three']->name);
                static::assertEquals(1, $classDef->methods['methodTwo']->parameters['four']->x);
                static::assertEquals('four', $classDef->methods['methodTwo']->parameters['four']->name);
            },
        ];

        yield 'Class with constants' => [
            'subject' => ClassWithConstantsChild::class,
            'attribute' => ClassWithClassConstants::class,
            'test' => static function(ClassWithClassConstants $classDef) {
                static::assertCount(3, $classDef->constants);

                static::assertEquals(1, $classDef->constants['CHILD_ONLY']->a);
                static::assertEquals(1, $classDef->constants['PARENT_ONLY']->a);
                static::assertEquals(5, $classDef->constants['INHERITED']->a);
            },
        ];

        yield 'Class with excluded properties' => [
            'subject' => ClassWithExcludedProperties::class,
            'attribute' => GenericClass::class,
            'test' => static function(GenericClass $classDef) {
                static::assertCount(1, $classDef->properties);

                static::assertArrayHasKey('b', $classDef->properties);
            },
        ];

        yield 'Class inheriting from interface' => [
            'subject' => ClassWithInterface::class,
            'attribute' => BasicClass::class,
            'test' => static function(BasicClass $classDef) {
                static::assertEquals(5, $classDef->a);
                static::assertEquals(10, $classDef->b);
            },
        ];

        yield 'Class with field with extra processing' => [
            'subject' => ClassWithExtraAnalysisSource::class,
            'attribute' => GenericClass::class,
            'test' => static function(GenericClass $classDef) {
                self::assertEquals('a', $classDef->properties['target']->targetDef->properties['a']->a);
            },
        ];

        yield 'Sub-attribute with its own sub-attribute' => [
            'subject' => ClassWithRecursiveSubAttributes::class,
            'attribute' => ClassWithSubSubAttributes::class,
            'test' => static function(ClassWithSubSubAttributes $classDef) {
                self::assertEquals('A', $classDef->a);
                self::assertEquals('B', $classDef->sub->b);
                self::assertEquals('C', $classDef->sub->sub->c);
                self::assertEquals(['D', 'E', 'F'], $classDef->sub->d);
            },
        ];

        yield 'Attribute on multiple components' => [
            'subject' => MultiuseClass::class,
            'attribute' => AppliesEverywhere::class,
            'test' => static function(AppliesEverywhere $classDef) {
                self::assertEquals(1, $classDef->a);
                self::assertEquals(MultiuseClass::class, $classDef->phpName);
                self::assertEquals(2, $classDef->properties['prop']->a);
                self::assertEquals('prop', $classDef->properties['prop']->phpName);
                self::assertEquals(3, $classDef->constants['B']->a);
                self::assertEquals('B', $classDef->constants['B']->phpName);
                self::assertEquals(4, $classDef->methods['method']->a);
                self::assertEquals('method', $classDef->methods['method']->phpName);
                self::assertEquals(5, $classDef->methods['method']->parameters['arg']->a);
                self::assertEquals('arg', $classDef->methods['method']->parameters['arg']->phpName);
            },
        ];

        yield 'UnitEnum with subattributes' => [
            'subject' => Suit::class,
            'attribute' => ClassWithOwnSubAttributes::class,
            'test' => static function(ClassWithOwnSubAttributes $classDef) {
                self::assertEquals('C', $classDef->c);
            },
        ];
    }

    /**
     * @test
     * @dataProvider scopedAttributeTestProvider()
     */
    public function analyze_classes_scoped(string $subject, string $attribute, ?string $scope , callable $tests): void
    {
        $analyzer = new Analyzer();

        $classDef = $analyzer->analyze($subject, $attribute, scope: $scope);
        $tests($classDef);
    }

    public function scopedAttributeTestProvider(): iterable
    {
        yield 'Incl by default: true; scope: One' => [
            'subject' => ClassWithScopes::class,
            'attribute' => ScopedClass::class,
            'scope' => 'One',
            'test' => static function(ScopedClass $classDef) {
                // Common to all cases, just to verify all components can be scoped.
                self::assertEquals('A', $classDef->val);
                self::assertEquals('A', $classDef->methods['aMethod']->val);
                self::assertEquals('A', $classDef->methods['aMethod']->parameters['param']->val);
                self::assertEquals('A', $classDef->sub->val);
                self::assertEquals('A1', $classDef->multi[0]->val);
                self::assertEquals('A2', $classDef->multi[1]->val);

                // The specific interpretation of this case.
                self::assertEquals('Z', $classDef->properties['noAttrib']->val);
                self::assertEquals('A', $classDef->properties['scoped']->val);
                self::assertEquals('Z', $classDef->properties['defaultAttrib']->val);
                self::assertArrayNotHasKey('defaultAttributeExcluded', $classDef->properties);
                self::assertEquals('A', $classDef->properties['notNullScope']->val);
                self::assertArrayNotHasKey('excludeOnlyInScopes', $classDef->properties);
                self::assertEquals('A', $classDef->properties['onlyInScopeOne']->val);
            },
        ];

        yield 'Incl by default: true; scope: Two' => [
            'subject' => ClassWithScopes::class,
            'attribute' => ScopedClass::class,
            'scope' => 'Two',
            'test' => static function(ScopedClass $classDef) {
                // Common to all cases, just to verify all components can be scoped.
                self::assertEquals('B', $classDef->val);
                self::assertEquals('B', $classDef->methods['aMethod']->val);
                self::assertEquals('B', $classDef->methods['aMethod']->parameters['param']->val);
                self::assertEquals('B', $classDef->sub->val);
                self::assertEquals('B1', $classDef->multi[0]->val);
                self::assertEquals('B2', $classDef->multi[1]->val);

                // The specific interpretation of this case.
                self::assertEquals('Z', $classDef->properties['noAttrib']->val);
                self::assertEquals('B', $classDef->properties['scoped']->val);
                self::assertEquals('Z', $classDef->properties['defaultAttrib']->val);
                self::assertArrayNotHasKey('defaultAttributeExcluded', $classDef->properties);
                self::assertEquals('B', $classDef->properties['notNullScope']->val);
                self::assertArrayNotHasKey('excludeOnlyInScopes', $classDef->properties);
                self::assertEquals('Z', $classDef->properties['onlyInScopeOne']->val);
            },
        ];

        yield 'Incl by default: true; scope: null' => [
            'subject' => ClassWithScopes::class,
            'attribute' => ScopedClass::class,
            'scope' => null,
            'test' => static function(ScopedClass $classDef) {
                // Common to all cases, just to verify all components can be scoped.
                self::assertEquals('Z', $classDef->val);
                self::assertEquals('Z', $classDef->methods['aMethod']->val);
                self::assertEquals('Z', $classDef->methods['aMethod']->parameters['param']->val);
                self::assertEquals('Z', $classDef->sub->val);
                self::assertEquals('X', $classDef->multi[0]->val);
                self::assertEquals('Y', $classDef->multi[1]->val);

                // The specific interpretation of this case.
                self::assertEquals('Z', $classDef->properties['noAttrib']->val);
                self::assertEquals('Z', $classDef->properties['scoped']->val);
                self::assertEquals('Z', $classDef->properties['defaultAttrib']->val);
                self::assertArrayNotHasKey('defaultAttributeExcluded', $classDef->properties);
                self::assertEquals('Z', $classDef->properties['notNullScope']->val);
                self::assertEquals('Z', $classDef->properties['excludeOnlyInScopes']->val);
                self::assertEquals('Z', $classDef->properties['onlyInScopeOne']->val);
            },
        ];

        yield 'Incl by default: false; scope: One' => [
            'subject' => ClassWithScopesNotDefault::class,
            'attribute' => ScopedClassNoDefaultInclude::class,
            'scope' => 'One',
            'test' => static function(ScopedClassNoDefaultInclude $classDef) {
                // Common to all cases, just to verify all components can be scoped.
                self::assertEquals('A', $classDef->val);
                self::assertEquals('A', $classDef->methods['aMethod']->val);
                self::assertEquals('A', $classDef->methods['aMethod']->parameters['param']->val);
                self::assertEquals('A', $classDef->sub->val);
                self::assertEquals('A1', $classDef->multi[0]->val);
                self::assertEquals('A2', $classDef->multi[1]->val);

                // The specific interpretation of this case.
                self::assertArrayNotHasKey('noAttrib', $classDef->properties);
                self::assertEquals('A', $classDef->properties['scoped']->val);
                self::assertEquals('Z', $classDef->properties['defaultAttrib']->val);
                self::assertArrayNotHasKey('defaultAttributeExcluded', $classDef->properties);
                self::assertEquals('A', $classDef->properties['notNullScope']->val);
                self::assertArrayNotHasKey('excludeOnlyInScopes', $classDef->properties);
                self::assertEquals('A', $classDef->properties['onlyInScopeOne']->val);
            },
        ];

        yield 'Incl by default: false; scope: Two' => [
            'subject' => ClassWithScopesNotDefault::class,
            'attribute' => ScopedClassNoDefaultInclude::class,
            'scope' => 'Two',
            'test' => static function(ScopedClassNoDefaultInclude $classDef) {
                // Common to all cases, just to verify all components can be scoped.
                self::assertEquals('B', $classDef->val);
                self::assertEquals('B', $classDef->methods['aMethod']->val);
                self::assertEquals('B', $classDef->methods['aMethod']->parameters['param']->val);
                self::assertEquals('B', $classDef->sub->val);
                self::assertEquals('B1', $classDef->multi[0]->val);
                self::assertEquals('B2', $classDef->multi[1]->val);

                // The specific interpretation of this case.
                self::assertArrayNotHasKey('noAttrib', $classDef->properties);
                self::assertEquals('B', $classDef->properties['scoped']->val);
                self::assertEquals('Z', $classDef->properties['defaultAttrib']->val);
                self::assertArrayNotHasKey('defaultAttributeExcluded', $classDef->properties);
                self::assertEquals('B', $classDef->properties['notNullScope']->val);
                self::assertArrayNotHasKey('excludeOnlyInScopes', $classDef->properties);
                self::assertArrayNotHasKey('onlyInScopeOne', $classDef->properties);
            },
        ];

        yield 'Incl by default: false; scope: null' => [
            'subject' => ClassWithScopesNotDefault::class,
            'attribute' => ScopedClassNoDefaultInclude::class,
            'scope' => null,
            'test' => static function(ScopedClassNoDefaultInclude $classDef) {
                // Common to all cases, just to verify all components can be scoped.
                self::assertArrayNotHasKey('noAttrib', $classDef->properties);
                self::assertEquals('Z', $classDef->methods['aMethod']->val);
                self::assertEquals('Z', $classDef->methods['aMethod']->parameters['param']->val);
                self::assertEquals('Z', $classDef->sub->val);
                self::assertEquals('X', $classDef->multi[0]->val);
                self::assertEquals('Y', $classDef->multi[1]->val);

                // The specific interpretation of this case.
                self::assertArrayNotHasKey('noAttrib', $classDef->properties);
                self::assertEquals('Z', $classDef->properties['scoped']->val);
                self::assertEquals('Z', $classDef->properties['defaultAttrib']->val);
                self::assertArrayNotHasKey('defaultAttributeExcluded', $classDef->properties);
                self::assertArrayNotHasKey('notNullScope', $classDef->properties);
                self::assertEquals('Z', $classDef->properties['excludeOnlyInScopes']->val);
                self::assertArrayNotHasKey('onlyInScopeOne', $classDef->properties);
            },
        ];

        yield 'LabeledApp: English' => [
            'subject' => LabeledApp::class,
            'attribute' => Labeled::class,
            'scope' => null,
            'test' => static function(Labeled $classDef) {
                self::assertEquals('Installation', $classDef->properties['install']->name);
                self::assertEquals('Setup', $classDef->properties['setup']->name);
                self::assertEquals('Untitled', $classDef->properties['login']->name);
                self::assertEquals('Untitled', $classDef->properties['customization']->name);
            },
        ];

        yield 'LabeledApp: Spanish' => [
            'subject' => LabeledApp::class,
            'attribute' => Labeled::class,
            'scope' => 'es',
            'test' => static function(Labeled $classDef) {
                self::assertEquals('Instalación', $classDef->properties['install']->name);
                self::assertEquals('Configurar', $classDef->properties['setup']->name);
                self::assertEquals('Untitled', $classDef->properties['login']->name);
                self::assertEquals('Untitled', $classDef->properties['customization']->name);
            },
        ];

        yield 'LabeledApp: German' => [
            'subject' => LabeledApp::class,
            'attribute' => Labeled::class,
            'scope' => 'de',
            'test' => static function(Labeled $classDef) {
                self::assertEquals('Installation', $classDef->properties['install']->name);
                self::assertEquals('Einrichten', $classDef->properties['setup']->name);
                self::assertEquals('Einloggen', $classDef->properties['login']->name);
                self::assertEquals('Untitled', $classDef->properties['customization']->name);
            },
        ];

        yield 'LabeledApp: French' => [
            'subject' => LabeledApp::class,
            'attribute' => Labeled::class,
            'scope' => 'fr',
            'test' => static function(Labeled $classDef) {
                self::assertEquals('Installation', $classDef->properties['install']->name);
                self::assertEquals('Setup', $classDef->properties['setup']->name);
                self::assertArrayNotHasKey('login', $classDef->properties);
                self::assertEquals('Untitled', $classDef->properties['customization']->name);
            },
        ];
    }

    /**
     * @see analyze_objects()
     */
    public function attributeObjectTestProvider(): iterable
    {
        $tests = iterator_to_array($this->attributeTestProvider());

        // For enum tests, skip those entirely since there's nothing to instantiate.
        if (function_exists('\enum_exists')) {
            $tests = array_filter($tests, static fn (array $test): bool => !\enum_exists($test['subject']));
        }

        $new = [];
        foreach ($tests as $name => $test) {
            $test['subject'] = new $test['subject'];
            $new[$name . ' (Object)'] = $test;
        }
        return $new;
    }
}
