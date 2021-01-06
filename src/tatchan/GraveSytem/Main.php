<?php

declare(strict_types=1);

namespace tatchan\GraveSytem;

use muqsit\invmenu\InvMenuHandler;
use pocketmine\entity\Entity;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use tatchan\GraveSytem\entity\Grave;
use tatchan\GraveSytem\entity\GraveData;


class Main extends PluginBase implements Listener
{

    private $Grave;

    private $config;

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        Entity::registerEntity(Grave::class, true, ["Grave"]);
        (new GraveData($this));
        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }
    }

    public function onDeath(PlayerDeathEvent $event) {
        $player = $event->getPlayer();
        if (!$event->getKeepInventory() == true || !$player->getGamemode() == 1) {

            $player->saveNBT();
            $pos = new Vector3($player->getX(), $player->getY() - 0.3, $player->getZ());

            $nbt = Entity::createBaseNBT($player, null, $player->getYaw(), $player->getPitch());
            $nbt->setTag($player->namedtag->getTag("Skin"));
            $Entity = Entity::createEntity("Grave", $player->getLevelNonNull(), $nbt, $player, $event->getDrops());
            GraveData::getInstance()->setenetiyplayer($Entity->getId(), $player);
            GraveData::getInstance()->setplayerinventory($Entity->getId(), $event->getDrops());
            $event->setDrops([Item::get(0, 0, 1)]);
            $Entity->getDataPropertyManager()->setBlockPos(Entity::DATA_PLAYER_BED_POSITION, $pos);
            $Entity->getDataPropertyManager()->setFloat(Grave::DATA_BOUNDING_BOX_HEIGHT, 1);
            $Entity->setGenericFlag(Entity::DATA_FLAG_SLEEPING, true);
            $Entity->setNameTag($player->getDisplayName());
            $Entity->spawnToAll();
            $this->Grave[$player->getId()] = $Entity;
            if (GraveData::getInstance()->getconfig("Displaytheposition") == true) {
                $player->sendMessage("§a{$player->getPosition()}の場所にお墓を生成しました");
            }
        } else {
            $player->sendMessage("§aKeepInventoryかゲームモードがクリエーティブなためお墓の生成に失敗しました");
        }
    }

    public function isGrave(Player $player): bool {
        return isset($this->Grave[$player->getId()]);
    }

    public function unGrave(Player $player) {
        $entity = $this->Grave[$player->getId()];
        $entity->flagForDespawn();
    }

    public function onDisable() {
        foreach ($this->getServer()->getLevels() as $level) {
            foreach ($level->getEntities() as $entity) {
                if ($entity instanceof Grave) {
                    $entity->kill();
                }
            }
        }
    }
}
