<?php

namespace App\Filament\Resources;

use App\Enums\AssociationEnum;
use App\Enums\AuvoDepartment;
use App\Filament\Resources\AuvoCollaboratorResource\Pages;
use App\Filament\Resources\AuvoCollaboratorResource\RelationManagers;
use App\Models\AuvoCollaborator;
use App\Models\AuvoWorkshop;
use App\Models\Ileva\IlevaWorkshop;
use App\Services\Auvo\AuvoAuthService;
use App\Services\Auvo\AuvoService;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Laravel\Octane\Facades\Octane;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Facades\DB;

class AuvoCollaboratorResource extends Resource
{
    protected static ?string $model = AuvoCollaborator::class;

    protected static ?string $modelLabel = 'Vistorias Interna';

    protected static ?string $navigationIcon = 'heroicon-o-wrench';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Repeater::make('workshops')
                    ->label('Oficinas')
                    ->addActionLabel('Adicionar oficina')
                    ->relationship()
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Select::make('association')
                            ->label('Associação')
                            ->placeholder('Selecione uma associação')
                            ->options(AssociationEnum::class)
                            ->live()
                            ->columnSpanFull(),

                        Select::make('ileva_id')
                            ->label('Oficina')
                            ->placeholder('Selecione um associado')
                            ->columnSpanFull()
                            ->searchable()
                            ->searchDebounce(300)
                            ->required()
                            ->live(onBlur: true)
                            ->disabled(fn(Get $get) => !$get('association'))
                            ->getSearchResultsUsing(
                                function (string $search, Get $get): array {

                                    $association = AssociationEnum::from($get('association'));

                                    return DB::connection($association->getDatabaseConnection())->table('hbrd_adm_store')
                                        ->select('id', 'nome', 'cnpj')
                                        ->where(function ($query) use ($search) {
                                            $query->where('nome', 'like', "%{$search}%")
                                                ->orWhere('cnpj', 'like', "%{$search}%");
                                        })
                                        ->orderBy('nome')
                                        ->limit(30)
                                        ->get()
                                        ->map(fn($workshop) => [(int) $workshop->id => "{$workshop->nome} - {$workshop->cnpj}"])
                                        ->toArray();
                                }
                            ),
                        TimePicker::make('visit_time')
                            ->label('Horário da visita')
                            ->columnSpan(1)
                            ->seconds(false)
                            ->required(),
                        Select::make('days_of_week')
                            ->label('Dias de atendimento na semana')
                            ->columnSpan(1)
                            ->multiple()
                            ->options([
                                '0' => 'Domingo',
                                '1' => 'Segunda-feira',
                                '2' => 'Terça-feira',
                                '3' => 'Quarta-feira',
                                '4' => 'Quinta-feira',
                                '5' => 'Sexta-feira',
                                '6' => 'Sábado',
                            ])
                            ->required(),


                    ])
                    ->itemLabel(function (array $state): ?string {

                        if (!$state['association']) {
                            return null;
                        }

                        $association = AssociationEnum::from($state['association']);
                        return DB::connection($association->getDatabaseConnection())->table('hbrd_adm_store')->find($state['ileva_id'])?->nome;
                    }),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Action::make('update_collaborators')
                    ->label('Atualizar colaboradores')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function (array $data): void {
                        try {
                            $accessToken = (
                                new AuvoAuthService(AuvoDepartment::Inspection)
                            )->getAccessToken();

                            $auvoService = new AuvoService($accessToken);

                            $collaborators = $auvoService->getUsers();

                            $tasks = [];

                            foreach ($collaborators as $collaborator) {
                                $tasks[] = function () use ($collaborator) {
                                    AuvoCollaborator::updateOrCreate(
                                        ['auvo_id' => $collaborator['userID']],
                                        [
                                            'name' => $collaborator['name'],
                                        ]
                                    );
                                };
                            }

                            Octane::concurrently($tasks);
                        } catch (\Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Erro ao atualizar colaboradores')
                                ->body($e->getMessage())
                                ->send();
                        }
                    })
                    ->modalSubmitActionLabel('Salvar')
            ])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime()
                    ->sortable()

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Gerenciar visitas'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuvoCollaborators::route('/'),
            'edit' => Pages\EditAuvoCollaborator::route('/{record}'),
        ];
    }
}
