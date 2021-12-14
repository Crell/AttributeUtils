<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\Records\Reflect;

class Complete
{
    public const PublicConst = 1;
    protected const ProtectedConst = 1;
    private const PrivateConst = 1;

    final public const PublicFinalConst = 1;

    public int $public;
    protected int $protected;
    private int $private;

    private string $notPromoted;

    public $untyped;

    public string $hasDefault = 'default';

    public static string $publicStatic;
    protected static string $protectedStatic;
    private static string $privateStatic;

    public function __construct(public string $promoted, string $notPromoted)
    {
        $this->notPromoted = $notPromoted;
    }

    public function __destruct()
    {

    }

    public function methodWithArgs(string $string, int $int, mixed $mixed = null, $untyped = null): string
    {

    }

    public function publicMethod(): string
    {
        return 's';
    }

    protected function protectedMethod(): int
    {
        return 1;
    }

    public function privateMethod(): float
    {
        return 3.14;
    }

    protected function variadic($a, ...$b)
    {

    }

    public function generator(): iterable
    {
        yield 1;
    }


}
