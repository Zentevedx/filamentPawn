<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pago extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'prestamo_id',
        'tipo_pago',
        'monto_pagado',
        'fecha_pago',
    ];

    public function prestamo()
    {
        return $this->belongsTo(Prestamo::class);
    }
}
