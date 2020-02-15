<?php

namespace BeyCoder;

use BeyCoder\Auth\AuthManager;
use pocketmine\plugin\PluginBase;

class AuthMain extends PluginBase
{
    /**
     * @var ApiManager $api
     */
    private $api;

    /**
     * @var AuthManager[] $users
     */
    public $users;

    /**
     * @var AuthManager[] $register
     */
    public $register;

    public function onEnable()
    {
        $this->getLogger()->info("Система авторизации включена!");

        $this->api = $this->getServer()->getPluginManager()->getPlugin("cwApiPE");

        if($this->getApi() == null)
        {
            $this->getLogger()->info("Плагин cwApiPE не найден! Установите и попробуйте запустить ещё раз");
            $this->getServer()->getPluginManager()->disablePlugin($this);

        }

        $this->getServer()->getPluginManager()->registerEvents(new JoinEvent($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new ChatEvent($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new SecurityEvent($this), $this);
    }

    /**
     * @return ApiManager
     */
    public function getApi(): ApiManager
    {
        return $this->api;
    }


}