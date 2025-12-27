@php
    $servers = $this->getServers();
@endphp

<x-filament-panels::page>
    @if($servers->count() > 0)
        <div class="fi-ta">
            <div class="fi-ta-content rounded-xl bg-white ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-x-auto">
                <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 dark:divide-white/5 text-start">
                    <tbody class="divide-y divide-gray-200 dark:divide-white/5" wire:poll.15s>
                        @foreach($servers as $server)
                            @php
                                $resources = $server->retrieveResources();
                                $nodeStats = $server->node->statistics();
                                $nodeInfo = $server->node->systemInformation();
                                
                                // CPU
                                $cpuCurrent = $resources['cpu_absolute'] ?? 0;
                                $cpuLimit = $server->cpu === 0 ? (($nodeInfo['cpu_count'] ?? 1) * 100) : $server->cpu;
                                $cpuPercent = $cpuLimit > 0 ? min(($cpuCurrent / $cpuLimit) * 100, 100) : 0;
                                
                                // Memory
                                $memCurrent = $resources['memory_bytes'] ?? 0;
                                $memLimit = $server->memory === 0 ? ($nodeStats['memory_total'] ?? 0) : ($server->memory * 1024 * 1024);
                                $memPercent = $memLimit > 0 ? min(($memCurrent / $memLimit) * 100, 100) : 0;
                                
                                // Disk
                                $diskCurrent = $resources['disk_bytes'] ?? 0;
                                $diskLimit = $server->disk === 0 ? ($nodeStats['disk_total'] ?? 0) : ($server->disk * 1024 * 1024);
                                $diskPercent = $diskLimit > 0 ? min(($diskCurrent / $diskLimit) * 100, 100) : 0;
                                
                                // Helper function for color
                                $getColor = function($percent) {
                                    if ($percent >= 90) return '#ef4444';
                                    if ($percent >= 70) return '#f59e0b';
                                    return '#22c55e';
                                };
                                
                                // Format bytes
                                $formatBytes = function($bytes) {
                                    if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
                                    if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
                                    if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
                                    return $bytes . ' Bytes';
                                };
                                
                                $formatLimit = function($mb) use ($formatBytes, $nodeStats) {
                                    if ($mb === 0) return '∞';
                                    return $formatBytes($mb * 1024 * 1024);
                                };
                            @endphp
                            <tr class="fi-ta-row transition hover:bg-gray-50 dark:hover:bg-white/5 cursor-pointer group"
                                onclick="window.location='{{ route('filament.server.pages.console', ['tenant' => $server]) }}'">
                                {{-- Icon --}}
                                <td class="fi-ta-cell p-3" style="width: 60px;">
                                    @if($server->icon ?? $server->egg?->image)
                                        <img src="{{ $server->icon ?? $server->egg->image }}" alt="" class="w-11 h-11 rounded object-cover">
                                    @endif
                                </td>
                                
                                {{-- Status Badge --}}
                                <td class="fi-ta-cell p-3" style="width: 100px;">
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-medium"
                                          style="background-color: color-mix(in srgb, {{ $server->condition->getColor(true) }} 15%, transparent); color: {{ $server->condition->getColor(true) }};">
                                        <x-filament::icon :icon="$server->condition->getIcon()" class="w-4 h-4" />
                                        {{ $server->condition->getLabel() }}
                                    </span>
                                </td>
                                
                                {{-- Name --}}
                                <td class="fi-ta-cell p-3">
                                    <div class="font-medium text-gray-950 dark:text-white">{{ $server->name }}</div>
                                    @if($server->description)
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($server->description, 40) }}</div>
                                    @endif
                                </td>
                                
                                {{-- Address --}}
                                <td class="fi-ta-cell p-3 hidden md:table-cell" style="width: 140px;">
                                    <span class="inline-flex items-center rounded-md bg-primary-50 dark:bg-primary-400/10 px-2 py-1 text-xs font-medium text-primary-700 dark:text-primary-400 ring-1 ring-inset ring-primary-700/10 dark:ring-primary-400/30">
                                        {{ $server->allocation?->address }}
                                    </span>
                                </td>
                                
                                {{-- CPU --}}
                                <td class="fi-ta-cell p-3 hidden lg:table-cell" style="width: 130px;">
                                    <div class="flex flex-col gap-1">
                                        <div class="relative rounded-full overflow-hidden w-full h-2" style="background-color: color-mix(in srgb, {{ $getColor($cpuPercent) }} 15%, transparent);">
                                            <div class="h-full rounded-full" style="width: {{ $cpuPercent }}%; background-color: {{ $getColor($cpuPercent) }};"></div>
                                        </div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 text-center">
                                            {{ number_format($cpuCurrent, 0) }}% / {{ $server->cpu === 0 ? '∞' : $server->cpu . '%' }}
                                        </span>
                                    </div>
                                </td>
                                
                                {{-- Memory --}}
                                <td class="fi-ta-cell p-3 hidden lg:table-cell" style="width: 150px;">
                                    <div class="flex flex-col gap-1">
                                        <div class="relative rounded-full overflow-hidden w-full h-2" style="background-color: color-mix(in srgb, {{ $getColor($memPercent) }} 15%, transparent);">
                                            <div class="h-full rounded-full" style="width: {{ $memPercent }}%; background-color: {{ $getColor($memPercent) }};"></div>
                                        </div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 text-center">
                                            {{ $formatBytes($memCurrent) }} / {{ $server->memory === 0 ? '∞' : $formatBytes($server->memory * 1024 * 1024) }}
                                        </span>
                                    </div>
                                </td>
                                
                                {{-- Disk --}}
                                <td class="fi-ta-cell p-3 hidden xl:table-cell" style="width: 150px;">
                                    <div class="flex flex-col gap-1">
                                        <div class="relative rounded-full overflow-hidden w-full h-2" style="background-color: color-mix(in srgb, {{ $getColor($diskPercent) }} 15%, transparent);">
                                            <div class="h-full rounded-full" style="width: {{ $diskPercent }}%; background-color: {{ $getColor($diskPercent) }};"></div>
                                        </div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 text-center">
                                            {{ $formatBytes($diskCurrent) }} / {{ $server->disk === 0 ? '∞' : $formatBytes($server->disk * 1024 * 1024) }}
                                        </span>
                                    </div>
                                </td>
                                
                                {{-- Remove Button --}}
                                <td class="fi-ta-cell p-3 text-end" style="width: 50px;" onclick="event.stopPropagation()">
                                    <button type="button"
                                            wire:click="removeServer({{ $server->id }})"
                                            wire:confirm="{{ trans('server-folders::messages.confirm_remove') }}"
                                            class="opacity-0 group-hover:opacity-100 inline-flex items-center justify-center rounded-lg p-2 text-gray-400 hover:text-danger-500 hover:bg-danger-50 dark:hover:bg-danger-500/10 transition">
                                        <x-filament::icon icon="tabler-folder-minus" class="w-5 h-5" />
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="flex flex-col items-center justify-center py-12 text-center">
            <div class="rounded-full bg-gray-100 dark:bg-gray-800 p-4 mb-4">
                <x-filament::icon icon="tabler-folder-open" class="w-12 h-12 text-gray-400" />
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">
                {{ trans('server-folders::messages.no_servers_in_folder') }}
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ trans('server-folders::messages.no_servers_desc') }}
            </p>
        </div>
    @endif
</x-filament-panels::page>
