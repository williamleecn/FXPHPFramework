<?php
require_once(__DIR__ . "/../data/Framework.Config.php");
require_once(__DIR__ . '/../include/Web.Framework/Core.Class.php');


Web\Framework\Core::InitFramework(__DIR__);

/**
 * Created by PhpStorm.
 * User: William
 * Date: 2015/1/20
 * Time: 15:06
 */
final class WebRouter extends \Web\Framework\Router
{


    const Token = 'wxtoken';


    /**
     * @var \Web\Controller\ControllerBase
     */
    public static $Context;

    public static function DoAction()
    {
        try {
            self::$ActionInfo = parent::ParseAction();

            if (empty(self::$ActionInfo['name'])
                || empty(self::$ActionInfo['class'])
            ) {
                \Web\Utils\WebUtils::JSONAlert(400,'错误的调用');
            }

            $name = self::$ActionInfo['name'];
            $class = self::$ActionInfo['class'];

            $ControllerClass = self::GetActionClass($name, $class);

            if ($ControllerClass === false) {
                \Web\Utils\WebUtils::JSONAlert(400,'不存在处理方法');
                self::ResponseEnd();
            }

            parent::Initialize();

            self::$Context = new $ControllerClass();

            self::$Context->Initialize($name, $class);

            self::$TemplatePath = self::ParseTemplatePath($name, $class);

            if (self::$Context->GetDoActionName() === false) {
                self::$Context->Execute();
            } else {
                self::$Context->ProcessDoAction();
            }

            self::$Context->ResponseEnd();

        } catch (Exception $ex) {
            \Web\Utils\WebUtils::Alert($ex->getMessage());
        }
    }


}

WebRouter::DoAction();
