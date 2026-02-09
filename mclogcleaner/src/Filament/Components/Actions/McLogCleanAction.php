<?php

namespace JuggleGaming\McLogCleaner\Filament\Components\Actions;

use App\Models\Server;
use Exception;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Size;
use Illuminate\Support\Facades\Http;
use JuggleGaming\McLogCleaner\Enums\CheckEgg;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Carbon\Carbon;

class McLogCleanAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'clean_logs';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->hidden(function () {
            $server = Filament::getTenant();
            return !CheckEgg::serverSupportsLogCleaner($server);
        });

        $this->label('Delete logs');
        $this->icon('tabler-trash');
        $this->color('danger');
        $this->size(Size::ExtraLarge);

        $this->requiresConfirmation()
            ->modalHeading('Delete logs')
            ->modalDescription('Choose which logs should be deleted.')
            ->modalSubmitActionLabel('Delete logs')
            ->form([
                Select::make('mode')
                    ->label('Delete logs')
                    ->options([
                        7        => 'Older than 7 days',
                        30       => 'Older than 30 days',
                        -1       => 'Delete all logs',
                        'custom' => 'Custom (days)',
                    ])
                    ->default(7)
                    ->required()
                    ->reactive(),

                TextInput::make('custom_days')
                    ->label('Delete logs older than (days)')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(365)
                    ->placeholder('e.g. 14')
                    ->required(fn ($get) => $get('mode') === 'custom')
                    ->visible(fn ($get) => $get('mode') === 'custom'),
            ]);

        $this->action(function (array $data) {
            $server = Filament::getTenant();

            $mode = $data['mode'];
            if ($mode !== 'custom') {
                $mode = (int) $mode;
            }

            if ($mode === 'custom') {
                $days = max(1, (int) $data['custom_days']);
            } elseif ($mode === -1) {
                $days = 0;
            } else {
                $days = $mode;
            }

            try {
                $files = Http::daemon($server->node)
                    ->get("/api/servers/{$server->uuid}/files/list-directory", [
                        'directory' => 'logs',
                    ])
                    ->throw()
                    ->json();

                if (!is_array($files)) {
                    throw new Exception('Invalid log directory response.');
                }

                $threshold = now()->subDays($days)->startOfDay();

                $logsToDelete = collect($files)
                    ->filter(fn ($file) => str_ends_with($file['name'], '.log.gz'))
                    ->filter(function ($file) use ($days, $threshold) {
                        if ($days === 0) {
                            return true;
                        }

                        $logDate = $this->extractLogDate($file['name']);

                        if (!$logDate) {
                            return false;
                        }

                        return $logDate->lessThan($threshold);
                    })
                    ->pluck('name')
                    ->map(fn ($name) => 'logs/' . $name)
                    ->values()
                    ->all();

                if (empty($logsToDelete)) {
                    Notification::make()
                        ->title('McLogCleaner')
                        ->body('No logs matching your selection were found.')
                        ->success()
                        ->send();
                    return;
                }

                Http::daemon($server->node)
                    ->post("/api/servers/{$server->uuid}/files/delete", [
                        'root'  => '/',
                        'files' => $logsToDelete,
                    ])
                    ->throw();

                Notification::make()
                    ->title('Logfolder cleaned')
                    ->body(count($logsToDelete) . ' files were deleted.')
                    ->success()
                    ->send();

            } catch (\Throwable $e) {
                report($e);

                Notification::make()
                    ->title('Cleanup failed.')
                    ->body($e->getMessage())
                    ->danger()
                    ->send();
            }
        });
    }

    private function extractLogDate(string $filename): ?Carbon
    {
        if (preg_match('/(\d{4}-\d{2}-\d{2})/', $filename, $matches)) {
            return Carbon::createFromFormat('Y-m-d', $matches[1])->startOfDay();
        }
        return null;
    }
}
