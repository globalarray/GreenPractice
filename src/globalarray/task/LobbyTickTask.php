<?php

declare(strict_types=1);

namespace globalarray\task;

use pocketmine\scheduler\Task;
use globalarray\lobby\Lobby;

final class LobbyTickTask extends Task{

    public function __construct() {}

    public function onRun(int $currentTick) : void{
        Lobby::onTick($currentTick);
    }
}