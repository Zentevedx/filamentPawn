<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
use App\Models\Cliente;
use App\Models\Prestamo;
use App\Models\Articulo;

Route::get('/buscar-detalle/{type}/{id}', function ($type, $id) {
    if ($type === 'clientes') {
        $record = Cliente::with('prestamos.articulos', 'prestamos.pagos')->findOrFail($id);
        return view('filament.modals.detalle-global-search', compact('record'));
    }

    if ($type === 'prestamos') {
        $record = Prestamo::with('cliente', 'articulos', 'pagos')->findOrFail($id);
        return view('filament.modals.detalle-global-search', compact('record'));
    }

    if ($type === 'articulos') {
        $record = Articulo::with('prestamo.cliente')->findOrFail($id);
        return view('filament.modals.detalle-global-search', compact('record'));
    }

    abort(404);
});

