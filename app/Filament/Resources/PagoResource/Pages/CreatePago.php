<?php

namespace App\Filament\Resources\PagoResource\Pages;

use App\Filament\Resources\PagoResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Caja;
use Filament\Notifications\Notification;

class CreatePago extends CreateRecord
{
    protected static string $resource = PagoResource::class;

    protected function afterCreate(): void
    {
        try {
            Caja::create([
                'tipo_movimiento' => 'Ingreso',
                'origen' => 'pago',
                'descripcion' => 'Pago de ' . $this->record->tipo_pago,
                'monto' => $this->record->monto_pagado,
                'fecha' => $this->record->fecha_pago,
                'referencia_id' => $this->record->id,
                'referencia_tabla' => 'pagos',
            ]);

            Notification::make()
                ->title('Caja actualizada')
                ->body('Pago registrado en caja exitosamente.')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Error en caja')
                ->body('No se pudo registrar el pago en caja: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}
