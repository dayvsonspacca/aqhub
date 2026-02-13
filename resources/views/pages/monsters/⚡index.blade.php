<?php

use App\Models\Monster;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new class extends Component
{
    use Toast;
    use WithPagination;

    public string $search = '';
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
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'health', 'label' => 'Health'],
            ['key' => 'difficulty', 'label' => 'Difficulty'],
            ['key' => 'level.value', 'label' => 'Level']
        ];
    }

    public function monsters(): LengthAwarePaginator
    {
        return Monster::query()
            ->when($this->search, fn(Builder $q) => $q->where('name', 'like', "%$this->search%"))
            ->paginate(15);
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

<livewire:main-container>
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
        :headers="$headers"
        :rows="$monsters"
        link="/monsters/{id}"
        with-pagination
    />

    <x-drawer wire:model="drawer" title="Filters" right separator with-close-button class="lg:w-1/3">
        <x-input placeholder="Search..." wire:model.live.debounce="search" icon="o-magnifying-glass" @keydown.enter="$wire.drawer = false" />

        <x-slot:actions>
            <x-button label="Reset" icon="o-x-mark" wire:click="clear" spinner />
            <x-button label="Done" icon="o-check" class="btn-primary" @click="$wire.drawer = false" />
        </x-slot:actions>
    </x-drawer>
</livewire:main-container>