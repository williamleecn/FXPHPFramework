<?php

namespace Webs\Bean;

class BeansArrayIterator
{
    private $index = 0;
    private $Count;
    public $array;

    public function __construct(&$array)
    {
        if (!is_array($array) || $array == null) {
            $this->Count = 0;
            $this->array = array();
            return;
        }

        $this->Count = count($array);
        $this->array = $array;
    }

    public function Next()
    {

        if (!isset($this->array[$this->index])) return null;

        return $this->array[$this->index++];

    }


    public function GetCurrentIndex()
    {
        return $this->index;
    }

    public function GetCount()
    {
        return $this->Count;
    }


    public function RemoveAt($index)
    {
        unset($this->array[$index]);
    }


    public function Rewind()
    {
        $this->Count = count($this->array);
        $this->index = 0;
    }


    /**
     * @return bool
     */
    public function HasNext()
    {
        if (($this->index + 1) > $this->Count) return false;
        return true;
    }

    public function AppendExcelCell($str)
    {
        return $str . "\t";
    }

    public function AppendExcelLastCell($str)
    {
        return $str . "\r\n";
    }

}
