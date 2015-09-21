<?php

namespace Web\Framework;

/**
 * 框架配置 数据库连接信息等
 */
final class Config extends ConfigBase
{
    public static $cfg_dbhost = '127.0.0.1';
    public static $cfg_dbname = 'dbname';
    public static $cfg_dbuser = 'root';
    public static $cfg_dbpwd = '';
    public static $cfg_dbprefix = 't3_';
    public static $cfg_db_language = 'utf8';
    public static $cfg_templateTheme = 'DefaultTheme';

}