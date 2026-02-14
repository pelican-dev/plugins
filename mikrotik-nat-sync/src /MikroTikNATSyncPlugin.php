<?php

namespace Avalon\MikroTikNATSync;

use Filament\Contracts\Plugin as FilamentPlugin;
use Filament\Panel;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use App\Contracts\Plugins\HasPluginSettings;
use App\Traits\EnvironmentWriterTrait;
use Illuminate\Console\Scheduling\Schedule;

class MikroTikNATSyncPlugin implements FilamentPlugin, HasPluginSettings
{
    use EnvironmentWriterTrait;

    public function getId(): string
    {
        return 'mikrotik-nat-sync';
    }

    public function register(Panel $panel): void
    {
        //
    }

    public function boot(Panel $panel): void
    {
        if (app()->runningInConsole()) {
            $this->commands([
                \Avalon\MikroTikNATSync\Console\Commands\SyncMikrotikCommand::class,
            ]);
        }

        app()->booted(function () {
            $schedule = app(Schedule::class);
            $interval = env('MIKROTIK_SYNC_INTERVAL', 'everyFiveMinutes');
            
            $schedule->command('mikrotik:sync')
                ->{$interval}()
                ->withoutOverlapping();
        });
    }

    public function getSettingsForm(): array
    {
        return [
            TextInput::make('mk_ip')
                ->label('IP MikroTik')
                ->default(env('MIKROTIK_IP'))
                ->required(),
            TextInput::make('mk_port')
                ->label('Порт REST API')
                ->default(env('MIKROTIK_PORT', '9080'))
                ->required(),
            TextInput::make('mk_user')
                ->label('Користувач')
                ->default(env('MIKROTIK_USER'))
                ->required(),
            TextInput::make('mk_pass')
                ->label('Пароль')
                ->password()
                ->revealable()
                ->default(env('MIKROTIK_PASS')),
            TextInput::make('mk_interface')
                ->label('Вхідний інтерфейс (WAN)')
                ->default(env('MIKROTIK_INTERFACE', 'ether1'))
                ->required(),
            TextInput::make('mk_forbidden_ports')
                ->label('Заборонені порти (через кому)')
                ->placeholder('22, 80, 443, 3306')
                ->default(env('MIKROTIK_FORBIDDEN_PORTS')),
            Select::make('mk_interval')
                ->label('Інтервал синхронізації')
                ->options([
                    'everyMinute' => 'Щохвилини',
                    'everyFiveMinutes' => 'Кожні 5 хвилин',
                    'everyTenMinutes' => 'Кожні 10 хвилин',
                    'hourly' => 'Щогодини',
                ])
                ->default(env('MIKROTIK_SYNC_INTERVAL', 'everyFiveMinutes'))
                ->required(),
        ];
    }

    public function saveSettings(array $data): void
    {
        $this->writeToEnvironment([
            'MIKROTIK_IP' => $data['mk_ip'],
            'MIKROTIK_PORT' => $data['mk_port'],
            'MIKROTIK_USER' => $data['mk_user'],
            'MIKROTIK_PASS' => $data['mk_pass'],
            'MIKROTIK_INTERFACE' => $data['mk_interface'],
            'MIKROTIK_SYNC_INTERVAL' => $data['mk_interval'],
            'MIKROTIK_FORBIDDEN_PORTS' => $data['mk_forbidden_ports'],
        ]);

        Notification::make()
            ->title('Налаштування збережено')
            ->success()
            ->send();
    }
}
