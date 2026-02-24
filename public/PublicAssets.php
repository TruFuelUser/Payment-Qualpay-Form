<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://trufuel.net
 * @since      1.0.0
 *
 * @package    Payment_Form_Trucore
 * @subpackage Payment_Form_Trucore/public
 */

/**
 * Defines the plugin name, version, and helpers to enqueue assets.
 */
class Payment_Form_Trucore_Public {

    /** @var string */
    private $plugin_name;

    /** @var string */
    private $version;

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'css/payment-form-trucore.css',
            [],
            $this->version,
            'all'
        );
    }

    public function enqueue_scripts() {
        wp_enqueue_script(
            "{$this->plugin_name}-form",
            plugin_dir_url( __FILE__ ) . 'js/payment-form-trucore.js',
            [ 'jquery' ],
            $this->version,
            true
        );

         wp_enqueue_script(
           "{$this->plugin_name}-content",
            plugin_dir_url( __FILE__ ) . 'js/content.js',
            [ 'jquery' ],
            $this->version,
            true
        );

        $site_key = get_option( 'trf_recaptcha_site_key', '' ); // Captcha Site Key (public)

        wp_localize_script(
            "{$this->plugin_name}-form",
            'TFPayment',
            [
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'trufuel_form_nonce' ),
                'recaptchaSiteKey'  => $site_key,
            ]
        );

        wp_localize_script(
            "{$this->plugin_name}-content",
            'TFContent',
            [
                'language'    => function_exists('pll_current_language') ? pll_current_language('slug') : 'en',
                'contentBase' => plugins_url( 'public/partials/content/content', dirname( __FILE__ ) ),
            ]
        );
    }
}
