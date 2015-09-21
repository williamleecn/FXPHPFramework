<?php
/**
 * Created by PhpStorm.
 * User: William
 * Date: 2015/9/11
 * Time: 10:07
 */

namespace Web\Framework;


use Web\Utils\DB;

abstract class ModelBase
{
    public $tableName = '';

    public $column = [];

    public $columnModify = [];

    /**
     * @param $data
     * @return $this
     */
    public function _FillData($data)
    {
        $this->columnModify = [];

        if (empty($data)) return $this;

        foreach ($data as $k => $v) {
            $this->_SetColumnValue($k, $v, false);
        }


        return $this;
    }


    public function _GetColumnValue($column)
    {
        $count = count($this->columnModify);

        for ($i = 0; $i < $count; $i++) {
            if ($this->columnModify[$i]['Field'] == $column) {
                return $this->columnModify[$i]['Value'];
            }
        }

        return null;
    }

    /**
     * @param $column
     * @param $val
     * @return bool
     */
    public function _SetColumnValue($column, $val, $Modified = true)
    {

        $count = count($this->columnModify);

        for ($i = 0; $i < $count; $i++) {
            if ($this->columnModify[$i]['Field'] == $column) {
                $this->columnModify[$i]['Value'] = $val;
                if (isset($this->columnModify[$i]['DefaultValue'])) {

                    $this->columnModify[$i]['Modified'] = true;
                    $this->columnModify[$i]['Value'] = $val;

                } else {

                    $this->columnModify[$i]['Modified'] = $Modified;
                    $this->columnModify[$i]['Value'] = $val;

                }

                return true;

            }

        }

        $count = count($this->column);

        for ($i = 0; $i < $count; $i++) {
            if ($this->column[$i]['Field'] == $column) {

                $this->columnModify[] = [
                    'Field' => $column,
                    'Modified' => $Modified,
                    'Value' => $val,
                    'DefaultValue' => $val
                ];
                return true;
            }
        }

        return false;

    }

    public function _HasColumnValue($column)
    {
        $count = count($this->columnModify);

        for ($i = 0; $i < $count; $i++) {
            if ($this->columnModify[$i]['Field'] == $column) {
                return true;
            }
        }

        return false;
    }

    public function _InitFieldModifyStatus()
    {
        $this->columnModify = [];

    }

    public function _GetPrimaryKeyName()
    {

        $count = count($this->column);

        for ($i = 0; $i < $count; $i++) {
            if ($this->column[$i]['PrimaryKey']) {

                return $this->column[$i]['Field'];
            }
        }
        return false;

    }

    public function _GetModifyColumns()
    {
        $data = [];

        $count = count($this->columnModify);

        for ($i = 0; $i < $count; $i++) {

            if ($this->columnModify[$i]['Modified']) {

                $data[] = $this->columnModify[$i]['Field'];
            }
        }

        return $data;
    }

    public function _GetModifyColumnAndValues()
    {
        $data = [];

        $count = count($this->columnModify);

        for ($i = 0; $i < $count; $i++) {

            if ($this->columnModify[$i]['Modified']) {

                $data[$this->columnModify[$i]['Field']] = $this->columnModify[$i]['Value'];
            }
        }

        return $data;
    }

}