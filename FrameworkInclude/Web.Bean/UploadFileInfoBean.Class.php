<?php
/**
 * Created by PhpStorm.
 * User: William
 * Date: 13-11-25
 * Time: 下午3:02
 */

namespace Web\Bean;


class UploadFileInfoBean
{
    private $FileName;
    private $FileNameNoExt;
    private $FielExt;

    private $FileType;
    private $FileSize = 0;
    private $TmpName;
    private $ErrorNum = 0;

    /**
     * @param int $ErrorNum
     */
    public function setErrorNum($ErrorNum)
    {
        $this->ErrorNum = $ErrorNum;
    }

    /**
     * @return int
     */
    public function getErrorNum()
    {
        return $this->ErrorNum;
    }

    /**
     * @param mixed $FielExt
     */
    public function setFielExt($FielExt)
    {
        $this->FielExt = $FielExt;
    }

    /**
     * @return mixed
     */
    public function getFielExt()
    {
        return $this->FielExt;
    }

    /**
     * @param mixed $FileName
     */
    public function setFileName($FileName)
    {
        $this->FileName = $FileName;
    }

    /**
     * @return mixed
     */
    public function getFileName()
    {
        return $this->FileName;
    }

    /**
     * @param mixed $FileNameNoExt
     */
    public function setFileNameNoExt($FileNameNoExt)
    {
        $this->FileNameNoExt = $FileNameNoExt;
    }

    /**
     * @return mixed
     */
    public function getFileNameNoExt()
    {
        return $this->FileNameNoExt;
    }

    /**
     * @param int $FileSize
     */
    public function setFileSize($FileSize)
    {
        $this->FileSize = $FileSize;
    }

    /**
     * @return int
     */
    public function getFileSize()
    {
        return $this->FileSize;
    }

    /**
     * @param mixed $FileType
     */
    public function setFileType($FileType)
    {
        $this->FileType = $FileType;
    }

    /**
     * @return mixed
     */
    public function getFileType()
    {
        return $this->FileType;
    }

    /**
     * @param mixed $TmpName
     */
    public function setTmpName($TmpName)
    {
        $this->TmpName = $TmpName;
    }

    /**
     * @return mixed
     */
    public function getTmpName()
    {
        return $this->TmpName;
    }


}