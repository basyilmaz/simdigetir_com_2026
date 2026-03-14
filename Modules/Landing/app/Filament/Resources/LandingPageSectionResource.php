<?php

namespace Modules\Landing\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Support\BulkActionRateLimiter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;
use Modules\Landing\Filament\Resources\LandingPageSectionResource\Pages;
use Modules\Landing\Models\LandingPage;
use Modules\Landing\Models\LandingPageSection;
use Modules\Landing\Services\LandingRevisionService;

class LandingPageSectionResource extends Resource
{
    protected static ?string $model = LandingPageSection::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    protected static ?string $navigationLabel = 'Bölümler';

    protected static ?string $modelLabel = 'Sayfa Bölümü';

    protected static ?string $pluralModelLabel = 'Bölümler';

    protected static ?string $navigationGroup = 'Sayfa Yönetimi';

    protected static ?int $navigationSort = 11;

    public const TEMPLATE_FIELDS = [
        'tpl_hero_badge_text',
        'tpl_hero_title_html',
        'tpl_hero_description_text',
        'tpl_hero_slide2_image_url',
        'tpl_hero_slide2_image_alt',
        'tpl_hero_slide2_image_srcset',
        'tpl_hero_slide2_image_sizes',
        'tpl_services_badge_text',
        'tpl_services_title_html',
        'tpl_services_subtitle_text',
        'tpl_faq_card_title_text',
        'tpl_faq_card_description_text',
        'tpl_corporate_cta_form_title_text',
        'tpl_corporate_cta_form_subtitle_text',
        'tpl_courier_cta_card_title_text',
        'tpl_courier_cta_card_description_text',
        'tpl_courier_cta_side_title_html',
    ];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('page_id')
                ->label('Sayfa')
                ->relationship('page', 'slug')
                ->searchable()
                ->preload()
                ->required(),
            Forms\Components\TextInput::make('key')
                ->label('Anahtar')
                ->required()
                ->maxLength(100)
                ->live(),
            Forms\Components\TextInput::make('type')
                ->label('Tip')
                ->required()
                ->maxLength(100),
            Forms\Components\TextInput::make('title')
                ->label('Başlık')
                ->maxLength(255),
            Forms\Components\TextInput::make('sort_order')
                ->label('Sıra')
                ->numeric()
                ->default(0)
                ->required(),
            Forms\Components\Placeholder::make('template_hint')
                ->label('Şablon')
                ->content(function (Get $get): string {
                    $sectionKey = (string) ($get('key') ?? '');
                    return match ($sectionKey) {
                        'hero' => 'Hero bölümü payload alanları aşağıda aktif.',
                        'services' => 'Hizmetler bölümü payload alanları aşağıda aktif.',
                        'faq' => 'SSS bölümü payload alanları aşağıda aktif.',
                        'corporate_cta' => 'Kurumsal CTA bölümü payload alanları aşağıda aktif.',
                        'courier_cta' => 'Kurye CTA bölümü payload alanları aşağıda aktif.',
                        default => 'Genel payload modu. Payload JSON veya anahtar-değer çifti kullanın.',
                    };
                }),
            Forms\Components\Section::make('Şablon Alanları')
                ->schema([
                    Forms\Components\TextInput::make('tpl_hero_badge_text')
                        ->label('Hero Rozet Metni')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['hero'])),
                    Forms\Components\TextInput::make('tpl_hero_title_html')
                        ->label('Hero Başlık HTML')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['hero'])),
                    Forms\Components\Textarea::make('tpl_hero_description_text')
                        ->label('Hero Açıklama')
                        ->rows(2)
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['hero'])),
                    Forms\Components\FileUpload::make('tpl_hero_slide2_image_url')
                        ->label('Hero Slayt 2 Görsel')
                        ->image()
                        ->disk('public')
                        ->directory('landing/hero')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['hero'])),
                    Forms\Components\TextInput::make('tpl_hero_slide2_image_alt')
                        ->label('Hero Slayt 2 Alt Yazı')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['hero'])),
                    Forms\Components\TextInput::make('tpl_hero_slide2_image_srcset')
                        ->label('Hero Slayt 2 Srcset')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['hero'])),
                    Forms\Components\TextInput::make('tpl_hero_slide2_image_sizes')
                        ->label('Hero Slayt 2 Sizes')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['hero'])),
                    Forms\Components\TextInput::make('tpl_services_badge_text')
                        ->label('Hizmetler Rozet Metni')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['services'])),
                    Forms\Components\TextInput::make('tpl_services_title_html')
                        ->label('Hizmetler Başlık HTML')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['services'])),
                    Forms\Components\Textarea::make('tpl_services_subtitle_text')
                        ->label('Hizmetler Alt Başlık')
                        ->rows(2)
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['services'])),
                    Forms\Components\TextInput::make('tpl_faq_card_title_text')
                        ->label('SSS Kart Başlığı')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['faq'])),
                    Forms\Components\Textarea::make('tpl_faq_card_description_text')
                        ->label('SSS Kart Açıklaması')
                        ->rows(2)
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['faq'])),
                    Forms\Components\TextInput::make('tpl_corporate_cta_form_title_text')
                        ->label('Kurumsal Form Başlığı')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['corporate_cta'])),
                    Forms\Components\TextInput::make('tpl_corporate_cta_form_subtitle_text')
                        ->label('Kurumsal Form Alt Başlığı')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['corporate_cta'])),
                    Forms\Components\TextInput::make('tpl_courier_cta_card_title_text')
                        ->label('Kurye Kart Başlığı')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['courier_cta'])),
                    Forms\Components\TextInput::make('tpl_courier_cta_card_description_text')
                        ->label('Kurye Kart Açıklaması')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['courier_cta'])),
                    Forms\Components\TextInput::make('tpl_courier_cta_side_title_html')
                        ->label('Kurye Yan Başlık HTML')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['courier_cta'])),
                ])
                ->columns(2)
                ->visible(fn (Get $get): bool => static::isSectionKey($get, ['hero', 'services', 'faq', 'corporate_cta', 'courier_cta'])),
            Forms\Components\Textarea::make('payload_json')
                ->label('Veri Yükü (JSON)')
                ->rows(7)
                ->placeholder('{"hero_badge_text":"7/24 Aktif Hizmet"}'),
            Forms\Components\KeyValue::make('payload')
                ->label('Veri Yükü')
                ->keyLabel('Anahtar')
                ->valueLabel('Değer')
                ->addButtonLabel('Veri Ekle'),
            Forms\Components\Toggle::make('is_active')
                ->label('Aktif')
                ->default(true)
                ->required(),
        ]);
    }

    public static function buildPayloadFromFormData(array $data): array
    {
        $sectionKey = (string) ($data['key'] ?? '');
        $payload = is_array($data['payload'] ?? null) ? $data['payload'] : [];

        $payloadJson = trim((string) ($data['payload_json'] ?? ''));
        if ($payloadJson !== '') {
            $decoded = json_decode($payloadJson, true);
            if (! is_array($decoded) || json_last_error() !== JSON_ERROR_NONE) {
                throw ValidationException::withMessages([
                    'payload_json' => 'Payload JSON formatı geçersiz.',
                ]);
            }

            $payload = array_merge($payload, $decoded);
        }

        $templatePayload = match ($sectionKey) {
            'hero' => static::heroPayloadFromTemplate($data),
            'services' => static::servicesPayloadFromTemplate($data),
            'faq' => static::faqPayloadFromTemplate($data),
            'corporate_cta' => static::corporateCtaPayloadFromTemplate($data),
            'courier_cta' => static::courierCtaPayloadFromTemplate($data),
            default => [],
        };

        $data['payload'] = array_merge($payload, $templatePayload);
        unset($data['payload_json']);
        foreach (self::TEMPLATE_FIELDS as $field) {
            unset($data[$field]);
        }

        return $data;
    }

    public static function injectTemplateFieldsFromPayload(array $data): array
    {
        $sectionKey = (string) ($data['key'] ?? '');
        $payload = is_array($data['payload'] ?? null) ? $data['payload'] : [];

        $data['payload_json'] = ! empty($payload)
            ? json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
            : null;

        if ($sectionKey === 'hero') {
            $data['tpl_hero_badge_text'] = $payload['hero_badge_text'] ?? null;
            $data['tpl_hero_title_html'] = $payload['hero_title_html'] ?? null;
            $data['tpl_hero_description_text'] = $payload['hero_description_text'] ?? null;
            $data['tpl_hero_slide2_image_url'] = $payload['hero_slide2_image_url'] ?? null;
            $data['tpl_hero_slide2_image_alt'] = $payload['hero_slide2_image_alt'] ?? null;
            $data['tpl_hero_slide2_image_srcset'] = $payload['hero_slide2_image_srcset'] ?? null;
            $data['tpl_hero_slide2_image_sizes'] = $payload['hero_slide2_image_sizes'] ?? null;
        }

        if ($sectionKey === 'services') {
            $data['tpl_services_badge_text'] = $payload['services_badge_text'] ?? null;
            $data['tpl_services_title_html'] = $payload['services_title_html'] ?? null;
            $data['tpl_services_subtitle_text'] = $payload['services_subtitle_text'] ?? null;
        }

        if ($sectionKey === 'faq') {
            $data['tpl_faq_card_title_text'] = $payload['faq_card_title_text'] ?? null;
            $data['tpl_faq_card_description_text'] = $payload['faq_card_description_text'] ?? null;
        }

        if ($sectionKey === 'corporate_cta') {
            $data['tpl_corporate_cta_form_title_text'] = $payload['corporate_cta_form_title_text'] ?? null;
            $data['tpl_corporate_cta_form_subtitle_text'] = $payload['corporate_cta_form_subtitle_text'] ?? null;
        }

        if ($sectionKey === 'courier_cta') {
            $data['tpl_courier_cta_card_title_text'] = $payload['courier_cta_card_title_text'] ?? null;
            $data['tpl_courier_cta_card_description_text'] = $payload['courier_cta_card_description_text'] ?? null;
            $data['tpl_courier_cta_side_title_html'] = $payload['courier_cta_side_title_html'] ?? null;
        }

        return $data;
    }

    protected static function heroPayloadFromTemplate(array $data): array
    {
        return static::filtered([
            'hero_badge_text' => $data['tpl_hero_badge_text'] ?? null,
            'hero_title_html' => $data['tpl_hero_title_html'] ?? null,
            'hero_description_text' => $data['tpl_hero_description_text'] ?? null,
            'hero_slide2_image_url' => $data['tpl_hero_slide2_image_url'] ?? null,
            'hero_slide2_image_alt' => $data['tpl_hero_slide2_image_alt'] ?? null,
            'hero_slide2_image_srcset' => $data['tpl_hero_slide2_image_srcset'] ?? null,
            'hero_slide2_image_sizes' => $data['tpl_hero_slide2_image_sizes'] ?? null,
        ]);
    }

    protected static function servicesPayloadFromTemplate(array $data): array
    {
        return static::filtered([
            'services_badge_text' => $data['tpl_services_badge_text'] ?? null,
            'services_title_html' => $data['tpl_services_title_html'] ?? null,
            'services_subtitle_text' => $data['tpl_services_subtitle_text'] ?? null,
        ]);
    }

    protected static function faqPayloadFromTemplate(array $data): array
    {
        return static::filtered([
            'faq_card_title_text' => $data['tpl_faq_card_title_text'] ?? null,
            'faq_card_description_text' => $data['tpl_faq_card_description_text'] ?? null,
        ]);
    }

    protected static function corporateCtaPayloadFromTemplate(array $data): array
    {
        return static::filtered([
            'corporate_cta_form_title_text' => $data['tpl_corporate_cta_form_title_text'] ?? null,
            'corporate_cta_form_subtitle_text' => $data['tpl_corporate_cta_form_subtitle_text'] ?? null,
        ]);
    }

    protected static function courierCtaPayloadFromTemplate(array $data): array
    {
        return static::filtered([
            'courier_cta_card_title_text' => $data['tpl_courier_cta_card_title_text'] ?? null,
            'courier_cta_card_description_text' => $data['tpl_courier_cta_card_description_text'] ?? null,
            'courier_cta_side_title_html' => $data['tpl_courier_cta_side_title_html'] ?? null,
        ]);
    }

    protected static function filtered(array $payload): array
    {
        return array_filter($payload, fn ($value) => $value !== null && $value !== '');
    }

    protected static function isSectionKey(Get $get, array $keys): bool
    {
        return in_array((string) ($get('key') ?? ''), $keys, true);
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
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('key')
                    ->label('Anahtar')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tip')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Öğe Sayısı'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Güncelleme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('page_id')
                    ->label('Sayfa')
                    ->options(fn () => LandingPage::query()->pluck('slug', 'id')->all()),
            ])
            ->defaultSort('sort_order')
            ->actions([
                Tables\Actions\Action::make('preview')
                    ->label('Önizleme')
                    ->icon('heroicon-o-eye')
                    ->url(fn (LandingPageSection $record): string => static::previewUrl($record))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('publish')
                    ->label('Yayınla')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (LandingPageSection $record): bool => ! $record->is_active)
                    ->requiresConfirmation()
                    ->action(function (LandingPageSection $record): void {
                        app(LandingRevisionService::class)->snapshotSection($record, 'publish');
                        $record->update(['is_active' => true]);
                    }),
                Tables\Actions\Action::make('unpublish')
                    ->label('Yayından Kaldır')
                    ->icon('heroicon-o-pause-circle')
                    ->color('warning')
                    ->visible(fn (LandingPageSection $record): bool => $record->is_active)
                    ->requiresConfirmation()
                    ->action(function (LandingPageSection $record): void {
                        app(LandingRevisionService::class)->snapshotSection($record, 'unpublish');
                        $record->update(['is_active' => false]);
                    }),
                Tables\Actions\EditAction::make()->label('Düzenle'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('publish_selected')
                        ->label('Seçilenleri Yayınla')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            if (! BulkActionRateLimiter::enforce('landing.sections.publish')) {
                                return;
                            }

                            $records->each(function (LandingPageSection $record): void {
                                app(LandingRevisionService::class)->snapshotSection($record, 'bulk_publish');
                                $record->update(['is_active' => true]);
                            });
                        }),
                    Tables\Actions\BulkAction::make('unpublish_selected')
                        ->label('Seçilenleri Kaldır')
                        ->icon('heroicon-o-pause-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            if (! BulkActionRateLimiter::enforce('landing.sections.unpublish')) {
                                return;
                            }

                            $records->each(function (LandingPageSection $record): void {
                                app(LandingRevisionService::class)->snapshotSection($record, 'bulk_unpublish');
                                $record->update(['is_active' => false]);
                            });
                        }),
                    Tables\Actions\DeleteBulkAction::make()->label('Sil'),
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
            'index' => Pages\ListLandingPageSections::route('/'),
            'create' => Pages\CreateLandingPageSection::route('/create'),
            'edit' => Pages\EditLandingPageSection::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['key', 'type', 'title'];
    }

    protected static function previewUrl(LandingPageSection $record): string
    {
        $slug = $record->page?->slug;
        $sectionHash = '#'.$record->key;

        if ($slug === 'home' || empty($slug)) {
            return url('/').$sectionHash;
        }

        return url('/'.$slug).$sectionHash;
    }
}
