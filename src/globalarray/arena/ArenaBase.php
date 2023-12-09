<?php

declare(strict_types=1);

namespace globalarray\arena;

use pocketmine\Server;
use pocketmine\level\Level;
use GreenAPI\types\DayNightCycle;
use GameAPI\arena\IArena;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\level\Position;
use Symfony\Component\Filesystem\Path;
use globalarray\lobby\Lobby;
use globalarray\arena\player\ArenaPlayer;
use const globalarray\RESOURCE_PATH;
use function spl_object_id;
use function delay;

abstract class ArenaBase implements IArena{

    /** @var int */
    protected $id;
    /** @var Level */
    protected $level;
    /** @var string */
    protected $mapName;
    /** @var Position */
    protected $spawnPosition;
    /** @var Player[] */
    protected $players = [];
    /** @var int */
    protected $slots;
    /** @var bool */
    protected $worldManipulation;
    /** @var int */
    protected $voidCoord;
    
    public function __construct(string $mapName, int $id) {
        $this->id = $id;
        $success = ($server = Server::getInstance())->loadLevel($mapName);
        if (!$success) {
            $this->getLogger()->error("Failed to load the arena \"$mapName\"");
            return;
        }

        $this->level = $server->getLevelByName($mapName);

        if ($this->level === NULL) {
            $this->getLogger()->error("Level isNull \"$mapName\"");
            return;
        }

        $this->level->setTime(DayNightCycle::NOON);
        $this->level->stopTime();
        $this->level->setAutoSave(false);
        $this->mapName = $mapName;

        $config = (new Config(Path::join(Path::join(RESOURCE_PATH, 'arenas'), $mapName . '.yml'), Config::YAML, [
            "slots" => 20,
            "allow_world_manipulation" => false,
            "spawn_position" => [0, 0, 0],
            "voidcoord" => 0
        ]))->getAll();

        $this->slots = $config["slots"];

        $this->voidCoord = $config["voidcoord"];
        
        $this->worldManipulation = (bool)$config["allow_world_manipulation"];

        $rawPosition = $config["spawn_position"];

        $this->spawnPosition = new Position($rawPosition[0], $rawPosition[1], $rawPosition[2], $this->level);
    }

    public function spawnPlayer(Player $player) : void{
        $player->setGamemode($this->worldManipulation ? 0 : 2);
        $player->setMaxHealth(20);
        $player->setHealth(20);
        $player->setFlying(false);
        $player->getInventory()->clearAll();
        $player->teleport($this->spawnPosition);
    }

    public function hasPlayer(Player $player) : bool{
        return isset($this->players[spl_object_id($player)]);
    }

    public function addPlayer(Player $player) : void{
        Lobby::removePlayer($player);
        $arenaPlayer = GameManager::openSession($player, $this);
        $this->players[spl_object_id($player)] = $arenaPlayer;
        $this->spawnPlayer($player);
    }

    public function removePlayer(Player $player) : void{
        unset($this->players[spl_object_id($player)]);
    }

    public function addSpectator(Player $player) : void{
        $arenaPlayer = GameManager::get($player);
        $arenaPlayer->setSpectator();
        $player->setGamemode(3); //watcher
        $player->teleport($this->spawnPosition->add(0, 3, 0));
        delay(function() use ($player) : void{
            if ($player->isConnected()) {
                //give menu
            }
        });
    }

    public function removeSpectator(Player $player) : void{
        $arenaPlayer = GameManager::get($player);
        $arenaPlayer->setSpectator(false);
        $this->spawnPlayer($player);
    }

    abstract public static function onTick(int $currentTick) : void;

    public function onDeath(ArenaPlayer $entity) : void{
        $this->addSpectator($entity->getBasePlayer());
        $damager = $entity->getOpponent();
        if ($damager !== NULL) {
            $damager->setOpponent(null);
            $damagerSession = $damager->getSession();
            $damagerSession->getData()->getStatsData()->onKill();
            $damagerSession->setDefaultNameTag();
        }
        $entitySession = $entity->getSession();
        $entitySession->getData()->getStatsData()->onDeath();
        $entity->setOpponent(null);
        $entitySession->setDefaultNameTag();
    }

    public function onDamage(ArenaPlayer $target, ArenaPlayer $damager) : void{
        $target->setLastReceiveHit(time());
        $target->setArenaNameTag();
    }

    public function onBlockPlace(Player $player, Vector3 $vec3, Block $block) : void{}

    public function onBlockBreak(Player $player, Vector3 $vec3, Block $block) : void{}

    public function onPlayerMove(Player $player, Vector3 $to, Vector3 $from) : void{
        if ($to->getFloorY() <= $this->voidCoord) {
            $arenaPlayer = GameManager::get($player);
            $this->onDeath($arenaPlayer);
        }
    }

    public function isAllowedWorldManipulation() : bool{
        return $this->worldManipulation;
    }

    public function getMaxPlayers() : int{
        return $this->slots;
    }

    public function getPlayersCount() : int{
        return count($this->players);
    }

    public function getLevel() : Level{
        return $this->level;
    }

    public function isAvailable() : bool{
        return $this->getPlayersCount() < $this->getMaxPlayers();
    }

    public function getId() : int{
        return $this->id;
    }
}