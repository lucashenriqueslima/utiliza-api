<?php

namespace App\Livewire;

use App\Enums\AccidentImageType;
use App\Enums\AccidentStatus;
use App\Models\Accident;
use App\Models\AccidentImage;
use App\Services\S3\S3Service;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Component;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Fieldset;
use Illuminate\Support\Facades\Crypt;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Wizard;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class AccidentExpertise extends Component implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    public Accident $accident;
    public bool $isFirstImageUpload = true;
    public bool $isInPreviewMode = false;


    public ?array $data = [];

    public function mount(string $encryptedKey)
    {
        try {
            $decryptedKey = explode('|', Crypt::decrypt($encryptedKey));

            $this->accident = Accident::where('plate', $decryptedKey[0])
                ->where('created_at', $decryptedKey[1])
                ->with(['images' => function ($query) {
                    $query->where('is_current', true);
                }])
                ->firstOrFail();

            $this->isInPreviewMode = $decryptedKey[2] ?? false;

            if ($this->accident->status === AccidentStatus::Finished && !$this->isInPreviewMode) {
                abort(404);
            }

            $this->form->fill();
        } catch (ModelNotFoundException $e) {
            abort(404);
        }
    }

    public function getFileUploadComponent(AccidentImageType $accidentImageType): FileUpload
    {
        return FileUpload::make($accidentImageType->value)
            ->label($accidentImageType->getLabel())
            ->image()
            ->columnSpanFull()
            ->default($this->accident->images->firstWhere('type', $accidentImageType)?->path)
            ->disabled($this->isInPreviewMode)
            ->downloadable($this->isInPreviewMode)
            ->afterStateUpdated(
                function (TemporaryUploadedFile $state) use ($accidentImageType) {

                    if ($this->isInPreviewMode) {
                        return;
                    }

                    if ($this->isFirstImageUpload) {
                        $this->accident->update([
                            'status' => AccidentStatus::InProgress,
                        ]);

                        $this->isFirstImageUpload = false;
                    }

                    Storage::disk('s3')->setVisibility($state->getPathname(), 'public');

                    AccidentImage::where('accident_id', $this->accident->id)
                        ->where('type', $accidentImageType)
                        ->where('is_current', true)
                        ->update(['is_current' => false]);

                    $this->accident->images()->create([
                        'path' => $state->getPathname(),
                        'type' => $accidentImageType,
                    ]);
                }
            )
            ->required();
    }

    public function getFieldsetComponent(string $label, array $accidentImageTypes): Fieldset
    {
        return Fieldset::make($label)
            ->columnSpan(2)
            ->extraAttributes([
                'class' => 'my-4',
            ])
            ->schema(
                array_map(
                    fn(AccidentImageType $accidentImageType) => $this->getFileUploadComponent($accidentImageType),
                    $accidentImageTypes
                )
            );
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Fotos Externas')
                        ->schema([
                            $this->getFieldsetComponent('Fotos da Frente', [
                                AccidentImageType::Front,
                                AccidentImageType::FrontLeft,
                                AccidentImageType::FrontRight,
                            ]),
                            $this->getFieldsetComponent('Fotos da Traseira', [
                                AccidentImageType::Rear,
                                AccidentImageType::RearLeft,
                                AccidentImageType::RearRight,
                            ]),
                            $this->getFieldsetComponent('Fotos das Laterais', [
                                AccidentImageType::Left,
                                AccidentImageType::Right,
                            ]),
                        ]),
                    Step::make('Fotos Internas')
                        ->schema([
                            $this->getFieldsetComponent('Porta-Malas', [
                                AccidentImageType::Trunk,
                                AccidentImageType::TrunkTire,
                            ]),
                            $this->getFileUploadComponent(AccidentImageType::Dashboard),
                        ]),
                    Step::make('Documentos')
                        ->schema([
                            $this->getFileUploadComponent(AccidentImageType::Crlv),
                            $this->getFileUploadComponent(AccidentImageType::Cnh),
                        ]),
                ])->submitAction(new HtmlString(Blade::render(<<<BLADE
                <x-filament::button
                    type="submit"
                    size="md"
                >
                    Concluir
                </x-filament::button>
            BLADE)))
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        Notification::make()
            ->title('Formulário enviado com sucesso!')
            ->body('Redirecionando para página Aaprovel...')
            ->success()
            ->persistent()
            ->send();

        $this->accident->update([
            'status' => AccidentStatus::Finished,
        ]);

        $this->dispatch('redirect');
    }
    public function render()
    {
        return view('livewire.accident-expertise');
    }
}
