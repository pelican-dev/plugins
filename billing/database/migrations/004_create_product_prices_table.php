<?php

use Boy132\Billing\Enums\PriceInterval;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_prices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('stripe_id')->nullable();
            $table->string('name');
            $table->float('cost', 2);
            $table->string('interval_type')->default(PriceInterval::Month);
            $table->unsignedInteger('interval_value')->default(1);

            $table->unsignedInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_prices');
    }
};
