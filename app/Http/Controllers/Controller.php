<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
// use Twilio\Rest\Client;
use Vonage\Client\Credentials\Basic;
use Vonage\Client;
use Vonage\SMS\Message\SMS;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function sendSms($to, $message)
    {
        // $account_sid = 'AC0201237e8b04951bfbcdb9db86613254';
        // $auth_token = '0c392402a2ec22b5e426f8b0275b80c5';
        // $twilio_number = "+19123043848";

        // $to = '+63'  . $to;

        // $client = new Client($account_sid, $auth_token);

        // return $client->messages->create(
        //     $to,
        //     array(
        //         'from' => $twilio_number,
        //         'body' => $message
        //     )
        // );

        $basic  = new Basic("81441324", "YE8fsKh1iubGLzwt");
        $client = new Client($basic);

        $response = $client->sms()->send(
            new SMS("639771023141", BRAND_NAME, 'A text message sent using the Nexmo SMS API')
        );

        $message = $response->current();

        return $message->getStatus();
    }
}
