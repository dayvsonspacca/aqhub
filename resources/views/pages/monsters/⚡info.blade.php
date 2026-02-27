<?php

use App\Models\Monster;
use Livewire\Component;
use Livewire\Attributes\Computed;

new class extends Component {
    public Monster $monster;

    public function mount(Monster $monster): void
    {
        $this->monster = $monster->load('passives');
    }

    #[Computed]
    public function previousMonster(): ?Monster
    {
        return Monster::where('id', '<', $this->monster->id)
            ->orderBy('id', 'desc')
            ->first(['id', 'name']);
    }

    #[Computed]
    public function nextMonster(): ?Monster
    {
        return Monster::where('id', '>', $this->monster->id)
            ->orderBy('id', 'asc')
            ->first(['id', 'name']);
    }

    public function with(): array
    {
        return [
            'breadcrumbs' => [['label' => 'Home', 'link' => '/'], ['label' => 'World'], ['label' => 'Monsters', 'link' => '/monsters'], ['label' => $this->monster->name]],
        ];
    }
};
?>

<livewire:main-container>
    <x-breadcrumbs :items="$breadcrumbs" />

    <x-header title="{{ $monster->name }}" separator size="text-3xl" class="my-5">
        <x-slot:actions>
            <x-button icon="o-chevron-left" class="btn-secondary" :link="$this->previousMonster ? '/monsters/' . $this->previousMonster->id : null" :disabled="!$this->previousMonster" wire:navigate />
            <x-button icon="o-chevron-right" class="btn-secondary" :link="$this->nextMonster ? '/monsters/' . $this->nextMonster->id : null" :disabled="!$this->nextMonster" wire:navigate />
        </x-slot:actions>
    </x-header>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="col-span-1 flex flex-col gap-4">
            <div class="bg-base-100 p-2 rounded-2xl">
                <div id="monster-swf"></div>
            </div>
            <x-card title="Animations" subtitle="Click to play" shadow separator>
                <div id="monster-anims" class="flex flex-wrap gap-2" wire:ignore>
                    <span class="text-gray-500 text-sm italic">Detecting animations...</span>
                </div>
            </x-card>
        </div>

        <div class="col-span-1 flex flex-col gap-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-stat title="Level" value="{{ $monster->level }}" icon="o-bolt" />
                <x-stat title="Health" value="{{ number_format($monster->health) }}" icon="o-heart" />
                <x-stat title="Difficulty" value="{{ $monster->difficulty ?? '--' }}" icon="o-fire" />
            </div>

            <x-card title="Details" shadow separator>
                <x-list-item :item="$monster" no-separator no-hover>
                    <x-slot:sub-value><span class="font-mono text-sm">{{ $monster->asset_name }}</span></x-slot:sub-value>
                    <x-slot:value>Asset Name</x-slot:value>
                </x-list-item>
                <x-list-item :item="$monster" no-hover>
                    <x-slot:value>Registered At</x-slot:value>
                    <x-slot:sub-value>{{ $monster->registered_at?->format('M d, Y') ?? 'N/A' }}</x-slot:sub-value>
                </x-list-item>
            </x-card>

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
                btn.className = "btn btn-sm btn-outline btn-primary uppercase font-bold";
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
            logLevel: "info",
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
