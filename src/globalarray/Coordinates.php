<?php

declare(strict_types=1);

namespace globalarray;

use GreenAPI\utils\RegistryTrait;
use pocketmine\level\Position;
use pocketmine\level\Location;
use pocketmine\Server;

/**
 * @method static Position LOBBY_SPAWN()
 * @method static Location FFA_JOIN_NPC()
 */
final class Coordinates{
    use RegistryTrait;

    public static function setup() : void{
        $defaultLevel = Server::getInstance()->getDefaultLevel();
        self::register("lobby_spawn", new Position(476, 110, -456, $defaultLevel));
        self::register("ffa_join_npc", new Location(476.5788, 111, -472.6338, 0, 0, $defaultLevel));
    }

    protected static function register(string $id, /*mixed*/ $coordinates) : void{
        self::_registryRegister($id, $coordinates);
    }
}