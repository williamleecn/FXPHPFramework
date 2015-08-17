<?php
/**
 * Created by PhpStorm.
 * User: William
 * Date: 2015/8/16
 * Time: 16:16
 */

namespace Web\Controller\User;


use Web\Controller\ControllerBase;

class ModifyPasswordController extends ControllerBase
{

    public function Initialize()
    {
        $this->CheckLogin = false;
        $this->ShowTemplate=true;

        parent::Initialize();
    }

    public function Execute()
    {

        \WebRouter::ChangeTemplatePath('User','Login');

        echo '-Execute-';

    }

    public function doModify()
    {
        echo 'doModfiy';
        exit;
    }


} 