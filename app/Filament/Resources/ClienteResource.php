<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClienteResource\Pages;
use App\Models\Cliente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Filament\GlobalSearch\Actions\Action as GlobalSearchAction;
use Filament\GlobalSearch\Actions\Action;

class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $recordTitleAttribute = 'nombre'; // 🔥 Esto habilita bien el Global Search

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre completo')
                    ->required()
                    ->maxLength(255)
                    ->extraInputAttributes(['style' => 'text-transform: uppercase']),
                Forms\Components\TextInput::make('ci')
                    ->label('CI')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(20)
                    ->extraInputAttributes(['style' => 'text-transform: uppercase']),
                Forms\Components\TextInput::make('direccion')
                    ->label('Dirección')
                    ->maxLength(255)
                    ->extraInputAttributes(['style' => 'text-transform: uppercase']),
                Forms\Components\TextInput::make('telefono')
                    ->label('Teléfono')
                    ->tel()
                    ->maxLength(20),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ci')
                    ->label('CI')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telefono')
                    ->label('Teléfono'),
                Tables\Columns\TextColumn::make('direccion')
                    ->label('Dirección'),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClientes::route('/'),
            'create' => Pages\CreateCliente::route('/create'),
            'edit' => Pages\EditCliente::route('/{record}/edit'),
        ];
    }

    // 🔥 GLOBAL SEARCH FUNCIONAL

    public static function getGlobalSearchAttributes(): array
    {
        return ['nombre', 'ci'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->nombre . ' (CI: ' . $record->ci . ')';
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Teléfono' => $record->telefono ?? '-',
            'Dirección' => $record->direccion ?? '-',
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
