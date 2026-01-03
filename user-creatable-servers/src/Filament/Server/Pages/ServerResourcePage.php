<?php

namespace Boy132\UserCreatableServers\Filament\Server\Pages;

use App\Filament\Server\Pages\ServerFormPage;
use App\Models\Server;
use App\Repositories\Daemon\DaemonServerRepository;
use App\Services\Servers\ServerDeletionService;
use Boy132\UserCreatableServers\Filament\App\Widgets\UserResourceLimitsOverview;
use Boy132\UserCreatableServers\Models\UserResourceLimits;
use Exception;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Support\Facades\FilamentView;
use Illuminate\Http\Client\ConnectionException;

class ServerResourcePage extends ServerFormPage
{
    protected static string|\BackedEnum|null $navigationIcon = 'tabler-cube-plus';

    protected static ?string $navigationLabel = 'Resource Limits';

    protected static ?string $title = 'Resource Limits';

    public static function canAccess(): bool
    {
        if (!config('user-creatable-servers.can_users_update_servers')) {
            return false;
        }

        /** @var Server $server */
        $server = Filament::getTenant();

        if (!UserResourceLimits::where('user_id', $server->owner_id)->exists()) {
            return false;
        }

        return parent::canAccess();
    }

    protected static ?int $navigationSort = 20;

    public function form(Schema $schema): Schema
    {
        /** @var Server $server */
        $server = Filament::getTenant();

        /** @var UserResourceLimits $userResourceLimits */
        $userResourceLimits = UserResourceLimits::where('user_id', $server->owner_id)->firstOrFail();

        $maxCpu = $server->cpu + $userResourceLimits->getCpuLeft();
        $maxMemory = $server->memory + $userResourceLimits->getMemoryLeft();
        $maxDisk = $server->disk + $userResourceLimits->getDiskLeft();

        $suffix = config('panel.use_binary_prefix') ? 'MiB' : 'MB';

        return $schema
            ->columns([
                'default' => 1,
                'lg' => 3,
            ])
            ->schema([
                TextInput::make('cpu')
                    ->label(trans('user-creatable-servers::strings.cpu'))
                    ->required()
                    ->live(onBlur: true)
                    ->hint(fn ($state) => $userResourceLimits->cpu > 0 ? ($maxCpu - $state . '% ' . trans('user-creatable-servers::strings.left')) : trans('user-creatable-servers::strings.unlimited'))
                    ->hintColor(fn ($state) => $userResourceLimits->cpu > 0 && $maxCpu - $state < 0 ? 'danger' : null)
                    ->numeric()
                    ->minValue(1)
                    ->maxValue($userResourceLimits->cpu > 0 ? $maxCpu : null)
                    ->suffix('%'),
                TextInput::make('memory')
                    ->label(trans('user-creatable-servers::strings.memory'))
                    ->required()
                    ->live(onBlur: true)
                    ->hint(fn ($state) => $userResourceLimits->memory > 0 ? ($maxMemory - $state . $suffix . ' ' . trans('user-creatable-servers::strings.left')) : trans('user-creatable-servers::strings.unlimited'))
                    ->hintColor(fn ($state) => $userResourceLimits->memory > 0 && $maxMemory - $state < 0 ? 'danger' : null)
                    ->numeric()
                    ->minValue(1)
                    ->maxValue($userResourceLimits->memory > 0 ? $maxMemory : null)
                    ->suffix($suffix),
                TextInput::make('disk')
                    ->label(trans('user-creatable-servers::strings.disk'))
                    ->required()
                    ->live(onBlur: true)
                    ->hint(fn ($state) => $userResourceLimits->disk > 0 ? ($maxDisk - $state . $suffix . ' ' . trans('user-creatable-servers::strings.left')) : trans('user-creatable-servers::strings.unlimited'))
                    ->hintColor(fn ($state) => $userResourceLimits->disk > 0 && $maxDisk - $state < 0 ? 'danger' : null)
                    ->numeric()
                    ->minValue(1)
                    ->maxValue($userResourceLimits->disk > 0 ? $maxDisk : null)
                    ->suffix($suffix),
            ]);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            UserResourceLimitsOverview::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        /** @var Server $server */
        $server = Filament::getTenant();

        return [
            Action::make('save')
                ->label(trans('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->submit('save')
                ->formId('form')
                ->keyBindings(['mod+s']),
            Action::make('delete_server')
                ->visible(fn () => config('user-creatable-servers.can_users_delete_servers'))
                ->authorize(fn () => $server->owner_id === auth()->user()->id || auth()->user()->can('delete server', $server))
                ->label(trans('user-creatable-servers::strings.modals.delete_server'))
                ->color('danger')
                ->icon('tabler-trash')
                ->requiresConfirmation()
                ->modalHeading(trans('user-creatable-servers::strings.modals.delete_server_confirm'))
                ->modalDescription(trans('user-creatable-servers::strings.modals.delete_server_warning'))
                ->modalSubmitActionLabel(trans('user-creatable-servers::strings.modals.delete_server'))
                ->action(function (ServerDeletionService $service) use ($server) {
                    try {
                        $service->handle($server);

                        Notification::make()
                            ->title(trans('user-creatable-servers::strings.notifications.server_deleted'))
                            ->body(trans('user-creatable-servers::strings.notifications.server_deleted_success'))
                            ->success()
                            ->send();

                        redirect(Filament::getDefaultPanel()->getUrl());
                    } catch (Exception $exception) {
                        report($exception);

                        Notification::make()
                            ->title(trans('user-creatable-servers::strings.notifications.server_delete_error'))
                            ->body($exception->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        /** @var Server $server */
        $server = Filament::getTenant();

        $server->update([
            'cpu' => $data['cpu'],
            'memory' => $data['memory'],
            'disk' => $data['disk'],
        ]);

        try {
            /** @var DaemonServerRepository $repository */
            $repository = app(DaemonServerRepository::class);

            $repository->setServer($server)->sync();

            Notification::make()
                ->title(trans('user-creatable-servers::strings.notifications.server_resources_updated'))
                ->body(trans('user-creatable-servers::strings.notifications.might_need_restart'))
                ->success()
                ->persistent()
                ->send();
        } catch (ConnectionException) {
            Notification::make()
                ->title(trans('user-creatable-servers::strings.notifications.server_resources_updated'))
                ->body(trans('user-creatable-servers::strings.notifications.manual_restart_needed'))
                ->warning()
                ->persistent()
                ->send();
        }

        $redirectUrl = self::getUrl();
        $this->redirect($redirectUrl, navigate: FilamentView::hasSpaMode($redirectUrl));
    }
}
