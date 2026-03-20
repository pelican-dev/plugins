<?php

namespace Notjami\Webhooks\Filament\Server\Resources\Webhooks\Pages;

use Filament\Resources\Pages\ManageRecords;
use Notjami\Webhooks\Filament\Server\Resources\Webhooks\WebhookResource;

class ManageWebhooks extends ManageRecords
{
    protected static string $resource = WebhookResource::class;
}
