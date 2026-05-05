<?php

namespace Boy132\UserCreatableServers\Filament\Components\Actions;

use Boy132\UserCreatableServers\Filament\App\Pages\CreateServerPage;
use Boy132\UserCreatableServers\Models\UserResourceLimits;
use Filament\Actions\Action;

class CreateServerAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'create_server';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(fn () => trans('user-creatable-servers::strings.create_server'));

        $this->visible(fn () => UserResourceLimits::where('user_id', auth()->user()->id)->exists());

        $this->disabled(function () {
            /** @var ?UserResourceLimits $userResourceLimits */
            $userResourceLimits = UserResourceLimits::where('user_id', auth()->user()->id)->first();

            if (!$userResourceLimits) {
                return true;
            }

            return !$userResourceLimits->canCreateServer(1, 1, 1);
        });

        $this->url(fn () => CreateServerPage::getUrl());
    }
}
