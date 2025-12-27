<?php

namespace FlexKleks\PasteFoxShare\Filament\Components\Actions;

use App\Models\Server;
use Exception;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Size;
use Illuminate\Support\Facades\Http;

class UploadLogsAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'upload_logs_pastefox';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->hidden(function () {
            /** @var Server $server */
            $server = Filament::getTenant();

            return $server->retrieveStatus()->isOffline();
        });

        $this->label(fn () => trans('pastefox-share::messages.share_logs'));

        $this->icon('tabler-share');

        $this->color('primary');

        $this->size(Size::ExtraLarge);

        $this->action(function () {
            /** @var Server $server */
            $server = Filament::getTenant();

            try {
                $logs = Http::daemon($server->node)
                    ->get("/api/servers/{$server->uuid}/logs", [
                        'size' => 5000,
                    ])
                    ->throw()
                    ->json('data');

                $logs = is_array($logs) ? implode(PHP_EOL, $logs) : $logs;

                $apiKey = config('pastefox-share.api_key');

                if (empty($apiKey)) {
                    Notification::make()
                        ->title(trans('pastefox-share::messages.api_key_missing'))
                        ->danger()
                        ->send();

                    return;
                }

                $response = Http::withHeaders([
                    'X-API-Key' => $apiKey,
                    'Content-Type' => 'application/json',
                ])
                    ->timeout(30)
                    ->connectTimeout(5)
                    ->throw()
                    ->post('https://pastefox.com/api/pastes', [
                        'content' => $logs,
                        'title' => 'Console Logs: '.$server->name.' - '.now()->format('Y-m-d H:i:s'),
                        'language' => 'log',
                        'visibility' => config('pastefox-share.visibility', 'PUBLIC'),
                    ])
                    ->json();

                if ($response['success']) {
                    $url = 'https://pastefox.com/'.$response['data']['slug'];

                    Notification::make()
                        ->title(trans('pastefox-share::messages.uploaded'))
                        ->body($url)
                        ->persistent()
                        ->success()
                        ->send();
                } else {
                    Notification::make()
                        ->title(trans('pastefox-share::messages.upload_failed'))
                        ->body($response['error'] ?? 'Unknown error')
                        ->danger()
                        ->send();
                }
            } catch (Exception $exception) {
                report($exception);

                Notification::make()
                    ->title(trans('pastefox-share::messages.upload_failed'))
                    ->body($exception->getMessage())
                    ->danger()
                    ->send();
            }
        });
    }
}
