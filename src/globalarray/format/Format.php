<?php

declare(strict_types=1);

namespace globalarray\format;

use function repeat;
use function str_pad;

final class Format {

	private static $counter = 0;
	private static $count;

	const NAME = [
		"§2Practice§f",

		"§aP§2ractice",
		"§aPr§2actice",
		"§aPra§2ctice",
		"§aPrac§2tice",
		"§aPract§2ice",
		"§aPracti§2ce",
        "§aPractic§2e",

		"§aPractice§f",
	];

    private static $cachedDate = "";

	public static function getName() : string{
		return "§l" . self::NAME[self::$counter];
	}

	public static function init(){
		repeat([self::class, 'updateDate'], 20*60*60);
		repeat([self::class, 'updateCounter'], 21);

		self::$count = count(self::NAME);
	}

	public static function getFormattedDate(string $title) : string{
		return str_pad(self::getCachedDate(), mb_strlen($title), ' ', STR_PAD_BOTH);
	}

	public static function getFormatTime(int $time) : string{
        $mins = floor($time/60);
        $sec = $time-$mins*60;
        if($sec < 10) $sec = '0'.$sec;
        return $mins.':'.$sec;
    }

	public static function updateCounter(): void{
		if(++self::$counter === self::$count){
			self::$counter = 0;
		}
	}

	public static function updateDate(): void{
		self::$cachedDate = "§7".date("j/n/Y");
	}

    public static function getCachedDate(): string{
        return self::$cachedDate;
    }
}