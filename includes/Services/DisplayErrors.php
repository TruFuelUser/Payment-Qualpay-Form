<?php

class PaymentErrors {
    public int $code;

    public function __construct(int $code = null) {
        $this->code = $code;

        // If Polylang Plugin is active, show messsage in the selected language, if not exist show messages in english
        $requestLang = isset($_REQUEST['lang']) ? sanitize_key($_REQUEST['lang']) : null;
        $this->language = $requestLang
                ?: ( function_exists('pll_current_language') ? pll_current_language('slug') : 'en' );
        $this->url  ="messages-$this->language.json";
    }
    
    // Display errors from messages.json
    public function displayError(?string $messagesFile = null): ?string {
        $path = $messagesFile ?? __DIR__ . "/messages/$this->url";
        $suchFile = "The Message File was not Found";
        if (!is_file($path)) return $suchFile;

        $errors = json_decode(file_get_contents($path), true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($errors)) return null;

        if (isset($errors[$this->code])) {
            $error = $errors[$this->code];
            if (isset($error['code']) && is_int($error['code'])) {
                http_response_code($error['code']);
            }
            // return $error;
            return $this->generateMessage($error);
        }
        return null;
    }

    public function displayOnlyMessageError(?string $messagesFile = null): ?string {
        $path = $messagesFile ?? __DIR__ . "/messages/$this->url";
        $suchFile = "The Message File was not Found";
        if (!is_file($path)) return $suchFile;

        $errors = json_decode(file_get_contents($path), true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($errors)) return null;

        if (isset($errors[$this->code])) {
            $error = $errors[$this->code];
            if (isset($error['code']) && is_int($error['code'])) {
                http_response_code($error['code']);
            }
            return $error['message'] ?? null;
        }
        return null;
    }

    // Generate a UI message. Optional params override instance props.
    public function generateMessage(array $error): string {
        $type    = $error['type']    ?? 'notice';
        $message = $error['message'] ?? '';
        $title   = $error['title']   ?? '';

        // Defaults
        $classType = 'notice';
        $colorClass = 'notice_color';
        $icon = '<i class="fa-solid fa-clock"></i>';

        switch ($type) {
            case 'warning':
                $classType = 'alert_message';
                $colorClass = 'warning_color';
                $icon = '<i class="fa-solid fa-circle-exclamation"></i>';
                break;
            case 'success':
                $classType = 'normal';
                $colorClass = 'success_color'; // normalized case
                $icon = '<i class="fa-solid fa-circle-check"></i>';
                break;
            case 'error':
                $classType = 'error_message';
                $colorClass = 'error_color';   // normalized case
                $icon = '<i class="fa-solid fa-circle-xmark"></i>';
                break;
            case 'notice':
                $classType = 'notice_message';
                $colorClass = 'notice_color';   // normalized case
                $icon = '<i class="fa-solid fa-clock"></i>';
                break;
        }

        // Escape user-visible text
        $safeMessage = htmlspecialchars($message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        return "<div class='ResponseMessage {$type}'>
                    <div class='message_rectangle {$colorClass}'></div>
                    <p class='message_icon {$colorClass} {$classType}'>{$icon}</p>
                    <button class='exit-message' type='button' aria-label='Dismiss'><i class='fa-solid fa-xmark'></i></button>
                    <div class='message-paragraph'>
                        <b class='bold-message'>{$title}</b><br>
                        <span class='text-message {$classType}'>{$safeMessage}</span>
                    </div>
                </div>";
    }
}