<?php
/**
 * Created by PhpStorm.
 * User: William
 * Date: 2015/9/9
 * Time: 18:01
 */

namespace Web\Framework;


use Web\Utils\DB;

abstract class DAOBase
{
    /**
     * @var ModelBase
     */
    public $Model = null;


    /**
     * @param $pkey
     * @return ModelBase
     */
    public function FindOneFromPrimaryKey($pkey)
    {
        $kname = $this->Model->_GetPrimaryKeyName();

        if ($kname == false) return false;

        return $this->Model->_FillData(DB::GetOneFromArray($this->Model->tableName, [$kname => $pkey]));

    }

    /**
     * @param $keys
     * @return ModelBase
     */
    public function FindOneFromArrayLimit($keys)
    {

        return $this->Model->_FillData(DB::GetOneFromArray($this->Model->tableName, $keys));

    }

    /**
     * @param $keys
     * @return ModelBase[]
     */
    public function FinAllFromKeyArray($keys)
    {
        $objects = [];

        $arr = DB::GetAllDataFromArray($this->Model->tableName, $keys);

        foreach ($arr as $item) {

            $model = clone $this->Model;

            $objects[] = $model->_FillData($item);
        }

        return $objects;

    }

    /**
     * @return bool|\mysqli_result
     */
    public function CreateNew()
    {

        return DB::InsertDataFromArray($this->Model->tableName, $this->Model->_GetModifyColumnAndValues());

    }

    /**
     * @return int
     */
    public function GetLastNewID()
    {
        return DB::GetLastID();

    }


    /**
     * @param $pkey
     * @return bool|\mysqli_result
     */
    public function DeleteFrommPrimaryKey($pkey)
    {
        $kname = $this->Model->_GetPrimaryKeyName();

        if ($kname == false) return false;

        return DB::Delete($this->Model->tableName, $kname, $pkey);
    }


    /**
     * @return bool|\mysqli_result
     */
    public function UpdateFromPrimaryKey()
    {

        $kname = $this->Model->_GetPrimaryKeyName();

        if ($kname == false) return false;

        $data = [];

        $count = count($this->Model->columnModify);
        $pkey = 0;

        for ($i = 0; $i < $count; $i++) {

            if ($this->Model->columnModify[$i]['Field'] == $kname) {
                $pkey = $this->Model->columnModify[$i]['Value'];
                continue;
            }

            if ($this->Model->columnModify[$i]['Modified'] && $this->Model->columnModify[$i]['Field'] != $kname) {

                $data[$this->Model->columnModify[$i]['Field']] = $this->Model->columnModify[$i]['Value'];
            }


        }

        return DB::UpdateDataFromArrayKeyVal($this->Model->tableName, $data, $kname, $pkey);

    }

    /**
     * @param array $KVals
     * @return bool|\mysqli_result
     */
    public function UpdateDataFromArray($KVals = [])
    {
        $kname = $this->Model->_GetPrimaryKeyName();
        if ($kname == false) return false;
        $data = [];
        $count = count($this->Model->columnModify);
        
        for ($i = 0; $i < $count; $i++) {
            if ($this->Model->columnModify[$i]['Field'] == $kname) {
                continue;
            }
            if ($this->Model->columnModify[$i]['Modified'] && $this->Model->columnModify[$i]['Field'] != $kname) {
                $data[$this->Model->columnModify[$i]['Field']] = $this->Model->columnModify[$i]['Value'];
            }
        }
        return DB::UpdateDataFromArray($this->Model->tableName, $data, DB::BuildWhereFromArray($KVals));
    }

}