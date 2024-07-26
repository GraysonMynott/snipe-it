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
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('format');
            $table->string('element');
            $table->timestamps();
            $table->integer('user_id')->nullable();
            $table->text('field_values')->nullable();
            $table->boolean('field_encrypted')->default(false);
            $table->string('db_column')->nullable();
            $table->text('help_text')->nullable();
            $table->boolean('show_in_email')->default(false);
            $table->boolean('show_in_requestable_list')->nullable()->default(false);
            $table->boolean('is_unique')->nullable()->default(false);
            $table->boolean('display_in_user_view')->nullable()->default(false);
            $table->boolean('auto_add_to_fieldsets')->nullable()->default(false);
            $table->boolean('show_in_listview')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_fields');
    }
};
