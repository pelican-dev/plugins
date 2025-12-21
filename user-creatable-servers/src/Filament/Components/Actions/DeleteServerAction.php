<?php

namespace Boy132\UserCreatableServers\Filament\Components\Actions;

use App\Filament\App\Resources\Servers\Pages\ListServers;
use App\Models\Server;
use App\Services\Servers\ServerDeletionService;
use Boy132\UserCreatableServers\Models\UserResourceLimits;
use Exception;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;

class DeleteServerAction extends DeleteAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Delete Server');

        $this->modalHeading('Delete Server');

        $this->modalDescription('Are you sure you want to delete this server? This action cannot be undone.');

        $this->modalSubmitActionLabel('Delete');

        $this->visible(function () {
            if (!config('user-creatable-servers.can_users_delete_servers')) {
                return false;
            }

            $user = auth()->user();
            if (!$user) {
                return false;
            }

            return UserResourceLimits::where('user_id', $user->id)->exists();
        });

        $this->authorize(function (Server $record) {
            return $record->owner_id === auth()->user()->id;
        });

        $this->action(function (Server $record) {
            try {
                /** @var ServerDeletionService $service */
                $service = app(ServerDeletionService::class);

                $service->handle($record);

                Notification::make()
                    ->title('Server Deleted')
                    ->body('The server has been successfully deleted.')
                    ->success()
                    ->send();

                redirect(ListServers::getUrl());
            } catch (Exception $exception) {
                report($exception);

                Notification::make()
                    ->title('Could not delete server')
                    ->body('An error occurred while deleting the server. Please contact the panel admin.')
                    ->danger()
                    ->send();
            }
        });
    }
}