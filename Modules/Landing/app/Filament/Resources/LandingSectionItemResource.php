<?php

namespace Modules\Landing\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\ValidationException;
use Modules\Landing\Filament\Resources\LandingSectionItemResource\Pages;
use Modules\Landing\Models\LandingPageSection;
use Modules\Landing\Models\LandingSectionItem;

class LandingSectionItemResource extends Resource
{
    protected static ?string $model = LandingSectionItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';

    protected static ?string $navigationLabel = 'İçerik Öğeleri';

    protected static ?string $modelLabel = 'İçerik Öğesi';

    protected static ?string $pluralModelLabel = 'İçerik Öğeleri';

    protected static ?string $navigationGroup = 'Sayfa Yönetimi';

    protected static ?int $navigationSort = 12;

    public const TEMPLATE_FIELDS = [
        'tpl_title',
        'tpl_subtitle',
        'tpl_text',
        'tpl_description',
        'tpl_number',
        'tpl_icon_text',
        'tpl_icon_class',
        'tpl_icon_style',
        'tpl_image_style',
        'tpl_link',
        'tpl_link_label',
        'tpl_date_label',
        'tpl_button_label',
        'tpl_button_href',
        'tpl_button_icon',
        'tpl_features_text',
        'tpl_question',
        'tpl_answer_html',
        'tpl_answer_text',
        'tpl_value',
        'tpl_hint',
        'tpl_target_blank',
        'tpl_card_class',
        'tpl_icon_wrapper_class',
        'tpl_image_url',
        'tpl_image_alt',
        'tpl_image_srcset',
        'tpl_image_sizes',
        'tpl_avatar_text',
        'tpl_avatar_style',
        'tpl_avatar_image_url',
        'tpl_avatar_image_alt',
        'tpl_avatar_image_srcset',
        'tpl_avatar_image_sizes',
        'tpl_stars',
        'tpl_author_name',
        'tpl_author_role',
        'payload_json',
    ];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('section_id')
                ->label('Bölüm')
                ->relationship('section', 'key')
                ->searchable()
                ->preload()
                ->live()
                ->required(),
            Forms\Components\TextInput::make('item_key')
                ->label('Öğe Anahtarı')
                ->maxLength(100),
            Forms\Components\TextInput::make('sort_order')
                ->label('Sıra')
                ->numeric()
                ->default(0)
                ->required(),
            Forms\Components\Placeholder::make('template_hint')
                ->label('Şablon')
                ->content(function (Get $get): string {
                    $sectionKey = static::resolveSectionKey($get('section_id'));
                    return match ($sectionKey) {
                        'services' => 'Hizmet kartı şablon alanları aşağıda aktif.',
                        'faq' => 'SSS/blog teaserları şablon alanları aşağıda aktif.',
                        'faq_items' => 'SSS öğesi şablon alanları aşağıda aktif.',
                        'contact_channels' => 'İletişim kanalları şablon alanları aşağıda aktif.',
                        'corporate_cta' => 'Kurumsal CTA şablon alanları aşağıda aktif.',
                        'courier_cta' => 'Kurye CTA şablon alanları aşağıda aktif.',
                        'testimonials' => 'Referans şablon alanları aşağıda aktif.',
                        default => 'Genel payload modu. Bilinen bir bölüm seçin.',
                    };
                }),
            Forms\Components\Section::make('Şablon Alanları')
                ->schema([
                    Forms\Components\TextInput::make('tpl_title')
                        ->label('Başlık')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['services', 'faq', 'courier_cta', 'contact_channels'])),
                    Forms\Components\Textarea::make('tpl_description')
                        ->label('Açıklama')
                        ->rows(3)
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['services', 'faq'])),
                    Forms\Components\TextInput::make('tpl_number')
                        ->label('Numara')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['services'])),
                    Forms\Components\TextInput::make('tpl_icon_text')
                        ->label('İkon Metni')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['services', 'faq_items'])),
                    Forms\Components\TextInput::make('tpl_icon_class')
                        ->label('İkon Sınıfı')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['faq', 'corporate_cta', 'courier_cta', 'contact_channels'])),
                    Forms\Components\TextInput::make('tpl_icon_style')
                        ->label('İkon Stili')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['services'])),
                    Forms\Components\TextInput::make('tpl_image_style')
                        ->label('Görsel Stili')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['faq'])),
                    Forms\Components\FileUpload::make('tpl_image_url')
                        ->label('Görsel')
                        ->image()
                        ->disk('public')
                        ->directory('landing')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['services', 'faq'])),
                    Forms\Components\TextInput::make('tpl_image_alt')
                        ->label('Görsel Alt Yazı')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['services', 'faq'])),
                    Forms\Components\TextInput::make('tpl_image_srcset')
                        ->label('Görsel Srcset')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['services', 'faq'])),
                    Forms\Components\TextInput::make('tpl_image_sizes')
                        ->label('Görsel Boyutları')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['services', 'faq'])),
                    Forms\Components\TextInput::make('tpl_link')
                        ->label('Bağlantı')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['faq', 'contact_channels'])),
                    Forms\Components\TextInput::make('tpl_link_label')
                        ->label('Bağlantı Etiketi')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['faq'])),
                    Forms\Components\TextInput::make('tpl_date_label')
                        ->label('Tarih Etiketi')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['faq'])),
                    Forms\Components\TextInput::make('tpl_button_label')
                        ->label('Buton Etiketi')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['services'])),
                    Forms\Components\TextInput::make('tpl_button_href')
                        ->label('Buton Bağlantısı')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['services'])),
                    Forms\Components\TextInput::make('tpl_button_icon')
                        ->label('Buton İkonu')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['services'])),
                    Forms\Components\TextInput::make('tpl_subtitle')
                        ->label('Alt Başlık')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['courier_cta'])),
                    Forms\Components\TextInput::make('tpl_avatar_text')
                        ->label('Avatar Metni')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['testimonials'])),
                    Forms\Components\TextInput::make('tpl_avatar_style')
                        ->label('Avatar Stili')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['testimonials'])),
                    Forms\Components\FileUpload::make('tpl_avatar_image_url')
                        ->label('Avatar Görseli')
                        ->image()
                        ->disk('public')
                        ->directory('landing/testimonials')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['testimonials'])),
                    Forms\Components\TextInput::make('tpl_avatar_image_alt')
                        ->label('Avatar Alt Yazı')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['testimonials'])),
                    Forms\Components\TextInput::make('tpl_avatar_image_srcset')
                        ->label('Avatar Srcset')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['testimonials'])),
                    Forms\Components\TextInput::make('tpl_avatar_image_sizes')
                        ->label('Avatar Boyutları')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['testimonials'])),
                    Forms\Components\TextInput::make('tpl_stars')
                        ->label('Yıldız Sayısı')
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(5)
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['testimonials'])),
                    Forms\Components\TextInput::make('tpl_author_name')
                        ->label('Yazar Adı')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['testimonials'])),
                    Forms\Components\TextInput::make('tpl_author_role')
                        ->label('Yazar Rolü')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['testimonials'])),
                    Forms\Components\Textarea::make('tpl_features_text')
                        ->label('Özellikler (satır başına bir tane)')
                        ->rows(4)
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['services'])),
                    Forms\Components\TextInput::make('tpl_text')
                        ->label('Metin')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['corporate_cta', 'testimonials'])),
                    Forms\Components\TextInput::make('tpl_question')
                        ->label('Soru')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['faq_items'])),
                    Forms\Components\Textarea::make('tpl_answer_html')
                        ->label('Cevap HTML')
                        ->rows(3)
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['faq_items'])),
                    Forms\Components\Textarea::make('tpl_answer_text')
                        ->label('Cevap Metni')
                        ->rows(2)
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['faq_items'])),
                    Forms\Components\TextInput::make('tpl_value')
                        ->label('Değer')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['contact_channels'])),
                    Forms\Components\TextInput::make('tpl_hint')
                        ->label('İpucu')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['contact_channels'])),
                    Forms\Components\TextInput::make('tpl_card_class')
                        ->label('Kart Sınıfı')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['contact_channels'])),
                    Forms\Components\TextInput::make('tpl_icon_wrapper_class')
                        ->label('İkon Sarıcı Sınıfı')
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['contact_channels'])),
                    Forms\Components\Toggle::make('tpl_target_blank')
                        ->label('Yeni Sekmede Aç')
                        ->inline(false)
                        ->visible(fn (Get $get): bool => static::isSectionKey($get, ['contact_channels'])),
                ])
                ->columns(2)
                ->visible(fn (Get $get): bool => static::isSectionKey($get, ['services', 'faq', 'faq_items', 'contact_channels', 'corporate_cta', 'courier_cta', 'testimonials'])),
            Forms\Components\Textarea::make('payload_json')
                ->label('Veri Yükü (JSON)')
                ->rows(7)
                ->placeholder('{"title":"Telefon","link":"tel:+905513567292"}'),
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('section.page.slug')
                    ->label('Sayfa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('section.key')
                    ->label('Bölüm')
                    ->searchable(),
                Tables\Columns\TextColumn::make('item_key')
                    ->label('Öğe Anahtarı')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Sıra')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Güncelleme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('section_id')
                    ->label('Bölüm')
                    ->options(fn () => LandingPageSection::query()->pluck('key', 'id')->all()),
            ])
            ->defaultSort('sort_order')
            ->actions([
                Tables\Actions\EditAction::make()->label('Düzenle'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'index' => Pages\ListLandingSectionItems::route('/'),
            'create' => Pages\CreateLandingSectionItem::route('/create'),
            'edit' => Pages\EditLandingSectionItem::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['item_key'];
    }

    public static function buildPayloadFromFormData(array $data): array
    {
        $sectionKey = static::resolveSectionKey($data['section_id'] ?? null);
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
            'services' => static::servicePayloadFromTemplate($data),
            'faq' => static::faqPayloadFromTemplate($data),
            'faq_items' => static::faqItemPayloadFromTemplate($data),
            'contact_channels' => static::contactChannelPayloadFromTemplate($data),
            'corporate_cta' => static::corporateCtaPayloadFromTemplate($data),
            'courier_cta' => static::courierCtaPayloadFromTemplate($data),
            'testimonials' => static::testimonialPayloadFromTemplate($data),
            default => [],
        };

        foreach (self::TEMPLATE_FIELDS as $field) {
            unset($data[$field]);
        }

        $data['payload'] = array_merge($payload, $templatePayload);
        return $data;
    }

    public static function injectTemplateFieldsFromPayload(array $data): array
    {
        $sectionKey = static::resolveSectionKey($data['section_id'] ?? null);
        $payload = is_array($data['payload'] ?? null) ? $data['payload'] : [];

        if ($sectionKey === 'services') {
            $data['tpl_title'] = $payload['title'] ?? null;
            $data['tpl_description'] = $payload['description'] ?? null;
            $data['tpl_number'] = $payload['number'] ?? null;
            $data['tpl_icon_text'] = $payload['icon_text'] ?? null;
            $data['tpl_icon_style'] = $payload['icon_style'] ?? null;
            $data['tpl_button_label'] = $payload['button_label'] ?? null;
            $data['tpl_button_href'] = $payload['button_href'] ?? null;
            $data['tpl_button_icon'] = $payload['button_icon'] ?? null;
            $data['tpl_image_url'] = $payload['image_url'] ?? null;
            $data['tpl_image_alt'] = $payload['image_alt'] ?? null;
            $data['tpl_image_srcset'] = $payload['image_srcset'] ?? null;
            $data['tpl_image_sizes'] = $payload['image_sizes'] ?? null;
            $data['tpl_features_text'] = isset($payload['features']) && is_array($payload['features'])
                ? implode("\n", $payload['features'])
                : null;
        }

        if ($sectionKey === 'faq') {
            $data['tpl_title'] = $payload['title'] ?? null;
            $data['tpl_description'] = $payload['description'] ?? null;
            $data['tpl_link'] = $payload['link'] ?? null;
            $data['tpl_link_label'] = $payload['link_label'] ?? null;
            $data['tpl_date_label'] = $payload['date_label'] ?? null;
            $data['tpl_icon_class'] = $payload['icon_class'] ?? null;
            $data['tpl_image_style'] = $payload['image_style'] ?? null;
            $data['tpl_image_url'] = $payload['image_url'] ?? null;
            $data['tpl_image_alt'] = $payload['image_alt'] ?? null;
            $data['tpl_image_srcset'] = $payload['image_srcset'] ?? null;
            $data['tpl_image_sizes'] = $payload['image_sizes'] ?? null;
        }

        if ($sectionKey === 'faq_items') {
            $data['tpl_icon_text'] = $payload['icon'] ?? null;
            $data['tpl_question'] = $payload['question'] ?? null;
            $data['tpl_answer_html'] = $payload['answer_html'] ?? null;
            $data['tpl_answer_text'] = $payload['answer_text'] ?? null;
        }

        if ($sectionKey === 'contact_channels') {
            $data['tpl_title'] = $payload['title'] ?? null;
            $data['tpl_value'] = $payload['value'] ?? null;
            $data['tpl_hint'] = $payload['hint'] ?? null;
            $data['tpl_icon_class'] = $payload['icon_class'] ?? null;
            $data['tpl_link'] = $payload['link'] ?? null;
            $data['tpl_card_class'] = $payload['card_class'] ?? null;
            $data['tpl_icon_wrapper_class'] = $payload['icon_wrapper_class'] ?? null;
            $data['tpl_target_blank'] = (bool) ($payload['target_blank'] ?? false);
        }

        if ($sectionKey === 'corporate_cta') {
            $data['tpl_text'] = $payload['text'] ?? null;
            $data['tpl_icon_class'] = $payload['icon_class'] ?? null;
        }

        if ($sectionKey === 'courier_cta') {
            $data['tpl_title'] = $payload['title'] ?? null;
            $data['tpl_subtitle'] = $payload['subtitle'] ?? null;
            $data['tpl_icon_class'] = $payload['icon_class'] ?? null;
        }

        if ($sectionKey === 'testimonials') {
            $data['tpl_avatar_text'] = $payload['avatar_text'] ?? null;
            $data['tpl_avatar_style'] = $payload['avatar_style'] ?? null;
            $data['tpl_avatar_image_url'] = $payload['avatar_image_url'] ?? null;
            $data['tpl_avatar_image_alt'] = $payload['avatar_image_alt'] ?? null;
            $data['tpl_avatar_image_srcset'] = $payload['avatar_image_srcset'] ?? null;
            $data['tpl_avatar_image_sizes'] = $payload['avatar_image_sizes'] ?? null;
            $data['tpl_stars'] = $payload['stars'] ?? null;
            $data['tpl_text'] = $payload['text'] ?? null;
            $data['tpl_author_name'] = $payload['author_name'] ?? null;
            $data['tpl_author_role'] = $payload['author_role'] ?? null;
        }

        $data['payload_json'] = ! empty($payload)
            ? json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
            : null;

        return $data;
    }

    protected static function resolveSectionKey(?int $sectionId): ?string
    {
        if (! $sectionId) {
            return null;
        }

        return LandingPageSection::query()->whereKey($sectionId)->value('key');
    }

    protected static function isSectionKey(Get $get, array $keys): bool
    {
        $sectionKey = static::resolveSectionKey($get('section_id'));
        return in_array((string) $sectionKey, $keys, true);
    }

    protected static function servicePayloadFromTemplate(array $data): array
    {
        $features = collect(explode("\n", (string) ($data['tpl_features_text'] ?? '')))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();

        return array_filter([
            'title' => $data['tpl_title'] ?? null,
            'description' => $data['tpl_description'] ?? null,
            'number' => $data['tpl_number'] ?? null,
            'icon_text' => $data['tpl_icon_text'] ?? null,
            'icon_style' => $data['tpl_icon_style'] ?? null,
            'button_label' => $data['tpl_button_label'] ?? null,
            'button_href' => $data['tpl_button_href'] ?? null,
            'button_icon' => $data['tpl_button_icon'] ?? null,
            'image_url' => $data['tpl_image_url'] ?? null,
            'image_alt' => $data['tpl_image_alt'] ?? null,
            'image_srcset' => $data['tpl_image_srcset'] ?? null,
            'image_sizes' => $data['tpl_image_sizes'] ?? null,
            'features' => $features,
        ], fn ($value) => $value !== null && $value !== '');
    }

    protected static function faqPayloadFromTemplate(array $data): array
    {
        return array_filter([
            'title' => $data['tpl_title'] ?? null,
            'description' => $data['tpl_description'] ?? null,
            'link' => $data['tpl_link'] ?? null,
            'link_label' => $data['tpl_link_label'] ?? null,
            'date_label' => $data['tpl_date_label'] ?? null,
            'icon_class' => $data['tpl_icon_class'] ?? null,
            'image_style' => $data['tpl_image_style'] ?? null,
            'image_url' => $data['tpl_image_url'] ?? null,
            'image_alt' => $data['tpl_image_alt'] ?? null,
            'image_srcset' => $data['tpl_image_srcset'] ?? null,
            'image_sizes' => $data['tpl_image_sizes'] ?? null,
        ], fn ($value) => $value !== null && $value !== '');
    }

    protected static function corporateCtaPayloadFromTemplate(array $data): array
    {
        return array_filter([
            'text' => $data['tpl_text'] ?? null,
            'icon_class' => $data['tpl_icon_class'] ?? null,
        ], fn ($value) => $value !== null && $value !== '');
    }

    protected static function courierCtaPayloadFromTemplate(array $data): array
    {
        return array_filter([
            'title' => $data['tpl_title'] ?? null,
            'subtitle' => $data['tpl_subtitle'] ?? null,
            'icon_class' => $data['tpl_icon_class'] ?? null,
        ], fn ($value) => $value !== null && $value !== '');
    }

    protected static function faqItemPayloadFromTemplate(array $data): array
    {
        return array_filter([
            'icon' => $data['tpl_icon_text'] ?? null,
            'question' => $data['tpl_question'] ?? null,
            'answer_html' => $data['tpl_answer_html'] ?? null,
            'answer_text' => $data['tpl_answer_text'] ?? null,
        ], fn ($value) => $value !== null && $value !== '');
    }

    protected static function contactChannelPayloadFromTemplate(array $data): array
    {
        return array_filter([
            'title' => $data['tpl_title'] ?? null,
            'value' => $data['tpl_value'] ?? null,
            'hint' => $data['tpl_hint'] ?? null,
            'icon_class' => $data['tpl_icon_class'] ?? null,
            'link' => $data['tpl_link'] ?? null,
            'card_class' => $data['tpl_card_class'] ?? null,
            'icon_wrapper_class' => $data['tpl_icon_wrapper_class'] ?? null,
            'target_blank' => (bool) ($data['tpl_target_blank'] ?? false),
        ], fn ($value) => $value !== null && $value !== '');
    }

    protected static function testimonialPayloadFromTemplate(array $data): array
    {
        return array_filter([
            'avatar_text' => $data['tpl_avatar_text'] ?? null,
            'avatar_style' => $data['tpl_avatar_style'] ?? null,
            'avatar_image_url' => $data['tpl_avatar_image_url'] ?? null,
            'avatar_image_alt' => $data['tpl_avatar_image_alt'] ?? null,
            'avatar_image_srcset' => $data['tpl_avatar_image_srcset'] ?? null,
            'avatar_image_sizes' => $data['tpl_avatar_image_sizes'] ?? null,
            'stars' => isset($data['tpl_stars']) ? (int) $data['tpl_stars'] : null,
            'text' => $data['tpl_text'] ?? null,
            'author_name' => $data['tpl_author_name'] ?? null,
            'author_role' => $data['tpl_author_role'] ?? null,
        ], fn ($value) => $value !== null && $value !== '');
    }
}
