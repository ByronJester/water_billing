<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Client;
use App\Models\ClientPayment;
use App\Models\ClientUtility;
use App\Models\WaterBill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateClient;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ClientController extends Controller
{
    public function viewClients(Request $request)
    {
        $auth = Auth::user();

        if($auth) {
            $clients = Client::orderBy('created_at', 'desc');

            return Inertia::render('Clients', [
                'auth'    => $auth,
                'options' => [
                    'clients' => $clients->get()
                ]
            ]);
        }

        return redirect('/');
    }

    public function viewClient($reference) 
    {   
        $auth = Auth::user();

        $client = Client::where('reference', $reference)->first(); 

        $payments = ClientPayment::where('client_id', $client->id)->orderBy('created_at')->whereYear('created_at', Carbon::now()->year)->get();

        $reportArr = [];

        if($auth) {
            return Inertia::render('Client', [
                'auth'    => $auth,
                'options' => [
                    'client' => $client,
                    'payments' => $payments,
                    'reports' => [
                        'amount' => $payments->pluck('amount'),
                        'month' => $payments->pluck('month')
                    ]
                    
                ]
            ]);
        }

        return redirect('/');
        
    }

    public function saveClient(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => "required|string",
            'address' => "required|string",
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 200);
        }

        $data = $request->toArray();
        
        $data['reference'] = strtoupper(Str::random(8));

        $saveClient = Client::create($data);
        
        return response()->json(['status' => 200], 200);  
    }

    public function changeStatus(Request $request)
    {
        Client::where('id', $request->id)->update([
            'is_active' => $request->is_active
        ]);

        return redirect()->back();
    }

    public function viewBill(Request $request)
    {
        $auth = Auth::user();

        return Inertia::render('Bill', [
            'auth'    => $auth,
            'options' => [
                'sample' => []
            ]
        ]);
    }

    public function generateBill(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reference' => "required|exists:clients,reference",
            'consumed_cubic_meter' => "required|numeric|min:1",
            'date' => "required"
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 200);
        }

        $client = Client::where('reference', $request->reference)->first();

        $bills = ClientPayment::where('client_id', $client->id)->where('status', 'unpaid')->get();

        $waterBill = WaterBill::orderBy('created_at', 'desc')->first();

        $waterBillAmount = $waterBill->amount * $request->consumed_cubic_meter;

        $totalAmount = $waterBillAmount;

        if(count($bills) > 0) {
            $totalAmount += $bills->sum('amount');
            $penalty = (5 / 100) * $totalAmount;
            $client->penalty = $client->penalty + $penalty;
            $client->save();
        }

        $saveBill = ClientPayment::create([
            'client_id' => $client->id,
            'amount' => $waterBillAmount,
            'status' => 'unpaid',
            'date' => $request->date
        ]);

        $data = [
            'client' => $client,
            'amount' => $totalAmount,
            'due_date' => $request->date
        ];

        return response()->json(['status' => 200, 'data' => $data], 200);  
    }
}
