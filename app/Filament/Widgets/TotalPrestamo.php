<?php

namespace App\Filament\Widgets;

use App\Models\Prestamo;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Actions\Action;          //  ⬅ usa las acciones de Filament 3
use Illuminate\Support\Carbon;

class TotalPrestamo extends StatsOverviewWidget
{
     /* ------------  Ajustes básicos ----------------- */
     protected function getHeading(): string
     {
         return 'Resumen de préstamos';
     }
 
     public int $offset = 0;                 // 0 = mes actual
     protected int|string|array $columnSpan = 'full';
     protected static ?int $sort = 1;
 
     /* ------------  Botones de navegación ------------ */
     protected function getHeaderActions(): array
     {
         return [
             Action::make('prev')
                 ->label('Anterior')
                 ->icon('heroicon-o-chevron-left')
                 ->action(fn () => $this->offset++)      // retrocede un mes
                 ->color('gray'),
 
             Action::make('next')
                 ->label('Siguiente')
                 ->icon('heroicon-o-chevron-right')
                 ->action(fn () => $this->offset--)      // avanza
                 ->color('gray')
                 ->disabled(fn () => $this->offset === 0),
         ];
     }
 

    /* ------------------  ESTADÍSTICAS --------------- */
    protected function getStats(): array
    {
        /* Mes “ancla” = hoy menos $offset meses */
        $baseDate = now()->startOfMonth()->subMonths($this->offset);

        /* 1. TOTAL histórico (o filtra por estado si quieres) */
        $totalHistorico = Prestamo::sum('monto');

        /* 2. Totales de los 3 meses que comienzan en $baseDate */
        $meses = collect(range(0, 7))->map(function ($i) use ($baseDate) {
            $inicio = $baseDate->copy()->subMonths($i)->startOfMonth();
            $fin    = $inicio->copy()->endOfMonth();

            return [
                'label' => $inicio->translatedFormat('F Y'),     // “Abril 2025”
                'total' => Prestamo::whereBetween('fecha_prestamo', [$inicio, $fin])
                                   ->sum('monto'),
            ];
        })->reverse();  // para mostrar del más antiguo al más reciente

        /* 3. Armar los “Stat” */
        $stats = [
            Stat::make('Total prestado', $totalHistorico)
                ->description('Suma histórica')
                ->icon('heroicon-o-banknotes')
                ->color('primary')
        ];

        foreach ($meses as $mes) {
            $stats[] = Stat::make($mes['label'], $mes['total'])
                ->description('Prestado en el mes')
                ->icon('heroicon-o-calendar-days')
                ->color('success');
        }

        return $stats;  
    }
}
