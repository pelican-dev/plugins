<?php

namespace Boy132\UserCreatableServers\Filament\App\Pages;

use App\Exceptions\Service\Deployment\NoViableAllocationException;
use App\Exceptions\Service\Deployment\NoViableNodeException;
use App\Filament\Components\Forms\Fields\StartupVariable;
use App\Filament\Server\Pages\Console;
use App\Models\Egg;
use App\Services\Servers\RandomWordService;
use BackedEnum;
use Boy132\UserCreatableServers\Filament\App\Widgets\UserResourceLimitsOverview;
use Boy132\UserCreatableServers\Models\UserResourceLimits;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Arr;

/**
 * @property Schema $form
 */
class CreateServerPage extends Page
{
    use InteractsWithFormActions;
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'tabler-cube-plus';

    protected static ?string $slug = 'create-server';

    protected string $view = 'filament.server.pages.server-form-page';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function getTitle(): string
    {
        return trans('user-creatable-servers::strings.create_server');
    }

    public static function getNavigationLabel(): string
    {
        return trans('user-creatable-servers::strings.create_server');
    }

    public static function canAccess(): bool
    {
        return UserResourceLimits::where('user_id', auth()->user()->id)->exists();
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        /** @var UserResourceLimits $userResourceLimits */
        $userResourceLimits = UserResourceLimits::where('user_id', auth()->user()->id)->firstOrFail();

        return $schema
            ->statePath('data')
            ->columns(3)
            ->schema([
                TextInput::make('name')
                    ->label(trans('user-creatable-servers::strings.name'))
                    ->required()
                    ->default(fn () => (new RandomWordService())->word())
                    ->columnSpanFull(),
                Select::make('egg_id')
                    ->label(trans('user-creatable-servers::strings.egg'))
                    ->prefixIcon('tabler-egg')
                    ->options(fn () => Egg::all()->mapWithKeys(fn (Egg $egg) => [$egg->id => $egg->name]))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, Set $set) {
                        $egg = Egg::find($state);

                        $variables = $egg->variables ?? [];
                        $serverVariables = collect();
                        foreach ($variables as $variable) {
                            $serverVariables->add($variable->toArray());
                        }

                        $set('variables', $serverVariables->sortBy(['sort'])->all());
                        for ($i = 0; $i < $serverVariables->count(); $i++) {
                            $set("variables.$i.variable_value", $serverVariables[$i]['default_value']);
                            $set("variables.$i.variable_id", $serverVariables[$i]['id']);
                        }
                    })
                    ->columnSpanFull(),
                TextInput::make('cpu')
                    ->label(trans('user-creatable-servers::strings.cpu'))
                    ->required()
                    ->numeric()
                    ->minValue($userResourceLimits->cpu > 0 ? 1 : 0)
                    ->maxValue($userResourceLimits->getCpuLeft())
                    ->suffix('%'),
                TextInput::make('memory')
                    ->label(trans('user-creatable-servers::strings.memory'))
                    ->required()
                    ->numeric()
                    ->minValue($userResourceLimits->memory > 0 ? 1 : 0)
                    ->maxValue($userResourceLimits->getMemoryLeft())
                    ->suffix(config('panel.use_binary_prefix') ? 'MiB' : 'MB'),
                TextInput::make('disk')
                    ->label(trans('user-creatable-servers::strings.disk'))
                    ->required()
                    ->numeric()
                    ->minValue($userResourceLimits->disk > 0 ? 1 : 0)
                    ->maxValue($userResourceLimits->getDiskLeft())
                    ->suffix(config('panel.use_binary_prefix') ? 'MiB' : 'MB'),
                Repeater::make('variables')
                    ->label(trans('user-creatable-servers::strings.variables'))
                    ->hidden(fn (Get $get) => !$get('egg_id'))
                    ->grid(2)
                    ->columnSpanFull()
                    ->reorderable(false)
                    ->addable(false)
                    ->deletable(false)
                    ->default([])
                    ->hidden(fn ($state) => empty($state))
                    ->schema([
                        StartupVariable::make('variable_value')
                            ->fromForm()
                            ->disabled(false),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->hiddenLabel()
                ->action('save')
                ->keyBindings(['mod+s'])
                ->tooltip(trans('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->icon('tabler-device-floppy'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            UserResourceLimitsOverview::class,
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        try {
            /** @var UserResourceLimits $userResourceLimits */
            $userResourceLimits = UserResourceLimits::where('user_id', auth()->user()->id)->firstOrFail();

            if ($server = $userResourceLimits->createServer($data['name'], $data['egg_id'], $data['cpu'], $data['memory'], $data['disk'], Arr::mapWithKeys($data['variables'], fn ($value) => [$value['env_variable'] => $value['variable_value']]))) {
                $redirectUrl = Console::getUrl(panel: 'server', tenant: $server);
                $this->redirect($redirectUrl, navigate: FilamentView::hasSpaMode($redirectUrl));
            }
        } catch (Exception $exception) {
            report($exception);

            if ($exception instanceof NoViableNodeException) {
                Notification::make()
                    ->title(trans('user-creatable-servers::strings.notifications.server_creation_failed'))
                    ->body(trans('user-creatable-servers::strings.notifications.no_viable_node_found'))
                    ->danger()
                    ->send();
            } elseif ($exception instanceof NoViableAllocationException) {
                Notification::make()
                    ->title(trans('user-creatable-servers::strings.notifications.server_creation_failed'))
                    ->body(trans('user-creatable-servers::strings.notifications.no_viable_allocation_found'))
                    ->danger()
                    ->send();
            } else {
                Notification::make()
                    ->title(trans('user-creatable-servers::strings.notifications.server_creation_failed'))
                    ->body(trans('user-creatable-servers::strings.notifications.unknown_server_creation_error'))
                    ->danger()
                    ->send();
            }
        }
    }
}
