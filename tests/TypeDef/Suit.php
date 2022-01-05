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
}
