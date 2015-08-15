<?php
/**
 * Created by PhpStorm.
 * User: William
 * Date: 13-11-25
 * Time: 下午2:59
 */

namespace Webs\Utils;


use Webs\Bean\UploadFileInfoBean;

final class FSUtils
{

    public static $LastError = '';

    /**
     * @param $UploadName
     * @return bool|UploadFileInfoBean
     */
    public static function GetUploadFileInfo($UploadName)
    {

        self::ClearError();

        if (!isset($_FILES[$UploadName])) {

            self::SetError('upload name not found!');
            return false;
        }

        return self::CheckInfo($_FILES[$UploadName]);


    }


    public static function CheckInfo($info)
    {

        $InfoBean = self::FormatUplaodFileInfo($info);

        if ($InfoBean == null) {
            self::SetError('upload occur error');
            return false;
        }

        if ($InfoBean->getErrorNum() > 0) {
            self::SetError('upload occur error:' . $InfoBean->getErrorNum());
            return false;
        }

        return $InfoBean;
    }

    /**
     * @param $info
     * @return null|UploadFileInfoBean
     */
    public static function FormatUplaodFileInfo($info)
    {

        $InfoBean = new UploadFileInfoBean();

        if (!is_array($info)) return null;

        if (!isset($info['error'])) return null;

        $InfoBean->setErrorNum($info["error"]);

        if ($InfoBean->getErrorNum() > 0) return $InfoBean;


        $InfoBean->setFileName(basename($info["name"]));


        $InfoBean->setFielExt(pathinfo($InfoBean->getFileName(), PATHINFO_EXTENSION));

        $pos = strrpos($InfoBean->getFileName(), '.');

        if ($pos === false) {
            $InfoBean->setFileNameNoExt($InfoBean->getFileName());

        } else {
            $InfoBean->setFileNameNoExt(substr($InfoBean->getFileName(), 0, strrpos($InfoBean->getFileName(), '.')));

        }

        $InfoBean->setFileType($info["type"]);
        $InfoBean->setFileSize($info["size"]);
        $InfoBean->setTmpName($info["tmp_name"]);

        return $InfoBean;

    }


    public static function CheckFileExtension($ext, $extArray = array())
    {
        foreach ($extArray as $item) {
            if (strtoupper($ext) == strtoupper($item)) return true;
        }
        return false;

    }

    /**
     * @param $InfoBean UploadFileInfoBean
     * @param $path
     * @param bool $Rename
     * @return bool |string
     */
    public static function SaveUploadFile($InfoBean, $path, $Rename = true, $DirPerMon = null)
    {

        if ($InfoBean == null) return false;

        if (!($InfoBean instanceof UploadFileInfoBean)) {
            return false;
        }

        $filename = $InfoBean->getFileNameNoExt() . '.' . $InfoBean->getFielExt();
        if ($Rename) {
            $filename = StringUtitly::FormatTimestamp(time(), 'YmdHis') . mt_rand(1000, 9999) . '.' . $InfoBean->getFielExt();
        }

        if (!empty($DirPerMon)) {
            $path .= '/' . $DirPerMon;
            if (!is_dir($path)) @mkdir($path, 0777);
        }

        $path .= '/' . $filename;

        if (@move_uploaded_file($InfoBean->getTmpName(), $path) == false) {
            return false;
        }

        return $filename;

    }


    public static function ClearError()
    {
        self::$LastError = '';
    }


    public static function SetError($str)
    {
        self::$LastError = $str;
    }

    public static function mkdirs($dir)
    {
        if (!is_dir($dir)) {
            if (!self::mkdirs(dirname($dir))) {
                return false;
            }
            if (!mkdir($dir)) {
                return false;
            }
        }
        return true;
    }

} 