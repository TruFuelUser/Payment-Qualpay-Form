<?php
// namespace TruFuel\Services;
## Capture Proccess
class Capture {
    public function capture($payload) {
        $pgId = $payload['pgId'];

        $transactionData = array(
            'merchant_id'      => get_option('trf_merchant_id', ''),
            'amt_tran'         => $payload['amt'],
        );    

        // make the request in the QualPay API
        $url = "https://api-test.qualpay.com/pg/capture/$pgId";  // URL
        $ch = curl_init($url);

        // Configure cURL to send the POST request
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($transactionData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode(get_option('trf_qualpay_api_key', ''))  
        ));

        // Execute URL Request
        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $responseData = json_decode($response, true);

        if ($statusCode == 200) {
            return ['message' => "Your payment has been successfully approved and captured, your transaction ID is: $pgId", 'transactionID' => $responseData['pg_id']];
        } else {
            return $responseData['rmsg'];
        }
    }

}