<?php

namespace App\Filament\Resources;

use App\Enums\AccidentStatus;
use App\Enums\AssociationEnum;
use App\Enums\VehicleType;
use App\Filament\Forms\Components\CpfTextInputComponent;
use App\Filament\Forms\Components\NameTextInputComponent;
use App\Filament\Forms\Components\PhoneTextInputComponent;
use App\Filament\Resources\AccidentResource\Pages;
use Webbingbrasil\FilamentCopyActions\Tables\Actions\CopyAction;
use App\Models\Accident;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Crypt;

class AccidentResource extends Resource
{
    protected static ?string $model = Accident::class;
    protected static ?string $navigationIcon = 'heroicon-o-link';
    protected static ?string $modelLabel = 'Vistorias Externa';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Associado')
                    ->columns(2)
                    ->schema([
                        Select::make('association')
                            ->label('Associação')
                            ->options(AssociationEnum::class)
                            ->columnSpanFull()
                            ->required(),
                        NameTextInputComponent::make()
                            ->columnSpan(2),
                        PhoneTextInputComponent::make(),
                        CpfTextInputComponent::make(),
                    ]),
                Section::make('Veículo')
                    ->columns(2)
                    ->schema([
                        Select::make('vehicle_type')
                            ->label('Tipo de Veículo')
                            ->options(VehicleType::class)
                            ->required(),
                        TextInput::make('plate')
                            ->label('Placa')
                            ->mask('***-****')
                            ->length(8)
                            ->required(),


                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('association')
                    ->label('Associação')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label('Telefone')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('cpf')
                    ->label('CPF')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('plate')
                    ->label('Placa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Criado há')
                    ->searchable()
                    ->sortable()
                    ->since(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    CopyAction::make('Copiar Link')
                        ->copyable(fn(Accident $record) => route('accident-expertise', ['encryptedKey' => Crypt::encrypt("{$record->plate}|{$record->created_at}")]))
                        ->successNotificationTitle('Link copiado com sucesso!'),
                    Action::make('validate_expertise')
                        ->label('Acompanhar')
                        ->icon('heroicon-o-eye')
                        ->color('info')
                        ->url(
                            fn(Accident $record): string => route('accident-expertise', [
                                'encryptedKey' => Crypt::encrypt("{$record->plate}|{$record->created_at}|true")
                            ]),
                            shouldOpenInNewTab: true
                        ),
                    Action::make('accident_download')
                        ->label('Download')
                        ->icon('heroicon-o-arrow-down-circle')
                        ->color('success')
                        ->url(fn(Accident $record): string => route('accident.download', $record->id), true)
                        ->hidden(fn(Accident $record): bool => $record->status === AccidentStatus::Pending),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    // Tables\Actions\Action::make('edit_status')
                    //     ->label('Editar Status')
                    //     ->color('warning')
                    //     ->form([
                    //         Select::make('status')
                    //             ->label('Status')
                    //             ->options(AccidentStatus::class)
                    //             ->required(),
                    //     ]),
                ]),
            ])
            ->recordUrl(null)
            ->poll('60s');
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
            'index' => Pages\ListAccidents::route('/'),
            'create' => Pages\CreateAccident::route('/create'),
            'edit' => Pages\EditAccident::route('/{record}/edit'),
        ];
    }
}
