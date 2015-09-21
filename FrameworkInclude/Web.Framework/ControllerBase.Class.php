<?php
/**
 * Created by PhpStorm.
 * User: William
 * Date: 2015/8/14
 * Time: 11:03
 */

namespace Web\Framework;


use Web\Utils\StringUtitly;
use Web\Utils\WebUtils;
use Web\Utils\XRequest;

interface  IController
{
    /**
     * 执行前初使化
     * @return mixed
     */
    public function Initialize();

    /**
     * 页面默认执行
     * @return mixed
     */
    public function Execute();


    /**
     * 执行Do的方法
     * @return mixed
     */
    public function ProcessDoAction();

    /**
     * 执行结束后处理
     * @return mixed
     */
    public function ResponseEnd();
}

abstract class ControllerBase implements IController
{

    public $Title = '';

    public $id;
    public $list = [];
    public $data;
    public $nav;
    public $listNav;

    public $CategoryName;
    public $ControllerName;

    public $ShowTemplate = true;

    /**
     * @var XRequest
     */
    public $Request;

    public function Alert($code, $msg, $redirect)
    {
        if ($this->Request->TryGetString('_r') == 'json') {
            $this->JSONAlert($code, $msg);
        }
        $this->JSAlert($msg, $redirect);
    }

    public function GetRequiredString($field, $code, $msg, $redirect = WebUtils::REDIRECT_GOBACK)
    {
        $str = $this->Request->TryGetString($field);

        if (StringUtitly::IsBlank($str)) {

            $this->Alert($code, $msg, $redirect);
        }

        return $str;


    }

    /**
     * 页面默认执行
     * @return mixed
     */
    public abstract function Execute();

    public function ResponseEnd()
    {
        Router::ResponseEnd();
    }

    public function JSAlert($msg, $redirect = WebUtils::REDIRECT_GOBACK, $isTop = false)
    {

        WebUtils::JSAlert($msg, $redirect, $isTop);

        $this->ResponseEnd();
    }

    public function JSONAlert($Result = 0, $msg = '', $data = array())
    {
        WebUtils::JSONAlert($Result, $msg, $data);

        $this->ResponseEnd();
    }

    public function JSONCallBackAlert($Result = 0, $msg = '', $data = array(), $callback = '')
    {
        WebUtils::JSONCallBackAlert($Result, $msg, $data, $callback);

        $this->ResponseEnd();
    }

    public function JSONCallBackAlert2($Result = 0, $msg = '', $data = array(), $callback = '')
    {
        WebUtils::JSONCallBackAlert2($Result, $msg, $data, $callback);

        $this->ResponseEnd();
    }

    public function GetDoActionName()
    {
        if (!$this->Request->HasKey('do') || $this->Request->IsEmpty('do')) return false;

        return $this->Request->TryGetString('do');
    }

    public function ProcessDoAction()
    {

        $Action = $this->GetDoActionName();

        $method = "do" . $Action;

        if (!method_exists($this, $method)) {
            throw new \Exception('Controller not exist method of do action: ' . $method);
        }

        return call_user_func_array(array($this, $method), array());
    }

}