<?php

declare(strict_types=1);

namespace globalarray;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use globalarray\listener\EventListener;
use globalarray\lobby\Lobby;
use globalarray\format\Format;
use globalarray\arena\ArenaFactory;
use globalarray\npc\NPCFactory;
use globalarray\config\ConfigPropertyIds;
use globalarray\data\provider\MysqlProvider;
use globalarray\data\provider\DummyProvider;
use globalarray\data\provider\DataProvider;
use GreenAPI\languages\Languages;
use Symfony\Component\Filesystem\Path;

use function strtolower;
use function is_dir;
use function mkdir;

final class Practice extends PluginBase{

    /** @var bool */
    private static $debug = false;

    /** @var DataProvider */
    private static $dataProvider;

    /** @var Config */
    private $config;

    public function onLoad() : void{
        define("globalarray\RESOURCE_PATH", $resourcePath = Path::join($this->getDataFolder(), 'resources'));

        if (!is_dir($resourcePath)) {
            @mkdir($resourcePath);
        }
        try {
            $this->config = new Config(Path::join(RESOURCE_PATH, "config.yml"), Config::YAML, [
                "provider" => "dummy",
                "debug" => true,
                "mysql" => [
                    "host" => "0.0.0.0",
                    "port" => 3306,
                    "user" => "default",
                    "password" => "password",
                    "database" => "minigames",
                ]
            ]);
    
            self::$debug = (bool)$this->config->get(ConfigPropertyIds::DEBUG);
    
            if (strtolower($this->config->get(ConfigPropertyIds::PROVIDER)) === "mysql") {
                self::$dataProvider = new MysqlProvider($this->config->get(ConfigPropertyIds::MYSQL));
            } else {
                self::$dataProvider = new DummyProvider();
            }

            Format::init();
            Languages::loadFromPath(Path::join(RESOURCE_PATH, "lang"));
        } catch (\Throwable $e) {
            $this->getLogger()->error("An error occurred while loading Practice: " . $e->getMessage());
        }
    }

    public function onEnable() : void{
        Lobby::init($this->getServer()->getDefaultLevel());
        NPCFactory::init();
        ArenaFactory::init();
        $this->registerEvents(new EventListener());
    }

    public static function getDataProvider() : DataProvider{
        return self::$dataProvider;
    }

    /*private static function initCServer() : void{
        CServer::setData(
            "Practice",
            [
                new Game(ModeIds::ID_LOBBY, ModeIds::NAME_LOBBY)
            ],
            function (Player $player) : Game{
                //get player arena
                return CServer::getGame(ModeIds::ID_LOBBY);
            },
            self::DEBUG,
            0x01
        );
    }*/

    public static function isDebug() : bool{
        return self::DEBUG;
    }
}