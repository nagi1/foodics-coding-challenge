<?php

namespace App\Actions;

use App\Models\Ingredient;
use App\Models\User;
use App\Notifications\StockBellowThresholdNotification;
use Illuminate\Support\Collection;
use Notification;

class NotifyStockBellowThresholdAction
{
    public function execute(Collection $ingredients): void
    {
        $ingredients
        ->filter(fn (Ingredient $ingredient) => ! $ingredient->isNotified())
        ->each(function (Ingredient $ingredient) {
            try {
                Notification::send(User::admins()->select(['id', 'email', 'role'])->get(), new StockBellowThresholdNotification($ingredient->name, $ingredient->stock));
                $ingredient->notify();
            } catch (\Exception $e) {
                // log error
                throw $e;
            }
        });
    }
}
