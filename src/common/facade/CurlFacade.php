<?php
namespace Johnson\AiFacescan\common\facade;


class CurlFacade extends Facade
{
    /**
     * 配置类
     * @return string \Johnson\AiFacescan\common\util\Curl
     */
    public function getFacadeClass()
    {
        return '\Johnson\AiFacescan\common\util\Curl';
    }
}