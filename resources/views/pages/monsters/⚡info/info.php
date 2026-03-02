<?php

use App\Models\Monster;
use Livewire\Component;
use Mary\Traits\Toast;

new class extends Component
{
    use Toast;

    public Monster $monster;

    public function mount(Monster $monster): void
    {
        $this->monster = $monster->load(['passives', 'maps']);
    }

    public function with(): array
    {
        return [
            'breadcrumbs' => [
                ['label' => 'Home', 'link' => '/'],
                ['label' => 'World'],
                ['label' => 'Monsters', 'link' => '/monsters'],
                ['label' => $this->monster->name],
            ],
        ];
    }
};
