//定义系统全局属性
var system = {'domain': 'www.eqphp.com'};
system.url = 'http://' + system.domain + '/';

//语言包
var lang = {};

//表单处理结果提示
var tip = ['操作成功','您还没有登陆，请<a href="'+system.url+'user/login">登录</a> <a href="'+system.url+'user/register">注册</a>','您已登陆成功','您无权限操作，请联系客服','服务器繁忙，请稍后再试','','','','',''];
var avatar_url=system.url+'file/picture/avatar/';
var image_url=system.url+'file/static/image/';

//正则表达式
var reg_exp = {
    "phone": /^((1[3,5,8][0-9])|(14[5,7])|(17[0,6,7,8]))\d{8}$/,
    "email": /^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/,
    "telephone": /^(0\d{2,3})?-?([2-9]\d{6,7})(-\d{1,5})?$/,
    "hot_line": /^(400|800)-(\d{3})-(\d{4})?$/,
    "qq": /^[1-9]\d{4,9}$/,
    "account": /^[a-zA-Z][a-zA-Z0-9_]{4,17}$/,
    "alpha": /^[a-zA-Z][a-zA-Z0-9_]+$/,
    "md5": /^[a-z0-9]{32}$/,
    "password": /^(.){6,15}$/,
    "money": /^[0-9]+([.][0-9]{1,2})?$/,
    "number": /^\-?[0-9]*\.?[0-9]*$/,
    "numeric": /^\d+$/,
    "url": /^http(s?):\/\/([\w-]+\.)+[\w-]+(\/[\w\- \.\/?%&=]*)?/,
    "cid": /^\d{18}\d{15}/,
    "zip": /^\d{6}$/,
    "address": /^(.){0,50}$/,
    "int": /^[-\+]?\d+$/,
    "float": /^[-\+]?\d+(\.\d+)?$/,
    "letter": /^[A-Za-z]+$/,
    "chinese": /^[\u4E00-\u9FA5]+$/,
    "chinese_name": /^[\u4E00-\u9FA5]{2,5}$/,
    "name": /^[\u4E00-\u9FA5\uf900-\ufa2d\w]+$/,
    "file_name": /^[^\/:*?"<>|,\\]+$/,
    "business_license": /^\d{13}$|^\d{14}([0-9]|X|x)$|^\d{6}(N|n)(A|a|B|b)\d{6}(X|x)$/
};

//判断指定值是否在指定的数组中
function in_array(value, option) {
    var value_type = typeof value;
    var option_type = typeof option;
    if (option_type !== 'object') return false;
    if (value_type === 'number' || value_type === 'string') {
        for (var i = 0; i < option.length; i++) {
            if (value === option[i]) {
                return true;
            }
        }
    }
    return false;
}

//取10以内随机整数
function get_random(i) {
    return Math.round(Math.random() * 10) % i;
}

//定义cookie对象
var cookie = {
    get: function (name) {
        var cv = document.cookie.split("; ");
        var cva = [], temp;
        for (var i = 0; i < cv.length; i++) {
            temp = cv[i].split("=");
            cva[temp[0]] = decodeURI(temp[1]);
        }
        return name ? cva[name] : cva;
    },

    set: function (name, value, expires, path, domain, secure) {
        if (!name || !value || name == "" || value == "") return false;
        if (expires) {
            if (/^[0-9]+$/.test(expires)) {
                var today = new Date();
                expires = new Date(today.getTime() + expires * 1000).toUTCString();
            } else if (!/^wed, \d{2} \w{3} \d{4} \d{2}:\d{2}:\d{2} GMT$/.test(expires)) {
                expires = undefined;
            }
        }
        //合并cookie的相关值
        var cv = name + "=" + encodeURI(value) + ";"
            + (expires ? " expires=" + expires + ";" : "")
            + (path ? "path=" + path + ";" : "")
            + (domain ? "domain=" + domain + ";" : "")
            + ((secure && secure != 0) ? "secure" : "");
        if (cv.length < 4096) {
            document.cookie = cv;
            return true;
        } else {
            return false;
        }
    },

    del: function (name, path, domain) {
        if (!name || name == "" || !this.get(name)) return false;
        document.cookie = name + "=;"
        + (path ? "path=" + path + ";" : "")
        + (domain ? "domain=" + domain + ";" : "")
        + "expires=Thu, 01-Jan-1970 00:00:01 GMT;";
        return true;
    }
};

var occurring = {
    //点击
    click: function (form_option, act_info, mode) {
        $(form_option.split('|')[0]).click(function () {

            //提示、并清空
            if (mode == 'tip_clear') {
                $(this).val('');
                $(form_option.split('|')[1]).html('');
            }

            //清空表单项/判断若相等则清空
            if ((mode == 'clear') || ((mode == 'eq_clear') && ($(this).val() == act_info))) {
                $(this).val('');
            }

            //显示提示信息
            if (mode == 'tip') {
                $(form_option.split('|')[1]).html(act_info);
            }

        });
    },

    //按下按键
    keydown: function (form_option, action) {
        $(form_option).keydown(function () {

        });
    },

    //松开按键
    keyup: function (form_option, act_info, mode) {
        $(form_option.split('|')[0]).live('keyup', function () {
            //判断按键值是否正确
            if (mode == 'check' && in_array(act_info,['int','number','numeric','letter','chinese','alpha'])) {
				var value=$(this).val();
				if (!reg_exp[act_info].test(value)) {
					var end_lie=act_info == 'chinese' ? 3 : 1;
					$(this).val(value.substr(0,value.length-end_lie));
				}
            }

            //提示信息（计算总价）
            if (mode == 'tip_amount') {
                var now_num = parseInt($(this).val());
                var now_result = isNaN(now_num) ? 1 : now_num;
                $(this).val(now_result);
                $(form_option.split('|')[1]).val(now_num * act_info);
            }

            //提示信息（如还可输入字数）
            if (mode == 'tip_number') {
                var now_value = $.trim($(this).val());
                var char_num = now_value.length;
                var min_num = parseInt(act_info.split('|')[0]);
                var max_num = parseInt(act_info.split('|')[1]);
                var allow_num, tip_str;
                if (char_num < min_num) {
                    allow_num = min_num - char_num;
                    tip_str = '您还需输入：<em>' + allow_num + '</em>个字';
                } else {

                    if (char_num == max_num) {
                        tip_str = '刚好<strong>' + max_num + '</strong>个字';
                    } else {
                        allow_num = max_num - char_num;
                        tip_str = '您还可以输入：<strong>' + allow_num + '</strong>个字';
                    }

                }

                if (char_num > max_num) {
                    $(this).val(now_value.substr(0, max_num));
                    tip_str = '不过所允许的字数：<em>' + max_num + '</em>';
                }

                $(form_option.split('|')[1]).html(tip_str);
            }

        });
    },

    //改变
    change: function (form_option, act_info) {
        $(form_option.split('|')[0]).change(function () {
            if ($(this).val() == '') {
                $(form_option.split('|')[1]).html(act_info.split("|")[0]);
            } else {
                $(form_option.split('|')[1]).html(act_info.split("|")[1]);
            }
        });
    },

    //选择
    select: function (form_option, act_info, mode) {
        $(form_option.split('|')[0]).select(function () {
            //提示
            if (mode == 'tip') {
                $(form_option.split('|')[1]).html(act_info);
            }
            //禁止选择
            if (mode == 'forbid') {
                return false;
            }
            //禁止、并提示
            if (mode == 'forbid_tip') {
                $(form_option.split('|')[1]).html(act_info);
                return false;
            }
        });
    },

    //失焦
    blur: function (form_option, act_info, length, step) {
        $(form_option.split('|')[0]).blur(function () {
            //最小长度，非空验证
            if ($(this).val().length < length) {
                $(form_option.split('|')[1]).html(act_info.split("|")[0]);
            } else {
                if (step < 1) {
                    return false;
                }

                if (step < 2) {
                    $(form_option.split('|')[1]).html(act_info.split("|")[0]);
                    return false;
                }
                //是否含有字母
                if (!isNaN($(this).val())) {
                    $(form_option.split('|')[1]).html(act_info.split("|")[1]);
                } else {
                    if (step < 3) {
                        $(form_option.split('|')[1]).html(act_info.split("|")[1]);
                        return false;
                    }
                    //是否含有汉字
                    if (/[\u4E00-\u9FA5]+/.test($(this).val())) {
                        if (step < 4) {
                            $(form_option.split('|')[1]).html(act_info.split("|")[2]);
                            return false;
                        }
                        $(form_option.split('|')[1]).html(act_info.split("|")[3]);
                    } else {
                        $(form_option.split('|')[1]).html(act_info.split("|")[2]);
                    }
                }
            }
        });
    },

    //正则验证
    regular: function (form_option, act_info, regular) {
        $(form_option.split('|')[0]).blur(function () {
            if (regular.test($(this).val())) {
                $(form_option.split('|')[1]).html(act_info.split("|")[1]);
            } else {
                $(form_option.split('|')[1]).html(act_info.split("|")[0]);
            }
        });
    }

};

//表单对象
var form = {
    //获取表单值
    get_data: function (name, id) {
        var out_data = {};
        var name_option = name.split(',');
        var name_length = name_option.length;
        for (var i = 0; i < name_length; i++) {
            out_data[name_option[i]] = $('input[name="' + name_option[i] + '"]').val();
        }
        if (id == null) return out_data;

        var id_option = id.split(',');
        var id_length = id_option.length;
        for (var j = 0; j < id_length; j++) {
            out_data[id_option[j]] = $('#' + id_option[j]).val();
        }
        return out_data;
    },

    //为表单input-text项填值
    set_data: function (data) {
        for (key in data) {
            $('input[name="' + key + '"]').val(data[key]);
        }
    },

    //异步post提交表单
    ajax_submit: function (url, post_data, tip) {
        $.post(url.action, post_data, function (result) {
            if (result.error == 0 && url.location.length > 2) {
                location.href = url.location + result.data;
                return false;
            }
            alert(tip[result.error]);
        }, "json");
    },

    //模拟下拉列表
    create_select: function (select_id) {
        $(select_id).mouseover(function () {
            $(this).css({'position': 'absolute', 'cursor': 'pointer', 'z-index': '999'});
            $(select_id + ' dd').show();

            $(select_id + ' dd').mouseover(function () {
                $(this).css({'color': 'red', 'background': '#efefef'});
            }).mouseout(function () {
                $(this).css({'color': '#666666', 'background': 'white'});
            }).click(function () {
                $(select_id + ' dt').html($(this).html());
                $(select_id + '_value').val($(this).attr('_value'));
                $(select_id + ' dd').hide();
            });

        }).mouseleave(function () {
            $(select_id + ' dd').hide();
            $(this).css({'position': 'absolute'});

        });
    },

    //模拟复选框
    create_checkbox: function (checkbox_id) {
        $('.' + checkbox_id + ' li').click(function () {
            var checked_data=$('input[name="' + checkbox_id + '"]').val();
            var click_option=$(this).attr('_value');
            if ($(this).hasClass('checked_box')) {
                $(this).removeClass('checked_box');
            } else {
                $(this).addClass('checked_box');
            }

            var checked_list=checked_data ? checked_data.split(',') : [];
            if (in_array(click_option,checked_list)) {
                for (i=0; i<checked_list.length; i++) {
                    if (checked_list[i] === click_option) {
                        checked_list.splice(i,1);
                    }
                }
            } else {
                checked_list.push(click_option);
            }
            $('input[name="' + checkbox_id + '"]').val(checked_list.join(','));
        });
    }
};

//设置样式、特效
var style = {
    //0-1、模拟(over/out)
    like_button: function (obj, over_color, out_color) {
        $(obj).mouseover(function () {
            $(this).css({"backgroundColor":over_color[0],"color":over_color[1]});
        }).mouseout(function () {
            $(this).css({"backgroundColor":out_color[0],"color":out_color[1]});
        });
    },

    //0-2、模拟(列表、文本)
    like_li: function (obj, over_color, out_color) {
        $(obj).mouseover(function () {
            $(this).css({"borderColor":over_color[0],"color":over_color[1]});
        }).mouseout(function () {
            $(this).css({"borderColor":out_color[0],"color":out_color[1]});
        });
    },

    //0-3、模拟(图标)
    like_icon: function (obj, b_position, e_position) {
        $(obj).mouseover(function () {
            $(this).css('background-position', e_position);
        }).mouseout(function () {
            $(this).css('background-position', b_position);
        });
    },

    //0-4、模拟(over/click/out)
    like_bg: function (obj, b_color, c_color, e_color) {
        $(obj).mouseover(function () {
            var now_color = $(this).css("backgroundColor");
            if (in_array(now_color,['rgb(255, 255, 255)','white','transparent'])) {
                $(this).css("backgroundColor", b_color);
            }
        }).click(function () {
            $(this).css("backgroundColor", c_color);
        }).mouseout(function () {
            var now_color = $(this).css("backgroundColor");
            if (now_color == 'rgb(255, 255, 215)' || now_color == '#ffffd7') {
                $(this).css("backgroundColor", e_color);
            }
        });
    },


    //4、控制层
    set_layer: function (button, layer, mode) {
        $(button).click(function () {
            if (mode == 'show') {
                $(layer).show();
            } else {
                $(layer).hide();
            }
        });
    },

    //5、两列等高
    equal_height: function (column, other_column) {
        var column_height = $(column).height();
        var other_height = $(other_column).height();
        if (column_height > other_height) {
            $(other_column).height(column_height);
        } else {
            $(column).height(other_height);
        }
    },

    //6、抖动
    shake: function (obj,time,mode) {
		var shake_player,
            offset = [10, 20, 10, 0, -10, -20, -10, 0],
			margin = mode ? 'margin-top' : 'margin-left',
			fx = function () {
				$(obj).css(margin,offset.shift() + 'px');
				if (offset.length <= 0) {
					$(obj).css(margin,0);
					clearInterval(shake_player);
				}
			};
        offset = offset.concat(offset.concat(offset));
        shake_player = setInterval(fx, time);
    },

    //7、闪烁
    flicker: function (obj,time) {
        var flicker_player,
            alpha = [1, 0.8, 0.6, 0.4, 0.2, 0, 0.2, 0.4, 0.6, 0.8, 1],
            fx = function () {
                $(obj).css('opacity',alpha.shift());
                if (alpha.length <= 0) {
                    $(obj).css('opacity',1);
                    clearInterval(flicker_player);
                }
            };
        alpha = alpha.concat(alpha.concat(alpha));
        flicker_player = setInterval(fx, time);
    },
	
	//8、定时消失
	disappear: function (obj,time) {
		var interval = setInterval(function () {
			var current_time = --time;
			if (current_time <= 0) {
				$(obj).fadeOut(1000);
                clearInterval(interval);
			}
		}, 1000);
    },

    //9、计时器
    clock: function (obj,time) {
        $(obj).attr('is_allow_click',0);
        var init_time=time;
        var interval = setInterval(function () {
            var current_time = --time;
            if (current_time > 0) {
                $(obj).css('color','gray');
                $(obj).find('b').html(current_time);
            }
            if (current_time == 0) {
                $(obj).attr('is_allow_click',1).css('color','#2971bd');
                $(obj).find('b').html(init_time);
                clearInterval(interval);
            }
        }, 1000);
    },

    clock2: function (button, time, tip) {
        tip = tip ? tip : '秒后可重新获取';
        $(button).attr('_disabled',1);
        var interval = setInterval(function () {
            var current_time = --time;
            if (current_time > 0) {
                $(button).css('background-color','gray').html(current_time+tip);
            }
            if (current_time == 0) {
                $(button).attr('_disabled',0).css('background-color','#65b1ee').html('获取口令');
                clearInterval(interval);
            }
        }, 1000);



    }
};

var popup={
    //响应
    respond: function (content, time, icon) {
        var icon_position=590;
        switch (icon) {
            case 'error': icon_position+=0; break;
            case 'right': icon_position+=100; break;
            case 'tip': icon_position+=200; break;
            case 'query': icon_position+=300; break;
            default: icon_position=590;
        }
        $('.popup_respond .dialog_icon').css('background-position', 'center -'+icon_position+'px');
        $('.popup_respond .popup_border').css('padding','0');
        $('.popup_respond .dialog_content').css('min-width','328px');
        $('.popup_respond .dialog_content p').html(content);
        $('.popup_respond').show();

        if (time) {
            $('.popup_respond').fadeOut(time*1000);
        }
    },

	//提示
    tip: function (content, title, time) {
        $('.popup_tip .dialog_icon').css('background-position', 'center -790px');
        title = title ? title : '温馨提示';
        $('.popup_tip .dialog_title span').html(title);
        $('.popup_tip .dialog_content p').html(content);
        $('.popup_tip').show();
		if (time) {
            style.disappear('.popup_tip', time);
        }
    },
	
	//警告
	alert: function (content, title, icon) {
        var icon_position=590;
        switch (icon) {
            case 'error': icon_position+=0; break;
            case 'right': icon_position+=100; break;
            case 'tip': icon_position+=200; break;
            case 'query': icon_position+=300; break;
            default: icon_position=590;
        }
        $('.popup_alert .dialog_icon').css('background-position', 'center -'+icon_position+'px');
        title = title ? title : '温馨提示';
        $('.popup_alert .dialog_title span').html(title);
        $('.popup_alert .dialog_content p').html(content);
        $('.popup_alert').show();
	},
	
	//通知、消息
	notice: function (content,title,time) {
        title = title ? title : '消息';
        $('.popup_notice .dialog_title span').html(title);
        $('.popup_notice .dialog_content').html(content);
        $('.popup_notice').show();
		if (time) {
			style.disappear('.popup_notice',time);
		}
	},
	
	//选择确认
	confirm: function (content, title, callback) {
        title = title ? title : '请问';
        $('.popup_confirm .dialog_title span').html(title);
        $('.popup_confirm .dialog_content p').html(content);
        $('.popup_confirm').show();
        $('.popup_confirm .confirm_button').click(function(){
            if (callback()){
                $('.popup_confirm').hide();
            }
        });
	},
	
	//对话框
	dialog: function (content,title,width) {
        width = width ? width : 480;
        $('.popup_dialog .popup_border').css({'width':width+'px','margin-left':-width/2+'px'});
        $('.popup_dialog .dialog_title span').css('width',(width-50)+'px').html(title);
        $('.popup_dialog .dialog_content').html(content);
        $('.popup_dialog').show();
	}
};

//检测账号
function is_account(value){
    return reg_exp.phone.test(value) || reg_exp.email.test(value) || reg_exp.qq.test(value) || reg_exp.account.test(value);
}


$(function () {

    //分页跳转
    $('.page_mark .skip_input').blur(function(){
        var page=parseInt($(this).val());
        location.href=$(this).attr('url')+page;
    });

	//更新验证码
    $('.captcha, .captcha_btn').click(function(){
        var p1=Math.round(-Math.random()*300);
        var p2=Math.round(-Math.random()*300);
        $('body').css('background-position',p1 + 'px ' + p2 + 'px');
        $('.captcha').attr('src',system.url + 'captcha/?random=' + Math.random());
    });

    //关闭弹窗对话框
    style.set_layer('.dialog_title a, .popup_alert .confirm_button, .popup_confirm .cancel_button','.popup_mask, .popup_notice','hide');

});