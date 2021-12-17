<?php

declare(strict_types=1);

namespace Crell\AttributeUtils;

use PHPUnit\Framework\TestCase;

/**
 * @requires PHP >= 8.1.0
 */
class TypeDefTest extends TestCase
{
    /**
     * @test
     * @dataProvider typeDefProvider()
     */
    public function typedefs(string $methodName, callable $test): void
    {
        $rType = (new \ReflectionClass(TypeExamples::class))
            ->getMethod($methodName)
            ->getReturnType();

        $def = new TypeDef($rType);

        $test($def);
    }

    public function typeDefProvider(): iterable
    {
        yield 'simpleInt' => [
            'subject' => 'simpleInt',
            'test' => static function (TypeDef $typeDef) {
                static::assertFalse($typeDef->allowsNull);
                static::assertTrue($typeDef->isSimple());
                static::assertEquals('int', $typeDef->getSimpleType());
            },
        ];

        yield 'simpleString' => [
            'subject' => 'simpleString',
            'test' => static function (TypeDef $typeDef) {
                static::assertFalse($typeDef->allowsNull);
                static::assertTrue($typeDef->isSimple());
                static::assertEquals('string', $typeDef->getSimpleType());
            },
        ];

        yield 'simpleStringNullable' => [
            'subject' => 'simpleStringNullable',
            'test' => static function (TypeDef $typeDef) {
                static::assertTrue($typeDef->allowsNull);
                static::assertTrue($typeDef->isSimple());
                static::assertEquals('string', $typeDef->getSimpleType());
            },
        ];

        yield 'simpleStringNullableUnion' => [
            'subject' => 'simpleStringNullableUnion',
            'test' => static function (TypeDef $typeDef) {
                static::assertTrue($typeDef->allowsNull);
                static::assertTrue($typeDef->isSimple());
                static::assertEquals('string', $typeDef->getSimpleType());
            },
        ];

        yield 'simpleArray' => [
            'subject' => 'simpleArray',
            'test' => static function (TypeDef $typeDef) {
                static::assertFalse($typeDef->allowsNull);
                static::assertTrue($typeDef->isSimple());
                static::assertEquals('array', $typeDef->getSimpleType());
            },
        ];

        yield 'simpleVoid' => [
            'subject' => 'simpleVoid',
            'test' => static function (TypeDef $typeDef) {
                static::assertFalse($typeDef->allowsNull);
                static::assertTrue($typeDef->isSimple());
                static::assertEquals('void', $typeDef->getSimpleType());
            },
        ];

        yield 'simpleNever' => [
            'subject' => 'simpleNever',
            'test' => static function (TypeDef $typeDef) {
                static::assertFalse($typeDef->allowsNull);
                static::assertTrue($typeDef->isSimple());
                static::assertEquals('never', $typeDef->getSimpleType());
            },
        ];

        yield 'returnsStatic' => [
            'subject' => 'returnsStatic',
            'test' => static function (TypeDef $typeDef) {
                static::assertFalse($typeDef->allowsNull);
                static::assertTrue($typeDef->isSimple());
                static::assertEquals('static', $typeDef->getSimpleType());
            },
        ];

        yield 'simpleClass' => [
            'subject' => 'simpleClass',
            'test' => static function (TypeDef $typeDef) {
                static::assertFalse($typeDef->allowsNull);
                static::assertTrue($typeDef->isSimple());
                static::assertEquals(OtherClass::class, $typeDef->getSimpleType());
            },
        ];

        yield 'scalarUnion' => [
            'subject' => 'scalarUnion',
            'test' => static function (TypeDef $typeDef) {
                static::assertFalse($typeDef->allowsNull);
                static::assertFalse($typeDef->isSimple());
                static::assertEquals(TypeComplexity::Union, $typeDef->complexity);
            },
        ];

        yield 'mixedUnion' => [
            'subject' => 'mixedUnion',
            'test' => static function (TypeDef $typeDef) {
                static::assertFalse($typeDef->allowsNull);
                static::assertFalse($typeDef->isSimple());
                static::assertEquals(TypeComplexity::Union, $typeDef->complexity);
            },
        ];

        yield 'intersection' => [
            'subject' => 'intersection',
            'test' => static function (TypeDef $typeDef) {
                static::assertFalse($typeDef->allowsNull);
                static::assertFalse($typeDef->isSimple());
                static::assertEquals(TypeComplexity::Intersection, $typeDef->complexity);
            },
        ];

    }
}

class ParentClass
{
    public function returnsSelfParent(): self {}

    public function returnsStatic(): static {}
}

class TypeExamples
{
    public function simpleInt(): int {}

    public function simpleString(): string {}

    public function simpleStringNullable(): ?string {}

    public function simpleStringNullableUnion(): string|null {}

    public function simpleArray(): array {}

    public function simpleClass(): OtherClass {}

    public function simpleVoid(): void {}

    public function simpleNever(): never {}

    public function returnsSelfChild(): self {}

    public function returnsStatic(): static {}

    public function scalarUnion(): int|string {}

    public function mixedUnion(): SomeClass|string {}

    public function intersection(): SomeClass&OtherClass {}
}

class SomeClass {}

class OtherClass {}
