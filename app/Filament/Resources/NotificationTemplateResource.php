<?php

namespace App\Filament\Resources;

use App\Domain\Notifications\Support\NotificationTemplateCatalog;
use App\Filament\Resources\NotificationTemplateResource\Pages;
use App\Models\NotificationTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class NotificationTemplateResource extends Resource
{
    protected static ?string $model = NotificationTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Bildirim Şablonları';

    protected static ?string $modelLabel = 'Bildirim Sablonu';

    protected static ?string $pluralModelLabel = 'Bildirim Şablonları';

    protected static ?string $navigationGroup = 'Operasyon';

    protected static ?int $navigationSort = 45;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Sablon Kimligi')
                ->icon('heroicon-o-bell-alert')
                ->schema([
                    Forms\Components\TextInput::make('event_key')
                        ->label('Event Key')
                        ->required()
                        ->maxLength(80)
                        ->live(onBlur: true)
                        ->placeholder('orders.order_created')
                        ->helperText(
                            'Onerilen event keyler: '.implode(', ', array_keys(NotificationTemplateCatalog::recommendedEventKeyLabels()))
                        ),
                    Forms\Components\Select::make('channel')
                        ->label('Kanal')
                        ->options([
                            'sms' => 'SMS',
                            'email' => 'Email',
                            'push' => 'Push',
                        ])
                        ->default('sms')
                        ->required()
                        ->native(false)
                        ->live(),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true)
                        ->inline(false),
                    Forms\Components\TagsInput::make('variables')
                        ->label('Placeholder Listesi')
                        ->suggestions(NotificationTemplateCatalog::variableSuggestions())
                        ->placeholder('order_no')
                        ->helperText('Placeholder isimlerini suslu parantezsiz girin. Ornek: order_no')
                        ->columnSpanFull(),
                ])->columns(3),

            Forms\Components\Section::make('Sablon Govdesi')
                ->schema([
                    Forms\Components\TextInput::make('subject')
                        ->label('Konu')
                        ->maxLength(255)
                        ->helperText('SMS kanalinda opsiyoneldir, email icin kullanilabilir.')
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('body')
                        ->label('Mesaj Govdesi')
                        ->required()
                        ->rows(8)
                        ->columnSpanFull()
                        ->helperText('Govde icinde {order_no} gibi placeholder kullanin. Asagidaki rehber secilen event icin gecerli alanlari listeler.'),
                ]),

            Forms\Components\Section::make('Placeholder Rehberi')
                ->schema([
                    Forms\Components\Placeholder::make('placeholder_guide')
                        ->label('')
                        ->content(fn (Get $get): HtmlString => new HtmlString(
                            static::renderPlaceholderGuide(
                                eventKey: (string) $get('event_key'),
                                channel: (string) $get('channel')
                            )
                        )),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('event_key')
                    ->label('Event Key')
                    ->searchable()
                    ->description(fn (NotificationTemplate $record): ?string => NotificationTemplateCatalog::definition($record->event_key)['label'] ?? null)
                    ->wrap(),
                Tables\Columns\TextColumn::make('channel')
                    ->label('Kanal')
                    ->badge(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('variables')
                    ->label('Placeholderlar')
                    ->formatStateUsing(function (mixed $state): string {
                        $variables = is_array($state) ? $state : [];

                        return $variables === [] ? '-' : implode(', ', $variables);
                    })
                    ->wrap()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Guncellendi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('channel')
                    ->label('Kanal')
                    ->options([
                        'sms' => 'SMS',
                        'email' => 'Email',
                        'push' => 'Push',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Aktiflik'),
            ])
            ->emptyStateHeading(static::emptyStateHeading())
            ->emptyStateDescription(static::emptyStateDescription())
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Şablon Oluştur'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([])
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->latest('updated_at'));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotificationTemplates::route('/'),
            'create' => Pages\CreateNotificationTemplate::route('/create'),
            'edit' => Pages\EditNotificationTemplate::route('/{record}/edit'),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function normalizeFormData(array $data): array
    {
        $data['event_key'] = trim((string) ($data['event_key'] ?? ''));
        $data['channel'] = strtolower(trim((string) ($data['channel'] ?? 'sms')));
        $data['subject'] = filled($data['subject'] ?? null) ? trim((string) $data['subject']) : null;
        $data['variables'] = array_values(array_filter(array_map(
            static fn (mixed $value): string => trim((string) $value),
            (array) ($data['variables'] ?? [])
        )));

        if ($data['variables'] === []) {
            $definition = NotificationTemplateCatalog::definition($data['event_key']);
            if ($definition) {
                $data['variables'] = array_keys($definition['variables']);
            }
        }

        return $data;
    }

    public static function emptyStateHeading(): string
    {
        return 'Henüz bildirim şablonu yok';
    }

    public static function emptyStateDescription(): string
    {
        return 'Sipariş yaşam döngüsü SMS şablonlarını katalogdan tek tıkla hazırlayabilir veya özel şablon oluşturabilirsiniz.';
    }

    public static function bootstrapCatalogTemplates(): int
    {
        $count = 0;

        foreach (NotificationTemplateCatalog::defaultSmsTemplates() as $eventKey => $template) {
            NotificationTemplate::query()->updateOrCreate(
                [
                    'event_key' => $eventKey,
                    'channel' => 'sms',
                ],
                [
                    'subject' => null,
                    'body' => $template['body'],
                    'variables' => $template['variables'],
                    'is_active' => true,
                ]
            );

            $count++;
        }

        return $count;
    }

    private static function renderPlaceholderGuide(string $eventKey, string $channel): string
    {
        $definition = NotificationTemplateCatalog::definition($eventKey);

        if (! $definition) {
            return sprintf(
                '<div class="text-sm leading-6"><p>Bu event key icin hazir placeholder rehberi yok. Govde icinde <code>{degisken_adi}</code> formatini kullanabilirsiniz.</p><p class="mt-2">Onerilen event keyler: %s</p></div>',
                e(implode(', ', array_keys(NotificationTemplateCatalog::recommendedEventKeyLabels())))
            );
        }

        $variableItems = [];
        foreach ($definition['variables'] as $variable => $description) {
            $variableItems[] = '<li><code>{'.e($variable).'}</code> - '.e($description).'</li>';
        }

        $channelNote = in_array(strtolower($channel), $definition['channels'], true)
            ? 'Secilen kanal katalogdaki varsayilan kanal ile uyumlu.'
            : 'Dikkat: bu event icin katalog varsayilani '.implode(', ', $definition['channels']).' kanali uzerinedir.';

        return '<div class="text-sm leading-6 space-y-3">'
            .'<p><strong>'.e($definition['label']).'</strong> - '.e($definition['description']).'</p>'
            .'<p>'.e($channelNote).'</p>'
            .'<p><strong>Kullanilabilir placeholderlar</strong></p>'
            .'<ul style="margin:0;padding-left:18px">'.implode('', $variableItems).'</ul>'
            .'<p style="margin-top:12px"><strong>Varsayilan govde</strong></p>'
            .'<div style="padding:12px;border:1px solid rgba(0,0,0,.08);border-radius:10px;background:rgba(0,0,0,.02)"><code style="white-space:pre-wrap">'.e($definition['default_body']).'</code></div>'
            .'</div>';
    }
}
