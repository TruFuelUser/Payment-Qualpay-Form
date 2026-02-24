<?php
if (!defined('ABSPATH')) exit;

class TrufPaymentAdmin {
    public function __construct() {
        // Hooks de administración
        add_action('admin_menu', [$this, 'register_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    /** Register Menu in Admin */
    public function register_menu() {
        add_menu_page(
            __('TruFuel Payment', 'trufuel-payment'),
            __('TruFuel Payment', 'trufuel-payment'),
            'manage_options',
            'trf-payment',
            [$this, 'renderSettingsPage'],
            'dashicons-money-alt'
        );
    }

    /** Register Options (site key & secret key) */
    public function register_settings() {
// ================ 
// RECAPTCHA REGISTER
//  ===============

        register_setting('trf_payment', 'trf_recaptcha_site_key', [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        ]);

        register_setting('trf_payment', 'trf_recaptcha_secret_key', [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
            'autoload'          => false,
        ]);

// ================ 
// EMAIL REGISTER
//  ===============

        register_setting('trf_payment', 'trf_confirm_mail', [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
            'autoload'          => false,
        ]);

// ================ 
// QUALPAY REGISTER
//  ===============

        register_setting('trf_payment', 'trf_merchant_id', [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
            'autoload'          => false,
        ]);

        register_setting('trf_payment', 'trf_qualpay_api_key', [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
            'autoload'          => false,
        ]);

// =================== 
// RECAPTCHA SECTION
//  ==================

        add_settings_section(
            'trf_contact_main',
            __('reCAPTCHA configuration', 'trufuel-payment'),
            '__return_false',
            'trf_payment'
        );

// =================== 
// EMAIL SECTION
//  ==================

        add_settings_section(
            'trf_contact_email',
            __('Email Send Configuration', 'trufuel-payment'),
            '__return_false',
            'trf_payment'
        );

// =================== 
// QUALPAY SECTION
//  ==================

         add_settings_section(
            'trf_qualpay',
            __('QualPay Configuration', 'trufuel-payment'),
            '__return_false',
            'trf_payment'
        );

// =================== 
//  ADD FIELDS QUALPAY
//  ==================

        add_settings_field(
            'trf_merchantID',
            __('Merchant ID', 'trufuel-payment'),
            [$this, 'fieldMerchantID'],
            'trf_payment',
            'trf_qualpay'
        );

         add_settings_field(
            'trf_apiKey',
            __('Api Key Qualpay', 'trufuel-payment'),
            [$this, 'fieldApiKey'],
            'trf_payment',
            'trf_qualpay'
        );

// ====================== 
//  ADD FIELDS RECAPTCHA
// =====================

        add_settings_field(
            'trf_recaptcha_site_key',
            __('Site Key (public)', 'trufuel-payment'),
            [$this, 'fieldSiteKey'],
            'trf_payment',
            'trf_contact_main'
        );

        add_settings_field(
            'trf_recaptcha_secret_key',
            __('Secret Key (private)', 'trufuel-payment'),
            [$this, 'fieldSecretKey'],
            'trf_payment',
            'trf_contact_main'
        );

// =================== 
//  ADD FIELDS EMAIL
//  ==================

         add_settings_field(
            'trf_confirm_mail',
            __('Confirm Payment Reception Email', 'trufuel-payment'),
            [$this, 'fieldEmail'],
            'trf_payment',
            'trf_contact_email'
        );

        // text field
        add_settings_field(
            'trf_keys_json_path',
            __('Ruta de keys.json', 'trufuel-payment'),
            function () {
                $v = get_option('trf_keys_json_path', '');
                echo '<input type="text" class="regular-text code" name="trf_keys_json_path"';
                echo ' value="' . esc_attr($v) . '" placeholder="/var/secure/trufuel/keys.json" />';
                echo '<p class="description">' . esc_html__('e.g: /var/secure/trufuel/keys.json (Linux) o C:\secure\trufuel\keys.json (Windows)', 'trufuel-payment') . '</p>';
            },
            'trf_payment',
            'trf_contact_keys'
        );


    }

    /** Renders site Keys (ReCaptcha) input */
    public function fieldSiteKey() {
        $value = get_option('trf_recaptcha_site_key', '');
        echo '<input type="text" class="regular-text" name="trf_recaptcha_site_key" value="' . esc_attr($value) . '">';
    }

    /** Render Site Key (ReCaptcha) Input */
    public function fieldSecretKey() {
        $value = get_option('trf_recaptcha_secret_key', '');
        echo '<input type="password" class="regular-text" name="trf_recaptcha_secret_key" value="' . esc_attr($value) . '">';
        echo '<p class="description">' . esc_html__("It's saved in the database. It's recommended to use wp-config.php for maximum security..", 'trufuel-payment') . '</p>';
    }

    /** Render Email Input */
    public function fieldEmail() {
        $value = get_option('trf_confirm_mail', '');
        echo '<input type="text" class="regular-text" name="trf_confirm_mail" value="' . esc_attr($value) . '">';
        echo '<p class="description">' . esc_html__("Your email address will be displayed here. For example: youremail@example.com.") ."<br>" . esc_html__("To use multiple email addresses, separate them with a semicolon (;).", 'trufuel-payment') . '</p>';
    }

    /** Render Merchant id Input */
    public function fieldMerchantID() {
        $value = get_option('trf_merchant_id', '');
        echo '<input type="text" class="regular-text" name="trf_merchant_id" value="' . esc_attr($value) . '">';
        echo '<p class="description">' . esc_html__("your MerchantID.") ."<br>" . esc_html__("your merchant ID given for QualPay.", 'trufuel-payment') . '</p>';
    }

     /** Render Api Key (Qualpay) */
    public function fieldApiKey() {
        $value = get_option('trf_qualpay_api_key', '');
        echo '<input type="password" class="regular-text" name="trf_qualpay_api_key" value="' . esc_attr($value) . '">';
        echo '<p class="description">' . esc_html__("your secret Key.") ."<br>" . esc_html__("your secret Key given for QualPay.", 'trufuel-payment') . '</p>';
    }

    /** Render the settings admin page */
    public function renderSettingsPage() {
        include TRF_PAYMENT_PATH . 'admin/partials/settings-page.php';
    }
}
