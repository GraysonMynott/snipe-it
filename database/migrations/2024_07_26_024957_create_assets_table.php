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
            $table->increments('id');
            $table->timestamp('created_at')->nullable()->index();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
            
            $table->string('name')->nullable();					        // Name of asset
            $table->string('asset_tag')->nullable();				    // Asset tag? Remove?
	        $table->string('serial')->nullable()->index();			    // Asset serial
            $table->text('notes')->nullable();
            $table->text('image')->nullable();
            $table->boolean('physical')->default(true);
            $table->boolean('archived')->nullable()->default(false);
            $table->string('assigned_type')->nullable();
            $table->dateTime('last_patch_date')->nullable();			// TODO: Change to "date"
            $table->date('next_patch_date')->nullable();

            // Links
            $table->unsignedInteger('company_id')->nullable()->index();
            $table->integer('model_id')->nullable();				    // ID of model
            $table->integer('location_id')->nullable();				    // ID of location
            $table->string('firmware_id')->nullable()->index();			// ID of firmware
            $table->integer('user_id')->nullable();				        // ID of user
            $table->integer('status_id')->nullable();				    // ID of status
            $table->integer('rtd_location_id')->nullable()->index();	// ID of RTD location?


            // To delete
            $table->date('purchase_date')->nullable();
            $table->date('asset_eol_date')->nullable();
            $table->boolean('eol_explicit')->default(false);
            $table->integer('assigned_to')->nullable();

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
