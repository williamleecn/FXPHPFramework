<?php
/**
 * Created by JetBrains PhpStorm.
 * User: William Lee
 * Date: 13-5-13
 * Time: 下午3:10
 *
 */

namespace Webs\Bean;

class NameValueCollection
{

    private $data = array();

    const SORT_ASC = 1;
    const SORT_DESC = 2;

    /**
     * @param $Bean NameValueCollectionBean
     */
    public function add(&$Bean)
    {
        if (!is_object($Bean)) return false;

        if ($this->HasKeyName($Bean->getName())) {
            return false;
        }

        $this->data[] = $Bean;

    }

    /**
     * @param $index
     * @return  null|NameValueCollectionBean
     */
    public function get($index)
    {
        if (isset($this->data[$index])) {
            return $this->data[$index];
        }

        return null;
    }

    /**
     * @return int
     */
    public function size()
    {
        return count($this->data);

    }

    /**
     *
     */
    public function removeAll()
    {
        $this->data = [];
        $this->index = 0;

    }

    /**
     * @return NameValueCollectionBeanIterator
     */
    public function Interator()
    {
        return new NameValueCollectionBeanIterator($this->data);

    }

    /**
     * @param $name
     * @return bool
     */
    public function HasKeyName($name)
    {
        $ite = $this->Interator();

        while ($ite->HasNext()) {

            if ($ite->Next()->getName() == $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $name
     */
    public function removeByKeyName($name)
    {
        foreach ($this->data as $index => $item) {
            if ($item->getName() == $name) {
                unset($this->data[$index]);
            }
        }

    }

    /**
     * @return array
     */
    public function GetAllKeyNames()
    {
        $keyns = array();

        $ite = $this->Interator();

        while ($ite->HasNext()) {

            $keyns[] = $ite->Next()->getName();
        }
        return $keyns;

    }

}