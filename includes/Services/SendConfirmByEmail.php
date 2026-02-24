<?php
## Crear la posibilidad de manejar result y error 
## Result para saber si salio bien o mal el envio
## error para alojar los errores que pudieran haber si hubieron 
## cuando se pasan los datos recordar pasar el software segun el input sin importar si se eligio con el suggestion box o de manera directa
## Hacer lo mismo del software con el PhoneType
class SendConfirmByEmail {
    private array $sendTo;
    private string $transactionID;

    public function __construct($sendTo, $sendToInternal, $transactionID, $amount) {
        date_default_timezone_set("America/New_York");
        $this->date =  date("F j, Y");
        $this->sendTo = (array) $sendTo;
        $this->sendToInternal = (array) $sendToInternal;
        $this->amount = (float) $amount;
        $this->transaction = (string) $transactionID;
    }

    public function send(array $data): array {
       $payload = $data;
       error_log('Email Internal '.print_r($this->sendToInternal, true));

       $subject = 'Payment received succesfully!!!';
       $message = "<div style='padding:30px;margin-left:40px;'>
                    <h1 style='padding:30px 0 0 0;margin:0;color:#137DC5;text-align:left;font-family:Inter, Arial, sans-serif;font-size:40px;font-weight:700;background:transparent'>
                        Success!
                    </h1>
                    <h3 style='padding:10px 0 0 0;margin:0;font-family:Inter, Arial, sans-serif;color:#282828B2;font-weight:600;font-size:25px;'>
                        Payment Completed
                    </h3>
                    <p style='padding:20px 0 0 0;width:486px;font-family:Inter, Arial, sans-serif;font-size:25px;font-weight:400;color:#757474;'>
                       Your transaction has been processed <br> successfully<br><br>
                       <b>Amount: </b>". $this->amount ."<br> 
                       <b>Transaction ID: </b>" . $this->transaction ."<br>
                       <b>Date: </b>". $this->date ."<br>

                    </p>
                    <div style='padding:60px 0 0 0;color:#F5793B;font-family:Inter, Arial, sans-serif;font-size:25px;font-weight:800;'>
                        Share Success. 
                    </div>
                    <div style='font-family:Inter, Arial, sans-serif;font-size:20px;font-weight:600;color:#282828CC;'>
                        TruFuel Team
                    </div>
                    </div>";

        $messageInternal = "<div style='padding:30px;margin-left:40px;'>
                    <h1 style='padding:30px 0 0 0;margin:0;color:#137DC5;text-align:left;font-family:Inter, Arial, sans-serif;font-size:40px;font-weight:700;background:transparent'>
                        Success!
                    </h1>
                    <h3 style='padding:10px 0 0 0;margin:0;font-family:Inter, Arial, sans-serif;color:#282828B2;font-weight:600;font-size:25px;'>
                        Payment received successfuly!!
                    </h3>
                    <p style='padding:20px 0 0 0;width:486px;font-family:Inter, Arial, sans-serif;font-size:25px;font-weight:400;color:#757474;'>
                        Someone made a payment and it was <br> successfully received<br><br>
                       <b>Amount: </b>". $this->amount ."<br> 
                       <b>Transaction ID: </b>" . $this->transaction ."<br>
                       <b>Date: </b>". $this->date ."<br>

                    </p>
                    <div style='padding:60px 0 0 0;color:#F5793B;font-family:Inter, Arial, sans-serif;font-size:25px;font-weight:800;'>
                        Share Success. 
                    </div>
                    <div style='font-family:Inter, Arial, sans-serif;font-size:20px;font-weight:600;color:#282828CC;'>
                        TruFuel Team
                    </div>
                    </div>";


        $headers = [
            'From: TrueFuel Site <kimbernew20@gmail.com>',
            'Content-Type: text/html; charset=UTF-8',
        ];

        if ( $payload['email'] !== '' ) {
            $headers[] = 'Reply-To: ' . $payload['email'];
        }

        $url = 'http://truefuel.local/wp-content/uploads/2026/01/Email-FeedBack.jpg';

        $upload_dir = wp_upload_dir();
        $path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $url);

        // $path ahora es algo como:
        // C:\...\wp-content\uploads\2026\01\Email-FeedBack.jpg

        $attachments = [$path];
        error_log('Attachment Path: '.$path);

        $success = wp_mail( $this->sendTo, $subject, $message, $headers, $attachments);
        wp_mail($this->sendToInternal, $subject, $messageInternal, $headers, $attachments);

        return [
            'success'    => (bool) $success,
            'error_code' => $success ? null : 102,
        ];
    }

    private function preparePayload(array $data): array {
        return [
            'code'  => $this->readField($data, 'code'),
        ];
    }

    // allows to separate multiple fields by commas, if they exist
    private function readField(array $data, string $key): string {
        $value = $data[$key] ?? '';

        if (is_array($value)) {
            $value = implode(', ', $value);
        }

        return sanitize_text_field( wp_unslash( (string) $value ) );
    }
}
