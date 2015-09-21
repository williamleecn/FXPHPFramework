<?php
/**
 * Created by PhpStorm.
 * User: kongy
 * Date: 2015/9/3
 * Time: 14:29
 */

namespace Web\Utils;

use Web\Framework\Config;

/**
 * 简单的数据操作模型
 * Class Model
 * @package Web\Utils
 * @Author 孔元元 <system@kyy1996.cn>
 */
class Model
{
    public $TrueTableName = "";
    public $TableName = "";
    public $InsertId = 0;
    public $AffectedRows = 0;
    private $LastSql = "";
    private $Data = [];
    private $Fields = [];
    private $Db;
    private $Prefix = "";
    private $PrimaryKey = "";
    private $Where = "";
    private $Limit = "";
    private $Order = "";
    private $FetchSql = false;

    /**
     * 模型初始化
     * @param string|object $TableName
     * @param bool $prefix
     * @Author 孔元元 <system@kyy1996.cn>
     */
    function __construct($TableName = "", $prefix = false)
    {
        $this->Db = new DB();
        if ($TableName) {
            if (is_string($TableName)) $this->TableName = $TableName;
            if (is_object($TableName)) $this->TableName = get_class($TableName);
        }
        if ($prefix !== false) $this->Prefix = $prefix ? $prefix : Config::$cfg_dbprefix;
        $this->TrueTableName = $this->Prefix . $this->TableName;
        $sql = "SHOW FULL COLUMNS FROM " . $this->TrueTableName;
        $row = $this->Select($sql);
        foreach ($row as $value) {
            $this->Fields[] = $value['Field'];
            if ($value['Key'] === "PRI") $this->PrimaryKey = $value['Field'];
        }
    }

    /**
     * 执行查询
     * @param string $sql
     * @return array|string
     * @Author 孔元元 <system@kyy1996.cn>
     */
    public function Select($sql = "")
    {
        if ($sql) {
            $this->LastSql = $sql;
        } else {
            $this->LastSql = "SELECT * FROM " . $this->TrueTableName . " " . $this->Where . " " . $this->Order . " " . $this->Limit;
        }
        if ($this->FetchSql) return $this->LastSql;
        return DB::GetAllData($this->LastSql);
    }

    /**
     * 查找一条数据
     * @param string $sql
     * @return array|bool|string
     * @Author 孔元元 <system@kyy1996.cn>
     */
    public function Find($sql = "")
    {
        if ($sql) {
            $this->LastSql = $sql;
        } else {
            $this->LastSql = "SELECT * FROM " . $this->TrueTableName . " " . $this->Where . " " . $this->Order . " " . $this->Limit;
        }
        if ($this->FetchSql) return $this->LastSql;
        return DB::GetOne($this->LastSql);
    }

    /**
     * 限制行数
     * @param string $limit
     * @return Model
     * @Author 孔元元 <system@kyy1996.cn>
     */
    public function Limit($limit = "")
    {
        $this->Limit = $limit;
        return $this;
    }

    /**
     * 插入数据
     * @param array $data
     * @return bool|resource
     * @Author 孔元元 <system@kyy1996.cn>
     */
    public function Add($data = [])
    {
        if ($data) $this->Create($data);
        $result = DB::InsertDataFromArray($this->TrueTableName, $this->Data);
        if ($result === false) return false;
        $this->LastSql = DB::$queryString;
        return $this->InsertId = DB::GetLastID();
    }

    /**
     * 生成数据对象
     * @param array $data
     * @return Model
     * @Author 孔元元 <system@kyy1996.cn>
     */
    public function Create($data = array())
    {
        /*if (is_object($data)) $this->TableName = get_class($data);
        $this->TrueTableName = $this->Prefix . $this->TableName;*/
        unset($this->Data);
        foreach ($data as $key => $value) {
            if (in_array($key, $this->Fields)) $this->Data[$key] = $value;
            if ($key == $this->PrimaryKey) $this->Where([$this->PrimaryKey => $value]);
        }
        return $this;
    }

    /**
     * 查询条件
     * 可传入条件数组或者字符串（包含'WHERE '）
     * @param array|string $data
     * @return Model
     * @Author 孔元元 <system@kyy1996.cn>
     */
    public function Where($data = [])
    {
        if (!empty($data)) {
            if (!is_string($data)) {
                $where = "";
                foreach ($data as $key => $value) {
                    if (in_array($key, $this->Fields)) {
                        $where .= "`{$key}` = '{$value}' AND ";
                    } else {
                        unset($data[$key]);
                    }
                }
                $where = "WHERE " . substr($where, 0, -5);
                if (!empty($data)) $this->Where = $where;
            } else {
                $this->Where = $data;
            }
        }
        return $this;
    }

    /**
     * 保存数据
     * 必须先使用Where方法传入条件
     * @param array $data
     * @return bool|resource|int
     * @Author 孔元元 <system@kyy1996.cn>
     */
    public function Save($data = [])
    {
        if ($data) $this->Create($data);
        $result = DB::UpdateDataFromArray($this->TrueTableName, $this->Data, $this->Where);
        $this->LastSql = DB::$queryString;
        if ($result === false) return false;
        return $this->AffectedRows = DB::GetAffectedRows();
    }

    /**
     * 删除数据
     * 必须先使用Where方法传入条件
     * @return int|bool
     * @Author 孔元元 <system@kyy1996.cn>
     */
    public function Delete()
    {
        $this->LastSql = "DELETE FROM " . $this->TrueTableName . " " . $this->Where . " " . $this->Order . " " . $this->Limit;
        $result = DB::Execute($this->LastSql);
        if ($result === false) return false;
        return $this->AffectedRows = DB::GetAffectedRows();
    }

    /**
     * @param string $order
     * @return Model
     * @Author 孔元元 <system@kyy1996.cn>
     */
    public function Order($order = "")
    {
        $this->Order = $order;
        return $this;
    }
}