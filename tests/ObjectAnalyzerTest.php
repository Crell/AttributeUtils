<?php

declare(strict_types=1);

namespace Crell\ObjectAnalyzer;

use Crell\ObjectAnalyzer\Attributes\BasicClassFielded;
use Crell\ObjectAnalyzer\Attributes\BasicClassReflectable;
use Crell\ObjectAnalyzer\Attributes\BasicClass;
use Crell\ObjectAnalyzer\Records\BasicWithCustomizedFields;
use Crell\ObjectAnalyzer\Records\BasicWithDefaultFields;
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
    public function generic(string $subject, string $attribute, callable $test): void
    {
        $analyzer = new ObjectAnalyzer();

        $classDef = $analyzer->analyze($subject, $attribute);

        $test($classDef);
    }

    public function attributeTestProvider(): iterable
    {
        yield 'Generic' => [
            'subject' => Point::class,
            'attribute' => BasicClass::class,
            'test' => static function(object $classDef) {
                static::assertInstanceOf(BasicClass::class, $classDef);
                static::assertEquals(0, $classDef->a);
                static::assertEquals(0, $classDef->b);
            },
        ];

        yield 'Reflectable with no override value' => [
            'subject' => NoProps::class,
            'attribute' => BasicClassReflectable::class,
            'test' => static function(object $classDef) {
                static::assertInstanceOf(BasicClassReflectable::class, $classDef);
                static::assertEquals(1, $classDef->a);
                static::assertEquals(0, $classDef->b);
                static::assertEquals('NoProps', $classDef->name);
            },
        ];

        yield 'Reflectable with an override value' => [
            'subject' => NoPropsOverride::class,
            'attribute' => BasicClassReflectable::class,
            'test' => static function(object $classDef) {
                static::assertInstanceOf(BasicClassReflectable::class, $classDef);
                static::assertEquals(1, $classDef->a);
                static::assertEquals(0, $classDef->b);
                static::assertEquals('Overridden', $classDef->name);
            },
        ];

        yield 'Fieldable with default properties' => [
            'subject' => BasicWithDefaultFields::class,
            'attribute' => BasicClassFielded::class,
            'test' => static function(object $classDef) {
                static::assertInstanceOf(BasicClassFielded::class, $classDef);
                static::assertEquals('a', $classDef->fields['i']->a);
                static::assertEquals('b', $classDef->fields['i']->b);
                static::assertEquals('a', $classDef->fields['s']->a);
                static::assertEquals('b', $classDef->fields['s']->b);
                static::assertEquals('a', $classDef->fields['f']->a);
                static::assertEquals('b', $classDef->fields['f']->b);
            },
        ];

        yield 'Fieldable with customized properties' => [
            'subject' => BasicWithCustomizedFields::class,
            'attribute' => BasicClassFielded::class,
            'test' => static function(object $classDef) {
                static::assertInstanceOf(BasicClassFielded::class, $classDef);
                static::assertEquals('a', $classDef->fields['i']->a);
                static::assertEquals('b', $classDef->fields['i']->b);
                static::assertEquals('A', $classDef->fields['s']->a);
                static::assertEquals('b', $classDef->fields['s']->b);
                static::assertEquals('a', $classDef->fields['f']->a);
                static::assertEquals('B', $classDef->fields['f']->b);
            },
        ];
    }
}
