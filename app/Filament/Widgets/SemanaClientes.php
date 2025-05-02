<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\Prestamo;
use Filament\Widgets\Widget;

class SemanaClientes extends Widget
{
    protected static string $view = 'filament.widgets.semana-clientes';
    protected int|string|array $columnSpan = 'full';

    public function getViewData(): array
    {
        // Hoy en Bolivia
        $hoy = Carbon::today('America/La_Paz');

        // Si solo quieres 7 días usa range(0, 6)
        $dias = collect(range(0, 13))->map(fn ($i) => [
            'fecha'  => $hoy->copy()->addDays($i),
            'nombre' => Str::upper(
                $hoy->copy()->addDays($i)->locale('es')->isoFormat('dddd')
            ),
        ]);

        $prestamos = Prestamo::with(['pagos' => fn ($q) => $q->latest('fecha_pago')])
            ->select('id', 'codigo', 'monto', 'fecha_prestamo')
            ->get();

        return compact('dias', 'prestamos');
    }
}
