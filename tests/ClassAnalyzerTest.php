<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer;

use Crell\ObjectAnalyzer\Attributes\BasicClass;
use Crell\ObjectAnalyzer\Attributes\BasicProperty;
use Crell\ObjectAnalyzer\Attributes\ClassWithProperties;
use Crell\ObjectAnalyzer\Attributes\ClassWithReflection;
use Crell\ObjectAnalyzer\Attributes\ClassWithReflectableProperties;
use Crell\ObjectAnalyzer\Records\ClassWithCustomizedFields;
use Crell\ObjectAnalyzer\Records\ClassWithCustomizedPropertiesExcludeByDefault;
use Crell\ObjectAnalyzer\Records\ClassWithDefaultFields;
use Crell\ObjectAnalyzer\Records\ClassWithPropertiesWithReflection;
use Crell\ObjectAnalyzer\Records\NoProps;
use Crell\ObjectAnalyzer\Records\NoPropsOverride;
use Crell\ObjectAnalyzer\Records\Point;
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
    public function attributeTestProvider(): iterable
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
            'attribute' => ClassWithReflectableProperties::class,
            'test' => static function(ClassWithReflectableProperties $classDef) {
                static::assertEquals('i', $classDef->properties['i']->name);
                static::assertEquals('beep', $classDef->properties['s']->name);
                static::assertEquals('f', $classDef->properties['f']->name);
            },
        ];

    }

    /**
     * @see analyze_objects()
     */
    public function attributeObjectTestProvider(): iterable
    {
        $tests = iterator_to_array($this->attributeTestProvider());

        $new = [];
        foreach ($tests as $name => $test) {
            $test['subject'] = new $test['subject'];
            $new[$name . ' (Object)'] = $test;
        }
        return $new;
    }
}
