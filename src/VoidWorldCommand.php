<?php

declare(strict_types=1);

namespace Peppu\VoidWorld;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\WorldCreationOptions;

final class VoidWorldCommand extends Command {
    public function __construct() {
        parent::__construct("voidworld");
        $this->setPermission("voidworld.command");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if (!($sender instanceof Player)) {
            $sender->sendMessage("プレイヤー以外は実行できません");
            return;
        }

        if (!isset($args[0])) {
                $sender->sendMessage("Usage: /voidworld create <name>\nUsage: /voidworld tp <name>\nUsage: /voidworld list");
                return;
        }
        switch ($args[0]) {
        case "create":
            if (!isset($args[1])) {
                $sender->sendMessage("Usage: /voidworld create <name>");
                return;
            }
            $worldOption = new WorldCreationOptions();
            $worldOption->setGeneratorClass(VoidGenerator::class);
            $worldOption->setSpawnPosition(new Vector3(0, 0, 0));
            Server::getInstance()->getWorldManager()->generateWorld($args[1], $worldOption);
            break;
        case "tp":
            if (!isset($args[1])) {
                $sender->sendMessage("Usage: /voidworld tp <name>");
                return;
            }
            
            $world = Server::getInstance()->getWorldManager()->getWorldByName($args[1]);
            if (is_null($world)) {
                $sender->sendMessage("ワールドがない");
                return;
            }
            $sender->teleport($world->getSpawnLocation());
            break;
        case "list":
            $worldDir = Server::getInstance()->getDataPath() . "/worlds";
            $worlds = scandir($worldDir);
            $message = "WorldList:\n";
            foreach ($worlds as $world) {
			    if (!is_dir($worldDir."/".$world) || $world === "." || $world === "..") continue;
                $message .= "$world\n";
            }
            $sender->sendMessage($message);
            break;
        }
    }
}