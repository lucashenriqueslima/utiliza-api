<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CallResource\Pages;
use App\Filament\Resources\CallResource\RelationManagers;
use App\Models\Call;
use App\Models\Ileva\IlevaAssociate;
use App\Models\Ileva\IlevaAssociateVehicle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class CallResource extends Resource
{
    protected static ?string $model = Call::class;
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $modelLabel = 'Chamado';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('associate_id')
                    ->label('Associado')
                    ->placeholder('Selecione um associado')
                    ->searchable()
                    ->searchDebounce(250)
                    ->required()
                    ->live()
                    ->getSearchResultsUsing(fn (string $search): array => 
                        IlevaAssociate::select('hbrd_asc_associado.id', 'hbrd_asc_pessoa.nome')
                        ->join('hbrd_asc_pessoa', 'hbrd_asc_associado.id_pessoa', '=', 'hbrd_asc_pessoa.id')
                        ->orderBy('hbrd_asc_pessoa.nome')
                        ->where('hbrd_asc_pessoa.nome', 'like', "%" . strtoupper($search) . "%")
                        ->limit(50)
                        ->pluck('hbrd_asc_pessoa.nome', 'hbrd_asc_associado.id')
                        ->toArray()
                    )
                    ->getOptionLabelUsing(fn ($value) => IlevaAssociate::find($value)->person->nome ?? $value),
                Forms\Components\Select::make('associate_vehicle_id')
                    ->label('Veículo')
                    ->placeholder('Selecione um veículo')
                    ->required()
                    ->disabled(fn (Get $get) => !$get('associate_id'))
                    ->options(fn (Get $get): array => IlevaAssociateVehicle::where('id_associado', $get('associate_id'))
                                ->pluck('placa', 'id')
                                ->toArray())
                    ->getOptionLabelUsing(fn ($value) => IlevaAssociateVehicle::find($value)->placa ?? $value),                
                Forms\Components\TextInput::make('latitude')
                    ->label('Latitude')
                    ->required()
                    ->regex('/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'),
                Forms\Components\TextInput::make('longitude')
                    ->label('Longitude')
                    ->regex('/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('associateCar.associate.name')
                    ->label('Associado')
                    ->searchable()
                    ->configure()
                    ->sortable(),
                Tables\Columns\TextColumn::make('associateCar.plate')
                    ->label('Veículo')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data de Criação')
                    ->searchable()
                    ->sortable()
                    ->since(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')    
                    ->badge(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->multiple()
                    ->options([
                        'waiting_biker' => 'Buscando Motoboy',
                        'in_service' => 'Em Serviço',
                        'done' => 'Concluído',
                    ])
                    ->default(['waiting_biker', 'in_service'])
            ])->persistFiltersInSession()
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
            'index' => Pages\ListCalls::route('/'),
            'create' => Pages\CreateCall::route('/create'),
            'edit' => Pages\EditCall::route('/{record}/edit'),
        ];
    }
}
