<?php
namespace Johnson\AiFacescan\common\util;

use think\Log;

/**
 * curl请求访问方法
 * Class Curl
 */
class Curl
{

    /**
     * 构造函数
     */
    public function __construct()
    {
    }
    /**
     * 错误提示
     * @var string
     */
    private $error = '';

    /**
     * get访问
     * @param $url
     * @param array $param
     * @param array $headers
     * @return bool|string
     */
    public function get($url, $param = [], $headers = [])
    {
        return $this->curl('get', $url, $param, $headers);
    }

    /**
     * post访问
     * @param $url
     * @param array $param
     * @param array $headers
     * @return bool|string
     */
    public function post($url, $param = [], $headers = [])
    {
        return $this->curl('post', $url, $param, $headers);
    }


    /**
     * 模拟get/post请求的方法
     * Curl constructor.
     * @param string $method
     * @param string $url
     * @param array $params
     * @param array $headers
     */
    public function curl($method = 'get', $url = '', $params = [], $headers = ["flag:ahc"])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // post方法
        $method = strtoupper($method);
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }

        $result = curl_exec($ch);
        if (curl_errno($ch)) {//7.如果出错
            return $this->setError(curl_error($ch));
        }
        curl_close($ch);

        return $result;
    }

    /**
     * 获取错误
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 设置错误
     * @param $error
     * @return boolean
     */
    private function setError($error)
    {
        $this->error = $error;
        return false;
    }
}