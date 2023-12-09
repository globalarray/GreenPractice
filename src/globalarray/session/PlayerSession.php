<?php

declare(strict_types=1);

namespace globalarray\session;

use pocketmine\Player;
use globalarray\data\PlayerData;
use globalarray\event\player\PlayerUpLevelEvent;
use globalarray\arena\GameManager;
use globalarray\lobby\Lobby;
use globalarray\session\tier\Tier;
use greenperms\player\PermPlayer;
use greenperms\group\Group;
use globalarray\util\Utils;
use pocketmine\utils\TextFormat as TF;

class PlayerSession implements Session{

    /** @var Tier */
    private $tier;
    /** @var Group */
    private $group;
    /** @var Player */
    private $basePlayer;
    /** @var PlayerData */
    private $data;

    public function __construct(
        /*protected readonly*/ Player $basePlayer,
        /*protected readonly*/ PlayerData $data
    ) {
        $this->basePlayer = $basePlayer;
        $this->data = $data;
        $permPlayer = PermPlayer::get($this->basePlayer);
        $this->group = $permPlayer->getGroup();
        $this->tier = Tier::fromString($this->group->getId());
        $this->setDefaultNameTag();
    }

    public function modifyLevel(int $delta) : void{
        $statsData = $this->data->getStatsData();
        $statsData->experience += $delta;
        $level = Utils::getLevelFromXp($statsData->getExperience());

        if (intval(floor($level)) - $statsData->getCeilLevel() >= 1) {
            (new PlayerUpLevelEvent($this))->call();
        }
        $statsData->level = $level;
    }

    private function buildNameTag() : string{
        $nametag = $this->group->getNameTagPrefix();
        $platform = Utils::getPlatform($this->basePlayer);
        $levelCeil = $this->getData()->getStatsData()->getCeilLevel();
        $inStaples = function (string $text) : string{ return TF::GRAY . '(' . $text . TF::GRAY . ')'; }; //PHP 7.3 moment.
        //$inStaples = fn(string $text) : string => TF::GRAY . '(' . $text . TF::GRAY . ')';
        return 
        $inStaples(Utils::getXpLevelColor($levelCeil) . $levelCeil) . $nametag . ' '. $this->basePlayer->getName() .
        ($platform !== "" ? $inStaples(TF::YELLOW . $platform) : '');
    }

    public function setDefaultNameTag() : void{
        $this->basePlayer->setNameTag($this->buildNameTag());
    }

    public function getGroup() : Group{
        return $this->group;
    }

    public function getTier() : Tier{
        return $this->tier;
    }

    public function getBasePlayer() : Player{
        return $this->basePlayer;
    }

    public function getData() : PlayerData{
        return $this->data;
    }

    public function close() : void{
        if (GameManager::get($this->basePlayer) !== NULL) {
            GameManager::closeSession($this->basePlayer);
            return;
        }
        Lobby::removePlayer($this->basePlayer);
    }
}