<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;          // 👈  nuevo
use Illuminate\Support\Str;             // (por si usas más helpers)
use Filament\Models\Contracts\HasGlobalSearch;

class Prestamo extends Model
{
    /* ──────────── 1. CASTEOS y APPENDS ──────────── */
    protected $casts = [
        'fecha_prestamo'   => 'date',
        'fecha_vencimiento'=> 'date',
    ];

    protected $appends = ['fecha_base'];    // 👈  expone el accesor

    /* ──────────── 2. FILLABLE ──────────── */
    protected $fillable = [
        'cliente_id',
        'codigo',
        'monto',
        'interes',
        'fecha_prestamo',
        'fecha_vencimiento',
        'estado',
        'multa_por_retraso',
    ];

    /* ──────────── 3. RELACIONES ──────────── */
    public function cliente()   { return $this->belongsTo(Cliente::class); }
    public function articulos() { return $this->hasMany(Articulo::class); }
    public function pagos()     { return $this->hasMany(Pago::class); }

    /* ──────────── 4. MUTADOR DE CÓDIGO ──────────── */
    public function setCodigoAttribute($value)
    {
        $this->attributes['codigo'] = Str::upper($value);
    }

    /* ──────────── 5. ACCESOR fecha_base 👈 ──────────── */
    public function getFechaBaseAttribute(): Carbon
    {
        // Último pago, o null si no hay
        $ultimoPago = $this->pagos()
            ->latest('fecha_pago')
            ->value('fecha_pago');

        // Devuelve siempre Carbon: último pago → fecha_prestamo → hoy()
        return Carbon::parse($ultimoPago ?? $this->fecha_prestamo ?? now());
    }

    /* ──────────── 6. HOOK retrieved (opcional) ──────────── */
    protected static function booted()
    {
        static::retrieved(function ($prestamo) {
            if ($prestamo->estado !== 'Activo') {
                return;
            }

            if (now()->diffInMonths($prestamo->fecha_base) >= 3) {
                $prestamo->estado = 'Vencido';
                $prestamo->save();

                // Actualiza artículos
                $prestamo->articulos()->update(['estado' => 'Vencido']);
            }
        });
    }

    /* ──────────── 7. BÚSQUEDA GLOBAL (Filament) ──────────── */
    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return 'Código de Préstamo: ' . $record->codigo;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Monto'  => 'Bs ' . number_format($record->monto, 2),
            'Estado' => $record->estado,
        ];
    }

    public static function getGlobalSearchAttributes(): array
    {
        return ['codigo'];
    }
}
