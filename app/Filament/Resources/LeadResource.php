<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Support\BulkActionRateLimiter;
use App\Support\CsvExporter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Leads\Models\Lead;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static ?string $navigationLabel = 'Talepler';

    protected static ?string $modelLabel = 'Talep';

    protected static ?string $pluralModelLabel = 'Talepler';

    protected static ?string $navigationGroup = 'Müşteri Talepleri';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('İletişim Bilgileri')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Tip')
                            ->options([
                                'corporate_quote' => 'Kurumsal Teklif',
                                'courier_apply' => 'Kurye Başvuru',
                                'contact' => 'İletişim',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->label('İsim')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('company_name')
                            ->label('Firma Adı')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telefon')
                            ->tel()
                            ->required()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('email')
                            ->label('E-posta')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('message')
                            ->label('Mesaj')
                            ->rows(3)
                            ->maxLength(2000),
                    ])->columns(2),

                Forms\Components\Section::make('Durum Bilgileri')
                    ->icon('heroicon-o-flag')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Durum')
                            ->options([
                                'new' => '🆕 Yeni',
                                'contacted' => '📞 İletişime Geçildi',
                                'qualified' => '✅ Uygun',
                                'lost' => '❌ Kayıp',
                                'won' => '🎉 Kazanıldı',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notlar')
                            ->rows(3),
                    ])->columns(1),

                Forms\Components\Section::make('Kaynak Bilgileri')
                    ->icon('heroicon-o-link')
                    ->schema([
                        Forms\Components\TextInput::make('source')
                            ->label('Kaynak (utm_source)')
                            ->disabled(),
                        Forms\Components\TextInput::make('medium')
                            ->label('Ortam (utm_medium)')
                            ->disabled(),
                        Forms\Components\TextInput::make('campaign')
                            ->label('Kampanya (utm_campaign)')
                            ->disabled(),
                        Forms\Components\TextInput::make('page_url')
                            ->label('Sayfa URL')
                            ->disabled(),
                    ])->columns(2)
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tip')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'corporate_quote' => 'primary',
                        'courier_apply' => 'success',
                        'contact' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'corporate_quote' => 'Kurumsal',
                        'courier_apply' => 'Kurye',
                        'contact' => 'İletişim',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('name')
                    ->label('İsim')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company_name')
                    ->label('Firma')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'warning',
                        'contacted' => 'info',
                        'qualified' => 'success',
                        'lost' => 'danger',
                        'won' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'new' => 'Yeni',
                        'contacted' => 'İletişimde',
                        'qualified' => 'Uygun',
                        'lost' => 'Kayıp',
                        'won' => 'Kazanıldı',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('source')
                    ->label('Kaynak')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tip')
                    ->options([
                        'corporate_quote' => 'Kurumsal',
                        'courier_apply' => 'Kurye',
                        'contact' => 'İletişim',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'new' => 'Yeni',
                        'contacted' => 'İletişimde',
                        'qualified' => 'Uygun',
                        'lost' => 'Kayıp',
                        'won' => 'Kazanıldı',
                    ]),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_csv')
                    ->label('CSV Dışa Aktar')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(function () {
                        $rows = Lead::query()
                            ->latest()
                            ->cursor()
                            ->map(fn (Lead $lead): array => [
                                $lead->id,
                                $lead->type,
                                $lead->name,
                                $lead->phone,
                                $lead->email,
                                $lead->status,
                                optional($lead->created_at)->format('Y-m-d H:i:s'),
                            ]);

                        return CsvExporter::download(
                            filename: 'leads-' . now()->format('Ymd-His') . '.csv',
                            headers: ['ID', 'Tip', 'İsim', 'Telefon', 'E-posta', 'Durum', 'Tarih'],
                            rows: $rows
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Görüntüle'),
                Tables\Actions\EditAction::make()->label('Düzenle'),
                Tables\Actions\DeleteAction::make()->label('Sil'),
                Tables\Actions\RestoreAction::make()->label('Geri Yükle'),
                Tables\Actions\ForceDeleteAction::make()->label('Kalıcı Sil'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('update_status')
                        ->label('Durum Güncelle')
                        ->icon('heroicon-o-flag')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('Yeni Durum')
                                ->required()
                                ->options([
                                    'new' => 'Yeni',
                                    'contacted' => 'İletişimde',
                                    'qualified' => 'Uygun',
                                    'lost' => 'Kayıp',
                                    'won' => 'Kazanıldı',
                                ]),
                        ])
                        ->action(function ($records, array $data): void {
                            if (! BulkActionRateLimiter::enforce('leads.update-status')) {
                                return;
                            }

                            $records->each(fn (Lead $lead) => $lead->update([
                                'status' => $data['status'],
                            ]));
                        }),
                    Tables\Actions\DeleteBulkAction::make()->label('Sil'),
                    Tables\Actions\RestoreBulkAction::make()->label('Geri Yükle'),
                    Tables\Actions\ForceDeleteBulkAction::make()->label('Kalıcı Sil'),
                ]),
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
            'index' => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLead::route('/create'),
            'view' => Pages\ViewLead::route('/{record}'),
            'edit' => Pages\EditLead::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'phone', 'email', 'company_name'];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->select(['id', 'name', 'phone', 'email', 'company_name']);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'new')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
