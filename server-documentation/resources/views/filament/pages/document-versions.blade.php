<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">
                {{ trans('server-documentation::strings.versions.current_document') }}
            </x-slot>

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="font-medium text-gray-500 dark:text-gray-400">{{ trans('server-documentation::strings.document.title') }}:</span>
                    <span class="ml-2">{{ $this->record->title }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-500 dark:text-gray-400">{{ trans('server-documentation::strings.versions.current_version') }}:</span>
                    <span class="ml-2">v{{ $this->record->getCurrentVersionNumber() }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-500 dark:text-gray-400">{{ trans('server-documentation::strings.versions.last_updated') }}:</span>
                    <span class="ml-2">{{ $this->record->updated_at?->diffForHumans() ?? 'Never' }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-500 dark:text-gray-400">{{ trans('server-documentation::strings.versions.last_edited_by') }}:</span>
                    <span class="ml-2">{{ $this->record->lastEditor?->username ?? 'Unknown' }}</span>
                </div>
            </div>
        </x-filament::section>

        {{ $this->table }}
    </div>
</x-filament-panels::page>
