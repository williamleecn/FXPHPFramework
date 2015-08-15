<?php
namespace Page\WAction;

use Webs\Config\WebConfig;
use Webs\Utils\DB;
use Webs\Utils\Logger;
use Webs\Utils\MenuUtils;
use Webs\Utils\MPXML;
use Webs\Utils\WeixinUtils;
use \Webs\Utils\RequestProtect;

abstract class BaseMType
{

    public $postObj;

    public $preurl = 'http://m.yifangyike.com/';

    /**
     * @var 消息id，64位整型
     */
    public $MsgId;

    abstract function Execute(&$postObj);

    abstract function Process();

}