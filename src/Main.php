<?php

declare(strict_types=1);

namespace Peppu\VoidWorld;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\generator\GeneratorManager;

final class Main extends PluginBase {
	use SingletonTrait;

	public function onLoad(): void {
		$this->setInstance($this);
	}

    public function onEnable() : void {
        $this->saveDefaultConfig();
		// ジェネレーターも登録
		GeneratorManager::getInstance()->addGenerator(VoidGenerator::class, "void", fn() => null, true);
			
		// コマンド登録
		$this->getServer()->getCommandMap()->register("voidworld", new VoidWorldCommand);

		// ワールドロード
        $worlds = $this->getConfig()->get("worlds", []);
		foreach ($worlds as $world) {
			$this->getServer()->getWorldManager()->loadWorld($world);
		}
	}
}