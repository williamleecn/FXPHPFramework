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
     * 网站名称
     */
    const WebName = 'WXDemo';


    /**
     * @var SimpleXMLElement
     */
    public static $Context;

    public static $postObj;



    public static function checkUser()
    {

        if (!self::checkSignature(self::Token)) {
            return false;
        }

        if (isset($_GET["echostr"])) {
            $echoStr = $_GET["echostr"];
            echo $echoStr;
            exit;
        }
        return true;

    }

    public static function checkSignature($token)
    {
        $CheckPass = false;

        if (isset($_GET["signature"]) &&
            isset($_GET["timestamp"]) &&
            isset($_GET["nonce"])
        ) {

            $signature = $_GET["signature"];
            $timestamp = $_GET["timestamp"];
            $nonce = $_GET["nonce"];

            $tmpArr = array($token, $timestamp, $nonce);
            sort($tmpArr, SORT_STRING);
            $tmpStr = implode($tmpArr);
            $tmpStr = sha1($tmpStr);

            if ($tmpStr == $signature) {
                $CheckPass = true;
            } else {

                \Web\Utils\Logger::Error($_SERVER, __LINE__);
                \Web\Utils\Logger::Error($_REQUEST, __LINE__);

                if (isset($_GET['WILLIAMDUMPWXAPIERRORINFO20140404'])) {
                    var_dump($token);
                    var_dump($tmpArr);
                    var_dump($tmpStr);
                    var_dump($signature);
                }

            }

        }

        return $CheckPass;
    }


    /**
     * @return SimpleXMLElement
     */
    public static function GetRequestXMLData()
    {
        if (!isset($GLOBALS["HTTP_RAW_POST_DATA"])) return false;

        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        if (empty($postStr)) return false;

        return @simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
    }


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
