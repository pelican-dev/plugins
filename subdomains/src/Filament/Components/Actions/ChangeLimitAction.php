<?php

namespace Boy132\Subdomains\Filament\Components\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use App\Models\Server;

class ChangeLimitAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'change_limit';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(fn () => trans('subdomains::strings.subdomain_change_limit'));

        $this->schema(function (?Server $server) {
            return [
                TextInput::make('limit')
                    ->label(fn () => trans('subdomains::strings.subdomain_limit'))
                    ->numeric()
                    ->required()
                    ->default(fn () => $server?->subdomain_limit ?? 0)
                    ->minValue(0),
            ];
        });

        $this->action(function (Server $server, array $data) {
            $old = $server->subdomain_limit ?? 0;
            $new = (int) ($data['limit'] ?? 0);

            $server->update(['subdomain_limit' => $new]);

            Notification::make()
                ->title(trans('subdomains::strings.subdomain_limit_changed'))
                ->body($old . ' -> ' . $new)
                ->success()
                ->send();
        });
    }
}
