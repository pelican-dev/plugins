<?php

namespace FlexKleks\PasteFoxShare\Filament\Admin\Pages;

use App\Traits\EnvironmentWriterTrait;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * @property Schema $form
 */
class PasteFoxSettings extends Page
{
    use EnvironmentWriterTrait;
    use InteractsWithFormActions;
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-share';

    protected string $view = 'filament.server.pages.server-form-page';

    /** @var array<mixed>|null */
    public ?array $data = [];

    public function getTitle(): string
    {
        return 'PasteFox Settings';
    }

    public static function getNavigationLabel(): string
    {
        return 'PasteFox';
    }

    public static function getNavigationGroup(): ?string
    {
        return trans('admin/dashboard.advanced');
    }

    public function mount(): void
    {
        $this->form->fill([
            'api_key' => config('pastefox-share.api_key'),
            'visibility' => config('pastefox-share.visibility', 'PUBLIC'),
            'effect' => config('pastefox-share.effect', 'NONE'),
            'password' => config('pastefox-share.password'),
            'theme' => config('pastefox-share.theme', 'dark'),
        ]);
    }

    /**
     * @return Component[]
     */
    public function getFormSchema(): array
    {
        return [
            Section::make('API Configuration')
                ->description('Without API key, pastes expire after 7 days and are always public.')
                ->schema([
                    TextInput::make('api_key')
                        ->label('API Key')
                        ->password()
                        ->revealable()
                        ->helperText('Optional - Get your API key from https://pastefox.com/dashboard'),
                ]),

            Section::make('Paste Settings')
                ->schema([
                    Select::make('visibility')
                        ->label('Visibility')
                        ->options([
                            'PUBLIC' => 'Public',
                            'PRIVATE' => 'Private (requires API key)',
                        ])
                        ->default('PUBLIC')
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
                        ->default('NONE'),

                    Select::make('theme')
                        ->label('Theme')
                        ->options([
                            'dark' => 'Dark',
                            'light' => 'Light',
                        ])
                        ->default('dark'),

                    TextInput::make('password')
                        ->label('Password Protection')
                        ->password()
                        ->revealable()
                        ->minLength(4)
                        ->maxLength(100)
                        ->helperText('Optional - 4-100 characters'),
                ]),
        ];
    }

    protected function getFormStatePath(): ?string
    {
        return 'data';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label(trans('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->action('save')
                ->keyBindings(['mod+s']),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

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
