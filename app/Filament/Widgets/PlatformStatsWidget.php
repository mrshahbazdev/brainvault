<?php

namespace App\Filament\Widgets;

use App\Models\Bookmark;
use App\Models\Note;
use App\Models\Team;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PlatformStatsWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->description('Registered users')
                ->icon('heroicon-o-users')
                ->color('primary'),
            Stat::make('Total Bookmarks', Bookmark::count())
                ->description('Across all users')
                ->icon('heroicon-o-bookmark')
                ->color('success'),
            Stat::make('Total Notes', Note::count())
                ->description('Across all users')
                ->icon('heroicon-o-document-text')
                ->color('warning'),
            Stat::make('Total Teams', Team::count())
                ->description('Active teams')
                ->icon('heroicon-o-user-group')
                ->color('info'),
            Stat::make('Pro Users', User::where('plan', 'pro')->count())
                ->description('Paid subscribers')
                ->icon('heroicon-o-star')
                ->color('success'),
            Stat::make('New Today', User::whereDate('created_at', today())->count())
                ->description('Users joined today')
                ->icon('heroicon-o-arrow-trending-up')
                ->color('primary'),
        ];
    }
}
