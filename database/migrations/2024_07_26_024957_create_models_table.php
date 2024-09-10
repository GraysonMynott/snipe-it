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
            $table->integer('manufacturer_id')->nullable();
            $table->string('model_number')->nullable();
            $table->string('name');
            $table->integer('category_id')->nullable();
            $table->integer('min_amt')->nullable();                         // To be removed
            $table->timestamps();
            $table->integer('depreciation_id')->nullable();                 // TO be removed
            $table->integer('user_id')->nullable();
            $table->integer('eol')->nullable();
            $table->string('image')->nullable();
            $table->boolean('deprecated_mac_address')->default(false);      // To be removed
            $table->softDeletes();
            $table->integer('fieldset_id')->nullable();                     // To be removed?
            $table->text('notes')->nullable();
            $table->tinyInteger('requestable')->default(0);                 // To be removed
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
