var picker = {};
picker.date = {
    init: function (input_object,position) {
        var param = {};
        var ini_date = new Date();

        param.now_year = parseInt(ini_date.getFullYear());
        param.now_month = parseInt(ini_date.getMonth() + 1);
        param.now_day = parseInt(ini_date.getDate());
        param.show_year = $('#select_year_month span em');
        param.show_month = $('#select_year_month span cite');

        param.show_year.html(param.now_year);
        param.show_month.html(param.now_month);

        this.draw_date(param);
        this.set_style(param,input_object,position);

        $('#last_year').unbind('click').click(function () {
            picker.date.last_year(param);
        });
        $('#next_year').unbind('click').click(function () {
            picker.date.next_year(param);
        });
        $('#last_month').unbind('click').click(function () {
            picker.date.last_month(param);
        });
        $('#next_month').unbind('click').click(function () {
            picker.date.next_month(param);
        });
    },

    last_year: function (param) {
        if (param.show_year.html() > 1901) {
            param.show_year.html(param.show_year.html() - 1);
            param.get_year = parseInt(param.show_year.html());
            this.draw_date(param);
        }
    },

    next_year: function (param) {
        if (param.show_year.html() < 2099) {
            param.show_year.html(parseInt(param.show_year.html()) + 1);
            param.get_year = parseInt(param.show_year.html());
            this.draw_date(param);
        }
    },

    last_month: function (param) {
        if (param.show_month.html() > 1) {
            param.show_month.html(param.show_month.html() - 1);
            param.get_month = parseInt(param.show_month.html() - 1);
            this.draw_date(param);
        }
    },

    next_month: function (param) {
        if (param.show_month.html() < 12) {
            param.show_month.html(parseInt(param.show_month.html()) + 1);
            param.get_month = parseInt(param.show_month.html() - 1);
            this.draw_date(param);
        }
    },

    select: function (click_object) {
        var click_value = click_object.html();
        if (isNaN(click_value)) {
            return false;
        }
        var get_year = $('#select_year_month span em').html();
        var get_month = $('#select_year_month span cite').html();
        if (get_month.length<2) {
            get_month='0'+get_month;
        }
        if (click_value.length<2) {
            click_value='0'+click_value;
        }
        var input_name=$('#calendar_plugins').attr('target_object');
        $('.'+input_name).val(get_year+'-'+get_month+'-'+click_value);
        $('#calendar_plugins').hide();
    },

    draw_date: function (param) {
        var i = 0;
        var get_year = parseInt(param.show_year.html());
        var get_month = parseInt(param.show_month.html());
        var m_f_w = new Date(get_year, (get_month - 1), 1).getDay();
        var lucky_my_days = (((get_year % 4 == 0) && (get_year % 100 != 0)) || (get_year % 400 == 0)) ? 29 : 28;
        var days_month = [31, lucky_my_days, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

        $('#table_calendar td').each(function (td) {
            if (td >= m_f_w) i++;
            if (i > 0 && i <= days_month[get_month-1]) {
                $(this).html(i);
                var show_color = (param.now_year == get_year && param.now_month == get_month && i == param.now_day) ? 'red' : 'gray';
                $(this).css('color', show_color);
            } else {
                if (td < 41) {
                    $(this).html('&nbsp;');
                }
            }
        });
    },

    set_style: function (param,input_object,position) {
        $('#calendar_plugins').css({'left':position.left+'px','top':position.top+'px'}).show().attr('target_object', input_object);
        style.set_layer('.calendar_date_close_btn','#calendar_plugins','hide');
        style.like_button('#select_year_month b, #select_year_month i', ['', 'red'], ['', 'white']);
        $('#table_calendar td').mouseover(function () {
            if ($(this).html() != '&nbsp;') {
                $(this).css({'color': 'white', 'background': '#2971bd'});
            }
        }).mouseout(function () {
            var get_year = parseInt(param.show_year.html());
            var get_month = parseInt(param.show_month.html());
            var today_color = (param.now_year == get_year && param.now_month == get_month && $(this).html() == param.now_day) ? 'red' : 'gray';
            $(this).css({'color': today_color, 'background': 'white'});
        });
    }

};

picker.time = {

    init: function (input_object) {
        this.time_draw();
        this.draw_time(24);
        $('#calendar_time_plugin').show().attr('target_object', input_object);
        $('#eqphp_selected_time').attr('now_unit', 0).find('em').css('color', 'gray').html('00');
        style.set_layer('.calendar_close_btn','#calendar_time_plugin','hide');
        style.like_button('#table_calendar_time td, .calendar_close_btn',['#2971bd','white'],['white','gray']);
    },

    select: function (click_object){
        var click_value=click_object.html();
        if (isNaN(click_value)) {
            return false;
        }

        var stage_object=$('#eqphp_selected_time');
        var now_unit=stage_object.attr('now_unit');

        if (now_unit) {
            this.draw_time(60);
            if (click_value.length<2) {
                click_value='0'+click_value;
            }
        }

        stage_object.find('em:eq('+now_unit+')').css('color','red').html(click_value);
        stage_object.attr('now_unit',++now_unit);
        if (now_unit>2) {
            stage_object.attr('now_unit',0);
            var input_name=$('#calendar_time_plugin').attr('target_object');
            $('.'+input_name).val(stage_object.html().replace(/<\/?[^>]*>/g,''));
            $('#calendar_time_plugin').hide();
        }
    },

    time_draw: function () {
        var ini_date = new Date();
        var now_hour = ini_date.getHours();
        var now_minute = ini_date.getMinutes();
        var now_second = ini_date.getSeconds();

        if (now_minute <= 9) now_minute = '0' + now_minute;
        if (now_second <= 9) now_second = '0' + now_second;

        var now_time = now_hour + ':' + now_minute + ':' + now_second;
        $('#system_now_time').html(now_time);
        setTimeout('picker.time.time_draw()', 1000);
    },

    draw_time: function (number){
        $('#table_calendar_time td').html('&nbsp');
        $('#table_calendar_time td').each(function(i){
            if (++i<=number) {
                $(this).html(i);
                if (i===number) {
                    $(this).html(0);
                }
            }
        });
    }
};

picker.color={
    init: function (input_object){
        $('#get_color_board').show().attr('target_object', input_object);
    },

    select: function (click_object){
        var input_name=$('#get_color_board').attr('target_object');
        $('.'+input_name).val(click_object.attr('bgcolor'));
        $('#get_color_board').hide();
    }
};

$(function () {
    $('#table_calendar td').click(function(){
        picker.date.select($(this));
    });
    $('#table_calendar_time td').click(function(){
        picker.time.select($(this));
    });
    $('#get_color_board td').click(function(){
        picker.color.select($(this));
    });
});