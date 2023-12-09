<?php

declare(strict_types=1);

namespace globalarray\npc;

use pocketmine\Player;
use pocketmine\level\Location;
use pocketmine\network\mcpe\protocol\types\Skin as McpeSkin;
use pocketmine\entity\Skin;
use npc\entity\NPCHuman;
use globalarray\util\Utils;
use Symfony\Component\Filesystem\Path;
use Closure;
use function repeat;
use const globalarray\RESOURCE_PATH;

abstract class NPCBase extends NPCHuman{

    /** @var bool */
    protected $dynamicName = false;

    public function __construct(string $skinPath, Location $location, bool $dynamicName = false) {
        $this->dynamicName = $dynamicName;
        $path = Path::join(RESOURCE_PATH, $skinPath);

        $mcpeSkin = new McpeSkin(
            'Standard_Custom',
            Utils::getSkinFromResources($path)
        );

        $skin = Skin::fromMcpeSkin($mcpeSkin);

        parent::__construct(
            "",
            $mcpeSkin,
            $skin->getBedrockSkin(),
            $location,
            Closure::fromCallable([self::class, 'onTap'])
        );
    }

    public function setRotationRange(int $min, int $max) : void{
        repeat(function() use ($min, $max) : void{
			$this->setRotation(mt_rand($min, $max), 1);
		}, 3 * 20);
    }

    public function isDynamicName() : bool{
        return $this->dynamicName;
    }

    public function getNameTagFor(Player $player) : string{
        return "";
    }

    public function onTap(Player $player) : void{

    }
}