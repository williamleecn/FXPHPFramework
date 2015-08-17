<?php

namespace Web\Wechat;

/*
使用方法：
 $arr = array(
	'account' => '公众平台帐号',
	'password' => '密码'
);
$w = new Weixin($arr);
$w->getAllUserInfo();//获取用户信息
$w->sendMessage('群发内容'); //群发给所有用户
$w->sendMessage('群发内容',$userId); //群发给特定用户
*/

class Weixin
{
    public $userFakeid; //所有粉丝的fakeid
    private $_account; //用户名
    private $_password; //密码
    private $url; //请求的网址
    private $send_data; //提交的数据
    private $getHeader = 0; //是否显示Header信息
    public $token; //公共帐号TOKEN
    private $host = 'mp.weixin.qq.com'; //主机
    private $origin = 'https://mp.weixin.qq.com';
    public $referer; //引用地址
    public $cookie;
    private $pageSize = 100000; //每页用户数（用于读取所有用户）
    private $userAgent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0';

    public $LoginLastError;

    public $NeedSmsVail = false;
    public $PhoneNotice = '';

    public $dump = '';
    public $isDump = false;
    public $UseProxy = false;
    public $ProxyApi = '';

    public function __construct($options)
    {
        $this->_account = isset($options['account']) ? $options['account'] : '';
        $this->_password = isset($options['password']) ? $options['password'] : '';

    }

    //登录
    public function login()
    {
        $this->NeedSmsVail = false;


        if (!empty($this->cookie) && !empty($this->token)) {
            $result = $this->GetnewMsgNum();

            $gobj = json_decode($result);

            if ($gobj != null) {
                if ($gobj->ret == 0) {
                    return true;
                }
            }

        }

        $this->cookie = '';

        $url = 'https://mp.weixin.qq.com/cgi-bin/login?lang=zh_CN';
        $this->send_data = array(
            'username' => $this->_account,
            'pwd' => md5($this->_password),
            'imgcode' => '',
            'f' => 'json'
        );

        $this->referer = "https://mp.weixin.qq.com/";
        $this->getHeader = 1;
        $raw = $this->curlPost($url);

        $pos = strpos($raw, "\r\n\r\n");

        $this->LoginLastError = array('raw' => $raw);
        if ($pos === false) {
            return false;
        }

        $http_header = substr($raw, 0, $pos);
        $http_body = substr($raw, $pos + 4);

        $obj = json_decode($http_body);


        switch ($obj->base_resp->ret) {
            case -1:
                $this->LoginLastError = array('errCode' => $obj->base_resp->ret, 'msg' => "系统错误");
                return false;
            case -2:
                $this->LoginLastError = array('errCode' => $obj->base_resp->ret, 'msg' => "帐号或密码错误");
                return false;
            case -3:
                $this->LoginLastError = array('errCode' => $obj->base_resp->ret, 'msg' => "密码错误");
                return false;
            case 400:
                $this->LoginLastError = array('errCode' => $obj->base_resp->ret, 'msg' => "不存在该帐户");
                return false;
            case -5:
                $this->LoginLastError = array('errCode' => $obj->base_resp->ret, 'msg' => "访问受限");
                return false;
            case -6:
                $this->LoginLastError = array('errCode' => $obj->base_resp->ret, 'msg' => "需要输入验证码");
                return false;
            case -7:
                $this->LoginLastError = array('errCode' => $obj->base_resp->ret, 'msg' => "此帐号已绑定私人微信号，不可用于公众平台登录");
                return false;
            case -8:
                $this->LoginLastError = array('errCode' => $obj->base_resp->ret, 'msg' => "邮箱已存在");
                return false;
            case -32:
                $this->LoginLastError = array('errCode' => $obj->base_resp->ret, 'msg' => "验证码输入错误");
                return false;
            case -200:
                $this->LoginLastError = array('errCode' => $obj->base_resp->ret, 'msg' => "因频繁提交虚假资料，该帐号被拒绝登录");
                return false;
            case -94:
                $this->LoginLastError = array('errCode' => $obj->base_resp->ret, 'msg' => "请使用邮箱登陆");
                return false;
            case 10:
                $this->LoginLastError = array('errCode' => $obj->base_resp->ret, 'msg' => "该公众会议号已经过期，无法再登录使用");
                return false;
            case 0:
                break;
            case 302:
                break;

            default:
                $this->LoginLastError = array('errCode' => $obj->base_resp->ret, 'msg' => "unknown", 'raw' => $raw);
                return false;
        }


        if (preg_match_all('/set-cookie:[\s]+(.*?=[^;]+)/i', $http_header, $match)) { //获取cookie

            $this->cookie .= implode($match[1], ' ;');
        } else {
            $this->LoginLastError = array('errCode' => $obj->base_resp->ret, 'msg' => "unknown", 'raw2' => $raw);
            return false;
        }

        if (isset($obj->redirect_url) && !empty($obj->redirect_url)) {

            if (strpos($obj->redirect_url, 'validate_phone_tmpl') > 0) {

                $this->LoginLastError = array('msg' => 'Need sms. RAW:' . $raw);

                $this->NeedSmsVail = true;

                $this->PhoneNotice = urldecode(rtrim(substr($obj->redirect_url, strrpos($obj->redirect_url, 'phone=') + 6), '",'));

                return false;
            }

            $this->LoginLastError = array();
            $this->token = rtrim(substr($obj->redirect_url, strrpos($obj->redirect_url, '=') + 1), '",');
            return true;
        }

        $this->LoginLastError = array('errCode' => $obj->base_resp->ret, 'msg' => "unknown", 'raw3' => $raw);
        return false;


    }


    public function SendGroupMessage($content)
    {


        $url = 'https://mp.weixin.qq.com/cgi-bin/masssendpage?t=mass/send&token=' . $this->token . '&lang=zh_CN';

        $this->getHeader = 0;
        $data = $this->vget($url);

        $ma = array();
        if (preg_match('/operation_seq: "(.*?)"/', $data, $ma) != 1) {
            return null;
        }

        $url = 'https://mp.weixin.qq.com/cgi-bin/masssend';
        $this->send_data = array(
            'type' => 1,
            'content' => $content,
            'sex' => '0',
            'groupid' => '-1',
            'synctxweibo' => '0',
            'synctxnews' => '0',
            'country' => '',
            'province' => '',
            'city' => '',
            'imgcode' => '',
            'operation_seq' => $ma[1],
            'token' => $this->token,
            'lang' => 'zh_CN',
            'random' => rand(),
            'f' => 'json',
            'ajax' => 1,
            't' => 'ajax-response'
        );
        $this->referer = 'https://mp.weixin.qq.com/cgi-bin/masssendpage?t=mass/send&token=' . $this->token . '&lang=zh_CN';
        $this->getHeader = 0;
        return $this->curlPost($url);
    }

    //单发消息
    private function send($fakeid, $content)
    {
        $url = 'https://mp.weixin.qq.com/cgi-bin/singlesend?t=ajax-response&lang=zh_CN';
        $this->send_data = array(
            'type' => 1,
            'content' => $content,
            'error' => 'false',
            'tofakeid' => $fakeid,
            'token' => $this->token,
            'ajax' => 1,
        );
        $this->referer = 'https://mp.weixin.qq.com/cgi-bin/singlemsgpage?token=' . $this->token . '&fromfakeid=' . $fakeid . '&msgid=&source=&count=20&t=wxm-singlechat&lang=zh_CN';
        return $this->curlPost($url);
    }

    public function GetnewMsgNum()
    {
        $this->getHeader = 0;

        $url = 'https://mp.weixin.qq.com/cgi-bin/getnewmsgnum';
        $this->send_data = array(
            'token' => $this->token,
            't' => 'ajax-getmsgnum',
            'lang' => 'zh_CN',
            'random' => rand(),
            'f' => 'json',
            'ajax' => 1,
            'lastmsgid' => 0
        );
        $this->referer = 'https://mp.weixin.qq.com/cgi-bin/masssendpage?t=mass/send&token=' . $this->token . '&lang=zh_CN';
        return $this->curlPost($url);
    }

    public function SendSMSCode()
    {
        $this->getHeader = 0;

        $url = 'https://mp.weixin.qq.com/cgi-bin/securesmsverify';
        $this->send_data = array(
            'act' => 'sendsmscode',
            'token' => '',
            'lang' => 'zh_CN',
            'random' => rand(),
            'f' => 'json',
            'ajax' => 1,
        );
        $this->referer = 'https://mp.weixin.qq.com/cgi-bin/readtemplate?t=user/validate_phone_tmpl&lang=zh_CN&type=&protected=1&phone=';
        return $this->curlPost($url);
    }

    public function VerifySMSCode($smscode)
    {
        $this->getHeader = 1;

        $url = 'https://mp.weixin.qq.com/cgi-bin/securesmsverify';
        $this->send_data = array(
            'act' => 'verifysmscode',
            'login_sms_code' => $smscode,
            'type' => '',
            'token' => '',
            'lang' => 'zh_CN',
            'random' => rand(),
            'f' => 'json',
            'ajax' => 1,
        );
        $this->referer = 'https://mp.weixin.qq.com/cgi-bin/readtemplate?t=user/validate_phone_tmpl&lang=zh_CN&type=&protected=1&phone=';
        $data = $this->curlPost($url);

        $html2 = explode("\r\n\r\n", $data);

        $http_header = $html2[0];
        $body = $html2[1];

        $obj = json_decode($body);

        if (!isset($obj->base_resp) ||
            !isset($obj->base_resp->ret) ||
            $obj->base_resp->ret != 0
        ) {
            $this->LoginLastError = $body;

            return false;
        }

        if (!preg_match_all('/set-cookie:[\s]+(.*?=[^;]+)/i', $http_header, $match)) { //获取cookie
            $this->LoginLastError = $http_header;

            return false;
        }

        $this->cookie .= implode($match[1], ' ;');

        if (isset($obj->redirect_url) && !empty($obj->redirect_url)) {

            if (strpos($obj->redirect_url, 'validate_phone_tmpl') > 0) {

                $this->LoginLastError = array('msg' => 'Need sms. RAW:' . $data);

                $this->NeedSmsVail = true;

                $this->PhoneNotice = urldecode(rtrim(substr($obj->redirect_url, strrpos($obj->redirect_url, 'phone=') + 6), '",'));

                return false;
            }

        }


        if (isset($obj->base_resp->err_msg) && !empty($obj->base_resp->err_msg)) {
            $this->token = substr($obj->base_resp->err_msg, strrpos($obj->base_resp->err_msg, '=') + 1);
            return true;
        }

        return false;

    }


    //群发消息
    public function sendMessage($content = '', $userId = '')
    {
        if (is_array($userId) && !empty($userId)) {
            foreach ($userId as $v) {
                $json = json_decode($this->send($v, $content));
                if ($json->ret != 0) {
                    $errUser[] = $v;
                }
            }
        } else {
            foreach ($this->userFakeid as $v) {
                $json = json_decode($this->send($v['fakeid'], $content));
                if ($json->ret != 0) {
                    $errUser[] = $v['fakeid'];
                }
            }
        }

        //共发送用户数
        $count = count($this->userFakeid);
        //发送失败用户数
        $errCount = count($errUser);
        //发送成功用户数
        $succeCount = $count - $errCount;

        $data = array(
            'status' => 0,
            'count' => $count,
            'succeCount' => $succeCount,
            'errCount' => $errCount,
            'errUser' => $errUser
        );

        return json_encode($data);
    }

    //获取所有用户信息
    public function getAllUserInfo()
    {
        $info = array();

        foreach ($this->userFakeid as $v) {
            $info[] = $this->getUserInfo($v['groupid'], $v['fakeid']);
        }

        return $info;
    }

    public function GetQRCodeImage($fakeid, $stype = 224)
    {
        $this->getHeader = 0;

        return $this->vget("https://mp.weixin.qq.com/misc/getqrcode?fakeid=" . $fakeid
            . "&token=" . $this->token . "&action=download&style=1&pixsize=" . $stype);


    }


    public function GetMyinfo()
    {
        $this->getHeader = 0;
        $html = $this->vget("https://mp.weixin.qq.com/cgi-bin/settingpage?t=setting/index&action=index&token=" . $this->token . "&lang=zh_CN");

        $html = StringUtitly::SubString($html, 'class="account_setting_area"', '</ul>');

        $rz = preg_match_all('/<li class="account_setting_item">.*?<h4>(.*?)<\/h4>.*?<div class="meta_content">(.*?)<\/div>.*?<\/li>/is', $html, $matches);

        if ($rz === false) return false;

        $attrs = [];

        foreach ($matches[1] as $index => $item) {
            if ($item == '头像') {
                $img = $matches[2][$index];

                preg_match('/src="(.*?)"/is', $img, $ma);

                $attrs[$item] = 'https://mp.weixin.qq.com' . $ma[1];

            } else {
                $attrs[$item] = StringUtitly::RemoveHtmlTag($matches[2][$index]);
            }

        }
        return ($attrs);

    }

    public function Agreement()
    {
        $this->getHeader = 1;
        $this->send_data = array(
            'issigned' => '1'
        );
        return $this->curlPost('https://mp.weixin.qq.com/cgi-bin/setprotocolsign?cgi=setprotocolsign&lang=zh_CN&token=' . $this->token);
    }

    public function TrunOnAPI($url, $token)
    {
        $this->getHeader = 0;
        $this->send_data = array(
            'url' => $url,
            'callback_token' => $token
        );
        return $this->curlPost('https://mp.weixin.qq.com/advanced/callbackprofile?t=ajax-response&token=' . $this->token . '&lang=zh_CN');
    }

    public function TrunOnAdv()
    {
        $this->getHeader = 0;
        $this->send_data = array(
            'flag' => '1',
            'type' => '2',
            'token' => $this->token
        );
        return $this->curlPost('https://mp.weixin.qq.com/misc/skeyform?form=advancedswitchform&lang=zh_CN');
    }


    //获取用户信息
    public function getUserInfo($groupId, $fakeId)
    {
        $url = "https://mp.weixin.qq.com/cgi-bin/getcontactinfo?t=ajax-getcontactinfo&lang=zh_CN&fakeid={$fakeId}";
        $this->getHeader = 0;
        $this->referer = 'https://mp.weixin.qq.com/cgi-bin/contactmanagepage?token=' . $this->token . '&t=wxm-friend&lang=zh_CN&pagesize=' . $this->pageSize . '&pageidx=0&type=0&groupid=' . $groupId;
        $this->send_data = array(
            'token' => $this->token,
            'ajax' => 1
        );
        $message_opt = $this->curlPost($url);
        return $message_opt;
    }

    //获取所有用户fakeid
    private function getUserFakeid()
    {

        $arr = array();
        ini_set('max_execution_time', 600);
        $pageSize = 1000000;
        $this->referer = "https://mp.weixin.qq.com/cgi-bin/home?t=home/index&lang=zh_CN&token=" . $this->token;
        $url = "https://mp.weixin.qq.com/cgi-bin/contactmanage?t=user/index&pagesize={$pageSize}&pageidx=0&type=0&groupid=0&token=" . $this->token . "&lang=zh_CN";
        $user = $this->vget($url);

        $preg = "/\"id\":(\d+),\"name\"/";
        preg_match_all($preg, $user, $b);
        $i = 0;
        foreach ($b[1] as $v) {
            $url = 'https://mp.weixin.qq.com/cgi-bin/contactmanage?t=user/index&pagesize=' . $pageSize . '&pageidx=0&type=0&groupid=' . $v . '&token=' . $this->token . '&lang=zh_CN';
            $user = $this->vget($url);
            $preg = "/\"id\":(\d+),\"nick_name\"/";
            preg_match_all($preg, $user, $a);
            foreach ($a[1] as $vv) {
                $arr[$i]['fakeid'] = $vv;
                $arr[$i]['groupid'] = $v;
                $i++;
            }
        }
        return $arr;
    }

    /**
     * curl模拟登录的post方法
     * @param $url request地址
     * @param $header 模拟headre头信息
     * @return json
     */
    private function  curlPost($url, $isUrlEndcodePost = false)
    {
        $header = array(
            'Accept:text/html, */*; q=0.01',
            'Accept-Charset:GBK,utf-8;q=0.7,*;q=0.3',
            'Accept-Encoding:gzip,deflate,sdch',
            'Accept-Language:en-US,en;q=0.8,zh-CN;q=0.6,zh;q=0.4',
            'Connection:keep-alive',
            'Host:' . $this->host,
            'Origin:' . $this->origin,
            'Referer:' . $this->referer,
            'User-Agent:' . $this->userAgent,
            'X-Requested-With:XMLHttpRequest'
        );

        if ($isUrlEndcodePost) {
            $header[] = 'Content-Type:application/x-www-form-urlencoded; charset=UTF-8';
        }


        if ($this->UseProxy) {

            $result = file_get_contents($this->ProxyApi, false, stream_context_create(
                    array('http' =>
                        array(
                            'method' => 'POST',
                            'header' => 'Content-type: application/x-www-form-urlencoded',
                            'content' => http_build_query(
                                array(
                                    'cmd' => 'POST',
                                    'url' => $url,
                                    'header' => serialize($header),
                                    'useragent' => $this->userAgent,
                                    'cookie' => $this->cookie,
                                    'posts' => serialize($this->send_data),
                                    'getheader' => $this->getHeader
                                )
                            )
                        )
                    )
                )

            );
            if ($this->isDump) {
                $this->dump .= $result . '|||';
            }

            return $result;

        }


        $curl = curl_init(); //启动一个curl会话
        curl_setopt($curl, CURLOPT_URL, $url); //要访问的地址
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header); //设置HTTP头字段的数组
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); //对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); //从证书中检查SSL加密算法是否存在
        //curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); //从证书中检查SSL加密算法是否存在
        //curl_setopt($curl, CURLOPT_SSLVERSION,3);
        curl_setopt($curl, CURLOPT_USERAGENT, $this->userAgent); //模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); //使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); //自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); //发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $this->send_data); //Post提交的数据包
        curl_setopt($curl, CURLOPT_COOKIE, $this->cookie); //读取储存的Cookie信息
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); //设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_ENCODING, "gzip");
        curl_setopt($curl, CURLOPT_HEADER, $this->getHeader); //显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //获取的信息以文件流的形式返回
        $result = curl_exec($curl); //执行一个curl会话

        curl_close($curl); //关闭curl

        if ($this->isDump) {
            $this->dump .= $result . '|||';
        }

        return $result;
    }

    public function vget($url)
    { // 模拟获取内容函数
        $header = array(
            'Accept:*/*',
            'Accept-Encoding:gzip,deflate',
            'Accept-Language:zh-CN,zh;q=0.8',
            'Connection:keep-alive',
            'Host:mp.weixin.qq.com',
            'Referer:' . $this->referer,
            'X-Requested-With:XMLHttpRequest'
        );


        if ($this->UseProxy) {

            $result = file_get_contents($this->ProxyApi, false, stream_context_create(
                    array('http' =>
                        array(
                            'method' => 'POST',
                            'header' => 'Content-type: application/x-www-form-urlencoded',
                            'content' => http_build_query(
                                array(
                                    'cmd' => 'GET',
                                    'url' => $url,
                                    'header' => serialize($header),
                                    'useragent' => $this->userAgent,
                                    'cookie' => $this->cookie,
                                    'getheader' => $this->getHeader
                                )
                            )
                        )
                    )
                )

            );
            if ($this->isDump) {
                $this->dump .= $result . '|||';
            }

            return $result;

        }


        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header); //设置HTTP头字段的数组
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        @curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); //从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_SSLVERSION, 3);
        curl_setopt($curl, CURLOPT_USERAGENT, $this->userAgent); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_HTTPGET, 1); // 发送一个常规的GET请求
        curl_setopt($curl, CURLOPT_COOKIE, $this->cookie); // 读取上面所储存的Cookie信息
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, $this->getHeader); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        curl_setopt($curl, CURLOPT_ENCODING, "gzip");
        $tmpInfo = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            //echo 'Errno'.curl_error($curl);
        }
        curl_close($curl); // 关闭CURL会话
        return $tmpInfo; // 返回数据
    }

}
