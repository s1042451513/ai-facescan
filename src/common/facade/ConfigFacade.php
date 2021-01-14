<?php

namespace Johnson\AiFacescan\common\facade;

class ConfigFacade extends Facade
{
    /**
     * 配置类
     * @return string \app\charserver\provider\ChatConfig
     */
    public function getFacadeClass()
    {
        return "\\Johnson\\AiFacescan\\common\\provider\\Config";
    }
}