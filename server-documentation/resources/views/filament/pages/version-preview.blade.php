<div class="space-y-4">
    <div class="grid grid-cols-2 gap-4 text-sm border-b border-gray-200 dark:border-gray-700 pb-4">
        <div>
            <span class="font-medium text-gray-500 dark:text-gray-400">Title:</span>
            <span class="ml-2">{{ $version->title }}</span>
        </div>
        <div>
            <span class="font-medium text-gray-500 dark:text-gray-400">Version:</span>
            <span class="ml-2">v{{ $version->version_number }}</span>
        </div>
        <div>
            <span class="font-medium text-gray-500 dark:text-gray-400">Edited By:</span>
            <span class="ml-2">{{ $version->editor?->username ?? 'Unknown' }}</span>
        </div>
        <div>
            <span class="font-medium text-gray-500 dark:text-gray-400">Date:</span>
            <span class="ml-2">{{ $version->created_at->format('M j, Y g:i A') }}</span>
        </div>
        @if($version->change_summary)
            <div class="col-span-2">
                <span class="font-medium text-gray-500 dark:text-gray-400">Change Summary:</span>
                <span class="ml-2">{{ $version->change_summary }}</span>
            </div>
        @endif
    </div>

    <div class="prose prose-sm dark:prose-invert max-w-none">
        {!! str($version->content)->sanitizeHtml() !!}
    </div>
</div>
