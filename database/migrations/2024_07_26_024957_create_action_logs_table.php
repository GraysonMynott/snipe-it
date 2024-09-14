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
        Schema::create('action_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('created_at')->nullable()->index();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
            
            $table->integer('user_id')->nullable();
            $table->string('action_type')->index();
            $table->integer('target_id')->nullable();
            $table->string('target_type')->nullable();
            $table->integer('location_id')->nullable();
            $table->text('note')->nullable();
            $table->text('filename')->nullable();
            $table->string('item_type');
            $table->integer('item_id');
            $table->integer('thread_id')->nullable()->index();
            $table->integer('company_id')->nullable()->index();
            $table->text('log_meta')->nullable();
            $table->dateTime('action_date')->nullable();
            $table->string('action_source')->nullable();
            $table->string('remote_ip', 45)->nullable()->index();
            $table->string('user_agent')->nullable();

            $table->index(['item_type', 'item_id', 'action_type']);
            $table->index(['target_type', 'target_id', 'action_type']);
            $table->index(['target_type', 'target_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_logs');
    }
};
