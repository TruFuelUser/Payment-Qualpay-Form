<?php
// namespace TruFuel\Services;
## Validate Leads Fields
class PaymentValidator {
    private array $errors = [];

    public function validate(array $fields): array {
        $data = $this->sanitize($fields);
        $expiration = $data['month'] .'/'. $data['year'];
        $cardNumber = preg_replace('/\D+/', '', $data['cardNumber']);

        function validateMMYY($dateString) {
            $format = 'm/y';
            $d = DateTime::createFromFormat($format, $dateString);

            if (!$d || $d->format($format) !== $dateString) {
                return false;
            }

            $now = DateTime::createFromFormat($format, date($format));

            // Comparar fechas
            return $d > $now;
        }


        if (empty($data['cardOwner']) || empty($data['cardNumber']) || empty($expiration) || empty($data['cvv']) || empty($data['address'] || empty($data['zip']) || empty($data['amt'])) ) {
            $this->addError('missingFields', 114);
        }

        if ($data['cardOwner'] === '') {
            $this->addError('cardOwner', 103);
        }
        elseif (!preg_match('/[a-zA-Z]/', $data['cardOwner'])) {
            $this->addError('cardOwner', 104);
        }

        if ($cardNumber === '') {
            $this->addError('cardNumber', 105);
        } 
        elseif (!preg_match('/^[0-9]+$/', $cardNumber) || strlen($cardNumber) > 16 || strlen($cardNumber) < 15) {
            $this->addError('cardNumber', 106);
        }

        if ($expiration === '') {
            $this->addError('expiration', 107);
        } 
        elseif (!validateMMYY($expiration)) {
            $this->addError('expiration', 108);
        } 

        if (!empty($data['email']) && !filter_var( $data['email'], FILTER_VALIDATE_EMAIL )) {
            $this->addError('email', 109);
        }

        if ($data['company'] !== '' && !preg_match('/[a-zA-Z]/', $data['company'])) {
            $this->addError('company', 110);
        }

        if (!is_numeric($data['amt']) && !$data['amt'] > 0) {
             $this->addError('amt', 115);
        }

        // create verification for amount (should be  0.00 format)

        return [ $data, $this->errors ];
    }

    private function sanitize(array $fields): array {
        $lookup = static fn($key, $default = '') => isset($fields[$key]) ? trim((string) $fields[$key]) : $default;
        $expiration = $lookup('month') . $lookup('year');
        $cardNumber = preg_replace('/\D+/', '', $lookup('cardNumber'));

        return [
            'cardOwner'     => $lookup('cardOwner'),
            'cardNumber'    => $cardNumber,
            'expDate'       => $expiration,
            'cvv'           => $lookup('cvv'),
            'address'       => $lookup('address'),
            'zipCode'       => $lookup('zipCode'),
            'company'       => $lookup('company'),
            'acctNo'        => $lookup('acctNo'),
            'invoice'       => $lookup('invoice'),
            'email'         => $lookup('email'),
            'amt'           => $lookup('amt'),
            'month'         => $lookup('month'),
            'year'          => $lookup('year')
        ];
    }


    private function addError(string $field, int $code): void {
        $this->errors[$field] = $code;
    }

}