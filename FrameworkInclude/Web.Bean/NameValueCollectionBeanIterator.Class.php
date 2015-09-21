<?php
/**
 * Created by JetBrains PhpStorm.
 * User: William Lee
 * Date: 13-5-13
 * Time: 下午1:36
 *
 */

namespace Web\Bean;


class NameValueCollectionBeanIterator extends BeansArrayIterator
{
    /**
     * @return NameValueCollectionBean
     */
    public function Next()
    {
        return parent::Next();
    }

}