<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Billeteras extends Model
{
    protected $fillable = [
        'user_id',
        'monto',
    ];
}
