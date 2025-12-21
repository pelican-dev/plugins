<?php

namespace Boy132\UserCreatableServers\Filament\App\Widgets;

use Boy132\UserCreatableServers\Models\UserResourceLimits;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserResourceLimitsOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $user = auth()->user();
        if (!$user) {
            return [];
        }

        $userResourceLimits = UserResourceLimits::where('user_id', $user->id)->first();
        if (!$userResourceLimits) {
            return [];
        }

        $userServers = $user->servers()->get();

        $suffix = config('panel.use_binary_prefix') ? ' MiB' : ' MB';

        $cpu = $userServers->sum('cpu') . '% / ' . ($userResourceLimits->cpu > 0 ? "{$userResourceLimits->cpu}%" : '∞');
        $memory = $userServers->sum('memory') . "{$suffix} / " . ($userResourceLimits->memory > 0 ? "{$userResourceLimits->memory}{$suffix}" : '∞');
        $disk = $userServers->sum('disk') . "{$suffix} / " . ($userResourceLimits->disk > 0 ? "{$userResourceLimits->disk}{$suffix}" : '∞');

        return [
            Stat::make(trans('user-creatable-servers::strings.cpu'), $cpu),
            Stat::make(trans('user-creatable-servers::strings.memory'), $memory),
            Stat::make(trans('user-creatable-servers::strings.disk'), $disk),
        ];
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        return UserResourceLimits::where('user_id', $user->id)->exists();
    }
}