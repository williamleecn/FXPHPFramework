<?php

namespace Web\Utils;

final class WebUtils
{

    const  REDIRECT_NO_REDIRECT = 0;
    const  REDIRECT_GOBACK = -1;

    const  REDIRECT_TOP_REFRESH = -2;

    public static $HEADER_CHROME_User_Agent = 'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31';

    public static $HTML_FIX_ENCODING_META = '<meta http-equiv="Content-Type" content="text/html;charset=utf-8">';

    public static $HEADER_PLAIN_TEXT = 'content-type: text/plain; charset=utf-8;';
    public static $HEADER_PLAIN_TEXT_GBK = 'content-type: text/plain; charset=GBK;';
    public static $HEADER_XML = 'content-type: text/xml; charset=utf-8;';


    public static function SetDownloadFileHeader($fn)
    {
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Disposition: attachment;filename=$fn");
    }

    public static function ClearClientCache()
    {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
    }

    public static function SetPlainTextHeaderGB()
    {
        header(WebUtils::$HEADER_PLAIN_TEXT_GBK);
    }

    public static function SetXMLHeader()
    {
        header(WebUtils::$HEADER_XML);
    }


    /**
     * URL重定向
     * @param string $url 重定向的URL地址
     * @param integer $time 重定向的等待时间（秒）
     * @param string $msg 重定向前的提示信息
     * @return void
     */
    public static function Redirect($url, $time = 0, $msg = '')
    {
        //多行URL地址支持
        $url = str_replace(array("\n", "\r"), '', $url);
        if (empty($msg))
            $msg = "系统将在{$time}秒之后自动跳转到{$url}！";
        if (!headers_sent()) {
            // redirect
            if (0 === $time) {
                header('Location: ' . $url);
            } else {
                header("refresh:{$time};url={$url}");
                echo($msg);
            }
            exit();
        } else {
            $str = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
            if ($time != 0)
                $str .= $msg;
            exit($str);
        }
    }


    public static function SetPlainTextHeader()
    {
        header(WebUtils::$HEADER_PLAIN_TEXT);
    }

    public static function echoPlianText($html)
    {
        echo '<PRE>' . $html . '</PRE>';
    }


    public static function GetIP()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    public static function GetHost()
    {
        return $_SERVER['HTTP_HOST'];
    }

    public static function GetLastLevelDomain()
    {
        $host = self::GetHost();

        return explode('.', $host)[0];
    }

    /**
     *
     * API JSON 统一输出
     *
     * @param int $Result
     * @param string $ErrInfo
     * @param array $data 必须是数组
     */
    public static function JSONAlert($Result = 0, $msg = '', $data = array())
    {
        header("content-type: application/json; charset=utf-8");

        if ($data == null || !is_array($data)) $data = array();

        $finaldata = array();
        $finaldata['Ret'] = $Result;

        if ($msg != '') {
            $finaldata['Msg'] = $msg;
        }

        $finaldata = array_merge($finaldata, $data);

        echo @json_encode($finaldata);

    }

    public static function JSONCallBackAlert($Result = 0, $msg = '', $data = array(), $callback = '')
    {
        header("content-type: text/plain; charset=utf-8");

        if ($data == null || !is_array($data)) $data = array();

        $finaldata = array();
        $finaldata['Ret'] = $Result;

        if ($msg != '') {
            $finaldata['Msg'] = $msg;
        }

        $finaldata = array_merge($finaldata, $data);

        $data = @json_encode($finaldata);

        echo $callback . '(' . $data . ')';

    }

    public static function JSONCallBackAlert2($Result = 0, $msg = '', $data = array(), $callback = '')
    {

        header("content-type: text/html; charset=utf-8");
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        echo '<script type="text/javascript">';


        if ($data == null || !is_array($data)) $data = array();

        $finaldata = array();
        $finaldata['Ret'] = $Result;

        if ($msg != '') {
            $finaldata['Msg'] = $msg;
        }

        $finaldata = array_merge($finaldata, $data);

        $data = @json_encode($finaldata);

        echo $callback . '(' . $data . ')';

        echo '</script>';
    }

    public static function JSAlert($msg, $redirect = WebUtils::REDIRECT_GOBACK, $isTop = false)
    {

        header("content-type: text/html; charset=utf-8");
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        echo '<script type="text/javascript">';

        if ($msg != '') {
            echo 'alert("' . addslashes($msg) . '");';
        }
        if ($redirect === self::REDIRECT_GOBACK) {
            echo ' window.history.go(-1);';
        } elseif ($redirect === self::REDIRECT_TOP_REFRESH && $isTop) {
            echo 'window.top.location.reload();';
        } elseif ($redirect === self::REDIRECT_NO_REDIRECT) {

        } else {
            if ($isTop) {

                echo 'window.top.location.href="' . $redirect . '";';

            } else {
                echo 'window.location.href="' . $redirect . '";';
            }
        }


        echo '</script>';
    }

    public static function RunMagicQuotes(&$svar)
    {
        if (!get_magic_quotes_gpc()) {
            if (is_array($svar)) {
                foreach ($svar as $_k => $_v) $svar[$_k] = self::RunMagicQuotes($_v);
            } else {
                $svar = addslashes($svar);
            }
        }
        return $svar;
    }


}