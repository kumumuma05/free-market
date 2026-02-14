<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete()->unique();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->tinyInteger('payment_method')->default(1);
            $table->string('shipping_postal', 10);
            $table->string('shipping_address');
            $table->string('shipping_building')->nullable();
            $table->string('status')->default('trading');
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('buyer_last_read_at')->nullable();
            $table->timestamp('seller_last_read_at')->nullable();
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
        Schema::dropIfExists('purchases');
    }
}
