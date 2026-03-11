<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class AdsPlatformGuide extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-information-circle';

    protected static ?string $navigationLabel = 'Nasil Calisir';

    protected static ?string $navigationGroup = 'Reklam Platformu';

    protected static ?int $navigationSort = 99;

    protected static ?string $title = 'Reklam Platformu Nasil Calisir';

    protected static ?string $slug = 'ads-platform-guide';

    protected static string $view = 'filament.pages.ads-platform-guide';

    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if ($user->hasRole('super-admin')) {
            return true;
        }

        return $user->hasAnyPermission([
            'ads.view',
            'ads.manage',
            'ads.publish',
            'ads.report',
        ]);
    }
}

