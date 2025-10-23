<?php

declare(strict_types=1);

namespace Peppu\VoidWorld;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\WorldCreationOptions;

final class VoidWorldCommand extends Command {
    public function __construct() {
        parent::__construct("voidworld");
        $this->setPermission("voidworld.command");
    }

    # /voidworlds create <name>
    # /voidworlds list
    # /voidworlds remove <name>
    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        // 0              1       2
        // ["peppu 000", "apple", 32]

        // voidworld create <name> <- 1
        if (!($sender instanceof Player)) return;

        switch ($args[0]) {
        case "create":
            if (!isset($args[1])) {
                $sender->sendMessage("args[1] is unset");
                return;
            }
            $worldOption = new WorldCreationOptions();
            $worldOption->setGeneratorClass(VoidGenerator::class);
            // worldOption -> generator

            Server::getInstance()->getWorldManager()->generateWorld($args[1], $worldOption);

            $config = Main::getInstance()->getConfig();

            // 既存の設定を取得
            $worlds = $config->get("worlds", []);

            // 新しいワールド名を追加
            $worlds[] = $args[1];

            // 設定を上書き
            $config->set("worlds", $worlds);

            // ファイルに保存
            $config->save();
            break;
        case "remove":
            if (!isset($args[1])) {
                $sender->sendMessage("Usage: /voidworld remove <name>");
                return;
            }

            $worldName = $args[1];
            $config = Main::getInstance()->getConfig();

            // 設定の world list を取得
            $worlds = $config->get("worlds", []);

            if (!in_array($worldName, $worlds)) {
                $sender->sendMessage("§cワールド '{$worldName}' は config に存在しません。");
                return;
            }

            // サーバー上に読み込まれていたらアンロード
            $worldManager = Server::getInstance()->getWorldManager();
            if ($worldManager->isWorldLoaded($worldName)) {
                $world = $worldManager->getWorldByName($worldName);
                $worldManager->unloadWorld($world, true);
            }

            // // フォルダを削除（例: worlds/<name>）
            // めんどいからけさない；

            // config から削除して保存
            $worlds = array_values(array_diff($worlds, [$worldName]));
            $config->set("worlds", $worlds);
            $config->save();
            break;
        case "tp":
            if (!isset($args[1])) {
                $sender->sendMessage("args[1] is unset");
                return;
            }

            $world = Server::getInstance()->getWorldManager()->getWorldByName($args[1]);
            if (is_null($world)) {
                $sender->sendMessage("world is null");
                return;
            }

            $sender->teleport($world->getSpawnLocation());
            break;
        case "list":
            $config = Main::getInstance()->getConfig();
            $worlds = $config->get("worlds", []);
            $message = "WorldList:\n";
            foreach ($worlds as $world) {
                $message .= "$world\n";
            }
            $sender->sendMessage($message);
            break;
        }
    }
}