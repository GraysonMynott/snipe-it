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
        Schema::create('checkout_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('requestable_id');
            $table->string('requestable_type');
            $table->integer('quantity')->default(1);
            $table->timestamps();
            $table->dateTime('canceled_at')->nullable();
            $table->dateTime('fulfilled_at')->nullable();
            $table->dateTime('deleted_at')->nullable();

            $table->index(['user_id', 'requestable_id', 'requestable_type'], 'checkout_requests_user_id_requestable_id_requestable_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkout_requests');
    }
};
