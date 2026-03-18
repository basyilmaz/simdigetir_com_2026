<?php

namespace Modules\Landing\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\ValidationException;
use Modules\Landing\Filament\Resources\LandingPageResource\Pages;
use Modules\Landing\Models\LandingPage;

class LandingPageResource extends Resource
{
    protected static ?string $model = LandingPage::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Sayfalar';

    protected static ?string $modelLabel = 'Sayfa';

    protected static ?string $pluralModelLabel = 'Sayfalar';

    protected static ?string $navigationGroup = 'Sayfa Yönetimi';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Sayfa Bilgileri')
                ->icon('heroicon-o-document-text')
                ->description('Temel URL, gorunen baslik ve yayin durumu bu bolumden yonetilir.')
                ->schema([
                    Forms\Components\TextInput::make('slug')
                        ->label('URL Yolu')
                        ->required()
                        ->maxLength(100)
                        ->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('title')
                        ->label('Başlık')
                        ->maxLength(255),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true)
                        ->required(),
                ])->columns(2),

            Forms\Components\Section::make('SEO Ayarları')
                ->icon('heroicon-o-magnifying-glass')
                ->description('Arama motoru basligi, aciklama ve indeksleme tercihlerini burada netlestirin.')
                ->schema([
                    Forms\Components\TextInput::make('meta.meta_title')
                        ->label('Meta Başlık')
                        ->maxLength(255),
                    Forms\Components\Textarea::make('meta.meta_description')
                        ->label('Meta Açıklama')
                        ->rows(2),
                    Forms\Components\Textarea::make('meta.meta_keywords')
                        ->label('Anahtar Kelimeler')
                        ->rows(2),
                    Forms\Components\TextInput::make('meta.canonical_url')
                        ->label('Kanonik URL')
                        ->maxLength(255),
                    Forms\Components\Select::make('meta.robots')
                        ->label('Robot Direktifi')
                        ->options([
                            'index, follow' => 'index, follow',
                            'noindex, follow' => 'noindex, follow',
                            'index, nofollow' => 'index, nofollow',
                            'noindex, nofollow' => 'noindex, nofollow',
                        ]),
                ])->columns(2)->collapsed(),

            Forms\Components\Section::make('Sosyal Medya (Open Graph)')
                ->icon('heroicon-o-share')
                ->description('Paylasim onizlemelerinde kullanilacak baslik, aciklama ve gorsel ayarlari.')
                ->schema([
                    Forms\Components\TextInput::make('meta.og_title')
                        ->label('OG Başlık')
                        ->maxLength(255),
                    Forms\Components\Textarea::make('meta.og_description')
                        ->label('OG Açıklama')
                        ->rows(2),
                    Forms\Components\TextInput::make('meta.og_image')
                        ->label('OG Görsel URL')
                        ->maxLength(255),
                ])->columns(2)->collapsed(),

            Forms\Components\Section::make('Site Haritası')
                ->icon('heroicon-o-map')
                ->description('Sitemap onceligi ve guncelleme sikligi arama motoru taramasini yonlendirir.')
                ->schema([
                    Forms\Components\Select::make('meta.sitemap_changefreq')
                        ->label('Değişim Sıklığı')
                        ->options([
                            'daily' => 'Günlük',
                            'weekly' => 'Haftalık',
                            'monthly' => 'Aylık',
                            'yearly' => 'Yıllık',
                        ]),
                    Forms\Components\TextInput::make('meta.sitemap_priority')
                        ->label('Öncelik')
                        ->numeric(),
                    Forms\Components\Toggle::make('meta.sitemap_is_active')
                        ->label('Site Haritasında Göster')
                        ->default(true),
                ])->columns(2)->collapsed(),

            Forms\Components\Section::make('Yapısal Veri (Schema)')
                ->icon('heroicon-o-code-bracket')
                ->description('Schema.org alanlarini ve ozel JSON-LD bloklarini kontrollu sekilde yonetin.')
                ->schema([
                    Forms\Components\Repeater::make('service_schema_items_editor')
                        ->label('Hizmet Şema Öğeleri')
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->label('Ad')
                                ->required()
                                ->maxLength(150),
                            Forms\Components\Textarea::make('description')
                                ->label('Açıklama')
                                ->rows(2),
                            Forms\Components\TextInput::make('serviceType')
                                ->label('Hizmet Tipi')
                                ->maxLength(150),
                            Forms\Components\TextInput::make('url')
                                ->label('URL')
                                ->maxLength(255),
                        ])
                        ->collapsed()
                        ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                        ->default([]),
                    Forms\Components\Textarea::make('structured_data_json')
                        ->label('Yapısal Veri (JSON)')
                        ->rows(8)
                        ->placeholder('{"@context":"https://schema.org","@type":"Organization"}')
                        ->columnSpanFull(),
                ])->collapsed(),

            Forms\Components\Section::make('Ek Bilgiler')
                ->schema([
                    Forms\Components\KeyValue::make('meta')
                        ->label('Meta Verileri')
                        ->keyLabel('Anahtar')
                        ->valueLabel('Değer')
                        ->addButtonLabel('Meta Ekle'),
                ])->collapsed(),
        ]);
    }

    public static function fillMetaEditorFields(array $data): array
    {
        $meta = is_array($data['meta'] ?? null) ? $data['meta'] : [];
        $data['structured_data_json'] = isset($meta['structured_data'])
            ? json_encode($meta['structured_data'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
            : null;
        $data['service_schema_items_editor'] = is_array($meta['service_schema_items'] ?? null)
            ? $meta['service_schema_items']
            : [];

        return $data;
    }

    public static function normalizeMetaEditorFields(array $data): array
    {
        $meta = is_array($data['meta'] ?? null) ? $data['meta'] : [];

        $structuredDataRaw = trim((string) ($data['structured_data_json'] ?? ''));
        if ($structuredDataRaw !== '') {
            $decoded = json_decode($structuredDataRaw, true);
            if (! is_array($decoded) || json_last_error() !== JSON_ERROR_NONE) {
                throw ValidationException::withMessages([
                    'structured_data_json' => 'Structured Data JSON formatı geçersiz.',
                ]);
            }

            $meta['structured_data'] = $decoded;
        } else {
            unset($meta['structured_data']);
        }

        $serviceSchemaItems = $data['service_schema_items_editor'] ?? [];
        if (is_array($serviceSchemaItems)) {
            $serviceSchemaItems = array_values(array_filter($serviceSchemaItems, function ($item) {
                return is_array($item) && ! empty(trim((string) ($item['name'] ?? '')));
            }));
        } else {
            $serviceSchemaItems = [];
        }

        if (! empty($serviceSchemaItems)) {
            $meta['service_schema_items'] = $serviceSchemaItems;
        } else {
            unset($meta['service_schema_items']);
        }

        $data['meta'] = $meta;
        unset($data['structured_data_json'], $data['service_schema_items_editor']);

        return $data;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('URL Yolu')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('seo_status')
                    ->label('SEO')
                    ->state(fn (LandingPage $record): string => static::seoStatusLabel($record))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Tam' => 'success',
                        'Kısmi' => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Güncelleme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->emptyStateHeading(static::emptyStateHeading())
            ->emptyStateDescription(static::emptyStateDescription())
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Ilk sayfayi ekle'),
            ])
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
            'index' => Pages\ListLandingPages::route('/'),
            'create' => Pages\CreateLandingPage::route('/create'),
            'edit' => Pages\EditLandingPage::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['slug', 'title'];
    }

    public static function seoStatusLabel(LandingPage $record): string
    {
        $meta = is_array($record->meta) ? $record->meta : [];

        $hasMetaTitle = filled(trim((string) ($meta['meta_title'] ?? '')));
        $hasMetaDescription = filled(trim((string) ($meta['meta_description'] ?? '')));

        if ($hasMetaTitle && $hasMetaDescription) {
            return 'Tam';
        }

        if ($hasMetaTitle || $hasMetaDescription) {
            return 'Kısmi';
        }

        return 'Eksik';
    }

    public static function previewUrl(LandingPage $record): string
    {
        return url(static::previewPathBySlug((string) $record->slug));
    }

    public static function emptyStateHeading(): string
    {
        return 'Henuz landing sayfasi yok';
    }

    public static function emptyStateDescription(): string
    {
        return 'SEO alanlarini, temel sayfa kimligini ve icerik sablonunu yonetmek icin ilk landing sayfasini olusturun.';
    }

    public static function previewPathBySlug(string $slug): string
    {
        $slugMap = [
            'home' => '/',
            'about' => '/hakkimizda',
            'services' => '/hizmetler',
            'contact' => '/iletisim',
            'faq' => '/sss',
            'corporate' => '/kurumsal',
            'courier-apply' => '/kurye-basvuru',
        ];

        return $slugMap[$slug] ?? '/'.ltrim($slug, '/');
    }
}
