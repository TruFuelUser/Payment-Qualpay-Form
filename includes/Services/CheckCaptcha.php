<?php
## RECAPTCHA VERIFICATION
## INCLUDE RECAPTCHA KEYS FOR CONFIGURATION
## CREATE CLASS FOR THIS

if (!defined('ABSPATH')) exit;

class TruCoreCaptchaPayment {
    /** @var string */
    private $token;
    /** @var string */
    private $secretKey;
    /** @var string|null Accion esperada (v3). Ej: 'submit' */
    private $expectedAction;
    /** @var float Umbral v3 (0.0-1.0) */
    private $threshold;
    /** @var array|null Guarda la última respuesta decodificada */
    public $lastResponse = null;

    private const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * @param string      $token         
     * @param string|null $secretKey      
     * @param string|null $expectedAction 
     * @param float       $threshold      
     */
    public function __construct(string $token, ?string $secretKey = null, ?string $expectedAction = null, float $threshold = 0.5) {
        $this->token          = $token;
        $this->secretKey      = $secretKey ?? (string) get_option('trf_recaptcha_secret_key', '');
        $this->expectedAction = $expectedAction;
        $this->threshold      = $threshold;
    }

    /**
     * verify Google token.
     * return true if validation is true; false is not.
     * In $this->lastResponse keep the array of Google response.
     */
    public function verify(): bool {
        if ($this->secretKey === '' || $this->token === '') {
            $this->lastResponse = null;
            error_log('reCAPTCHA missing secret key or token 1234');
            return false;
        }

        $resp = wp_remote_post(self::VERIFY_URL, [
            'timeout' => 8,
            'body' => [
                'secret'   => $this->secretKey,
                'response' => $this->token,
                'remoteip' => $_SERVER['REMOTE_ADDR'] ?? ''
            ],
        ]);

        if (is_wp_error($resp)) {
            error_log('reCAPTCHA request error: ' . $resp->get_error_message());
            $this->lastResponse = null;
            return false;
        }

        $body = wp_remote_retrieve_body($resp);
        $json = json_decode($body, true);

        if (!is_array($json)) {
            error_log('reCAPTCHA invalid JSON response');
            $this->lastResponse = null;
            return false;
        }

        $this->lastResponse = $json;

        // v2 & v3: success should be true
        if (empty($json['success'])) {
            error_log('reCAPTCHA verification failed: ' . print_r($json, true));
            return false;
        }

        //  if the 'score' is present, we assume v3 and apply extra rules
        if (isset($json['score'])) {
            if ($json['score'] < $this->threshold) {
                error_log('reCAPTCHA v3 score too low: ' . $json['score']);
                return false;
            }
            if ($this->expectedAction !== null && isset($json['action']) && $json['action'] !== $this->expectedAction) {
                error_log("reCAPTCHA v3 action mismatch: expected '{$this->expectedAction}', got '{$json['action']}'");
                return false;
            }
        }

        return true;
    }
}
