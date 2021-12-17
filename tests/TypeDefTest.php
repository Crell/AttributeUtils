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
                static::assertTrue($typeDef->accepts('int'));
                static::assertTrue($typeDef->accepts(get_debug_type(1)));
                static::assertFalse($typeDef->accepts('string'));
            },
        ];

        yield 'simpleString' => [
            'subject' => 'simpleString',
            'test' => static function (TypeDef $typeDef) {
                static::assertFalse($typeDef->allowsNull);
                static::assertTrue($typeDef->isSimple());
                static::assertEquals('string', $typeDef->getSimpleType());
                static::assertTrue($typeDef->accepts('string'));
                static::assertTrue($typeDef->accepts(get_debug_type('hi')));
                static::assertFalse($typeDef->accepts('float'));
            },
        ];

        yield 'simpleStringNullable' => [
            'subject' => 'simpleStringNullable',
            'test' => static function (TypeDef $typeDef) {
                static::assertTrue($typeDef->allowsNull);
                static::assertTrue($typeDef->isSimple());
                static::assertEquals('string', $typeDef->getSimpleType());
                static::assertTrue($typeDef->accepts('string'));
                static::assertTrue($typeDef->accepts(get_debug_type('hi')));
                static::assertTrue($typeDef->accepts('null'));
                static::assertTrue($typeDef->accepts(get_debug_type(null)));
            },
        ];

        yield 'simpleStringNullableUnion' => [
            'subject' => 'simpleStringNullableUnion',
            'test' => static function (TypeDef $typeDef) {
                static::assertTrue($typeDef->allowsNull);
                static::assertTrue($typeDef->isSimple());
                static::assertEquals('string', $typeDef->getSimpleType());
                static::assertTrue($typeDef->accepts('string'));
                static::assertTrue($typeDef->accepts(get_debug_type('hi')));
                static::assertTrue($typeDef->accepts('null'));
                static::assertTrue($typeDef->accepts(get_debug_type(null)));
            },
        ];

        yield 'simpleArray' => [
            'subject' => 'simpleArray',
            'test' => static function (TypeDef $typeDef) {
                static::assertFalse($typeDef->allowsNull);
                static::assertTrue($typeDef->isSimple());
                static::assertEquals('array', $typeDef->getSimpleType());
                static::assertTrue($typeDef->accepts('array'));
                static::assertTrue($typeDef->accepts(get_debug_type([1, 2])));
            },
        ];

        yield 'simpleVoid' => [
            'subject' => 'simpleVoid',
            'test' => static function (TypeDef $typeDef) {
                static::assertFalse($typeDef->allowsNull);
                static::assertTrue($typeDef->isSimple());
                static::assertEquals('void', $typeDef->getSimpleType());
                static::assertTrue($typeDef->accepts('void'));
                static::assertFalse($typeDef->accepts('string'));
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
                // @todo accepts() doesn't work with this yet.
            },
        ];

        yield 'simpleClass' => [
            'subject' => 'simpleClass',
            'test' => static function (TypeDef $typeDef) {
                static::assertFalse($typeDef->allowsNull);
                static::assertTrue($typeDef->isSimple());
                static::assertEquals(OtherClass::class, $typeDef->getSimpleType());
                static::assertTrue($typeDef->accepts(OtherClass::class));
                static::assertFalse($typeDef->accepts(SomeClass::class));
            },
        ];

        yield 'scalarUnion' => [
            'subject' => 'scalarUnion',
            'test' => static function (TypeDef $typeDef) {
                static::assertFalse($typeDef->allowsNull);
                static::assertFalse($typeDef->isSimple());
                static::assertEquals(TypeComplexity::Union, $typeDef->complexity);
                static::assertTrue($typeDef->accepts('int'));
                static::assertTrue($typeDef->accepts('string'));
                static::assertFalse($typeDef->accepts(SomeClass::class));
            },
        ];

        yield 'mixedUnion' => [
            'subject' => 'mixedUnion',
            'test' => static function (TypeDef $typeDef) {
                static::assertFalse($typeDef->allowsNull);
                static::assertFalse($typeDef->isSimple());
                static::assertEquals(TypeComplexity::Union, $typeDef->complexity);
                static::assertTrue($typeDef->accepts(SomeClass::class));
                static::assertTrue($typeDef->accepts('string'));
                static::assertFalse($typeDef->accepts(OtherClass::class));
            },
        ];

        yield 'intersection' => [
            'subject' => 'intersection',
            'test' => static function (TypeDef $typeDef) {
                static::assertFalse($typeDef->allowsNull);
                static::assertFalse($typeDef->isSimple());
                static::assertEquals(TypeComplexity::Intersection, $typeDef->complexity);
                static::assertFalse($typeDef->accepts(SomeClass::class));
                static::assertFalse($typeDef->accepts(OtherClass::class));
            },
        ];

        yield 'interfaceIntersection' => [
            'subject' => 'interfaceIntersection',
            'test' => static function (TypeDef $typeDef) {
                static::assertFalse($typeDef->allowsNull);
                static::assertFalse($typeDef->isSimple());
                static::assertEquals(TypeComplexity::Intersection, $typeDef->complexity);
                static::assertTrue($typeDef->accepts(Implementer::class));
                static::assertFalse($typeDef->accepts(IncompleteImplementer::class));
                static::assertFalse($typeDef->accepts(OtherClass::class));
            },
        ];

        yield 'mixedReturn' => [
            'subject' => 'mixedReturn',
            'test' => static function (TypeDef $typeDef) {
                static::assertTrue($typeDef->allowsNull);
                static::assertTrue($typeDef->isSimple());
                static::assertEquals(TypeComplexity::Simple, $typeDef->complexity);
                static::assertTrue($typeDef->accepts('string'));
                static::assertTrue($typeDef->accepts(Implementer::class));
                static::assertTrue($typeDef->accepts(IncompleteImplementer::class));
                static::assertTrue($typeDef->accepts(OtherClass::class));
            },
        ];

        yield 'noReturnType' => [
            'subject' => 'noReturnType',
            'test' => static function (TypeDef $typeDef) {
                static::assertTrue($typeDef->allowsNull);
                static::assertTrue($typeDef->isSimple());
                static::assertEquals(TypeComplexity::Simple, $typeDef->complexity);
                static::assertTrue($typeDef->accepts('string'));
                static::assertTrue($typeDef->accepts(Implementer::class));
                static::assertTrue($typeDef->accepts(IncompleteImplementer::class));
                static::assertTrue($typeDef->accepts(OtherClass::class));
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

    // Curiously, PHP lets us define this type but it is impossible.
    public function intersection(): SomeClass&OtherClass {}

    public function interfaceIntersection(): I1&I2 {}

    public function mixedReturn(): mixed {}

    public function noReturnType() {}
}

class SomeClass {}

class OtherClass {}

interface I1 {}

interface I2 {}

class Implementer implements I1, I2 {}

class IncompleteImplementer implements I1 {}
