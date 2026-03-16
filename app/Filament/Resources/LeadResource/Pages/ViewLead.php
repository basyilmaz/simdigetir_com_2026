<?php

namespace App\Filament\Resources\LeadResource\Pages;

use App\Filament\Resources\LeadResource;
use Filament\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\HtmlString;
use Modules\Leads\Models\Lead;

class ViewLead extends ViewRecord
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Talep Özeti')
                    ->schema([
                        TextEntry::make('name')
                            ->label('İsim'),
                        TextEntry::make('phone')
                            ->label('Telefon'),
                        TextEntry::make('email')
                            ->label('E-posta')
                            ->placeholder('-'),
                        TextEntry::make('company_name')
                            ->label('Firma')
                            ->placeholder('-'),
                        TextEntry::make('status')
                            ->label('Durum')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => static::statusLabel($state)),
                        TextEntry::make('type')
                            ->label('Tip')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'corporate_quote' => 'Kurumsal',
                                'courier_apply' => 'Kurye',
                                'contact' => 'İletişim',
                                default => $state,
                            }),
                    ])
                    ->columns(2),
                Section::make('Aktivite Akışı')
                    ->schema([
                        TextEntry::make('activity_timeline')
                            ->label('')
                            ->state(fn (Lead $record): HtmlString => static::renderTimelineHtml($record))
                            ->html()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    /**
     * @return array<int, array{event: string, at: string, description: string}>
     */
    public static function buildTimelineItems(Lead $lead): array
    {
        $items = [
            [
                'event' => 'Talep oluşturuldu',
                'at' => optional($lead->created_at)->format('d.m.Y H:i:s') ?? '-',
                'description' => 'Talep sisteme alındı.',
            ],
        ];

        if ($lead->source || $lead->campaign) {
            $source = $lead->source ?: 'doğrudan';
            $campaign = $lead->campaign ?: '-';
            $items[] = [
                'event' => 'Kaynak bilgisi',
                'at' => optional($lead->created_at)->format('d.m.Y H:i:s') ?? '-',
                'description' => "Kaynak: {$source}, Kampanya: {$campaign}",
            ];
        }

        if (filled($lead->notes)) {
            $items[] = [
                'event' => 'Not eklendi',
                'at' => optional($lead->updated_at)->format('d.m.Y H:i:s') ?? '-',
                'description' => (string) $lead->notes,
            ];
        }

        $items[] = [
            'event' => 'Durum',
            'at' => optional($lead->updated_at)->format('d.m.Y H:i:s') ?? '-',
            'description' => 'Güncel durum: '.static::statusLabel((string) $lead->status),
        ];

        if ($lead->updated_at && $lead->created_at && ! $lead->updated_at->equalTo($lead->created_at)) {
            $items[] = [
                'event' => 'Son güncelleme',
                'at' => $lead->updated_at->format('d.m.Y H:i:s'),
                'description' => 'Kaydın en son düzenlendiği an.',
            ];
        }

        return $items;
    }

    /**
     * @return array<int, array{event: string, at: string, description: string}>
     */
    public static function timelineItemsForDisplay(Lead $lead): array
    {
        return collect(static::buildTimelineItems($lead))
            ->map(function (array $item): array {
                return [
                    'event' => trim((string) ($item['event'] ?? '')),
                    'at' => trim((string) ($item['at'] ?? '')),
                    'description' => trim((string) ($item['description'] ?? '')),
                ];
            })
            ->filter(fn (array $item): bool => $item['event'] !== '' || $item['description'] !== '')
            ->unique(fn (array $item): string => implode('|', $item))
            ->values()
            ->all();
    }

    public static function renderTimelineHtml(Lead $lead): HtmlString
    {
        $items = static::timelineItemsForDisplay($lead);

        if ($items === []) {
            return new HtmlString('<div class="text-sm text-gray-500">Aktivite kaydi bulunmuyor.</div>');
        }

        $html = '<div class="space-y-3">';

        foreach ($items as $item) {
            $html .= sprintf(
                '<div class="rounded-xl border border-gray-200 bg-white px-4 py-3 shadow-sm">'
                .'<div class="flex items-center justify-between gap-3">'
                .'<div class="font-medium text-gray-900">%s</div>'
                .'<div class="text-xs text-gray-500">%s</div>'
                .'</div>'
                .'<div class="mt-2 text-sm text-gray-600">%s</div>'
                .'</div>',
                e($item['event'] !== '' ? $item['event'] : 'Aktivite'),
                e($item['at'] !== '' ? $item['at'] : '-'),
                e($item['description'] !== '' ? $item['description'] : '-')
            );
        }

        $html .= '</div>';

        return new HtmlString($html);
    }

    protected static function statusLabel(string $state): string
    {
        return match ($state) {
            'new' => 'Yeni',
            'contacted' => 'İletişimde',
            'qualified' => 'Uygun',
            'lost' => 'Kayıp',
            'won' => 'Kazanıldı',
            default => $state,
        };
    }
}
