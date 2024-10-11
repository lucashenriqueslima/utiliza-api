<?php

namespace App\Filament\Resources;

use App\Enums\AssociationEnum;
use App\Enums\CallStatus;
use App\Enums\ExpertiseStatus;
use App\Filament\Pages\ValidateExpertise;
use App\Filament\Resources\CallResource\Pages;
use App\Helpers\FormatHelper;
use App\Helpers\LinkGeneratorHelper;
use App\Models\Associate;
use App\Models\Call;
use App\Models\Ileva\IlevaAssociate;
use App\Models\Ileva\IlevaAssociatePerson;
use App\Models\Ileva\IlevaAssociateVehicle;
use App\Models\User;
use App\Services\CallService;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Cheesegrits\FilamentGoogleMaps\Fields\Geocomplete;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class CallResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Call::class;
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $modelLabel = 'Chamado';
    protected static ?int $navigationSort = 1;

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'update_responsable'
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('temp_associate_id')
                    ->live(),
                Hidden::make('temp_associate_car_id')
                    ->live(),
                Section::make()->columns(2)->schema([
                    Select::make('association')
                        ->label('Associação')
                        ->options(
                            AssociationEnum::getLabelsToCallResource()
                        )
                        ->columnSpanFull()
                        ->required(),
                    Select::make('associate_id')
                        ->label('Associado')
                        ->placeholder('Selecione um associado')
                        ->searchable()
                        ->searchDebounce(300)
                        ->required(fn(Get $get) => (!$get('temp_associate_id')))
                        ->disabled(fn(Get $get) => ($get('temp_associate_id')))
                        ->live()
                        ->getSearchResultsUsing(
                            fn(string $search): array =>


                            IlevaAssociatePerson::select('id', 'nome')
                                ->with([
                                    'ilevaAssociate:id,id_pessoa,id_situacao',
                                    'ilevaAssociate.ilevaSituation:id,nome'
                                ])
                                ->where('nome', 'like', "%{$search}%")
                                ->limit(50)
                                ->get()
                                ->mapWithKeys(fn($associate) => [$associate->id => "{$associate->nome} | {$associate->ilevaAssociate->ilevaSituation->nome}"])
                                ->toArray()
                        )
                        ->getOptionLabelUsing(fn($value) => IlevaAssociate::find($value)->person->nome ?? $value)
                        ->createOptionForm([
                            Section::make('Associado')
                                ->description('Caso o associado não esteja cadastrado, preencha os campos abaixo para criar um novo.')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('name')
                                        ->label('Nome')
                                        ->columnSpan(2)
                                        ->required(),
                                    TextInput::make('phone')
                                        ->label('Telefone')
                                        ->mask('(99) 99999-9999')
                                        ->length(15)
                                        ->required(),
                                    TextInput::make('cpf')
                                        ->label('CPF')
                                        ->mask('999.999.999-99')
                                        ->length(14)
                                        ->required(),
                                ]),
                            Section::make('Carro do Associado')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('model')
                                        ->label('Modelo')
                                        ->columnSpan(2)
                                        ->required(),
                                    TextInput::make('plate')
                                        ->label('Placa')
                                        ->required(),
                                    TextInput::make('brand')
                                        ->label('Marca')
                                        ->required(),
                                    TextInput::make('color')
                                        ->label('Cor')
                                        ->required(),
                                    TextInput::make('year')
                                        ->label('Ano')
                                        ->numeric()
                                        ->length(4)
                                        ->required(),
                                ]),

                        ])
                        ->createOptionUsing(function (array $data, Set $set): string {
                            $associate = Associate::create([
                                'name' => $data['name'],
                                'phone' => FormatHelper::onlyNumbers($data['phone']),
                                'cpf' => $data['cpf'],
                            ]);

                            $vehicle = $associate->car()->create([
                                'model' => $data['model'],
                                'plate' => $data['plate'],
                                'brand' => $data['brand'],
                                'color' => $data['color'],
                                'year' => $data['year'],
                            ]);

                            $set('temp_associate_id', $associate->id);
                            $set('temp_associate_car_id', $vehicle->id);

                            Notification::make()
                                ->title('Associado Criado')
                                ->success()
                                ->send();

                            return 'Associado Criado';
                        }),
                    Select::make('associate_vehicle_id')
                        ->label('Veículo')
                        ->placeholder('Selecione um veículo')
                        ->required(fn(Get $get) => (!$get('temp_associate_id')))
                        ->disabled(fn(Get $get) => (!$get('associate_id') || $get('temp_associate_id')))
                        ->options(fn(Get $get): array => IlevaAssociateVehicle::where('id_associado', $get('associate_id'))
                            ->pluck('placa', 'id')
                            ->toArray())
                        ->getOptionLabelUsing(fn($value) => IlevaAssociateVehicle::find($value)->placa ?? $value),
                ]),

                Section::make()->columns(1)->schema([
                    Textarea::make('observation')
                        ->label('Observação')
                        ->placeholder('Digite uma observação...')
                        ->autosize()
                ]),



                Section::make()->columns(1)->schema([

                    TextInput::make('link_google_maps')
                        ->label('Link Google Maps')
                        ->placeholder('Cole o link do Google Maps aqui...')
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set): void {

                            if (!$state) {
                                return;
                            }

                            $coordinates = (new CallService())->extractCoordinatesFromGoogleMapsUrl($state);

                            if (!$coordinates) {
                                Notification::make()
                                    ->title('Link inválido, confira e tente novamente')
                                    ->warning()
                                    ->send();

                                return;
                            }

                            Notification::make()
                                ->title('Coordenadas extraídas com sucesso!')
                                ->success()
                                ->send();

                            $set('location', $coordinates);
                        }),

                    Geocomplete::make('address')
                        ->placeholder('Digite um endereço...')
                        ->countries(['br'])
                        ->geolocateIcon('heroicon-o-map')
                        ->label('Endereço Completo')
                        ->required(),
                    Map::make('location')
                        ->mapControls([
                            'mapTypeControl'    => true,
                            'scaleControl'      => true,
                            'streetViewControl' => true,
                            'rotateControl'     => true,
                            'fullscreenControl' => true,
                            'searchBoxControl'  => false, // creates geocomplete field inside map
                            'zoomControl'       => false,
                        ])
                        ->height(fn() => '400px') // map height (width is controlled by Filament options)
                        ->defaultZoom(12) // default zoom level when opening form
                        ->autocomplete(
                            fieldName: 'address',
                            countries: ['BR']
                        ) // field on form to use as Places geocompletion field
                        ->autocompleteReverse(true) // reverse geocode marker location to autocomplete field
                        ->debug() // prints reverse geocode format strings to the debug console
                        ->defaultLocation([-16.6811204, -49.2567963]) // default for new forms
                        ->draggable() // allow dragging to move marker
                        ->clickable(true) // allow clicking to move marker
                        ->geolocate(true) // adds a button to request device location and set map marker accordingly
                        ->geolocateOnLoad(true, true)
                        ->label('Localização')
                ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                TextColumn::make('id')
                    ->label('Cód.')
                    ->searchable()
                    ->configure()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Responsável')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('association')
                    ->label('Associação')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('associateCar.associate.name')
                    ->label('Associado')
                    ->searchable()
                    ->configure()
                    ->sortable(),
                TextColumn::make('associateCar.associate.cpf')
                    ->label('Associado | CPF/CNPJ')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('associateCar.associate.phone')
                    ->label('Associado | Telefone')
                    ->searchable()
                    ->sortable()
                    ->url(fn(Call $record): string => LinkGeneratorHelper::whatsapp(FormatHelper::onlyNumbers($record->associateCar->associate->phone), "Olá {$record->associateCar->associate->name}"), true)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('associateCar.plate')
                    ->label('Associado | Placa')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('associateCar.model')
                    ->label('Modelo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('address')
                    ->label('Endereço')
                    ->searchable()
                    ->sortable()
                    ->limit(25)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        return $state;
                    })
                    ->url(fn(Call $record): string => LinkGeneratorHelper::googleMaps($record->location->longitude, $record->location->latitude), true),
                TextColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                TextColumn::make('estimated_time_arrival')
                    ->label('Tempo Estimado de Chegada')
                    ->getStateUsing(
                        fn(Call $record): ?string =>
                        $record->status == CallStatus::WaitingArrival ? $record->estimated_time_arrival : null
                    )
                    ->color(
                        fn(Call $record): string =>
                        Carbon::parse($record->estimated_time_arrival)->isPast() ? 'danger' : 'success'
                    )
                    ->weight('bold')
                    ->searchable()
                    ->sortable()
                    ->since(),
                TextColumn::make('location')
                    ->label('Distância/Tempo')
                    ->sortable()
                    ->getStateUsing(function ($record) {

                        if ($record->biker_id == null || $record->status == CallStatus::Approved) {
                            return null;
                        }

                        $rawDistance = DB::select(
                            'SELECT ST_Distance_Sphere(POINT(?, ?), bg.location) AS distance
                            FROM biker_geolocations AS bg
                            WHERE bg.biker_id = ?',
                            [
                                $record->location->longitude,
                                $record->location->latitude,
                                $record->biker_id
                            ]
                        );

                        $distance = number_format($rawDistance[0]->distance / 1000, 1);


                        return str_replace('.', ',', number_format($distance * 3.5) . " min ( " . $distance * 1.5 . " km)");
                    }),
                TextColumn::make('created_at')
                    ->label('Data de Criação')
                    ->searchable()
                    ->sortable()
                    ->since(),
                TextColumn::make('biker.name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('biker.phone')
                    ->label('Motoboy | Telefone')
                    ->searchable()
                    ->sortable()
                    ->url(fn(Call $record): ?string => $record->biker ? LinkGeneratorHelper::whatsapp(FormatHelper::onlyNumbers($record->biker?->phone), "Olá {$record->biker?->name}") : null, true)
                    ->toggleable(isToggledHiddenByDefault: true),
                // TextColumn::make('validated_by')
                //     ->label('Validado(s) por:')
                //     ->getStateUsing(function ($record) {
                //         return $record->whereHas('expertises', function ($query) {
                //             $query->where('status', ExpertiseStatus::Done);
                //         })->with('expertises.user')?->get()->pluck('expertises')
                //             ->flatten()
                //             ->pluck('user')
                //             ->pluck('name')
                //             ->unique()
                //             ->implode(', ');
                //     })
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->reorderable('sort')
            ->paginatedWhileReordering()
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->multiple()
                    ->options([
                        'searching_biker' => 'Buscando Motoboy',
                        'waiting_arrival' => 'Aguardando Chegada',
                        'in_service' => 'Em Serviço',
                        'in_validation' => 'Em Validação',
                        'waiting_biker_see_validation' => 'Aguardando Motoboy Ver Validação',
                        'waiting_validation' => 'Aguardando Aprovação',
                        'approved' => 'Aprovado',
                    ]),
                SelectFilter::make('user_id')
                    ->label('Responsável')
                    ->multiple()
                    ->options(
                        User::select('id', 'name')->get()->pluck('name', 'id')
                    )
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('change_biker')
                        ->label('Alterar Motoboy')
                        ->icon('heroicon-o-arrow-path-rounded-square')
                        ->color('primary')
                        ->form([
                            TextArea::make('reason_change_biker')
                                ->label('Motivo da Troca')
                                ->placeholder('Digite o motivo da troca de motoboy...')
                                ->required(),
                        ])
                        ->action(function (array $data, Call $record): void {

                            $record->expertises()->update([
                                'status' => ExpertiseStatus::ChangedBiker
                            ]);

                            $record->bikerChangeCalls()->create([
                                'biker_id' => $record->biker_id,
                                'user_id' => Auth::id(),
                                'reason' => $data['reason_change_biker'],
                            ]);

                            $record->biker_id = null;
                            $record->status = CallStatus::SearchingBiker;
                            $record->save();

                            Notification::make()
                                ->title('A busca de um novo motoboy foi iniciada')
                                ->success()
                                ->send();
                        })
                        ->hidden(fn(Call $call): bool => $call->status == CallStatus::Approved),
                    Action::make('locate_biker')
                        ->label('Localizar Motoboy')
                        ->icon('heroicon-o-map-pin')
                        ->color('info')
                        ->visible(fn(Call $record): bool => $record?->biker?->geolocation !== null)
                        ->url(fn(Call $record): string => LinkGeneratorHelper::googleMaps($record?->biker?->geolocation?->location?->longitude, $record?->biker?->geolocation?->location?->latitude), true),
                    Action::make('validate_expertise')
                        ->label('Validar')
                        ->icon('heroicon-o-eye')
                        ->color('danger')
                        ->url(fn(Call $record): string => self::getUrl('validate', ['callId' => $record]))
                        ->hidden(fn(Call $call): bool => !in_array($call->status, [CallStatus::WaitingValidation, CallStatus::InValidation])),
                    Action::make('call_download')
                        ->label('Download')
                        ->icon('heroicon-o-arrow-down-circle')
                        ->color('success')
                        ->url(fn(Call $record): string => route('call.download', $record->id), true)
                        ->hidden(fn(Call $call): bool => !in_array($call->status, [CallStatus::Approved])),
                ])
            ])
            ->bulkActions([
                BulkAction::make('change_user_responsable')
                    ->label('Trocar Responsável')
                    ->icon('heroicon-o-pencil')
                    ->color('info')
                    ->visible(fn(): bool => auth()->user()->can('update_responsable_call'))
                    ->requiresConfirmation()
                    ->form([
                        Select::make('user_id')
                            ->label('Responsável')
                            ->options(fn() => User::select('id', 'name')->get()->pluck('name', 'id'))
                            ->required(),
                    ])
                    ->action(fn(Collection $records, array $data) => $records->each(fn(Call $record) => $record->update(['user_id' => $data['user_id']]))),
            ])
            ->poll('25s');
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
            'validate' => Pages\ValidateExpertise::route('/{callId}/expertises/validate'),
        ];
    }
}
