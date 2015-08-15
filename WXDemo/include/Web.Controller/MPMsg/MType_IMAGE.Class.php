<?php
namespace Page\WAction;

use Webs\Utils\MPXML;

class MType_IMAGE extends BaseMType
{

    function Execute(&$postObj)
    {

        self::Process();
    }

    function Process()
    {
        $this->ResponseEmptyMessage();

    }
}