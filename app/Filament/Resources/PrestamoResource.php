<?php

namespace App\Filament\Resources;

use Illuminate\Database\Eloquent\Model; // no olvides este use arriba del archivo
use App\Filament\Resources\PrestamoResource\Pages;
use App\Filament\Resources\PrestamoResource\RelationManagers;
use App\Models\Prestamo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;
use Filament\GlobalSearch\Actions\Action as GlobalSearchAction;
use Filament\GlobalSearch\Actions\Action;


class PrestamoResource extends Resource
{
    protected static ?string $model = Prestamo::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                
    
                Forms\Components\TextInput::make('codigo')
                    ->label('Código')
                    ->required()
                    ->autocomplete('off')
                    ->unique(ignoreRecord: true)
                    ->maxLength(50)
                    ->extraInputAttributes(['style' => 'text-transform: uppercase']),
    
                Forms\Components\TextInput::make('monto')
                    ->label('Monto')
                    ->required()
                    ->numeric()
                    ->prefix('Bs')
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            // Calcula el 1% y lo asigna a multa_por_retraso
                            $set('multa_por_retraso', number_format($state * 0.01,0, '.', ''));
                        }
                    }),

                Forms\Components\Select::make('cliente_id')
                    ->label('Cliente')
                    ->relationship('cliente', 'nombre')
                    ->searchable()
                    ->required(),
    
                Forms\Components\TextInput::make('multa_por_retraso')
                    ->label('Multa por retraso')
                    ->numeric()
                    ->prefix('Bs')
                    ->default(0),
    
                Forms\Components\DatePicker::make('fecha_prestamo')
                    ->label('Fecha del Préstamo')
                    ->required(),
    
                Forms\Components\Select::make('estado')
                    ->label('Estado')
                    ->options([
                        'Activo' => 'Activo',
                        'Pagado' => 'Pagado',
                        'Vencido' => 'Vencido',
                    ])
                    ->default('Activo')
                    ->required(),
            ]);
    }
    



public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('codigo')
                ->label('Código')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('cliente.nombre')
                ->label('Cliente')
                ->searchable(),

                Tables\Columns\TextColumn::make('monto')
                ->label('Monto')
                ->formatStateUsing(fn ($state) => number_format($state, 0, '.', '.'))
                ->sortable(),
            

            Tables\Columns\TextColumn::make('estado')
                ->label('Estado')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Activo' => 'warning',
                    'Pagado' => 'success',
                    'Vencido' => 'danger',
                    default => 'gray',
                }),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Registrado')
                ->date('d-m-Y')
                ->sortable(),
        ])
        ->filters([])
        ->actions([
            Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPrestamos::route('/'),
            'create' => Pages\CreatePrestamo::route('/create'),
            'edit' => Pages\EditPrestamo::route('/{record}/edit'),
        ];
    }
    protected static function afterCreate($record): void
{
    try {
        \App\Models\Caja::create([
            'tipo_movimiento' => 'Ingreso',
            'origen' => 'prestamo',
            'descripcion' => 'Nuevo préstamo otorgado',
            'monto' => $record->monto,
            'fecha' => now()->toDateString(),
            'referencia_id' => $record->id,
            'referencia_tabla' => 'prestamos',
        ]);

        Notification::make()
            ->title('Caja actualizada')
            ->body('Movimiento en caja registrado exitosamente.')
            ->success()
            ->send();

    } catch (\Exception $e) {
        Notification::make()
            ->title('Error en caja')
            ->body('No se pudo registrar el movimiento en caja: ' . $e->getMessage())
            ->danger()
            ->send();
    }
}

// Agrega estos métodos en la clase PrestamoResource

public static function getGloballySearchableAttributes(): array
{
    return ['codigo'];
}

public static function getGlobalSearchResultTitle(Model $record): string
{
    return 'Préstamo: ' . $record->codigo;
}

public static function getGlobalSearchResultDetails(Model $record): array
{
    return [
        'Estado' => $record->estado,
        'Monto' => 'Bs ' . number_format($record->monto, 2),
    ];
}
public static function getGlobalSearchResultActions(Model $record): array
{
    return [
        Action::make('view')
    ->label('Ver Detalles')
    ->url(route('filament.pawn.pages.detalle-general', ['record' => $record->id]))

    ];
}

}
