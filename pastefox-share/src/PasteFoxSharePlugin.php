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

    public function register(Panel $panel): void {}

    public function boot(Panel $panel): void {}

    public function getSettingsForm(): array
    {
        return [
            Section::make(trans('pastefox-share::messages.section_api'))
                ->description(trans('pastefox-share::messages.section_api_description'))
                ->schema([
                    TextInput::make('api_key')
                        ->label(trans('pastefox-share::messages.api_key'))
                        ->password()
                        ->revealable()
                        ->helperText(trans('pastefox-share::messages.api_key_helper'))
                        ->default(fn () => config('pastefox-share.api_key')),
                ]),

            Section::make(trans('pastefox-share::messages.section_paste'))
                ->schema([
                    Select::make('visibility')
                        ->label(trans('pastefox-share::messages.visibility'))
                        ->options([
                            'PUBLIC' => trans('pastefox-share::messages.visibility_public'),
                            'PRIVATE' => trans('pastefox-share::messages.visibility_private'),
                        ])
                        ->default(fn () => config('pastefox-share.visibility', 'PUBLIC'))
                        ->helperText(trans('pastefox-share::messages.visibility_helper')),

                    Select::make('effect')
                        ->label(trans('pastefox-share::messages.effect'))
                        ->options([
                            'NONE' => trans('pastefox-share::messages.effect_none'),
                            'MATRIX' => trans('pastefox-share::messages.effect_matrix'),
                            'GLITCH' => trans('pastefox-share::messages.effect_glitch'),
                            'CONFETTI' => trans('pastefox-share::messages.effect_confetti'),
                            'SCRATCH' => trans('pastefox-share::messages.effect_scratch'),
                            'PUZZLE' => trans('pastefox-share::messages.effect_puzzle'),
                            'SLOTS' => trans('pastefox-share::messages.effect_slots'),
                            'SHAKE' => trans('pastefox-share::messages.effect_shake'),
                            'FIREWORKS' => trans('pastefox-share::messages.effect_fireworks'),
                            'TYPEWRITER' => trans('pastefox-share::messages.effect_typewriter'),
                            'BLUR' => trans('pastefox-share::messages.effect_blur'),
                        ])
                        ->default(fn () => config('pastefox-share.effect', 'NONE')),

                    Select::make('theme')
                        ->label(trans('pastefox-share::messages.theme'))
                        ->options([
                            'dark' => trans('pastefox-share::messages.theme_dark'),
                            'light' => trans('pastefox-share::messages.theme_light'),
                        ])
                        ->default(fn () => config('pastefox-share.theme', 'dark')),

                    TextInput::make('password')
                        ->label(trans('pastefox-share::messages.password'))
                        ->password()
                        ->revealable()
                        ->minLength(4)
                        ->maxLength(100)
                        ->helperText(trans('pastefox-share::messages.password_helper'))
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
            ->title(trans('pastefox-share::messages.settings_saved'))
            ->success()
            ->send();
    }
}
