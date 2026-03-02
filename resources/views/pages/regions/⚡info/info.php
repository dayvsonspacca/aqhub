<?php

use App\Models\Region;
use Livewire\Component;
use Mary\Traits\Toast;

new class extends Component
{
    use Toast;

    public Region $region;

    public function mount(Region $region): void
    {
        $this->region = $region->load('maps');
    }

    public function with(): array
    {
        return [
            'breadcrumbs' => [
                ['label' => 'Home', 'link' => '/'],
                ['label' => 'World'],
                ['label' => 'Regions', 'link' => '/regions'],
                ['label' => $this->region->name],
            ],
        ];
    }
};
