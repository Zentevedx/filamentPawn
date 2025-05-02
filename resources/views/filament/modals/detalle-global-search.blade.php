<div class="space-y-6">

    <div class="p-4 bg-gray-100 rounded-xl">
        <h2 class="text-xl font-bold mb-2">Cliente</h2>
        <p><strong>Nombre:</strong> {{ $record->nombre ?? '-' }}</p>
        <p><strong>CI:</strong> {{ $record->ci ?? '-' }}</p>
        <p><strong>Teléfono:</strong> {{ $record->telefono ?? '-' }}</p>
        <p><strong>Dirección:</strong> {{ $record->direccion ?? '-' }}</p>
    </div>

    @if($record->prestamos ?? false)
    <x-filament::accordion>
        @foreach($record->prestamos as $prestamo)
            <x-filament::accordion.item heading="Préstamo: {{ $prestamo->codigo }} ({{ $prestamo->estado }})">
                <div class="space-y-2">

                    <p><strong>Monto:</strong> Bs {{ number_format($prestamo->monto, 2) }}</p>
                    <p><strong>Fecha Préstamo:</strong> {{ \Carbon\Carbon::parse($prestamo->fecha_prestamo)->format('d-m-Y') }}</p>

                    {{-- Mostrar artículos --}}
                    @if($prestamo->articulos->count())
                    <x-filament::accordion>
                        @foreach($prestamo->articulos as $articulo)
                            <x-filament::accordion.item heading="Artículo: {{ $articulo->nombre_articulo }}">
                                <div class="p-2">
                                    <strong>Estado:</strong> {{ $articulo->estado }}<br>
                                    <strong>Descripción:</strong> {{ $articulo->descripcion ?? '-' }}
                                </div>
                            </x-filament::accordion.item>
                        @endforeach
                    </x-filament::accordion>
                    @endif

                    {{-- Mostrar pagos --}}
                    @if($prestamo->pagos->count())
                    <x-filament::accordion>
                        @foreach($prestamo->pagos as $pago)
                            <x-filament::accordion.item heading="Pago {{ $pago->tipo_pago }} - Bs {{ number_format($pago->monto_pagado, 2) }}">
                                <div class="p-2">
                                    <strong>Fecha de Pago:</strong> {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d-m-Y') }}
                                </div>
                            </x-filament::accordion.item>
                        @endforeach
                    </x-filament::accordion>
                    @endif

                </div>
            </x-filament::accordion.item>
        @endforeach
    </x-filament::accordion>
    @endif

</div>
