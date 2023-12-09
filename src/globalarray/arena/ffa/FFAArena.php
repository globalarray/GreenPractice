<?php

declare(strict_types=1);

namespace globalarray\arena\ffa;

use globalarray\arena\ArenaBase;
use globalarray\util\ArmorUtils;
use globalarray\arena\player\ArenaPlayer;
use pocketmine\Player;
use pocketmine\item\Armor;

abstract class FFAArena extends ArenaBase{

    public static function onTick(int $currentTick) : void{

    }

    public function isAvailable() : bool{
        return true;
    }

    public function onDeath(ArenaPlayer $entity) : void{
        parent::onDeath($entity);
        $damager = $entity->getOpponent();
        if ($damager !== NULL) {
            $this->giveKit($damager->getBasePlayer());
        }
    }

    public function giveKit(Player $player) : void{
        ($inventory = $player->getInventory())->clearAll();
        foreach ($this->getKitItems() as $item) {
            if ($item instanceof Armor) {
                ArmorUtils::equip($player, $item);
            }
            $inventory->addItem($item);
        }
    }

    abstract public function getKitItems() : iterable;
}