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
        'house_no',
        'street',
        'town',
        'province',
        'reference',
        'is_active',
        'penalty',
        'payment_date',
        'phone',
        'serial'
    ];

    protected $appends = [
        'name',
        'fullname',
        'amount_to_pay',
        'due_date',
        'payment_status',
        'other_fee',
        'total',
        'cubic_meter_consumed',
        'serial_display',
        'latest_consumed',
        'address',
        'display_created_at'
    ];

    public function getFullNameAttribute()
    {
        if(!!$this->middle_name) {
            return $this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name;
        }

        return $this->first_name . ' ' . $this->last_name;
    }

    public function getNameAttribute()
    {
        $name = $name = $this->first_name . ' ' . $this->last_name;

        return $name . ' - ' . $this->reference;
        
    }

    public function getAmountToPayAttribute()
    {
        $payments = ClientPayment::where('client_id', $this->id)->where('status', 'unpaid')->get();

        if(count($payments) > 0) {
            return $payments->sum('amount');
        }

        return 0;
        
    }

    public function getCubicMeterConsumedAttribute()
    {
        $payments = ClientPayment::where('client_id', $this->id)->where('status', 'unpaid')->get();

        if(count($payments) > 0) {
            return $payments->sum('consumed_cubic_meter');
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


    public function getPaymentStatusAttribute()
    {
        if($this->amount_to_pay > 0) {
            return 'Unpaid';
        } else {
            return 'Paid';
        }
    }

    public function getStatusAttribute($value)
    {
        return ucfirst($value);
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

    public function getSerialDisplayAttribute($value)
    {
        return $this->serial . ' - ' . $this->name;
    }

    public function getLatestConsumedAttribute($value)
    {
        $consumed_cubic_meter = ClientPayment::where('client_id', $this->id)->sum('consumed_cubic_meter');

        if($consumed_cubic_meter > 0) {
            return $consumed_cubic_meter;
        }

        return 'No previous reading';

        
    }

    public function getAddressAttribute()
    {
        return $this->house_no . ', ' . $this->street . ', ' . $this->town . ', ' . $this->province;
    }

    public function getDisplayCreatedAtAttribute()
    {
        $date = Carbon::parse($this->created_at);

        return $date->isoFormat('LL'); 
    }
}
