<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 刘志淳 <chun@engineer.com>
// +----------------------------------------------------------------------

namespace Johnson\AiFacescan\console\command;

use think\App;
use think\Config;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;
use think\Db;

abstract class Install extends Command
{

    protected $type = "Facescan";

    protected function configure()
    {
        parent::configure();
        $this->setName('aifacescan:install')
            ->addArgument('name', Argument::REQUIRED, "The name of the class")
            ->addOption('plain', null, Option::VALUE_NONE, 'Generate an empty controller class.')
            ->setDescription('Create a new resource controller class');
    }

    protected function execute(Input $input, Output $output)
    {
        // 迁移数据库
        $this->migrateTable();
        // 迁移api文件

        // 迁移后台文件

    }

    /**
     * 迁移数据库
     * @return int
     */
    protected function migrateTable()
    {
        $tablePrefix = Config::get('database.prefix');
        $sql = "
            CREATE TABLE `{$tablePrefix}user_facescan` (
                `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
                `u_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户ID',
                `imgmd5` VARCHAR(32) NOT NULL DEFAULT '' COMMENT 'md5图片检索字符' COLLATE 'utf8_unicode_ci',
                `img` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '图片名url' COLLATE 'utf8_unicode_ci',
                `age` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '肤龄',
                `toily` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'T区油分',
                `uoily` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'U区油分',
                `pockmark` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '痘痘分',
                `spot` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '斑点分',
                `wrinkle` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '皱纹分',
                `blackhead` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '黑头得分',
                `pore` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '毛孔分',
                `sensitive` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '敏感性分',
                `dark_circle` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '黑眼圈分',
                `appearance` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '颜值分',
                `question` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '问题' COLLATE 'utf8_unicode_ci',
                `advise` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '建议' COLLATE 'utf8_unicode_ci',
                `create_time` INT(10) UNSIGNED NOT NULL COMMENT '创建时间',
                `update_time` INT(10) UNSIGNED NOT NULL COMMENT '更新时间',
                PRIMARY KEY (`id`),
                INDEX `user_id_img` (`u_id`, `imgmd5`)
            )
            COMMENT='肌肤分析表'
            COLLATE='utf8_unicode_ci'
            ENGINE=MyISAM
            ;
        ";
        return Db::execute($sql);
    }

    /**
     * 迁移api文件
     */
    protected function migrateApi()
    {

    }

    /**
     * 迁移总后台文件
     */
    protected function migrateAdmin()
    {

    }

    protected function buildClass($name)
    {
        $stub = file_get_contents($this->getStub());

        $namespace = trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');

        $class = str_replace($namespace . '\\', '', $name);

        return str_replace(['{%className%}', '{%namespace%}', '{%app_namespace%}'], [
            $class,
            $namespace,
            App::$namespace,
        ], $stub);

    }

    protected function getPathName($name)
    {
        $name = str_replace(App::$namespace . '\\', '', $name);

        return APP_PATH . str_replace('\\', '/', $name) . '.php';
    }

    protected function getClassName($name)
    {
        $appNamespace = App::$namespace;

        if (strpos($name, $appNamespace . '\\') === 0) {
            return $name;
        }

        if (Config::get('app_multi_module')) {
            if (strpos($name, '/')) {
                list($module, $name) = explode('/', $name, 2);
            } else {
                $module = 'common';
            }
        } else {
            $module = null;
        }

        if (strpos($name, '/') !== false) {
            $name = str_replace('/', '\\', $name);
        }

        return $this->getNamespace($appNamespace, $module) . '\\' . $name;
    }

    protected function getNamespace($appNamespace, $module)
    {
        return $module ? ($appNamespace . '\\' . $module) : $appNamespace;
    }

}
