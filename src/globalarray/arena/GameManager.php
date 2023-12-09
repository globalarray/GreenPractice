<?php

declare(strict_types=1);

namespace globalarray\arena;

use globalarray\util\CommonFactoryTrait;
use globalarray\arena\player\ArenaPlayer;
use pocketmine\Player;

final class GameManager{
    use CommonFactoryTrait;

    public static function openSession(Player $player, ArenaBase $arena) : void{
        self::register($player, new ArenaPlayer(
            $player,
            $arena
        ));
    }

    public static function closeSession(Player $player) : void{
        self::unregister($player);
    }
}