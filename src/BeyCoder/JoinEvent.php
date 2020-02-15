<?php


namespace BeyCoder;


use BeyCoder\Auth\AuthManager;
use BeyCoder\Auth\AuthSaveSystem;
use Exception;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;

class JoinEvent implements Listener
{

    /**
     * @var AuthMain $main
     */
    private $main;

    /**
     * JoinEvent constructor.
     * @param AuthMain $main
     */
    public function __construct(AuthMain $main)
    {
        $this->main = $main;
    }

    public function onJoin(PlayerLoginEvent $event)
    {
        $player = $event->getPlayer();

        if(!$this->checkPlayer($player)) return;

        $this->main->users[strtolower($player->getName())] = new AuthManager($this->main->getApi(), $player);
        $this->main->register[strtolower($player->getName())] = false;

        try {
            if ($this->main->users[strtolower($player->getName())]->login("")) {
                $player->sendMessage($this->main->getApi()->getLangManager("ru")->get("successAuthMessage"));
            }else
            {
                $player->sendMessage($this->main->getApi()->getLangManager("ru")->get("authRequiredMessage"));
            }
        } catch (Exception $e) {
            if($e->getCode() == 404)
            {
                $player->sendMessage($this->main->getApi()->getLangManager("ru")->get("registerRequiredMessage"));
                $this->main->register[strtolower($player->getName())] = true;
            }else
            {
                $this->main->getLogger()->critical("[" . $e->getCode() . "] " . $e->getMessage());
            }
        }
    }

    public function onQuit(PlayerQuitEvent $event)
    {
        $player = $event->getPlayer();

        unset($this->main->users[strtolower($player->getName())]);
        unset($this->main->register[strtolower($player->getName())]);
    }

    /**
     * @param Player $player
     *
     * @return bool
     */
    public function checkPlayer(Player $player)
    {
        foreach ($this->main->getServer()->getOnlinePlayers() as $onlinePlayer)
        {
            if(strtolower($player->getName()) == strtolower($onlinePlayer->getName()) && $player->getClientId() != $onlinePlayer->getClientId())
            {
                $player->kick($this->main->getApi()->getLangManager("ru")->get("playerAlreadyOnline"), false);
                return false;
            }
        }

        return true;
    }
}