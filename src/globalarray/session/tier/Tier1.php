<?php

declare(strict_types=1);

namespace globalarray\session\tier;

use pocketmine\utils\TextFormat;
use GreenAPI\utils\BackedEnumTrait;

enum Tier : string{
    use BackedEnumTrait;

    case DEFAULT = "default";
    case FRESHER = "fresher";
    case BLATANT = "blatant";
    case PROGRESSED = "progressed";
    case ULTIMATIVE = "ultimative";
    case YOUTUBE = "youtube";
    case MODERATOR = "moderator";
    case OWNER = "owner";

    public function isStaff() : bool{
        return in_array($this, [
            self::MODERATOR, self::OWNER
        ]);
    }

    public function isHighStaff() : bool{
        return in_array($this, [
            self::OWNER
        ]);
    }

    public function getPriorityLevel() : int{
        return match ($this) {
            default => 0,
            self::DEFAULT => 0,
            self::BLATANT, self::FRESHER, self::ULTIMATIVE, self::PROGRESSED, self::YOUTUBE => 1,
            self::MODERATOR => 2,
            self::OWNER => 3
        };
    }

    public function colorName(string $name) : string{
        $color = self::getNameTagColor();

        if ($this === self::YOUTUBE) {
            $position = intval(ceil(strlen($name) / 2));
            $splitableName = TextFormat::RED . substr($name, $position);
            return $color . substr($name, 0, $position) . $splitableName;
        }

        return $color . $name;
    }

    public function getNameTagColor() : string{
        return match ($this) {
            self::DEFAULT => TextFormat::GRAY,
            self::FRESHER => TextFormat::GREEN,
            self::MODERATOR => TextFormat::GREEN,
            self::YOUTUBE => TextFormat::RED,
            self::ULTIMATIVE, self::PROGRESSED => TextFormat::GOLD,
            self::OWNER => TextFormat::DARK_RED,
        };
    }

    public function getChatColor() : string{
        return match ($this) {
            default => TextFormat::GRAY,
            self::DEFAULT => TextFormat::GRAY,
            self::FRESHER => TextFormat::WHITE,
            self::YOUTUBE, self::ADMIN => TextFormat::RED,
            self::MODERATOR, self::CREATOR => TextFormat::GREEN,
            self::ULTIMATIVE, self::PROGRESSED => TextFormat::YELLOW,
            self::OWNER => TextFormat::BOLD . TextFormat::RED,
        };
    }
}