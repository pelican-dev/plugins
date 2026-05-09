<?php

namespace Boy132\Register;

use App\Contracts\Plugins\HasPluginSettings;
use Boy132\Register\Filament\Pages\Auth\Register;
use Filament\Contracts\Plugin;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Panel;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Artisan;
use Throwable;

class RegisterPlugin implements HasPluginSettings, Plugin
{
    public function getId(): string
    {
        return 'register';
    }

    public function register(Panel $panel): void
    {
        $panel->registration(Register::class);
    }

    public function boot(Panel $panel): void {}

    public function getSettingsForm(): array
    {
        return [
            Section::make('Registration Limits')
                ->columns(1)
                ->schema([
                    TextInput::make('max_users')
                        ->label('Maximum number of users')
                        ->helperText('Set to 0 for unlimited registrations.')
                        ->required()
                        ->numeric()
                        ->minValue(0)
                        ->default(fn () => config('register.max_users')),
                ]),
            Section::make('Default Resource Limits for New Users')
                ->columns(4)
                ->schema([
                    TextInput::make('default_cpu')
                        ->label('CPU (%)')
                        ->required()
                        ->numeric()
                        ->minValue(0)
                        ->default(fn () => config('register.default_cpu')),
                    TextInput::make('default_memory')
                        ->label('RAM (MB)')
                        ->required()
                        ->numeric()
                        ->minValue(0)
                        ->default(fn () => config('register.default_memory')),
                    TextInput::make('default_disk')
                        ->label('Disk (MB)')
                        ->required()
                        ->numeric()
                        ->minValue(0)
                        ->default(fn () => config('register.default_disk')),
                    TextInput::make('default_server_limit')
                        ->label('Server limit')
                        ->helperText('Set to 0 for unlimited servers.')
                        ->required()
                        ->numeric()
                        ->minValue(0)
                        ->default(fn () => config('register.default_server_limit')),
                ]),
        ];
    }

    public function saveSettings(array $data): void
    {
        try {
            $this->persistSettingsToConfig($data);

            Notification::make()
                ->title('Settings saved')
                ->success()
                ->send();
        } catch (Throwable $exception) {
            report($exception);

            Notification::make()
                ->title('Unable to save settings')
                ->body('Could not write to register/config/register.php. Check file permissions.')
                ->danger()
                ->send();
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function persistSettingsToConfig(array $data): void
    {
        $configPath = plugin_path($this->getId(), 'config/register.php');

        $config = [
            'max_users' => max(0, (int) ($data['max_users'] ?? 0)),
            'default_cpu' => max(0, (int) ($data['default_cpu'] ?? 0)),
            'default_memory' => max(0, (int) ($data['default_memory'] ?? 0)),
            'default_disk' => max(0, (int) ($data['default_disk'] ?? 0)),
            'default_server_limit' => max(0, (int) ($data['default_server_limit'] ?? 0)),
        ];

        $content = <<<'PHP'
<?php

return 
PHP;

        $content .= var_export($config, true);
        $content .= ";\n";

        if (file_put_contents($configPath, $content, LOCK_EX) === false) {
            throw new \RuntimeException('Failed writing register config file.');
        }

        foreach ($config as $key => $value) {
            config()->set("register.{$key}", $value);
        }

        if (app()->configurationIsCached()) {
            Artisan::call('config:clear');
        }
    }
}
