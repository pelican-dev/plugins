<?php

namespace FlexKleks\ServerFolders\Filament\App\Resources\ServerFolders\Pages;

use App\Models\Role;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use FlexKleks\ServerFolders\Filament\App\Resources\ServerFolders\ServerFolderResource;
use Illuminate\Contracts\Support\Htmlable;

class ViewServerFolder extends Page
{
    use InteractsWithRecord;

    protected static string $resource = ServerFolderResource::class;

    protected string $view = 'server-folders::view-folder';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        abort_unless($this->record->isVisibleTo(auth()->user()), 403);
    }

    public function getTitle(): string|Htmlable
    {
        return $this->record->name;
    }

    public function getBreadcrumb(): string
    {
        return $this->record->name;
    }

    public function isOwner(): bool
    {
        return $this->record->isEditableBy(auth()->user());
    }

    public function getServers()
    {
        return $this->record->servers()->with(['egg', 'node', 'allocation'])->get();
    }

    protected function getHeaderActions(): array
    {
        $actions = [];

        // Only owner can add servers
        if ($this->isOwner()) {
            $actions[] = Action::make('addServer')
                ->label(trans('server-folders::messages.add_server'))
                ->icon('tabler-plus')
                ->form([
                    Select::make('server_id')
                        ->label(trans('server-folders::messages.select_server'))
                        ->options(function () {
                            $existingIds = $this->record->servers->pluck('id')->toArray();

                            return auth()->user()->accessibleServers()
                                ->whereNotIn('servers.id', $existingIds)
                                ->pluck('servers.name', 'servers.id');
                        })
                        ->searchable()
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->record->servers()->attach($data['server_id']);

                    Notification::make()
                        ->title(trans('server-folders::messages.server_added'))
                        ->success()
                        ->send();
                });

            $actions[] = Action::make('edit')
                ->label(trans('server-folders::messages.edit'))
                ->icon('tabler-pencil')
                ->color('gray')
                ->form([
                    TextInput::make('name')
                        ->label(trans('server-folders::messages.folder_name'))
                        ->required()
                        ->maxLength(50)
                        ->default(fn () => $this->record->name),
                    ColorPicker::make('color')
                        ->label(trans('server-folders::messages.color'))
                        ->default(fn () => $this->record->color),
                    Toggle::make('is_shared')
                        ->label(trans('server-folders::messages.share_folder'))
                        ->helperText(trans('server-folders::messages.share_folder_hint'))
                        ->default(fn () => $this->record->is_shared)
                        ->live(),
                    Select::make('roles')
                        ->label(trans('server-folders::messages.shared_with_roles'))
                        ->helperText(trans('server-folders::messages.shared_with_roles_hint'))
                        ->multiple()
                        ->options(Role::pluck('name', 'id'))
                        ->default(fn () => $this->record->roles->pluck('id')->toArray())
                        ->searchable()
                        ->preload()
                        ->visible(fn ($get) => $get('is_shared')),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'name' => $data['name'],
                        'color' => $data['color'],
                        'is_shared' => $data['is_shared'] ?? false,
                    ]);

                    if ($data['is_shared'] ?? false) {
                        $this->record->roles()->sync($data['roles'] ?? []);
                    } else {
                        $this->record->roles()->detach();
                    }

                    Notification::make()
                        ->title(trans('server-folders::messages.folder_updated'))
                        ->success()
                        ->send();
                });

            $actions[] = DeleteAction::make()
                ->record($this->record)
                ->successRedirectUrl(ServerFolderResource::getUrl());
        }

        return $actions;
    }

    public function removeServer(int $serverId): void
    {
        // Only owner can remove servers
        if (!$this->isOwner()) {
            Notification::make()
                ->title(trans('server-folders::messages.no_permission'))
                ->danger()
                ->send();

            return;
        }

        $this->record->servers()->detach($serverId);

        Notification::make()
            ->title(trans('server-folders::messages.server_removed'))
            ->success()
            ->send();
    }
}
