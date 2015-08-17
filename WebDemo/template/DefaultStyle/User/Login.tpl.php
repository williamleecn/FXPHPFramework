<h3>HTML</h3>

name:<input name="name">
<BR>
psw:<input name="psw">

<input type="submit" value="登录"><BR>
<?php echo WebRouter::$Context->CurrMenu ?><BR>

-------------------<BR>

<?php echo WebRouter::$Context->data ?>

-------------------<BR>
<?php
foreach(WebRouter::$Context->AllUser as $itm){
?>
    <?php echo $itm['id']?>:<?php echo $itm['name']?><BR>
<?php
}
?>
