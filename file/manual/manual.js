$(function(){

    var title=[
        'EQPHP简介',
        '获取安装EQPHP、环境配置、框架初始化',
        '命名规范与相关约束',
        '案例：留言本',
        '捐赠',
        '加入我们',
        '目录结构与说明',
        '调用流程与执行原理',
        '路由（route - url）',
        '控制器（action）',
        '视图（view）',
        '模型（model）与业务逻辑（server）',
        '组件（plugin）',
        '工具类（library）、快捷函数（shortcut）',
        '数据库（database）操作',
        '请求（request）与响应（response）',
        '数据输入（input）与验证过滤（validate）',
        '文件上传（upload）、下载（download）、文件目录（directory）操作',
        '构建DOM（html）、表单（form）',
        'session、cookie、memcache、redis、文件缓存（cache）静态化',
        '邮件（mail）、图像（image）、加密（encrypt）',
        '日志（logger）、性能、错误调试（debug）',
        'NoSQL技术（mongoDB）',
        '视图（view） - 模板引擎（template）',
        'java-api代理（proxy）、server-api开发（restful）',
        '数据传输与安全',
        'web模式、cli模式、计划任务（crontab）、监听队列（queue）',
        'websocket、消息推送、即时聊天',
        '测试驱动开发（TDD）、单元测试（unit test）',
        '架构方案、项目部署',
        '框架常量一览表',
        '工具类（函数）速查手册',
        '框架自带组件类',
        '系统错误、异常一览表'
    ];

    var article_id=parseInt(location.hash.substr(1,2));
    if (isNaN(article_id)) {
        article_id=1;
    }
    $('.main h1 strong').html(title[article_id-1]);
    $('.main .item:eq('+(article_id-1)+')').show();    

    //$('.main h1 strong').html(title[0]);
    //$('.main .item:eq(0)').show();

    $('.nav_title').mouseover(function(){
        $('.nav_title dl dd').show();
    }).mouseleave(function(){
        $('.nav_title dl dd').hide();
    });

    $('.nav_title dl dd').each(function(i){
        $(this).click(function(){
            $('.nav_title dl dd').css('color','#005EAC');
            $(this).css('color','red');

            $('.main h1 strong').html(title[i]);            
            $('.main .item').hide();
            $('.main .item:eq('+i+')').show();
        }).mouseover(function(){

            var now_color=$(this).css("color");
            if (now_color=='rgb(0, 94, 172)' || now_color=='#005eac') {
                $(this).css('color','#FE5F1B');
            }

        }).mouseout(function(){

            var now_color=$(this).css("color");
            if (now_color=='rgb(254, 95, 27)' || now_color=='#fe5f1b') {
                $(this).css('color','#005EAC');
            }

        });
    });

});
