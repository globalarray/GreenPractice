<?php

declare(strict_types=1);

namespace globalarray\lobby;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\level\Level;
use globalarray\task\LobbyTickTask;
use globalarray\Coordinates;
use globalarray\session\SessionManager;
use GreenAPI\types\DayNightCycle;

final class Lobby{

    private static /*array*/ $players = [];

    private static /*Level*/ $level;

    public static function init(Level $level) : void{
        Server::getInstance()->getScheduler()->scheduleRepeatingTask(new LobbyTickTask(), 1);
        self::$level = $level;
        $level->setTime(DayNightCycle::NOON);
        $level->stopTime();
    }

    public static function addPlayer(Player $player) : void{
        $session = SessionManager::get($player);
        $player->teleport(Coordinates::LOBBY_SPAWN());
        $player->setMaxHealth(20);
        $player->setHealth(20);
        $player->setGamemode(2); //adventure
        if ($session !== NULL) {
            $player->setXpLevel($ceilLevel = $session->getData()->getStatsData()->getCeilLevel());
            $player->setXpProgress($session->getData()->getStatsData()->getLevel()- $ceilLevel);
        }
        self::$players[spl_object_id($player)] = $player;
    }

    public static function isLobby(Level $level) : bool{
        return self::$level === $level;
    }

    public static function removePlayer(Player $player) : void{
        unset(self::$players[spl_object_id($player)]);
    }

    public static function onTick(int $currentTick) : void{

    }
}