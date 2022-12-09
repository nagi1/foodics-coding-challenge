<?php

use App\Models\Ingredient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Actions\CalculateUsedStockAction;

uses(RefreshDatabase::class);

it('can calculate the used stock in a product based on product quantity', function () {
    $beef = Ingredient::factory()->makeBeef(20_000)->create();
    $cheese = Ingredient::factory()->makeCheese(5000)->create();
    $onion = Ingredient::factory()->makeOnion(1000)->create();
    $chicken = Ingredient::factory()->makeChicken(5000)->create();
    $tomato = Ingredient::factory()->makeTomato(1000)->create();


    $chickenSandwitch = makeChickenSandwitch();
    $burger = makeBurger();

    $chickenInChickenSandwitch = $chickenSandwitch->ingredients()->where('ingredient_id', $chicken->id)->first()->pivot;
    $beefInBurger = $burger->ingredients()->where('ingredient_id', $beef->id)->first()->pivot;

    $actual = app(CalculateUsedStockAction::class)->execute($chickenInChickenSandwitch, 2);

    // 300 = chicken-sandwitch(2) * chicken(2) * chicken-weight(75)
    expect($actual)->toBe(300);

    $actual = app(CalculateUsedStockAction::class)->execute($beefInBurger, 3);

    // 450 = burgers(3) * beef(1) * beef-weight(150)
    expect($actual)->toBe(450);
});
