<?php
/**
 * Created by JetBrains PhpStorm.
 * User: William
 * Date: 13-8-8
 * Time: 下午11:53
 * To change this template use File | Settings | File Templates.
 */

namespace Web\Wechat;

final class MPXML
{

    public static function TextAlert(&$postObj, $msg, $time = null)
    {
        return self::TextAlertEx($postObj->FromUserName, $postObj->ToUserName, $msg, $time);
    }

    public static function TextAlertEx($FromUserName, $ToUserName, $msg, $time = null)
    {

        $doc = self::CreateXML();

        $xml = self::CreateResponseXML($doc, $FromUserName, $ToUserName, 'text', $time);

        self::appendChild($xml, array(
            self::createCDATASection($doc, 'Content', $msg)
        ));

        $doc->appendChild($xml);

        $xmlstring = $doc->saveXML();

        self::XMLEcho($xmlstring);

    }

    /**
     * @param $postObj
     * @param $items array
     * @param null $time
     * @return mixed
     */
    public static function MultiTextAlert(&$postObj, $items, $time = null)
    {
        return self::MultiTextAlertEx($postObj->FromUserName, $postObj->ToUserName, $items, $time);
    }

    /**
     * @param $FromUserName
     * @param $ToUserName
     * @param $items array
     * @param null $time
     */
    public static function MultiTextAlertEx($FromUserName, $ToUserName, $items, $time = null)
    {

        $doc = self::CreateXML();

        $xml = self::CreateResponseXML($doc, $FromUserName, $ToUserName, 'news', $time);

        self::appendChild($xml, array(
            self::createTextNode($doc, 'ArticleCount', count($items))
        ));

        $Doc_Articles = $doc->createElement('Articles');

        foreach ($items as $item) {

            $Doc_item = $doc->createElement('item');

            self::appendChild($Doc_item, array(
                self::createCDATASection($doc, 'Title', $item->getTitle()),
                self::createCDATASection($doc, 'Description', $item->getDescription()),
                self::createCDATASection($doc, 'PicUrl', $item->getPicUrl()),
                self::createCDATASection($doc, 'Url', $item->getUrl()),
            ));

            $Doc_Articles->appendChild($Doc_item);

        }

        $xml->appendChild($Doc_Articles);

        $doc->appendChild($xml);
        $xmlstring = $doc->saveXML();

        self::XMLEcho($xmlstring);

    }

    /**'
     * @param $doc \DOMDocument
     * @param $name
     * @param $val
     * @return \DOMElement
     */
    public static function createCDATASection(&$doc, $name, $val)
    {

        $Doc_item = $doc->createElement($name);
        $Doc_item->appendChild($doc->createCDATASection($val));

        return $Doc_item;

    }

    /**'
     * @param $doc \DOMDocument
     * @param $name
     * @param $val
     * @return \DOMElement
     */
    public static function createTextNode(&$doc, $name, $val)
    {

        $Doc_item = $doc->createElement($name);
        $Doc_item->appendChild($doc->createTextNode($val));

        return $Doc_item;

    }


    /**
     * @param $parent \DOMElement
     * @param $items array
     */
    public static function  appendChild(&$parent, $items)
    {
        foreach ($items as $item) {
            $parent->appendChild($item);
        }
    }


    /**
     * @return \DOMDocument
     */
    public static function CreateXML()
    {
        WebUtils::SetXMLHeader();

        $doc = new \DOMDocument('1.0', 'utf-8');
        $doc->formatOutput = true;

        return $doc;
    }

    /**
     * @param $doc
     * @param $FromUserName
     * @param $ToUserName
     * @param $type
     * @param null $time
     * @return mixed
     */
    public static function CreateResponseXML($doc, $FromUserName, $ToUserName, $type, $time = null)
    {

        $xml = $doc->createElement('xml');

        self::appendChild($xml, array(
            self::createCDATASection($doc, 'ToUserName', $FromUserName),
            self::createCDATASection($doc, 'FromUserName', $ToUserName),
            self::createCDATASection($doc, 'CreateTime', ($time == null ? time() : $time)),
            self::createCDATASection($doc, 'MsgType', $type)
        ));

        return $xml;
    }

    public static function XMLEcho($xmlstring)
    {

        Logger::LogTrack($xmlstring);

        echo $xmlstring;

        exit;
    }

}