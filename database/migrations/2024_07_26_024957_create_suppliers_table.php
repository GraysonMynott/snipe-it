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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('address', 250)->nullable();
            $table->string('address2', 250)->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country', 2)->nullable();
            $table->string('phone', 35)->nullable();
            $table->string('fax', 35)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('contact', 100)->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->integer('user_id')->nullable();
            $table->softDeletes();
            $table->string('zip', 10)->nullable();
            $table->string('url', 250)->nullable();
            $table->string('image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
