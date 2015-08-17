<?php
require_once(__DIR__ . "/../data/Framework.Config.php");
require_once(__DIR__ . '/../include/Web.Framework/Core.Class.php');

Web\Framework\Core::InitFramework(__DIR__);//初使化框架

/**
 * Created by PhpStorm.
 * User: William
 * Date: 2015/1/20
 * Time: 15:06
 */
final class WebRouter extends \Web\Framework\Router
{
    /**
     * 网站名称
     */
    const WebName = 'WXDemo';

    /**
     * @var \Web\Controller\ControllerBase
     */
    public static $Context;
}

WebRouter::DoAction();
