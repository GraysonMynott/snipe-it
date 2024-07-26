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
        Schema::create('components', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->integer('category_id')->nullable();
            $table->integer('location_id')->nullable();
            $table->integer('company_id')->nullable()->index();
            $table->integer('user_id')->nullable();
            $table->integer('supplier_id')->nullable();
            $table->integer('qty')->default(1);
            $table->string('order_number')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 20)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->integer('min_amt')->nullable();
            $table->string('serial')->nullable();
            $table->string('image')->nullable();
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('components');
    }
};
