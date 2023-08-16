<?

declare(strict_types=1);

namespace Potions\entity\listener;

use pocketmine\data\bedrock\PotionTypeIdMap;
use pocketmine\data\bedrock\PotionTypeIds;
use pocketmine\entity\Location;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\world\sound\ThrowSound;

class Usage implements Listener
{
    public function onInteract(PlayerInteractEvent $event): void
    {
        $itemInHand = $event->getPlayer()->getInventory()->getItemInHand();
        if ($itemInHand->getMeta() === 22) {
            $this->launchCustomPotion($event->getPlayer());
            $event->cancel();
        }
    }

    protected function launchCustomPotion(Player $player): void
    {
        $location = $player->getLocation();

        $customPotion = new CustomPotionEntity(
            Location::fromObject(
                $player->getEyePos(),
                $location->getWorld(),
                $location->getYaw(),
                $location->getPitch()
            ),
            $player,
            PotionTypeIdMap::getInstance()->fromId(PotionTypeIds::STRONG_HEALING)
        );

        $launchEvent = new ProjectileLaunchEvent($customPotion);
        $launchEvent->call();

        if ($launchEvent->isCancelled()) {
            $customPotion->flagForDespawn();
        }

        $customPotion->spawnToAll();
        $location->getWorld()->addSound($location, new ThrowSound());

        if (!$player->isCreative()) {
            $player->getInventory()->setItemInHand(VanillaItems::AIR());
        }
    }

    public function onItemUse(PlayerItemUseEvent $event): void
    {
        $itemInHand = $event->getPlayer()->getInventory()->getItemInHand();
        if ($itemInHand->getMeta() === 22) {
            $this->launchCustomPotion($event->getPlayer());
            $event->cancel();
        }
    }
}
