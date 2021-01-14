<?php
namespace Johnson\AiFacescan\common\provider;

class Config
{
    private $root = __DIR__.'/../../';
    private $configDir = 'config';
    private $defaultConfigName = 'config.php';
    private $config = [];
    private static $instance = null;


    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * 初始化变量内容
     */
    protected function init()
    {
        // 初始化默认配置
        $this->loadDefaultConfig($this->root.$this->defaultConfigName, $this->config);
        // 初始化配置
        $this->loadConfig($this->root.$this->configDir, $this->config);
    }

    /**
     * 设置配置
     */
    public function setConfig($name = "", $value = "")
    {
        if (empty($name)) {
            return "";
        }

        $nameArr = explode('.', $name);
        $config = &$this->config;
        foreach($nameArr as $conname) {
            if (empty($config[$conname])) {
                $config[$conname] = '';
            }
            $config = &$config[$conname];
        }

        return $config = $value;
    }

    /**
     * 获取配置
     */
    public function getConfig($name = "")
    {
        if (empty($name)) {
            return $this->config;
        }

        $nameArr = explode('.', $name);
        $config = $this->config;
        foreach ($nameArr as $conname) {
            if (empty($config[$conname])) {
                return false;
            }
            $config = $config[$conname];
        }
        return $config;
    }

    /**
     * 获取单例
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * 加载配置文件
     */
    protected function loadConfig ($dirname = '', &$config = [])
    {
        $dir = opendir($dirname);
        while($filename = readdir($dir)) {
            if ($filename == '.' || $filename == '..') {
                continue;
            }

            $filelink = $dirname.'/'.$filename;
            if (is_dir($filelink)) {
                $this->loadConfig($filelink, $config);
            }else {
                $conNmae = substr($filename, 0, strrpos($filename, '.'));
                $config[$conNmae] = include($filelink);
            }
        }
    }

    /**
     * 加载默认配置文件
     */
    protected function loadDefaultConfig ($configFile = '', &$config = [])
    {
        $config = include($configFile);
    }
}