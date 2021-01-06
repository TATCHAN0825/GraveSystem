<?php


namespace tatchan\GraveSytem\entity;


use muqsit\invmenu\InvMenu;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\inventory\Inventory;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;

class Grave extends Human
{

    /**
     * @var Player
     */
    private $player;
    /**
     * @var Entity
     */
    private $entity;


    public function __construct(Level $level, CompoundTag $nbt) {
        parent::__construct($level, $nbt);
    }

    public function onUpdate(int $currentTick): bool {
        if ($this->isFlaggedForDespawn()) {
            return false;
        }
        return true;
    }

    public function attack(EntityDamageEvent $event): void {
        if ($event instanceof EntityDamageByEntityEvent) {
            $entity = $event->getEntity();
            $damger = $event->getDamager();
            if ($damger instanceof Player) {
                if ($entity instanceof Grave) {

                        $this->id = $entity->getId();
                        $this->entity = $entity;
                        $name = GraveData::getInstance()->getentityplayer($entity->getId())->getName();
                        $inv = GraveData::getInstance()->getplayerinventory($this->id);
                    if (GraveData::getInstance()->getconfig("gravegetitem") == false) {
                        if($damger->getName() !== $name){
                            $damger->sendMessage("§eほかの人の墓を開けるとかができません");
                            return;
                        }
                    }
                        $menu = InvMenu::create(InvMenu::TYPE_CHEST);
                        $menu->getInventory()->setContents($inv);
                        $menu->setName("{$name}のアイテム");
                        $menu->send($damger);
                        $listener = function (Player $player, Inventory $inventory): void {
                            GraveData::getInstance()->setplayerinventory(
                                $this->id, $inventory->getContents());
                            if (count($inventory->getContents()) == 0) {
                                $this->entity->kill();
                            }
                        };
                        $menu->setInventoryCloseListener($listener);
                }
            }
        }
    }

    public function getGravePlayer(): Player {
        return $this->player;
    }
}