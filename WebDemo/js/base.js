var JBase = {
    doAction: function (url, doAction, arg, callback, errCallBack, complateCallBack, info) {

        if (arg == null)arg = {};
        arg.do = doAction;

        arg._r = 'json';
        arg._v = 100;
        arg._t = new Date().getTime();

        this.doActionCore(url, doAction, arg, callback, errCallBack, complateCallBack, info);

    },
    doActionCore: function (url, doAction, arg, callback, errCallBack, complateCallBack, info) {

        $.ajax({
            url: url,
            type: 'POST',
            async: true,
            data: arg,
            cache: false,
            success: function (data) {

                if (data) {

                    if (data.Ret == 0) {

                        if (callback)callback(data, info);

                        return;
                    }

                    if (errCallBack) {
                        errCallBack(data.Msg, info);
                    }
                    else {
                        alert(data.Msg);
                    }


                } else {
                    if (errCallBack) {
                        errCallBack('系统发生错误', info)
                    }
                    else {
                        alert('系统发生错误')
                    }

                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {

                if (XMLHttpRequest.readyState == 4) {
                    if (errCallBack) {
                        errCallBack('系统发生错误', info)
                    }
                    else {
                        alert('系统发生错误')
                    }
                    return;
                }

                if (errCallBack) {
                    errCallBack('网络似乎不通', info)
                }
                else {
                    alert('网络似乎不通')
                }

            },
            complete: function (jqXHR, textStatus) {
                if (complateCallBack) complateCallBack(info);
            },
            dataType: 'json'
        });

    }
}