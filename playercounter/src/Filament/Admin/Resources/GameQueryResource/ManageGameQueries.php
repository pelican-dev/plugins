<?php

namespace Boy132\PlayerCounter\Filament\Admin\Resources\GameQueryResource\Pages;

use Boy132\PlayerCounter\Filament\Admin\Resources\GameQueryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageGameQueries extends ManageRecords
{
    protected static string $resource = GameQueryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->createAnother(false),
        ];
    }
}
