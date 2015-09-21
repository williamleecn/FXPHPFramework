<?php

namespace Web\Framework;

/**
 * Created by PhpStorm.
 * User: William
 * Date: 2015/9/8
 * Time: 17:03
 */
abstract class ConfigBase
{

    public static $DB_SafeCheck = false;
    public static $DB_isDebug = true;
    public static $isDebug = true;

    public static $cfg_dataPath = __DIR__;
    public static $ErrorPage = '';
    public static $ShowErrorMsg = true;

    public static function IsDebug($i = 0)
    {
        if ($i == 100 && is_file(__DIR__ . '/Test100')) return true;

        return false;
    }
}