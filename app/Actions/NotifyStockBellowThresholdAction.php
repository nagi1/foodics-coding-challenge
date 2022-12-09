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
        // First, we filter the ingredients that are not notified
        // yet Then, we send the notification to admins, and
        // mark the ingredients as notified.
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
