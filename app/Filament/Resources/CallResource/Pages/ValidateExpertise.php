<?php

namespace App\Filament\Resources\CallResource\Pages;

use App\Enums\ExpertisePersonType;
use App\Enums\ExpertiseStatus;
use App\Enums\S3Prefix;
use App\Filament\Resources\CallResource;
use App\Models\Associate;
use App\Models\AssociateCar;
use App\Models\Call;
use App\Models\Expertise;
use App\Models\ExpertiseFile;
use App\Models\ExpertiseFormInput;
use App\Services\S3\S3Service;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class ValidateExpertise extends Page implements HasForms
{
    use InteractsWithRecord, InteractsWithForms;
    protected static string $resource = CallResource::class;

    protected static string $view = 'filament.resources.call-resource.pages.validate-expertise';

    protected static ?string $title = 'Validar Perícia';
    public ?array $data = [];
    public ?Collection $expertises;

    public Call $call;
    public AssociateCar $associateCar;
    public Associate $associate;

    public function mount(int|string $callId): void
    {
        $this->record = $this->resolveRecord($callId);

        DB::enableQueryLog();

        $this->expertises = $this->record->expertises()
            ->with([
                'files' => function ($query) {
                    $query->whereNull('is_approved');
                },
                'thirdParty',
                'thirdParty.car'
            ])
            ->where('status', ExpertiseStatus::Waiting)
            ->get();


        if ($this->expertises->isEmpty()) {
            Notification::make()
                ->title('Perícia já validada')
                ->warning()
                ->send();
            $this->redirect(CallResource::getUrl());

            return;
        }

        $this->associateCar = $this->record->associateCar()->first();
        $this->associate = $this->associateCar->associate()->first();

        $this->form->fill();
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
        $data = $this->form->getState();

        $this->expertises
            ->files
            ->each(function ($expertiseFile) use ($data) {
                $expertiseFile->update([
                    'is_approved' => $data["{$expertiseFile->expertise_id}.{$expertiseFile->id}.is_approved"],
                    'refusal_description' => $data["{$expertiseFile->expertise_id}.{$expertiseFile->id}.refusal_description"],
                ]);
            });

        $this->expertises->update([
            'status' => ExpertiseStatus::Done
        ]);

        if ($this->expertises->files->where('is_approved', false)->exists()) {
        }
    }

    public function getAssociateExpertiseForm(): ?Section
    {
        $associateExpertise = $this->expertises->where('person_type', ExpertisePersonType::Associate)
            ->first();

        if (!$associateExpertise) {
            return null;
        }
        $associateFormFields = array();

        $associateExpertise->files
            ->each(function ($associateExpertiseFile) use (&$associateFormFields, $associateExpertise) {
                $associateFormFields[] = Section::make($associateExpertiseFile->file_expertise_type->getLabel())
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
                                ->disabled(fn (Get $get) => ($get("{$associateExpertise->id}.{$associateExpertiseFile->id}.is_approved")))
                                ->required(fn (Get $get) => ($get("{$associateExpertise->id}.{$associateExpertiseFile->id}.is_approved") == false))
                                ->minLength(10)
                                ->maxLength(50)
                        ])
                    ]);
            });

        return Section::make($this->associate->name . ' - ' . $this->associateCar->plate)
            ->description('Associado')
            ->columns(2)
            ->collapsed()
            ->schema([
                ...$associateFormFields
            ]);
    }

    public function getThirdPartyExpertiseForms(): ?array
    {
        $thirdPartyExpertises = $this->expertises->where('person_type', ExpertisePersonType::ThirdParty);
        if ($thirdPartyExpertises->isEmpty()) {
            return null;
        }

        $thirdPartyExpertiseForms = array();

        $thirdPartyExpertises->each(function ($thirdPartyExpertise) use (&$thirdPartyExpertiseForms) {
            $thirdPartyExpertiseForms[] = Section::make(
                $thirdPartyExpertise->thirdParty->name . ' - ' . Str::remove('-', $thirdPartyExpertise->thirdParty->car->plate)
            )
                ->description('Terceiro')
                ->columns(2)
                ->collapsed()
                ->schema([
                    ...$thirdPartyExpertise->files
                        ->map(function ($thirdPartyFile) use ($thirdPartyExpertise) {
                            return
                                Section::make($thirdPartyFile->file_expertise_type->getLabel())
                                ->columns(2)
                                ->schema([
                                    ViewField::make("{$thirdPartyExpertise->id}.{$thirdPartyFile->id}.{$thirdPartyFile->file_expertise_type->value}_preview")
                                        ->view("filament.resources.call-resource.components.{$thirdPartyFile->file_expertise_type->getFileType()}-preview", [
                                            'url' => S3Service::getUrl($thirdPartyFile->path),
                                            'name' => $thirdPartyFile->file_expertise_type->getLabel(),
                                        ]),
                                    Group::make()->schema([
                                        ToggleButtons::make("{$thirdPartyExpertise->id}.{$thirdPartyFile->id}.{$thirdPartyFile->file_expertise_type->value}_is_approved")
                                            ->label('Aprovar?')
                                            ->boolean()
                                            ->grouped()
                                            ->default(false)
                                            ->live()
                                            ->required(),
                                        Textarea::make("{$thirdPartyExpertise->id}.{$thirdPartyFile->id}.{$thirdPartyFile->file_expertise_type->value}_refusal_description")
                                            ->label('Motivo da recusa')
                                            ->placeholder('Descreva o motivo da recusa...')
                                            ->disabled(fn (Get $get) => ($get("{$thirdPartyExpertise->id}.{$thirdPartyFile->id}.{$thirdPartyFile->file_expertise_type->value}_is_approved")))
                                            ->required(fn (Get $get) => ($get("{$thirdPartyExpertise->id}.{$thirdPartyFile->id}.{$thirdPartyFile->file_expertise_type->value}_is_approved") == false))
                                            ->minLength(10)
                                            ->maxLength(50)
                                    ])
                                ]);
                        })

                ]);
        });

        return $thirdPartyExpertiseForms;
    }
}
