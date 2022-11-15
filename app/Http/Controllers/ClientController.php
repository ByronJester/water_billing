<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\User;
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
                    'clients' => $clients->get(),
                    'users' => User::get(),
                    'incidents' => ClientUtility::get()
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
            'first_name' => "required|string",
            'last_name' => "required|string",
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

        $consumed_cubic_meter =  ($request->consumed_cubic_meter - $bills->sum('consumed_cubic_meter'));
        $waterBillAmount = $consumed_cubic_meter * $waterBill->amount;

        $totalAmount = $waterBillAmount;

        if(count($bills) > 0) {
            $totalAmount += $bills->sum('amount');
            $penalty = (5 / 100) * $totalAmount;
            $client->penalty = $client->penalty + $penalty;
        }

        $client->payment_date = null;
        $client->save();

        $saveBill = ClientPayment::create([
            'client_id' => $client->id,
            'consumed_cubic_meter' => $consumed_cubic_meter,
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

    public function markAsPaid(Request $request)
    {
        Client::where('id', $request->id)->update([
            'penalty' => 0,
            'payment_date' => Carbon::now()
        ]);

        ClientUtility::where('client_id', $request->id)->update(['status' => 'paid']);

        ClientPayment::where('client_id', $request->id)->update(['status' => 'paid']);

        return response()->json(['status' => 200], 200);  
    }

    public function notifyClient(Request $request)
    {

        $users = User::where('reference', $request->reference)->get();

        $sms = null;

        foreach($users as $user) {
            $message = 'Due date of your payment is ' . $request->due_date;
            $phone = substr($user->phone, 1);


            $sms = $this->sendSms($phone, $message);
        }

        return response()->json(['status' => 200, 'sms' => $sms], 200);  
    }

    public function viewUtilities(Request $request)
    {
        $auth = Auth::user();

        $utilities = ClientUtility::orderBy('created_at', 'desc');

        if($auth) {
            if($auth->role == 2) {
                $client = Client::where('reference', $auth->reference)->first();

                $utilities = $utilities->where('client_id', $client->id);
            } 

            return Inertia::render('Utilities', [
                'auth'    => $auth,
                'options' => [
                    'utilities' => $utilities->get()
                ]
            ]);
        }

        return redirect('/');
    }

    public function generateIncidentReport(Request $request)
    {
        $auth = Auth::user();

        $validator = Validator::make($request->all(), [
            'description' => "required",
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 200);
        }

        $data = $request->toArray();
        $client = Client::where('reference', $auth->reference)->first();
        
        $data['client_id'] = $client->id;
        $data['status'] = 'pending';

        ClientUtility::create($data);

        return response()->json(['status' => 200], 200);  
    }

    public function incidentReportChangeStatus(Request $request)
    {
        $auth = Auth::user();

        $utilities = ClientUtility::orderBy('created_at', 'desc');

        if($auth->role == 2) {
            $client = Client::where('reference', $auth->reference)->first();

            $utilities = $utilities->where('client_id', $client->id);
        }

        ClientUtility::where('id', $request->id)->update([
            'status' => $request->status,
            'amount' => $request->amount
        ]);

        return response()->json(['status' => 200, 'utilities' => $utilities->get()], 200);  
    }
}
