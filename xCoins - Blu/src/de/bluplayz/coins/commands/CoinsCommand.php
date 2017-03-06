<?php

namespace de\bluplayz\coins\commands;

use de\bluplayz\coins\Coins;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class CoinsCommand extends Command
{
    /** @var Coins */
    private $plugin;

    public function __construct(Coins $plugin)
    {
        $this->plugin = $plugin;
        parent::__construct("Coins", "coins description", "§eUsage: /coins | /setcoins | /addcoins", ["addcoins", "setcoins"]);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param string[] $args
     *
     * @return mixed
     */
    public function execute(CommandSender $sender, $label, array $args)
    {
        switch (strtolower($label)) {
            case "addcoins":
                if(!$sender->hasPermission("coins.add")){
                    $sender->sendMessage($this->plugin->locale->getMessage("COINS_NO_PERMISSIONS"));
                    return;
                }
                if (isset($args[0]) && isset($args[1])) {
                    // /addcoins <player> <coins>
                    $playername = $args[0];
                    $coins = (int) $args[1];

                    if($this->plugin->getServer()->getPlayer($playername) != null){
                        $player = $this->plugin->getServer()->getPlayer($playername);
                        $this->plugin->addCoins($player, $coins);

                        //send message to target
                        $message = $this->plugin->locale->getMessage("COINS_GET_FROM_OTHER_MESSAGE");
                        $message = str_replace("{player}", $sender->getName(), $message);
                        $message = str_replace("{coins}", $coins, $message);
                        $player->sendMessage($message);

                        //success message
                        $message = $this->plugin->locale->getMessage("COINS_ADD_SUCCESS");
                        $message = str_replace("{player}", $player->getName(), $message);
                        $message = str_replace("{coins}", $coins, $message);
                        $sender->sendMessage($message);
                        return;
                    } else {
                        //player not online or not exists
                        $sender->sendMessage(str_replace("{player}", $playername, $this->plugin->locale->getMessage("COINS_PLAYER_NOT_FOUND")));
                        return;
                    }

                }
                if (isset($args[0]) && !isset($args[1])) {
                    // /addcoins <coins>
                    if (!$sender instanceof Player) {
                        return;
                    }
                    $player = $sender;
                    $coins = (int)$args[0];

                    $this->plugin->addCoins($player, $coins);
                    //success message
                    $message = $this->plugin->locale->getMessage("COINS_ADD_SUCCESS");
                    $message = str_replace("{player}", $player->getName(), $message);
                    $message = str_replace("{coins}", $coins, $message);
                    $sender->sendMessage($message);
                    return;
                }
                //send usage
                $sender->sendMessage("§eUsage: /addcoins <player> <coins> | /addcoins <coins>");
                break;
            case "setcoins":
                if(!$sender->hasPermission("coins.set")){
                    $sender->sendMessage($this->plugin->locale->getMessage("COINS_NO_PERMISSIONS"));
                    return;
                }
                if (isset($args[0]) && isset($args[1])) {
                    // /setcoins <player> <coins>
                    $playername = $args[0];
                    $coins = (int) $args[1];

                    if($this->plugin->getServer()->getPlayer($playername) != null){
                        $player = $this->plugin->getServer()->getPlayer($playername);
                        $this->plugin->setCoins($player, $coins);

                        //send message to target
                        $message = $this->plugin->locale->getMessage("COINS_SET_FROM_OTHER_MESSAGE");
                        $message = str_replace("{player}", $sender->getName(), $message);
                        $message = str_replace("{coins}", $coins, $message);
                        $player->sendMessage($message);

                        //success message
                        $message = $this->plugin->locale->getMessage("COINS_ADD_SUCCESS");
                        $message = str_replace("{player}", $player->getName(), $message);
                        $message = str_replace("{coins}", $coins, $message);
                        $sender->sendMessage($message);
                        return;
                    } else {
                        //player not online or not exists
                        $sender->sendMessage(str_replace("{player}", $playername, $this->plugin->locale->getMessage("COINS_PLAYER_NOT_FOUND")));
                        return;
                    }

                }
                if (isset($args[0]) && !isset($args[1])) {
                    // /setcoins <coins>
                    if (!$sender instanceof Player) {
                        return;
                    }
                    $player = $sender;
                    $coins = (int)$args[0];

                    $this->plugin->setCoins($player, $coins);
                    //success message
                    $message = $this->plugin->locale->getMessage("COINS_SET_SUCCESS");
                    $message = str_replace("{player}", $player->getName(), $message);
                    $message = str_replace("{coins}", $coins, $message);
                    $sender->sendMessage($message);
                    return;
                }
                //send usage
                $sender->sendMessage("§eUsage: /setcoins <player> <coins> | /setcoins <coins>");
                break;
            case "coins":
                if (!$sender instanceof Player) {
                    return;
                }
                $player = $sender;

                $coins = $this->plugin->getCoins($player);
                $player->sendMessage(str_replace("{coins}", $coins, $this->plugin->locale->getMessage("COINS_GET_MESSAGE")));
                break;
        }
    }
}