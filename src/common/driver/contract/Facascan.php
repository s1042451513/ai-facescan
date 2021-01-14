<?php
namespace Wjohnson\AiFacescan\common\driver\contract;

interface Facascan
{
    /**
     * 开始扫描
     * @return mixed
     */
    public function scan($param);

    /**
     * 验证参数
     * @return mixed
     */
    public function checkParam($param);

    /**
     * 生成秘钥
     * @return mixed
     */
    public function buildRequestParam($param);

    /**
     * 发送请求
     * @return mixed
     */
    public function sendRequest($url, &$param, &$header);


}