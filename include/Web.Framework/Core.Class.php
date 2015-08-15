<?php
namespace Web\Framework;

use Web\Utils\DB;
use Web\Utils\Logger;
use Web\Utils\NDebug;

/**
 * Created by PhpStorm.
 * User: William
 * Date: 13-11-27
 * Time: 下午12:13
 */
final class Core
{

    public static $WebRoot;
    public static $Config;
    public static $FrameworkIncludePath;

    private static function Init($WebRoot)
    {
        error_reporting(E_ALL);

        @date_default_timezone_set('Etc/GMT' . 8 * -1);

        self::$FrameworkIncludePath = dirname(__DIR__);


        self::$WebRoot = $WebRoot;

        spl_autoload_register('Web\Framework\Core::AutoLoader');

    }


    public static function  LoadConfig()
    {

        Logger::$path = Config::$cfg_dataPath . '/Log/';

        DB::$safeCheck = Config::$DB_SafeCheck;
        DB::$isDebug = Config::$DB_isDebug;

        NDebug::$debug = Config::$isDebug;
    }

    public static function OpenMsqlConn()
    {
        DB::Open(
            Config::$cfg_dbhost,
            Config::$cfg_dbuser,
            Config::$cfg_dbpwd,
            Config::$cfg_dbname
        );
    }

    public static function AutoLoader($klass)
    {

        $parts = explode('\\', $klass);

        $path = null;

        if ($parts[0] != 'Web') return;

        $parts = array_slice($parts, 1);

        $cpath = DIRECTORY_SEPARATOR . 'Web.' . implode(DIRECTORY_SEPARATOR, $parts) . '.Class.php';

        $path = Core::$FrameworkIncludePath . $cpath;

        if (!file_exists($path)) {
            $path = self::$WebRoot . DIRECTORY_SEPARATOR . 'include' . $cpath;
        }

        if (!file_exists($path)) {

            NDebug::VerDump($path);
            Logger::Error('ERROR: class not found :' . $klass);
            return;
        }

        return require $path;
    }

    public static function InitFramework($WebRoot)
    {
        self::Init($WebRoot);

        self::LoadConfig();

    }


}