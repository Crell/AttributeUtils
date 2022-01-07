<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

use Crell\AttributeUtils\Attributes\Reflect\MethodType;
use Crell\AttributeUtils\Attributes\Reflect\ReflectClass;
use Crell\AttributeUtils\Attributes\Reflect\ReflectEnum;
use Crell\AttributeUtils\Attributes\Reflect\ReflectProperty;
use Crell\AttributeUtils\Records\NoProps;
use Crell\AttributeUtils\Records\Reflect\AnInterface;
use Crell\AttributeUtils\Records\Reflect\ClassUsesTrait;
use Crell\AttributeUtils\Records\Reflect\Complete;
use Crell\AttributeUtils\Records\Reflect\SampleTrait;
use Crell\AttributeUtils\TypeDef\BackedSuit;
use Crell\AttributeUtils\TypeDef\Suit;
use PHPUnit\Framework\TestCase;

/**
 * @requires PHP >= 8.1.0
 */
class ReflectTest extends TestCase
{
    /**
     * @test
     * @dataProvider classAttributeExamples()
     */
    public function analyze_classes(string $subject, callable $test): void
    {
        $analyzer = new Analyzer();

        $classDef = $analyzer->analyze($subject, ReflectClass::class);

        $test($classDef);
    }
    /**
     * @test
     * @dataProvider enumAttributeExamples()
     */
    public function analyze_enums(string $subject, callable $test): void
    {
        $analyzer = new Analyzer();

        $classDef = $analyzer->analyze($subject, ReflectEnum::class);

        $test($classDef);
    }

    public function enumAttributeExamples(): iterable
    {
        yield Suit::class => [
            'subject' => Suit::class,
            'test' => static function (ReflectEnum $enumDef) {
                static::assertEquals(Suit::class, $enumDef->phpName);
                // The one method is the cases() method.
                static::assertCount(1, $enumDef->staticMethods);
                static::assertCount(1, $enumDef->methods);
                static::assertCount(1, $enumDef->constants);
                static::assertCount(4, $enumDef->cases);
                static::assertFalse($enumDef->isInternal);
                static::assertFalse($enumDef->isIterable);
                static::assertFalse($enumDef->isBacked);
                static::assertFalse($enumDef->cases['Spades']->isBacked);
                static::assertEquals(Suit::Spades,$enumDef->constants['Joker']->value);
            },
        ];

        yield BackedSuit::class => [
            'subject' => BackedSuit::class,
            'test' => static function (ReflectEnum $enumDef) {
                static::assertEquals(BackedSuit::class, $enumDef->phpName);
                // The built in cases(), from(), and tryFrom().
                static::assertCount(3, $enumDef->staticMethods);
                static::assertCount(1, $enumDef->methods);
                static::assertCount(1, $enumDef->constants);
                static::assertCount(4, $enumDef->cases);
                static::assertFalse($enumDef->isInternal);
                static::assertFalse($enumDef->isIterable);
                static::assertTrue($enumDef->isBacked);
                static::assertTrue($enumDef->cases['Spades']->isBacked);
                static::assertEquals('string', $enumDef->backingType);
                static::assertEquals('S', $enumDef->cases['Spades']->value);
                static::assertEquals(BackedSuit::Spades,$enumDef->constants['Joker']->value);
            },
        ];
    }

    public function classAttributeExamples(): iterable
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
                static::assertEquals(ClassType::NormalClass, $classDef->structType);

                static::assertCount(7, $classDef->properties);
                static::assertCount(3, $classDef->staticProperties);
                static::assertCount(8, $classDef->methods);
                static::assertCount(4, $classDef->constants);

                static::assertInstanceOf(ReflectProperty::class, $classDef->properties['public']);
                static::assertInstanceOf(ReflectProperty::class, $classDef->staticProperties['publicStatic']);
                static::assertEquals('public', $classDef->properties['public']->phpName);
                static::assertEquals(Visibility::Public, $classDef->properties['public']->visibility);
                static::assertEquals('int', $classDef->properties['public']->type->getSimpleType());

                static::assertTrue($classDef->properties['promoted']->isPromoted);
                static::assertEquals('string', $classDef->properties['promoted']->type->getSimpleType());

                static::assertEquals(MethodType::Constructor, $classDef->methods['__construct']->methodType);
                static::assertEquals(MethodType::Destructor, $classDef->methods['__destruct']->methodType);
                static::assertEquals(MethodType::Normal, $classDef->methods['privateMethod']->methodType);

                static::assertEquals('string', $classDef->methods['publicMethod']->returnType->getSimpleType());
                static::assertEquals('mixed', $classDef->methods['variadic']->returnType->getSimpleType());

                static::assertTrue($classDef->methods['variadic']->isVariadic);
                static::assertTrue($classDef->methods['generator']->isGenerator);

                static::assertCount(4, $classDef->methods['methodWithArgs']->parameters);
                static::assertEquals('int', $classDef->methods['methodWithArgs']->parameters['int']->phpName);
                static::assertFalse($classDef->methods['methodWithArgs']->parameters['int']->isVariadic);
                static::assertEquals('string', $classDef->methods['methodWithArgs']->parameters['string']->type->getSimpleType());
                static::assertEquals('mixed', $classDef->methods['methodWithArgs']->parameters['untyped']->type->getSimpleType());

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
                static::assertEquals(ClassType::Trait, $classDef->structType);
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
                static::assertEquals(ClassType::Interface, $classDef->structType);
                static::assertCount(1, $classDef->methods);
                static::assertEquals('interfaceMethod', $classDef->methods['interfaceMethod']->phpName);
            },
        ];
    }
}
