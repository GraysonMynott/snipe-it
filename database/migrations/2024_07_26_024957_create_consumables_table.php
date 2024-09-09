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
        Schema::create('consumables', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->integer('category_id')->nullable();
            $table->integer('location_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('qty')->default(0);
            $table->boolean('requestable')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 20)->nullable();
            $table->string('order_number')->nullable();
            $table->unsignedInteger('company_id')->nullable()->index();
            $table->integer('min_amt')->nullable();
            $table->string('model_number')->nullable();
            $table->integer('manufacturer_id')->nullable();
            $table->string('item_no')->nullable();
            $table->string('image')->nullable();
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumables');
    }
};
