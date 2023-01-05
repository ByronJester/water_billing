<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientUtility extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'description',
        'status',
        'amount'
    ];

    protected $appends = [
        'client_name',
        'client_address',
        'worker',
        'display_service'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function getClientNameAttribute()
    {
        return $this->client->first_name . ' ' . $this->client->last_name;
    }

    public function getClientAddressAttribute()
    {
        return $this->client->address;
    }

    public function getWorkerAttribute()
    {
        $worker = User::where('id', $this->user_id)->first();

        if($worker->user_type == 'admin') {
            return 'Unassigned';
        }

        return $worker->name;
    }

    public function getDisplayServiceAttribute()
    {
        return $this->description . " (₱ " . $this->amount . ")";
    }
}
