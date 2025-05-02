<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Caja extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tipo_movimiento',
        'origen',
        'descripcion',
        'monto',
        'fecha',
        'referencia_id',
        'referencia_tabla',
        'saldo',
    ];
}
