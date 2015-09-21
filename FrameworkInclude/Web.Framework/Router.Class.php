<?php
/**
 * Created by PhpStorm.
 * User: William
 * Date: 2015/8/14
 * Time: 10:58
 */

namespace Web\Framework;

use Web\Utils\WebUtils;


abstract class Router
{

    public static $ControllerInfo;

    public static $TemplatePath;
    public static $TemplateTheme;

    public static $category;
    public static $controller;

    private static $OB_Started = false;
    public static $DefaultHome = '';

    const ROUTE_TYPE_QUERY = 'FromQuery';
    const ROUTE_TYPE_PATH = 'FromPath';
    public static $TRoute = self::ROUTE_TYPE_QUERY;

    public static function Initialize()
    {
        ob_start();
        self::$OB_Started = true;
        ExceptionHandler::$OB_Started = self::$OB_Started;

        self::$TemplateTheme = Config::$cfg_templateTheme;

    }

    public static function PreDoAction()
    {
        self::Initialize();

        if (self::$TRoute == self::ROUTE_TYPE_QUERY) {
            self::$ControllerInfo = self::ParseControllerFromQuery();

        } else {
            self::$ControllerInfo = self::ParseControllerFromPath();
        }

        if (empty(self::$ControllerInfo['category'])
            || empty(self::$ControllerInfo['controller'])
        ) {
            if (empty(self::$DefaultHome))
                throw new \Exception('Controller Class Not Found!');

            WebUtils::Redirect(self::$DefaultHome);
        }

        self::$category = self::$ControllerInfo['category'];
        self::$controller = self::$ControllerInfo['controller'];

        $ControllerClass = self::GetControllerClass(self::$category, self::$controller);


        if ($ControllerClass === false) {
            throw new \Exception('Controller Class Not Found:' . self::ParseControllerClass(self::$category, self::$controller));
        }

        return $ControllerClass;

    }

    /**
     * @param ControllerBase $Context
     * @param string $ControllerClass
     * @param $category
     * @param $controller
     * @throws \Exception
     */
    public static function ProcessController(&$Context, $ControllerClass, $category, $controller)
    {
        $Context = new $ControllerClass();


        if (!$Context instanceof IController) {
            throw new \Exception('Controller Class Not Base Of IController:' . self::ParseControllerClass($category, $controller));
        }


        $Context->Initialize();

        self::$TemplatePath = self::ParseTemplatePath($category, $controller);

        if ($Context->GetDoActionName() === false) {
            $Context->Execute();
        } else {
            $Context->ShowTemplate = false;
            $Context->ProcessDoAction();
        }

        if ($Context->ShowTemplate) {

            if (is_file(self::$TemplatePath)) {
                include(self::$TemplatePath);
            } else {

                throw new \Exception('Template Not found:' . self::$TemplatePath);

            }
        }

        $Context->ResponseEnd();
    }


    public static function ParseControllerFromPath($regx = '/^\/([a-zA-Z0-9_]+)\.([a-zA-Z0-9]+)$/')
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


        $path = substr($pinfo, strpos($pinfo, '/'));

        $macthes = array();
        if (preg_match($regx, $path, $macthes) == false) {
            return null;
        }

        return [
            'category' => $macthes[1],
            'controller' => $macthes[2],
        ];
    }

    public static function ParseControllerFromQuery($parm = 'r', $regx = '/^([a-zA-Z0-9_]+)\.([a-zA-Z0-9]+)$/')
    {

        if (!isset($_REQUEST[$parm]) || empty($_REQUEST[$parm])) return null;


        $path = $_REQUEST[$parm];

        $macthes = array();
        if (preg_match($regx, $path, $macthes) == false) {
            return null;
        }
        return [
            'category' => $macthes[1],
            'controller' => $macthes[2],
        ];
    }


    public static function ParseTemplatePath($category, $controller)
    {
        return Core::$WebRoot . DIRECTORY_SEPARATOR . "template" . DIRECTORY_SEPARATOR . self::$TemplateTheme . DIRECTORY_SEPARATOR . $category . DIRECTORY_SEPARATOR . $controller . ".tpl.php";
    }


    public static function  ChangeTemplatePath($category, $controller)
    {
        self::$TemplatePath = self::ParseTemplatePath($category, $controller);
    }

    public static function  GetControllerClass($category, $controller)
    {

        $sclass = self::ParseControllerClass($category, $controller);

        if ($sclass == false || !class_exists($sclass)) {
            return false;
        }

        return $sclass;

    }

    public static function  ParseControllerClass($category, $controller)
    {

        if (empty($category) || empty($controller)) return false;

        $sclass = '\\Web\\Controller\\' . $category . '\\' . $controller . 'Controller';

        return $sclass;

    }


    public static function  GetControllerPath($category, $controller)
    {
        if (self::$TRoute == self::ROUTE_TYPE_QUERY) {
            return "/index.php?r=$category.$controller";
        } else {
            return "/index.php/$category.$controller";
        }
    }

    public static function ResponseEnd()
    {
        if (self::$OB_Started) ob_end_flush();
        exit;
    }

} 