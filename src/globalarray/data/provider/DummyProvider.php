<?php

declare(strict_types=1);

namespace globalarray\data\provider;

use pocketmine\Player;
use globalarray\data\PlayerData;
use globalarray\event\data\PlayerDataLoadedEvent;

class DummyProvider implements DataProvider{

    public function requestData(Player $player) : void{
        $data = PlayerData::fromTemplate($player->getName());
        (new PlayerDataLoadedEvent($player, $data))->call();
    }

    public function saveData(Player $player) : void{}
}