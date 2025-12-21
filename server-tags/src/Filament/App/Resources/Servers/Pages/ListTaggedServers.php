<?php

namespace Boy132\ServerTags\Filament\App\Resources\Servers\Pages;

use App\Filament\App\Resources\Servers\ServerResource;
use Boy132\ServerTags\Models\ServerTag;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Colors\Color;
use Illuminate\Database\Eloquent\Builder;

class ListTaggedServers extends ListServer
{
    protected static string $resource = ServerResource::class;

    protected static string $view = 'filament.resources.pages.list-server';

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make(trans('server-tags::strings.all_servers'))
                ->badge(fn () => ServerResource::getEloquentQuery()->count())
                ->icon('tabler-server'),
        ];

        $tags = ServerTag::withCount('servers')
            ->orderBy('name')
            ->get();

        foreach ($tags as $tag) {
            $tabs[$tag->slug] = Tab::make($tag->name)
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('serverTags', fn ($q) => $q->where('server_tags.id', $tag->id)))
                ->badge(fn () => $tag->servers_count)
                ->badgeColor(Color::hex($tag->color))
                ->icon('tabler-tag');
        }

        $tabs['untagged'] = Tab::make(trans('server-tags::strings.untagged'))
            ->modifyQueryUsing(fn (Builder $query) => $query->doesntHave('serverTags'))
            ->badge(fn () => ServerResource::getEloquentQuery()->doesntHave('serverTags')->count())
            ->badgeColor('gray')
            ->icon('tabler-tag-off');

        return $tabs;
    }
}