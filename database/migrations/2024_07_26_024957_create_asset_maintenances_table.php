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
        Schema::create('asset_maintenances', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('asset_id');
            $table->string('asset_maintenance_type');
            $table->string('title', 100);
            $table->boolean('is_warranty');
            $table->date('start_date');
            $table->date('completion_date')->nullable();
            $table->integer('asset_maintenance_time')->nullable();
            $table->longText('notes')->nullable();
            $table->decimal('cost', 20)->nullable();
            $table->dateTime('deleted_at')->nullable();
            $table->timestamps();
            $table->integer('user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_maintenances');
    }
};
