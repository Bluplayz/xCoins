<?php

namespace de\bluplayz\coins\listeners;

use de\bluplayz\coins\Coins;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

class PlayerJoinListener implements Listener
{
    /** @var Coins */
    private $plugin;

    public function __construct(Coins $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @param PlayerJoinEvent $event
     */
    public function onJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();

        //init data
        $this->plugin->initData($player);
    }
}