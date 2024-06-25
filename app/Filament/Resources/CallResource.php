<?php

namespace App\Filament\Resources;

use App\Enums\CallStatus;
use App\Filament\Pages\ValidateExpertise;
use App\Filament\Resources\CallResource\Actions\ExtractCoordinatesFromGoogleMapsUrl;
use App\Filament\Resources\CallResource\Actions\ExtractCordinatesFromGoogleMapsUrl;
use App\Filament\Resources\CallResource\Pages;
use App\Helpers\FormatHelper;
use App\Models\Associate;
use App\Models\AssociateCar;
use App\Models\Call;
use App\Models\Ileva\IlevaAssociate;
use App\Models\Ileva\IlevaAssociateVehicle;
use App\Services\CallService;
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

use function PHPSTORM_META\map;

class CallResource extends Resource
{
    protected static ?string $model = Call::class;
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $modelLabel = 'Chamado';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('temp_associate_id')
                    ->live(),
                Hidden::make('temp_associate_car_id')
                    ->live(),
                Section::make()->columns(2)->schema([
                    Select::make('associate_id')
                        ->label('Associado')
                        ->placeholder('Selecione um associado')
                        ->searchable()
                        ->searchDebounce(300)
                        ->required(fn (Get $get) => (!$get('temp_associate_id')))
                        ->disabled(fn (Get $get) => ($get('temp_associate_id')))
                        ->live()
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
                        })
                        ->getSearchResultsUsing(
                            fn (string $search): array =>


                            IlevaAssociate::select('hbrd_asc_associado.id', 'hbrd_asc_pessoa.nome')
                                ->selectSub(function ($query) {
                                    $query->selectRaw('datediff(now(), ifnull(min(hbrd_finan_boleto.dt_vencimento), now()))')
                                        ->from('hbrd_finan_boleto')
                                        ->whereColumn('hbrd_finan_boleto.id_pessoa', 'hbrd_asc_pessoa.id')
                                        ->where('situacao', 'Aberto')
                                        ->where('hbrd_finan_boleto.dt_vencimento', '<', now())
                                        ->groupBy('hbrd_finan_boleto.id_pessoa');
                                }, 'days_without_payment')
                                ->join('hbrd_asc_pessoa', 'hbrd_asc_associado.id_pessoa', '=', 'hbrd_asc_pessoa.id')
                                ->orderBy('hbrd_asc_pessoa.nome')
                                ->where('hbrd_asc_pessoa.nome', 'like', "%" . $search . "%")
                                ->limit(50)
                                ->get()
                                ->mapWithKeys(fn ($associate) => [$associate->id => "{$associate->nome} | " . ($associate->days_without_payment ?? 'Regularizado')])
                                ->toArray()
                        )
                        ->getOptionLabelUsing(fn ($value) => IlevaAssociate::find($value)->person->nome ?? $value),
                    Select::make('associate_vehicle_id')
                        ->label('Veículo')
                        ->placeholder('Selecione um veículo')
                        ->required(fn (Get $get) => (!$get('temp_associate_id')))
                        ->disabled(fn (Get $get) => (!$get('associate_id') || $get('temp_associate_id')))
                        ->options(fn (Get $get): array => IlevaAssociateVehicle::where('id_associado', $get('associate_id'))
                            ->pluck('placa', 'id')
                            ->toArray())
                        ->getOptionLabelUsing(fn ($value) => IlevaAssociateVehicle::find($value)->placa ?? $value),
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

                            $coordinates = CallService::extractCoordinatesFromGoogleMapsUrl($state);

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
                        ->height(fn () => '400px') // map height (width is controlled by Filament options)
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
            ->defaultSort('status', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('Cód.')
                    ->searchable()
                    ->configure()
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
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        return $state;
                    }),
                TextColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                TextColumn::make('location')
                    ->label('Distância/Tempo')
                    ->sortable()
                    ->getStateUsing(function ($record) {

                        if ($record->biker_id == null) return null;

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


                        return str_replace('.', ',', number_format($distance * 4) . " min ($distance km)");
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
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->multiple()
                    ->options([
                        'searching_biker' => 'Buscando Motoboy',
                        'waiting_arrival' => 'Aguardando Chegada',
                        'in_service' => 'Em Serviço',
                        'waiting_validation' => 'Aguardando Aprovação',
                        'approved' => 'Aprovado',
                    ])
                    ->default(['searching_biker', 'waiting_arrival', 'waiting_validation', 'in_service'])
            ])
            ->actions([
                Action::make('validate_expertise')
                    ->label('Validar')
                    ->button()
                    ->icon('heroicon-o-eye')
                    ->color('danger')
                    ->url(fn (Call $record): string => self::getUrl('validate', ['callId' => $record]))
                    ->hidden(fn (Call $call): bool => $call->status != CallStatus::WaitingValidation)
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->poll('10s');
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
