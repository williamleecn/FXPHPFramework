
<div class="sidebar-nav">
    <ul>

        <li><a href="Navigation.NavigationList" class="nav-header"><i class="fa fa-fw fa-dashboard"></i>栏目管理</a></li>

        <li><a href="javascript:void(0)" data-target=".tmenuverify" class="nav-header collapsed" data-toggle="collapse"><i class="fa fa-fw fa-legal"></i> 审核管理<i class="fa fa-collapse"></i></a></li>
        <li><ul class="tmenuverify nav nav-list collapse">
                <li class="subitem"><a href="Verify.VerifyList?type=NewList"><span class="fa fa-caret-right"></span> 文章</a></li>
                <li class="subitem"><a href="Verify.VerifyList?type=SinglePage"><span class="fa fa-caret-right"></span> 单页</a></li>
                <li class="subitem"><a href="Verify.VerifyList?type=Job"><span class="fa fa-caret-right"></span> 招聘</a></li>
                <li class="subitem"><a href="Verify.VerifySetting"><span class="fa fa-caret-right"></span> 审核设置</a></li>

            </ul>
        </li>

        <?php
        $list=WebRouter::$Action->GetLeftNavList();
        foreach($list as $item){
        ?>

        <li><a href="javascript:void(0)" data-target=".tmenu<?php echo $item['id']?>" class="nav-header collapsed" data-toggle="collapse"><i class="fa fa-fw fa-briefcase"></i> <?php echo $item['name']?><i class="fa fa-collapse"></i></a></li>
        <li><ul class="tmenu<?php echo $item['id']?> nav nav-list collapse">

                <?php
                foreach($item['subnav'] as $item2){
                    if($item2['type']=='Link')continue;
                ?>
                <li class="subitem<?php echo $item2['id']?>"><a href="Models.Type<?php echo $item2['type']?>?topid=<?php echo $item2['topid']?>&id=<?php echo $item2['id']?>"><span class="fa fa-caret-right"></span> <?php echo $item2['name']?></a></li>
                <?php } ?>

            </ul>
        </li>

        <?php } ?>

        <li><a href="User.ModifyPsw" class="nav-header"><i class="fa fa-fw fa-edit"></i> 修改密码</a></li>
        <li><a href="User.Logout" class="nav-header"><i class="fa fa-fw fa-anchor"></i> 退出</a></li>
    </ul>
</div>