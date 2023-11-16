<?php

namespace Terpz710\SoupPvP;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\item\MushroomStew;
use pocketmine\plugin\PluginBase;
use pocketmine\player\Player;

class Main extends PluginBase implements Listener {

    /** @var array */
    private $allowedWorlds = [];

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        $this->saveDefaultConfig();
        $this->allowedWorlds = $this->getConfig()->get("allowedWorlds", []);
    }

    public function onPlayerInteract(PlayerInteractEvent $event) {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if ($event->getAction() === PlayerInteractEvent::RIGHT_CLICK_BLOCK && $item instanceof MushroomStew) {
            $event->cancel();

            if ($this->isPlayerInAllowedWorld($player)) {
                if ($player->getHealth() < $player->getMaxHealth()) {
                    $healAmount = min(4, $player->getMaxHealth() - $player->getHealth());
                    $player->setHealth($player->getHealth() + $healAmount);
                    $player->sendMessage("Healed §b$healAmount §rtotal hearts!");
                    $player->getInventory()->removeItem($item);
                } else {
                    $player->sendMessage("You have §bfull health§r already!");
                }
            }
        }
    }

    public function onPlayerItemConsume(PlayerItemConsumeEvent $event) {
        $player = $event->getPlayer();
        $item = $event->getItem();

        if ($item instanceof MushroomStew && !$this->isPlayerInAllowedWorld($player)) 
            $event->cancel();
        }

    private function isPlayerInAllowedWorld(Player $player): bool {
        return in_array($player->getWorld()->getName(), $this->allowedWorlds, true);
    }
}
