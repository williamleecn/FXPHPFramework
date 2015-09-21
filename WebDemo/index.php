<?php
use Web\Framework\RouterImpl;

require(__DIR__ . '/../data/ConfigBase.Config.php');
require(__DIR__ . '/../data/Framework.Config.php');
require(__DIR__ . '/../FrameworkInclude/Web.Framework/CoreBase.Class.php');
require(__DIR__ . '/include/Web.Framework/Core.Class.php');

Web\Framework\Core::InitFramework(__DIR__);

final class WebRouter extends RouterImpl
{
    /**
     * Web Name
     */
    const WebName = 'WebAdmin';

}

WebRouter::$DefaultHome = WebRouter::GetControllerPath('User', 'Login');

WebRouter::DoAction();
