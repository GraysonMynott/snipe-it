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
        Schema::create('departments', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            
            $table->string('name');
            $table->string('phone', 20)->nullable();
            $table->integer('user_id');
            $table->integer('company_id')->nullable()->index();
            $table->integer('location_id')->nullable();
            $table->integer('manager_id')->nullable();
            $table->string('notes')->nullable();
            $table->string('image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
