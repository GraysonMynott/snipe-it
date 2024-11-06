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
        Schema::create('models', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            // Links
            $table->integer('manufacturer_id');				            // ID of Manufacturer
            $table->integer('category_id')->nullable();				    // ID of Category

            // Model-specific fields
            $table->string('name');					                    // Name
            $table->text('notes')->nullable();                          // Notes
            $table->string('model_number')->nullable();			        // Model number/SKU
            $table->date('eol')->nullable();				            // End of life date
            
            //Optional
            $table->string('image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('models');
    }
};
