<?php

namespace JuggleGaming\McLogCleaner;

use App\Contracts\Plugins\HasPluginSettings;
use App\Traits\EnvironmentWriterTrait;
use Filament\Contracts\Plugin;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Panel;

class McLogCleanerPlugin implements HasPluginSettings, Plugin
{
    use EnvironmentWriterTrait;

    public function getId(): string
    {
        return 'mclogcleaner';
    }

    public function register(Panel $panel): void
    {
        //
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public function getSettingsForm(): array
    {
        return [
            Toggle::make('mclogcleaner_text_enabled')
                ->label('Enable button text')
                ->default(fn () => (bool) config('mclogcleaner.mclogcleaner_text_enabled', true)),
        ];
    }

    public function saveSettings(array $data): void
    {
        $this->writeToEnvironment([
            'MCLOGCLEANER_TEXT_ENABLED' => $data['mclogcleaner_text_enabled'] ? 'true' : 'false',
        ]);

        Notification::make()
            ->title('McLogCleaner')
            ->body('Settings successfully saved!')
            ->success()
            ->send();
    }
}
