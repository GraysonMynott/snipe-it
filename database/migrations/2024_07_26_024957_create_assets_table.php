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
            # Default entity fields
            $table->increments('id');                                   // Entity ID
            $table->timestamp('created_at')->nullable()->index();       // Created date
            $table->timestamp('updated_at')->nullable();                // Updated date
            $table->softDeletes();                                      // Soft delete fields
            
            // Asset-specific fields
            $table->string('name')->nullable();					        // Asset name
            $table->text('notes')->nullable();                          // Asset notes/comment
            $table->string('mac_address')->nullable()->unique();		// Asset MAC address
	        $table->string('serial')->nullable()->unique();			    // Asset serial
            $table->string('ip_address')->nullable()->unique();			// Asset IP
            $table->string('oob_ip')->nullable();			            // OoB management IP
            $table->text('image')->nullable();                          // Image of asset
            $table->boolean('physical')->default(true);                 // Physical/Virtual/Logical
            $table->boolean('archived')->nullable()->default(false);    // Is asset archived?
            $table->date('last_patch_date')->nullable();			    // Date of last patch
            $table->date('next_patch_date')->nullable();                // Date of next patch
            $table->string('asset_tag')->nullable();				    // Asset tag? Remove?

            // Links
            $table->unsignedInteger('company_id')->nullable()->index(); // ID of Company
            $table->integer('model_id')->nullable();				    // ID of Model      [inherits manufacturer and category]
            $table->integer('location_id')->nullable();				    // ID of Location
            $table->string('firmware_id')->nullable()->index();			// ID of Firmware
            $table->integer('user_id')->nullable();				        // ID of User
            $table->integer('status_id')->nullable();				    // ID of Status

	        // Indexes
            $table->index(['assigned_type', 'assigned_to']);
            $table->index(['deleted_at', 'asset_tag']);
            $table->index(['deleted_at', 'assigned_type', 'assigned_to']);
            $table->index(['deleted_at', 'location_id']);
            $table->index(['deleted_at', 'model_id']);
            $table->index(['deleted_at', 'name']);
            $table->index(['deleted_at', 'rtd_location_id']);
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
