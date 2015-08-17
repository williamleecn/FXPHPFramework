<?php
/**
 * Created by PhpStorm.
 * User: William
 * Date: 2015/8/17
 * Time: 13:26
 */

namespace Web\Framework;


use Web\Utils\Logger;

class WXRouter extends Router
{

    public static $Token = '';

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
                Logger::Error($_SERVER, __LINE__);
                Logger::Error($_REQUEST, __LINE__);

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


} 