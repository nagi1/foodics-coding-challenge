<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $dates = [
        'notified_at',
    ];

    public function stockBellowPercentageThreshold(int $threshold = 50): bool
    {
        // max_stock = 20
        // stock = 10
        // threshold = 50
        // 10 / 20 * 100 = 50
        return ($this->stock / $this->max_stock) * 100 < $threshold;
    }

    public function notify(): void
    {
        $this->fill([
            'notified_at' => now(),
        ])->update();
    }

    public function isNotified(): bool
    {
        return $this->notified_at !== null;
    }
}
