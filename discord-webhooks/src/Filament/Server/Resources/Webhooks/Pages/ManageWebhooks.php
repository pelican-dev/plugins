<?php

namespace Notjami\Webhooks\Filament\Server\Resources\Webhooks\Pages;

use Notjami\Webhooks\Filament\Server\Resources\Webhooks\WebhookResource;
use Filament\Resources\Pages\ManageRecords;

class ManageWebhooks extends ManageRecords
{
    protected static string $resource = WebhookResource::class;
}
