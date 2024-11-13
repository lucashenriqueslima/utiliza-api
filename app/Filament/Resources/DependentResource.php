<?php

namespace App\Filament\Resources;

use App\Enums\AssociationEnum;
use App\Filament\Resources\DependentResource\Pages;
use App\Filament\Resources\DependentResource\RelationManagers;
use App\Models\Dependent;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class DependentResource extends Resource
{
    protected static ?string $model = Dependent::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $modelLabel = 'Dependente';


    public static function canCreate(): bool
    {
        return false;
    }

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
            ->recordUrl(null)
            ->columns([
                TextColumn::make('association')
                    ->label('Associação')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Dependente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('associate.name')
                    ->label('Associado | Nome')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('associate.cpf')
                    ->label('Associado | CPF')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('benefit')
                    ->label('Benefício')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('situation')
                    ->label('Situação')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('contract_date')
                    ->label('Data do Contrato')
                    ->date()
                    ->searchable()
                    ->sortable(),

            ])
            ->filters([
                SelectFilter::make('association')
                    ->label('Associação')
                    ->multiple()
                    ->options(AssociationEnum::class),
                SelectFilter::make('benefit')
                    ->label('Benefício')
                    ->multiple()
                    ->options(fn() => Dependent::query()->pluck('benefit')->unique()->mapWithKeys(fn($benefit) => [$benefit => $benefit])->toArray()),
                SelectFilter::make('situation')
                    ->label('Situação')
                    ->multiple()
                    ->options(fn() => Dependent::query()->pluck('situation')->unique()->mapWithKeys(fn($situation) => [$situation => $situation])->toArray()),
                Filter::make('contract_date')
                    ->label('Data do Contrato')
                    ->form([
                        DatePicker::make('initial_date')
                            ->label('Data Inicial'),
                        DatePicker::make('final_date')
                            ->label('Data Final'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['initial_date'], fn($query, $initial_date) => $query->where('contract_date', '>=', $initial_date))
                            ->when($data['final_date'], fn($query, $final_date) => $query->where('contract_date', '<=', $final_date));
                    })
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])

            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
                ExportBulkAction::make()
                    ->label('Exportar')
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
            'index' => Pages\ListDependents::route('/'),
            'create' => Pages\CreateDependent::route('/create'),
            'edit' => Pages\EditDependent::route('/{record}/edit'),
        ];
    }
}
