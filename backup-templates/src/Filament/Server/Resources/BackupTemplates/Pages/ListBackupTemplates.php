<?php

namespace Ebnater\BackupTemplates\Filament\Server\Resources\BackupTemplates\Pages;

use App\Models\Server;
use Ebnater\BackupTemplates\Filament\Server\Resources\BackupTemplates\BackupTemplateResource;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;

class ListBackupTemplates extends ListRecords
{
    protected static string $resource = BackupTemplateResource::class;

    public function getBreadcrumbs(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->authorize(function (): bool {
                    /** @var Server|null $server */
                    $server = Filament::getTenant();

                    return $server !== null && user()?->can('backupTemplates.create', $server);
                })
                ->createAnother(false)
                ->mutateDataUsing(function (array $data): array {
                    /** @var Server|null $server */
                    $server = Filament::getTenant();

                    if ($server) {
                        $data['server_id'] = $server->id;
                    }

                    return $data;
                }),
        ];
    }
}
