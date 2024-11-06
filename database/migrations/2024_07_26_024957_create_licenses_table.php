<?php

// TODO [GM]: Sort Licenses

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
            $table->timestamps();
            $table->softDeletes();

            // Links
            $table->integer('manufacturer_id');				            // ID of Manufacturer
            $table->integer('category_id')->nullable();				    // ID of Category

            // License-specific fields
            $table->string('name')->nullable();
            $table->text('serial')->nullable();
            $table->integer('seats')->default(1);
            $table->text('notes')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('expiration_date')->nullable();
            $table->date('termination_date')->nullable();
            $table->boolean('maintained')->nullable();
            $table->boolean('reassignable')->default(true);
            $table->unsignedInteger('company_id')->nullable()->index();

            $table->integer('min_amt')->nullable();                         // To be removed
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
