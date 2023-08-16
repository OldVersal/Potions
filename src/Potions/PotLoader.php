<?php

namespace Potions;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Server;

use Potions\entity\listener\Usage;
use Potions\entity\CustomPot;

class PotLoader extends PluginBase implements Listener {

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents(new Usage(), $this);
        $this->register();
    }

    public function register(): void {
        $entityMap = $this->getServer()->getEntityFactory();
        $entityMap->register(CustomPot::class, true);
    }
}
