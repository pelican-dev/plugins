<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('stripe_coupon_id')->nullable();
            $table->string('stripe_promotion_id')->nullable();
            $table->string('name');
            $table->string('code')->unique();
            $table->integer('amount_off')->nullable();
            $table->integer('percent_off')->nullable();
            $table->integer('max_redemptions')->nullable();
            $table->dateTime('redeem_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
