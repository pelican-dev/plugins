<?php

declare(strict_types=1);

namespace Starter\ServerDocumentation\Filament\Admin\Resources;

use App\Models\Egg;
use App\Models\Server;
use App\Traits\Filament\CanCustomizePages;
use App\Traits\Filament\CanCustomizeRelations;
use App\Traits\Filament\CanModifyForm;
use App\Traits\Filament\CanModifyTable;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Starter\ServerDocumentation\Enums\DocumentType;
use Starter\ServerDocumentation\Filament\Admin\Resources\DocumentResource\Pages;
use Starter\ServerDocumentation\Filament\Admin\Resources\DocumentResource\RelationManagers;
use Starter\ServerDocumentation\Filament\Concerns\HasDocumentTableColumns;
use Starter\ServerDocumentation\Models\Document;
use Starter\ServerDocumentation\Services\DocumentService;

class DocumentResource extends Resource
{
    use CanCustomizePages;
    use CanCustomizeRelations;
    use CanModifyForm;
    use CanModifyTable;
    use HasDocumentTableColumns;

    protected static ?string $model = Document::class;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-file-text';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'title';

    /**
     * Check if the user can access the document resource.
     */
    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->can('viewList document');
    }

    public static function getNavigationLabel(): string
    {
        return trans('server-documentation::strings.navigation.documents');
    }

    public static function getModelLabel(): string
    {
        return trans('server-documentation::strings.document.singular', [], 'en') !== 'server-documentation::strings.document.singular'
            ? trans('server-documentation::strings.document.singular')
            : 'Document';
    }

    public static function getPluralModelLabel(): string
    {
        return trans('server-documentation::strings.document.plural', [], 'en') !== 'server-documentation::strings.document.plural'
            ? trans('server-documentation::strings.document.plural')
            : 'Documents';
    }

    public static function getNavigationGroup(): ?string
    {
        return trans('server-documentation::strings.navigation.group', [], 'en') !== 'server-documentation::strings.navigation.group'
            ? trans('server-documentation::strings.navigation.group')
            : 'Content';
    }

    public static function getNavigationBadge(): ?string
    {
        $count = app(DocumentService::class)->getDocumentCount();

        return $count > 0 ? (string) $count : null;
    }

    public static function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(trans('server-documentation::strings.form.details_section'))->schema([
                    TextInput::make('title')
                        ->label(trans('server-documentation::strings.document.title'))
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, $set, ?Document $record) => $record === null ? $set('slug', Str::slug($state)) : null
                        ),

                    TextInput::make('slug')
                        ->label(trans('server-documentation::strings.document.slug'))
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->rules(['alpha_dash']),

                    Select::make('type')
                        ->label(trans('server-documentation::strings.document.type'))
                        ->options(DocumentType::options())
                        ->default(DocumentType::Player->value)
                        ->required()
                        ->native(false),

                    Toggle::make('is_global')
                        ->label(trans('server-documentation::strings.labels.all_servers'))
                        ->helperText(trans('server-documentation::strings.labels.all_servers_helper')),

                    Toggle::make('is_published')
                        ->label(trans('server-documentation::strings.document.is_published'))
                        ->default(true)
                        ->helperText(trans('server-documentation::strings.labels.published_helper')),

                    TextInput::make('sort_order')
                        ->label(trans('server-documentation::strings.document.sort_order'))
                        ->numeric()
                        ->default(0)
                        ->helperText(trans('server-documentation::strings.labels.sort_order_helper')),
                ])->columns(3)->columnSpanFull(),

                Section::make(trans('server-documentation::strings.document.content'))->schema([
                    RichEditor::make('content')
                        ->label('')
                        ->required()
                        ->extraAttributes(['style' => 'min-height: 400px;'])
                        ->columnSpanFull(),
                ])->columnSpanFull(),

                Section::make(trans('server-documentation::strings.form.server_assignment'))
                    ->description(trans('server-documentation::strings.form.server_assignment_description'))
                    ->collapsed(fn (?Document $record) => $record !== null)
                    ->visible(fn (?Document $record, string $operation) => $operation === 'create' || ($record && !$record->is_global))
                    ->schema([
                        Select::make('filter_egg')
                            ->label(trans('server-documentation::strings.form.filter_by_egg'))
                            ->options(fn () => Egg::pluck('name', 'id'))
                            ->placeholder(trans('server-documentation::strings.form.all_eggs'))
                            ->live()
                            ->afterStateUpdated(fn ($set) => $set('servers', [])),

                        CheckboxList::make('servers')
                            ->label(trans('server-documentation::strings.form.assign_to_servers'))
                            ->relationship('servers', 'name')
                            ->options(function ($get) {
                                $query = Server::query()->orderBy('name');

                                if ($eggId = $get('filter_egg')) {
                                    $query->where('egg_id', $eggId);
                                }

                                return $query->pluck('name', 'id');
                            })
                            ->searchable()
                            ->bulkToggleable()
                            ->columns(2)
                            ->helperText(trans('server-documentation::strings.form.assign_servers_helper'))
                            ->visible(fn ($get) => !$get('is_global')),
                    ])->columnSpanFull(),

                Section::make(trans('server-documentation::strings.permission_guide.title'))
                    ->description(trans('server-documentation::strings.permission_guide.description'))
                    ->collapsed()
                    ->schema([
                        Placeholder::make('help')
                            ->label('')
                            ->content(new HtmlString(
                                view('server-documentation::filament.partials.permission-guide', ['showExamples' => false])->render()
                            )),
                    ])->columnSpanFull(),
            ]);
    }

    public static function defaultTable(Table $table): Table
    {
        return $table
            ->columns([
                static::getDocumentTitleColumn(),
                static::getDocumentTypeColumn(),
                static::getDocumentGlobalColumn(),
                static::getDocumentPublishedColumn(),

                TextColumn::make('servers_count')
                    ->counts('servers')
                    ->label(trans('server-documentation::strings.table.servers'))
                    ->badge(),

                TextColumn::make('author.username')
                    ->label(trans('server-documentation::strings.document.author'))
                    ->toggleable(isToggledHiddenByDefault: true),

                static::getDocumentUpdatedAtColumn(),
            ])
            ->filters([
                static::getDocumentTypeFilter(),
                static::getDocumentGlobalFilter(),
                static::getDocumentPublishedFilter(),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->groupedBulkActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('sort_order')
            ->emptyStateIcon('tabler-file-off')
            ->emptyStateHeading(trans('server-documentation::strings.table.empty_heading'))
            ->emptyStateDescription(trans('server-documentation::strings.table.empty_description'));
    }

    /** @return class-string[] */
    public static function getRelations(): array
    {
        return [
            RelationManagers\ServersRelationManager::class,
        ];
    }

    /** @return array<string, PageRegistration> */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
            'versions' => Pages\ViewDocumentVersions::route('/{record}/versions'),
        ];
    }
}
