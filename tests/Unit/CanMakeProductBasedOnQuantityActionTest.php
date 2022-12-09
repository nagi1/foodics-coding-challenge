<?php

use App\Actions\CanMakeProductBasedOnQuantityAction;
use App\Models\Ingredient;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can calculate the maximum number of products based on a given quantity', function () {
    Ingredient::factory()->makeBeef(20_000)->create();
    Ingredient::factory()->makeCheese(5000)->create();
    Ingredient::factory()->makeOnion(1000)->create();
    $product = makeBurger();

    $actual = app(CanMakeProductBasedOnQuantityAction::class)->execute($product, 10);

    expect($actual)->toBeTrue();
});

it('can return false when it cant make product for given quantity', function () {
    Ingredient::factory()->makeBeef(20_000)->create();
    Ingredient::factory()->makeCheese(5000)->create();
    Ingredient::factory()->makeOnion(1000)->create();

    $product = makeBurger();

    $actual = app(CanMakeProductBasedOnQuantityAction::class)->execute($product, 100);

    expect($actual)->toBeFalse();
});
