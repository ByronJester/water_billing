<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\User;
use App\Models\Client;
use App\Models\ClientPayment;
use App\Models\PaymentHistory;
use App\Models\ClientUtility;
use App\Models\WaterBill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CreateClient;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    public function viewClients(Request $request)
    {
        $auth = Auth::user();

        if($auth) {
            $clients = Client::orderBy('created_at', 'desc')->where('is_active', true);
            $activatedClients = Client::orderBy('created_at', 'desc')->where('status', 'activated')->where('is_active', true)->get();

            return Inertia::render('Clients', [ 
                'auth'    => $auth,
                'options' => [
                    'clients' => $clients->get(),
                    'users' => User::get(),
                    'incidents' => ClientUtility::where('status', 'pending')->get(),
                    'activatedClients' => $activatedClients
                ]
            ]);
        }

        return redirect('/');
    }

    public function viewClient($reference) 
    {   
        $auth = Auth::user();

        $client = Client::where('reference', $reference)->first(); 

        $payments = ClientPayment::where('client_id', $client->id)->orderBy('date')->whereYear('date', Carbon::now()->year)->get();

        $unpaid = ClientPayment::where('client_id', $client->id)->where('status', 'unpaid')->whereYear('date', Carbon::now()->year)->get();

        $reportArr = [];

        if($auth) {
            return Inertia::render('Client', [
                'auth'    => $auth,
                'options' => [
                    'client' => $client,
                    'payments' => $payments,
                    'reports' => [
                        'amount' => $payments->pluck('amount'), 
                        'month' => $payments->pluck('month'),
                    ],
                    'unpaid' => $unpaid->sum('amount_to_pay')
                ]
            ]);
        }

        return redirect('/');
        
    }

    public function saveClient(Request $request)  
    {
        $validator = Validator::make($request->all(), [
            'first_name' => "required|alpha_spaces",
            'middle_name' => "nullable|alpha_spaces",
            'last_name' => "required|alpha_spaces",
            'house_no' => "required|string",
            'street' => "required|string",
            'town' => "required|string",
            'province' => "required|string", 
            'phone' => "required|numeric|digits:11|unique:clients,phone|unique:users,phone",
            'serial' => "required|numeric|unique:clients,serial|digits:9",
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 200);
        }

        $data = $request->toArray();
        
        $data['reference'] = strtoupper(Str::random(8));

        $saveClient = Client::forceCreate($data);

        $sms = $this->saveClientUser($data);

        ClientUtility::forceCreate([
            'user_id' => 1,
            'client_id' => $saveClient->id,
            'description' => "New Connection",
            'amount' => 0,
            'status' => "pending"
        ]);
        
        return response()->json(['status' => 200, 'sms' => $sms], 200);  
    }

    public function saveClientUser($data)
    {
        $password = strtoupper(Str::random(8));

        $req['first_name'] = $data['first_name'];
        $req['middle_name'] = $data['middle_name'];
        $req['last_name'] = $data['last_name'];
        $req['phone'] = $data['phone'];
        $req['username'] = $data['reference'];
        $req['password'] = Hash::make($password);
        $req['user_type'] = 'client';
        $req['role'] = 2;
        $req['reference'] = $data['reference'];
        
        $message = "Dear Client, \r\n Your temporary username is your account number ( %s ) and your password is  %s. \r\n You are required to change your password in your profile to our system for security purposes. \r\n Thank you!";
        $message = sprintf($message, $data['reference'], $password);

        $saveUser = User::create($req);

        $sms = $this->sendSms($saveUser->phone, $message);

        return $sms;
    }

    public function changeStatus(Request $request)
    {   
        $client = Client::where('id', $request->id)->first();
        $client->is_active = $request->is_active;
        $client->save();

        User::where('reference', $client->reference)->update([
            'is_active' => $request->is_active
        ]);

        return redirect()->back();
    }

    public function viewBill(Request $request) 
    {
        $auth = Auth::user();

        $clients = Client::where('status', 'activated')->get();

        return Inertia::render('Bill', [
            'auth'    => $auth,
            'options' => [
                'clients' => $clients
            ]
        ]);
    }

    public function generateBill(Request $request)
    {   
        $auth = Auth::user();

        $client = Client::where('reference', $request->reference)->first();

        $bills = ClientPayment::where('client_id', $client->id)->where('status', 'unpaid')->get();

        $validator = Validator::make($request->all(), [
            'reference' => "required|exists:clients,reference",
            'consumed_cubic_meter' => "required|numeric|min:" . ($bills->sum('consumed_cubic_meter') + 1),
            'date' => "required"
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages(), 'status' => 422], 200);
        }

        $dateSelected = Carbon::parse($request->date);

        $existingReading = ClientPayment::where('client_id', $client->id)->whereMonth('date', $dateSelected->month)->first();

        if(!!$existingReading) {
            $errMessage = 'Reading already exist for this month.';
            return response()->json(['status' => 422, 'errMessage' => $errMessage], 200); 
        }

        $waterBill = WaterBill::orderBy('created_at', 'desc')->first();

        $consumed_cubic_meter =  ($request->consumed_cubic_meter - $bills->sum('consumed_cubic_meter'));
        $waterBillAmount = $consumed_cubic_meter * $waterBill->amount;

        $previousBill = $bills->sum('amount');

        $totalAmount = $waterBillAmount;

        $penalty = 0;

        if(count($bills) > 0) {
            $penalty = (10 / 100) * $totalAmount;

            foreach($bills as $bill) {
                $bill = (object) $bill;

                $totalAmount += $bill->amount;
            }
        }

        $due_date = Carbon::parse($request->date);
        $days = $this->getDays($request->date);

        $due_date = $due_date->addDays($days);

        

        $saveBill = ClientPayment::forceCreate([
            'client_id' => $client->id,
            'consumed_cubic_meter' => $consumed_cubic_meter, 
            'amount' => $waterBillAmount,
            'status' => 'unpaid',
            'date' => $request->date,
            'penalty' => $penalty,
            'payment_date' => null
        ]);

        $now = Carbon::parse($request->date);

        $month = null;
        $year = $now->year;
        
        switch ($now->month) {
            case 1:
                $month = 'January';
                break;

            case 2:
                $month = 'February';
                break;

            case 3:
                $month = 'March';
                break;

            case 4:
                $month = 'April';
                break;

            case 5:
                $month = 'May';
                break;
            
            case 6:
                $month = 'June';
                break;
            
            case 7:
                $month = 'July';
                break;

            case 8:
                $month = 'August';
                break;

            case 9:
                $month = 'September';
                break;

            case 10:
                $month = 'October';
                break;

            case 11:
                $month = 'November';
                break;

            case 12:
                $month = 'December';
                break;
            
        }
        

        $date = Carbon::parse($request->date);

        $charges = ClientUtility::where('client_id', $client->id)->where('status', 'completed')->whereMonth('created_at', Carbon::now()->month)->sum('amount');

        $otherBills = ClientPayment::where('client_id', $client->id)->where('status', 'unpaid')->get();

        $xxx = ClientPayment::where('client_id', $client->id)->whereYear('date', Carbon::now()->year)->where('status', 'unpaid')->get();

        $data = [
            'client' => $client,
            'pres' => $request->consumed_cubic_meter,
            'prev' => $bills->sum('consumed_cubic_meter'),
            'consumption' => $consumed_cubic_meter,
            'current_reading' => $waterBillAmount,
            'previous_bill' => $previousBill,
            'cost' => $waterBill->amount,
            'due_date' => $due_date,
            'total' => $xxx->sum('amount_to_pay'),
            'date' => $date->isoFormat('LL'),
            'reader' => $auth->name,
            'month' => $month, 
            'year' => $year,
            'count' => count($bills) . ' month(s)',
            'message' => count($bills) >= 2  ? "WARNING FOR DISCONNECTION. \r\n Please settle your balance." : '',
            'penalty' => $penalty,
            'charges' => $charges
        ]; 

        $users = User::where('reference', $request->reference)->get();

        $message = "BILLING FOR THIS MONTH \r\n %s %s - %s \r\n Amount Due: ₱ %s \r\n Due Date: %s. \r\n For more info please visit water billing system. \r\n https://water-billing-v2.onrender.com";
        $message = sprintf($message, $client->first_name, $client->last_name, $client->reference, $totalAmount + $penalty + $charges, $due_date->isoFormat('LL'));
        
        foreach($users as $user) {
            $this->sendSms($user->phone, $message);
        }

        return response()->json(['status' => 200, 'data' => $data, 'message' => $message], 200);  
    }

    public function markAsPaid(Request $request)
    {
        $auth = Auth::user();

        $message = null;

        $isPaid = false;

        $client_id = $request->client_id;
        $payment_amount = $request->paymentAmount;
        $month = $request->month;

        $client = Client::where('id', $client_id)->first();

        $description = $auth->first_name . ' ' . $auth->last_name . ' has mark as paid connection with serial #' . $client->serial . '.';

        $this->saveLogs($description);

        $previousPayment = ClientPayment::where('client_id', $client_id)->where('status', 'unpaid')->whereMonth('date', $month - 1)->first();

        if($previousPayment) {
            $message = 'Please pay your previous balance first.';
        }

        $payment = ClientPayment::where('client_id', $client_id)->where('status', 'unpaid')->whereMonth('date', $month)->first();
        $charges = ClientUtility::where('client_id', $client_id)->where('status', 'completed')->whereMonth('created_at', $month)->sum('amount');


        $history = [
            'client_id' => $payment->client_id,
            'consumed_cubic_meter' => $payment->consumed_cubic_meter,
            'amount' => $payment->amount,
            'payment' => $payment->payment,
            'penalty' => $payment->penalty,
            'penalty_payment' => $payment->penalty_payment,
            'date' => $payment->date,
            'status' => $payment->status,
            'payment_date' => $payment->payment_date,
            'history_payment' => $payment_amount
        ];

        $display = null;

        if(!$payment) {
            return response()->json(['status' => 200], 200);  
        }
        
        if($payment) {
            if($payment_amount == (($payment->amount - $payment->payment) + $payment->penalty + $charges)) {
                ClientUtility::where('client_id', $client_id)->where('status', 'completed')->whereMonth('created_at', $month)->update(['status' => 'paid']);

                $payment->payment = $payment->amount;
                $payment->penalty_payment = $payment->penalty;
                $payment->status = 'paid';
                $payment->payment_date = Carbon::now();
                $payment->save();

                $history['payment'] = $payment->amount;
                $history['penalty_payment'] = $payment->penalty;
                $history['status'] = 'paid';
                $history['payment_date'] = Carbon::now();

                PaymentHistory::forceCreate($history);

                $isPaid = true;
            } else {
                
                if($payment_amount < (($payment->amount - $payment->payment) + $charges)) {
                    if($payment_amount >= $charges) {
                        ClientUtility::where('client_id', $client_id)->where('status', 'completed')->whereMonth('created_at', $month)->update(['status' => 'paid']);

                        $payment->payment = ($payment_amount + $payment->payment) - $charges;
                        $payment->payment_date = Carbon::now();
                        $payment->save();


                        $history['payment'] = ($payment_amount + $payment->payment) - $charges;
                        $history['payment_date'] = Carbon::now();

                        PaymentHistory::forceCreate($history);
                    } else {
                        $message = 'Minimum amount must be equal to utility charges (' . $charges . ').';
                    } 
                    
                } else {
                    $message = 'Payment exceeded to existing balance.';
                }
            }

            $payments = ClientPayment::orderBy('date', 'desc')->where('client_id', $client_id)->get();
            $other_charges = ClientUtility::where('client_id', $client_id)->where('status', 'paid')->whereMonth('created_at', $month)->get();
            $chargesAmount = $other_charges->sum('amount');
            $stringCharges = $other_charges->pluck('display_service');

            $now = Carbon::now(); 

            $x = ClientPayment::where('client_id', $client_id)->where('status', 'unpaid')->get();

            $display = [
                'reference' => $client->reference,
                'name' => $client->fullname,
                'address' => $client->address,
                'serial' => $client->serial,
                'month' => $this->getMonth($month),
                'present' => count($payments) > 0 ? $payments[0]['consumed_cubic_meter'] : 0,
                'previous' => count($payments) > 1 ? $payments[1]['consumed_cubic_meter'] : 0,
                'charges' => implode(", ", $stringCharges->toArray()),
                'chargesAmount' => !!$isPaid ? 0 : $chargesAmount,
                'amount_to_pay' => $payment->amount_to_pay,
                'total' => $x->sum('amount_to_pay'), 
                'amount_paid' => $payment_amount,
                'penalty' => $payment->penalty,
                'cashier' => $auth->name,
                'now' => $now->isoFormat('LL')
            ];
        }

        return response()->json(['status' => 200, 'message' => $message, 'data' => $display], 200);  
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

                $utilities = $utilities->where('client_id', $client->id)->whereNotIn('status', ['paid']);
            } else {
                if($auth->user_type == 'utility') {
                    $utilities = $utilities->where('user_id', $auth->id)->whereNotIn('status', ['paid']);
                }
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

        $data = $request->except(['user_id']);
        $client = Client::where('reference', $auth->reference)->first();
        
        $admin = User::where('user_type', 'admin')->first();

        $date = Carbon::now();

        $payment = ClientPayment::orderBy('date')->where('client_id', $client->id)->whereMonth('date', $date->month)->first();

        if($payment && ($payment->status == 'paid' || $payment->status == 'PAID')){
            $dd = Carbon::parse($payment->date);

            $data['created_at'] = $date->year . '-' . ($date->month + 1) . '-' . $date->day;
            $data['updated_at'] = $date->year . '-' . ($date->month + 1) . '-' . $date->day;
        }

        $data['user_id'] = $admin->id;
        $data['client_id'] = $client->id;
        $data['status'] = 'pending';
        $data['amount'] = 0;

        ClientUtility::forceCreate($data);

        // $utilities = User::where('user_type', 'utility')->get();

        // foreach($utilities as $utility) {
            $message = "There's new incident report submitted from client with Line #: " . $client->reference . ". Please visit our system for more information.";

            $this->sendSms($admin->phone, $message);
        // }

        return response()->json(['status' => 200], 200);  
    }

    public function incidentReportChangeStatus(Request $request)
    {
        $auth = Auth::user();

        $utility = ClientUtility::where('id', $request->id)->first();
        
        $utilities = ClientUtility::orderBy('created_at', 'desc')->whereNotIn('status', ['paid']);

        $client = Client::where('id', $utility->client_id)->first();

        if($auth->role == 2) {
            $client = Client::where('reference', $auth->reference)->first();

            $utilities = $utilities->where('client_id', $client->id);
        }

        $now = Carbon::now();

        

        $payment = ClientPayment::where('client_id', $utility->client_id)->whereMonth('created_at', $now->month)->first();
        
        $utility->status = $request->status;
        $utility->amount = $request->amount; 

        // if($payment && ($payment->status == 'paid' || $payment->status == 'PAID')) {
        //     $date = Carbon::parse($utility->created_at);
        //     $date = $date->addMonth(1);
        //     $utility->created_at = $date;
        //     $utility->updated_at = $date;
        // }

        if($request->status == 'completed') {
            $user = User::where('reference', $client->reference)->first();

            $message = "Dear Client, \r\n Your water service is completed. \r\n Your service charge is ₱ %s. \r\n For more info please visit water billing system. \r\n https://water-billing-v2.onrender.com";
            $message = sprintf($message, $request->amount);

            $this->sendSms($user->phone, $message);
        }

        $utility->save();

        return response()->json(['status' => 200, 'utilities' => $utilities->get()], 200);  
    }

    public function getMonth($arg)
    {
        if($arg == 1) {
            return 'January';
        }

        if($arg == 2) {
            return 'February';
        }

        if($arg == 3) {
            return 'March';
        }

        if($arg == 4) {
            return 'April';
        }

        if($arg == 5) {
            return 'May';
        }

        if($arg == 6) {
            return 'June';
        }

        if($arg == 7) {
            return 'July';
        }
        
        if($arg == 8) {
            return 'August';
        }

        if($arg == 9) {
            return 'September';
        }

        if($arg == 10) {
            return 'October';
        }

        if($arg == 11) {
            return 'November';
        }

        if($arg == 12) {
            return 'December';
        }
    }

    public function getDays($date)
    {
        $date = Carbon::parse($date);

        $month = $date->month;

        $day = null;

        if($month == 1) {
            $day = 31;
        }

        if($month == 2) {
            $day = 28;
        }

        if($month == 3) {
            $day = 31;
        }

        if($month == 4) {
            $day = 30;
        }

        if($month == 5) {
            $day = 31;
        }

        if($month == 6) {
            $day = 30;
        }

        if($month == 7) {
            $day = 31;
        }

        if($month == 8) {
            $day = 31;
        }

        if($month == 9) {
            $day = 30;
        }

        if($month == 10) {
            $day = 31;
        }

        if($month == 11) {
            $day = 30;
        }

        if($month == 12) {
            $day = 31;
        }

        return $day;
    }

    public function activateConnection(Request $request)
    {
        CLient::where('id', $request->id)->update(['status' => 'activated']);

        return response()->json(['status' => 200], 200);  
    }

    public function voidReading(Request $request)
    {
        $latest = ClientPayment::orderBy('created_at', 'desc')->where('client_id', $request->client_id)->first();

        ClientPayment::where('id', $latest->id)->delete();

        return response()->json(['status' => 200], 200);
    }

    public function viewPayment(Request $request)
    {
        $payments = ClientPayment::where('client_id', $request->client_id)->whereYear('date', Carbon::now()->year)->get();
        $histories = PaymentHistory::orderBy('date')->where('client_id', $request->client_id)->whereYear('date', Carbon::now()->year)->get();
        $total = ClientPayment::where('client_id', $request->client_id)->whereYear('date', Carbon::now()->year)->where('status', 'unpaid')->get();
        $months = ClientPayment::where('client_id', $request->client_id)->whereYear('date', Carbon::now()->year)->where('status', 'unpaid')->get();

        return response()->json(['payments' => $payments, 'total' => $total->sum('amount_to_pay'), 'months' => $months->pluck('month'), 'histories' => $histories], 200);
    }

    public function assignWoker(Request $request)
    {
        ClientUtility::where('id', $request->id)->update($request->toArray());

        $worker = User::where('id', $request->user_id)->first();

        $ir = ClientUtility::where('id', $request->id)->first();

        $client = Client::where('id', $ir->client_id)->first();

        $date = Carbon::now();

        if($ir->description == "New Connection") {
            $message = "Dear " . $worker->name . ",\r\nWe have a water line installation on \r\n" . $date->isoFormat('LL') .  "\r\nSerial #:\r\n" . $client->serial . "\r\n" . "Name:\r\n" . $client->fullname . "\r\n" . "Address:\r\n" . $client->address . "\r\n";
            $clientMessage = "Dear Client,\r\n" . "Be inform that the schedule of the installation for your water meter will be on " . $date->isoFormat('LL') . ". Make sure that you're at home to avoid any problems in installing your water meter line.\r\nThank you!";
        } else {
            $message = "Dear " . $worker->name . ",\r\nThere was a client report \r\n" . "Serial #:\r\n" . $client->serial . "\r\n" . "Name:\r\n" . $client->fullname . "\r\n" . "Address:\r\n" . $client->address . "\r\n" . "Report:\r\n"  . $ir->description;
            $clientMessage = "Dear Client,\r\n" . "Good day!\r\nOur workers is now on their way to fixed your problem regarding your water line. Make sure that you're at home to avoid any problem in fixing your water line.\r\nThank you!";
        }

        $this->sendSms($worker->phone, $message);
        $this->sendSms($client->phone, $clientMessage);

        return response()->json(['status' => 200], 200);
    }

    public function saveReceipt(Request $request)
    {
        $client = Client::where('reference', $request->reference)->first();

        $payment = ClientPayment::orderBy('created_at', 'desc')->where('client_id', $client->id)->first();
        
        if($image = $request->image) {
            
            $path = public_path().'/images/uploads';

            $filename = time() . '_' . Str::random(8);

            $extension = $image->getClientOriginalExtension();
            
            $uplaod = $image->move($path, $filename . '.' . $extension);

            $image = $filename . '.' . $extension;

            $payment->receipt = $image;
            $payment->save();
        }

        return response()->json(['status' => $request->toArray()], 200);
    }
}
