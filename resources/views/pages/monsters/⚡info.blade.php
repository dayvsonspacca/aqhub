<?php

use App\Models\Monster;
use Livewire\Component;

new class extends Component
{
    public Monster $monster;
    public ?Monster $previousMonster = null;
    public ?Monster $nextMonster = null;

    public function mount(Monster $monster): void
    {
        $this->monster = $monster->load('passives');
        $this->previousMonster = Monster::where('id', '<', $monster->id)->orderBy('id', 'desc')->first();
        $this->nextMonster = Monster::where('id', '>', $monster->id)->orderBy('id', 'asc')->first();
    }

    public function with(): array
    {
        return [
            'monster' => $this->monster,
            'previousMonster' => $this->previousMonster,
            'nextMonster' => $this->nextMonster,
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
    <x-header title="{{ $monster->name }}" separator size="text-3xl" class="my-5">
        <x-slot:actions>
            <x-button
                icon="o-chevron-left"
                class="btn-secondary"
                :link="$previousMonster ? '/monsters/' . $previousMonster->id : null"
                :disabled="!$previousMonster"
                wire:navigate
            />
            <x-button
                icon="o-chevron-right"
                class="btn-secondary"
                :link="$nextMonster ? '/monsters/' . $nextMonster->id : null"
                :disabled="!$nextMonster"
                wire:navigate
            />
        </x-slot:actions>
    </x-header>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="col-span-1" id="monster-swf"></div>
        <div class="col-span-1 flex flex-col gap-4">

            {{-- Stats --}}
            <div class="flex gap-4">
                <x-stat
                    title="Level"
                    value="{{ $monster->level->value }}"
                    icon="o-bolt"
                />
                <x-stat
                    title="Health"
                    value="{{ number_format($monster->health) }}"
                    icon="o-heart"
                />
                <x-stat
                    title="Difficulty"
                    value="{{ $monster->difficulty }}"
                    icon="o-fire"
                />
            </div>

            @if($monster->passives->isNotEmpty())
                <x-card title="Passives" shadow separator>
                    <div class="flex flex-wrap gap-2">
                        @foreach($monster->passives as $passive)
                            <x-badge :value="$passive->description" class="badge-primary" />
                        @endforeach
                    </div>
                </x-card>
            @endif

            <x-card title="Details" shadow separator>
                <x-list-item :item="$monster" no-separator no-hover>
                    <x-slot:value>Asset Name</x-slot:value>
                    <x-slot:sub-value>{{ $monster->asset_name }}</x-slot:sub-value>
                </x-list-item>
                <x-list-item :item="$monster" no-hover>
                    <x-slot:value>Registered At</x-slot:value>
                    <x-slot:sub-value>{{ $monster->registered_at?->format('M d, Y') ?? 'N/A' }}</x-slot:sub-value>
                </x-list-item>
            </x-card>

        </div>
    </div>
</livewire:main-container>

@script
<script>
    loadSwf('monster-swf', '/proxy/swf/monster/{{ $monster->asset_name }}');
</script>
@endscript