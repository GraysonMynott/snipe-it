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
        Schema::create('asset_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            
            $table->integer('user_id')->nullable();
            $table->string('action_type');
            $table->integer('asset_id');
            $table->integer('checkedout_to')->nullable();
            $table->integer('location_id')->nullable();
            $table->string('asset_type', 100)->nullable();
            $table->text('note')->nullable();
            $table->text('filename')->nullable();
            $table->dateTime('requested_at')->nullable();
            $table->dateTime('accepted_at')->nullable();
            $table->integer('accessory_id')->nullable();
            $table->integer('accepted_id')->nullable();
            $table->integer('consumable_id')->nullable();
            $table->date('expected_checkin')->nullable();
            $table->integer('component_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_logs');
    }
};
