<?php
/**
 * Created by PhpStorm.
 * User: William
 * Date: 2015/8/14
 * Time: 10:58
 */

namespace Web\Framework;


use Web\Utils\NDebug;

abstract class Router
{

    public static $ControllerInfo;

    public static $TemplatePath;
    public static $TemplateStyle;

    public static function Initialize()
    {

        self::$TemplateStyle = Config::$cfg_templateStyle;

    }


    public static function ParseController($regx = '/^\/([a-zA-Z0-9_]+)\.([a-zA-Z0-9]+)$/')
    {

        $pinfo = '';

        if (isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO'])) {

            $pinfo = $_SERVER['PATH_INFO'];

        } else {

            if (isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI'])) {

                $pinfo = $_SERVER['REQUEST_URI'];
            }
        }


        if (empty($pinfo)) return NULL;


        $action = substr($pinfo, strpos($pinfo, '/'));

        $macthes = array();
        if (preg_match($regx, $action, $macthes) == false) {
            return null;
        }

        return [
            'name' => $macthes[1],
            'class' => $macthes[2],
        ];
    }


    public static function ParseTemplatePath($name, $class)
    {
        return Core::$WebRoot . DIRECTORY_SEPARATOR . "template" . DIRECTORY_SEPARATOR . self::$TemplateStyle . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . $class . ".tpl.php";
    }


    public static function  ChangeTemplatePath($name, $class)
    {
        self::$TemplatePath = ParseTemplatePath($name, $class);
    }

    public static function  GetControllerClass($name, $class)
    {

        if (empty($name) || empty($class)) return false;

        $sclass = '\\Web\\Controller\\' . $name . '\\' . $class . 'Controller';

        if (!class_exists($sclass)) {
            NDebug::VerDump($sclass);
            return false;
        }

        return $sclass;

    }


    public static function  GetControllerPath($name, $class)
    {
        return "/index.php/$name.$class";
    }

    public static function ResponseEnd()
    {
        exit;
    }

} 