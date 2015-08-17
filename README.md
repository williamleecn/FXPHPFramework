# FXPHPFramework

/目录下

    data 目录:配置文件及日志文件存放目录
        Framework.Config.php 配置文件
        Log 日志文件
        Assert 暂时只放验证码的字体文件

    include 目录：核心代码存放目录
        Web.Framework
            Core.Class.php 框架核心
            IController.Class.php  Controller的interface类
            Router.Class.php 路由处理类

        Web.Utils
            DB.Class.php 数据库类，框架必需
            Logger.Class.php 日志类，框架必需
            NDebug.Class.php 调试类，框架必需
            StringUtitly.Class.php 字符串处理类，框架必需
            WebUtils.Class.php 字符串处理类，框架必需
            XRequest.Class.php 请求数据处理类，框架必需


