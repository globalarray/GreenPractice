<?php

declare(strict_types=1);

namespace globalarray\npc\join;

use globalarray\npc\Joinable;
use pocketmine\Player;
use globalarray\arena\ArenaFactory;
use globalarray\arena\ArenaMode;
use globalarray\npc\NPCBase;
use globalarray\format\Format;
use globalarray\Coordinates;
use Symfony\Component\Filesystem\Path;

final class FFAJoinNPC extends NPCBase implements Joinable{

    public function __construct() {
        parent::__construct(Path::join("join", "ffa.png"), Coordinates::FFA_JOIN_NPC(), true);
    }

    public function onTap(Player $player) : void{
        ArenaFactory::joinTo($player, ArenaMode::BFFA);
    }

    public function getNameTagFor(Player $player) : string{
        return tr("%npc.join.ffa%", $player, [
            "title" => Format::getName(),
        ]);
    }
}