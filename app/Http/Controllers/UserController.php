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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function loginView() 
    {    
        $auth = Auth::user();

        $canAccessUsers = ['admin', 'staff'];

        if($auth) {
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

        return Inertia::render('Login', [
            'data' => [
                'sample' => 1,
                'auth' => $auth
            ]
        ]);
    }

    public function saveClientUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => "required|string",
            'last_name' => "required|string",
            'phone' => "required|numeric|unique:users,phone",
            'email' => "required|unique:users,email|email:rfc,dns", 
            'user_type' => "required",
            'role' => "required",
            'password' => "sometimes|required|min:8",
            'confirm_password' => "sometimes|required|same:password|min:8",
            'reference' => "required|exists:clients,reference",

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 200);
        }

        $password = $request->password;

        $data = $request->except(['confirm_password']);

        $data['password'] = Hash::make($password);
        
        $saveUser = User::create($data);
        
        return response()->json(['status' => 200], 200);  
    }

    public function saveUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => "required|string",
            'last_name' => "required|string",
            'phone' => "required|numeric|unique:users,phone",
            'email' => "required|unique:users,email|email:rfc,dns", 
            'user_type' => "required",
            'role' => "required",
            'password' => "sometimes|required|min:8",
            'confirm_password' => "sometimes|required|same:password|min:8",
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 200);
        }

        $data = $request->except(['confirm_password']);

        $data['password'] = Hash::make($request->password);
        
        $saveUser = User::create($data);
        
        return response()->json(['status' => 200], 200);  
    }

    public function loginAccount(Request $request)
    {
        $maintenance = Maintenance::first();

        $data = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        $user = User::where('email', $request->email)->first();

        if(!$user) {
            return redirect()->back()->with('message', 'No account found.');
        }

        if(!$user->is_active) {
            return redirect()->back()->with('message', 'Your account is not verified.');
        }

        if($user->user_type != 'admin') {
            if($maintenance->is_active) {
                return redirect()->back()->with('message', 'System is under maintenance.');
            }
        }

        if(Auth::attempt($data)) {
            $auth = Auth::user();

            if($auth) {
                return redirect('/'); 
            }

        } else {
            return redirect()->back()->with('message', 'Invalid Credentials.');
        }
    }

    public function viewUsers(Request $request)
    { 
        $auth = Auth::user();

        if($auth) {
            $users = User::orderBy('created_at', 'desc')->whereNotIn('id', [$auth->id]);

            if($auth->user_type == 'staff') {
                $users = $users->where('user_type', '!=', 'admin');
            }

            return Inertia::render('Users', [
                'auth'    => $auth,
                'options' => [
                    'users' => $users->get(),
                    'maintenance' => Maintenance::first()
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
            'name' => "required|string",
            'phone' => "required|numeric|unique:users,phone," . $request->id,
            'email' => "required|email:rfc,dns|unique:users,email," . $request->id, 
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

        return redirect()->back();
    }
} 
 