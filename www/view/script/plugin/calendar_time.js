$(function(){

time_draw(); //显示当前时间


$('#start_time').click(function(){
init_calendar_time();
$('#calendar_time_plugin').show();
$(this).attr('is_not_option','yes');
});

$('#end_time').click(function(){
init_calendar_time();
$('#calendar_time_plugin').show();
$(this).attr('is_not_option','yes');
});

// click_input('#start_time');

// click_input('#end_time');

});


function click_input(input_obj){
$(input_obj).click(function(){
init_calendar_time();
$('#calendar_time_plugin').show();
$(this).attr('is_not_option','yes');
});
}




function init_calendar_time(){
var ini_date=new Date();
var now_hour=ini_date.getHours();
var now_minute=ini_date.getMinutes();
var now_second=ini_date.getSeconds();

$("#table_calendar_time td").each(function(i){
$now_hour_color=($(this).html()==now_hour)?'red':'gray';
$(this).css({color:$now_hour_color,background:'white'});

if (i<24) {
$(this).mouseover(function(){
$(this).css({color:'white',background:'#6ecefe'});
}).mouseout(function(){
$now_hour_color=($(this).html()==now_hour)?'red':'gray';
$(this).css({color:$now_hour_color,background:'white'});
}).click(function(){

//设置小时
if ($('#eqphp_selected_time').attr('now_option')=='h') {
$('#eqphp_selected_time em:eq(0)').html($(this).html()).css('color','red');

$('#table_calendar_time td').each(function(j){
$(this).html(j+1);
if (j==59) { $(this).html(0); }

$now_minute_color=($(this).html()==now_minute)?'red':'gray';
$(this).css({color:$now_minute_color,background:'white',cursor:'pointer'});

$(this).mouseover(function(){
$(this).css({color:'white',background:'#6ecefe'});
}).mouseout(function(){
$now_minute_color=($(this).html()==now_minute)?'red':'gray';
$(this).css({color:$now_minute_color,background:'white'});
});
});

}


//设置分钟
$("#table_calendar_time td").each(function(){
$(this).click(function(){
if ($('#eqphp_selected_time').attr('now_option')=='m') {
if ($('#eqphp_selected_time').attr('hit_minute')=='no') {
var write_value=($(this).html()<10)? '0'+$(this).html() : $(this).html();
$('#eqphp_selected_time em:eq(1)').html(write_value).css('color','red');
$('#eqphp_selected_time').attr('hit_minute','yes');
}
$('#eqphp_selected_time').attr('now_option','s');
}

/**设置秒开始****************/
$("#table_calendar_time td").each(function(){
$(this).css('color','gray');
$(this).click(function(){
if ($('#eqphp_selected_time').attr('now_option')=='s') {
//设置秒
var write_value=($(this).html()<10)? '0'+$(this).html() : $(this).html();
$('#eqphp_selected_time em:eq(2)').html(write_value).css('color','red');

var set_sure_time=$('#eqphp_selected_time').text();
set_input_time_val(set_sure_time);

$('#eqphp_selected_time').attr('now_option','h');
$('#eqphp_selected_time').attr('hit_minute','no');





$('#calendar_time_plugin').fadeOut(800);
}
});
});
/**设置秒结束*******************/
});
});

//将下一设置目标设为分钟项
$('#eqphp_selected_time').attr('now_option','m');
});


} else {
$(this).css('cursor','default');
}

});
}



function set_input_time_val(set_sure_time){
//开始时间
var start_obj=$('#start_time');
if (start_obj.attr('is_not_option')=='yes') {
start_obj.val(set_sure_time);
start_obj.attr('is_not_option','no');
}

//结束时间
var end_obj=$('#end_time');
if (end_obj.attr('is_not_option')=='yes') {
end_obj.val(set_sure_time);
end_obj.attr('is_not_option','no');
}

}





//显示当前时间
function time_draw(){
var ini_date=new Date();
var now_hour=ini_date.getHours();
var now_minute=ini_date.getMinutes();
var now_second=ini_date.getSeconds();

if(now_minute<=9) now_minute='0'+now_minute;
if(now_second<=9) now_second='0'+now_second;

var now_time=now_hour+':'+now_minute+':'+now_second;
$("#system_now_time").html(now_time);
setTimeout("time_draw()",1000);
}