<?php
/**
 * Created by PhpStorm.
 * User: William
 * Date: 2015/1/20
 * Time: 22:09
 */


namespace Web\Controller;

use Web\Framework\Core;
use Web\Utils\XRequest;

abstract class ControllerBase extends \Web\Framework\ControllerBase
{
    public $CheckLogin = true;
    public $UserInfo = null;


    const SESSION = 'PHP_SESSION';


    public function Initialize()
    {
        Core::OpenMsqlConn();

        @session_start();

        $this->Request = new XRequest($_REQUEST);

        $this->CategoryName = \WebRouter::$ControllerInfo['category'];
        $this->ControllerName = \WebRouter::$ControllerInfo['controller'];

        if ($this->CheckLogin) $this->CheckSession();


    }

    private function CheckSession()
    {
        if ($this->UserInfo == null) {


            if (!isset($_SESSION[self::SESSION]) || empty($_SESSION[self::SESSION])) {

                $this->Alert(9001, '登录超时', \WebRouter::GetControllerPath('User', 'Login'));

            }

            $this->UserInfo = $_SESSION[self::SESSION];
        }
    }

    public function SavekSession()
    {
        $_SESSION[self::SESSION] = $this->UserInfo;

    }


}