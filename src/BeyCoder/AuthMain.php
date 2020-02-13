<?php

namespace BeyCoder;

use pocketmine\plugin\PluginBase;

class AuthMain extends PluginBase
{
    /**
     * @var ApiManager $api
     */
    private $api;

    public function onEnable()
    {
        $this->getLogger()->info("Система авторизации включена!");

        $this->api = $this->getServer()->getPluginManager()->getPlugin("cwApiPE");
        if($this->getApi() == null)
        {
            $this->getLogger()->info("Плагин cwApiPE не найден! Установите и попробуйте запустить ещё раз");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }
    }

    /**
     * @return ApiManager
     */
    public function getApi(): ApiManager
    {
        return $this->api;
    }
}