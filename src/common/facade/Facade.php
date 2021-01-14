<?php
namespace Johnson\AiFacescan\common\facade;

abstract class Facade
{
    protected static $facades = [];

    abstract function getFacadeClass();

    public static function __callStatic($name, $arguments)
    {
        return static::getFacadeInstance()->{$name}(...$arguments);
    }

    public static function getFacadeInstance()
    {
        $class = '';
        $static = new static();
        $classOrg = $static->getFacadeClass();

        if (empty(static::$facades[$classOrg])) {
            if (class_exists($classOrg)) {
                $class = new $classOrg;
            }

            if (file_exists($classOrg)) {
                require_once($classOrg);
                $startpos = strrpos($classOrg, '/') + 1;
                $className = substr($classOrg, $startpos, strrpos($classOrg, '.') - $startpos);
                $class = new $className();
                if (method_exists($class, 'getInstance')) {
                    $class = $class->getInstance();
                }
            }

            if (empty($class)) {
                return ('have not this class');
            }

            static::$facades[$classOrg] = $class;
        }

        return static::$facades[$classOrg];
    }

}