<?php

namespace App\Filament\Resources;

use App\Enums\BillStatus;
use App\Filament\Resources\BillResource\Pages;
use App\Filament\Resources\BillResource\RelationManagers;
use App\Helpers\FormatHelper;
use App\Helpers\LinkGeneratorHelper;
use App\Models\Bill;
use App\Models\CallValue;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Support\RawJs;

class BillResource extends Resource
{
    protected static ?string $model = Bill::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $modelLabel = 'Cobrança';
    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('payment_vouncher_file_path')
                    ->label('Comprovante de pagamento')
                    ->directory('public/payment_vounchers')
                    ->acceptedFileTypes(['image/*', 'application/pdf'])
                    ->imageEditor()
                    ->maxSize(2048)
                    ->imageEditorAspectRatios([
                        null,
                        '16:9',
                        '4:3',
                        '1:1',
                    ])
                    ->columnSpanFull(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        BillStatus::Pending->value => 'Pendente',
                        BillStatus::Paid->value => 'Pago',
                        BillStatus::Canceled->value => 'Cancelado',
                    ])
                    ->columnSpanFull(),

                Textarea::make('description')
                    ->label('Descrição')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                Action::make('edit_call_value')
                    ->label('Editar valor do Chamado')
                    ->icon('heroicon-o-pencil')
                    ->form([
                        TextInput::make('value')
                            ->label('Valor')
                            ->mask('99,99')
                            ->prefix('R$')
                            ->default(fn (): string => (new CallValue())->getValidValueAttribute()),
                    ])
                    ->action(function (array $data): void {

                        $callValue = CallValue::create([
                            'value' => FormatHelper::number($data['value']),
                            'is_valid' => true,
                        ]);

                        CallValue::where('is_valid', true)
                            ->where('id', '!=', $callValue->id)
                            ->update(['is_valid' => false]);

                        Notification::make()
                            ->title('Valor do chamado atualizado com sucesso!')
                            ->success()
                            ->send();
                    })
                    ->modalSubmitActionLabel('Salvar')
            ])
            ->columns([
                TextColumn::make('call.biker.name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('call.biker.phone')
                    ->label('Telefone')
                    ->searchable()
                    ->sortable()
                    ->url(
                        fn (Bill $record): string => LinkGeneratorHelper::whatsapp(FormatHelper::onlyNumbers($record->call->biker->phone), "Olá {$record->call->biker->name}"),
                        shouldOpenInNewTab: true
                    ),
                TextColumn::make('call.biker.pix_key')
                    ->label('Chave PIX')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('value')
                    ->label('Valor')
                    ->money('BRL', locale: 'pt-BR')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label('Data de vencimento')
                    ->date('d/m/Y')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                ImageColumn::make('payment_vouncher_file_path')
                    ->label('Comprovante de pagamento')
                    ->alignCenter(),


            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                BulkAction::make('paid')
                    ->label('Marcar como pago')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->update(['status' => BillStatus::Paid])),
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
            'index' => Pages\ListBills::route('/'),
            // 'create' => Pages\CreateBill::route('/create'),
            // 'edit' => Pages\EditBill::route('/{record}/edit'),
        ];
    }
}
