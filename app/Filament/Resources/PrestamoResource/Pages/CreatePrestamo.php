<?php

namespace App\Filament\Resources\PrestamoResource\Pages;

use App\Filament\Resources\PrestamoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Caja;
use Filament\Notifications\Notification;


class CreatePrestamo extends CreateRecord
{
    protected static string $resource = PrestamoResource::class;
    protected function afterCreate(): void
    {
        try {
            Caja::create([
                'tipo_movimiento' => 'Egreso',
                'origen' => 'prestamo',
                'descripcion' => 'Nuevo préstamo otorgado',
                'monto' => $this->record->monto,
                'fecha' => now()->toDateString(),
                'referencia_id' => $this->record->id,
                'referencia_tabla' => 'prestamos',
            ]);

            Notification::make()
                ->title('Caja actualizada')
                ->body('Movimiento en caja registrado exitosamente.')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error en caja')
                ->body('No se pudo registrar el movimiento en caja: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}
