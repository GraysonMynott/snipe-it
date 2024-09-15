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

            $table->integer('manufacturer_id')->nullable();
            $table->string('model_number')->nullable();
            $table->string('name');
            $table->integer('category_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('eol')->nullable();
            $table->integer('eos')->nullable();
            $table->string('image')->nullable();
            $table->text('notes')->nullable();
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
