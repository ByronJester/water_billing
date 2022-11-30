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
        "due_date"
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
}
