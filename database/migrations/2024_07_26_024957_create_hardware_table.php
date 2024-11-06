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
        Schema::create('hardware', function (Blueprint $table) {
            // Default entity fields
            $table->increments('id');                                   // Entity ID
            $table->timestamp('created_at')->nullable()->index();       // Created date
            $table->timestamp('updated_at')->nullable();                // Updated date
            $table->softDeletes();                                      // Soft delete fields
            
            // Links
            $table->integer('model_id')->unique();				        // ID of Model
            $table->integer('account_id')->nullable();				    // ID of Account    (e.g U/C, GreenLake, etc)
            $table->integer('support_id')->nullable();				    // ID of Support
            $table->integer('status_id')->nullable();				    // ID of Status

            // Hardware-specific fields
            $table->string('mac_address')->nullable()->unique();		// MAC address
            $table->string('serial')->nullable()->unique();		        // Serial number
            $table->text('notes')->nullable();                          // Notes
            $table->date('next_patch_date')->nullable();                // Date of purchase
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hardware');
    }
};
