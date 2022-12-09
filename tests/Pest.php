<?php

use App\Actions\MakeProductUsingIngredientsAction;
use App\Models\Ingredient;
use App\Models\Product;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(Tests\TestCase::class)->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function makeBurger(): Product
{
    // Make Burger
    return app(MakeProductUsingIngredientsAction::class)->execute([
        'name' => 'Burger',
        'description' => 'Beef Burger',
        'price' => 35.99,
    ], [
        [
            'ingredient_id' => Ingredient::query()->select('id')->where('name', 'Beef')->first(), // Beef
            'quantity' => 1,
            'weight' => 150,
        ],
        [
            'ingredient_id' => Ingredient::query()->select('id')->where('name', 'Cheese')->first(), // Cheese
            'quantity' => 1,
            'weight' => 30,
        ],
        [
            'ingredient_id' => Ingredient::query()->select('id')->where('name', 'Onion')->first(), // Onion
            'quantity' => 1,
            'weight' => 20,
        ],
    ]);
}

function makeChickenSandwitch(): Product
{
    // Make Cheese Sandwitch
    return app(MakeProductUsingIngredientsAction::class)->execute([
        'name' => 'Cheese Sandwitch',
        'description' => 'Cheese Sandwitch',
        'price' => 25.99,
    ], [
        [
            'ingredient_id' => Ingredient::query()->select('id')->where('name', 'Cheese')->first(), // Cheese
            'quantity' => 2,
            'weight' => 50,
        ],
        [
            'ingredient_id' => Ingredient::query()->select('id')->where('name', 'Chicken')->first(),
            'quantity' => 2,
            'weight' => 75,
        ],
        [
            'ingredient_id' => Ingredient::query()->select('id')->where('name', 'Tomato')->first(), // Tomato
            'quantity' => 2,
            'weight' => 10,
        ],

    ]);
}
