<?php

use App\Actions\CalculateUsedStockAction;
use App\Events\OrderCreated;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use App\Notifications\StockBellowThresholdNotification;

use function Pest\Laravel\expectsNotification;

uses(RefreshDatabase::class);

function createPayload(array $products): array
{
    return [
        'products' => $products,
    ];
}

test('it returns validation error 422 when cant make the order', function () {
    /**
     * @var TestCase $this
     */
    Ingredient::factory()->makeBeef(20_000)->create();
    Ingredient::factory()->makeCheese(5000)->create();
    Ingredient::factory()->makeOnion(1000)->create();

    $burger = makeBurger();

    $products = [
        [
            'product_id' => $burger->id,
            'quantity' => 100,
        ],
    ];

    $payload = createPayload($products);

    /**
     * @var User $user
     */
    $user = User::factory()->createOne();

    $this->actingAs($user, 'sanctum');

    $response = $this->postJson('/api/orders', $payload);

    expect($response->getStatusCode())->toBe(422);
    expect(Order::count())->toBe(0);
});

test('it returns 201 and order correct response', function () {
    /**
     * @var TestCase $this
     */
    Ingredient::factory()->makeBeef(20_000)->create();
    Ingredient::factory()->makeCheese(5000)->create();
    Ingredient::factory()->makeOnion(1000)->create();

    $burger = makeBurger();

    $products = [
        [
            'product_id' => $burger->id,
            'quantity' => 1,
        ],
    ];

    $payload = createPayload($products);

    /**
     * @var User $user
     */
    $user = User::factory()->createOne();

    $this->actingAs($user, 'sanctum');

    $response = $this->postJson('/api/orders', $payload);

    expect($response->getStatusCode())->toBe(201);
    expect($response->json())->toBeArray()->toBe([
                "message" => "Order created successfully",
                "data" =>  [
                    "order" =>  [
                    "id" => 1,
                    "total_price" => 35.99
                    ]
                ]
        ]);
});

test('it retruns 422 when one of the ordered item cant be made due to stock', function () {
    /**
     * @var TestCase $this
     */
    Ingredient::factory()->makeBeef(20_000)->create();
    Ingredient::factory()->makeCheese(5000)->create();
    Ingredient::factory()->makeOnion(1000)->create();
    Ingredient::factory()->makeChicken(500)->create();
    Ingredient::factory()->makeTomato(1000)->create();

    $burger = makeBurger();

    $chicken = makeChickenSandwitch();

    $products = [
        [
            'product_id' => $burger->id,
            'quantity' => 3,
        ],
        [
            'product_id' => $chicken->id,
            'quantity' => 4,
        ],
    ];

    $payload = createPayload($products);

    /**
     * @var User $user
     */
    $user = User::factory()->createOne();

    $this->actingAs($user, 'sanctum');

    $response = $this->postJson('/api/orders', $payload);

    expect($response->getStatusCode())->toBe(422);
    expect(Order::count())->toBe(0);
});

test('it stores the order correctly in database', function () {
    /**
     * @var TestCase $this
     */
    Ingredient::factory()->makeBeef(20_000)->create();
    Ingredient::factory()->makeCheese(5000)->create();
    Ingredient::factory()->makeOnion(1000)->create();
    Ingredient::factory()->makeChicken(5000)->create();
    Ingredient::factory()->makeTomato(1000)->create();

    Event::fake();

    $burger = makeBurger();

    $chicken = makeChickenSandwitch();

    $products = [
        [
            'product_id' => $burger->id,
            'quantity' => 3,
        ],
        [
            'product_id' => $chicken->id,
            'quantity' => 4,
        ],
    ];

    $payload = createPayload($products);

    /**
     * @var User $user
     */
    $user = User::factory()->createOne();

    $this->actingAs($user, 'sanctum');

    $response = $this->postJson('/api/orders', $payload);

    expect($response->getStatusCode())->toBe(201);

    // Expect the order to be created
    expect(Order::count())->toBe(1);

    $order = Order::first();

    // Expect order to have 2 items
    expect($order->products->count())->toBe(2);

    // Expect order to have 3 burgers
    expect($order->products->first()->pivot->quantity)->toBe(3);

    // Expect order to have 4 chicken sandwitches
    expect($order->products->last()->pivot->quantity)->toBe(4);

    // Expect order status to be pending
    expect($order->status)->toBe('pending');

    // Expect order total price to be correct
    $total = $burger->price * 3 + $chicken->price * 4;
    expect($order->total_price)->toBe($total);

    // Expect order to be associated with the user
    expect($order->user_id)->toBe($user->id);

    // Expect the event to be fired
    Event::assertDispatched(OrderCreated::class);
});

test('it updates the stock correctly after the order', function () {
    /**
     * @var TestCase $this
     */
    $beef = Ingredient::factory()->makeBeef(20_000)->create();
    $cheese = Ingredient::factory()->makeCheese(5000)->create();
    $onion = Ingredient::factory()->makeOnion(1000)->create();
    $chicken = Ingredient::factory()->makeChicken(5000)->create();
    $tomato = Ingredient::factory()->makeTomato(1000)->create();

    $burger = makeBurger();

    $chickenSandwitch = makeChickenSandwitch();

    $products = [
        [
            'product_id' => $burger->id,
            'quantity' => 3,
        ],
        [
            'product_id' => $chickenSandwitch->id,
            'quantity' => 4,
        ],
    ];

    $payload = createPayload($products);

    /**
     * @var User $user
     */
    $user = User::factory()->createOne();

    $this->actingAs($user, 'sanctum');

    $response = $this->postJson('/api/orders', $payload);

    expect($response->getStatusCode())->toBe(201);

    // Expect the stock to be updated
    $chickenInChickenSandwitch = $chickenSandwitch->ingredients()->where('ingredient_id', $chicken->id)->first()->pivot;
    $beefInBurger = $burger->ingredients()->where('ingredient_id', $beef->id)->first()->pivot;

    $usedChicken = app(CalculateUsedStockAction::class)->execute($chickenInChickenSandwitch, 4);
    $usedBeef = app(CalculateUsedStockAction::class)->execute($beefInBurger, 3);

    expect($beef->fresh()->stock)->toBe(20_000 - $usedBeef);
    expect($chicken->fresh()->stock)->toBe(5000 - $usedChicken);
});


test('it sends alearts admins when one of the ingredients goes bellow 50%', function () {
    /**
     * @var TestCase $this
     */

    Notification::fake();

    $beef = Ingredient::factory()->makeBeef(20_000)->create();
    $cheese = Ingredient::factory()->makeCheese(3000)->create();
    $onion = Ingredient::factory()->makeOnion(1000)->create();

    $burger = makeBurger();


    $products = [
        [
            'product_id' => $burger->id,
            'quantity' => 30,
        ],
    ];

    $payload = createPayload($products);

    /**
     * @var User $user
     */
    $user = User::factory()->createOne();

    /**
     * @var User $user
     */
    $admin = User::factory()->admin()->createOne();

    $this->actingAs($user, 'sanctum');

    $response = $this->postJson('/api/orders', $payload);

    expect($response->getStatusCode())->toBe(201);

    // Expect to notification to be sent to the admin
    Notification::assertSentTo(
        [$admin],
        StockBellowThresholdNotification::class
    );

    Notification::assertCount(1);


    expect($onion->fresh()->isNotified())->toBeTrue();
});

test('it sends multiple alearts to admins when multiple ingredients goes bellow 50%', function () {
    /**
     * @var TestCase $this
     */

    Notification::fake();

    $beef = Ingredient::factory()->makeBeef(20_000)->create();
    $cheese = Ingredient::factory()->makeCheese(1500)->create();
    $onion = Ingredient::factory()->makeOnion(1000)->create();

    $burger = makeBurger();


    $products = [
        [
            'product_id' => $burger->id,
            'quantity' => 30,
        ],
    ];

    $payload = createPayload($products);

    /**
     * @var User $user
     */
    $user = User::factory()->createOne();

    /**
     * @var User $user
     */
    $admin = User::factory()->admin()->createOne();

    $this->actingAs($user, 'sanctum');

    $response = $this->postJson('/api/orders', $payload);

    expect($response->getStatusCode())->toBe(201);

    // Expect to notification to be sent to the admin
    Notification::assertSentTo(
        [$admin],
        StockBellowThresholdNotification::class
    );

    Notification::assertCount(2);

    expect($onion->fresh()->isNotified())->toBeTrue();
    expect($cheese->fresh()->isNotified())->toBeTrue();
});

test('it sends only one email to admin when ingredients goes bellow 50% across multiple orders', function () {
    /**
     * @var TestCase $this
     */

    Notification::fake();

    $beef = Ingredient::factory()->makeBeef(20_000)->create();
    $cheese = Ingredient::factory()->makeCheese(3000)->create();
    $onion = Ingredient::factory()->makeOnion(1000)->create();

    $burger = makeBurger();


    $products1 = [
      [
            'product_id' => $burger->id,
            'quantity' => 30,
        ],
    ];
    $products2 = [
      [
            'product_id' => $burger->id,
            'quantity' => 1,
        ],
    ];

    $payload1 = createPayload($products1);
    $payload2 = createPayload($products2);

    /**
     * @var User $user
     */
    $user = User::factory()->createOne();

    /**
     * @var User $user
     */
    $admin = User::factory()->admin()->createOne();

    $this->actingAs($user, 'sanctum');

    $response1 = $this->postJson('/api/orders', $payload1);
    $response2 = $this->postJson('/api/orders', $payload2);
    $response3 = $this->postJson('/api/orders', $payload2);

    expect($response1->getStatusCode())->toBe(201);
    expect($response2->getStatusCode())->toBe(201);
    expect($response3->getStatusCode())->toBe(201);

    // Expect to notification to be sent to the admin
    Notification::assertSentTo(
        [$admin],
        StockBellowThresholdNotification::class
    );

    Notification::assertCount(1);


    expect($onion->fresh()->isNotified())->toBeTrue();
});
