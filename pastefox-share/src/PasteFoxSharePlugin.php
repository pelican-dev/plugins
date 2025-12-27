<?php

namespace FlexKleks\PasteFoxShare;

use App\Contracts\Plugins\HasPluginSettings;
use App\Traits\EnvironmentWriterTrait;
use Filament\Contracts\Plugin;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Panel;
use Filament\Schemas\Components\Section;

class PasteFoxSharePlugin implements HasPluginSettings, Plugin
{
    use EnvironmentWriterTrait;

    public function getId(): string
    {
        return 'pastefox-share';
    }

    public function register(Panel $panel): void
    {
        if ($panel->getId() === 'server') {
            $id = str($panel->getId())->title();

            $panel->discoverPages(
                plugin_path($this->getId(), "src/Filament/$id/Pages"),
                "FlexKleks\\PasteFoxShare\\Filament\\$id\\Pages"
            );
        }
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public function getSettingsForm(): array
    {
        return [
            Section::make('API Configuration')
                ->description('Without API key, pastes expire after 7 days and are always public.')
                ->schema([
                    TextInput::make('api_key')
                        ->label('API Key')
                        ->password()
                        ->revealable()
                        ->helperText('Optional - Get your API key from https://pastefox.com/dashboard')
                        ->default(fn () => config('pastefox-share.api_key')),
                ]),

            Section::make('Paste Settings')
                ->schema([
                    Select::make('visibility')
                        ->label('Visibility')
                        ->options([
                            'PUBLIC' => 'Public',
                            'PRIVATE' => 'Private (requires API key)',
                        ])
                        ->default(fn () => config('pastefox-share.visibility', 'PUBLIC'))
                        ->helperText('Private pastes require an API key'),

                    Select::make('effect')
                        ->label('Visual Effect')
                        ->options([
                            'NONE' => 'None',
                            'MATRIX' => 'Matrix Rain',
                            'GLITCH' => 'Glitch',
                            'CONFETTI' => 'Confetti',
                            'SCRATCH' => 'Scratch Card',
                            'PUZZLE' => 'Puzzle Reveal',
                            'SLOTS' => 'Slot Machine',
                            'SHAKE' => 'Shake',
                            'FIREWORKS' => 'Fireworks',
                            'TYPEWRITER' => 'Typewriter',
                            'BLUR' => 'Blur Reveal',
                        ])
                        ->default(fn () => config('pastefox-share.effect', 'NONE')),

                    Select::make('theme')
                        ->label('Theme')
                        ->options([
                            'dark' => 'Dark',
                            'light' => 'Light',
                        ])
                        ->default(fn () => config('pastefox-share.theme', 'dark')),

                    TextInput::make('password')
                        ->label('Password Protection')
                        ->password()
                        ->revealable()
                        ->minLength(4)
                        ->maxLength(100)
                        ->helperText('Optional - 4-100 characters')
                        ->default(fn () => config('pastefox-share.password')),
                ]),
        ];
    }

    public function saveSettings(array $data): void
    {
        $this->writeToEnvironment([
            'PASTEFOX_API_KEY' => $data['api_key'] ?? '',
            'PASTEFOX_VISIBILITY' => $data['visibility'] ?? 'PUBLIC',
            'PASTEFOX_EFFECT' => $data['effect'] ?? 'NONE',
            'PASTEFOX_THEME' => $data['theme'] ?? 'dark',
            'PASTEFOX_PASSWORD' => $data['password'] ?? '',
        ]);

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }
}
