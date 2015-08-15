<?php
/**
 * Created by PhpStorm.
 * User: William
 * Date: 2015/8/14
 * Time: 14:34
 */

namespace Web\Utils;


final class NDebug
{

    public static $debug = true;

    public static function VerDump($obj)
    {
        if (self::$debug) var_dump($obj);
    }


}