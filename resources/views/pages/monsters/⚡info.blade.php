<?php
use App\Models\Monster;
use Livewire\Component;

new class extends Component
{
    public Monster $monster;    
    
    public function mount(Monster $monster): void
    {
        // Carrega as passivas junto
        $this->monster = $monster->load('passives');
    }
    
    public function with(): array
    {
        return [
            'monster' => $this->monster,
            'breadcrumbs' => [
                ['label' => 'Home', 'link' => '/'],
                ['label' => 'World'],
                ['label' => 'Monsters', 'link' => '/monsters'],
                ['label' => $this->monster->name]
            ]
        ];
    }
};
?>

<livewire:main-container>
    <x-breadcrumbs :items="$breadcrumbs" />
    <x-header title="{{ $monster->name }}" separator size="text-3xl" class="my-5" />
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <img src="https://picsum.photos/500/200" alt="{{ $monster->name }}" class="rounded-lg shadow-lg w-full max-w-md" />
    </div>
</livewire:main-container>