<?php

declare(strict_types=1);

namespace Endermanbugzjfc\BaauSek;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;

class Loader extends PluginBase implements Listener {

	protected function onEnable() : void {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onPlayerJoinEvent(PlayerJoinEvent $event) : void {
		new Session($event->getPlayer());
	}


}