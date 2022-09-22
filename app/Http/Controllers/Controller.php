<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Twilio\Rest\Client;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function sendSms($to, $message)
    {
        // $account_sid = 'AC0201237e8b04951bfbcdb9db86613254';
        // $auth_token = 'f3e32661062146402c7427e0a2dc3aed';
        // $twilio_number = "+19123043848";

        // $client = new Client($account_sid, $auth_token);

        // $to = '+'  . $to;

        // return $client->messages->create(
        //     $to,
        //     array(
        //         'from' => $twilio_number,
        //         'body' => $message
        //     )
        // );
    }
}
