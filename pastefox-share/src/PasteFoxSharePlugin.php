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
use Illuminate\Support\Facades\Http;

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

            Section::make(trans('pastefox-share::messages.section_custom_domain'))
                ->description(trans('pastefox-share::messages.section_custom_domain_description'))
                ->schema([
                    Select::make('custom_domain')
                        ->label(trans('pastefox-share::messages.custom_domain'))
                        ->options(fn () => $this->getCustomDomainOptions())
                        ->disableOptionWhen(fn (string $value): bool => str_ends_with($value, ':disabled'))
                        ->default(fn () => config('pastefox-share.custom_domain'))
                        ->helperText(fn () => filled(config('pastefox-share.api_key'))
                            ? trans('pastefox-share::messages.custom_domain_helper')
                            : trans('pastefox-share::messages.custom_domain_no_api_key'))
                        ->disabled(fn () => blank(config('pastefox-share.api_key'))),
                ]),
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function getCustomDomainOptions(): array
    {
        $options = ['' => trans('pastefox-share::messages.custom_domain_none')];

        $apiKey = config('pastefox-share.api_key');
        if (blank($apiKey)) {
            return $options;
        }

        try {
            $response = Http::withHeaders([
                'X-API-Key' => $apiKey,
                'Content-Type' => 'application/json',
            ])
                ->timeout(10)
                ->get('https://pastefox.com/api/domains')
                ->json();

            if ($response['success'] ?? false) {
                foreach ($response['domains'] ?? [] as $domain) {
                    if ($domain['status'] !== 'ACTIVE') {
                        continue;
                    }

                    if ($domain['isActive'] ?? false) {
                        $options[$domain['domain']] = $domain['domain'];
                    } else {
                        $options[$domain['domain'] . ':disabled'] = $domain['domain'] . ' (' . trans('pastefox-share::messages.custom_domain_inactive') . ')';
                    }
                }
            }
        } catch (\Exception $e) {
            // Silently fail, just return default options
        }

        return $options;
    }

    public function saveSettings(array $data): void
    {
        $this->writeToEnvironment([
            'PASTEFOX_API_KEY' => $data['api_key'] ?? '',
            'PASTEFOX_VISIBILITY' => $data['visibility'] ?? 'PUBLIC',
            'PASTEFOX_EFFECT' => $data['effect'] ?? 'NONE',
            'PASTEFOX_THEME' => $data['theme'] ?? 'dark',
            'PASTEFOX_PASSWORD' => $data['password'] ?? '',
            'PASTEFOX_CUSTOM_DOMAIN' => $data['custom_domain'] ?? '',
        ]);

        Notification::make()
            ->title(trans('pastefox-share::messages.settings_saved'))
            ->success()
            ->send();
    }
}
