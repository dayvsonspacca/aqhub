<?php

namespace App\Actions\Contracts;

use AqwSocketClient\Objects\Quest\Quest;

interface UpsertQuestContract
{
    public function handle(Quest $quest): \App\Models\Quest;
}
