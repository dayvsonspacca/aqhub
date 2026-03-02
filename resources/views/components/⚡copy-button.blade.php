<?php

use Mary\Traits\Toast;

use Livewire\Component;

new class extends Component {
    use Toast;

    public string $label;
    public string $value;

    public function copy(): void
    {
        $this->js("navigator.clipboard.writeText('{$this->value}')");
        $this->success("Copied: {$this->value}", position: 'toast-top');
    }
};
?>

<div>
    <x-button label="{{ $label }}" icon="o-clipboard" class="btn-ghost btn-sm font-mono"
        wire:click="copy('{{ $value }}')" />
</div>