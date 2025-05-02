<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasGlobalSearch;


class Articulo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'prestamo_id',
        'nombre_articulo',
        'descripcion',
        'estado',
        'foto_url',
    ];

    // Relaciones
    public function prestamo()
    {
        return $this->belongsTo(Prestamo::class);
    }

    // Mutador para guardar el nombre en mayúsculas
    public function setNombreArticuloAttribute($value)
    {
        $this->attributes['nombre_articulo'] = strtoupper($value);
    }
    public static function getGlobalSearchResultTitle(Model $record): string
{
    return $record->nombre_articulo;
}

public static function getGlobalSearchResultDetails(Model $record): array
{
    return [
        'Estado' => $record->estado,
    ];
}

public static function getGlobalSearchAttributes(): array
{
    return ['nombre_articulo'];
}

}
