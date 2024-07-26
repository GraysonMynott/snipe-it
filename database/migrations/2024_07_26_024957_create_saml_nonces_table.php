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
        Schema::create('saml_nonces', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nonce')->index();
            $table->dateTime('not_valid_after')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saml_nonces');
    }
};
