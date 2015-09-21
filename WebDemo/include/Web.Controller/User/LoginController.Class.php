<?php
/**
 * Created by PhpStorm.
 * User: William
 * Date: 2015/6/27
 * Time: 1:02
 */

namespace Web\Controller\User;

use Web\Controller\ControllerBase;
use Web\Utils\DB;
use Web\Utils\StringUtitly;
use Web\Utils\WebUtils;

class LoginController extends ControllerBase
{

    public $AllUser;

    public function Initialize()
    {
        $this->CheckLogin = false;
        parent::Initialize();
    }

    public function Execute()
    {
        $this->data = sprintf("服务时间: %s,当前IP为: %s", StringUtitly::FormatTimestamp(time()), WebUtils::GetIP());
    }


    public function doLogin()
    {

        $name = $this->GetRequiredString('name', 110, 'name is empty');

        $psw = $this->GetRequiredString('psw', 111, 'psw is empty');

        //写法1
        $one = DB::GetOneFromArray("dmo_user", ['name' => $name]);
        //

        //写法2
        $name = DB::Esc($name);//转义
        $one = DB::GetOne("SELECT id,name,psw FROM dmo_user WHERE name='$name'");
        //

        if (empty($one)) {
            $this->JSONAlert(112, 'user name not exist or psw incorrect');
        }

        if ($one['psw'] != md5($psw)) {
            $this->JSONAlert(112, 'user name not exist or psw incorrect');
        }

        $this->UserInfo = $one;
        $this->SavekSession();

        $this->JSONAlert(0, 'ok', ['Redirect' => \WebRouter::GetControllerPath('User','Index')]);
    }


}