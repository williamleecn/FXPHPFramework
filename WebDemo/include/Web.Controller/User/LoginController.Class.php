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

    public $AllUser;

    public function Initialize()
    {
        $this->CheckLogin = false;
        parent::Initialize();
    }

    public function Execute()
    {
        $this->AllUser = DB::GetAllData("SELECT id,name,psw FROM dmo_user ");

        $this->data = 'time=>' . StringUtitly::FormatTimestamp(time());
    }


    public function doLogin()
    {

        /*
                DB::InsertDataFromArray('dmo_user', [
                    'name' => 'test',
                    'psw' => md5('test')
                ]);

                DB::UpdateDataFromArray('dmo_user', [
                    'name' => 'test',
                    'psw' => md5('test')
                ],"WHERE id=1");

        */


        DB::Update("DELETE FROM dmo_user WHERE id=111");


        $name = $this->Request->TryGetString('name');//I('name');  $_REQUEST['name']

        $psw = $this->Request->TryGetString('psw');

        if (StringUtitly::IsEmptyOrBlank($name)) {
            $this->JSONAlert(110, 'name is empty');
        }

        if (StringUtitly::IsEmptyOrBlank($psw)) {
            $this->JSONAlert(111, 'psw is empty');
        }

        $name = DB::Esc($name);//转义

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