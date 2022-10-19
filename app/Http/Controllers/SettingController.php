<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\WaterBill;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function viewWaterBill(Request $request) 
    {
        $auth = Auth::user();

        if($auth) {
            $bills = WaterBill::orderBy('created_at', 'desc');

            return Inertia::render('Settings', [
                'auth'    => $auth,
                'options' => [
                    'bills' => $bills->get()
                ]
            ]);
        }

        return redirect('/');
    }

    public function saveBill(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => "required|numeric|min:1",
            'date' => "required",
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 200);
        }

        $data = $request->toArray();
        
        $data['user_id'] = Auth::user()->id;

        $saveBill = WaterBill::create($data);
        
        return response()->json(['status' => 200], 200);  
    }
}
