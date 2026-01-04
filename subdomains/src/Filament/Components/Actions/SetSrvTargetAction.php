<?php

namespace Boy132\Subdomains\Filament\Components\Actions;

use App\Models\Node;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class SetSrvTargetAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'set_srv_target';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(fn () => trans('subdomains::strings.set_srv_target'));

        $this->icon('tabler-world-www');

        $this->schema(function (Node $node) {
            return [
                TextInput::make('srv_target')
                    ->label(fn () => trans('subdomains::strings.srv_target'))
                    ->default(fn () => $node->srv_target) // @phpstan-ignore property.undefined
                    ->placeholder('play.example.com OR IPv4/IPv6 address')
                    ->helperText(trans('subdomains::strings.srv_target_confirmation'))
                    ->rules(['nullable', 'string', 'regex:/^(?=.{1,253}$)(?!-)[A-Za-z0-9-]{1,63}(?<!-)(?:\.(?!-)[A-Za-z0-9-]{1,63}(?<!-))*$/']),
            ];
        });

        $this->action(function (Node $node, array $data) {
            // ForceFill so we don't need to overwrite on Node::$fillable
            $node->forceFill(['srv_target' => $data['srv_target']])->save();

            Notification::make()
                ->title(trans('subdomains::strings.notifications.srv_target_updated_title'))
                ->body(trans('subdomains::strings.notifications.srv_target_updated'))
                ->warning()
                ->send();
        })->requiresConfirmation()->modalIconColor('danger');
    }
}
