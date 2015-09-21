<?php
/**
 * Created by JetBrains PhpStorm.
 * User: William
 * Date: 13-7-2
 * Time: 下午6:13
 * To change this template use File | Settings | File Templates.
 */

namespace Web\Utils;


final class Logger
{

    public static $path = '';
    public static $LogPreName = 'Log';

    public static function Fatal($addinfo = '', $obj = null)
    {
        return self::Log('FATAL', debug_backtrace(), $obj, $addinfo);
    }

    public static function Error($addinfo = '', $obj = null)
    {
        return self::Log('ERROR', debug_backtrace(), $obj, $addinfo);
    }

    public static function Warn($addinfo = '', $obj = null)
    {
        return self::Log('WARN', debug_backtrace(), $obj, $addinfo);
    }

    public static function Info($addinfo = '', $obj = null)
    {
        return self::Log('INFO', debug_backtrace(), $obj, $addinfo);
    }

    public static function Debug($addinfo = '', $obj = null)
    {
        return self::Log('DEBUG', debug_backtrace(), $obj, $addinfo);
    }

    public static function Log($type, $stack, $obj, $addinfo)
    {
        $stackarr = [];

        foreach ($stack as $index => $item) {

            if ($index == 0) continue;

            if (!isset($item['class'])) continue;

            if (!isset($item['function'])) continue;


            $stackarr[] = $item['class'] . '->' . $item['function'] . '.' . (isset($item['line']) ? $item['line'] : '0');
        }

        $stackstrr = implode('|', $stackarr);

        $data = StringUtitly::FormatTimestamp(time());
        $data .= "\t";

        $data .= $type;
        $data .= "/";

        $data .= $stackstrr;
        $data .= "\t";

        $data .= $addinfo;
        $data .= "\t";

        $data .= json_encode(var_export($obj, TRUE));

        return self::WriteLog($data);
    }

    public static function WriteLog($data)
    {

        if (!is_dir(self::$path)) {
            @mkdir(self::$path, 0777, true);
        }

        if (!is_dir(self::$path)) return false;

        $tpath = self::$path . DIRECTORY_SEPARATOR . self::$LogPreName . date('_Ymd', time());

        return @file_put_contents($tpath, $data . "\r\n", FILE_APPEND);

    }


}