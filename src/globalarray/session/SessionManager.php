<?php

declare(strict_types=1);

namespace globalarray\session;

use globalarray\util\CommonFactoryTrait;
use globalarray\data\PlayerData;
use pocketmine\Player;

final class SessionManager{
    use CommonFactoryTrait;

    public static function openSession(Player $player, PlayerData $data) : void{
        self::register($player, new PlayerSession(
            $player,
            $data
        ));
    }

    public static function closeSession(Player $player) : void{
        $session = self::get($player);

        if ($session === NULL) return;

        $session->close();

        self::unregister($player);
    }
}