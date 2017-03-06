<?php

namespace de\bluplayz\coins;

use de\bluplayz\coins\commands\CoinsCommand;
use de\bluplayz\coins\data\MySQL;
use de\bluplayz\coins\listeners\PlayerJoinListener;
use de\bluplayz\coins\locale\Locale;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Coins extends PluginBase
{
    /** @var string */
    public $datatype = "mysql | files";
    /** @var Coins */
    public $instance;
    /** @var MySQL */
    public $mysql;
    /** @var Locale */
    public $locale;

    /** @var array */
    public $mysqldata = [
        "Address" => "localhost",
        "Port" => 3306,
        "Username" => "root",
        "Password" => "12345",
        "Database" => "coins"
    ];

    public function onEnable()
    {
        //startup message
        $this->getLogger()->info("§aLoading xCoins...");

        //save instance
        $this->instance = $this;

        //register events
        self::registerEvents();

        //register commands
        self::registerCommands();

        //init config
        $this->initConfig();

        //init mysql or files
        if (strtolower($this->datatype) == "mysql") {
            $this->mysql = new MySQL($this);
        } else {
            $this->initFolders();
        }

        //init locale
        $this->locale = new Locale($this);

        //success loading message
        $this->getLogger()->info("§axCoins was successfully loaded!");
    }

    /**
     * register events
     */
    private function registerEvents()
    {
        $this->getServer()->getPluginManager()->registerEvents(new PlayerJoinListener($this), $this);
    }

    /**
     * register commands
     */
    private function registerCommands()
    {
        $this->getServer()->getCommandMap()->register("coins_plugin", new CoinsCommand($this));
    }

    /**
     * initialize the config
     */
    private function initConfig()
    {
        @mkdir($this->getDataFolder());
        $this->saveResource("config.yml");
        $config = $this->getConfig();

        $this->datatype = strtolower($config->get("DataType"));
        $this->mysqldata = $config->get("MySQL");
    }

    /**
     * initialize the files
     */
    private function initFolders()
    {
        @mkdir($this->getDataFolder() . "players");

        $array1 = range("a", "z");
        $array2 = range("0", "9");
        $array = array_merge($array1, $array2);

        //create folder for each letter
        foreach ($array as $key) {
            @mkdir($this->getDataFolder() . "players/" . $key);
        }
    }

    /** create data if player joined the game
     *
     * @param Player $player
     */
    public function initData(Player $player)
    {
        switch (strtolower($this->datatype)) {
            case "mysql":
            case "sql":
                $sql = "SELECT * FROM coins WHERE playername='" . strtolower($player->getName()) . "'";
                $data = $this->mysql->query($sql);
                if (empty($data)) {
                    $sql = "
                      INSERT INTO coins 
                      (playername, coins, ip, cid)
                      VALUES('" . strtolower($player->getName()) . "','0','" . $player->getAddress() . "','" . $player->getClientId() . "');
                    ";
                    $this->mysql->update($sql);
                }
                break;
            case "files":
            case "dateien":
                $playerconfig = new Config($this->getDataFolder() . "players/" . strtolower($player->getName(){0}) . "/" . strtolower($player->getName()) . ".yml", Config::YAML);
                if (!$playerconfig->exists("playername")) {
                    $playerconfig->set("playername", strtolower($player->getName()));
                    $playerconfig->save();
                }
                if (!$playerconfig->exists("coins")) {
                    $playerconfig->set("coins", 0);
                    $playerconfig->save();
                }
                if (!$playerconfig->exists("ip")) {
                    $playerconfig->set("ip", $player->getAddress());
                    $playerconfig->save();
                }
                if (!$playerconfig->exists("cid")) {
                    $playerconfig->set("cid", $player->getClientId());
                    $playerconfig->save();
                }
                break;
        }
    }

    /** add coins to the player
     *
     * @param Player $player
     */
    public function addCoins(Player $player, $addedcoins)
    {
        switch (strtolower($this->datatype)) {
            case "mysql":
            case "sql":
                $sql = "SELECT * FROM coins WHERE playername='" . strtolower($player->getName()) . "'";
                $data = $this->mysql->query($sql);
                if (!empty($data)) {
                    $newcoins = $addedcoins + $data[0]["coins"];
                    $sql = "UPDATE coins SET coins='$newcoins' WHERE playername='" . strtolower($player->getName()) . "';";
                    $this->mysql->update($sql);
                }

                break;
            case "files":
            case "dateien":
                $playerconfig = new Config($this->getDataFolder() . "players/" . strtolower($player->getName(){0}) . "/" . strtolower($player->getName()) . ".yml", Config::YAML);
                $coins = $playerconfig->get("coins");
                $newcoins = $coins + $addedcoins;
                $playerconfig->set("coins", $newcoins);
                $playerconfig->save();
                break;
        }
    }

    /** set the coins of the player
     *
     * @param Player $player
     */
    public function setCoins(Player $player, $coins)
    {
        switch (strtolower($this->datatype)) {
            case "mysql":
            case "sql":
                $sql = "UPDATE coins SET coins='$coins' WHERE playername='" . strtolower($player->getName()) . "';";
                $this->mysql->update($sql);

                break;
            case "files":
            case "dateien":
                $playerconfig = new Config($this->getDataFolder() . "players/" . strtolower($player->getName(){0}) . "/" . strtolower($player->getName()) . ".yml", Config::YAML);
                $playerconfig->set("coins", $coins);
                $playerconfig->save();
                break;
        }
    }

    /**
     * @param Player $player
     * @return int
     */
    public function getCoins(Player $player)
    {
        switch (strtolower($this->datatype)) {
            case "mysql":
            case "sql":
                $sql = "SELECT * FROM coins WHERE playername='" . strtolower($player->getName()) . "'";
                $data = $this->mysql->query($sql);
                if (!empty($data)) {
                    return $data[0]["coins"];
                }
                break;
            case "files":
            case "dateien":
                $playerconfig = new Config($this->getDataFolder() . "players/" . strtolower($player->getName(){0}) . "/" . strtolower($player->getName()) . ".yml", Config::YAML);
                return $playerconfig->get("coins");
                break;
        }

        return 0;
    }
}