<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticuloResource\Pages;
use App\Filament\Resources\ArticuloResource\RelationManagers;
use App\Models\Articulo;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Model; // no olvides este use arriba del archivo
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\GlobalSearch\Actions\Action as GlobalSearchAction;
use Filament\GlobalSearch\Actions\Action;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;




class ArticuloResource extends Resource
{
    protected static ?string $model = Articulo::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Select::make('prestamo_id')
                ->label('Préstamo asociado')
                ->relationship('prestamo', 'codigo')
                ->searchable()
                ->required(),

            Forms\Components\TextInput::make('nombre_articulo')
                ->label('Nombre del artículo')
                ->required()
                ->prefix('Marca - Modelo')
                ->maxLength(100)
                ->autocomplete('off')
                ->extraInputAttributes(['style' => 'text-transform: uppercase']),

            Forms\Components\Textarea::make('descripcion')
                ->label('Descripción')
                ->maxLength(65535)
                ->rows(4)
                ->nullable(),

      

Forms\Components\FileUpload::make('foto_url')
    ->label('Foto del artículo')
    ->image()
    ->imagePreviewHeight('250')
    ->directory('articulos')
    ->visibility('public')
    ->enableOpen()
    ->enableDownload()
    ->preserveFilenames(false)
    ->saveUploadedFileUsing(function ($file, $record): string {
    $manager = new ImageManager(new GdDriver());

    $image = $manager->read($file->getRealPath())
        ->scaleDown(width: 800)
        ->toJpeg(quality: 75);

    $codigo = $record->prestamo->codigo ?? 'NO-CODIGO';
    $nombre = str($record->nombre_articulo)->slug() ?? 'articulo';
    $fecha = now()->format('Ymd');
    $filename = "{$codigo}-{$nombre}-{$fecha}.jpg";

    Storage::disk('public')->put("articulos/{$filename}", (string) $image->toString());

    return "articulos/{$filename}";
})
,


            
        ]);
}


public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('nombre_articulo')
            ->label('Nombre del artículo')
            ->searchable(),

            Tables\Columns\TextColumn::make('prestamo.codigo')
                ->label('Código Préstamo')
                ->searchable(),


            Tables\Columns\TextColumn::make('estado')
                ->label('Estado')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Prendado' => 'warning',
                    'Retirado' => 'success',
                    'Vencido' => 'danger',
                    'Vendido' => 'primary',
                    default => 'gray',
                }),

            Tables\Columns\ImageColumn::make('foto_url')
                ->label('Foto')
                ->size(50)
                ->circular(),

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
            'index' => Pages\ListArticulos::route('/'),
            'create' => Pages\CreateArticulo::route('/create'),
            'edit' => Pages\EditArticulo::route('/{record}/edit'),
        ];
    }

// Agrega estos métodos en la clase ArticuloResource

public static function getGloballySearchableAttributes(): array
{
    return ['nombre_articulo'];
}

public static function getGlobalSearchResultTitle(Model $record): string
{
    return 'Artículo: ' . $record->nombre_articulo;
}

public static function getGlobalSearchResultDetails(Model $record): array
{
    return [
        'Estado' => $record->estado,
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
