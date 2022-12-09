<?php

namespace App\Enums;

enum IngredientUnit: string
{
    case Grams = 'g';
    case Kilograms = 'kg';

    // convert to grams
    public function toGrams(int $weight): int
    {
        return match ($this->value) {
            self::Grams->value => $weight,
            self::Kilograms->value => $weight * 1000,
        };
    }

    // convert to kilograms
    public function toKilograms(float $weight): float
    {
        return match ($this->value) {
            self::Grams->value => $weight / 1000,
            self::Kilograms->value => $weight,
        };
    }
}
