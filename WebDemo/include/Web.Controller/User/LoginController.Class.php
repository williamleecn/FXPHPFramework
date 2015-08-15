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

class LoginController extends ControllerBase
{
    public function Initialize()
    {
        $this->CheckLogin = false;
        parent::Initialize();
    }


    public function Execute()
    {
        $this->data = StringUtitly::FormatTimestamp(time());
    }

    public function doLogin()
    {

        $name = $this->Request->TryGetString('name');
        $psw = $this->Request->TryGetString('psw');

        if (StringUtitly::IsEmptyOrBlank($name)) {
            $this->JSONAlert(110, 'name is empty');

        }

        if (StringUtitly::IsEmptyOrBlank($psw)) {
            $this->JSONAlert(111, 'psw is empty');
        }

        $name = DB::Esc($name);

        $one = DB::GetOne("SELECT id,name,psw FROM dmo_user WHERE name='$name'");

        if (empty($one)) {
            $this->JSONAlert(112, 'user name not exist or psw incorrect');
        }

        if ($one['psw'] != md5($psw)) {
            $this->JSONAlert(112, 'user name not exist or psw incorrect');
        }


        $this->JSONAlert(0, 'ok', ['Redirect' => 'User.Index']);
    }


} 