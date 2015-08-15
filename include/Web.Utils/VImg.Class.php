<?php
/**
 * Created by PhpStorm.
 * User: William
 * Date: 2015/1/21
 * Time: 20:41
 */

namespace Web\Utils;


class VImg
{
    const TYPE_NUMBER_ONLY = 0;
    const TYPE_LETTER_ONLY = 1;
    const TYPE_NUMBER_LETTER = 2;

    public $CodeValue = '';

    public $config = array(
        'font_size' => 12,
        'img_height' => 0,
        'word_type' => self::TYPE_NUMBER_ONLY,
        'img_width' => 0,
        'use_boder' => true,
        'font_file' => array(),

    );

    public $EnableFilter = false;

    public function __construct($fonts, $fontsize, $width, $heigt, $words, $type = self::TYPE_NUMBER_ONLY)
    {
        $this->config['font_file'] = $fonts;
        $this->config['font_size'] = $fontsize;
        $this->config['img_height'] = $heigt;
        $this->config['img_width'] = $width;
        $this->config['word_type'] = $type;
        $this->config['word_count'] = $words;

    }


    public function CreateImage()
    {

        //创建图片，并设置背景色
        $im = @imagecreate($this->config['img_width'], $this->config['img_height']);

        imagecolorallocate($im, 255, 255, 255);

        //文字随机颜色
        $fontColor[] = imagecolorallocate($im, 0x15, 0x15, 0x15);
        $fontColor[] = imagecolorallocate($im, 0x95, 0x1e, 0x04);
        $fontColor[] = imagecolorallocate($im, 0x93, 0x14, 0xa9);
        $fontColor[] = imagecolorallocate($im, 0x12, 0x81, 0x0a);
        $fontColor[] = imagecolorallocate($im, 0x06, 0x3a, 0xd5);

        //获取随机字符
        $rndstring = '';

        $chars_number = '0123456789';
        $chars_letter = 'abcdefghigklmnopqrstuvwxwyABCDEFGHIGKLMNOPQRSTUVWXWY';
        $chars_all = $chars_letter . $chars_number;

        $chars = $chars_number;

        if ($this->config['word_type'] == self::TYPE_LETTER_ONLY) {
            $chars = $chars_letter;
        } elseif ($this->config['word_type'] == self::TYPE_NUMBER_LETTER) {
            $chars = $chars_all;
        }

        $rndstring = '';
        $length = $this->config['word_count'];

        $max = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rndstring .= $chars[mt_rand(0, $max)];
        }


        $this->CodeValue = strtolower($rndstring);

        //背景横线
        $lineColor1 = imagecolorallocate($im, 0xda, 0xd9, 0xd1);
        for ($j = 3; $j <= $this->config['img_height'] - 3; $j = $j + 3) {
            imageline($im, 2, $j, $this->config['img_width'] - 2, $j, $lineColor1);
        }

        //背景竖线
        $lineColor2 = imagecolorallocate($im, 0xda, 0xd9, 0xd1);
        for ($j = 2; $j < $this->config['img_width']; $j = $j + 6) {
            imageline($im, $j, 0, $j + 8, $this->config['img_height'], $lineColor2);
        }

        //画边框
        if ($this->config['use_boder']) {
            $bordercolor = imagecolorallocate($im, 0x9d, 0x9e, 0x96);
            imagerectangle($im, 0, 0, $this->config['img_width'] - 1, $this->config['img_height'] - 1, $bordercolor);
        }

        //输出文字
        $lastc = '';

        $font_file = $this->config['font_file'][mt_rand(0, count($this->config['font_file']) - 1)];

        for ($i = 0; $i < $length; $i++) {
            $bc = mt_rand(0, 1);
            $rndstring[$i] = strtoupper($rndstring[$i]);
            $c_fontColor = $fontColor[mt_rand(0, 4)];
            $y_pos = $i == 0 ? 5 : $i * ($this->config['font_size']);
            $c = mt_rand(-30, 30);
            @imagettftext($im, $this->config['font_size'], $c, $y_pos, 30, $c_fontColor, $font_file, $rndstring[$i]);
            $lastc = $rndstring[$i];
        }

        //图象效果

        if ($this->EnableFilter) {

            $filter_type = mt_rand(0, 4);

            switch ($filter_type) {
                case 0:
                    imagefilter($im, IMG_FILTER_GAUSSIAN_BLUR);
                    break;
                case 1:
                    imagefilter($im, IMG_FILTER_NEGATE);
                    break;
                case 2:
                    imagefilter($im, IMG_FILTER_EMBOSS);
                    break;
                case 3:
                    imagefilter($im, IMG_FILTER_EDGEDETECT);
                    break;
                default:
                    break;
            }

        }

        return $im;
    }


    public function EchoImage($im)
    {
        header("Pragma:no-cache\r\n");
        header("Cache-Control:no-cache\r\n");
        header("Expires:0\r\n");

        if (function_exists("imagejpeg")) {
            header("content-type:image/jpeg\r\n");
            imagejpeg($im);
        } else {
            header("content-type:image/png\r\n");
            imagepng($im);
        }
        imagedestroy($im);
    }

}