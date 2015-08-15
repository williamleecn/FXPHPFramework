<?php
namespace Web\Utils;
/**
 * Created by JetBrains PhpStorm.
 * User: William Lee
 * Date: 13-3-13
 * Time: 上午12:17
 *
 */
class PaginationCore
{

    public $Query;

    public $currpage = 1;
    public $totalrz = -1;
    public $totalpage = 0;
    public $itemperpage = 0;
    public $pdata;
    public $addpar = array();
    public $listpnum = 2;
    public $defpageitems = 10;
    protected $isNormalQuery = true;

    function __construct($query)
    {
        $this->Query = $query;

        DB::Execute($query);

        $this->totalrz = DB::GetTotalRow();

        DB::FreeResult();


    }


    function SetParameter($n, $v)
    {
        array_push($this->addpar, array($n => $v));
    }

    function SetItemsOfPage($c)
    {
        if ($c <= 0) {
            $c = $this->defpageitems;
        }
        $this->itemperpage = $c;

        $this->InitPageNum();

    }


    function InitPageNum()
    {

        if ($this->totalrz <= 0) {
            $this->totalpage = 1;
        } else {
            $tt = $this->totalrz / $this->itemperpage;
            $this->totalpage = ceil($tt);
        }

        if ($this->currpage > $this->totalpage)
            $this->currpage = $this->totalpage;
    }

    function GetPageData()
    {

        if ($this->itemperpage <= 0) $this->itemperpage = $this->defpageitems;

        $query = '';

        $this->currpage = $this->currpage < 0 ? 0 : $this->currpage;
        $this->itemperpage = $this->itemperpage < 0 ? 1 : $this->itemperpage;

        if ($this->isNormalQuery) {

            $query = $this->Query . ' LIMIT ' . (($this->currpage - 1) * $this->itemperpage) . ',' . $this->itemperpage;

        } else {

            $query = str_replace('<-s->', ($this->currpage - 1) * $this->itemperpage, $this->querycp);
            $query = str_replace('<-n->', $this->itemperpage, $query);

        }


        DB::Execute($query);

        $this->InitPageNum();

        $setting = array();

        while ($row = DB::GetArray()) {
            $setting[] = $row;
        }

        $this->pdata = $setting;
        return $this->pdata;
    }

}
