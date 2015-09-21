<?php
/**
 * Created by PhpStorm.
 * User: William
 * Date: 2015/8/21
 * Time: 19:04
 */

namespace Web\CodeAutomatic;


class ClassAndTemplateMaker
{
    public $IncludeDir;
    public $TemplateDir;
    public $WebController = 'Web.Controller';
    public $TemplateStyle = 'DefaultTheme';

    function __construct($IncludeDir, $TemplateDir)
    {
        $this->IncludeDir = $IncludeDir;
        $this->TemplateDir = $TemplateDir;
    }

    public function GetTemplatePath($type, $controller)
    {

        return $this->TemplateDir . DIRECTORY_SEPARATOR . $this->TemplateStyle . DIRECTORY_SEPARATOR .
        $type . DIRECTORY_SEPARATOR . $controller . '.tpl.php';
    }


    public function GetControllerPath($type, $controller)
    {

        return $this->IncludeDir . DIRECTORY_SEPARATOR . $this->WebController . DIRECTORY_SEPARATOR
        . $type . DIRECTORY_SEPARATOR . $controller . 'Controller.Class.php';
    }

    public function GetControllerList()
    {

        $dir = $this->IncludeDir . DIRECTORY_SEPARATOR . $this->WebController;

        $list = [];
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {

                    if ($file == "." || $file == "..") continue;

                    if (is_dir($dir . DIRECTORY_SEPARATOR . $file)) {
                        $list[] = ['Dir' => $file, 'Controllers' => []];
                    }
                }
                closedir($dh);
            }
        }

        for ($i = 0; $i < count($list); $i++) {

            $dir2 = $dir . DIRECTORY_SEPARATOR . $list[$i]['Dir'];
            if ($dh = opendir($dir2))

                while (($file = readdir($dh)) !== false) {

                    if ($file == "." || $file == "..") continue;

                    if (is_file($dir2 . DIRECTORY_SEPARATOR . $file)) {

                        if (preg_match('/(.*?)Controller\.Class\.php$/', $file, $ma) == 1) {

                            $tpl = $this->GetTemplatePath($list[$i]['Dir'], $ma[1]);

                            $list[$i]['Controllers'][] = [
                                'name' => $ma[1],
                                'tplExist' => file_exists($tpl) ? true : false
                            ];

                        }

                    }
                }

            closedir($dh);
        }

        return $list;


    }


}