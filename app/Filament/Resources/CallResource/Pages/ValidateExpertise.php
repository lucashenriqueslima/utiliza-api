<?php

namespace App\Filament\Resources\CallResource\Pages;

use App\Enums\CallStatus;
use App\Enums\ExpertiseFileType;
use App\Enums\ExpertiseFileValidationErrorStatus;
use App\Enums\ExpertisePersonType;
use App\Enums\ExpertiseStatus;
use App\Enums\ExpertiseType;
use App\Enums\S3Prefix;
use App\Filament\Resources\CallResource;
use App\Jobs\Call\SendExpertiseValidationErrorsPushNotificationJob;
use App\Jobs\Call\SendPushNotificationAfterValidationJob;
use App\Jobs\CallRequest\SendCallRequestPushNotificationJob;
use App\Models\Associate;
use App\Models\AssociateCar;
use App\Models\Call;
use App\Models\Expertise;
use App\Models\ExpertiseFile;
use App\Models\ExpertiseFileValidationError;
use App\Models\ExpertiseFormInput;
use App\Services\S3\S3Service;
use Filament\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Reactive;


class ValidateExpertise extends Page implements HasForms
{
    use InteractsWithRecord, InteractsWithForms;
    protected static string $resource = CallResource::class;

    protected static string $view = 'filament.resources.call-resource.pages.validate-expertise';

    protected static ?string $title = 'Validar Vistorias';
    public ?array $data = [];
    public ?Collection $expertises;
    public AssociateCar $associateCar;
    public Associate $associate;
    public array $expertiseAlreadyAnswered = [];

    public function getExpertisesQuery(): Relation | Builder
    {
        return $this->record->expertises()
            ->where('status', ExpertiseStatus::Waiting);
    }
    public function mount(int|string $callId): void
    {
        $this->record = $this->resolveRecord($callId);

        $this->expertises = $this->getExpertisesQuery()
            ->with([
                'files' => function ($query) {
                    $query->whereNull('is_approved')
                        ->where('file_expertise_type', '!=', ExpertiseFileType::DynamicImage);
                },
                'thirdParty',
                'thirdParty.car'
            ])
            ->get();

        if ($this->expertises->isEmpty()) {
            Notification::make()
                ->title('Perícia já validada')
                ->warning()
                ->send();
            $this->redirect(CallResource::getUrl());

            return;
        }

        if ($this->expertises
            ->whereNotNull('user_id')
            ->where('user_id', '!=', auth()->id())

            ->isNotEmpty()
        ) {
            Notification::make()
                ->title('Atenção, o chamado já está em processo de validação por outro usuário.')
                ->warning()
                ->persistent()
                ->send();
        }


        $this->expertises->toQuery()->update([
            'user_id' => auth()->id(),
        ]);

        $this->record->update([
            'status' => CallStatus::InValidation
        ]);

        $this->associateCar = $this->record->associateCar()->first();
        $this->associate = $this->associateCar->associate()->first();
    }

    public function getAssociateExpertiseForm(): Component
    {
        $associateExpertise = $this->expertises->where('person_type', ExpertisePersonType::Associate)
            ->first();

        if (!$associateExpertise) {
            return Hidden::make('');
        }
        $associateFormFields = [];

        $associateExpertise->files
            ->where('file_expertise_type', '!=', ExpertiseFileType::DynamicImage)
            ->whereNull('is_approved')
            ->each(function ($associateExpertiseFile) use (&$associateFormFields, $associateExpertise) {
                if ($associateExpertiseFile->file_expertise_type == ExpertiseFileType::DynamicImage) {
                    return;
                }

                $associateFormFields[] = Section::make("Associado | {$associateExpertiseFile->file_expertise_type->getLabel()}")
                    ->columns(2)
                    ->schema([
                        ViewField::make("{$associateExpertise->id}.{$associateExpertiseFile->id}.preview")
                            ->view("filament.resources.call-resource.components.{$associateExpertiseFile->file_expertise_type->getFileType()}-preview", [
                                'url' => S3Service::getUrl($associateExpertiseFile->path),
                                'name' => $associateExpertiseFile->file_expertise_type->getLabel(),
                            ]),
                        Group::make()->schema([
                            ToggleButtons::make("{$associateExpertise->id}.{$associateExpertiseFile->id}.is_approved")
                                ->label('Aprovar?')
                                ->boolean()
                                ->grouped()
                                ->default(false)
                                ->live()
                                ->required(),
                            Textarea::make("{$associateExpertise->id}.{$associateExpertiseFile->id}.refusal_description")
                                ->label('Motivo da recusa')
                                ->placeholder('Descreva o motivo da recusa...')
                                ->disabled(fn(Get $get) => ($get("{$associateExpertise->id}.{$associateExpertiseFile->id}.is_approved")))
                                ->required(fn(Get $get) => ($get("{$associateExpertise->id}.{$associateExpertiseFile->id}.is_approved") == false))
                        ])
                    ]);
            });

        return Section::make($this->associate->name . ' - ' . $this->associateCar->plate)
            ->description('Associado')
            ->columns(2)
            ->icon(fn(): string => in_array($associateExpertise->id, $this->expertiseAlreadyAnswered) ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-circle')
            ->collapsed()
            ->schema([
                ...$associateFormFields
            ]);
    }

    public function getThirdPartyExpertiseForms(): array
    {
        $thirdPartyExpertises = $this->expertises->where('person_type', ExpertisePersonType::ThirdParty);
        if ($thirdPartyExpertises->isEmpty()) {
            return [];
        }

        $thirdPartyExpertiseForms = [];

        $thirdPartyExpertises->each(function ($thirdPartyExpertise) use (&$thirdPartyExpertiseForms) {
            $thirdPartyExpertiseForms[] = Section::make(
                Str::upper($thirdPartyExpertise->thirdParty->name . ' - ' . Str::remove('-', $thirdPartyExpertise->thirdParty->car->plate))
            )
                ->description('Terceiro')
                ->columns(2)
                ->collapsed()
                ->icon(fn(): string => in_array($thirdPartyExpertise->id, $this->expertiseAlreadyAnswered) ? 'heroicon-o-check-circle' : 'heroicon-o-exclamation-circle')
                ->schema([
                    ...$thirdPartyExpertise->files
                        ->where('file_expertise_type', '!=', ExpertiseFileType::DynamicImage)
                        ->map(function ($thirdPartyFile) use ($thirdPartyExpertise) {
                            return
                                Section::make('Terceiro ' . Str::upper($thirdPartyExpertise->thirdParty->name . ' - ' . Str::remove('-', $thirdPartyExpertise->thirdParty->car->plate)) . " | {$thirdPartyFile->file_expertise_type->getLabel()}")
                                ->columns(2)
                                ->schema([
                                    ViewField::make("{$thirdPartyExpertise->id}.{$thirdPartyFile->id}.{$thirdPartyFile->file_expertise_type->value}_preview")
                                        ->view("filament.resources.call-resource.components.{$thirdPartyFile->file_expertise_type->getFileType()}-preview", [
                                            'url' => S3Service::getUrl($thirdPartyFile->path),
                                            'name' => $thirdPartyFile->file_expertise_type->getLabel(),
                                        ]),
                                    Group::make()->schema([
                                        ToggleButtons::make("{$thirdPartyExpertise->id}.{$thirdPartyFile->id}.is_approved")
                                            ->label('Aprovar?')
                                            ->boolean()
                                            ->grouped()
                                            ->default(false)
                                            ->live()
                                            ->required(),
                                        Textarea::make("{$thirdPartyExpertise->id}.{$thirdPartyFile->id}.refusal_description")
                                            ->label('Motivo da recusa')
                                            ->placeholder('Descreva o motivo da recusa...')
                                            ->disabled(fn(Get $get) => ($get("{$thirdPartyExpertise->id}.{$thirdPartyFile->id}.is_approved")))
                                            ->required(fn(Get $get) => ($get("{$thirdPartyExpertise->id}.{$thirdPartyFile->id}.is_approved") == false))
                                    ])
                                ]);
                        })

                ]);
        });

        return $thirdPartyExpertiseForms;
    }

    public function form(Form $form): Form
    {
        return $form->schema(
            [
                $this->getAssociateExpertiseForm(),
                ...$this->getThirdPartyExpertiseForms(),
            ]
        )
            ->statePath('data');
    }

    public function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Validar')
                ->submit('save'),
        ];
    }

    public function save(): void
    {

        if ($this->checkIfExistNewestsExpertisesWaitingForValidation()) {
            Notification::make()
                ->title('Atenção existem novas perícias para validação para este chamado.')
                ->warning()
                ->persistent()
                ->send();


            foreach ($this->expertises as $expertise) {
                $this->expertiseAlreadyAnswered[] = $expertise->id;
            };

            $this->expertises = $this->getExpertisesQuery()->get();

            return;
        }

        $data = $this->form->getState();

        ExpertiseFileValidationError::where('call_id', $this->record->id)
            ->update([
                'status' => 'expired'
            ]);

        $this->expertises
            ->each(function ($expertise) use ($data) {
                $expertise->files
                    ->where('file_expertise_type', '!=', ExpertiseFileType::DynamicImage)
                    ->whereNull('is_approved')
                    ->each(function ($file) use ($data, $expertise) {
                        if (!isset($data[$expertise->id][$file->id])) {
                            return;
                        }
                        $isApproved = $data[$expertise->id][$file->id]['is_approved'];
                        $refusalDescription = $data[$expertise->id][$file->id]['refusal_description'] ?? null;

                        if (!$isApproved) {
                            $file->validationErrors()
                                ->create([
                                    'call_id' => $this->record->id,
                                    'error_message' => $refusalDescription
                                ]);
                        }

                        $file->update([
                            'is_approved' => $isApproved,
                        ]);
                    });
            });


        $this->expertises->toQuery()->update([
            'status' => ExpertiseStatus::Done,
            'user_id' => auth()->id(),
        ]);


        $this->record->update([
            'status' => CallStatus::WaitingBikerSeeValidation
        ]);

        // SendPushNotificationAfterValidationJob::dispatch(
        //     $this->record,
        //     $this->record->biker->firebase_token
        // );

        Notification::make()
            ->title('Perícia validada com sucesso!')
            ->success()
            ->send();

        $this->redirect(CallResource::getUrl());
    }

    public function checkIfExistNewestsExpertisesWaitingForValidation(): bool
    {
        return $this->expertises->count() != $this->getExpertisesQuery()->count();
    }
}
