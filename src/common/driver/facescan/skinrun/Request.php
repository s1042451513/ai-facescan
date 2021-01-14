<?php
namespace Johnson\AiFacescan\common\driver\facescan\skinrun;

use Johnson\AiFacescan\common\driver\contract\Facascan;
use Johnson\AiFacescan\common\facade\ConfigFacade;
use Johnson\AiFacescan\common\facade\CurlFacade;

class Request implements Facascan
{
    private $error = '';

    private $driverName = 'skinrun';

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

        if (empty($result)) {
            return $this->setError('没有返回值');
        }
        $result = json_decode($result, true);

        if (!in_array($result['code'], ['200', '201'])) {
            return $this->setError($result['message']);
        }
        // 返回结果
        return $result['data'];
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

        // 验证年龄存在
        if (empty($param['age'])) {
            return $this->setError('请填写年龄');
        }

        // 验证年龄是数字
        if (!is_int($param['age'])) {
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
        $faceScanApi = $driverConfig['api_base_url'].$driverConfig['facescan_api_url'];
        $client_token = $driverConfig['app_token'];
        $client_secret = $driverConfig['app_secret'];

        // 毫秒级时间戳
        $t = explode(" ", microtime());
        $time = explode(',', round(round($t[1].substr($t[0], 2, 3))))[0];

        $headers = array();
        array_push($headers, "token".":".$client_token);
        array_push($headers, "version".":"."3.0");
        array_push($headers, "timestamp".":".$time);
        array_push($headers, "sign".":".(md5($time.$client_secret)));

        // 初始化体内容
        $bodys = [
            'image'  => $param['image'],
            'age'    => $param['age'],
        ];

        return [
            'url' => $faceScanApi,
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