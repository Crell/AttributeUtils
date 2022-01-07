<?php

declare(strict_types=1);

namespace Crell\AttributeUtils\TypeDef;

enum Suit
{
    case Spades;
    case Clubs;
    case Diamonds;
    case Hearts;

    public const Joker = Suit::Spades;

    public function color(): string
    {
        return match($this) {
            self::Spades, self::Clubs => 'black',
            default => 'red',
        };
    }
}
