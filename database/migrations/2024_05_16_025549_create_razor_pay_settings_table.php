<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('razor_pay_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('status');
            $table->string('country_name');
            $table->string('currency_name');
            $table->double('currency_rate');
            $table->text('razorpay_key');
            $table->text('razorpay_secret_key');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('razor_pay_settings');
    }
};
