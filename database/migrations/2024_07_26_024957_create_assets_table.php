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
        Schema::create('assets', function (Blueprint $table) {
            // Default entity fields
            $table->increments('id');                                   // Entity ID
            $table->timestamp('created_at')->nullable()->index();       // Created date
            $table->timestamp('updated_at')->nullable();                // Updated date
            $table->softDeletes();                                      // Soft delete fields
            
            // Links
            $table->integer('hardware_id')->unique();				    // ID of Hardware
            $table->integer('license_id')->nullable();				    // ID of License
            $table->integer('location_id')->nullable();				    // ID of Location
            $table->integer('firmware_id')->nullable();				    // ID of Firmware
            $table->integer('status_id')->nullable();				    // ID of Status
            $table->integer('parent_id')->nullable();				    // ID of Parent (if stack)

            // Asset-specific fields
            $table->string('name')->nullable()->unique();				// Name of asset
            $table->text('notes')->nullable();                          // Asset notes/comment
            $table->string('pvl')->default('Physical');                 // Are we physical/virtual/logical
            $table->string('ip_address')->nullable()->unique();         // Management IP
            $table->string('oob_ip')->nullable();                       // OoB management IP
            $table->date('last_patch_date')->nullable();                // Date of last patch
            $table->date('next_patch_date')->nullable();                // Date of next patch
            
            // Optional
            $table->text('image')->nullable();                          // Image of asset

	        // Indexes
            $table->index(['deleted_at', 'location_id']);
            $table->index(['deleted_at', 'model_id']);
            $table->index(['deleted_at', 'name']);
	        $table->index(['deleted_at', 'status_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
