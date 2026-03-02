<x:main-container>
    <x-breadcrumbs :items="$breadcrumbs" />

    <x-header :title="$map->name" separator size="text-3xl" class="my-5" />

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Region --}}
        @if ($map->region)
            <x-card title="Region" :subtitle="$map->region->name" shadow class="col-span-2" />
        @endif

        {{-- Details --}}
        <x-card title="Details" shadow separator class="col-span-2" subtitle="Additional information">
            <x-list-item :item="$map" no-separator no-hover>
                <x-slot:value>Name</x-slot:value>
                <x-slot:sub-value>{{ $map->name }}</x-slot:sub-value>
            </x-list-item>
            @if ($map->description)
                <x-list-item :item="$map" no-separator no-hover>
                    <x-slot:value>Description</x-slot:value>
                    <x-slot:sub-value>{{ $map->description }}</x-slot:sub-value>
                </x-list-item>
            @endif
            @if ($map->recommended_min_level && $map->recommended_max_level) 
                <x-list-item :item="$map" no-separator no-hover>
                    <x-slot:value>Level recommended</x-slot:value>
                    <x-slot:sub-value>{{ $map->recommended_min_level }} - {{ $map->recommended_max_level }}</x-slot:sub-value>
                </x-list-item>
            @endif
            @if ($map->upgrade_only) 
                <x-alert title="This map is upgrade only" icon="o-exclamation-triangle" class="alert-info alert-outline" />
            @endif
        </x-card>

        {{-- Monsters --}}
        @if ($map->monsters->isNotEmpty())
            <x-card title="Monsters" subtitle="These are the monsters you can find here" shadow separator
                class="col-span-2" x-data="{ open: true }">
                <x-slot:menu>
                    <x-button :icon="'o-chevron-down'" class="btn-circle btn-sm" x-on:click="open = !open"
                        x-bind:class="open ? 'rotate-180' : ''" />
                </x-slot:menu>

                <div x-show="open" x-collapse>
                    @foreach ($map->monsters as $monster)
                        <x-list-item :item="$monster" link="/monsters/{{ $monster->id }}" class="px-0"
                            no-separator />
                    @endforeach
                </div>
            </x-card>
        @endif
    </div>
</x:main-container>
