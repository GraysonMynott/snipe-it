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
        Schema::create('firmware', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            // Links
            $table->integer('manufacturer_id');				            // ID of Manufacturer
            $table->integer('category_id')->nullable();				    // ID of Category

            // Firmware-specific fields
            $table->string('name');                                     // Name
            $table->string('major_release');                            // Major release
            $table->string('minor_release')->nullable();    	        // Minor release
            $table->boolean('recommended')->default(false);             // Is this firmware recommended?
            $table->date('eol_date')->nullable();                       // Date of EoL
            $table->date('eos_date')->nullable();                       // Date of EoS
            $table->text('notes')->nullable();                          // Notes
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
