<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'image',
        'phone',
        'email',
        'password',
        'user_type',
        'role',
        'is_active',
        'reference',
        'username',
        'is_change_password'
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'name',
        'status',
        'warning'
    ];

    public function getReferenceAttribute($val)
    {
        if(!$val) {
            return 'N/A';
        }

        return $val;
    }

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getStatusAttribute()
    {
        if($this->is_active) {
            return 'ACTIVE';
        }

        return 'INACTIVE';
    }

    public function getWarningAttribute()
    {
        if($this->user_type == 'client') {
            $client = Client::where('reference', $this->reference)->first();

            $payments = ClientPayment::where('client_id', $client->id)->where('status', 'unpaid')->get();

            if(count($payments) >= 3) {
                return true;
            }

            return false;
        }

        return false;
    }
}
