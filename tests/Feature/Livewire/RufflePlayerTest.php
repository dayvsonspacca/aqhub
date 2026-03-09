<?php

use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class);

it('renders the container with the given player-id', function () {
    Livewire::test('ruffle-player', [
        'swf' => '/swfs/monster.swf',
        'playerId' => 'monster-player',
    ])
        ->assertSeeHtml('id="monster-player"')
        ->assertSet('playerId', 'monster-player');
});

it('auto-generates a player-id when none is provided', function () {
    $component = Livewire::test('ruffle-player', [
        'swf' => '/swfs/monster.swf',
    ]);

    $playerId = $component->get('playerId');

    expect($playerId)->toStartWith('ruffle-');
    $component->assertSeeHtml("id=\"{$playerId}\"");
});

it('passes the swf url to the component', function () {
    Livewire::test('ruffle-player', [
        'swf' => '/swfs/monster.swf',
        'playerId' => 'test-player',
    ])
        ->assertSet('swf', '/swfs/monster.swf');
});

it('passes parameters to the component', function () {
    Livewire::test('ruffle-player', [
        'swf' => '/swfs/monster.swf',
        'parameters' => ['sFile' => 'Dragon.swf', 'sSymbol' => 'Dragon'],
        'playerId' => 'test-player',
    ])
        ->assertSet('parameters', ['sFile' => 'Dragon.swf', 'sSymbol' => 'Dragon']);
});

it('does not autoload by default', function () {
    Livewire::test('ruffle-player', [
        'swf' => '/swfs/monster.swf',
        'playerId' => 'test-player',
    ])
        ->assertSet('autoload', false);
});

it('renders with autoload enabled', function () {
    Livewire::test('ruffle-player', [
        'swf' => '/swfs/monster.swf',
        'playerId' => 'test-player',
        'autoload' => true,
    ])
        ->assertSet('autoload', true);
});
