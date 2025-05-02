<x-filament::page>
    <div class="p-6 space-y-6">

        <h2 class="text-2xl font-bold mb-4">Buscar Global</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="p-4 border rounded-lg shadow bg-white">
                <h3 class="text-lg font-semibold mb-3">Clientes</h3>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($clientes as $cliente)
                        <li>{{ $cliente->nombre }} (CI: {{ $cliente->ci }})</li>
                    @endforeach
                </ul>
            </div>

            <div class="p-4 border rounded-lg shadow bg-white">
                <h3 class="text-lg font-semibold mb-3">Préstamos</h3>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($prestamos as $prestamo)
                        <li>Código: {{ $prestamo->codigo }} - Bs {{ number_format($prestamo->monto, 2) }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="p-4 border rounded-lg shadow bg-white">
                <h3 class="text-lg font-semibold mb-3">Artículos</h3>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($articulos as $articulo)
                        <li>{{ $articulo->nombre_articulo }} (Estado: {{ $articulo->estado }})</li>
                    @endforeach
                </ul>
            </div>

        </div>

    </div>
</x-filament::page>
