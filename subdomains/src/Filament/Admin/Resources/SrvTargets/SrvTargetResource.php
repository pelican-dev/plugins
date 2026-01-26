<?php

namespace Boy132\Subdomains\Filament\Admin\Resources\SrvTargets;

use App\Filament\Admin\Resources\Nodes\Pages\EditNode;
use App\Models\Node;
use Boy132\Subdomains\Filament\Admin\Resources\SrvTargets\Pages\ManageSrvTargets;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;

class SrvTargetResource extends Resource
{
    protected static ?string $model = Node::class;

    protected static ?string $slug = 'srv-targets';

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-world-www';

    public static function getModelLabel(): string
    {
        return trans('subdomains::strings.srv_target');
    }

    public static function getNavigationGroup(): ?string
    {
        return trans_choice('subdomains::strings.subdomain', 2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(trans('admin/node.table.name'))
                    ->url(fn (Node $node) => user()?->can('update', $node) ? EditNode::getUrl(['record' => $node]) : null),
                TextColumn::make('fqdn')
                    ->label(trans('admin/node.table.address')),
                TextInputColumn::make('srv_target')
                    ->label(trans('subdomains::strings.srv_target'))
                    ->placeholder(trans('subdomains::strings.no_srv_target'))
                    ->updateStateUsing(function (Node $node, $state) {
                        $node->forceFill([
                            'srv_target' => $state,
                        ])->save();
                    }),
            ])
            ->emptyStateIcon('tabler-world-www')
            ->emptyStateDescription('')
            ->emptyStateHeading(trans('admin/node.no_nodes'));
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSrvTargets::route('/'),
        ];
    }
}
