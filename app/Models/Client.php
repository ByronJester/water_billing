<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'address',
        'reference',
        'is_active',
        'penalty',
        'payment_date'
    ];

    protected $appends = [
        'name',
        'amount_to_pay',
        'due_date',
        'status',
        'other_fee',
        'total'
    ];

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getAmountToPayAttribute()
    {
        $payments = ClientPayment::where('client_id', $this->id)->where('status', 'unpaid')->get();

        if(count($payments) > 0) {
            return $payments->sum('amount');
        }

        return 0;
        
    }

    public function getOtherFeeAttribute()
    {
        $utilities = ClientUtility::where('client_id', $this->id)->where('status', 'completed')->get();

        if(count($utilities) > 0) {
            return $utilities->sum('amount');
        }

        return 0;
        
    }

    public function getDueDateAttribute()
    {
        $payment = ClientPayment::orderBy('date', 'desc')->where('client_id', $this->id)->where('status', 'unpaid')->first();

        if($payment) {
            return $payment->due_date;
        }

        return null;
    }


    public function getStatusAttribute()
    {
        if($this->amount_to_pay > 0) {
            return 'Unpaid';
        } else {
            return 'Paid';
        }
    }

    public function getPaymentDateAttribute($value)
    {
        if(!$value) return $value;
        
        $date = Carbon::parse($value);

        return $date->isoFormat('LL'); 
    }

    public function getTotalAttribute($value)
    {
        return $this->amount_to_pay + $this->penalty + $this->other_fee;
    }
}
