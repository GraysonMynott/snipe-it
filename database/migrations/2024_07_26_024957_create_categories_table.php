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
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->string('name');
            $table->integer('user_id')->nullable();
            $table->longText('eula_text')->nullable();
            $table->boolean('use_default_eula')->default(false);
            $table->boolean('require_acceptance')->default(false);
            $table->string('category_type')->nullable()->default('asset');
            $table->boolean('checkin_email')->default(false);
            $table->string('image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
