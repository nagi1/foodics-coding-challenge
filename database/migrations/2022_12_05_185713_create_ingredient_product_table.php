<?php

use App\Enums\IngredientUnit;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ingredient_product', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Ingredient::class);
            $table->foreignIdFor(\App\Models\Product::class);
            $table->integer('weight')->default(1);
            $table->integer('quantity')->default(1);
            $table->string('unit')->default(IngredientUnit::Grams->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ingredient_product');
    }
};
