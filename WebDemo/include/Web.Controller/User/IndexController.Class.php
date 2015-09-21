<?php
/**
 * Created by PhpStorm.
 * User: William
 * Date: 2015/9/18
 * Time: 12:47
 */

namespace Web\Controller\User;


use Web\Controller\ControllerBase;
use Web\Utils\DB;

class IndexController extends ControllerBase
{

    public function Execute()
    {
        $this->list=DB::GetAllDataFromArray('dmo_user');

    }

}