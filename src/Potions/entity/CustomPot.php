<?php

declare(strict_types=1);

namespace Potions\entity;

use pocketmine\entity\projectile\SplashPotion;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\entity\Effect;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\player\Player;

class CustomPot extends SplashPotion {

    protected $gravity = 0.07;
    protected $drag = 0.015;

    public function entityBaseTick(int $tickDiff = 1): bool {
        if ($this->isCollided) {
            $this->flagForDespawn();
        }
        return parent::entityBaseTick($tickDiff);
    }

    protected function onHit(ProjectileHitEvent $event): void {
        $effects = $this->getPotionEffects();
        $hasEffects = !empty($effects);

        if ($hasEffects) {
            if (!$this->willLinger()) {
                foreach ($this->getWorld()->getNearbyEntities($this->getBoundingBox()->expandedCopy(3, 3, 3)) as $nearby) {
                    if ($nearby instanceof Player && $nearby->isAlive()) {
                        foreach ($effects as $effect) {
                            $newEffect = new EffectInstance($effect->getType(), $effect->getDuration(), $effect->getEffectLevel());
                            $nearby->addEffect($newEffect);
                        }
                    }
                }
            }
        }
    }
}
