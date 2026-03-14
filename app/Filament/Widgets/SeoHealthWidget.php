<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Modules\Landing\Filament\Resources\LandingPageResource;
use Modules\Landing\Models\LandingPage;

class SeoHealthWidget extends BaseWidget
{
    protected static ?int $sort = 6;

    protected static ?string $pollingInterval = '60s';

    public static function canView(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if ($user->hasRole('super-admin')) {
            return true;
        }

        return $user->hasAnyPermission(['landing.manage', 'reports.view']);
    }

    protected function getStats(): array
    {
        $pages = LandingPage::query()->get();
        $total = $pages->count();

        $complete = $pages->filter(
            fn (LandingPage $page): bool => LandingPageResource::seoStatusLabel($page) === 'Tam'
        )->count();

        $partial = $pages->filter(
            fn (LandingPage $page): bool => LandingPageResource::seoStatusLabel($page) === 'Kısmi'
        )->count();

        $missing = $pages->filter(
            fn (LandingPage $page): bool => LandingPageResource::seoStatusLabel($page) === 'Eksik'
        )->count();

        $healthRate = $total > 0 ? round(($complete / $total) * 100, 1) : 0.0;

        return [
            Stat::make('SEO Tam Sayfa', $complete)
                ->description("Toplam {$total} sayfanın tamamı")
                ->color('success'),
            Stat::make('SEO Kısmi Sayfa', $partial)
                ->description('Meta alanları eksik sayfalar')
                ->color('warning'),
            Stat::make('SEO Eksik Sayfa', $missing)
                ->description('Meta title + description boş sayfalar')
                ->color($missing > 0 ? 'danger' : 'success'),
            Stat::make('SEO Sağlık Oranı', number_format($healthRate, 1, ',', '.') . '%')
                ->description('Tam sayfa / toplam sayfa')
                ->color($healthRate >= 75 ? 'success' : 'warning'),
        ];
    }
}
