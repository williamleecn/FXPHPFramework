<?php
/**
 * Created by PhpStorm.
 * User: William
 * Date: 2015/9/18
 * Time: 11:08
 */

namespace Web\Framework;

use Web\Controller\ControllerBase;

class RouterImpl extends Router
{
    /**
     * @var ControllerBase
     */
    public static $Context;

    public static function  GetControllerPath($category, $controller)
    {
        return "/WebDemo/?r=$category.$controller";
    }

    public static function DoAction()
    {
        $ControllerClass = parent::PreDoAction();

        parent::ProcessController(self::$Context, $ControllerClass, self::$category, self::$controller);
    }

}