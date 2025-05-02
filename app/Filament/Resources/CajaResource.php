<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CajaResource\Pages;
use App\Filament\Resources\CajaResource\RelationManagers;
use App\Models\Caja;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CajaResource extends Resource
{
    protected static ?string $model = Caja::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Select::make('tipo_movimiento')
                ->label('Tipo de movimiento')
                ->options([
                    'Ingreso' => 'Ingreso',
                    'Egreso' => 'Egreso',
                ])
                ->required(),

            Forms\Components\Select::make('origen')
                ->label('Origen')
                ->options([
                    'gasto' => 'Gasto',
                    'aporte' => 'Aporte de capital externo',
                ])
                ->required(),

            Forms\Components\TextInput::make('descripcion')
                ->label('Descripción')
                ->required()
                ->maxLength(255)
                ->autocomplete('off')
                ->extraInputAttributes(['style' => 'text-transform: uppercase']),

            Forms\Components\TextInput::make('monto')
                ->label('Monto')
                ->required()
                ->numeric()
                ->minValue(0)
                ->autocomplete('off'),

            Forms\Components\DatePicker::make('fecha')
                ->label('Fecha del movimiento')
                ->required(),
        ]);
}


    public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('fecha')
                ->label('Fecha')
                ->date('d-m-Y')
                ->sortable(),

            Tables\Columns\TextColumn::make('tipo_movimiento')
                ->label('Movimiento')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Ingreso' => 'success',
                    'Egreso' => 'danger',
                    default => 'gray',
                }),

            Tables\Columns\TextColumn::make('origen')
                ->label('Origen'),

            Tables\Columns\TextColumn::make('descripcion')
                ->label('Descripción')
                ->wrap(),

            Tables\Columns\TextColumn::make('monto')
                ->label('Monto')
                ->formatStateUsing(fn ($state) => number_format($state, 2, '.', '.'))
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
            'index' => Pages\ListCajas::route('/'),
            'create' => Pages\CreateCaja::route('/create'),
            'edit' => Pages\EditCaja::route('/{record}/edit'),
        ];
    }
    
}
