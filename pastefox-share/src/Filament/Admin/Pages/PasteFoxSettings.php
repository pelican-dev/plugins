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
        ]);
    }

    /**
     * @return Component[]
     */
    public function getFormSchema(): array
    {
        return [
            TextInput::make('api_key')
                ->label('API Key')
                ->password()
                ->revealable()
                ->required()
                ->helperText('Get your API key from https://pastefox.com/dashboard'),
            Select::make('visibility')
                ->label('Default Visibility')
                ->options([
                    'PUBLIC' => 'Public',
                    'PRIVATE' => 'Private',
                ])
                ->default('PUBLIC'),
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
            'PASTEFOX_API_KEY' => $data['api_key'],
            'PASTEFOX_VISIBILITY' => $data['visibility'],
        ]);

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }
}
