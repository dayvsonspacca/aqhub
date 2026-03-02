<x:main-container>
    <x-breadcrumbs :items="$breadcrumbs" />
    <x-header title="Regions" separator size="text-3xl" class="mt-5">
        <x-slot:middle class="justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button class="btn-primary" label="Filters" @click="$wire.drawer = true" responsive icon="o-funnel" />
        </x-slot:actions>
    </x-header>

    <x-table
        :sort-by="$sortBy"
        class="bg-base-100"
        :headers="$headers"
        :rows="$regions"
        link="/regions/{id}"
        with-pagination
        show-empty-text
        :per-page-values="[10, 25, 50]"
        per-page="perPage"
    />

    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button class="lg:w-1/3">
        <x-input placeholder="Search..." wire:model.live.debounce="search" icon="o-magnifying-glass" @keydown.enter="$wire.drawer = false" />

        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Done" icon="o-check" class="btn-secondary" @click="$wire.drawer = false" />
        </x-slot:actions>
    </x-drawer>
</x:main-container>