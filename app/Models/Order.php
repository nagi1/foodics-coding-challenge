<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'total_price' => 'float',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_product')
        ->using(OrderProduct::class)
        ->withPivot([
            'quantity',
        ]);
    }
}
