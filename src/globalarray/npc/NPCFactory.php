<?php

declare(strict_types=1);

namespace globalarray\npc;

use globalarray\util\CommonFactoryTrait;
use globalarray\npc\join\FFAJoinNPC;

final class NPCFactory{
    use CommonFactoryTrait;

    public static function init() : void{
        $id = 0;
        self::register($id++, new FFAJoinNPC());
    }
}