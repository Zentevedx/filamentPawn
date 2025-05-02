<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'nombre',
        'ci',
        'direccion',
        'telefono',
    ];
    
    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = strtoupper($value);
    }

    public function setCiAttribute($value)
    {
        $this->attributes['ci'] = strtoupper($value);
    }

    public function setDireccionAttribute($value)
    {
        $this->attributes['direccion'] = strtoupper($value);
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->nombre . ' (CI: ' . $record->ci . ')';
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Teléfono' => $record->telefono,
            'Dirección' => $record->direccion,
        ];
    }

    public static function getGlobalSearchAttributes(): array
    {
        return ['nombre', 'ci'];
    }
    public function prestamos()
{
    return $this->hasMany(Prestamo::class);
}

}
