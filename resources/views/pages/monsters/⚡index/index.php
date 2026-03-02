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

    public int $perPage = 10;

    public bool $drawer = false;

    public array $sortBy = [
        'column' => 'name',
        'direction' => 'asc',
        'column' => 'level',
        'direction' => 'asc',
        'column' => 'health',
        'direction' => 'asc',
    ];

    public function clear(): void
    {
        $this->reset();
        $this->resetPage();
        $this->success('Filters cleared.', position: 'toast-bottom');
    }

    public function headers(): array
    {
        return [
            ['key' => 'name.value', 'label' => 'Name', 'sortBy' => 'name'],
            ['key' => 'level.value', 'label' => 'Level', 'sortBy' => 'level'],
            ['key' => 'health.value', 'label' => 'Health', 'sortBy' => 'health'],
        ];
    }

    public function monsters(): LengthAwarePaginator
    {
        return Monster::query()
            ->when($this->search, fn (Builder $q) => $q->where('name', 'like', "%$this->search%"))
            ->with('maps')
            ->orderBy(...array_values($this->sortBy))
            ->paginate($this->perPage);
    }

    public function updated($property): void
    {
        if (! is_array($property) && $property != '') {
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
            ],
        ];
    }
};
