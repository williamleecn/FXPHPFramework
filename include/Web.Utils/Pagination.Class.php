<?php
namespace Web\Utils;


/**
 * William
 *
 */
final class Pagination extends PaginationCore
{

    function __construct($query){
        parent::__construct($query);

    }

    //获取分页导航列表
    function GetPageList()
    {

        $prevpage = $this->currpage - 1;
        $nextpage = $this->currpage + 1;

        $strget = '';
        foreach ($this->addpar as $v2) {
            foreach ($v2 as $k => $v)
                $strget .= "$k=$v&";
        }


        $dd = '<ul class="pagination">';
        if ($prevpage < 1) {
            //$dd .= '<li class="prev"><a>← 上一页</a></li>';
        } else {
            $dd .= '<li class="prev"><a href="?' . $strget . 'page=' . $prevpage . '">← 上一页</a></li>';
        }

        $fromp = 1;
        $topz = 0;
        $listc = $this->listpnum;

        if ($this->currpage > $this->totalpage)
            $this->currpage = $this->totalpage;

        if ($this->totalpage <= $listc) {
            $fromp = 1;
            $topz = $this->totalpage;
        } else {

            if (($this->currpage + $listc) <= $this->totalpage) {
                $fromp = $this->currpage;
                $topz = $this->currpage + $listc;
            } else {
                $fromp = $this->totalpage - $listc;
                $topz = $this->totalpage;
            }
        }

        for ($i = $fromp; $i <= $topz; $i++) {

            if ($i == $this->currpage) {
                $dd .= '<li class="active"><a>' . $i . '</a></li> ';
            } else {
                $dd .= '<li><a href="?' . $strget . 'page=' . $i . '">' . $i . '</a></li>';
            }
        }


        if ($nextpage > $this->totalpage) {
           // $dd .= '<li class="next"><a>下一页 → </a></li>';
        } else {
            $dd .= '<li class="next"><a href="?' . $strget . 'page=' . $nextpage . '">下一页 → </a></li>';
        }

        $dd.='</ul>';


        return $dd;
    }

}
