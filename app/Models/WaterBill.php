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
        'amount'
    ];

    protected $appends = [
        'personnel',
        'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPersonnelAttribute()
    {
        return $this->user->name;
    }

    public function getDateAttribute()
    {
        $date = Carbon::parse($this->created_at);

        return $date->isoFormat('LL');  
    }
}
