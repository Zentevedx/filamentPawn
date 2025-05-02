{{-- resources/views/filament/widgets/semana-clientes.blade.php --}}
@php use Illuminate\Support\Str; @endphp

<x-filament::widget>
    <div class="rounded-2xl bg-gradient-to-br from-slate-800 via-slate-700 to-slate-900 p-6 text-slate-100 shadow-lg">
        {{-- Mes actual en mayúsculas, p.e. ABRIL --}}
        <h2 class="text-3xl font-black text-center tracking-widest mb-8">
            {{ Str::upper(now()->locale('es')->isoFormat('MMMM')) }}
        </h2>

        {{-- Grid de 7 columnas fijas (scroll horizontal en pantallas estrechas) --}}
        <div class="grid grid-cols-7 gap-4 overflow-x-auto">
            @foreach ($dias as $dia)
                @php $isToday = $dia['fecha']->isToday(); @endphp

                <x-filament::card :class="[
                    'flex flex-col items-center space-y-3 p-4 transition',
                    $isToday ? 'bg-primary-600/70 ring-2 ring-primary-400' : 'bg-slate-700/60 hover:bg-slate-600/60',
                ]">
                    {{-- Día de la semana + número --}}
                    <h3 class="font-bold text-xl tracking-wide text-center">
                        {{ $dia['nombre'] }}
                        <span class="block text-4xl font-extrabold mt-1">
                            {{ $dia['fecha']->day }}
                        </span>
                    </h3>

                    <div class="w-full flex flex-col gap-1">
                        @foreach ($prestamos as $p)
                            @php
                                $mesDiff = 0;
                                for ($i = 1; $i <= 3; $i++) {
                                    if ($p->fecha_base->copy()->addMonths($i)->isSameDay($dia['fecha'])) {
                                        $mesDiff = $i;
                                        break;
                                    }
                                }

                                $color = match($mesDiff) {
                                    1 => 'success',   // verde
                                    2 => 'warning',   // amarillo
                                    3 => 'danger',    // rojo
                                    default => null,
                                };
                            @endphp

                            @if ($color)
                                <x-filament::button
    :color="$color"
    tag="a"
    :href="route('filament.pawn.pages.detalle-general', ['record' => $p->id])"
    size="sm"
    class="w-full justify-center whitespace-nowrap">
    {{ $p->codigo }} – {{ number_format($p->monto, 0, ',', '.') }}
</x-filament::button>
                            @endif
                        @endforeach
                    </div>
                </x-filament::card>
            @endforeach
        </div>
    </div>
</x-filament::widget>
