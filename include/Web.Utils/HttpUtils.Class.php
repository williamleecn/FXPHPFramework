<?php
/**
 * Created by PhpStorm.
 * User: William
 * Date: 2015/8/14
 * Time: 16:59
 */

namespace Web\Utils;


class HttpUtils
{

    /**
     * @var array header 头部
     */
    public $header = array();

    public $cookie = null;
    public $timeout = 30;

    /**
     * @var null 是否包含HTTP头部信息
     */
    public $hasheader = false;
    /**
     * @var bool 是否不包含HTTP内容
     */
    public $nobody = false;

    /**
     * @var bool 是否抓取跳转后的页面
     */
    public $isflow = true;

    public function HttpGet($url)
    {
        try {

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);

            if (!empty($this->cookie) && is_array($this->cookie)) {

                $scookie = '';
                foreach ($this->cookie as $key => $val) {
                    $scookie .= $key . '=' . urlencode($val) . '; ';
                }
                curl_setopt($ch, CURLOPT_COOKIE, $scookie);

            } elseif (is_string($this->cookie)) {
                curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
            }

            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_HEADER, $this->hasheader);
            curl_setopt($ch, CURLOPT_NOBODY, $this->nobody);
            curl_setopt($ch, CURLOPT_ENCODING, "gzip");
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $this->isflow);
            ob_start();
            curl_exec($ch);
            $contents = ob_get_contents();
            ob_end_clean();
            curl_close($ch);
            return $contents;

        } catch (Exception $ex) {
            return $ex->getMessage();
        }
        return null;
    }

    public function HttpPost($url, $fields)
    {
        try {

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);

            curl_setopt($ch, CURLOPT_POST, 1); //post提交方式

            $curlPost = '';

            foreach ($fields as $name => $val) {

                $curlPost .= urlencode($name) . '=' . urlencode($val) . '&';
            }

            curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);


            if (!empty($this->cookie) && is_array($this->cookie)) {

                $scookie = '';
                foreach ($this->cookie as $key => $val) {
                    $scookie .= $key . '=' . urlencode($val) . '; ';
                }
                curl_setopt($ch, CURLOPT_COOKIE, $scookie);

            } elseif (is_string($this->cookie)) {
                curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
            }

            curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_HEADER, $this->hasheader);
            curl_setopt($ch, CURLOPT_NOBODY, $this->nobody);
            curl_setopt($ch, CURLOPT_ENCODING, "gzip");
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $this->isflow);
            ob_start();
            curl_exec($ch);
            $contents = ob_get_contents();
            ob_end_clean();
            curl_close($ch);
            return $contents;

        } catch (Exception $ex) {
            return $ex->getMessage();
        }
        return null;
    }

    /**
     * 默认头部
     * @return array
     */
    public static function GetDefaultHeader()
    {
        return array(
            'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Charset:GBK,utf-8;q=0.7,*;q=0.3',
            'Accept-Encoding:gzip,deflate,sdch',
            'Accept-Language:en-US,en;q=0.8,zh-CN;q=0.6,zh;q=0.4',
            'Cache-Control:max-age=0',
            'User-AgentModel: Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.94 Safari/537.36'
        );
    }

} 