<?php
namespace Wjohnson\AiFacescan\common\driver\facescan\yiyuan;

use Wjohnson\AiFacescan\common\driver\contract\Facascan;
use Wjohnson\AiFacescan\common\facade\ConfigFacade;
use Wjohnson\AiFacescan\common\facade\CurlFacade;

class Request implements Facascan
{
    public $error = '';

    private $driverName = 'yiyuan';
    /**
     * 开始扫描
     * @return mixed
     */
    public function scan($param)
    {
        // 验证参数
        if (!$this->checkParam($param)) {
            return false;
        }

        // 构建请求参数
        $requestParam = $this->buildRequestParam($param);

        // 请求接口
        $result = $this->sendRequest($requestParam['url'], $requestParam['bodys'], $requestParam['headers']);

        // 返回结果
        return $result;
    }

    /**
     * 验证参数
     * @return mixed
     */
    public function checkParam($param = [])
    {
        // 验证图片
        if (empty($param['image'])) {
            return $this->setError('图片不能为空');
        }

        // 验证图片传递的是字符串
        if (!is_string($param['image'])) {
            return $this->setError('非法参数');
        }

        return true;
    }

    /**
     * 生成秘钥
     * @return mixed
     */
    public function buildRequestParam($param = [])
    {

        // 初始化参数
        $config = ConfigFacade::getConfig('ai');
        $driverType = $config['facescan']['driver_type'];
        $driverConfig = $config[$driverType];
        $url = $driverConfig['api_base_url'].$driverConfig['facescan_api_url'];
        $client_id = $driverConfig['app_id'];
        $client_secret = $driverConfig['app_secret'];
        $headers = array();
        array_push($headers, "Authorization:Basic " . base64_encode($client_id.":".$client_secret));
        //根据API的要求，定义相对应的Content-Type
        array_push($headers, "Content-Type".":"."application/x-www-form-urlencoded; charset=UTF-8");
        $bodys = 'image='.$param['image'];

        return [
            'url' => $url,
            'headers' => $headers,
            'bodys' => $bodys,
        ];
    }

    /**
     * 发送请求
     * @return mixed
     */
    public function sendRequest($url, &$param = [], &$header = [])
    {
        $result = CurlFacade::post($url, $param, $header);
        return $result;
    }

    /**
     * 获取错误消息
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 设置错误消息
     * @param string $error 错误消息
     * @return boolean
     */
    public function setError($error = '')
    {
        $this->error = $error;
        return false;
    }
}