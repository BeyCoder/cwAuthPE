<?php


namespace BeyCoder;


use BeyCoder\Auth\AuthSaveSystem;
use Exception;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;

class ChatEvent implements Listener
{
    /**
     * @var AuthMain $main
     */
    private $main;

    /**
     * ChatEvent constructor.
     * @param AuthMain $main
     */
    public function __construct(AuthMain $main)
    {
        $this->main = $main;
    }

    public function onChat(PlayerCommandPreprocessEvent $event)
    {
        $message = $event->getMessage();
        $player = $event->getPlayer();

        $message = trim($message);

        if($this->main->users[strtolower($player->getName())]->isLogged() == false) {
            $event->setCancelled(true);

            if($message[0] == "/") return;

            if ($this->main->register[strtolower($player->getName())]) {
                if (strlen($message) < 6 || strlen($message) > 25) {
                    $player->sendMessage($this->main->getApi()->getLangManager("ru")->get("incorrectRegisterPassword"));
                } else {
                    $newUser = new AuthSaveSystem($player, -1, $message, $player->getClientId());
                    $this->main->getApi()->getDatabaseManager()->getDatabaseAuth()->createUser($newUser);
                    $player->sendMessage($this->main->getApi()->getLangManager("ru")->get("successRegisterMessage"));
                    $player->getServer()->getPluginManager()->callEvent(new PlayerAuthEvent($player));

                    $this->main->getApi()->getPrefixManager($player)->setPrefix($this->main->getApi()->getLangManager("ru")->get("defaultPlayerPrefix"));

                    try {
                        $this->main->users[strtolower($player->getName())]->login($message);
                        $this->main->register[strtolower($player->getName())] = false;
                    } catch (Exception $e) {
                        if ($e->getCode() != 404) $this->main->getLogger()->critical("[" . $e->getCode() . "] " . $e->getMessage());
                    }
                }

            }
            else {
                try{
                    if($this->main->users[strtolower($player->getName())]->login($message))
                    {
                        $player->sendMessage($this->main->getApi()->getLangManager("ru")->get("successAuthMessage"));
                        $player->getServer()->getPluginManager()->callEvent(new PlayerAuthEvent($player));

                        $user = new AuthSaveSystem($player, -1, $message, (string)$player->getClientId());
                        $this->main->getApi()->getDatabaseManager()->getDatabaseAuth()->updateCID($user);
                    }else
                    {
                        $player->sendMessage($this->main->getApi()->getLangManager("ru")->get("incorrectPasswordMessage"));
                    }
                } catch (Exception $e) {
                    if ($e->getCode() != 404) $this->main->getLogger()->critical("[" . $e->getCode() . "] " . $e->getMessage());
                }
            }
        }

    }
}