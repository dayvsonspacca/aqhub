<x:main-container>
    <x-breadcrumbs :items="$breadcrumbs" />

    <x-header title="{{ $region->name }}" separator size="text-3xl" class="my-5" />

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Maps -->
        @if ($region->maps->isNotEmpty())
            <x-card title="Maps" subtitle="Maps presents in this region" shadow separator class="col-span-2"
                x-data="{ open: true }">
                <x-slot:menu>
                    <x-button :icon="'o-chevron-down'" class="btn-circle btn-sm" x-on:click="open = !open"
                        x-bind:class="open ? 'rotate-180' : ''" />
                </x-slot:menu>

                <div x-show="open" x-collapse>
                    @foreach ($region->maps as $map)
                        <x-list-item :item="$map" link="/maps/{{ $map->id }}" class="px-0" no-separator>
                            <x-slot:actions>
                                <livewire:copy-button :label="'/join ' . $map->join_name" :value="$map->join_name" :key="'copy-' . $map->id" />
                            </x-slot:actions>
                        </x-list-item>
                    @endforeach
                </div>
            </x-card>
        @endif
    </div>
</x:main-container>