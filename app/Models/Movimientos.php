<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movimientos extends Model
{
    protected $fillable = [
        'wallet_id',
        'mes',
        'detalle_ingreso',
        'ingreso',
        'detalle_gasto',
        'gasto',
    ];

    public function wallet()
    {
        return $this->belongsTo(Billeteras::class);
    }
}
