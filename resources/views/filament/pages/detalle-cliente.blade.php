<x-filament::page>
    <div class="space-y-4">
        <h1 class="text-2xl font-bold">Detalles del Cliente</h1>

        <div><strong>Nombre:</strong> {{ $cliente->nombre }}</div>
        <div><strong>CI:</strong> {{ $cliente->ci }}</div>
        <div><strong>Teléfono:</strong> {{ $cliente->telefono ?? '-' }}</div>
        <div><strong>Dirección:</strong> {{ $cliente->direccion ?? '-' }}</div>
    </div>
</x-filament::page>
