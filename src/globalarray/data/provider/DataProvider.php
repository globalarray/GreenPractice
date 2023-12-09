<?php

declare(strict_types=1);

namespace globalarray\data\provider;

use pocketmine\Player;

interface DataProvider{
    public function requestData(Player $player) : void;
    public function saveData(Player $player) : void;
}