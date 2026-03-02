<?php

use App\Models\Map;
use Livewire\Component;
use Mary\Traits\Toast;

new class extends Component
{
    use Toast;

    public Map $map;

    public function mount(Map $map): void
    {
        $this->map = $map->load(['monsters', 'region']);
    }

    public function with(): array
    {
        return [
            'breadcrumbs' => [
                ['label' => 'Home', 'link' => '/'],
                ['label' => 'World'],
                ['label' => 'Maps', 'link' => '/maps'],
                ['label' => $this->map->name],
            ],
        ];
    }
};
