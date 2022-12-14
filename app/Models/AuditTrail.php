<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditTrail extends Model
{
    use HasFactory;

    protected $with = [
        'user'
    ];

    protected $appends = [
        'user_name',
        'user_type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getUserNameAttribute()
    {
        return $this->user->first_name . ' ' . $this->user->last_name;
    }

    public function getUserTypeAttribute()
    {
        return ucfirst($this->user->user_type);
    }
}
