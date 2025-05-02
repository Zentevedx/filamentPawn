<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Cliente;
use App\Models\Prestamo;
use App\Models\Articulo;

class BuscarGlobal extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';
    protected static string $view = 'filament.pages.buscar-global';

    public $clientes;
    public $prestamos;
    public $articulos;

    public function mount()
    {
        $this->clientes = Cliente::all();
        $this->prestamos = Prestamo::all();
        $this->articulos = Articulo::all();
    }
}
