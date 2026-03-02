<?php

use App\Models\Map;
use App\Models\Monster;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new class extends Component
{
    use Toast;
    use WithPagination;

    public string $search = '';
    public int $perPage = 10;
    public bool $drawer = false;

    public function clear(): void
    {
        $this->reset();
        $this->resetPage();
        $this->success('Filters cleared.', position: 'toast-bottom');
    }

    public function headers(): array
    {
        return [
            ['key' => 'name.value', 'label' => 'Name'],
            ['key' => 'maps', 'label' => 'Maps', 'format' => fn($row, Collection $field) => implode(' ', $field->map(fn(Map $value, $key) => $value->name)->toArray())]
        ];
    }

    public function monsters(): LengthAwarePaginator
    {
        return Monster::query()
            ->when($this->search, fn(Builder $q) => $q->where('name', 'like', "%$this->search%"))
            ->with('maps')
            ->orderBy('name')
            ->paginate($this->perPage);
    }

    public function updated($property): void
    {
        if (! is_array($property) && $property != "") {
            $this->resetPage();
        }
    }

    public function with(): array
    {
        return [
            'headers' => $this->headers(),
            'monsters' => $this->monsters(),
            'breadcrumbs' => [
                ['label' => 'Home', 'link' => '/'],
                ['label' => 'World'],
                ['label' => 'Monsters', 'link' => '/monsters'],
            ]
        ];
    }
};
?>

<x:main-container>
    <x-breadcrumbs :items="$breadcrumbs" />
    <x-header title="Monsters" separator size="text-3xl" class="mt-5">
        <x-slot:middle class="justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button class="btn-primary" label="Filters" @click="$wire.drawer = true" responsive icon="o-funnel" />
        </x-slot:actions>
    </x-header>

    <x-table
        class="bg-base-100"
        :headers="$headers"
        :rows="$monsters"
        link="/monsters/{id}"
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