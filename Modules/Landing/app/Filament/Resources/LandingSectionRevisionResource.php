<?php

namespace Modules\Landing\Filament\Resources;

use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Modules\Landing\Filament\Resources\LandingSectionRevisionResource\Pages;
use Modules\Landing\Models\LandingPage;
use Modules\Landing\Models\LandingPageSection;
use Modules\Landing\Models\LandingSectionRevision;
use Modules\Landing\Services\LandingRevisionService;

class LandingSectionRevisionResource extends Resource
{
    protected static ?string $model = LandingSectionRevision::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Revizyonlar';

    protected static ?string $navigationGroup = 'Sayfa Yönetimi';

    protected static ?int $navigationSort = 13;

    protected static ?string $modelLabel = 'Revizyon';

    protected static ?string $pluralModelLabel = 'Revizyonlar';

    public static function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('page.slug')
                    ->label('Sayfa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('section.key')
                    ->label('Bölüm')
                    ->searchable(),
                Tables\Columns\TextColumn::make('note')
                    ->label('Not')
                    ->searchable(),
                Tables\Columns\TextColumn::make('changedBy.email')
                    ->label('Değiştiren')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('page_id')
                    ->label('Sayfa')
                    ->options(fn () => LandingPage::query()->pluck('slug', 'id')->all()),
                Tables\Filters\SelectFilter::make('section_id')
                    ->label('Bölüm')
                    ->options(fn () => LandingPageSection::query()->pluck('key', 'id')->all()),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Başlangıç'),
                        Forms\Components\DatePicker::make('until')->label('Bitiş'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $q, $date): Builder => $q->whereDate('created_at', '>=', $date)
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $q, $date): Builder => $q->whereDate('created_at', '<=', $date)
                            );
                    }),
            ])
            ->defaultSort('id', 'desc')
            ->actions([
                Tables\Actions\Action::make('restore')
                    ->label('Geri Yükle')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (LandingSectionRevision $record): void {
                        $restoredSection = app(LandingRevisionService::class)->restoreRevision($record, auth()->id());
                        $sectionKey = $restoredSection?->key ?? $record->section?->key ?? 'bilinmiyor';
                        $previewUrl = static::previewUrl($restoredSection);

                        Notification::make()
                            ->title('Revizyon geri yüklendi')
                            ->body("Bölüm: {$sectionKey}")
                            ->actions($previewUrl ? [
                                NotificationAction::make('preview')
                                    ->label('Önizleme')
                                    ->url($previewUrl, shouldOpenInNewTab: true),
                            ] : [])
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Sil'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLandingSectionRevisions::route('/'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['note'];
    }

    protected static function previewUrl(?LandingPageSection $section): ?string
    {
        if (! $section) {
            return null;
        }

        $slug = $section->page?->slug;
        $sectionHash = '#'.$section->key;

        if ($slug === 'home' || empty($slug)) {
            return url('/').$sectionHash;
        }

        return url('/'.$slug).$sectionHash;
    }
}
