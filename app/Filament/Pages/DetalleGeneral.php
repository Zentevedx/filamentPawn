<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Notifications\Notification;
use App\Models\Cliente;
use App\Models\Prestamo;
use App\Models\Articulo;
use Livewire\Attributes\Url;

class DetalleGeneral extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';
    protected static string $view = 'filament.pages.detalle-general';

    #[Url]
    public ?string $record = null;

    public $recordType;
    public $recordData;
    public $prestamos = [];
    public $articulos = [];
    public $pagos = [];

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    

    public function mount(): void
    {
        if (! $this->record) {
            Notification::make()
                ->title('Error')
                ->body('No se recibió un ID válido.')
                ->danger()
                ->send();
            abort(404);
        }

        // Buscar Cliente
        $cliente = Cliente::with('prestamos.articulos', 'prestamos.pagos')->find($this->record);
        if ($cliente) {
            $this->recordType = 'cliente';
            $this->recordData = $cliente;
            $this->prestamos = $cliente->prestamos;

            Notification::make()
                ->title('Cliente encontrado')
                ->body('Se cargó el cliente correctamente.')
                ->success()
                ->send();
            return;
        }

        // Buscar Préstamo
        $prestamo = Prestamo::with('articulos', 'pagos')->find($this->record);
        if ($prestamo) {
            $this->recordType = 'prestamo';
            $this->recordData = $prestamo;
            $this->articulos = $prestamo->articulos;
            $this->pagos = $prestamo->pagos;

            Notification::make()
                ->title('Préstamo encontrado')
                ->body('Se cargó el préstamo correctamente.')
                ->success()
                ->send();
            return;
        }

        // Buscar Artículo
        $articulo = Articulo::with('prestamo.cliente')->find($this->record);
        if ($articulo) {
            $this->recordType = 'articulo';
            $this->recordData = $articulo;

            Notification::make()
                ->title('Artículo encontrado')
                ->body('Se cargó el artículo correctamente.')
                ->success()
                ->send();
            return;
        }

        // Si no se encontró nada
        Notification::make()
            ->title('No encontrado')
            ->body('No se encontró ningún registro para el ID proporcionado.')
            ->danger()
            ->send();

        abort(404);
    }
}
