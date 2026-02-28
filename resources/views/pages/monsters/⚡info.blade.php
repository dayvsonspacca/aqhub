<?php

use App\Models\Monster;
use Livewire\Component;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;

    public Monster $monster;

    public function mount(Monster $monster): void
    {
        $this->monster = $monster->load(['passives', 'maps']);
    }

    public function with(): array
    {
        return [
            'breadcrumbs' => [['label' => 'Home', 'link' => '/'], ['label' => 'World'], ['label' => 'Monsters', 'link' => '/monsters'], ['label' => $this->monster->name]],
        ];
    }

    public function copyJoinName(string $joinName): void
    {
        $this->js("navigator.clipboard.writeText('/join $joinName')");
        $this->success("Copied: /join $joinName", position: 'toast-top');
    }
};
?>

<livewire:main-container>
    <x-breadcrumbs :items="$breadcrumbs" />

    <x-header title="{{ $monster->name }}" separator size="text-3xl" class="my-5" />

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Stats -->
        <div class="col-span-2 grid grid-cols-2 gap-4">
            <x-stat class="col-span-2 md:col-span-1" title="Level" value="{{ $monster->level }}" icon="o-bolt" />
            <x-stat class="col-span-2 md:col-span-1" title="Health" value="{{ $monster->health }}" icon="o-heart" />
        </div>

        <!-- Player -->
        <x-card title="Animations" subtitle="Click to play" shadow separator class="col-span-1" body-class="overflow-hidden">
            <div wire:ignore>
                <div id="monster-swf"></div>
                <div id="monster-anims" class="flex flex-wrap gap-2">
                    <x-progress class="progress-primary h-0.5" indeterminate />
                </div>
            </div>
        </x-card>

        <!-- Details -->
        <x-card title="Details" shadow separator class="col-span-1" subtitle="Additional information">
            <x-list-item :item="$monster" no-separator no-hover>
                <x-slot:value>Asset Name</x-slot:value>
                <x-slot:sub-value>{{ $monster->asset_name }}</x-slot:sub-value>
            </x-list-item>
            <x-list-item :item="$monster" no-separator no-hover>
                <x-slot:value>Asset Link</x-slot:value>
                <x-slot:sub-value>{{ $monster->asset_link }}</x-slot:sub-value>
            </x-list-item>
            <x-list-item :item="$monster" no-hover>
                <x-slot:value>Registered At</x-slot:value>
                <x-slot:sub-value>{{ $monster->registered_at?->format('M d, Y') ?? 'N/A' }}</x-slot:sub-value>
            </x-list-item>
        </x-card>

        <!-- Locations -->
        @if ($monster->maps->isNotEmpty())
        <x-card title="Locations" subtitle="Where you can find this monster" shadow separator class="col-span-2" x-data="{ open: true }">
            <x-slot:menu>
                <x-button :icon="'o-chevron-down'" class="btn-circle btn-sm" x-on:click="open = !open"
                    x-bind:class="open ? 'rotate-180' : ''" />
            </x-slot:menu>

            <div x-show="open" x-collapse>
                @foreach ($monster->maps as $map)
                <x-list-item :item="$map" link="/maps/{{ $map->id }}" class="px-0" no-separator>
                    <x-slot:actions>
                        <x-button
                            label="/join {{ $map->join_name }}"
                            icon="o-clipboard"
                            class="btn-ghost btn-sm font-mono"
                            wire:click="copyJoinName('{{ $map->join_name }}')" />
                    </x-slot:actions>
                </x-list-item>
                @endforeach
            </div>
        </x-card>
        @endif

        <!-- Passives -->
        @if ($monster->passives->isNotEmpty())
        <x-card title="Passives" shadow separator>
            <div class="flex flex-wrap gap-2">
                @foreach ($monster->passives as $passive)
                <x-badge :value="$passive->description" class="badge-primary" />
                @endforeach
            </div>
        </x-card>
        @endif
    </div>
</livewire:main-container>

@script
<script>
    window.onMonsterLabelsLoaded = function(labelsCSV) {
        const anims = labelsCSV.split(",").filter(Boolean);
        const container = document.getElementById("monster-anims");
        if (!container) return;

        container.innerHTML = "";

        anims.forEach(anim => {
            const btn = document.createElement("button");
            btn.className = "btn btn-xs btn-outline btn-primary uppercase font-bold";
            btn.textContent = anim;
            btn.onclick = () => {
                const playerElement = document.querySelector("ruffle-player");
                playerElement.load({
                    url: "/swfs/monster.swf",
                    parameters: {
                        sFile: "{{ $monster->asset_name }}",
                        sSymbol: "{{ $monster->asset_link }}",
                        sAnim: anim
                    }
                });
            };
            container.append(btn);
        });
    };

    window.RufflePlayer = window.RufflePlayer || {};
    window.RufflePlayer.config = {
        publicPath: "/build/ruffle/",
        backgroundColor: "#1f202a",
        quality: "high",
        autoplay: "on",
        unmuteOverlay: "hidden",
        splashScreen: false,
        showSwfDownload: true,
        allowScriptAccess: true
    };

    const ruffle = window.RufflePlayer.newest();
    const player = ruffle.createPlayer();
    const container = document.getElementById("monster-swf");

    container.innerHTML = "";
    container.append(player);

    player.load({
        url: "/swfs/monster.swf",
        parameters: {
            sFile: "{{ $monster->asset_name }}",
            sSymbol: "{{ $monster->asset_link }}",
            sAnim: "Idle"
        }
    });
</script>
@endscript