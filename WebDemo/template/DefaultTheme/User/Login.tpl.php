<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <script type="text/javascript" src="js/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="js/base.js"></script>
</head>
<body>

<table>
    <tr>
        <td colspan="2"><?php echo WebRouter::$Context->data ?></td>
    </tr>
    <tr>
        <td>name</td>
        <td><input name="name"></td>
    </tr>
    <tr>
        <td>psw</td>
        <td><input name="psw"></td>
    </tr>

    <tr>
        <td colspan="2">
            <button class="submit" type="submit">登录</button>
        </td>
    </tr>

</table>

<script type="text/javascript">

    $('button.submit').click(function (e) {

        JBase.doAction('', 'Login', {
            name: $('input[name="name"]').val(),
            psw: $('input[name="psw"]').val()
        }, function (data, info) {

            if (data.Ret == 0) {
                window.location.href = data.Redirect;
                return;
            }

            alert(data.Msg);


        })


    })


</script>

</body>
</html>



