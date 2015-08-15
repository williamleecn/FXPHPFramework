<?php
namespace Web\Utils;


class StringUtitly
{

    public static function ValidateEmailAddress($email)
    {
        return preg_match('/^[a-z0-9]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $email) == 1;
    }

    public static function ValidateMobileNumber($mobile)
    {
        return preg_match('/^[1][34568]{1}\d{9}$/', $mobile) == 1;
    }

    /**
     * 将日期格式根据以下规律修改为不同显示样式
     * 小于1分钟 则显示多少秒前
     * 小于1小时，显示多少分钟前
     * 一天内，显示多少小时前
     * 3天内，显示前天22:23或昨天:12:23。
     * 超过3天，则显示完整日期。
     * @static
     * @param  $sorce_date 数据源日期 unix时间戳
     * @return void
     */
    public static function getDateStyle($sorce_date)
    {


        $nowTime = time(); //获取今天时间戳

        $timeHtml = ''; //返回文字格式

        $temp_time = 0;

        switch ($sorce_date) {


            //一分钟

            case ($sorce_date + 60) >= $nowTime:

                $temp_time = $nowTime - $sorce_date;

                $timeHtml = $temp_time . "秒前";

                break;


            //小时

            case ($sorce_date + 3600) >= $nowTime:

                $temp_time = date('i', $nowTime - $sorce_date);

                $timeHtml = $temp_time . "分" . date('s', $nowTime - $sorce_date) . "秒前";

                break;


            //天

            case ($sorce_date + 3600 * 24) >= $nowTime:

                $temp_time = intval(($nowTime - $sorce_date) / 3600);

                $timeHtml = $temp_time . '小时前';

                break;


            //昨天

            case ($sorce_date + 3600 * 24 * 2) >= $nowTime:

                $temp_time = date('H:i', $sorce_date);

                $timeHtml = '昨天' . $temp_time;

                break;


            //前天

            case ($sorce_date + 3600 * 24 * 3) >= $nowTime:

                $temp_time = date('H:i', $sorce_date);

                $timeHtml = '前天' . $temp_time;

                break;


            //3天前

            case ($sorce_date + 3600 * 24 * 4) >= $nowTime:

                $timeHtml = '3天前';

                break;


            default:

                $timeHtml = date('Y-m-d', $sorce_date);

                break;


        }

        return $timeHtml;


    }


    public static function SecondsToTime($s, $format = 'is')
    {
        if ('is' == $format) {

            if ($s <= 0) return '0:00';

            if ($s < 60) return '0:' . str_pad($s, 2, '0', STR_PAD_LEFT);


            $str = intval($s / 60) . ':' . str_pad(($s % 60), 2, '0', STR_PAD_LEFT);

            return $str;

        }

    }


    /**
     * 去除HTML标签
     *
     * @param $html
     * @return mixed
     */
    public static function RemoveHtmlTag($html)
    {
        $html = preg_replace('/<.*?>/', "", $html);
        $html = preg_replace('/\t+/', "", $html);
        $html = preg_replace('/ /', "", $html);
        $html = preg_replace('/&nbsp;/', "", $html);
        $html = preg_replace('/\n/', "", $html);
        $html = preg_replace('/\r/', "", $html);
        $html = preg_replace('/<br>/', "", $html);
        return $html;
    }

    public static function TestLoginName($name)
    {
        if (trim($name) == '') {
            return FALSE;
        }
        return preg_match("/^[0-9a-z]+$/", $name) == 1;
    }

    public static function IsBlank($str)
    {
        if (empty($str)) return true;
        if (trim($str) == '') return true;
        return false;

    }

    public static function IsEmptyOrBlank($str)
    {
        if (empty($str)) return true;
        if (trim($str) == '') return true;
        return false;

    }

    public static function FormatMSSQLArrayData($data)
    {

        if (!is_array($data)) return $data;

        $final = array();

        foreach ($data as $i => $val) {

            if (is_array($val)) {
                $final[$i] = self::FormatMSSQLArrayData($val);
                continue;
            }

            if (is_string($val)) {
                $final[$i] = StringUtitly::ConvertGB2UTF8($val);
                continue;
            }

            if ($val instanceof \DateTime) {

                $final[$i] = $val->format('Y-m-d H:i:s');
                continue;
            }

            $final[$i] = $val;

        }

        return $final;


    }

    /**
     * GBK 转 UTF8
     *
     * @param $str
     * @return string
     */
    public static function ConvertGB2UTF8($str)
    {
        return @iconv("GB2312//IGNORE", "UTF-8//IGNORE", $str);
    }

    public
    static function ConvertUTF8TOGBK($str)
    {
        return @iconv("UTF-8//IGNORE", "GB2312//IGNORE", $str);
    }

    public static function cn_substr_utf8($str, $length, $start = 0)
    {

        if (strlen($str) < $start + 1) {
            return '';
        }
        preg_match_all("/./su", $str, $ar);
        $str = '';
        $tstr = '';

        //为了兼容mysql4.1以下版本,与数据库varchar一致,这里使用按字节截取
        for ($i = 0; isset($ar[0][$i]); $i++) {
            if (strlen($tstr) < $start) {
                $tstr .= $ar[0][$i];
            } else {
                if (strlen($str) < $length + strlen($ar[0][$i])) {
                    $str .= $ar[0][$i];
                } else {
                    break;
                }
            }
        }
        return $str;
    }

    public static function ReplaceExcelInputString(&$string)
    {
        return trim(preg_replace('/[\r\n\t]/s', '', $string));
    }

    public static function ReplaceDirtyWords($str)
    {
        $dd = file_get_contents(VDATA . '/dirty.text');

        $arr = explode("\n", $dd);

        foreach ($arr as $word) {
            $str = str_replace($word, '*', $str);
        }

        return $str;

    }

    public static function DelArrayFromKey($arr, $keyarr)
    {

        if (!is_array($keyarr)) {
            unset($arr[$keyarr]);
            //var_dump($arr);
            return $arr;
        }

        foreach ($keyarr as $key) {
            unset($arr[$key]);
        }
        return $arr;
    }

    public static function GetArrayValFromKey($arr, $key)
    {
        $arr2 = [];

        if (!is_array($arr)) {
            return $arr2;
        }

        foreach ($arr as $val) {
            $arr2[] = $val[$key];
        }
        return $arr2;
    }


    public static function FormatTimestamp($date, $formsrt = 'Y-m-d H:i:s', $ZoreRenturnEmpty = false)
    {
        if ($ZoreRenturnEmpty) {
            if (intval($date) == 0) return '';
        }
        if ($formsrt == '') {
            $formsrt = 'Y-m-d H:i:s';
        }

        return date($formsrt, intval($date));
    }

    const DATETIME_AFTER = 1;
    const DATETIME_EQUAL = 0;
    const DATETIME_BEFORE = -1;

    public static function CompareDateTime($dt1, $dt2)
    {

        $dt1 = intval($dt1);
        $dt2 = intval($dt2);

        if ($dt1 == $dt2) return StringUtitly::DATETIME_EQUAL;

        if ($dt1 > $dt2) {
            return StringUtitly::DATETIME_AFTER;
        }
        return StringUtitly::DATETIME_BEFORE;
    }

    public static function isDateTimeInRegion($dt1, $begin, $end)
    {

        $dt1 = intval($dt1);
        $begin = intval($begin);
        $end = intval($end);

        if (\StringUtitly::CompareDateTime($dt1, $end) == \StringUtitly::DATETIME_AFTER) return false;

        if (\StringUtitly::CompareDateTime($dt1, $begin) == \StringUtitly::DATETIME_BEFORE) return false;

        return true;

    }

    public static function StringToTime($date)
    {
        return strtotime($date);
    }

    public static function SubString($str, $begin, $end, $post = 0)
    {

        $p1 = strpos($str, $begin, $post);

        if ($p1 === false) return false;

        $p1 += strlen($begin);

        $p2 = strpos($str, $end, $p1);

        if ($p2 === false) return false;

        return substr($str, $p1, $p2 - $p1);

    }


    public static function ReEscapeString($str)
    {
        return addslashes(stripslashes($str));
    }

    public static function RandomHex($len, $join = '')
    {

        $arr = array();

        for ($i = 0; $i < $len; $i++) {
            $arr[] = str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
        }

        return implode($join, $arr);

    }

    public static function FormatYmd($str)
    {

        if (empty($str) || strlen($str) < 8) return '';
        return substr($str, 0, 4) . '-' . substr($str, 4, 2) . '-' . substr($str, 6, 2);

    }

}

