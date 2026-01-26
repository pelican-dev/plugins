<?php

namespace Boy132\Subdomains\Filament\Admin\Resources\CloudflareDomains\Pages;

use Boy132\Subdomains\Filament\Admin\Resources\CloudflareDomains\CloudflareDomainResource;
use Boy132\Subdomains\Models\CloudflareDomain;
use Exception;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;

class ManageCloudflareDomains extends ManageRecords
{
    protected static string $resource = CloudflareDomainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->createAnother(false)
                ->hidden(fn () => is_null(config('subdomains.token')))
                ->using(function (array $data) {
                    try {
                        return CloudflareDomain::create($data);
                    } catch (Exception $exception) {
                        Notification::make()
                            ->title(trans('subdomains::strings.notifications.not_synced'))
                            ->body($exception->getMessage())
                            ->warning()
                            ->persistent()
                            ->send();
                    }
                }),
        ];
    }
}
