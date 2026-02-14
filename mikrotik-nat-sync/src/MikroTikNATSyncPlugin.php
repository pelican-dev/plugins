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
            $interval = env('MIKROTIK_NAT_SYNC_INTERVAL', 'everyFiveMinutes');
            
            $schedule->command('mikrotik:sync')
                ->{$interval}()
                ->withoutOverlapping();
        });
    }

    public function getSettingsForm(): array
    {
        return [
            TextInput::make('mk_ip')
                ->label('MikroTik IP')
                ->default(env('MIKROTIK_NAT_SYNC_IP'))
                ->required(),
            TextInput::make('mk_port')
                ->label('REST API Port')
                ->default(env('MIKROTIK_NAT_SYNC_PORT', '9080'))
                ->required(),
            TextInput::make('mk_user')
                ->label('Username')
                ->default(env('MIKROTIK_NAT_SYNC_USER'))
                ->required(),
            TextInput::make('mk_pass')
                ->label('Password')
                ->password()
                ->revealable()
                ->default(env('MIKROTIK_NAT_SYNC_PASSWORD')),
            TextInput::make('mk_interface')
                ->label('WAN Interface')
                ->default(env('MIKROTIK_NAT_SYNC_INTERFACE', 'ether1'))
                ->required(),
            TextInput::make('mk_forbidden_ports')
                ->label('Forbidden Ports (comma separated)')
                ->placeholder('22, 80, 443, 3306')
                ->default(env('MIKROTIK_NAT_SYNC_FORBIDDEN_PORTS')),
            Select::make('mk_interval')
                ->label('Sync Interval')
                ->options([
                    'everyMinute' => 'Every Minute',
                    'everyFiveMinutes' => 'Every 5 Minutes',
                    'everyTenMinutes' => 'Every 10 Minutes',
                    'hourly' => 'Hourly',
                ])
                ->default(env('MIKROTIK_NAT_SYNC_INTERVAL', 'everyFiveMinutes'))
                ->required(),
        ];
    }

    public function saveSettings(array $data): void
    {
        $this->writeToEnvironment([
            'MIKROTIK_NAT_SYNC_IP' => $data['mk_ip'],
            'MIKROTIK_NAT_SYNC_PORT' => $data['mk_port'],
            'MIKROTIK_NAT_SYNC_USER' => $data['mk_user'],
            'MIKROTIK_NAT_SYNC_PASSWORD' => $data['mk_pass'],
            'MIKROTIK_NAT_SYNC_INTERFACE' => $data['mk_interface'],
            'MIKROTIK_NAT_SYNC_INTERVAL' => $data['mk_interval'],
            'MIKROTIK_NAT_SYNC_FORBIDDEN_PORTS' => $data['mk_forbidden_ports'],
        ]);

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }
}
