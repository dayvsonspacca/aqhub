<?php

use Livewire\Component;

new class extends Component {
    public string $swf;

    /** @var array<string, string> */
    public array $parameters = [];

    public bool $autoload = false;

    public string $playerId = '';

    public function mount(): void
    {
        if ($this->playerId === '') {
            $this->playerId = 'ruffle-' . uniqid();
        }
    }
};
?>

<div wire:ignore>
    <div id="{{ $playerId }}" class="w-full aspect-4/3"></div>
</div>

@script
<script>
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

    const __ruffleContainer = document.getElementById(@json($playerId));

    window[@json($playerId)] = {
        _player: null,

        load(extraParameters = {}) {
            if (!this._player) {
                this._player = window.RufflePlayer.newest().createPlayer();
                this._player.style.width = "100%";
                this._player.style.height = "100%";
                __ruffleContainer.innerHTML = "";
                __ruffleContainer.append(this._player);
            }

            this._player.load({
                url: @json($swf),
                parameters: { ...@json($parameters), ...extraParameters }
            });
        },

        playAnim(anim) {
            if (!this._player || typeof this._player.playAnim !== "function") {
                return;
            }

            this._player.playAnim(anim);
        }
    };

    @if($autoload)
        window[@json($playerId)].load();
    @endif
</script>
@endscript
