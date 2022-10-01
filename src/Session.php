<?php

declare(strict_types=1);

namespace Endermanbugzjfc\ExcretionMechanism;

use SOFe\AwaitStd\AwaitStd;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\player\Player;
use pocketmine\utils\Limits;

class Session {

	public function __construct(
		private Player $player,
		private AwaitStd $std
	) {
	}

	private function loopPlayerExhaustEvent() : \Generator {
		while (true) {
			yield from $this->std(
				PlayerExhaustEvent::class,
				fn(PlayerExhaustEvent $event) => $event->getPlayer() === $this->store->getPlayer(),
				false,
				EventPriority::MONITOR,
				false,
				$this->store->getPlayer()
			);

			// Reuse event instance here because of laziness.
			$this->store->uncancel();
			$this->store->call();
			if (!$this->store->isCancelled()) {
				if ($this->store->getPercentage() >= 0.5) {
					// Apply effect unless player reduce the storage by pooping.
					$this->nausea->setDuration(Limits::INT32_MAX); // TODO: test.
				} elseif ($this->store->getPercentage() >= 1) {
					// Kill player because they have not pooped for too long.
					$this->store->getPlayer()->kill(); // TODO: cause.
				}
			}
		}
	}
}