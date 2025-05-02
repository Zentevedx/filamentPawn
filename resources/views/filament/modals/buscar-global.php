<x-filament::page>
    <h2 class="text-xl font-bold mb-4">Resultados de búsqueda: "{{ $query }}"</h2>

    <div class="space-y-6">

        @if($clientes->count())
            <div class="p-4 bg-gray-100 rounded-xl">
                <h3 class="font-semibold">Clientes</h3>
                <ul>
                    @foreach($clientes as $cliente)
                        <li class="mt-1">{{ $cliente->nombre }} (CI: {{ $cliente->ci }})</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($prestamos->count())
            <div class="p-4 bg-gray-100 rounded-xl">
                <h3 class="font-semibold">Préstamos</h3>
                <ul>
                    @foreach($prestamos as $prestamo)
                        <li class="mt-1">Código: {{ $prestamo->codigo }} - Bs {{ number_format($prestamo->monto, 2) }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($articulos->count())
            <div class="p-4 bg-gray-100 rounded-xl">
                <h3 class="font-semibold">Artículos</h3>
                <ul>
                    @foreach($articulos as $articulo)
                        <li class="mt-1">{{ $articulo->nombre_articulo }} (Estado: {{ $articulo->estado }})</li>
                    @endforeach
                </ul>
            </div>
        @endif

    </div>
</x-filament::page>
