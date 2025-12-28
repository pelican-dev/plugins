<?php

namespace Boy132\MclogsUploader\Filament\Components\Actions;

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
        return 'upload_logs';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->hidden(function () {
            $mc_tag = "mclogs-updater";

            /** @var Server $server */
            $server = Filament::getTenant();

            if ($server->retrieveStatus()->isOffline()) {
                return true;
            }
            $egg = $server->egg;

            // check if egg has tag 'mclogs-updater'
            if (method_exists($egg, $mc_tag)) {
                try {
                    $hasTag = $egg->tags()->where('name', $mc_tag)->exists();
                    if ($hasTag) {
                        return false;
                    }
                } catch (Exception $e) {
                }
            }
            if (isset($egg->tags)) {
                $tags = is_string($egg->tags) ? json_decode($egg->tags, true) : $egg->tags;
                if (is_array($tags) && in_array($mc_tag, $tags)) {
                    return false;
                }
            }
            if (isset($egg->meta) && is_array($egg->meta) && isset($egg->meta['tags'])) {
                if (in_array($mc_tag, $egg->meta['tags'])) {
                    return false;
                }
            }

            try {
                $eggTags = \DB::table('egg_tag')
                    ->join('tags', 'egg_tag.tag_id', '=', 'tags.id')
                    ->where('egg_tag.egg_id', $egg->id)
                    ->where('tags.name', $mc_tag)
                    ->exists();

                if ($eggTags) {
                    return false;
                }
            } catch (Exception $e) {

            }

            return true;
        });

        $this->label(fn () => trans('mclogs-uploader::upload.upload_logs'));

        $this->icon('tabler-upload');

        $this->color('primary');

        $this->size(Size::ExtraLarge);

        $this->action(function () {
            /** @var Server $server */
            $server = Filament::getTenant();

            try {
                $logs = Http::daemon($server->node)
                    ->get("/api/servers/{$server->uuid}/logs")
                    ->throw()
                    ->json('data');

                $logs = is_array($logs) ? implode(PHP_EOL, $logs) : $logs;

                $response = Http::asForm()
                    ->timeout(15)
                    ->connectTimeout(5)
                    ->throw()
                    ->post('https://api.mclo.gs/1/log', [
                        'content' => $logs,
                    ])
                    ->json();

                if ($response['success']) {
                    Notification::make()
                        ->title(trans('mclogs-uploader::upload.uploaded'))
                        ->body($response['url'])
                        ->persistent()
                        ->success()
                        ->send();
                } else {
                    Notification::make()
                        ->title(trans('mclogs-uploader::upload.upload_failed'))
                        ->body($response['error'])
                        ->danger()
                        ->send();
                }
            } catch (Exception $exception) {
                report($exception);

                Notification::make()
                    ->title(trans('mclogs-uploader::upload.upload_failed'))
                    ->body($exception->getMessage())
                    ->danger()
                    ->send();
            }
        });
    }
}