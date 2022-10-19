<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class WaterBill extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'date'
    ];

    protected $appends = [
        'personnel'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPersonnelAttribute()
    {
        return $this->user->name;
    }

    public function getDateAttribute($value)
    {
        $date = Carbon::parse($value);

        return $date->isoFormat('LL');  
    }
}
