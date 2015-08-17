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
final class WebRouter extends \Web\Framework\WXRouter
{

    /**
     * 网站名称
     */
    const WebName = 'WXDemo';


    /**
     * @var SimpleXMLElement
     */
    public static $Context;



    public static function DoAction()
    {

        self::$postObj = self::GetRequestXMLData();

        if (
            self::$postObj === FALSE ||
            !isset (self::$postObj->FromUserName) ||
            !isset(self::$postObj->ToUserName) ||
            !isset(self::$postObj->CreateTime) ||
            !isset(self::$postObj->MsgType) ||
            \Web\Utils\StringUtitly::IsBlank(self::$postObj->FromUserName) ||
            \Web\Utils\StringUtitly::IsBlank(self::$postObj->ToUserName) ||
            \Web\Utils\StringUtitly::IsBlank(self::$postObj->CreateTime) ||
            \Web\Utils\StringUtitly::IsBlank(self::$postObj->MsgType)
        ) {
            \Web\Utils\MPXML::TextAlertEx(0, 0, "request error");
            exit;
        }

        $ControllerClass = "\\Web\\Controller\\MPMsg\\MType_" . strtoupper(self::$Context->MsgType);

        if (!class_exists($ControllerClass, true)) {

            \Web\Utils\Logger::Error(self::$postObj, "MsgType Not Class Attach");

            \Web\Utils\MPXML::TextAlert(self::$postObj, "MsgType Not Class Attach");
        }

        $openid = self::$postObj->FromUserName;

        self::$Context = new $ControllerClass();

        self::$Context->Initialize();



    }


}

WebRouter::DoAction();
