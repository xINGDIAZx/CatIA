<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    use HasFactory;

    protected $table = 'movimientos';

    protected $fillable = [
        'wallet_id',
        'mes',
        'detalle_ingreso',
        'ingreso',
        'detalle_gasto',
        'gasto'
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'wallet_id');
    }
}
