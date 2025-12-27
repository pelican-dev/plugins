<?php

namespace FlexKleks\ServerFolders\Filament\App\Resources\ServerFolders\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use FlexKleks\ServerFolders\Filament\App\Resources\ServerFolders\ServerFolderResource;

class ManageServerFolders extends ManageRecords
{
    protected static string $resource = ServerFolderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['user_id'] = auth()->id();

                    return $data;
                }),
        ];
    }
}
