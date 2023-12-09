<?php

declare(strict_types=1);

namespace globalarray\session\tier;

use pocketmine\utils\TextFormat;

/**
 * @method static Tier DEFAULT()
 * @method static Tier BLATANT()
 * @method static Tier FRESHER()
 * @method static Tier PROGRESSED()
 * @method static Tier YOUTUBE()
 * @method static Tier MODERATOR()
 * @method static Tier OWNER()
 */
class Tier {

    private const HIGH_STAFF_ORDINAL = 3;
    private const STAFF_ORDINAL = 2;

    use EnumTrait{
        __construct as Enum___construct;
    }

    protected static function setup() : void{
        self::registerAll(
            new self("default", 0, "", TextFormat::GRAY, TextFormat::GRAY),
            new self("blatant", 1, "practice.tier.blatant", TextFormat::LIGHT_PURPLE, TextFormat::LIGHT_PURPLE),
            new self("fresher", 1, "", TextFormat::GREEN, TextFormat::GREEN),
            new self("progressed", 1, "", TextFormat::GOLD, TextFormat::YELLOW),
            new self("youtube", 1, "practice.tier.youtube", TextFormat::RED, TextFormat::RED),
            new self("moderator", 2, "", TextFormat::DARK_GREEN, TextFormat::DARK_GREEN),
            new self("owner", 3, "", TextFormat::DARK_RED, TextFormat::BOLD . TextFormat::DARK_RED)
        );
    }

    private $ordinal;
    private $permission;
    private $nametagColor;
    private $chatColor;

    public function __construct(string $name, int $ordinal, string $permission, string $nametagColor, string $chatColor) {
        $this->Enum__construct($name);
        $this->ordinal = $ordinal;
        $this->permission = $permission;
        $this->nametagColor = $nametagColor;
        $this->chatColor = $chatColor;
    }

    public function isHighStaff() : bool{
        return $this->ordinal >= self::HIGH_STAFF_ORDINAL;
    }

    public function isStaff() : bool{
        return $this->ordinal >= self::STAFF_ORDINAL;
    }

    public function getPermission() : string{
        return $this->permission;
    }

    public function getOrdinal() : int{
        return $this->ordinal;
    }

    public function getNameTagColor() : string{
        return $this->nametagColor;
    }

    public function getChatColor() : string{
        return $this->chatColor;
    }
}