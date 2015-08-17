<?php
/**
 * Created by PhpStorm.
 * User: William
 * Date: 2015/8/14
 * Time: 10:58
 */

namespace Web\Framework;


use Web\Utils\NDebug;
use Web\Utils\WebUtils;

abstract class Router
{

    public static $ControllerInfo;

    public static $TemplatePath;
    public static $TemplateStyle;

    public static function Initialize()
    {

        self::$TemplateStyle = Config::$cfg_templateStyle;

    }


    public static function DoAction()
    {
        self::Initialize();

        try {

            self::$ControllerInfo = self::ParseController('/^\/([a-zA-Z0-9_]+)\/([a-zA-Z0-9]+)$/');

            if (empty(self::$ControllerInfo['name'])
                || empty(self::$ControllerInfo['class'])
            ) {
                WebUtils::JSAlert('错误的调用', WebUtils::REDIRECT_NO_REDIRECT);//TODO 友好提示
            }

            $name = self::$ControllerInfo['name'];
            $class = self::$ControllerInfo['class'];

            $ControllerClass = self::GetControllerClass($name, $class);

            if ($ControllerClass === false) {
                WebUtils::JSAlert('不存在处理方法', WebUtils::REDIRECT_NO_REDIRECT);//TODO 友好提示
                self::ResponseEnd();
            }


            self::$Context = new $ControllerClass();

            self::$Context->Initialize();

            self::$TemplatePath = self::ParseTemplatePath($name, $class);

            if (self::$Context->GetDoActionName() === false) {
                self::$Context->Execute();
            } else {
                self::$Context->ProcessDoAction();
            }

            if (self::$Context->ShowTemplate) {


                if (is_file(self::$TemplatePath)) {
                    include(self::$TemplatePath);
                } else {

                    NDebug::VerDump(self::$TemplatePath);

                    WebUtils::JSAlert('不存在模版', WebUtils::REDIRECT_NO_REDIRECT);

                }
            }

            self::$Context->ResponseEnd();

        } catch (Exception $ex) {
            WebUtils::Alert($ex->getMessage());
        }
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