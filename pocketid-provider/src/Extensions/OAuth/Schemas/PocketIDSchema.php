<?php

namespace Ebnater\PocketIDProvider\Extensions\OAuth\Schemas;

use App\Extensions\OAuth\Schemas\OAuthSchema;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use SocialiteProviders\PocketID\Provider;

final class PocketIDSchema extends OAuthSchema
{
    public function getId(): string
    {
        return 'pocketid';
    }

    public function getSocialiteProvider(): string
    {
        return Provider::class;
    }

    public function getServiceConfig(): array
    {
        return array_merge(parent::getServiceConfig(), [
            'base_url' => env('OAUTH_POCKETID_BASE_URL'),
        ]);
    }

    public function getSetupSteps(): array
    {
        return array_merge([
            Step::make('Configure Pocket ID Application')
                ->schema([
                    TextEntry::make('instructions')
                        ->hiddenLabel()
                        ->state(new HtmlString(Blade::render('
                            <ol class="list-decimal list-inside space-y-1">
                                <li>Log in to your Pocket ID instance</li>
                                <li>Navigate to your application or create a new OAuth application</li>
                                <li>Copy the <strong>Client ID</strong> and <strong>Client Secret</strong> from your Pocket ID application</li>
                                <li>Configure the redirect URL shown below in your Pocket ID application settings</li>
                            </ol>
                        '))),
                    TextInput::make('_noenv_callback')
                        ->label('Callback URL')
                        ->dehydrated()
                        ->disabled()
                        ->default(fn () => url('/auth/oauth/callback/pocketid')),
                ]),
        ], parent::getSetupSteps());
    }

    public function getSettingsForm(): array
    {
        return array_merge(parent::getSettingsForm(), [
            TextInput::make('OAUTH_POCKETID_BASE_URL')
                ->label('Base URL')
                ->placeholder('https://id.example.com')
                ->columnSpan(2)
                ->required()
                ->url()
                ->autocomplete(false)
                ->default(env('OAUTH_POCKETID_BASE_URL')),
            TextInput::make('OAUTH_POCKETID_DISPLAY_NAME')
                ->label('Display Name')
                ->placeholder('Pocket ID')
                ->autocomplete(false)
                ->default(env('OAUTH_POCKETID_DISPLAY_NAME', 'Pocket ID')),
            ColorPicker::make('OAUTH_POCKETID_DISPLAY_COLOR')
                ->label('Display Color')
                ->placeholder('#000000')
                ->default(env('OAUTH_POCKETID_DISPLAY_COLOR', '#000000'))
                ->hex(),
        ]);
    }

    public function getName(): string
    {
        return env('OAUTH_POCKETID_DISPLAY_NAME', 'Pocket ID');
    }

    public function getIcon(): string
    {
        return 'tabler-id';
    }

    public function getHexColor(): string
    {
        return env('OAUTH_POCKETID_DISPLAY_COLOR', '#000000');
    }
}
