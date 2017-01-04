$(function(){

    var title=[
        'EQPHP简介',
        '获取安装EQPHP、环境配置、框架初始化',
        '命名规范与相关约束',
        '案例：留言本',
        '疑难解答、建议反馈',
        '加入我们',
        '目录结构与说明',
        '调用流程与执行原理',
        '路由（route - url）',
        '控制器（action）',
        '视图（view）',
        '模型（model）与业务逻辑（server）',
        '组件（plugin）',
        '工具类（class）、函数方法（common）',
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
        '国际化（I18N）、多语言操作界面',
        'web模式、cli模式、计划任务（crontab）',
        '测试驱动开发（TDD）、单元测试（unit test）',
        '架构方案、项目部署',
        '框架常量一览表',
        '框架全局函数',
        '工具类（函数）速查手册',
        '框架自带组件类',
        '系统错误、异常一览表'
    ];

    
    var article_id=parseInt(location.hash.substr(1,2));
    if (isNaN(article_id)) {
        article_id=parseInt(cookie.get('current_article_id'));
        article_id=isNaN(article_id) ? 1 : article_id;
    }
    $('.main h1 strong').html(title[article_id-1]);
    $('.main .item:eq('+(article_id-1)+')').show();
    get_count_info(article_id);



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

            cookie.set('current_article_id',i+1);

            get_count_info(i+1);
            $('.main h1').attr('article_id',i+1);
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


    $('.main h1 a').each(function(j){
        $(this).click(function(){

            article_id=$('.main h1').attr('article_id');

            //投票
            if (j<2) {
                var vote_list = cookie.get('vote_list');
                var now_vote = parseInt($(this).html());
                var now_object = $(this);
                var attitude = j ? 'support' : 'against';
                var cookie_value = article_id;

                if (vote_list) {
                    var vote_option = vote_list.split(',');
                    if (in_array(article_id, vote_option)) {
                        var i = get_random(3);
                        var tip = ['您已经投过票了', '哇塞，不用这么勤快！', '请给其它资源投票！'];
                        $('#vote_tip').html(tip[i]).css('color', 'red').show().fadeOut(3000);
                        return false;
                    }
                    cookie_value = vote_list + ',' + article_id;
                }
                vote(now_object, now_vote, article_id, attitude, cookie_value);
            }

            //评论
            if (j===2) {
                $('#comment_data').html('');
                article_id=$('.main h1').attr('article_id');
                get_comment(article_id, 1);
                $('.comment_block').show();
                $('html,body').animate({scrollTop: $('.comment_block').offset().top}, 1000);
            }

        });

    });


    //加载更多评论
    $('#load_more button').click(function () {
        var page = parseInt($(this).attr('_page'));
        $(this).attr('_page', page + 1);
        if (page < 2) {
            $("html,body").animate({scrollTop: $('.comment_block').offset().top}, 1000);
        } else {
            if (page === 2) {
                $('.comment_item').remove();
            }
            article_id=$('.main h1').attr('article_id');
            get_comment(article_id, page);
        }
    });

    //显示评论回复表单
    $('.comment_time, .reply_main').live('mousemove', function () {
        $(this).find('.reply_btn').show();
    }).live('mouseleave', function () {
        $(this).find('.reply_btn').hide();
    });

    //提交评论
    occurring.keyup('.comment_textarea|.pub_result_tip', '3|300', 'tip_number');
    $('#comment_form').submit(function () {
        var tip_object = $('.pub_result_tip');
        var comment = $('.comment_textarea').val();
        article_id=$('.main h1').attr('article_id');

        if (comment.length < 3) {
            tip_object.html('<span class="fcr">评论内容不能少于3个字！</span>');
            return false;
        }

        $.post(system.url + 'comment/save/blog/'+article_id, {'comment': comment}, function (response) {

            var code = response.error;
            if (code == 0) {
                var comment_num = parseInt($('.vote_num:eq(2)').html());
                $('.vote_num:eq(2)').html(comment_num + 1);
                $('.comment_textarea').val('');
                tip_object.html('<span class="fcg">非常感谢您的评论！</span>');

                var html = '<div class="comment_item">';
                html += '<div class="avatar_50"><img src="' + avatar_url + '50/' + response.data.user.avatar + '"></div>';
                html += '<div class="comment_main" id="reply_comment_' + response.data.comment.comment_id + '">';
                html += '<div class="comment_detail"><a href="'+system.url+'blog/list/uid_' + response.data.user.id + '">' + response.data.user.nick_name + '</a> : ' + response.data.comment.comment + '</div>';
                html += '<div class="comment_time"><span>刚刚</span></div></div>';

                $('#comment_data').append(html);
                return false;
            }

            if (code == 2) {
                var now_height = $("body").css("height");
                $('.big_lay').css('height', now_height).show();
                return false;
            }
            Array.prototype.push.apply(show_message, ['没有评论主题', '评论内容请保持在3-300字之间', '不可以给自己评论', '评论保存失败']);
            tip_object.html('<span class="fcr">' + show_message[response.error] + '</span>');
        }, "json");

        return false;
    });


    //赞评论
    $('.praise_btn').live('click', function () {
        var now_object = $(this);
        var cookie_value = now_object.attr('comment_id');
        var praise_list = cookie.get('praise_list');
        var now_praise = parseInt(now_object.html());
        var comment_id = now_object.attr('comment_id');

        if (praise_list) {
            var praise_option = praise_list.split(',');
            if (in_array(comment_id, praise_option)) {
                now_object.css('color', 'red').html('您已赞过');
                return false
            }
            cookie_value = praise_list + ',' + comment_id;
        }
        article_id=$('.main h1').attr('article_id');
        praise(now_object, now_praise, article_id, comment_id, cookie_value);
    }).live('mouseout', function () {
        var now_praise = $(this).attr('support');
        $(this).show().text(now_praise);
    });


    //回复评论
    occurring.keyup('.reply_textarea|.reply_result_tip', '1|140', 'tip_number');
    $('.reply_btn').live('click', function () {
        var now_object = $(this);
        var i = $('.reply_btn').index($(this));
        $('.reply_form').html('');
        $('.reply_result_tip').html('140字以内神回复');
        $('.reply_textarea').css('width',now_object.attr('_width'));
        $('.reply_form:eq(' + i + ')').html($('#reply_form_html').html());

        $('#reply_form').submit(function () {
            var reply_info = $.trim($('.reply_textarea').val());
            if (reply_info.length < 1) {
                $('.reply_result_tip').html('<span class="fcr">回复内容不能为空</span>');
                return false;
            }
            var receiver = now_object.attr('user_id');
            var comment_id = now_object.attr('comment_id');
            article_id=$('.main h1').attr('article_id');
            var json = {'comment_id': comment_id, 'receiver': receiver, 'reply': reply_info};
            $.post(system.url + 'comment/reply/blog/'+article_id, json, function (response) {
                var code=response.error;
                if (code == 0) {
                    $('.reply_textarea').val('');
                    $('.reply_result_tip').html('<span class="fcg">非常感谢您的回复</span>');

                    var html = '<div class="comment_reply">';
                    html += '<div class="avatar_30"><img src="' + avatar_url + '30/' + response.data.user.avatar + '"></div>';
                    html += '<div class="reply_main">';
                    html += '<div class="reply_info"><a href="'+system.url+'blog/list/uid_' + response.data.user.id + '">' + response.data.user.nick_name + '</a> 回复 <a href="'+system.url+'blog/list/uid_' + response.data.receiver.id + '">' + response.data.receiver.nick_name + '</a> : ' + response.data.reply.reply + '</div>';
                    html += '<div class="reply_time"><span>刚刚</span></div>';
                    html += '</div></div>';

                    $('#reply_comment_' + comment_id).append(html);
                } else {

                    if (code == 2) {
                        var now_height = $("body").css("height");
                        $('.big_lay').css('height', now_height).show();
                        return false;
                    }
                    Array.prototype.push.apply(show_message, ['没有回复主题','待回复的评论不存在', '不能回复自己的评论', '回复内容请保持在1-240字之间', '回复失败']);
                    $('.reply_result_tip').html('<span class="fcr">' + show_message[response.error] + '</span>');
                }
            }, "json");
            return false;
        });

    });



});

//投票方法
function vote(now_object, now_vote, article_id, attitude, cookie_value) {
    $.post(system.url + 'comment/vote/blog/' + article_id, {'attitude': attitude}, function (response) {
        if (response.error == 0) {
            now_object.html(now_vote + 1);
            cookie.set('vote_list', cookie_value);
            var successful_tip = {'against': '谢谢专家，我们会倍加进取！', 'support': '感谢支持，我们会再接再励！'};
            $('#vote_tip').html(successful_tip[attitude]).css('color', 'green').show().fadeOut(3000);
        } else {
            Array.prototype.push.apply(show_message, ['没有投票主题', '只允许拍砖或送鲜花', '您已投过票，不能重复投票', '投票失败']);
            $('#vote_tip').html(show_message[response.error]).css('color','red').show().fadeOut(3000);
        }
    }, "json");
}

//赞评论
function praise(now_object, now_praise, article_id, comment_id, cookie_value) {
    $.post(system.url + 'comment/praise/blog/' + article_id, {'comment_id': comment_id}, function (response) {
        if (response.error == 0) {
            cookie.set('praise_list', cookie_value);
            now_object.html(now_praise + 1).attr('support', now_praise + 1);
            return true;
        }
        now_object.html('您已赞过');
    }, "json");
}

//获取评论
function get_comment(article_id, page) {
    $.get(system.url + 'comments/get/blog/' + article_id + '/' + page, '', function (response) {
        if (response.error) {
            return false;
        }

        if (response.data.page_amount < 2) {
            $('#load_more').hide();
        } else {
            $('#load_more').show();
        }

        if (response.data.comment_amount) {
            if (response.data.page_amount == (page-1)) {
                $('#load_more').hide();
            } else {
                $('#load_more button').html('加载更多评论...');
            }

            var html = '';
            var user = response.data.user;
            var user_id = $('input[name="user_id"]').val();
            var comments = response.data.comment;
            var replies = response.data.reply;
            $.each(comments, function (index, comment) {
                html += '<div class="comment_item">';
                html += '<div class="avatar_50"><img src="' + avatar_url + '50/' + user[comment.user_id].avatar + '"></div>';
                html += '<div class="comment_main" id="reply_comment_' + comment.id + '">';
                html += '<div class="comment_detail"><a href="'+system.url+'blog/list/uid_' + comment.user_id + '">' + user[comment.user_id].nick_name + '</a> : ' + comment.comment + '</div>';
                html += '<div class="comment_time">';
                html += '<span>' + comment.time + '&#12288;&#12288;赞(<a target="_self" href="javascript:void(0);" class="praise_btn" comment_id="' + comment.id + '" support="' + comment.support + '">' + comment.support + '</a>)</span>';
                if (comment.user_id != user_id) {
                    html += '<a target="_self" href="javascript:void(0);" _width="580px" user_id="' + comment.user_id + '" comment_id="' + comment.id + '" class="hide reply_btn">回复</a></div><div class="reply_form"></div>';
                }
                if (replies[comment.id]) {
                    $.each(replies[comment.id], function (key, reply) {
                        html += '<div class="comment_reply">';
                        html += '<div class="avatar_30"><img src="' + avatar_url + '30/' + user[reply.sender].avatar + '"></div>';
                        html += '<div class="reply_main">';
                        html += '<div class="reply_info"><a href="'+system.url+'blog/list/uid_' + reply.sender + '">' + user[reply.sender].nick_name + '</a> 回复 <a href="'+system.url+'blog/list/uid_' + reply.receiver + '">' + user[reply.receiver].nick_name + '</a> : ' + reply.reply + '</div>';
                        html += '<div class="reply_time"><span>' + reply.time + '</span>';
                        if (reply.sender == user_id) {
                            html += '</div>';
                        } else {
                            html += '<a target="_self" href="javascript:void(0);" _width="530px" class="reply_btn hide" user_id="' + reply.sender + '" comment_id="' + comment.id + '">回复</a></div><div class="reply_form"></div>';
                        }
                        html += '</div></div>';
                    });
                }
                html += '</div></div>';
            });

            $('#comment_data').append(html);
        }

    }, "json");
}

function get_count_info(article_id){
    $.get(system.url+'comments/counter/blog/'+article_id,null,function(response){
        if (response.error==0) {
            $('.main h1 a').each(function(i){
                $(this).html(response.data[i]);
            });
            if (response.data[4]==0) {
                $('#login_register_button').show();
                $('#top_user_info').hide();
            } else {
                $('#login_register_button').hide();
                $('#top_user_info b').html(response.data[4]);
                $('#top_user_info').show();
            }
        }
    },'json');
}