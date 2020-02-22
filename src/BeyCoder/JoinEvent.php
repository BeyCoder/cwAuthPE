<?php


namespace BeyCoder;


use BeyCoder\Auth\AuthManager;
use BeyCoder\Auth\AuthSaveSystem;
use Exception;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
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

    /**
     * @param PlayerLoginEvent $event
     */
    public function onJoin(PlayerLoginEvent $event)
    {
        $player = $event->getPlayer();

        if(!$this->checkPlayer($player)) return;

        $this->main->users[strtolower($player->getName())] = new AuthManager($this->main->getApi(), $player);
        $this->main->register[strtolower($player->getName())] = false;

        try {
            if ($this->main->users[strtolower($player->getName())]->login("")) {
                $player->sendMessage($this->main->getApi()->getLangManager("ru")->get("successAuthMessage"));
                $player->getServer()->getPluginManager()->callEvent(new PlayerAuthEvent($player));
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

    public function onLogin(PlayerAuthEvent $event)
    {
        $prefix = $this->main->getApi()->getPrefixManager($event->getPlayer())->getPrefix();

        $event->getPlayer()->setNameTag($event->getPlayer()->getDisplayName());

        if($prefix == "§r§fИгрок§r") return;
        $this->main->getServer()->broadcastTitle("\n\n", "§a§l+ §r§8(" . $prefix ."§8) §r§o§b" . $event->getPlayer()->getName());
    }

    public function onLogout(PlayerDeauthEvent $event)
    {
        $prefix = $this->main->getApi()->getPrefixManager($event->getPlayer())->getPrefix();

        if ($prefix == "§r§fИгрок§r") return;
        $this->main->getServer()->broadcastTitle("\n\n", "§c§l- §r§8(" . $prefix . "§8) §r§o§b" . $event->getPlayer()->getName());
    }

        /**
     * @param PlayerJoinEvent $event
     */
    public function onJoinToServer(PlayerJoinEvent $event)
    {
        $event->setJoinMessage(false);
        $event->getPlayer()->setDisplayName($event->getPlayer()->getDisplayName());
    }

    /**
     * @param PlayerQuitEvent $event
     */
    public function onQuit(PlayerQuitEvent $event)
    {
        $player = $event->getPlayer();
        $event->setQuitMessage(false);

        if($this->main->users[strtolower($player->getName())]->isLogged() == true) $this->main->getServer()->getPluginManager()->callEvent(new PlayerDeauthEvent($player));

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
            if(strtolower($player->getName()) == strtolower($onlinePlayer->getName()))
            {
                //$player->kick($this->main->getApi()->getLangManager("ru")->get("playerAlreadyOnline"), false);
                //return false;
            }
        }

        return true;
    }
}