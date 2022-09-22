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
        'status'
    ];
}
