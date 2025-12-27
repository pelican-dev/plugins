<?php

namespace FlexKleks\ServerFolders\Filament\Server\Components;

use App\Models\Server;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use FlexKleks\ServerFolders\Models\ServerFolder;

class AddToFolderAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'add_to_folder';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(fn () => trans('server-folders::messages.move_to_folder'));

        $this->icon('tabler-folder');

        $this->color('gray');

        $this->form([
            Select::make('folder_id')
                ->label(trans('server-folders::messages.select_folder'))
                ->options(fn () => ServerFolder::where('user_id', auth()->id())->pluck('name', 'id'))
                ->searchable()
                ->required(),
        ]);

        $this->action(function (array $data) {
            /** @var Server $server */
            $server = Filament::getTenant();

            $folder = ServerFolder::where('user_id', auth()->id())
                ->find($data['folder_id']);

            if ($folder) {
                // Remove from other folders first
                ServerFolder::where('user_id', auth()->id())
                    ->get()
                    ->each(fn ($f) => $f->servers()->detach($server->id));

                // Add to selected folder
                $folder->servers()->attach($server->id);

                Notification::make()
                    ->title(trans('server-folders::messages.server_added'))
                    ->success()
                    ->send();
            }
        });
    }
}
