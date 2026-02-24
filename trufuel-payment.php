<?php
/**
 * Plugin Name: Payment Form Qualpay
 * Description: Payment form compatible with Qualpay and ReCaptcha.
 * Version: 1.0.0
 * Author: TruFuel Systems
 * Text Domain: qualpay
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'TRF_PAYMENT_PATH', plugin_dir_path( __FILE__ ) );
define( 'TRF_PAYMENT_URL', plugin_dir_url( __FILE__ ) );
define( 'TRF_PAYMENT_VER', '1.0.0' );

// Include Classes
require_once TRF_PAYMENT_PATH . 'includes/Admin.php';
require_once TRF_PAYMENT_PATH . 'public/PublicAssets.php';
require_once TRF_PAYMENT_PATH . 'includes/Services/DisplayErrors.php';
require_once TRF_PAYMENT_PATH . 'includes/Services/CheckCaptcha.php';
require_once TRF_PAYMENT_PATH . 'includes/Services/PaymentValidator.php';
require_once TRF_PAYMENT_PATH . 'includes/Services/Payment.php';
require_once TRF_PAYMENT_PATH . 'includes/Services/SendConfirmByEmail.php';


function trufuelAjaxPayment() {
    $fields = $_POST['form_fields'] ?? [];
    $token = $fields['token'] ?? '';

    // Verify Captcha
    $captcha = new TruCoreCaptchaPayment($token, null, 'submit', 0.7);
    if (!$captcha->verify()) {
         $messages = (new PaymentErrors(111))->displayError();
         wp_send_json_error([ 'html' => $messages ] );
         return;
    }

    // Validate Fields
    $validator = new PaymentValidator();
    [$data, $errors] = $validator->validate($fields);

    if ($errors) {
        $messages = '';
        foreach ($errors as $field => $code) {
            $display = new PaymentErrors($code);
            $messages .= $display->displayError(); 
        }
        wp_send_json_error([ 'html' => $messages ] );
        return;
    }

    $pay = new Payment();

    try {
        $response = $pay->payment([
            'cardOwner'     => $data['cardOwner'],
            'cardNumber'    => $data['cardNumber'],
            "expDate"       => $data['expDate'],
            "cvv"           => $data['cvv'], 
            "address"       => $data['address'],
            'zip'           => $data['zipCode'],
            "company"       => $data['company'],
            "acctNo"        => $data['acctNo'],
            "invoice"       => $data['invoice'],
            "email"         => $data['email'],
            "amt"           => $data['amt']
        ]);

        $message = [
            'type' => 'success',
            'title' => 'Success!',
            'message' => $response['message'],
        ];

        $transactionID = $response['transactionID'];

        $displayMessage = new PaymentErrors(0); //message code isn't use
        $html = $displayMessage->generateMessage($message); 
        wp_send_json_success(['html' =>  $html, 'transactionID' => $transactionID]);
        return;
    }catch (Exeption $e) { 
        $message = new PaymentErrors(102);
        $html = $message->displayError();
        wp_send_json_error(['html' => $html]);
        return;
    }
}

function trufuelAjaxSendEmail() {
    $fields  =  $_POST['form_fields'] ?? [];
    // change This for sent to multiple mail accounts
    $emailToOption   = trim(get_option('trf_confirm_mail', ''));
    $emailToInternal = array_filter(array_map('trim', explode(';', $emailToOption)));
    $emailTo         = $fields['email'];
    $transactionID   = $fields['transactionID'];
    $amount          = $fields['amt'];

    if ( empty($emailTo) && empty($emailToInternal) ) {
        return;
    }

    $repo = new SendConfirmByEmail($emailTo, $emailToInternal, $transactionID, $amount);
    $result = $repo->send($fields);

    if (empty($result['success'])) {
        $errorCode = $result['error_code'] ?? 102;
        error_log('Error sending confirm email, code: ' . $errorCode);
        return;
    }

    $message = new PaymentErrors(113);
    // Show only Message without HTML
    $html = $message->displayOnlyMessageError();
  
    wp_send_json_success(['html' => $html]);           
    return;
}

function showPaymentForm() {
    ob_start();
    include TRF_PAYMENT_PATH . 'public/partials/payment-form-display.php';
    return ob_get_clean();
}

// ========== MAKE PAYMENT =========================
add_action( 'wp_ajax_trufuel_payment', 'trufuelAjaxPayment' );
add_action( 'wp_ajax_nopriv_trufuel_payment', 'trufuelAjaxPayment' );

// ========== SEND CONFIRM BY EMAIL =========================
add_action( 'wp_ajax_trufuel_send_email', 'trufuelAjaxSendEmail' );
add_action( 'wp_ajax_nopriv_trufuel_send_email', 'trufuelAjaxSendEmail' );

$paymentPublic = new Payment_Form_Trucore_Public( 'trucore-payment-form', TRF_PAYMENT_VER );
add_action( 'wp_enqueue_scripts', [ $paymentPublic, 'enqueue_styles' ], 99 );
add_action( 'wp_enqueue_scripts', [ $paymentPublic, 'enqueue_scripts' ] );

if ( is_admin() ) {
    new TrufPaymentAdmin();
}

add_shortcode( 'trufuel-payment-form', 'showPaymentForm' );
