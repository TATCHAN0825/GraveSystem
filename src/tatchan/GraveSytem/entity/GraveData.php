<?php


namespace tatchan\GraveSytem\entity;


use pocketmine\Player;
use pocketmine\utils\Config;
use tatchan\GraveSytem\Main;

class GraveData {

	private static $instance = null;
	private $entityisplayer,$playerinventory;

	public static function getInstance() {
		return self::$instance;
	}
	public function __construct(Main $plugin) {
		$this->entityisplayer = [];
		$this->playerinventory = [];
		self::$instance = $this;
        $this->config = new Config($plugin->getDataFolder() . "config.yml", Config::YAML, [
                "Displaytheposition" => true,
                "gravegetitem" => false,
            ]
        );
	}
	public function setenetiyplayer(int $entityid, Player $player){
		$this->entityisplayer[$entityid] = $player;
	}
	public function setplayerinventory(int $entityid, array $item){
		$this->playerinventory[$entityid] = $item;
	}
	public function getplayerinventory(int $entityid):array{
		return $this->playerinventory[$entityid];
	}
	public function getentityplayer(int $entityid) : Player{
		return $this->entityisplayer[$entityid];
	}
	public function getconfig(string $key){
	    return $this->config->get($key);
    }
}