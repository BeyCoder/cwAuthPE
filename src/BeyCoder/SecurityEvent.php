<?php


namespace BeyCoder;


use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerMoveEvent;

class SecurityEvent implements Listener
{
    /**
     * @var AuthMain $main
     */
    private $main;

    /**
     * SecurityEvent constructor.
     * @param AuthMain $main
     */
    public function __construct(AuthMain $main)
    {
        $this->main = $main;
    }

    public function onMove(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();

        if(!$this->main->users[strtolower($player->getName())]->isLogged())
        {
            $event->setCancelled(true);
        }
    }

    public function onCommand(PlayerCommandPreprocessEvent $event)
    {
        $player = $event->getPlayer();

        if(!$this->main->users[strtolower($player->getName())]->isLogged())
        {
            $event->setCancelled(true);
        }
    }

    public function onInteract(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();

        if(!$this->main->users[strtolower($player->getName())]->isLogged())
        {
            $event->setCancelled(true);
        }
    }

    public function onItemConsume(PlayerItemConsumeEvent $event)
    {
        $player = $event->getPlayer();

        if(!$this->main->users[strtolower($player->getName())]->isLogged())
        {
            $event->setCancelled(true);
        }
    }

    public function onBreak(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();

        if(!$this->main->users[strtolower($player->getName())]->isLogged())
        {
            $event->setCancelled(true);
        }
    }

    public function onPlace(BlockPlaceEvent $event)
    {
        $player = $event->getPlayer();

        if (!$this->main->users[strtolower($player->getName())]->isLogged()) {
            $event->setCancelled(true);
        }
    }

}