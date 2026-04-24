<?php

namespace Boy132\Chatwoot;

use App\Contracts\Plugins\HasPluginSettings;
use App\Traits\EnvironmentWriterTrait;
use Filament\Contracts\Plugin;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Panel;

class ChatwootPlugin implements HasPluginSettings, Plugin {
    use EnvironmentWriterTrait;

    public function getId(): string {
        return 'chatwoot';
    }

    public function register(Panel $panel): void {}

    public function boot(Panel $panel): void {}

    public function getSettingsForm(): array {
        return [
            TextInput::make('base_url')
                ->label('Base URL')
                ->required()
                ->default(fn () => config('chatwoot.base_url')),
            TextInput::make('website_token')
                ->label('Website Token')
                ->required()
                ->default(fn () => config('chatwoot.website_token')),
        ];
    }

    public function saveSettings(array $data): void {
        $this->writeToEnvironment([
            'CHATWOOT_BASE_URL' => $data['base_url'],
            'CHATWOOT_WEBSITE_TOKEN' => $data['website_token'],
        ]);

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }
}
