<?php

namespace App\Filament\Resources\CallResource\Pages;

use App\Enums\ExpertiseStatus;
use App\Filament\Resources\CallResource;
use App\Models\Associate;
use App\Models\AssociateCar;
use App\Models\Expertise;
use App\Models\ExpertiseFormInput;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Illuminate\Database\Eloquent\Collection;

class ValidateExpertise extends Page implements HasForms
{
    use InteractsWithRecord, InteractsWithForms;
    protected static string $resource = CallResource::class;

    protected static string $view = 'filament.resources.call-resource.pages.validate-expertise';

    protected static ?string $title = 'Validar Perícia';

    public ?array $data = [];
    public ?Expertise $expertise;
    public AssociateCar $associateCar;
    public Associate $associate;
    public Collection $expertiseFormInputs;
    public Collection $expertiseFiles;


    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->expertise = $this->record->expertises()
            ->where('status', ExpertiseStatus::Waiting)
            ->first();

        if (!$this->expertise) {
            Notification::make()
                ->title('Perícia já validada')
                ->warning()
                ->send();
            $this->redirect(CallResource::getUrl());

            return;
        }

        $this->associateCar = $this->record->associateCar()->first();
        $this->associate = $this->associateCar->associate()->first();
        $this->expertiseFormInputs = $this->expertise->formInputs()
            ->where('is_approved', null)
            ->get();
        $this->expertiseFiles = $this->expertise->files()
            ->where('is_approved', null)
            ->get();
    }

    public function form(Form $form): Form
    {
        return $form->schema(
            [
                Section::make($this->associate->name . ' - ' . $this->associateCar->plate)
                    ->description('Associado')
                    ->schema([
                        TextInput::make('report_text')
                            ->label('Observações')
                            ->placeholder('Observações')
                            ->columns(2)
                            ->default($this->expertiseFormInputs->where('field_type', 'report_text')->first()->value)
                            ->disabled(),

                        TextInput::make('report_text')
                            ->label('Observações')
                            ->placeholder('Observações')
                            ->columns(2)
                            ->default($this->expertiseFormInputs->where('field_type', 'report_text')->first()->value)
                            ->disabled()
                    ])
            ]
        );
    }

    public function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Salvar')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        // $this->expertise->formInputs()
        //     ->where('field_type', 'report_text')
        //     ->update(['is_approved' => true]);

        // $this->expertise->files()
        //     ->where('is_approved', null)
        //     ->update(['is_approved' => true]);

        // $this->expertise->update(['status' => ExpertiseStatus::Approved]);

        // Notification::make()
        //     ->title('Perícia validada com sucesso')
        //     ->success()
        //     ->send();

        // $this->redirect(CallResource::getUrl());
    }
}
