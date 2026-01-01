<?php

namespace Boy132\UserCreatableServers;

use App\Contracts\Plugins\HasPluginSettings;
use App\Traits\EnvironmentWriterTrait;
use Filament\Contracts\Plugin;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Panel;
use Filament\Schemas\Components\Section;

class UserCreatableServersPlugin implements HasPluginSettings, Plugin
{
    use EnvironmentWriterTrait;

    public function getId(): string
    {
        return 'user-creatable-servers';
    }

    public function register(Panel $panel): void
    {
        $id = str($panel->getId())->title();

        $panel->discoverPages(plugin_path($this->getId(), "src/Filament/$id/Pages"), "Boy132\\UserCreatableServers\\Filament\\$id\\Pages");
        $panel->discoverResources(plugin_path($this->getId(), "src/Filament/$id/Resources"), "Boy132\\UserCreatableServers\\Filament\\$id\\Resources");
        $panel->discoverWidgets(plugin_path($this->getId(), "src/Filament/$id/Widgets"), "Boy132\\UserCreatableServers\\Filament\\$id\\Widgets");
    }

    public function boot(Panel $panel): void {}

    public function getSettingsForm(): array
    {
        return [
            Section::make('Limits')
                ->columns(3)
                ->schema([
                    TextInput::make('database_limit')
                        ->label('Default database limit')
                        ->required()
                        ->numeric()
                        ->minValue(0)
                        ->default(fn () => config('user-creatable-servers.database_limit')),
                    TextInput::make('allocation_limit')
                        ->label('Default allocation limit')
                        ->required()
                        ->numeric()
                        ->minValue(0)
                        ->default(fn () => config('user-creatable-servers.allocation_limit')),
                    TextInput::make('backup_limit')
                        ->label('Default backup limit')
                        ->required()
                        ->numeric()
                        ->minValue(0)
                        ->default(fn () => config('user-creatable-servers.backup_limit')),
                ]),
            Section::make('User Settings')
                ->columns()
                ->schema([
                    Toggle::make('can_users_update_servers')
                        ->label('Can users update servers?')
                        ->hintIcon('tabler-question-mark')
                        ->hintIconTooltip('If checked users can update the resource limits of their servers after creation.')
                        ->inline(false)
                        ->default(fn () => config('user-creatable-servers.can_users_update_servers')),
                    Toggle::make('can_users_delete_servers')
                        ->label('Can users delete servers?')
                        ->hintIcon('tabler-question-mark')
                        ->hintIconTooltip('If checked users can delete their own servers.')
                        ->inline(false)
                        ->default(fn () => config('user-creatable-servers.can_users_delete_servers')),
                ]),
            Section::make('Deployment Settings')
                ->columns()
                ->schema([
                    TagsInput::make('deployment_tags')
                        ->label('Node tags')
                        ->hintIcon('tabler-question-mark')
                        ->hintIconTooltip('Only nodes with these tags will be used for deployment. Leave empty to allow all nodes.')
                        ->default(fn () => array_filter(explode(',', config('user-creatable-servers.deployment_tags')))),
                    TagsInput::make('deployment_ports')
                        ->label('Ports')
                        ->placeholder('New port or port range')
                        ->hintIcon('tabler-question-mark')
                        ->hintIconTooltip('These ports will be used for deployment. You can enter individual ports or port ranges. (e.g. 8000-8100) Leave empty to create servers with any allocations.')
                        ->default(fn () => array_filter(explode(',', config('user-creatable-servers.deployment_ports')))),
                ]),
        ];
    }

    public function saveSettings(array $data): void
    {
        $this->writeToEnvironment([
            'UCS_DEFAULT_DATABASE_LIMIT' => $data['database_limit'],
            'UCS_DEFAULT_ALLOCATION_LIMIT' => $data['allocation_limit'],
            'UCS_DEFAULT_BACKUP_LIMIT' => $data['backup_limit'],
            'UCS_CAN_USERS_UPDATE_SERVERS' => $data['can_users_update_servers'] ? 'true' : 'false',
            'UCS_CAN_USERS_DELETE_SERVERS' => $data['can_users_delete_servers'] ? 'true' : 'false',
            'UCS_DEPLOYMENT_TAGS' => implode(',', $data['deployment_tags']),
            'UCS_DEPLOYMENT_PORTS' => implode(',', $data['deployment_ports']),
        ]);

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }
}
