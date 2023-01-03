<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ClientPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'amount',
        'status',
        'date',
        'consumed_cubic_meter'
    ];

    protected $appends = [
        "month",
        "due_date",
        'charges',
        'total',
        'amount_to_pay',
        'added_payment',
        'paid_charges'
    ];

    public function getMonthAttribute()
    {
        // $date = Carbon::parse($this->date);
        // $date = $date->subMonth(1);

        return date("F", strtotime($this->date));
    }

    public function getDueDateAttribute()
    {
        $date = Carbon::parse($this->date);
        $date = $date->addMonth(1);

        return $date->isoFormat('LL'); 
    }

    public function getStatusAttribute($value)
    {
        return strtoupper($value);
    }

    public function getChargesAttribute()
    {
        $month = Carbon::parse($this->date);

        $charges = ClientUtility::whereMonth('created_at', $month)->whereIn('status', ['completed'])->where('client_id', $this->client_id)->sum('amount');

        return $charges;
    }

    public function getPaidChargesAttribute()
    {
        $month = Carbon::parse($this->date);

        $charges = ClientUtility::whereMonth('created_at', $month)->whereIn('status', ['paid'])->where('client_id', $this->client_id)->sum('amount');

        return $charges;
    }

    public function getAddedPaymentAttribute()
    {
        return  $this->payment + $this->penalty_payment + $this->paid_charges;
    }

    public function getTotalAttribute()
    {
        return ($this->amount + $this->penalty + $this->charges);
    }

    public function getAmountToPayAttribute()
    {
        return ($this->amount + $this->penalty + $this->charges) - ($this->payment + $this->penalty_payment + $this->paid_charges);
    }

}
