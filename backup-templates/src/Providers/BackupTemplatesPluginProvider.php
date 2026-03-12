<?php

namespace Ebnater\BackupTemplates\Providers;

use App\Filament\Server\Resources\Backups\BackupResource;
use App\Models\Server;
use Ebnater\BackupTemplates\Models\BackupTemplate;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\ServiceProvider;

class BackupTemplatesPluginProvider extends ServiceProvider
{
    public function register(): void
    {
        BackupResource::modifyForm(function (Schema $schema): Schema {
            $components = $schema->getComponents();

            $components[] = Select::make('backup_template_id')
                ->label(trans('backup-templates::strings.backup_form.template'))
                ->options(function (): array {
                    /** @var Server|null $server */
                    $server = Filament::getTenant();

                    if (!$server) {
                        return [];
                    }

                    return BackupTemplate::query()
                        ->where('server_id', $server->id)
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray();
                })
                ->searchable()
                ->preload()
                ->live()
                ->dehydrated(false)
                ->placeholder(trans('backup-templates::strings.backup_form.template_placeholder'))
                ->helperText(trans('backup-templates::strings.backup_form.template_help'))
                ->afterStateUpdated(function ($state, Set $set): void {
                    if (blank($state)) {
                        return;
                    }

                    /** @var Server|null $server */
                    $server = Filament::getTenant();

                    if (!$server) {
                        return;
                    }

                    $template = BackupTemplate::query()
                        ->where('server_id', $server->id)
                        ->find($state);

                    if ($template) {
                        $set('ignored', $template->ignored ?? '');
                    }
                });

            return $schema->components($components);
        });
    }

    public function boot(): void
    {
        Server::resolveRelationUsing('backupTemplates', fn (Server $server) => $server->hasMany(BackupTemplate::class, 'server_id', 'id'));
    }
}
