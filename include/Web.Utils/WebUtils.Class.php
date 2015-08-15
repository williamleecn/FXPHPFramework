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

    public static function Redirect($url)
    {
        header("Location:$url");
        exit;
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

        echo $callback . '(".' . $data . '.")';

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