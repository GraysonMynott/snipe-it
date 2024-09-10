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
        Schema::create('licenses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->text('serial')->nullable();
            $table->integer('seats')->default(1);
            $table->text('notes')->nullable();
            $table->integer('user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->date('purchase_date')->nullable();
            $table->date('expiration_date')->nullable();
            $table->date('termination_date')->nullable();
            $table->boolean('maintained')->nullable();
            $table->boolean('reassignable')->default(true);
            $table->unsignedInteger('company_id')->nullable()->index();
//            $table->unsignedInteger('usercenter_id')->nullable()->index();        // To be added
            $table->integer('manufacturer_id')->nullable();
            $table->integer('category_id')->nullable();

            $table->integer('min_amt')->nullable();                         // To be removed
            $table->decimal('purchase_cost', 20)->nullable();               // To be removed
            $table->string('order_number', 50)->nullable();                 // To be removed
            $table->integer('depreciation_id')->nullable();                 // To be removed
            $table->string('license_name', 120)->nullable();                // To be removed
            $table->string('license_email')->nullable();                    // To be removed
            $table->string('purchase_order')->nullable();                   // To be removed
            $table->boolean('depreciate')->nullable();                      // To be removed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};
