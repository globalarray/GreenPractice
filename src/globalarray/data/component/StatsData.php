<?php

declare(strict_types=1);

namespace globalarray\data\component;

use globalarray\util\Utils;

final class StatsData{

    /** @var float */
    public $level;
    /** @var int */
    private $recordKillStreak;
    /** @var int */
    private $killStreak;
    /** @var int */
    private $kills;
    /** @var int */
    private $experience;

    public function __construct(
        int $kills = 0,
        int $recordKillStreak = 0,
        int $killStreak = 0,
        int $experience = 0
    ) {
        $this->kills = $kills;
        $this->recordKillStreak = $recordKillStreak;
        $this->killStreak = $killStreak;
        $this->experience = $experience;
        $this->level = Utils::getLevelFromXp($experience);
    }

    public function getKills() : int{
        return $this->kills;
    }

    public function getRecordKillStreak() : int{
        return $this->recordKillStreak;
    }

    public function getKillStreak() : int{
        return $this->killStreak;
    }

    public function getExperience() : int{
        return $this->experience;
    }

    public function getLevel() : float{
        return $this->level;
    }

    public function getCeilLevel() : int{
        return (int)floor($this->level);
    }
}