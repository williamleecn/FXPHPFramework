<?php
namespace Web\Controller\MPMsg;

use Web\Framework\IController;
use Webs\Utils\DB;
use Webs\Utils\Logger;
use Webs\Utils\MenuUtils;
use Webs\Utils\MPXML;
use Webs\Utils\WeixinUtils;
use \Webs\Utils\RequestProtect;

abstract class BaseMType implements IController
{

    public $postObj;

    public $preurl = 'http://m.yifangyike.com/';

    /**
     * @var 消息id，64位整型
     */
    public $MsgId;

    /**
     * 页面默认执行
     * @return mixed
     */
    abstract public function Execute();


    /**
     * 执行前初使化
     * @return mixed
     */
    public function Initialize()
    {

    }

    /**
     * 执行Do的方法
     * @return mixed
     */
    public function ProcessDoAction()
    {
        // TODO: Implement ProcessDoAction() method.
    }

    /**
     * 执行结束后处理
     * @return mixed
     */
    public function ResponseEnd()
    {
        exit;
    }


}