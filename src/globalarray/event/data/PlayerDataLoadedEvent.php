<?php

declare(strict_types=1);

namespace globalarray\event\data;

use pocketmine\Player;
use pocketmine\event\player\PlayerEvent;
use globalarray\data\PlayerData;

final class PlayerDataLoadedEvent extends PlayerEvent{
    public static $handlerList = null;

    protected $data;

    public function __construct(Player $player, PlayerData $data) {
        $this->player = $player;
        $this->data = $data;
    }

    public function getData() : PlayerData{
        return $this->data;
    }
}