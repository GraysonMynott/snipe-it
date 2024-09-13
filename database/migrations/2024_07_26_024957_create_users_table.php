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
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();
            
            $table->string('email')->nullable();
            $table->string('password');
            $table->text('permissions')->nullable();
            $table->boolean('activated')->default(false);
            $table->integer('created_by')->nullable();
            $table->string('activation_code')->nullable()->index();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->string('persist_code')->nullable();
            $table->string('reset_password_code')->nullable()->index();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('website')->nullable();
            $table->string('country')->nullable();
            $table->string('gravatar')->nullable();
            $table->integer('location_id')->nullable();
            $table->string('phone')->nullable();
            $table->string('jobtitle')->nullable();
            $table->integer('manager_id')->nullable();
            $table->text('employee_num')->nullable();
            $table->string('avatar')->nullable();
            $table->string('username')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedInteger('company_id')->nullable()->index();
            $table->text('remember_token')->nullable();
            $table->boolean('ldap_import')->default(false);
            $table->string('locale', 10)->nullable()->default('en-US');
            $table->boolean('show_in_list')->default(true);
            $table->string('two_factor_secret', 32)->nullable();
            $table->boolean('two_factor_enrolled')->default(false);
            $table->boolean('two_factor_optin')->default(false);
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip', 10)->nullable();
            $table->string('skin')->nullable();
            $table->boolean('remote')->nullable()->default(false);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('scim_externalid')->nullable();
            $table->boolean('autoassign_licenses')->default(true);
            $table->boolean('vip')->nullable()->default(false);

            $table->index(['username', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
