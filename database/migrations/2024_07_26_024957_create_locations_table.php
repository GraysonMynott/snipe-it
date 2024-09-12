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
        Schema::create('locations', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->string('name')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('address')->nullable();
            $table->string('address2')->nullable();
            $table->string('zip', 10)->nullable();
            $table->string('phone', 20)->nullable();
            $table->integer('parent_id')->nullable()->index();
            $table->string('currency', 10)->nullable();
            $table->string('ldap_ou')->nullable();
            $table->integer('manager_id')->nullable();
            $table->string('image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
