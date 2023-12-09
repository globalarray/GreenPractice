<?php

declare(strict_types=1);

namespace globalarray\util;

use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use pocketmine\network\mcpe\protocol\types\InputModeIds;

final class Utils{

    public static function getPlatform(Player $player) : string{
        if ($player->isDesktop()) {
            return "PC";
        } else if ($player->getCurrentInputMode() === InputModeIds::CONTROLLER) {
            return "GP";
        } else {
            return "";
        }
        /*return match(true) {
            $player->isDesktop() => "PC",
            $player->getCurrentInputMode() === InputModeIds::CONTROLLER => "GP",
            default => ""
        };*/
    }

    public static function getLevelFromXp(int $xp) : float{
        return floor(ExperienceUtils::getLevelFromXp($xp) / 3) / 3;
    }

    public static function getXpLevelColor(int $level) : string{
        if($level < 10){
            return TF::GRAY;
        }elseif($level < 20){
            return TF::YELLOW;
        }elseif($level < 40){
            return TF::GREEN;
        }elseif($level < 60){
            return TF::AQUA;
        }else{
            return TF::LIGHT_PURPLE;
        }
    }

    /**
	 * @param resource $image
	 * @return string
	 */
	public static function convertImage($image): string{
		$data = "";

		for($y = 0; $y < imagesy($image); ++$y){
			for($x = 0; $x < imagesx($image); ++$x){
				$color = imagecolorsforindex($image, imagecolorat($image, $x, $y));
				$data .= chr($color["red"]) . chr($color["green"]) . chr($color["blue"]) . chr($color["alpha"] === 0 ? 0xff : ~$color["alpha"] << 1 & 0xff);
			}
		}

		return $data;
	}

	/**
	 * @param string $name
	 * @return string
	 */
	public static function getSkinFromResources(string $name): string{
		$image = imagecreatefrompng($name);
		$d = self::convertImage($image);
		imagedestroy($image);
		return $d;
	}
}