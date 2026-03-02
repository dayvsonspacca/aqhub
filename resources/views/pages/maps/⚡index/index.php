<?php

use App\Models\Map;
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

    public ?bool $upgradeOnly = null;

    public array $sortBy = [
        'column' => 'name',
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
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'join_name.value', 'label' => 'Join', 'disableLink' => true, 'sortable' => false],
            ['key' => 'region.name', 'label' => 'Region', 'disableLink' => true, 'sortable' => false],
        ];
    }

    public function maps(): LengthAwarePaginator
    {
        return Map::query()
            ->when($this->search, fn (Builder $q) => $q->where('name', 'like', "%$this->search%"))
            ->when(! is_null($this->upgradeOnly), fn (Builder $q) => $q->where('upgrade_only', $this->upgradeOnly))
            ->with('region')
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
            'maps' => $this->maps(),
            'breadcrumbs' => [
                ['label' => 'Home', 'link' => '/'],
                ['label' => 'World'],
                ['label' => 'Maps', 'link' => '/maps'],
            ],
        ];
    }
};
