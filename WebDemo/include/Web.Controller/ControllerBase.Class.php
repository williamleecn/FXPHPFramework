<?php
/**
 * Created by PhpStorm.
 * User: William
 * Date: 2015/1/20
 * Time: 22:09
 */


namespace Web\Controller;

use Web\Framework\Core;
use Web\Framework\IController;
use Web\Utils\WebUtils;
use Web\Utils\XRequest;

abstract class ControllerBase implements IController
{
    public $ShowTemplate = true;
    public $CheckLogin = true;
    public $UserInfo = null;

    /**
     * @var int 一般用于存放ID
     */
    public $id;

    /**
     * @var int 一般用于存放ID
     */
    public $topid;

    public $list = [];
    public $data;

    public $nav;
    public $listNav;

    const SESSION = 'PHP_SESSION';

    public $CurrMenu = '';

    public $Title = '';

    /**
     * @var XRequest
     */
    public $Request;

    public function Initialize()
    {
        Core::OpenMsqlConn();

        @session_start();

        $this->Request = new XRequest($_REQUEST);


        $this->CurrMenu = \WebRouter::$ControllerInfo['name'] . '.' . \WebRouter::$ControllerInfo['class'];

        if ($this->CheckLogin) $this->CheckSession();


    }

    private function CheckSession()
    {
        if ($this->UserInfo == null) {


            if (!isset($_SESSION[self::SESSION]) || empty($_SESSION[self::SESSION])) {

                if ($this->Request->TryGetString('r') == 'json') {
                    WebUtils::JSONAlert(9001, '登录超时');
                    $this->ResponseEnd();
                } else {
                    WebUtils::JSAlert('登录超时', "/index.php/User.Login");
                    $this->ResponseEnd();
                }

            }

            $this->UserInfo = $_SESSION[self::SESSION];
        }
    }

    public abstract function Execute();

    public function ResponseEnd()
    {
        exit;
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

            return;
        }

        return call_user_func_array(array($this, $method), array());
    }


}