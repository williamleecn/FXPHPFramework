<?php
/**
 * Created by PhpStorm.
 * User: William
 * Date: 2015/8/14
 * Time: 11:03
 */

namespace Web\Framework;


interface  IController
{
    /**
     * 执行前初使化
     * @return mixed
     */
    public function Initialize();

    /**
     * 页面默认执行
     * @return mixed
     */
    public function Execute();


    /**
     * 执行Do的方法
     * @return mixed
     */
    public function ProcessDoAction();

    /**
     * 执行结束后处理
     * @return mixed
     */
    public function ResponseEnd();
} 