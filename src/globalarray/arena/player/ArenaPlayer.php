<?php

declare(strict_types=1);

namespace globalarray\arena\player;

use pocketmine\Player;
use globalarray\arena\ArenaBase;
use globalarray\session\SessionManager;
use pocketmine\network\mcpe\protocol\SetEntityDataPacket;
use pocketmine\utils\TextFormat as TF;

class ArenaPlayer{

    /** @var ArenaPlayer|null */
    private $opponent = null;
    /** @var Player */
    private $basePlayer;
    /** @var ArenaBase */
    private $arena;
    /** @var int */
    private $lastReceiveHit = 0;
    /** @var PlayerSession */
    private $session;
    
    public function __construct(
        /*private readonly*/ Player $basePlayer,
        /*private readonly*/ ArenaBase $arena
    ) {
        $this->opponent = $basePlayer;
        $this->arena = $arena;
        $this->session = SessionManager::get($basePlayer);
    }

    public function getSession() : PlayerSession{
        return $this->session;
    }

    public function setOpponent(?ArenaPlayer $opponent = null) : void{
        $this->opponent = $opponent;
    } 

    public function getOpponent() : ?ArenaPlayer{
        return $this->opponent;
    }

    public function getArena() : ArenaBase{
        return $this->arena;
    }

    public function getLastReceiveHit() : int{
        return $this->lastReceiveHit;
    }

    public function setLastReceiveHit(int $lastReceiveHit) : void{
        $this->lastReceiveHit = $lastReceiveHit;
    }

    private function buildNameTag() : string{
        $hp = $this->basePlayer->getHealth();
        $healthColor = match (true) {
            $this->basePlayer->gamemode === Player::ADVENTURE || $this->basePlayer->gamemode === Player::SPECTATOR || $hp > 15 || $hp <= 0 => TF::WHITE,
            $hp > 12 => TF::YELLOW,
            $hp > 7 => TF::GOLD,
            $hp > 0 => TF::RED,
        };
        $platform = Utils::getPlatform($this->basePlayer);

        return /* getcolorpriv */ $this->basePlayer->getName() . PHP_EOL . ($platform !== "" ? TF::GRAY . '(' . TF::YELLOW . $platform . TF::GRAY . ')' : '') .
        $healthColor . $hp . ' HP';
    }

    public function setArenaNameTag() : void{
        if ($this->opponent === NULL) return;
        $nametag = $this->buildArenaNameTag();
        $pk = new SetEntityDataPacket();
        $pk->entityRuntimeId = $this->basePlayer->getId();
        $pk->metadata = [
            Entity::DATA_NAMETAG => [
                Entity::DATA_TYPE_STRING,
                $nametag
            ]
        ];
        $this->opponent->getBasePlayer()->sendDataPacket($pk);
    }

    public function close() : void{
        $this->arena->removePlayer($this->basePlayer);
    }
}