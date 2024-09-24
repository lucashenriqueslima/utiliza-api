<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BikerResource\Pages;
use App\Filament\Resources\BikerResource\RelationManagers;
use App\Helpers\FormatHelper;
use App\Helpers\LinkGeneratorHelper;
use App\Models\Biker;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BikerResource extends Resource
{
    protected static ?string $model = Biker::class;

    protected static ?string $modelLabel = 'Motoqueiros';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('Motoboy | Telefone')
                    ->searchable()
                    ->sortable()
                    ->url(fn(Biker $record): string => LinkGeneratorHelper::whatsapp(FormatHelper::onlyNumbers($record->phone), "Olá {$record->name}"), true),
                TextColumn::make('status')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('locate_biker')
                        ->label('Localizar Motoboy')
                        ->icon('heroicon-o-map-pin')
                        ->color('info')
                        ->visible(fn(Biker $record): bool => $record->geolocation !== null)
                        ->url(fn(Biker $record): string => LinkGeneratorHelper::googleMaps($record->geolocation->location->longitude, $record->geolocation->location->latitude), true),
                ]),
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
            'index' => Pages\ListBikers::route('/'),
            'create' => Pages\CreateBiker::route('/create'),
            'edit' => Pages\EditBiker::route('/{record}/edit'),
        ];
    }
}
