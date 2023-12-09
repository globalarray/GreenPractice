<?php

declare(strict_types=1);

namespace globalarray\arena;

use pocketmine\Player;
use pocketmine\utils\Config;
use Symfony\Component\Filesystem\Path;
use globalarray\arena\ffa\BuildFFAArena;
use const globalarray\RESOURCE_PATH;
use function tr;

final class ArenaFactory{

    private static $arenas = [];
    private static $modes = [];

    public static function init() : void{
        $arenas = (new Config(Path::join(RESOURCE_PATH, "arenas.yml"), Config::YAML, [
            ArenaMode::BFFA => [
                "bffa1"
            ]
        ]))->getAll();

        $id = 0;

        foreach ($arenas as $mode => $worlds) {
            foreach ($worlds as $world) {
                $id++;
                switch ($mode) {
                    case ArenaMode::BFFA:
                        $arena = new BuildFFAArena($world, $id);
                        break;
                    default:
                        throw new \InvalidArgumentException();
                }
                /*$arena = match ($mode) {
                    ArenaMode::BFFA => new BuildFFAArena($world, $id)
                };*/
                self::register($id, $mode, $arena);
            }
        }
    }

    public static function joinTo(Player $player, string $mode) : void{
        foreach (self::$modes[$mode] as $id) {
            $arena = self::$arenas[$id];
            if ($arena->isAvailable()) {
                $arena->addPlayer($player);
                return;
            }
        }
        $player->sendMessage(tr("%practice.arena.notfound%", $player));
    }

    public static function register(int $id, string $mode, ArenaBase $arena) : void{
        self::$arenas[$id] = $arena;
        self::$modes[$mode][] = $id;
    }
}