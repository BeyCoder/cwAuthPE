<?php


namespace BeyCoder;

use pocketmine\event\player\PlayerEvent;
use pocketmine\Player;

class PlayerDeauthEvent extends PlayerEvent
{
    public static $handlerList = null;

    public function __construct(Player $player)
    {
        $this->player = $player;
    }

    /**
     * @param Player $player
     */
    public function setPlayer(Player $player)
    {
        $this->player = $player;
    }
}