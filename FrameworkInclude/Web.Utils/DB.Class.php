<?php
namespace Web\Utils;

/**
 * 数据库基础类
 */
final class DB
{

    public static $linkID;
    public static $dbPrefix;
    public static $result;
    public static $queryString;
    public static $isOpen = false;
    public static $isClose = false;
    public static $safeCheck = false;
    public static $isDebug = false;
    public static $AlertSQL = false;

    /**
     * 连接数据库
     * @param string $hostUrl
     * @param string $username
     * @param string $pwd
     * @param string $dbname
     * @param string $db_language
     * @return boolean
     */
    public static function Open($hostUrl, $username, $pwd, $dbname, $db_language = 'UTF8')
    {
        if (DB::$isOpen)
            return TRUE;

        //连接数据库
        DB::$linkID = 0;
        DB::$result["me"] = 0;

        $info = parse_url($hostUrl);

        $dbhost = '';

        if (isset($info['host'])) {
            $info['host'];
        } else {
            $dbhost = isset($info['path']) ? $info['path'] : $hostUrl;
        }

        if (!isset($info['port'])) {
            $dbport = 3306;
        } else {
            $dbport = $info['port'];
        }

        DB::$linkID = mysqli_init();

        mysqli_real_connect(DB::$linkID, $dbhost, $username, $pwd, false, $dbport);

        if (mysqli_errno(DB::$linkID) != 0)
            DB::DisplayError('ERROR： DB CONNECTING :' . mysqli_errno(DB::$linkID));


        //处理错误，成功连接则选择数据库
        if (!DB::$linkID) {
            DB::DisplayError('ERROR： DB CONNECTING');
            return false;
        }
        DB::$isOpen = TRUE;

        $serverinfo = mysqli_get_server_info(DB::$linkID);

        if ($serverinfo > '4.1') {
            mysqli_query(DB::$linkID, "SET character_set_connection={$db_language},character_set_results={$db_language},character_set_client=binary");
        }
        if ($serverinfo > '5.0') {
            mysqli_query(DB::$linkID, "SET sql_mode=''");
        }
        if ($dbname && !@mysqli_select_db(DB::$linkID, $dbname)) {
            DB::DisplayError('DATABASE CAN NOT BE USE');
            return false;
        }

        return TRUE;
    }

    //为了防止采集等需要较长运行时间的程序超时，在运行这类程序时设置系统等待和交互时间
    public static function SetLongLink()
    {
        @mysqli_query("SET interactive_timeout=3600, wait_timeout=3600 ;", DB::$linkID);
    }

    //关闭数据库
    public static function Close()
    {
        DB::$isClose = true;
        @mysqli_close(DB::$linkID);
    }

    public static function Esc($_str)
    {
        return @mysqli_real_escape_string(DB::$linkID, $_str);
    }

    public static function DisabledTableKeys($tb)
    {
        if (empty($tb))
            return false;

        $rs = mysqli_query(DB::$linkID, "ALTER TABLE {$tb} DISABLE KEYS;");
        return $rs;
    }

    public static function EnabledTableKeys($tb)
    {
        if (empty($tb))
            return false;

        $rs = mysqli_query(DB::$linkID, "ALTER TABLE {$tb} ENABLE  KEYS;");
        return $rs;
    }

    public static function DisabledAutoCommit()
    {

        $rs = mysqli_query(DB::$linkID, "SET AUTOCOMMIT = 0");
        return $rs;
    }

    public static function EnabledAutoCommit()
    {
        $rs = mysqli_query(DB::$linkID, "SET AUTOCOMMIT = 1");
        return $rs;
    }

    public static function Commit()
    {
        $rs = mysqli_query(DB::$linkID, "commit");
        return $rs;
    }


    /**
     * @param $ClassName string
     * @param $obj mixed|NULL
     * @param $data array
     */

    //执行一个不返回结果的SQL语句，如update,delete,insert等
    public static function Update($sql = '')
    {

        if (!DB::$isOpen && DB::$isClose)
            return FALSE;

        if (empty($sql))
            return FALSE;

        DB::SetQuery($sql);

        //SQL语句安全检查
        if (DB::$safeCheck)
            DB::CheckSql(DB::$queryString, 'update');

        $rs = mysqli_query(DB::$linkID, DB::$queryString);

        return $rs;
    }

    public static function Delete($tableName, $kname, $pkey)
    {

        $tableName = DB::Esc($tableName);
        $kname = DB::Esc($kname);
        $pkey = DB::Esc($pkey);

        return self::Update("DELETE FROM `" . $tableName . "` WHERE `$kname`='$pkey'");
    }

    //执行一个返回影响记录条数的SQL语句，如update,delete,insert等
    public static function Update2($sql = '')
    {
        if (!DB::$isOpen && DB::$isClose)
            return FALSE;

        if (empty($sql))
            return FALSE;

        DB::SetQuery($sql);


        //SQL语句安全检查
        if (DB::$safeCheck)
            DB::CheckSql(DB::$queryString, 'update');

        mysqli_query(DB::$linkID, DB::$queryString);

        return mysqli_affected_rows(DB::$linkID);
    }

    public static function GetFetchRow($id = 'me')
    {
        return @mysqli_fetch_row(DB::$result[$id]);
    }

    public static function GetAffectedRows()
    {
        return mysqli_affected_rows(DB::$linkID);
    }

    public static function ExecuteNoneQuery($sql = '')
    {
        return DB::Update($sql);
    }

    /**
     *
     * 执行一个带返回结果的SQL语句，如SELECT，SHOW等
     *
     * @param string $sql <i style="color:#c00">SQL String</i>
     * @param string $id ProcessID
     * @return boolean
     */
    public static function Execute($sql = '', $id = "me")
    {


        if (!DB::$isOpen && DB::$isClose)
            return FALSE;

        if (!empty($sql)) {
            DB::SetQuery($sql);
        }

        //SQL语句安全检查
        if (DB::$safeCheck)
            DB::CheckSql(DB::$queryString);

        DB::$result[$id] = mysqli_query(DB::$linkID, DB::$queryString);

        if (DB::$result[$id] === FALSE) {
            DB::DisplayError(mysqli_error(DB::$linkID) . " <br />Error sql: <font color='red'>" . DB::$queryString . "</font>");
        }
    }

    public static function CheckSql($sql, $type = '')
    {

    }

    //执行一个SQL语句,返回前一条记录或仅返回一条记录
    public static function GetOne($sql = '', $acctype = MYSQLI_ASSOC)
    {
        if (!DB::$isOpen && DB::$isClose)
            return FALSE;

        if (empty($sql))
            return FALSE;

        //SQL语句安全检查
        if (DB::$safeCheck)
            DB::CheckSql(DB::$queryString . 'select');

        if (!empty($sql) && !preg_match("/LIMIT/i", $sql))
            DB::SetQuery(preg_replace("/[,;]$/i", '', trim($sql)) . " LIMIT 0,1;");
        else
            DB::SetQuery($sql);

        DB::Execute('', "one");

        $arr = DB::GetArray("one", $acctype);

        if (!is_array($arr)) {
            return '';
        }

        @mysqli_free_result(DB::$result["one"]);
        return ($arr);
    }

    //执行一个不与任何表名有关的SQL语句,Create等
    public static function ExecuteSafeQuery($sql, $id = "me")
    {

        if (!DB::$isOpen && DB::$isClose)
            return FALSE;

        if (empty($sql))
            return FALSE;

        DB::$result[$id] = @mysqli_query($sql, DB::$linkID);
    }

    //返回当前的一条记录并把游标移向下一记录
    // MYSQLI_ASSOC、MYSQLI_NUM、MYSQLI_BOTH
    public static function GetArray($id = "me", $acctype = MYSQLI_ASSOC)
    {

        if (DB::$result[$id] === 0) {
            return FALSE;
        }
        return @mysqli_fetch_array(DB::$result[$id], $acctype);
    }


    public static function  GetAllData($query, $id = 'me')
    {

        DB::Execute($query, $id);
        $alldata = array();

        while ($row = DB::GetArray($id)) {
            $alldata[] = $row;
        }
        return $alldata;

    }


    public static function GetObject($id = "me")
    {
        if (DB::$result[$id] === 0) {
            return FALSE;
        } else {
            return mysqli_fetch_object(DB::$result[$id]);
        }
    }

    // 检测是否存在某数据表
    public static function IsTable($tbname)
    {
        if (!DB::$isOpen && DB::$isClose)
            return FALSE;
        $prefix = "#@__";
        $tbname = str_replace($prefix, DB::$dbPrefix, $tbname);
        if (mysqli_num_rows(@mysqli_query(DB::$linkID, "SHOW TABLES LIKE '" . $tbname . "'"))) {
            return TRUE;
        }
        return FALSE;
    }

    //获得MySql的版本号
    public static function GetVersion($isformat = TRUE)
    {

        if (!DB::$isOpen && DB::$isClose)
            return FALSE;

        $rs = mysqli_query(DB::$linkID, "SELECT VERSION();");
        $row = mysqli_fetch_array($rs);
        $mysql_version = $row[0];
        mysqli_free_result($rs);
        if ($isformat) {
            $mysql_versions = explode(".", trim($mysql_version));
            $mysql_version = number_format($mysql_versions[0] . "." . $mysql_versions[1], 2);
        }
        return $mysql_version;
    }

    //获取特定表的信息
    public static function GetTableFields($tbname, $id = "me")
    {

        if (!DB::$isOpen && DB::$isClose)
            return FALSE;

        $prefix = "#@__";
        $tbname = str_replace($prefix, $GLOBALS['cfg_dbprefix'], $tbname);
        $query = "SELECT * FROM {$tbname} LIMIT 0,1";
        DB::$result[$id] = mysqli_query(DB::$linkID, $query);
    }

    //获取字段详细信息
    public static function GetFieldObject($id = "me")
    {
        return mysqli_fetch_field(DB::$result[$id]);
    }

    //获得查询的总记录数
    public static function GetTotalRow($id = "me")
    {
        if (DB::$result[$id] === 0) {
            return -1;
        } else {
            return @mysqli_num_rows(DB::$result[$id]);
        }
    }

    /**
     * 获取上一步INSERT操作产生的ID
     *   如果 AUTO_INCREMENT 的列的类型是 BIGINT，则 mysqli_insert_id() 返回的值将不正确。
     *   可以在 SQL 查询中用 MySQL 内部的 SQL 函数 LAST_INSERT_ID() 来替代。
     *   $rs = mysqli_query(DB::$linkID, "Select LAST_INSERT_ID() as lid");
     *  $row = mysqli_fetch_array($rs);
     *  return $row["lid"];
     * @return int
     */
    public static function GetLastID()
    {
        return mysqli_insert_id(DB::$linkID);
    }

    //释放记录集占用的资源
    public static function FreeResult($id = "me")
    {
        @mysqli_free_result(DB::$result[$id]);
    }

    public static function FreeResultAll()
    {
        if (!is_array(DB::$result)) {
            return '';
        }
        foreach (DB::$result as $vv) {
            if ($vv) {
                @mysqli_free_result($vv);
            }
        }
    }

    //设置SQL语句，会自动把SQL语句里的#@__替换为DB::$dbPrefix(在配置文件中为$cfg_dbprefix)
    public static function SetQuery($sql)
    {
        $prefix = "#@__";
        $sql = str_replace($prefix, DB::$dbPrefix, $sql);
        DB::$queryString = $sql;
    }

    //显示数据链接错误信息
    private static function DisplayError($msg)
    {
        if (DB::$isDebug) {
            echo $msg;
        }
    }

    /**
     * @param $DBTable
     * @param array $datas
     */
    public static function GetOneFromArray($DBTable, $datas = [])
    {

        $sql = "SELECT * FROM `$DBTable` ";

        if (count($datas) > 0) {

            $sql .= 'WHERE ';
            $index = 0;
            foreach ($datas as $key => $val) {
                $sql .= '`' . self::Esc($key) . '`=';

                if ($val === null) {
                    $sql .= 'NULL';
                } else {
                    $sql .= '\'' . self::Esc($val) . '\'';
                }

                $index++;

                if ($index < count($datas)) {
                    $sql .= ' AND ';
                }
            }

        }

        if (self::$AlertSQL) {
            die($sql);
        }
        
        return DB::GetOne($sql);


    }

    /**
     * @param $DBTable
     * @param array $datas
     */

    /**
     * @param $DBTable
     * @param array $datas
     */
    public static function GetAllDataFromArray($DBTable, $datas = [])
    {
        $sql = "SELECT * FROM `$DBTable` ";

        $where = self::BuildWhereFromArray($datas);

        return DB::GetAllData($sql . $where);
    }

    public static function BuildWhereFromArray($datas = [])
    {
        $sql = '';

        if (count($datas) > 0) {
            $sql .= 'WHERE ';
            $index = 0;
            foreach ($datas as $key => $val) {
                $sql .= '`' . self::Esc($key) . '`=';
                if ($val === null) {
                    $sql .= 'NULL';
                } else {
                    $sql .= '\'' . self::Esc($val) . '\'';
                }
                $index++;
                if ($index < count($datas)) {
                    $sql .= ' AND ';
                }
            }
        }
        return $sql;
    }


    public static function UpdateDataFromArrayKeyVal($DBTable, $datas, $skey, $sval)
    {

        $sql = "UPDATE `$DBTable` SET ";


        $index = 0;
        foreach ($datas as $key => $val) {
            $sql .= '`' . self::Esc($key) . '`=';

            if ($val === null) {
                $sql .= 'NULL';
            } else {
                $sql .= '\'' . self::Esc($val) . '\'';
            }

            $index++;

            if ($index < count($datas)) {
                $sql .= ',';
            }
        }
        $skey = self::Esc($skey);
        $sval = self::Esc($sval);

        $sql .= " `$skey`='$sval'";

        return self::Update($sql);
    }

    public static function UpdateDataFromArray($DBTable, $datas, $where)
    {

        $sql = "UPDATE `$DBTable` SET ";


        $index = 0;
        foreach ($datas as $key => $val) {
            $sql .= '`' . self::Esc($key) . '`=';

            if ($val === null) {
                $sql .= 'NULL';
            } else {
                $sql .= '\'' . self::Esc($val) . '\'';
            }

            $index++;

            if ($index < count($datas)) {
                $sql .= ',';
            }
        }

        $sql .= ' ' . $where;

        return self::Update($sql);

    }

    /**
     * @param $DBTable
     * @param $datas
     * @return bool|\mysqli_result
     */
    public static function InsertDataFromArray($DBTable, $datas)
    {

        $sql = "INSERT INTO `$DBTable` ";

        $keys = '(';
        $vals = '(';

        $index = 0;
        foreach ($datas as $key => $val) {

            $keys .= '`' . self::Esc($key) . '`';
            if ($val === null) {
                $vals .= 'NULL';
            } else {
                $vals .= '\'' . self::Esc($val) . '\'';
            }

            $index++;

            if ($index < count($datas)) {
                $keys .= ',';
                $vals .= ',';
            }

        }

        $keys .= ')';
        $vals .= ')';

        $sql .= $keys . ' VALUES ' . $vals;

        if (self::$AlertSQL) {
            die($sql);
        }

        return self::Update($sql);

    }

    /**
     *
     * 自动填充数组到含有geeter,setter的类中
     *
     * @param $data array 数组,Key要对应Bean中名称,不区分大小
     * @param $Bean object Bean类,需要已经实例化
     * @return bool
     */
    public static function FillDataToBean($data, &$Bean)
    {

        //一定是必需是一个已经实例化的实例,不能为null
        if (!is_object($Bean)) return false;

        //获得实例所属类的函数名
        $ClassName = get_class($Bean);

        $class = new \ReflectionClass($ClassName); //

        $fields = $class->getMethods(\ReflectionMethod::IS_PUBLIC); //

        foreach ($fields as $method) {

            $name = $method->getName();

            $prefix = substr($name, 0, 3);
            $property = substr($name, 3);

            if ($prefix == 'set') {

                foreach ($data as $key => $val) {

                    if (strtoupper($key) == strtoupper($property)) {
                        $method->invoke($Bean, $val);
                    }

                }

            }


        }

        return true;
    }

}
