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
        Schema::create('support', function (Blueprint $table) {
            // Default entity fields
            $table->increments('id');                                   // Entity ID
            $table->timestamp('created_at')->nullable()->index();       // Created date
            $table->timestamp('updated_at')->nullable();                // Updated date
            $table->softDeletes();                                      // Soft delete fields
            
            // Links
            $table->integer('account_id')->unique();				    // ID of Account

            // Support-specific fields
            $table->string('name')->nullable()->unique();		        // Identifier (name/contract ID/etc)
            $table->text('notes')->nullable();                          // Notes
            $table->date('expiry_date')->nullable();                    // Date of expiry
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support');
    }
};
