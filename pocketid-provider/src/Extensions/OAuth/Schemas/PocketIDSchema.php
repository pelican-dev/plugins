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
    /**
     * Returns the identifier for the Pocket ID OAuth provider schema.
     *
     * @return string The provider identifier "pocketid".
     */
    public function getId(): string
    {
        return 'pocketid';
    }

    /**
     * Get the Socialite provider class used for Pocket ID.
     *
     * @return string The fully-qualified Socialite provider class name for Pocket ID.
     */
    public function getSocialiteProvider(): string
    {
        return Provider::class;
    }

    /**
     * Provide the service configuration array for the Pocket ID OAuth provider.
     *
     * @return array The service configuration array. Includes a 'base_url' entry sourced from the `OAUTH_POCKETID_BASE_URL` environment variable.
     */
    public function getServiceConfig(): array
    {
        return array_merge(parent::getServiceConfig(), [
            'base_url' => env('OAUTH_POCKETID_BASE_URL'),
        ]);
    }

    /**
     * Provide setup steps required to configure Pocket ID as an OAuth provider.
     *
     * Returns an array of setup Step instances that guide the user through configuring a Pocket ID application;
     * the steps include rendered HTML instructions and a disabled TextInput showing the required callback URL.
     *
     * @return array An array of Step objects for the setup UI, including an instructional HTML entry and a read-only callback URL field.
     */
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

    /**
     * Builds the settings form fields for the Pocket ID OAuth provider.
     *
     * Returns the provider-specific settings merged with the parent form and includes:
     * - `OAUTH_POCKETID_BASE_URL`: base URL for the Pocket ID service (URL-validated).
     * - `OAUTH_POCKETID_DISPLAY_NAME`: user-facing display name for the provider.
     * - `OAUTH_POCKETID_DISPLAY_COLOR`: hex color used for provider display.
     *
     * @return array An array of form field definitions for the settings form.
     */
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

    /**
     * Get the display name for the Pocket ID provider.
     *
     * @return string The display name from the `OAUTH_POCKETID_DISPLAY_NAME` environment variable, or `'Pocket ID'` if not set.
     */
    public function getName(): string
    {
        return env('OAUTH_POCKETID_DISPLAY_NAME', 'Pocket ID');
    }

    /**
     * Gets the UI icon identifier for the provider.
     *
     * @return string The icon identifier used to represent the provider (e.g., 'heroicon-o-identification').
     */
    public function getIcon(): string
    {
        return 'heroicon-o-identification';
    }

    /**
     * Get the provider's display color as a hex string.
     *
     * @return string The hex color used for the provider's display (e.g., '#000000'); defaults to '#000000' if not configured.
     */
    public function getHexColor(): string
    {
        return env('OAUTH_POCKETID_DISPLAY_COLOR', '#000000');
    }
}