<?php

namespace Boy132\UserCreatableServers\Filament\Components\Actions;

use App\Exceptions\Service\Deployment\NoViableAllocationException;
use App\Exceptions\Service\Deployment\NoViableNodeException;
use App\Filament\Server\Pages\Console;
use App\Models\Egg;
use App\Services\Servers\RandomWordService;
use Boy132\UserCreatableServers\Models\UserResourceLimits;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class CreateServerAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'create_server';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->visible(fn () => UserResourceLimits::where('user_id', auth()->user()->id)->exists());

        $this->disabled(function () {
            /** @var ?UserResourceLimits $userResourceLimits */
            $userResourceLimits = UserResourceLimits::where('user_id', auth()->user()->id)->first();

            if (!$userResourceLimits) {
                return true;
            }

            return !$userResourceLimits->canCreateServer(1, 1, 1);
        });

        $this->form(function () {
            /** @var UserResourceLimits $userResourceLimits */
            $userResourceLimits = UserResourceLimits::where('user_id', auth()->user()->id)->firstOrFail();

            return [
                TextInput::make('name')
                    ->label(trans('usercreatableservers::strings.name'))
                    ->required()
                    ->default(fn () => (new RandomWordService())->word())
                    ->columnSpanFull(),
                Select::make('egg_id')
                    ->label(trans('usercreatableservers::strings.egg'))
                    ->prefixIcon('tabler-egg')
                    ->options(fn () => Egg::all()->mapWithKeys(fn (Egg $egg) => [$egg->id => $egg->name]))
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('memory')
                    ->label(trans('usercreatableservers::strings.memory'))
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue($userResourceLimits->getMemoryLeft())
                    ->suffix(config('panel.use_binary_prefix') ? 'MiB' : 'MB'),
                TextInput::make('disk')
                    ->label(trans('usercreatableservers::strings.disk'))
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue($userResourceLimits->getDiskLeft())
                    ->suffix(config('panel.use_binary_prefix') ? 'MiB' : 'MB'),
                TextInput::make('cpu')
                    ->label(trans('usercreatableservers::strings.cpu'))
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue($userResourceLimits->getCpuLeft())
                    ->suffix('%'),
            ];
        });

        $this->action(function (array $data) {
            try {
                /** @var UserResourceLimits $userResourceLimits */
                $userResourceLimits = UserResourceLimits::where('user_id', auth()->user()->id)->firstOrFail();

                if ($server = $userResourceLimits->createServer($data['name'], $data['egg_id'], $data['memory'], $data['disk'], $data['cpu'])) {
                    redirect(Console::getUrl(panel: 'server', tenant: $server));
                }
            } catch (Exception $exception) {
                report($exception);

                if ($exception instanceof NoViableNodeException) {
                    Notification::make()
                        ->title('Could not create server')
                        ->body('No viable node was found. Please contact the panel admin.')
                        ->danger()
                        ->send();
                } elseif ($exception instanceof NoViableAllocationException) {
                    Notification::make()
                        ->title('Could not create server')
                        ->body('No viable allocation was found. Please contact the panel admin.')
                        ->danger()
                        ->send();
                } else {
                    Notification::make()
                        ->title('Could not create server')
                        ->body('Unknown error. Please contact the panel admin.')
                        ->danger()
                        ->send();
                }
            }
        });
    }
}
