<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

use Crell\AttributeUtils\Attributes\Reflect\MethodType;
use Crell\AttributeUtils\Attributes\Reflect\ReflectClass;
use Crell\AttributeUtils\Attributes\Reflect\ReflectProperty;
use Crell\AttributeUtils\Attributes\Reflect\StructType;
use Crell\AttributeUtils\Attributes\Reflect\Visibility;
use Crell\AttributeUtils\Records\NoProps;
use Crell\AttributeUtils\Records\Reflect\AnInterface;
use Crell\AttributeUtils\Records\Reflect\ClassUsesTrait;
use Crell\AttributeUtils\Records\Reflect\Complete;
use Crell\AttributeUtils\Records\Reflect\SampleTrait;
use PHPUnit\Framework\TestCase;

/**
 * @requires PHP >= 8.1.0
 */
class ReflectTest extends TestCase
{
    /**
     * @test
     * @dataProvider attributeTestProvider()
     */
    public function analyze_classes(string $subject, callable $test): void
    {
        $analyzer = new Analyzer();

        $classDef = $analyzer->analyze($subject, ReflectClass::class);

        $test($classDef);
    }

    public function attributeTestProvider(): iterable
    {
        yield NoProps::class => [
            'subject' => NoProps::class,
            'test' => static function (ReflectClass $classDef) {
                static::assertEmpty($classDef->properties);
            },
        ];

        yield Complete::class => [
            'subject' => Complete::class,
            'test' => static function (ReflectClass $classDef) {
                static::assertEquals(Complete::class, $classDef->phpName);
                static::assertEquals('Complete', $classDef->shortName);
                static::assertEquals('Crell\\AttributeUtils\\Records\\Reflect', $classDef->namespace);
                static::assertFalse($classDef->isInternal);
                static::assertFalse($classDef->isIterable);
                static::assertFalse($classDef->isFinal);
                static::assertTrue($classDef->isInstantiable);
                static::assertTrue($classDef->isCloneable);
                static::assertEquals(StructType::NormalClass, $classDef->structType);

                static::assertCount(10, $classDef->properties);
                static::assertCount(8, $classDef->methods);
                static::assertCount(4, $classDef->constants);

                static::assertInstanceOf(ReflectProperty::class, $classDef->properties['public']);
                static::assertEquals('public', $classDef->properties['public']->phpName);
                static::assertEquals(Visibility::Public, $classDef->properties['public']->visibility);

                static::assertTrue($classDef->properties['promoted']->isPromoted);

                static::assertEquals(MethodType::Constructor, $classDef->methods['__construct']->methodType);
                static::assertEquals(MethodType::Destructor, $classDef->methods['__destruct']->methodType);
                static::assertEquals(MethodType::Normal, $classDef->methods['privateMethod']->methodType);

                static::assertTrue($classDef->methods['variadic']->isVariadic);
                static::assertTrue($classDef->methods['generator']->isGenerator);

                static::assertCount(4, $classDef->methods['methodWithArgs']->parameters);
                static::assertEquals('int', $classDef->methods['methodWithArgs']->parameters['int']->phpName);
                static::assertFalse($classDef->methods['methodWithArgs']->parameters['int']->isVariadic);

                static::assertEquals('PublicConst', $classDef->constants['PublicConst']->phpName);
                static::assertEquals(1, $classDef->constants['PublicConst']->value);
                static::assertEquals(Visibility::Public, $classDef->constants['PublicConst']->visibility);

                static::assertTrue($classDef->constants['PublicFinalConst']->isFinal);
            },
        ];

        yield SampleTrait::class => [
            'subject' => SampleTrait::class,
            'test' => static function (ReflectClass $classDef) {
                static::assertEquals(SampleTrait::class, $classDef->phpName);
                static::assertEquals(StructType::Trait, $classDef->structType);
                static::assertCount(1, $classDef->methods);
                static::assertEquals('traitMethod', $classDef->methods['traitMethod']->phpName);
                static::assertEquals('val', $classDef->properties['val']->phpName);
            },
        ];

        yield ClassUsesTrait::class => [
            'subject' => ClassUsesTrait::class,
            'test' => static function (ReflectClass $classDef) {
                static::assertEquals(ClassUsesTrait::class, $classDef->phpName);
                static::assertCount(2, $classDef->methods);
                static::assertEquals('traitMethod', $classDef->methods['traitMethod']->phpName);
                static::assertEquals('localMethod', $classDef->methods['localMethod']->phpName);
                static::assertEquals('val', $classDef->properties['val']->phpName);
            },
        ];

        yield AnInterface::class => [
            'subject' => AnInterface::class,
            'test' => static function (ReflectClass $classDef) {
                static::assertEquals(AnInterface::class, $classDef->phpName);
                static::assertEquals(StructType::Interface, $classDef->structType);
                static::assertCount(1, $classDef->methods);
                static::assertEquals('interfaceMethod', $classDef->methods['interfaceMethod']->phpName);
            },
        ];
    }
}
