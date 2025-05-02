<?php

namespace App\Filament\Pages;

use App\Models\Cliente;
use Filament\Pages\Page;
use Livewire\Attributes\Url; // <-- Importante

class DetalleCliente extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static string $view = 'filament.pages.detalle-cliente';

    #[Url] // <-- Esto le dice que es un parámetro de URL
    public ?int $record = null;

    public ?Cliente $cliente = null;

    public function mount(): void
    {
        if ($this->record) {
            $this->cliente = Cliente::findOrFail($this->record);
        }
    }
}
