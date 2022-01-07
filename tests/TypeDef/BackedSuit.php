<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\TypeDef;

enum BackedSuit: string
{
    case Spades = 'S';
    case Clubs = 'C';
    case Diamonds = 'D';
    case Hearts = 'H';

    public const Joker = BackedSuit::Spades;

    public function color(): string
    {
        return match($this) {
            self::Spades, self::Clubs => 'black',
            default => 'red',
        };
    }
}
