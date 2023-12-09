<?php

declare(strict_types=1);

namespace globalarray\arena\ffa;

use pocketmine\Player;
use pocketmine\item\ItemIds;
use pocketmine\item\Item;
use globalarray\arena\player\ArenaPlayer;

class BuildFFAArena extends FFAArena{

    public function spawnPlayer(Player $player) : void{
        parent::spawnPlayer($player);
        $this->giveKit($player);
    }

    public function onDamage(ArenaPlayer $target, ArenaPlayer $damager) : void{
        $target->setOpponent($damager);
    }

    public function getKitItems() : array{
        return [
            Item::get(ItemIds::DIAMOND_HELMET),
            Item::get(ItemIds::DIAMOND_CHESTPLATE),
            Item::get(ItemIds::DIAMOND_LEGGINGS),
            Item::get(ItemIds::DIAMOND_BOOTS)
        ];
    }
}