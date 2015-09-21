<?php
namespace Web\Framework;

use Web\Utils\DB;
use Web\Utils\Logger;
use Web\Utils\NDebug;

/**
 * Created by PhpStorm.
 * User: William
 * Date: 13-11-27
 * Time: 12:13
 */
class CoreBase
{

    public static $WebRoot;
    public static $WebName;

    public static $Config;
    public static $FrameworkIncludePath;

    public static $ClassLoader = 'Web\Framework\CoreBase::AutoLoader';

    public static $shutdown_function = 'Web\Framework\ExceptionHandler::fatalError';
    public static $error_function = 'Web\Framework\ExceptionHandler::appError';
    public static $exception_function = 'Web\Framework\ExceptionHandler::appException';

    public static function Init($WebRoot)
    {
        error_reporting(E_ALL);


        self::$FrameworkIncludePath = dirname(__DIR__);

        self::$WebRoot = $WebRoot;

        self::spl_autoload_register();

    }

    public static function setLocalTimezone($zone = -8)
    {
        @date_default_timezone_set('Etc/GMT' . $zone);
    }

    public static function spl_autoload_register()
    {
        spl_autoload_register(self::$ClassLoader);
    }

    public static function set_error_exception_handler()
    {
        ExceptionHandler::$isDebug = Config::$isDebug;
        ExceptionHandler::$ErrorPage = Config::$ErrorPage;
        ExceptionHandler::$ShowErrorMsg = Config::$ShowErrorMsg;
        ExceptionHandler::$exceptionFile = Config::$cfg_dataPath . '/Assert/think_exception.tpl.php';


        register_shutdown_function(self::$shutdown_function);
        set_error_handler(self::$error_function);
        set_exception_handler(self::$exception_function);
    }

    public static function  LoadConfig()
    {

        Logger::$path = Config::$cfg_dataPath . DIRECTORY_SEPARATOR . 'Log';

        Logger::$LogPreName = 'Log' . self::$WebName;

        DB::$safeCheck = Config::$DB_SafeCheck;
        DB::$isDebug = Config::$DB_isDebug;

        NDebug::$debug = Config::$isDebug;


        self::set_error_exception_handler();
    }

    public static function OpenMsqlConn()
    {
        return DB::Open(
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

        $path = self::$WebRoot . DIRECTORY_SEPARATOR . 'include' . $cpath;

        //Load the class on root of current web first
        if (file_exists($path)) {
            return require $path;
        }

        //load the class on framework dir
        $path = Core::$FrameworkIncludePath . $cpath;

        if (!file_exists($path)) {
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