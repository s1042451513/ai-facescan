<?php
namespace Wjohnson\AiFacescan\common\facade;


class CurlFacade extends Facade
{
    /**
     * 配置类
     * @return string \Wjohnson\AiFacescan\common\util\Curl
     */
    public function getFacadeClass()
    {
        return '\Wjohnson\AiFacescan\common\util\Curl';
    }
}