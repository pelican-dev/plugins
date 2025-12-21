<?php

namespace Boy132\ServerTags\Filament\Admin\Resources\ServerTags\Pages;

use Boy132\ServerTags\Filament\Admin\Resources\ServerTags\ServerTagResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageServerTags extends ManageRecords
{
    protected static string $resource = ServerTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->createAnother(false),
        ];
    }
}