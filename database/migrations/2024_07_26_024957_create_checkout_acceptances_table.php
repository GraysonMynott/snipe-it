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
        Schema::create('checkout_acceptances', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->string('checkoutable_type');
            $table->unsignedBigInteger('checkoutable_id');
            $table->integer('assigned_to_id')->nullable();
            $table->string('signature_filename')->nullable();
            $table->text('note')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('declined_at')->nullable();
            $table->text('stored_eula')->nullable();
            $table->string('stored_eula_file')->nullable();

            $table->index(['checkoutable_type', 'checkoutable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkout_acceptances');
    }
};
