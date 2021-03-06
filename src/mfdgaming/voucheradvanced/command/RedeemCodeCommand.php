<?php

namespace mfdgaming\voucheradvanced\command;

use mfdgaming\voucheradvanced\VoucherAdvanced as Main;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class RedeemCodeCommand extends PluginCommand {
	public function __construct(Main $owner) {
		parent::__construct("redeemcode", $owner);
		$this->setDescription("Redeem Code Command");
		$this->setAliases(["redeem"]);
		$this->setPermission("use.voucheradvanced.redeemcode");
		$this->plugin = $owner;
	}
	public function execute(CommandSender $sender, $currentAlias, array $args) {
		if($sender instanceof Player) {
			if (!isset($args[0])) {
				$sender->sendMessage(TextFormat::GOLD . "/redeemcode <code>");
			} else {
				$codes = $this->plugin->codes->get("codes", []);
				$code = $args[0];
				$id = array_search($code, $codes);
				if(array_key_exists("$id", $codes)) {
					unset($codes[$id]);
					$codes = array_values($codes);
					$this->plugin->codes->set("codes", $codes);
					$this->plugin->codes->save();
					$money = $this->plugin->getConfig()->get("money-amount-to-gain");
					$this->plugin->economy->addMoney($sender, $money);
					$cmd = $this->plugin->getConfig()->get("command-after-redeem");
					if($this->plugin->getConfig()->get("command-as-console") == "true") {
						$this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender(), $cmd);
					} else {
						$this->plugin->getServer()->dispatchCommand($sender, $cmd);
					}
					$sender->sendMessage(TextFormat::GREEN . "Successfully redeemed the redeemcode " . $code);
					return true;
				} else {
					$sender->sendMessage(TextFormat::RED . "Invalid redeemcode!");
					return true;
				}
			}
		} else {
			$sender->sendMessage(TextFormat::RED . "You cant execute this command as a console!");
			return true;
		}
	}
}
