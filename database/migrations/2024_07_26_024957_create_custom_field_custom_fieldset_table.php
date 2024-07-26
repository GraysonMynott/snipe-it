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
        Schema::create('custom_field_custom_fieldset', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('custom_field_id');
            $table->integer('custom_fieldset_id');
            $table->integer('order');
            $table->boolean('required');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_field_custom_fieldset');
    }
};
