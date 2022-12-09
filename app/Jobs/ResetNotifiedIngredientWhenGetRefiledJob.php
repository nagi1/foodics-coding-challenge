<?php

namespace App\Jobs;

use App\Models\Ingredient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ResetNotifiedIngredientWhenGetRefiledJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        // loop over all ingredients, and check if they are notified
        // if they're notified and stock is above the threshold
        // then reset the notified_at column to null.

        Ingredient::query()
            ->select('id', 'stock', 'max_stock', 'notified_at')
            ->whereNotNull('notified_at')
            ->get()
            ->each(function (Ingredient $ingredient) {
                if (! $ingredient->stockBellowPercentageThreshold()) {
                    $ingredient->notified_at = null;
                    $ingredient->save();
                }
            });
    }
}
