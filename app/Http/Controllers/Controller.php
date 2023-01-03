<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditTrail;
use Vonage\Client\Credentials\Basic;
use Vonage\Client;
use Vonage\SMS\Message\SMS;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function sendSms($to, $message)
    {
        $ch = curl_init();

        $parameters = array(
            'apikey' => '3763c09ae9280c98876389affa1ad5fc', //Your API KEY
            'number' => $to,
            'message' => $message,
            'sendername' => 'SEMAPHORE'
        );

        curl_setopt( $ch, CURLOPT_URL,'https://semaphore.co/api/v4/messages' );
        curl_setopt( $ch, CURLOPT_POST, 1 );

        //Send the parameters set above with the request
        curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $parameters ) );

        // Receive response from server
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec( $ch );

        curl_close ($ch);

        return $output;
    }

    public function saveLogs($description)
    {
        $auth = Auth::user();

        AuditTrail::forceCreate(
            [
                'user_id' => $auth->id,
                'description' => $description
            ]
        );
    }

    // public function saveClientUser($data)
    // {
    //     $password = strtoupper(Str::random(8));

    //     $req['first_name'] = $data['first_name'];
    //     $req['middle_name'] = $data['middle_name'];
    //     $req['last_name'] = $data['last_name'];
    //     $req['phone'] = $data['phone'];
    //     $req['username'] = $data['reference'];
    //     $req['password'] = Hash::make($password);
    //     $req['user_type'] = 'client';
    //     $req['role'] = 2;
    //     $req['reference'] = $data['reference'];
        
    //     $message = "Dear Client, \r\n Your temporary username is your account number ( %s ) and your password is  %s. \r\n You are required to change your password in your profile to our system for security purposes. \r\n Thank you!";
    //     $message = sprintf($message, $data['reference'], $password);

    //     $this->sendSms($data['phone'], $message); 

    //     $saveUser = User::create($req);

    //     return response()->json(['status' => 200], 200);
    // }
}
