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
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('user_id')->nullable();
            $table->integer('per_page')->default(20);
            $table->string('site_name', 100)->default('Snipe IT Asset Management');
            $table->integer('qr_code')->nullable();
            $table->string('qr_text', 32)->nullable();
            $table->integer('display_asset_name')->nullable();
            $table->integer('display_checkout_date')->nullable();
            $table->integer('display_eol')->nullable();
            $table->integer('auto_increment_assets')->default(0);
            $table->string('auto_increment_prefix')->nullable();
            $table->boolean('load_remote')->default(true);
            $table->string('logo')->nullable();
            $table->string('header_color')->nullable();
            $table->string('alert_email')->nullable();
            $table->boolean('alerts_enabled')->default(true);
            $table->longText('default_eula_text')->nullable();
            $table->string('barcode_type')->nullable()->default('QRCODE');
            $table->text('webhook_endpoint')->nullable();
            $table->string('webhook_channel')->nullable();
            $table->string('webhook_botname')->nullable();
            $table->string('webhook_selected')->nullable()->default('slack');
            $table->string('default_currency', 10)->nullable();
            $table->text('custom_css')->nullable();
            $table->tinyInteger('brand')->default(1);
            $table->string('ldap_enabled')->nullable();
            $table->string('ldap_server')->nullable();
            $table->string('ldap_uname')->nullable();
            $table->longText('ldap_pword')->nullable();
            $table->string('ldap_basedn')->nullable();
            $table->string('ldap_default_group')->nullable();
            $table->text('ldap_filter')->nullable();
            $table->string('ldap_username_field')->nullable()->default('samaccountname');
            $table->string('ldap_lname_field')->nullable()->default('sn');
            $table->string('ldap_fname_field')->nullable()->default('givenname');
            $table->string('ldap_auth_filter_query')->nullable()->default('uid=');
            $table->integer('ldap_version')->nullable()->default(3);
            $table->string('ldap_active_flag')->nullable();
            $table->string('ldap_dept')->nullable();
            $table->string('ldap_emp_num')->nullable();
            $table->string('ldap_email')->nullable();
            $table->string('ldap_phone_field')->nullable();
            $table->string('ldap_jobtitle')->nullable();
            $table->string('ldap_manager')->nullable();
            $table->string('ldap_country')->nullable();
            $table->string('ldap_location')->nullable();
            $table->boolean('full_multiple_companies_support')->default(false);
            $table->boolean('ldap_server_cert_ignore')->default(false);
            $table->string('locale', 10)->nullable()->default('en-US');
            $table->tinyInteger('labels_per_page')->default(30);
            $table->decimal('labels_width', 6, 5)->default(2.625);
            $table->decimal('labels_height', 6, 5)->default(1);
            $table->decimal('labels_pmargin_left', 6, 5)->default(0.21975);
            $table->decimal('labels_pmargin_right', 6, 5)->default(0.21975);
            $table->decimal('labels_pmargin_top', 6, 5)->default(0.5);
            $table->decimal('labels_pmargin_bottom', 6, 5)->default(0.5);
            $table->decimal('labels_display_bgutter', 6, 5)->default(0.07);
            $table->decimal('labels_display_sgutter', 6, 5)->default(0.05);
            $table->tinyInteger('labels_fontsize')->default(9);
            $table->decimal('labels_pagewidth', 7, 5)->default(8.5);
            $table->decimal('labels_pageheight', 7, 5)->default(11);
            $table->tinyInteger('labels_display_name')->default(0);
            $table->tinyInteger('labels_display_serial')->default(1);
            $table->tinyInteger('labels_display_tag')->default(1);
            $table->string('alt_barcode')->nullable()->default('C128');
            $table->boolean('alt_barcode_enabled')->nullable()->default(true);
            $table->integer('alert_interval')->nullable()->default(30);
            $table->integer('alert_threshold')->nullable()->default(5);
            $table->string('name_display_format', 10)->nullable()->default('first_last');
            $table->string('email_domain')->nullable();
            $table->string('email_format')->nullable()->default('filastname');
            $table->string('username_format')->nullable()->default('filastname');
            $table->boolean('is_ad')->default(false);
            $table->string('ad_domain')->nullable();
            $table->string('ldap_port', 5)->default('389');
            $table->boolean('ldap_tls')->default(false);
            $table->integer('zerofill_count')->default(5);
            $table->boolean('ldap_pw_sync')->default(true);
            $table->tinyInteger('two_factor_enabled')->nullable();
            $table->boolean('require_accept_signature')->default(false);
            $table->string('date_display_format')->default('Y-m-d');
            $table->string('time_display_format')->default('h:i A');
            $table->bigInteger('next_auto_tag_base')->default(1);
            $table->text('login_note')->nullable();
            $table->integer('thumbnail_max_h')->nullable()->default(50);
            $table->boolean('pwd_secure_uncommon')->default(false);
            $table->string('pwd_secure_complexity')->nullable();
            $table->integer('pwd_secure_min')->default(8);
            $table->integer('audit_interval')->nullable();
            $table->integer('audit_warning_days')->nullable();
            $table->boolean('show_url_in_emails')->default(false);
            $table->string('custom_forgot_pass_url')->nullable();
            $table->boolean('show_alerts_in_menu')->default(true);
            $table->boolean('labels_display_company_name')->default(false);
            $table->boolean('show_archived_in_list')->default(false);
            $table->text('dashboard_message')->nullable();
            $table->char('support_footer', 5)->nullable()->default('on');
            $table->text('footer_text')->nullable();
            $table->char('modellist_displays')->nullable()->default('image,category,manufacturer,model_number');
            $table->boolean('login_remote_user_enabled')->default(false);
            $table->boolean('login_common_disabled')->default(false);
            $table->string('login_remote_user_custom_logout_url')->default('');
            $table->char('skin')->nullable();
            $table->boolean('show_images_in_email')->default(true);
            $table->char('admin_cc_email')->nullable();
            $table->boolean('labels_display_model')->default(false);
            $table->char('privacy_policy_link')->nullable();
            $table->char('version_footer', 5)->nullable()->default('on');
            $table->boolean('unique_serial')->default(false);
            $table->boolean('logo_print_assets')->default(false);
            $table->char('depreciation_method', 10)->nullable()->default('default');
            $table->char('favicon')->nullable();
            $table->string('default_avatar')->nullable()->default('default.png');
            $table->char('email_logo')->nullable();
            $table->char('label_logo')->nullable();
            $table->boolean('allow_user_skin')->default(false);
            $table->boolean('show_assigned_assets')->default(false);
            $table->string('login_remote_user_header_name')->default('');
            $table->boolean('ad_append_domain')->default(false);
            $table->boolean('saml_enabled')->default(false);
            $table->mediumText('saml_idp_metadata')->nullable();
            $table->string('saml_attr_mapping_username')->nullable();
            $table->boolean('saml_forcelogin')->default(false);
            $table->boolean('saml_slo')->default(false);
            $table->text('saml_sp_x509cert')->nullable();
            $table->text('saml_sp_privatekey')->nullable();
            $table->text('saml_custom_settings')->nullable();
            $table->text('saml_sp_x509certNew')->nullable();
            $table->char('digit_separator')->nullable()->default('1,234.56');
            $table->text('ldap_client_tls_cert')->nullable();
            $table->text('ldap_client_tls_key')->nullable();
            $table->string('dash_chart_type')->nullable()->default('name');
            $table->boolean('label2_enable')->default(false);
            $table->string('label2_template')->nullable()->default('DefaultLabel');
            $table->string('label2_title')->nullable();
            $table->boolean('label2_asset_logo')->default(false);
            $table->string('label2_1d_type')->default('default');
            $table->string('label2_2d_type')->default('default');
            $table->string('label2_2d_target')->default('hardware_id');
            $table->string('label2_fields')->default('name=name;serial=serial;model=model.name;');
            $table->boolean('google_login')->nullable()->default(false);
            $table->string('google_client_id')->nullable();
            $table->string('google_client_secret')->nullable();
            $table->boolean('profile_edit')->nullable()->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
