<?php

use App\Models\Region;
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

    public array $sortBy = [
        'column' => 'name', 'direction' => 'asc',
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
            ['key' => 'maps', 'label' => 'Maps', 'format' => fn ($row, Collection $field) => $field->count(), 'sortable' => false],
        ];
    }

    public function regions(): LengthAwarePaginator
    {
        return Region::query()
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
            'regions' => $this->regions(),
            'breadcrumbs' => [
                ['label' => 'Home', 'link' => '/'],
                ['label' => 'World'],
                ['label' => 'Regions', 'link' => '/regions'],
            ],
        ];
    }
};
