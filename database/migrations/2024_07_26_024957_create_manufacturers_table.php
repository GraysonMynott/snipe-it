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
        Schema::create('manufacturers', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            // Manufacturer-specific fields
            $table->string('name');                                 // Manufacturer name
            $table->text('notes');                                  // Notes
            $table->string('url')->nullable();                      // Manufacturer URL
            $table->string('support_url')->nullable();              // Manufacturer support URL

            // Optional
            $table->string('image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacturers');
    }
};
