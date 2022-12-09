<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\IngredientProduct;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'ingredient_product')
        ->using(IngredientProduct::class)
        ->withPivot([
            'weight',
            'quantity',
            'unit',
        ]);
    }
}
