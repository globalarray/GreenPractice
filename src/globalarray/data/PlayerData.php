<?php

declare(strict_types=1);

namespace globalarray\data;

use globalarray\data\component\UserData;
use globalarray\data\component\StatsData;

final class PlayerData{

    private $userData;
    private $statsData;

    public function __construct(
        UserData $userData,
        StatsData $statsData
    ) {
        $this->userData = $userData;
        $this->statsData = $statsData;
    }

    public function getUserData() : UserData{
        return $this->userData;
    }

    public function getStatsData() : StatsData{
        return $this->statsData;
    }

    public static function fromTemplate(string $nickname) : self{
        return new self(
            new UserData($nickname),
            new StatsData,
        );
    }
}