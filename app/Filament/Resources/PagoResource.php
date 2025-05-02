<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PagoResource\Pages;
use App\Models\Pago;
use App\Models\Prestamo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PagoResource extends Resource
{
    protected static ?string $model = Pago::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        $prestamoId = request()->query('prestamo_id');

        return $form
            ->schema([
                Forms\Components\Select::make('prestamo_id')
                    ->label('Préstamo')
                    ->relationship('prestamo', 'codigo')
                    ->required()
                    // si vino prestamo_id en la URL, lo preselecciona:
                    ->default($prestamoId),

                Forms\Components\Select::make('tipo_pago')
                    ->label('Tipo de Pago')
                    ->options([
                        'Interes'  => 'Interés',
                        'Capital'  => 'Capital',
                    ])
                    ->required()
                    // sugerimos "Interes" por defecto
                    ->default('Interes'),

                Forms\Components\TextInput::make('monto_pagado')
                    ->label('Monto Pagado')
                    ->numeric()
                    ->required(),

                Forms\Components\DatePicker::make('fecha_pago')
                    ->label('Fecha de Pago')
                    ->required()
                    // fecha de hoy por defecto
                    ->default(now()->toDateString()),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('prestamo.codigo')
                    ->label('Código del préstamo')
                    ->searchable(),

                Tables\Columns\TextColumn::make('tipo_pago')
                    ->label('Tipo de pago')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Interes' => 'info',
                        'Capital' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('monto_pagado')
                    ->label('Monto pagado')
                    ->formatStateUsing(fn ($state) => number_format($state, 0, '.', '.'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('fecha_pago')
                    ->label('Fecha de pago')
                    ->date('d-m-Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registrado')
                    ->date('d-m-Y')
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPagos::route('/'),
            'create' => Pages\CreatePago::route('/create'),
            'edit' => Pages\EditPago::route('/{record}/edit'),
        ];
    }
    protected static function afterCreate($record): void
    {
        \App\Models\Caja::create([
            'tipo_movimiento' => 'Ingreso',
            'origen' => 'prestamo',
            'descripcion' => 'Nuevo préstamo otorgado',
            'monto' => $record->monto,
            'fecha' => now()->toDateString(),
            'referencia_id' => $record->id,
            'referencia_tabla' => 'prestamos',
        ]);
    }
    
    // Aquí la magia: calculamos automáticamente Capital e Interes
    protected static function mutateFormDataBeforeCreate(array $data): array
{
    $prestamo = \App\Models\Prestamo::find($data['prestamo_id']);
    $capitalPagado = $prestamo->pagos()->where('tipo_pago', 'Capital')->sum('monto_pagado');
    $capitalRestante = $prestamo->monto - $capitalPagado;
    $montoCliente = $data['monto_pagado'];

    if ($montoCliente >= $capitalRestante) {
        // Pago Capital completo
        $pagoCapital = \App\Models\Pago::create([
            'prestamo_id' => $prestamo->id,
            'tipo_pago' => 'Capital',
            'monto_pagado' => $capitalRestante,
            'fecha_pago' => $data['fecha_pago'],
        ]);

        \App\Models\Caja::create([
            'tipo_movimiento' => 'Ingreso',
            'origen' => 'pago',
            'descripcion' => 'Pago de Capital',
            'monto' => $capitalRestante,
            'fecha' => now()->toDateString(),
            'referencia_id' => $pagoCapital->id,
            'referencia_tabla' => 'pagos',
        ]);

        $interes = $montoCliente - $capitalRestante;

        if ($interes > 0) {
            $pagoInteres = \App\Models\Pago::create([
                'prestamo_id' => $prestamo->id,
                'tipo_pago' => 'Interes',
                'monto_pagado' => $interes,
                'fecha_pago' => $data['fecha_pago'],
            ]);

            \App\Models\Caja::create([
                'tipo_movimiento' => 'Ingreso',
                'origen' => 'pago',
                'descripcion' => 'Pago de Interes',
                'monto' => $interes,
                'fecha' => now()->toDateString(),
                'referencia_id' => $pagoInteres->id,
                'referencia_tabla' => 'pagos',
            ]);
        }

        $prestamo->estado = 'Pagado';
        $prestamo->save();

        foreach ($prestamo->articulos as $articulo) {
            $articulo->estado = 'Retirado';
            $articulo->save();
        }

    } else {
        $pagoInteres = \App\Models\Pago::create([
            'prestamo_id' => $prestamo->id,
            'tipo_pago' => 'Interes',
            'monto_pagado' => $montoCliente,
            'fecha_pago' => $data['fecha_pago'],
        ]);

        \App\Models\Caja::create([
            'tipo_movimiento' => 'Ingreso',
            'origen' => 'pago',
            'descripcion' => 'Pago de Interes',
            'monto' => $montoCliente,
            'fecha' => now()->toDateString(),
            'referencia_id' => $pagoInteres->id,
            'referencia_tabla' => 'pagos',
        ]);
    }

    return [];
}

}
