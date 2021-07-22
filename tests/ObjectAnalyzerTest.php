<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer;

use Crell\ObjectAnalyzer\Attributes\BasicClass;
use Crell\ObjectAnalyzer\Attributes\BasicClassFielded;
use Crell\ObjectAnalyzer\Attributes\BasicClassReflectable;
use Crell\ObjectAnalyzer\Attributes\BasicClassReflectableProperties;
use Crell\ObjectAnalyzer\Records\BasicWithCustomizedFields;
use Crell\ObjectAnalyzer\Records\BasicWithCustomizedFieldsExcludeByDefault;
use Crell\ObjectAnalyzer\Records\BasicWithDefaultFields;
use Crell\ObjectAnalyzer\Records\BasicWithReflectableProperties;
use Crell\ObjectAnalyzer\Records\NoProps;
use Crell\ObjectAnalyzer\Records\NoPropsOverride;
use Crell\ObjectAnalyzer\Records\Point;
use PHPUnit\Framework\TestCase;

class ObjectAnalyzerTest extends TestCase
{

    /**
     * @test
     * @dataProvider attributeTestProvider()
     */
    public function analyze_classes(string $subject, string $attribute, callable $test): void
    {
        $analyzer = new ObjectAnalyzer();

        $classDef = $analyzer->analyze($subject, $attribute);

        $test($classDef);
    }

    /**
     * @test
     * @dataProvider attributeObjectTestProvider()
     */
    public function analyze_objects(object $subject, string $attribute, callable $test): void
    {
        $analyzer = new ObjectAnalyzer();

        $classDef = $analyzer->analyze($subject, $attribute);

        $test($classDef);
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
            'attribute' => BasicClassReflectable::class,
            'test' => static function(BasicClassReflectable $classDef) {
                static::assertEquals(1, $classDef->a);
                static::assertEquals(0, $classDef->b);
                static::assertEquals('NoProps', $classDef->name);
            },
        ];

        yield 'Reflectable with an override value' => [
            'subject' => NoPropsOverride::class,
            'attribute' => BasicClassReflectable::class,
            'test' => static function(BasicClassReflectable $classDef) {
                static::assertEquals(1, $classDef->a);
                static::assertEquals(0, $classDef->b);
                static::assertEquals('Overridden', $classDef->name);
            },
        ];

        yield 'Fieldable with default properties' => [
            'subject' => BasicWithDefaultFields::class,
            'attribute' => BasicClassFielded::class,
            'test' => static function(BasicClassFielded $classDef) {
                static::assertEquals('a', $classDef->properties['i']->a);
                static::assertEquals('b', $classDef->properties['i']->b);
                static::assertEquals('a', $classDef->properties['s']->a);
                static::assertEquals('b', $classDef->properties['s']->b);
                static::assertEquals('a', $classDef->properties['f']->a);
                static::assertEquals('b', $classDef->properties['f']->b);
            },
        ];

        yield 'Fieldable with customized properties' => [
            'subject' => BasicWithCustomizedFields::class,
            'attribute' => BasicClassFielded::class,
            'test' => static function(BasicClassFielded $classDef) {
                static::assertEquals('a', $classDef->properties['i']->a);
                static::assertEquals('b', $classDef->properties['i']->b);
                static::assertEquals('A', $classDef->properties['s']->a);
                static::assertEquals('b', $classDef->properties['s']->b);
                static::assertEquals('a', $classDef->properties['f']->a);
                static::assertEquals('B', $classDef->properties['f']->b);
            },
        ];

        yield 'Fieldable default no-include fields' => [
            'subject' => BasicWithCustomizedFieldsExcludeByDefault::class,
            'attribute' => BasicClassFielded::class,
            'test' => static function(BasicClassFielded $classDef) {
                static::assertArrayNotHasKey('i', $classDef->properties);
                static::assertEquals('A', $classDef->properties['s']->a);
                static::assertEquals('b', $classDef->properties['s']->b);
                static::assertEquals('a', $classDef->properties['f']->a);
                static::assertEquals('B', $classDef->properties['f']->b);
            },
        ];

        yield 'Fieldable reflectable fields' => [
            'subject' => BasicWithReflectableProperties::class,
            'attribute' => BasicClassReflectableProperties::class,
            'test' => static function(BasicClassReflectableProperties $classDef) {
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
