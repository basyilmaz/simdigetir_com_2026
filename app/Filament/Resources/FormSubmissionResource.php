<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FormSubmissionResource\Pages;
use App\Models\FormSubmission;
use App\Models\User;
use App\Support\BulkActionRateLimiter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FormSubmissionResource extends Resource
{
    protected static ?string $model = FormSubmission::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    protected static ?string $navigationLabel = 'Form Gönderileri';

    protected static ?string $modelLabel = 'Form Gönderisi';

    protected static ?string $pluralModelLabel = 'Form Gönderileri';

    protected static ?string $navigationGroup = 'Büyüme';

    protected static ?int $navigationSort = 33;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Genel Bilgiler')
                ->schema([
                    Forms\Components\TextInput::make('id')
                        ->label('ID')
                        ->disabled(),
                    Forms\Components\Select::make('form_definition_id')
                        ->label('Form')
                        ->relationship('formDefinition', 'title')
                        ->disabled(),
                    Forms\Components\TextInput::make('status')
                        ->label('Durum')
                        ->disabled(),
                    Forms\Components\Select::make('assigned_to')
                        ->label('Atanan')
                        ->options(fn (): array => User::query()->pluck('email', 'id')->all()),
                    Forms\Components\DateTimePicker::make('follow_up_at')
                        ->label('Takip Tarihi'),
                    Forms\Components\Textarea::make('internal_note')
                        ->label('İç Not')
                        ->rows(3)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('request_ip')
                        ->label('IP')
                        ->disabled(),
                    Forms\Components\TextInput::make('page_url')
                        ->label('Sayfa URL')
                        ->disabled(),
                    Forms\Components\DateTimePicker::make('created_at')
                        ->label('Gönderim Tarihi')
                        ->disabled(),
                ])->columns(2),

            Forms\Components\Section::make('Payload')
                ->schema([
                    Forms\Components\Placeholder::make('payload_pretty')
                        ->label('Veri (JSON)')
                        ->content(
                            fn (?FormSubmission $record): string => json_encode(
                                $record?->payload ?? [],
                                JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
                            ) ?: '{}'
                        ),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('formDefinition.title')
                    ->label('Form')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'received' => 'warning',
                        'processed' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('assignedUser.email')
                    ->label('Atanan')
                    ->toggleable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('follow_up_at')
                    ->label('Takip')
                    ->dateTime('d.m.Y H:i')
                    ->toggleable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('request_ip')
                    ->label('IP')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('page_url')
                    ->label('URL')
                    ->limit(50)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'received' => 'Alındı',
                        'processed' => 'İşlendi',
                        'failed' => 'Hata',
                    ]),
                Tables\Filters\Filter::make('follow_up_overdue')
                    ->label('Takibi Geciken')
                    ->query(fn (Builder $query): Builder => $query
                        ->whereNotNull('follow_up_at')
                        ->where('follow_up_at', '<=', now())
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Görüntüle'),
                Tables\Actions\Action::make('assign_me')
                    ->label('Bana Ata')
                    ->icon('heroicon-o-user')
                    ->color('info')
                    ->action(function (FormSubmission $record): void {
                        $record->update(['assigned_to' => auth()->id()]);

                        Notification::make()
                            ->title('Kayıt size atandı')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('mark_processed')
                    ->label('İşlendi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (FormSubmission $record): bool => $record->status !== 'processed')
                    ->action(function (FormSubmission $record): void {
                        $record->update(['status' => 'processed']);
                    }),
                Tables\Actions\Action::make('mark_failed')
                    ->label('Hata')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (FormSubmission $record): bool => $record->status !== 'failed')
                    ->action(function (FormSubmission $record): void {
                        $record->update(['status' => 'failed']);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_update_status')
                        ->label('Toplu Durum Güncelle')
                        ->icon('heroicon-o-flag')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('Durum')
                                ->required()
                                ->options([
                                    'received' => 'Alındı',
                                    'processed' => 'İşlendi',
                                    'failed' => 'Hata',
                                ]),
                        ])
                        ->action(function ($records, array $data): void {
                            if (! BulkActionRateLimiter::enforce('form-submissions.update-status')) {
                                return;
                            }

                            $records->each(fn (FormSubmission $record) => $record->update([
                                'status' => (string) $data['status'],
                            ]));
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFormSubmissions::route('/'),
            'view' => Pages\ViewFormSubmission::route('/{record}'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['status', 'request_ip', 'page_url', 'internal_note'];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['formDefinition', 'assignedUser']);
    }
}
