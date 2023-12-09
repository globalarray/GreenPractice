<?php

declare(strict_types=1);

namespace globalarray\data\provider;

use pocketmine\Server;
use pocketmine\Player;
use globalarray\session\SessionManager;
use GreenAPI\mysqlt\mysqlt;
use GreenAPI\mysqlt\MysqlCredentials;
use GreenAPI\mysqlt\MysqliException;

/*
CREATE TABLE minigames.practice_data
(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nickname VARCHAR(16) NOT NULL,
    displayedNickname VARCHAR(16) NOT NULL,
    kills INT NOT NULL,
    recordKillStreak INT NOT NULL,
    killStreak INT NOT NULL,
    experience INT NOT NULL
) ENGINE = InnoDB 
*/
class MysqlProvider implements DataProvider{

    /** @var mysqlt */
    private static $mysqlt;

    public function __construct(array $credentials) {
        self::$mysqlt = new mysqlt(
            new MysqlCredentials(
                $credentials["host"],
                $credentials["user"], 
                $credentials["password"],
                $credentials["database"],
                $credentials["port"]
            )
        );
    }

    public function requestData(Player $player) : void{
        $nickname = $player->getLowerCaseName();
        
        self::$mysqlt->query(
            "SELECT * FROM practice_data WHERE nickname = '%s'",
            [$nickname]
        )->onResolve(
            function ($result) use ($nickname) : void{
                self::onReceiveData($nickname, $result);
            }
            //\Closure::fromCallable([self::class, 'onReceiveData'])
        )->onError(
            function(MysqliException $error) use ($nickname, $player) : void{
                \GlobalLogger::get()->warning("An error occurred while reading the \"$nickname\" player data: " . $error->getMessage());
                if ($player->isConnected()) {
                    $player->close('', "Data error");
                }
            }
        );
    }

    public function saveData(Player $player) : void{
        $nickname = $player->getLowerCaseName();

        $session = SessionManager::get($player);

        if ($session === NULL) return;

        $data = $session->getData();

        $q = /** @lang MySQL */
        <<<QUERY
        INSERT INTO practice_data (
            nickname,
            displayedNickname,
            kills,
            recordKillStreak,
            killStreak,
            experience
        ) VALUES ('%s', '%s', %d, %d, %d, %d) ON DUPLICATE KEY UPDATE
        displayedNickname = '%s',
        kills = %d,
        recordKillStreak = %d,
        killStreak = %d,
        experience = %d;
        QUERY;

        self::$mysqlt->query(
            $q,
            [
                $nickname,
                $player->getName(),
                ($statsData = $data->getStatsData())->getKills(),
                $statsData->getRecordKillStreak(),
                $statsData->getKillStreak(),
                $statsData->getExperience(),
                //
                $player->getName(),
                ($statsData = $data->getStatsData())->getKills(),
                $statsData->getRecordKillStreak(),
                $statsData->getKillStreak(),
                $statsData->getExperience(),
            ]
        )->onError(
            function (MysqliException $error) use ($nickname) : void{
                \GlobalLogger::get()->warning("An error occurred while saving the \"$nickname\" player data: " . $error->getMessage());
            }
        );
    }

    private static function onReceiveData(string $nickname, $result) : void{
        $player = Server::getInstance()->getPlayer($nickname);
        if ($player === NULL) return;

        if ($result === NULL || !count($result)) {
            $data = PlayerData::fromTemplate($player->getName());
            (new PlayerDataLoadedEvent($player, $data))->call();
            return;
        }

        $data = new PlayerData(
            new UserData(
                $player->getName()
            ),
            new StatsData(
                (int)$result["kills"],
                (int)$result["recordKillStreak"],
                (int)$result["killStreak"],
                (int)$result["experience"]
            )
        );

        (new PlayerDataLoadedEvent($player, $data))->call();
    }
}