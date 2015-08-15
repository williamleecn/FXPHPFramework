<?php
namespace Page\WAction;

use Webs\Utils\MPXML;
use Webs\Utils\WeixinUtils;

class MType_TEXT extends BaseMType
{
    /**
     * @var 文本消息内容
     */
    public $Content;

    function Execute(&$postObj)
    {
        $this->postObj = $postObj;

        $this->Content = $postObj->Content;

        self::Process();
    }

    function Process()
    {
        $this->ResponseEmptyMessage();

    }


}