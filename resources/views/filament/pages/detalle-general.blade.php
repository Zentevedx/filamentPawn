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

        @php $deudaTotal = 0; @endphp

        <x-filament::card>
            <h2 class="text-xl font-bold">Préstamos</h2>

            @forelse ($prestamos as $prestamo)
                @php

                    $pagadoCapital = $prestamo->pagos->where('tipo_pago', 'Capital')->sum('monto_pagado');
                    $deuda = ($prestamo->monto + ($prestamo->multa_por_retraso ?? 0)) - $pagadoCapital;
                    $deuda = max(0, $deuda);
                    $deudaTotal += $deuda;

                    // Cálculo del retraso
                     $fechaPrestamo = \Carbon\Carbon::parse($prestamo->fecha_prestamo);
    $pagosInteres = $prestamo->pagos->where('tipo_pago', 'Interes');
    $mesesPagados = $pagosInteres->count();

    // La fecha de vencimiento se calcula sumando meses completos desde el inicio
    $fechaVencimiento = $fechaPrestamo->copy()->addMonths($mesesPagados + 1);

    // Cálculo de días de retraso
    $diasRetraso = (int) now()->diffInDays($fechaVencimiento, false); // negativo = ya pasó // negativo si hay retraso
                @endphp

                <x-filament::card class="bg-white shadow-sm border">
                    <h3 class="text-lg font-semibold">Código: {{ $prestamo->codigo }}</h3>
                    <p><strong>Monto:</strong> Bs {{ number_format($prestamo->monto, 2) }}</p>
                    <p><strong>Multa por retraso:</strong> Bs {{ number_format($prestamo->multa_por_retraso ?? 0, 2) }}</p>
                    <p><strong>Estado:</strong> {{ $prestamo->estado }}</p>
                    <p><strong>Fecha de Préstamo:</strong> {{ $fechaPrestamo->format('d/m/Y') }}</p>
                    <p><strong>Retraso:</strong>
    @if ($diasRetraso < 0)
        <span class="text-red-600 font-semibold">{{ abs($diasRetraso) }} días de retraso</span>
    @else
        <span class="text-green-600">Sin retraso</span>
    @endif
</p>

                    <p class="text-red-600 font-bold">Deuda Restante: Bs {{ number_format($deuda, 2) }}</p>

                    <a href="{{ route('filament.pawn.resources.pagos.create', ['prestamo_id' => $prestamo->id]) }}"
                       class="inline-flex items-center mt-2 text-primary-600 hover:underline">
                        ➕ Registrar Pago
                    </a>

                    <details class="mt-4">
                        <summary class="text-green-600 font-semibold cursor-pointer">Artículos</summary>
                        <div class="pl-4 mt-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($prestamo->articulos as $articulo)
                                <div class="border p-2 rounded">
                                    <p><strong>Nombre:</strong> {{ $articulo->nombre_articulo }}</p>
                                    <p><strong>Estado:</strong> {{ $articulo->estado }}</p>
                                    @if ($articulo->foto_url)
                                        <img src="{{ asset('storage/' . $articulo->foto_url) }}" class="w-32 h-32 object-cover mt-2 rounded" alt="Foto">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </details>

                    <details class="mt-4">
                        <summary class="text-purple-600 font-semibold cursor-pointer">Pagos</summary>
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
                </x-filament::card>
            @empty
                <p>No hay préstamos registrados.</p>
            @endforelse

            <div class="mt-6 text-lg font-bold text-red-700">
                💰 Deuda total del cliente: Bs {{ number_format($deudaTotal, 2) }}
            </div>
        </x-filament::card>
    </div>
@endif

</x-filament::page>
