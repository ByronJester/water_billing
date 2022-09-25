<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Models\Client;

class UserController extends Controller
{
    public function loginView()
    {    
        $auth = Auth::user();

        $canAccessUsers = ['admin', 'staff'];

        if($auth) {
            if(in_array($auth->user_type, $canAccessUsers)) {
                return redirect('/users');
            } else {
                if($auth->user_type == 'reader') {
                    return redirect('/bills'); 
                }

                if($auth->user_type == 'utility') {
                    return redirect('/utilities');
                }

                if($auth->user_type == 'client') {
                    return redirect('/announcements');
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
            'name' => "required|string",
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
            'name' => "required|string",
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
                    'users' => $users->get()
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
} 
 