<?php
function GetUploadBox($fr, $pic,$w=200,$h=140)
{
    ?>
    <div style="height: 150px;" class="uploadlox <?php echo $fr ?>">
        <input type="hidden" name="<?php echo $fr ?>" value="<?php echo $pic ?>">

        <div style="width: 200px; height: 140px; float: left">
            <img class="pic" height="<?php echo $h?>" width="<?php echo $w?>" src="<?php echo empty($pic) ? '/images/170x170.gif' : $pic ?>">
        </div>
        <div style="width: 120px; height: 80px; float: left; padding-left: 10px;">
            <a href="javascript:UploadBox.ShowUploadBox('<?php echo $fr ?>')" style="margin-top: 50px;"
               class="btn btn-primary"><span class="fa fa-plus-square-o"></span> 上传新图片</a>

            <a href="javascript:void" onclick="$(this).parents('.uploadlox').find('input').val('');$(this).parents('.uploadlox').find('img').attr('src','/images/170x170.gif');"
               style="margin-top: 5px;"
               class="btn btn-danger"><span class="fa fa-magic"></span> 清除图片</a>
        </div>
        <div style="clear: both"></div>
    </div>
<?php
}
?>

<?php
function GetUploadBox2($fr)
{
    ?>
    <div style="height: 80px;" class="uploadlox <?php echo $fr ?>">
        <div style="width: 120px; height: 30px; float: left; padding-left: 10px;">
            <a href="javascript:UploadBox.ShowUploadBox('<?php echo $fr ?>')" style="margin-top: 25px;"
               class="btn btn-primary"><span class="fa fa-plus-square-o"></span> 上传新图片</a>
        </div>
        <div style="clear: both"></div>
    </div>
<?php
}
?>


<?php
function GetUploadBoxFrame()
{
    ?>

    <div class="uploadbox_bg"
         style="left: 0; top: 0; background: #fff; bottom: 0; right: 0; position: absolute; opacity: 0.6; z-index: 10001; display: none"></div>
    <div class="uploadbox_con"
         style="top: 100px; left: 100px; width: 350px; height: 200px; z-index: 10002; position: absolute; display: none;background: #F4F4F4">
        <iframe src="javascript:void(0)" frameborder="0" style="width: 350px; height: 200px;"></iframe>
    </div>
<?php
}

?>