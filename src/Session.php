<?php

declare(strict_types=1);

namespace Endermanbugzjfc\BaauSek;

use SOFe\AwaitGenerator\f2c;
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
			yield from $this->std->awaitEvent(
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

	private function loopPlayerToggleSneakEvent() : void {
		while (true) {
			yield from $this->nextPlayerToggleSneakEvent(true);
			// 「醞釀屎意」
			$hatchUp = new PlayerPoopPreExcretEvent();
			$hatchUp->call();

			// Poop whenever $hatchUp->await() cycles until player unseaks.
			while ($this->store->getPlayer()->isSneaking()) {
				[, $event] = yield from Await::race([
					$hatchUp->await(),
					$this->nextPlayerToggleSneakEvent(false)
				]);
				if ($event instanceof PlayerToggleSneakEvent) {
					break;
				}

				$excret = new PlayerPoopExcretEvent();
				$excret->call();
				if (!$excret->isCancelled()) {
					$pos = $excert->getPosition();
					$pos->getWorld()->dropItem($pos, $excret->getItem(), $excret->getMotion());
				}
			}
		}
	}

	private function nextPlayerToggleSneakEvent(bool $sneak) : \Generator {
		yield from $this->std->awaitEvent(
			PlayerToggleSneakEvent::class,
			fn(PlayerToggleSneakEvent $event) => $event->getPlayer() === $this->store->getPlayer() && $event->isSneaking() === $sneak,
			false,
			EventPriority::MONITOR,
			false,
			$this->store->getPlayer()
		);
	}
