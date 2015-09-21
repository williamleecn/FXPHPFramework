<?php
/**
 * Created by PhpStorm.
 * User: William
 * Date: 2015/8/21
 * Time: 16:31
 */

namespace Web\Framework;


class ExceptionHandler
{
    public static $isDebug = false;
    public static $OB_Started = false;
    public static $ErrorPage = '';
    public static $ShowErrorMsg = '';
    public static $exceptionFile = '';

    // 致命错误捕获
    static public function fatalError()
    {

        if ($e = error_get_last()) {

            switch ($e['type']) {
                case E_ERROR:
                case E_PARSE:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_USER_ERROR:
                    if (self::$OB_Started) ob_end_clean();
                    self::halt($e);
                    break;
            }
        }
    }


    /**
     * 错误输出
     * @param mixed $error 错误
     * @return void
     */
    static public function halt($error)
    {


        $e = array();
        if (self::$isDebug) {

            if (self::$OB_Started) ob_end_clean();
            //调试模式下输出错误信息
            if (!is_array($error)) {
                $trace = debug_backtrace();
                $e['message'] = $error;
                $e['file'] = $trace[0]['file'];
                $e['line'] = $trace[0]['line'];

                for ($i = 0; $i < count($trace); $i++) {
                    unset($trace[$i]['args']);
                    unset($trace[$i]['object']);
                }

                ob_start();
                print_r($trace);

                $e['trace'] = ob_get_clean();
            } else {
                $e = $error;
            }

        } else {
            //否则定向到错误页面
            if (!empty(self::$ErrorPage)) {
                redirect(self::$ErrorPage);
            } else {
                $message = is_array($error) ? $error['message'] : $error;
                $e['message'] = self::$ShowErrorMsg ? $message : 'Error';
            }
        }
        // 包含异常页面模板

        if (empty(self::$exceptionFile)) return;

        include self::$exceptionFile;

        exit;
    }

    /**
     * 自定义异常处理
     * @access public
     * @param mixed $e 异常对象
     */
    static public function appException($e)
    {
        $error = array();
        $error['message'] = $e->getMessage();
        $trace = $e->getTrace();
        if ('E' == $trace[0]['function']) {
            $error['file'] = $trace[0]['file'];
            $error['line'] = $trace[0]['line'];
        } else {
            $error['file'] = $e->getFile();
            $error['line'] = $e->getLine();
        }
        $error['trace'] = $e->getTraceAsString();

        // 发送404信息
        header('HTTP/1.1 404 Not Found');
        header('Status:404 Not Found');
        self::halt($error);
    }

    /**
     * 自定义错误处理
     * @access public
     * @param int $errno 错误类型
     * @param string $errstr 错误信息
     * @param string $errfile 错误文件
     * @param int $errline 错误行数
     * @return void
     */
    static public function appError($errno, $errstr, $errfile, $errline)
    {
        switch ($errno) {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                if (self::$OB_Started) ob_end_clean();
                $errorStr = "$errstr " . $errfile . " 第 $errline 行.";
                self::halt($errorStr);
                break;
            default:
                $errorStr = "[$errno] $errstr " . $errfile . " 第 $errline 行.";
                self::halt($errorStr);
                break;
        }
    }


}
