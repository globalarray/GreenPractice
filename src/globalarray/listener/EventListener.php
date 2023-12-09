<?php

declare(strict_types=1);

namespace globalarray\listener;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\server\QueryRegenerateEvent;
use globalarray\Practice;
use globalarray\event\data\PlayerDataLoadedEvent;
use globalarray\session\SessionManager;
use globalarray\lobby\Lobby;
use globalarray\util\Utils;
use globalarray\session\tier\Tier;
use function tr;

final class EventListener implements Listener{

    private const NO_DAMAGE_CAUSE = [
        EntityDamageEvent::CAUSE_VOID,
        EntityDamageEvent::CAUSE_FALL
    ];

    private const ALLOW_PVP_MODES = [
        Player::SURVIVAL,
        Player::ADVENTURE
    ];

    public function __construct() {

    }

    public function onQueryRegenerate(QueryRegenerateEvent $event) : void{
        $online = $event->getPlayerCount();
        $event->setPlayerCount($online);
        $event->setMaxPlayerCount($online + 1);
        $event->setMotd("§k§l§7ii§r §l§aGREENWIX §k§l§7ii§r §l§8[§2Practice§8]§r§r");
    }

    public function onEntityDamage(EntityDamageEvent $event) : void{
        if (Lobby::isLobby(($entity = $event->getEntity())->getLevel())) {
            $event->setCancelled();
            return;
        }
        if (in_array($event->getCause(), self::NO_DAMAGE_CAUSE)) {
            $event->setCancelled();
            return;
        }

        if ($event instanceof EntityDamageByEntityEvent) {

            if (!($entity instanceof Player && ($damager = $event->getDamager()) instanceof Player)) {
                $event->setCancelled();
                return;
            }

            $arenaEntity = GameManager::get($entity);
            $arenaDamager = GameManager::get($damager);

            if ($arenaEntity !== NULL && $arenaDamager !== NULL) {

                foreach ([$damager, $entity] as $player) {
                    if (!in_array($player->getGamemode(), self::ALLOW_PVP_MODES)) {
                        $event->setCancelled();
                        return;
                    }
                }

                if ($entity->getHealth() - $event->getFinalDamage() <= 0.3) {
                    $event->setCancelled();
                    $arenaEntity->getArena()->onDeath($entity);
                    return;
                }

                $cancelled = $arenaEntity->getArena()->onDamage($arenaEntity, $arenaDamager);

                if ($cancelled) {
                    $event->setCancelled();
                }
            }
        }

            /*if ($arenaPlayer === NULL || !in_array($entity->getGamemode(), self::ALLOW_PVP_MODES)) {
                $event->setCancelled();
                return;
            }

            $damager = $event->getDamager();

            if (($opponent = $arenaPlayer->getOpponent()) !== NULL) {
                if ($arenaPlayer->getArena()->is1vs1()) {
                    if ($damager !== $opponent->getBasePlayer()) {
                        $event->setCancelled();
                        return;
                    }
                } else {
                    $arenaPlayer->setOpponent($damager);
                }
                if ($entity->getHealth() - $event->getFinalDamage() <= 0.3) {
                    $event->setCancelled();
                    $arenaPlayer->getArena()->onDeath($entity, $damager);
                }
                return;
            }

            $damagerArenaPlayer = GameManager::get(($damager = $event->getDamager()));

            if ($damagerArenaPlayer === NULL || !in_array($damager->getGamemode(), self::ALLOW_PVP_MODES)) {
                $event->setCancelled();
                return;
            }

            if ($arenaPlayer->isInvulnerable()) {
                $damager->sendMessage(tr("%ffa.isinvulnerable%", $damager));
                return;
            }
        }*/
    }

    public function onPlayerChat(PlayerChatEvent $event) : void{
        $session = SessionManager::get($event->getPlayer());
        $ceilLevel = $session->getData()->getStatsData()->getCeilLevel();
        $message = str_replace('{level}', Utils::getXpLevelColor($ceilLevel) . $ceilLevel, $event->getFormat());
        $event->setFormat($message);
    }

    public function onPlayerExhaust(PlayerExhaustEvent $event) : void{
        $event->setCancelled();
    }

    public function onPlayerLogin(PlayerLoginEvent $event) : void{
        Practice::getDataProvider()->requestData($event->getPlayer());
    }

    public function onPlayerJoin(PlayerJoinEvent $event) : void{
        Lobby::addPlayer($player = $event->getPlayer());
        $event->setJoinMessage(null);
        $session = SessionManager::get($player);
        if ($session !== NULL) {
            if (($tier = $session->getTier())->getOrdinal() >= Tier::BLATANT()->getOrdinal()) {
                foreach (Server::getInstance()->getOnlinePlayers() as $target) {
                    $target->sendMessage(tr("%donater.join%", $target, [
                        "name" => $tier->colorName($player->getName())
                    ]));
                }
            }
        }
    }

    public function onPlayerQuit(PlayerQuitEvent $event) : void{
        Practice::getDataProvider()->saveData($player = $event->getPlayer());
        SessionManager::closeSession($player);
        $event->setQuitMessage(null);
    }

    public function onDataLoaded(PlayerDataLoadedEvent $event) : void{
        SessionManager::openSession($event->getPlayer(), $event->getData());
    }
}