<?php

namespace de\bluplayz\coins\locale;

use de\bluplayz\coins\Coins;
use pocketmine\utils\Config;

class Locale
{
    /** @var Coins */
    private $plugin;
    /** @var array */
    private $messages = [];

    public function __construct(Coins $plugin)
    {
        $this->plugin = $plugin;
        self::init();
    }

    /**
     * init messages which not exists
     */
    private function init()
    {
        $locale = new Config($this->plugin->getDataFolder() . "messages.yml", Config::YAML);
        $messages = [
            "COINS_GET_MESSAGE" => "§7[§aCoins§7] §7You have §b{coins} §7Coins!",
            "COINS_PLAYER_NOT_FOUND" => "§7[§aCoins§7] §cThe player §b{player} §cwas not found on this server!",
            "COINS_ADD_SUCCESS" => "§7[§aCoins§7] §aYou successfully gave the player §b{player} {coins} §aCoins!",
            "COINS_SET_SUCCESS" => "§7[§aCoins§7] §aYou successfully set the coins from the player §b{player} §ato §b{coins}§a!",
            "COINS_NO_PERMISSIONS" => "§7[§aCoins§7] §cYou don't have the permission to execute this command!",
            "COINS_GET_FROM_OTHER_MESSAGE" => "§7[§aCoins§7] §aYou got §b{coins} §aCoins from §b{player}§a!",
            "COINS_SET_FROM_OTHER_MESSAGE" => "§7[§aCoins§7] §aYour Coins was set to §b{coins} §aby §b{player}§a!"
        ];

        //set default message if key doesnt exists
        foreach ($messages as $key => $value) {
            if (!$locale->exists($key)) {
                $locale->set($key, $value);
                $locale->save();
            }
        }
        $this->messages = $locale->getAll();
    }

    /**
     * @return string
     */
    public function getMessage($key)
    {
        return isset($this->messages[$key]) ? $this->messages[$key] : "error while translating, check your messages.yml file!";
    }
}