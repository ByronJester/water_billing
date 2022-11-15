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
        'client_address'
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
}
