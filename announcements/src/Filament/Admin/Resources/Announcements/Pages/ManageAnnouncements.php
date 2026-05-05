<?php

namespace Boy132\Announcements\Filament\Admin\Resources\Announcements\Pages;

use Boy132\Announcements\Filament\Admin\Resources\Announcements\AnnouncementResource;
use Filament\Resources\Pages\ManageRecords;

class ManageAnnouncements extends ManageRecords
{
    protected static string $resource = AnnouncementResource::class;
}
