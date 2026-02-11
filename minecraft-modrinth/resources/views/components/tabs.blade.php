<div class="flex justify-center">
    <x-filament::tabs class="w-auto">
        <x-filament::tabs.item 
            alpine-active="$wire.activeTab === 'all'"
            x-on:click="$wire.setActiveTab('all')"
        >
            {{ trans('minecraft-modrinth::strings.page.view_all') }}
        </x-filament::tabs.item>
        
        <x-filament::tabs.item 
            alpine-active="$wire.activeTab === 'installed'"
            x-on:click="$wire.setActiveTab('installed')"
        >
            {{ trans('minecraft-modrinth::strings.page.view_installed') }}
        </x-filament::tabs.item>
    </x-filament::tabs>
</div>
