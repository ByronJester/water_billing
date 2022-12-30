<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ClientUtility;
use App\Models\Client;
use App\Models\Maintenance;
use App\Models\AuditTrail;
use App\Models\Verification;
use App\Models\WaterBill;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class UserController extends Controller
{
    public function loginView() 
    {    
        $auth = Auth::user();

        $canAccessUsers = ['admin', 'staff', 'cashier'];

        if($auth) {
            if(!$auth->is_change_password) {
                return redirect('/users/profile'); 
            } else {
                if(in_array($auth->user_type, $canAccessUsers)) {
                    return redirect('/clients');
                } else {
                    if($auth->user_type == 'reader') {
                        return redirect('/bills'); 
                    }
    
                    if($auth->user_type == 'utility') {
                        return redirect('/clients/view/utilities'); 
                    } 
    
                    if($auth->user_type == 'client') {
                        $route =  "/clients" . "/" . $auth->reference;
                        return redirect($route);
                    }
                }
            }
            
        }

        return Inertia::render('Login', [
            'data' => [
                'sample' => 1,
                'auth' => $auth
            ]
        ]);
    }

    public function saveUser(Request $request)
    {
        $validator = Validator::make($request->toArray(), [
            'first_name' => "required|alpha_spaces",
            'middle_name' => "nullable|alpha_spaces",
            'last_name' => "required|alpha_spaces",
            'phone' => "required|numeric|unique:users,phone",
            'email' => "nullable",
            'username' => "required|unique:users,username|min:5|regex:/(^[A-Za-z0-9]+$)+/", 
            'user_type' => "required",
            'role' => "required",
            'password' => "sometimes|required|min:8",
            'confirm_password' => "sometimes|required|same:password|min:8"
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 200);
        }

        $data = $request->except(['confirm_password']);

        $data['password'] = Hash::make($request->password);
        $data['is_change_password'] = true;
        
        $saveUser = User::create($data);
        
        return response()->json(['status' => 200], 200);  
    }

    public function loginAccount(Request $request)
    {
        $maintenance = Maintenance::first();

        $data = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        $user = User::where('username', $request->username)->first();

        if(!$user) {
            return response()->json(['status' => 422, 'message' => 'No account found.' ], 200); 
        }

        if(!$user->is_active) {
            return response()->json(['status' => 422, 'message' => 'Your account is deactivated.' ], 200); 
        }

        if($user->user_type != 'admin' && $user->user_type != 'staff') {
            if($maintenance->is_active) {
                // return redirect()->back()->with('message', 'System is under maintenance.');

                return response()->json(['status' => 422, 'message' => 'System is under maintenance.' ], 200);
            }
        }

        if(Auth::attempt($data)) {
            $auth = Auth::user();

            if($auth->user_type != 'client') {
                $description = $auth->first_name . ' ' . $auth->last_name . ' has logged in.';
    
                $this->saveLogs($description);
            }

            if($auth) {
                return response()->json(['status' => 200, 'message' => 'success' ], 200);
            }

        } else {
            return response()->json(['status' => 422, 'message' => 'Invalid Credentials.' ], 200);
        }
    }

    public function viewUsers(Request $request)
    { 
        $auth = Auth::user();

        if($auth) {
            $users = User::orderBy('created_at', 'desc')->whereNotIn('id', [$auth->id]);
            $workers = User::where('user_type', 'utility')->get();

            if($auth->user_type == 'staff') {
                $users = $users->where('user_type', '!=', 'admin');
            }

            $bills = WaterBill::orderBy('created_at', 'desc')->get();

            $utilities = ClientUtility::orderBy('created_at', 'desc')->get();

            return Inertia::render('Users', [
                'auth'    => $auth,
                'options' => [
                    'users' => $users->get(),
                    'maintenance' => Maintenance::first(), 
                    'bills' => $bills,
                    'utilities' => $utilities,
                    'workers' => $workers
                ]
            ]);
        }

        return redirect('/');
    }

    public function changeStatus(Request $request)
    {
        User::where('id', $request->id)->update([ 
            'is_active' => $request->is_active
        ]);

        return redirect()->back();
    }

    public function logoutAccount()
    {   
        $auth = Auth::user();

        if($auth->user_type != 'client') {
            $description = $auth->first_name . ' ' . $auth->last_name . ' has logged out.';

            $this->saveLogs($description);
        }
        
        Auth::logout();

        return redirect('/');;
    }

    public function viewProfile(Request $request)
    {
        $auth = Auth::user();

        if($auth) {
            return Inertia::render('Profile', [
                'auth'    => $auth,
                'options' => [
                ]
            ]);
        }

        return redirect('/');
    }

    public function editProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => "required|alpha_spaces",
            'middle_name' => "nullable|alpha_spaces",
            'last_name' => "required|alpha_spaces",
            'phone' => "required|numeric|unique:users,phone," . $request->id,
            'username' => "required|min:5|regex:/(^[A-Za-z0-9]+$)+/|unique:users,username," . $request->id, 
            'password' => "sometimes|required|min:8",
            'confirm_password' => "sometimes|required|same:password|min:8",
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 200);
        }

        $data = $request->toArray();

        if(!!$request->password) {
            $data = $request->except(['confirm_password']);

            $data['password'] = Hash::make($request->password);

            $data['is_change_password'] = true;
        }

        
        
        $saveUser = User::where('id', $request->id)->update($data);
        
        return response()->json(['status' => 200], 200);  
    }

    public function viewReports(Request $request)
    {
        $auth = Auth::user();

        if($auth) {
            return Inertia::render('Reports', [
                'auth'    => $auth,
                'options' => [
                    'ir' => ClientUtility::get(),
                    'clients' => Client::get() 
                ]
            ]);
        }

        return redirect('/');
    }

    public function resetPassword(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        
        $password = sprintf("%06d", mt_rand(1, 999999));

        $user->password = Hash::make($password); 

        $user->save();

        $message = 'Your password has been reset and your temporary password is ' . $password;

        $this->sendSms($user->phone, $message);

        return response()->json(['status' => 200], 200);  
    }

    public function systemMaintenance(Request $request)
    {
        $maintenance = Maintenance::first();

        $maintenance->is_active = $request->is_active;
        $maintenance->save(); 

        if($request->is_active) {
            $users = User::where('user_type', 'client')->get();

            $now = Carbon::now();

            foreach($users as $user) {
                $message = "ANNOUNCEMENT \r\n  Dear Clients, \r\n  We will be experiencing server downtime on the %s due to system maintenance. \r\n  Water Billing Management System will not be available during this time. \r\n We apologize for any inconvenience caused and thank you for continuous support. \r\n  For any inquiries, please contact 09566814383/09657657443.";
                $message = sprintf($message, $now->isoFormat('LL'));

                $this->sendSms($user->phone, $message);
            }
        } else {
            $users = User::where('user_type', 'client')->get();

            $now = Carbon::now();

            foreach($users as $user) {
                $message = "ANNOUNCEMENT \r\n  Dear Clients, \r\n  We will be resuming normal hours starting today. You can now access our website https://water-billing-6mb6.onrender.com  \r\n  Thank you for understanding. \r\n  For any inquiries, please contact 09566814383/09657657443.";
                $message = sprintf($message, $now->isoFormat('LL'));

                $this->sendSms($user->phone, $message);
            }
        }
        

        return redirect()->back();
    }

    public function notifyMaintenance(Request $request)
    {
        $users = User::where('user_type', 'client')->get();

        $a = null;

        $now = Carbon::now();
        $now->addDays(1);

        foreach($users as $user) {
            $message = "ANNOUNCEMENT \r\n  Dear Clients, \r\n  Due to scheduled maintenance activity, Water Billing Management System will also not be available on %s. But our office is open everyday. \r\n  We apologize for any inconvenience caused and thank you for continuous support. \r\n  For any inquiries, please contact 09566814383/09657657443.";
            $message = sprintf($message, $now->isoFormat('LL'));

            $a = $this->sendSms($user->phone, $message); 
        }

        return response()->json(['status' => 200, 'sms' => $a], 200);  
    }

    public function viewUtilities(Request $request)
    {
        $auth = Auth::user();

        if($auth) {
            return Inertia::render('Utility', [
                'auth'    => $auth,
                'options' => [
                    'trails' => AuditTrail::orderBy('created_at', 'desc')->get(),
                    'clients' => Client::where('is_active', false)->get()
                ]
            ]);
        }

        return redirect('/');
    }

    public function alive(Request $request)
    {   
        return response()->json(['status' => 200, 'res' => $res], 200); 
    }
} 
 