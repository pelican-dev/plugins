<?php

namespace FlexKleks\PasteFoxShare;

use App\Contracts\Plugins\HasPluginSettings;
use App\Traits\EnvironmentWriterTrait;
use Filament\Contracts\Plugin;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Panel;

class PasteFoxSharePlugin implements HasPluginSettings, Plugin
{
    use EnvironmentWriterTrait;

    public function getId(): string
    {
        return 'pastefox-share';
    }

    public function register(Panel $panel): void
    {
        $id = str($panel->getId())->title();

        $panel->discoverPages(
            plugin_path($this->getId(), "src/Filament/$id/Pages"),
            "FlexKleks\\PasteFoxShare\\Filament\\$id\\Pages"
        );
    }

    public function boot(Panel $panel): void {}

    public function getSettingsForm(): array
    {
        return [
            TextInput::make('api_key')
                ->label('API Key')
                ->password()
                ->revealable()
                ->required()
                ->helperText('Get your API key from https://pastefox.com/dashboard')
                ->default(fn () => config('pastefox-share.api_key')),
            Select::make('visibility')
                ->label('Default Visibility')
                ->options([
                    'PUBLIC' => 'Public',
                    'PRIVATE' => 'Private',
                ])
                ->default(fn () => config('pastefox-share.visibility', 'PUBLIC')),
        ];
    }

    public function saveSettings(array $data): void
    {
        $this->writeToEnvironment([
            'PASTEFOX_API_KEY' => $data['api_key'],
            'PASTEFOX_VISIBILITY' => $data['visibility'],
        ]);

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }
}
