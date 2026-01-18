<x-filament-panels::page>
    @php
        use Starter\ServerDocumentation\Enums\DocumentType;
        $documents = $this->getDocuments();
    @endphp

    @once
        @push('styles')
            <link rel="stylesheet" href="{{ asset('plugins/server-documentation/css/document-content.css') }}">
        @endpush
    @endonce

    @if($documents->isEmpty())
        <div class="flex flex-col items-center justify-center p-8 text-center">
            <x-filament::icon
                icon="tabler-file-off"
                class="h-12 w-12 text-gray-400 dark:text-gray-500 mb-4"
            />
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                {{ trans('server-documentation::strings.server_panel.no_documents') }}
            </h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ trans('server-documentation::strings.server_panel.no_documents_description') }}
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            {{-- Document list sidebar --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ trans('server-documentation::strings.navigation.documents') }}</h3>
                    </div>
                    <nav class="p-2 space-y-1">
                        @foreach($documents as $document)
                            @php
                                $docType = DocumentType::tryFromLegacy($document->type);
                            @endphp
                            <button
                                wire:click="selectDocument({{ $document->id }})"
                                @class([
                                    'w-full text-left px-3 py-2 rounded-lg text-sm transition-colors',
                                    'bg-primary-50 text-primary-700 dark:bg-primary-900/50 dark:text-primary-400' => $selectedDocument?->id === $document->id,
                                    'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800' => $selectedDocument?->id !== $document->id,
                                ])
                            >
                                <div class="flex items-center gap-2">
                                    <x-filament::icon
                                        :icon="$docType?->icon() ?? 'tabler-file-text'"
                                        @class([
                                            'h-4 w-4',
                                            'text-danger-500' => $docType === DocumentType::HostAdmin,
                                            'text-warning-500' => $docType === DocumentType::ServerAdmin,
                                            'text-info-500' => $docType === DocumentType::ServerMod,
                                        ])
                                    />
                                    <span class="truncate">{{ $document->title }}</span>
                                </div>
                                @if($document->is_global)
                                    <span class="text-xs text-gray-500 dark:text-gray-400 ml-6">{{ trans('server-documentation::strings.server_panel.global') }}</span>
                                @endif
                            </button>
                        @endforeach
                    </nav>
                </div>
            </div>

            {{-- Document content --}}
            <div class="lg:col-span-3">
                @if($selectedDocument)
                    @php
                        $selectedDocType = DocumentType::tryFromLegacy($selectedDocument->type);
                    @endphp
                    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    {{ $selectedDocument->title }}
                                </h2>
                                <div class="flex items-center gap-2">
                                    @if($selectedDocType && $selectedDocType !== DocumentType::Player)
                                        <span @class([
                                            'inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full',
                                            'bg-danger-50 text-danger-700 dark:bg-danger-900/50 dark:text-danger-400' => $selectedDocType === DocumentType::HostAdmin,
                                            'bg-warning-50 text-warning-700 dark:bg-warning-900/50 dark:text-warning-400' => $selectedDocType === DocumentType::ServerAdmin,
                                            'bg-info-50 text-info-700 dark:bg-info-900/50 dark:text-info-400' => $selectedDocType === DocumentType::ServerMod,
                                        ])>
                                            <x-filament::icon :icon="$selectedDocType->icon()" class="h-3 w-3" />
                                            {{ $selectedDocType->label() }}
                                        </span>
                                    @endif
                                    @if($selectedDocument->is_global)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                                            <x-filament::icon icon="tabler-world" class="h-3 w-3" />
                                            {{ trans('server-documentation::strings.server_panel.global') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            @if($selectedDocument->updated_at)
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ trans('server-documentation::strings.server_panel.last_updated', ['time' => $selectedDocument->updated_at->diffForHumans()]) }}
                                </p>
                            @endif
                        </div>
                        <div class="p-6 document-content prose prose-sm dark:prose-invert max-w-none">
                            {!! str($selectedDocument->content)->sanitizeHtml() !!}
                        </div>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center p-8 text-center bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10">
                        <x-filament::icon
                            icon="tabler-file-text"
                            class="h-12 w-12 text-gray-400 dark:text-gray-500 mb-4"
                        />
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ trans('server-documentation::strings.server_panel.select_document') }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ trans('server-documentation::strings.server_panel.select_document_description') }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    @endif
</x-filament-panels::page>
