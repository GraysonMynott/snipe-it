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
            
            $table->string('name')->nullable();
            $table->string('asset_tag')->nullable();
            $table->integer('model_id')->nullable();
            $table->string('serial')->nullable()->index();
            $table->date('purchase_date')->nullable();
            $table->date('asset_eol_date')->nullable();
            $table->boolean('eol_explicit')->default(false);
            $table->decimal('purchase_cost', 20)->nullable();
            $table->string('order_number')->nullable();
            $table->integer('assigned_to')->nullable();
            $table->text('notes')->nullable();
            $table->text('image')->nullable();
            $table->integer('user_id')->nullable();
            $table->boolean('physical')->default(true);
            $table->integer('status_id')->nullable();
            $table->boolean('archived')->nullable()->default(false);
            $table->integer('warranty_months')->nullable();
            $table->boolean('depreciate')->nullable();
            $table->tinyInteger('requestable')->default(0);
            $table->integer('rtd_location_id')->nullable()->index();
            $table->string('_snipeit_mac_address_1')->nullable();
            $table->string('accepted')->nullable();
            $table->dateTime('last_checkout')->nullable();
            $table->dateTime('last_checkin')->nullable();
            $table->date('expected_checkin')->nullable();
            $table->unsignedInteger('company_id')->nullable()->index();
            $table->string('assigned_type')->nullable();
            $table->dateTime('last_audit_date')->nullable();
            $table->date('next_audit_date')->nullable();
            $table->integer('location_id')->nullable();
            $table->integer('checkin_counter')->default(0);
            $table->integer('checkout_counter')->default(0);
            $table->integer('requests_counter')->default(0);
            $table->boolean('byod')->nullable()->default(false);

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
