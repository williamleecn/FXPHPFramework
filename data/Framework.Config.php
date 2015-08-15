<?php

namespace Web\Framework;

/**
 * 框架配置 数据库连接信息等
 */
final class Config
{
    public static $cfg_dbhost = '127.0.0.1';
    public static $cfg_dbname = 'fxdemo';
    public static $cfg_dbuser = 'root';
    public static $cfg_dbpwd = '';
    public static $cfg_dbprefix = 'fxd_';
    public static $cfg_db_language = 'utf8';
    public static $cfg_templateStyle = 'DefaultStyle';


    public static $DB_SafeCheck = false;
    public static $DB_isDebug = true;
    public static $isDebug = true;

    public static $cfg_dataPath = __DIR__;

    public static function IsDebug($i = 0)
    {
        return false;
    }

}