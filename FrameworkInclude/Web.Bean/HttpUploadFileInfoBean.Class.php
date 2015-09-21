<?php
/**
 * Created by PhpStorm.
 * User: William
 * Date: 2015/8/21
 * Time: 12:21
 */

namespace Web\Bean;


class HttpUploadFileInfoBean
{

    private $fieldName;
    private $fileFullName;
    private $contentType;
    private $fileName;
    private $fileLength;
    private $data;

    /**
     * @return mixed
     */
    public function getFileFullName()
    {
        return $this->fileFullName;
    }

    /**
     * @param mixed $fileFullName
     */
    public function setFileFullName($fileFullName)
    {
        $this->fileFullName = $fileFullName;
    }

    /**
     * @return mixed
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param mixed $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @param mixed $fieldName
     */
    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;
    }

    /**
     * @return mixed
     */
    public function getFileLength()
    {
        return $this->fileLength;
    }

    /**
     * @param mixed $fileLength
     */
    public function setFileLength($fileLength)
    {
        $this->fileLength = $fileLength;
    }

    /**
     * @return mixed
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param mixed $fileName
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }


}