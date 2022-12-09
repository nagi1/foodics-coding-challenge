<?php

namespace App\Enums;

enum OrderStatus: string
{
    case NOT_ENOUGH_INGREDIENTS = 'not_enough_ingredients';
    case PENDING = 'pending';
}
