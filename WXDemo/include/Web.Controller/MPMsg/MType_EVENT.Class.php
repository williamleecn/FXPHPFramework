<?php
namespace Page\WAction;

use Webs\Config\WebConfig;
use Webs\Utils\ArrayUtils;
use Webs\Utils\DB;
use Webs\Utils\Logger;
use Webs\Utils\MPXML;
use Webs\Utils\StringUtitly;
use Webs\Utils\WebUtils;
use Webs\Utils\WxChatUtils;


class MType_EVENT extends BaseMType
{

    public $Event;

    public $EventKey;
    public $Ticket;

    public $Latitude;
    public $Longitude;
    public $Precision;

    function Execute(&$postObj)
    {
        $this->postObj = $postObj;

        $this->Event = $postObj->Event;

        self::Process();
    }

    function Process()
    {
        $method = "OnEvent_" . strtoupper($this->Event);

        if (!method_exists(__CLASS__, $method)) {

            Logger::LogError($this->postObj, 'Event Not Function Attach');

            MPXML::TextAlert($this->postObj, "Event Not Function Attach:" . $this->Event);
        }

        return call_user_func_array(array(__CLASS__, $method), array());
    }

    /**
     * EventKey     事件KEY值，qrscene_为前缀，后面为二维码的参数值
     * Ticket     二维码的ticket，可用来换取二维码图片
     *
     */
    public function OnEvent_SUBSCRIBE()
    {
        $this->OnEvent_SCAN();
    }

    /**
     * EventKey     事件KEY值，qrscene_为前缀，后面为二维码的参数值
     * Ticket     二维码的ticket，可用来换取二维码图片
     *
     */
    public function OnEvent_SCAN()
    {

        if (isset($this->postObj->EventKey) &&
            isset($this->postObj->Ticket)
        ) {
            $this->EventKey = intval(ltrim($this->postObj->EventKey, 'qrscene_'));
            $this->Ticket = $this->postObj->Ticket;

        }

        $openid = $this->postObj->FromUserName;

        $this->ResponseEmptyMessage();


    }

    public function OnEvent_UNSUBSCRIBE()
    {

        $openid = $this->postObj->FromUserName;

        DB::Update("UPDATE vwx_account SET SUBSCRIBE=0 WHERE OPENID='$openid'");

        MPXML::TextAlert($this->postObj, "欢迎再次关注");
    }

    public function OnEvent_CLICK()
    {
        $this->UpdateUserLastActionTime($this->postObj->FromUserName);

        $this->EventKey = $this->postObj->EventKey;

        $this->ResponseEmptyMessage();

    }

    /**
     * Latitude      地理位置纬度
     * Longitude     地理位置经度
     * Precision     地理位置精度
     */
    public function OnEvent_LOCATION()
    {
        $this->Latitude = $this->postObj->Latitude;
        $this->Longitude = $this->postObj->Longitude;
        $this->Precision = $this->postObj->Precision;

        $this->ResponseEmptyMessage();

    }

    public function OnEvent_MASSSENDJOBFINISH()
    {
        array(
            'ToUserName' => $this->postObj->ToUserName,
            'MsgID' => $this->postObj->MsgID,
            'TIME' => time(),
            'Status' => $this->postObj->Status,
            'TotalCount' => $this->postObj->TotalCount,
            'FilterCount' => $this->postObj->FilterCount,
            'SentCount' => $this->postObj->SentCount,
            'ErrorCount' => $this->postObj->ErrorCount
        );

    }

    public function OnEvent_VIEW()
    {
        ($this->postObj->FromUserName);
    }



}