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

            $table->string('name');					// Name
            $table->string('model_number')->nullable();			// Model number/SKU
            $table->integer('user_id')->nullable();			// User ID of creator??
            $table->integer('eol')->nullable();				// End of life date
            $table->integer('eos')->nullable();				// End of support date
            $table->string('image')->nullable();
	    $table->text('notes')->nullable();

	    // Links
            $table->integer('manufacturer_id')->nullable();		// Manufacturer ID
            $table->integer('category_id')->nullable();			// Category ID
	    
	    $table->tinyInteger('requestable')->default(0);                 // To be removed
            $table->integer('min_amt')->nullable();                         // To be removed
            $table->integer('fieldset_id')->nullable();                     // To be removed?
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
