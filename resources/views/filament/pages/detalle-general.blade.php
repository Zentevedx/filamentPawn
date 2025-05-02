<x-filament::page>

    @if ($recordType === 'cliente')
        <div class="space-y-4">
            <x-filament::card>
                <h2 class="text-xl font-bold">Detalles del Cliente</h2>
                <p><strong>Nombre:</strong> {{ $recordData->nombre }}</p>
                <p><strong>CI:</strong> {{ $recordData->ci }}</p>
                <p><strong>Teléfono:</strong> {{ $recordData->telefono }}</p>
                <p><strong>Dirección:</strong> {{ $recordData->direccion }}</p>
            </x-filament::card>

            <x-filament::card>
                <h2 class="text-xl font-bold">Préstamos Asociados</h2>
                
                @forelse ($prestamos as $prestamo)
                    <details class="border rounded p-4 mb-4">
                        <summary class="font-semibold cursor-pointer text-blue-600">
                            Código: {{ $prestamo->codigo }} | Monto: Bs {{ number_format($prestamo->monto, 2) }}
                        </summary>

                        <div class="mt-2 space-y-2">
                            <p><strong>Interés:</strong> Bs {{ number_format($prestamo->interes, 2) }}</p>
                            <p><strong>Estado:</strong> {{ $prestamo->estado }}</p>
                            <p><strong>Fecha de Préstamo:</strong> {{ \Carbon\Carbon::parse($prestamo->fecha_prestamo)->format('d/m/Y') }}</p>

                            <a href="{{ route('filament.pawn.resources.pagos.create', ['prestamo_id' => $prestamo->id]) }}"
    class="inline-flex items-center mt-2 text-primary-600 hover:underline">
    ➕ Registrar Pago
</a>


                            <details class="mt-4">
                                <summary class="text-green-600 font-semibold cursor-pointer">Artículos Prendados</summary>
                                <div class="pl-4 mt-2">
                                    @foreach ($prestamo->articulos as $articulo)
                                        <div class="border p-2 mb-2 rounded">
                                            <p><strong>Nombre:</strong> {{ $articulo->nombre_articulo }}</p>
                                            <p><strong>Estado:</strong> {{ $articulo->estado }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </details>

                            <details class="mt-4">
                                <summary class="text-purple-600 font-semibold cursor-pointer">Pagos Realizados</summary>
                                <div class="pl-4 mt-2">
                                    @foreach ($prestamo->pagos as $pago)
                                        <div class="border p-2 mb-2 rounded">
                                            <p><strong>Tipo de Pago:</strong> {{ $pago->tipo_pago }}</p>
                                            <p><strong>Monto:</strong> Bs {{ number_format($pago->monto_pagado, 2) }}</p>
                                            <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </details>

                        </div>
                    </details>
                @empty
                    <p>No hay préstamos asociados.</p>
                @endforelse

            </x-filament::card>
        </div>

    @elseif ($recordType === 'prestamo')
        <x-filament::card>
            <h2 class="text-xl font-bold">Detalles del Préstamo</h2>
            <p><strong>Código:</strong> {{ $recordData->codigo }}</p>
            <p><strong>Monto:</strong> Bs {{ number_format($recordData->monto, 2) }}</p>
            <p><strong>Interés:</strong> Bs {{ number_format($recordData->interes, 2) }}</p>
            <p><strong>Estado:</strong> {{ $recordData->estado }}</p>
            <p><strong>Fecha de Préstamo:</strong> {{ \Carbon\Carbon::parse($recordData->fecha_prestamo)->format('d/m/Y') }}</p>

            <div class="mt-4">
                <a href="{{ route('filament.pawn.resources.pagos.create', ['prestamo_id' => $prestamo->id]) }}"
    class="inline-flex items-center mt-2 text-primary-600 hover:underline">
    ➕ Registrar Pago
</a>

            </div>

            <details class="mt-6">
                <summary class="font-semibold cursor-pointer text-blue-600">Artículos Prendados</summary>
                <div class="pl-4 mt-2">
                    @foreach ($articulos as $articulo)
                        <div class="border p-2 mb-2 rounded">
                            <p><strong>Nombre:</strong> {{ $articulo->nombre_articulo }}</p>
                            <p><strong>Estado:</strong> {{ $articulo->estado }}</p>
                        </div>
                    @endforeach
                </div>
            </details>

            <details class="mt-4">
                <summary class="font-semibold cursor-pointer text-green-600">Pagos Registrados</summary>
                <div class="pl-4 mt-2">
                    @foreach ($pagos as $pago)
                        <div class="border p-2 mb-2 rounded">
                            <p><strong>Tipo de Pago:</strong> {{ $pago->tipo_pago }}</p>
                            <p><strong>Monto:</strong> Bs {{ number_format($pago->monto_pagado, 2) }}</p>
                            <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</p>
                        </div>
                    @endforeach
                </div>
            </details>

        </x-filament::card>

    @elseif ($recordType === 'articulo')
        <x-filament::card>
            <h2 class="text-xl font-bold">Detalles del Artículo</h2>
            <p><strong>Nombre:</strong> {{ $recordData->nombre_articulo }}</p>
            <p><strong>Estado:</strong> {{ $recordData->estado }}</p>
            @if ($recordData->foto_url)
                <img src="{{ asset('storage/' . $recordData->foto_url) }}" alt="Foto del artículo" class="w-32 mt-4 rounded">
            @endif
        </x-filament::card>

    @else
        <x-filament::card>
            <h2 class="text-red-500 font-bold text-xl">No se encontró el registro solicitado.</h2>
        </x-filament::card>
    @endif

</x-filament::page>
