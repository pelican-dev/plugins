<?php

namespace Boy132\Billing;

use App\Contracts\Plugins\HasPluginSettings;
use App\Enums\CustomizationKey;
use App\Filament\App\Resources\Servers\ServerResource;
use App\Filament\Pages\Auth\EditProfile;
use App\Traits\EnvironmentWriterTrait;
use Filament\Contracts\Plugin;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Navigation\NavigationItem;
use Filament\Notifications\Notification;
use Filament\Panel;

class BillingPlugin implements HasPluginSettings, Plugin
{
    use EnvironmentWriterTrait;

    public function getId(): string
    {
        return 'billing';
    }

    public function register(Panel $panel): void
    {
        $id = str($panel->getId())->title();

        if ($panel->getId() === 'app') {
            ServerResource::embedServerList();

            $panel->navigation(true);
            $panel->topbar(function () {
                $navigationType = user()?->getCustomization(CustomizationKey::TopNavigation);

                return $navigationType === 'topbar' || $navigationType === 'mixed' || $navigationType === true;
            });

            $panel->navigationItems([
                NavigationItem::make(fn () => trans('filament-panels::auth/pages/edit-profile.label'))
                    ->icon('tabler-user-circle')
                    ->url(fn () => EditProfile::getUrl(panel: 'app'))
                    ->isActiveWhen(fn () => request()->routeIs(EditProfile::getRouteName()))
                    ->sort(99),
            ]);

            $panel->clearCachedComponents();
        }

        $panel->discoverResources(plugin_path($this->getId(), "src/Filament/$id/Resources"), "Boy132\\Billing\\Filament\\$id\\Resources");
        $panel->discoverPages(plugin_path($this->getId(), "src/Filament/$id/Pages"), "Boy132\\Billing\\Filament\\$id\\Pages");
        $panel->discoverWidgets(plugin_path($this->getId(), "src/Filament/$id/Widgets"), "Boy132\\Billing\\Filament\\$id\\Widgets");
    }

    public function boot(Panel $panel): void {}

    public function getSettingsFormData(): array
    {
        $data = config('billing');

        $data['deployment_tags'] = array_filter(explode(',', $data['deployment_tags']));

        return $data;
    }

    public function getSettingsForm(): array
    {
        return [
            TextInput::make('key')
                ->label('Stripe Key')
                ->required(),
            TextInput::make('secret')
                ->label('Stripe Secret')
                ->required(),
            Select::make('currency')
                ->label('Currency')
                ->required()
                ->options([
                    'USD' => 'US Dollar',
                    'EUR' => 'Euro',
                    'GBP' => 'British Pound',
                ]),
            TagsInput::make('deployment_tags')
                ->label('Default node tags for deployment'),
        ];
    }

    public function saveSettings(array $data): void
    {
        $this->writeToEnvironment([
            'STRIPE_KEY' => $data['key'],
            'STRIPE_SECRET' => $data['secret'],
            'BILLING_CURRENCY' => $data['currency'],
            'BILLING_DEPLOYMENT_TAGS' => implode(',', $data['deployment_tags']),
        ]);

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }
}
