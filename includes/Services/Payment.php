<?php
// namespace TruFuel\Services;
## Payment process
class Payment {
    public function payment($payload) {
         $transactionData = array(
            'merchant_id'      => get_option('trf_merchant_id', ''),
            'amt_tran'         => $payload['amt'],
            'cardholder_name'  => $payload['cardOwner'], 
            'card_number'      => $payload['cardNumber'],
            'exp_date'         => $payload['expDate'],
            'cvv2'             => $payload['cvv'],
            'avs_address'      => $payload['address'],
            'avs_zip'          => $payload['zip']
         );    

         // Just if exist address (given avs_address)
         if ($payload['address'] !== null && $payload['address'] !== '') {
            $transactionData['avs_address'] = $payload['address'];
         };

        // make the request in the QualPay API
        $url = 'https://api-test.qualpay.com/pg/auth';  // URL
        $ch = curl_init($url);

        // Configure cURL to send the POST request
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($transactionData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode(get_option('trf_qualpay_api_key', ''))  // Usar el API Key como Bearer Token
        ));

        // Execute URL Request
        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $responseData = json_decode($response, true);

        if ($statusCode == 200) {
            return ['message' => "Your payment has been successfully approved, your transaction ID is: $responseData[pg_id]", 'transactionID' => $responseData['pg_id']];
        } else {
            return $responseData['rmsg'];
        }
    }

}