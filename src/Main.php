<?php

declare(strict_types=1);

namespace Peppu\VoidWorld;

use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\world\generator\GeneratorManager;

final class Main extends PluginBase {
	
	public function onEnable() : void {
		GeneratorManager::getInstance()->addGenerator(VoidGenerator::class, "void", fn() => null, true);
		$this->getServer()->getCommandMap()->register("voidworld", new VoidWorldCommand);

		$worldDir = Server::getInstance()->getDataPath() . "/worlds";
        $worlds = scandir($worldDir);
		foreach ($worlds as $world) {
			if (!is_dir($worldDir."/".$world) || $world === "." || $world === "..") continue;
			$this->getServer()->getWorldManager()->loadWorld($world);
		}
	}
}