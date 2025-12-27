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
                $hasApiKey = !empty($apiKey);

                // Build request payload
                $payload = [
                    'content' => $logs,
                    'title' => 'Console Logs: '.$server->name.' - '.now()->format('Y-m-d H:i:s'),
                    'language' => 'log',
                    'effect' => config('pastefox-share.effect', 'NONE'),
                    'theme' => config('pastefox-share.theme', 'dark'),
                ];

                // Only add these options if API key is present
                if ($hasApiKey) {
                    $payload['visibility'] = config('pastefox-share.visibility', 'PUBLIC');

                    $password = config('pastefox-share.password');
                    if (!empty($password)) {
                        $payload['password'] = $password;
                    }
                }

                // Build HTTP request
                $request = Http::withHeaders(['Content-Type' => 'application/json'])
                    ->timeout(30)
                    ->connectTimeout(5);

                // Add API key header if available
                if ($hasApiKey) {
                    $request = $request->withHeaders(['X-API-Key' => $apiKey]);
                }

                $response = $request
                    ->throw()
                    ->post('https://pastefox.com/api/pastes', $payload)
                    ->json();

                if ($response['success']) {
                    $url = 'https://pastefox.com/'.$response['data']['slug'];

                    $body = $url;
                    if (!$hasApiKey) {
                        $body .= "\n".trans('pastefox-share::messages.expires_7_days');
                    }

                    Notification::make()
                        ->title(trans('pastefox-share::messages.uploaded'))
                        ->body($body)
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
