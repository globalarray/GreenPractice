<?php

declare(strict_types=1);

namespace globalarray\util;

use pocketmine\Player;

trait CommonFactoryTrait{

    private static /*array*/ $items = [];

    public static function get(/*string|int|Player*/ $id) /*: mixed*/{
        if ($id instanceof Player) $id = $id->getLowerCaseName();
        return self::$items[$id] ?? null;
    }

    public static function getAll() : array{
        return self::$items;
    }

    public static function register(/*string|int|Player*/ $id, /*mixed*/ $value) : void{
        if ($id instanceof Player) $id = $id->getLowerCaseName();
        self::$items[$id] = $value;
    }

    public static function unregister(/*string|int|Player*/ $id) : void{
        if ($id instanceof Player) $id = $id->getLowerCaseName();
        unset(self::$items[$id]);
    }
}