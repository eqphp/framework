$(function(){

var ini_date=new Date();
var now_year=ini_date.getFullYear();
var now_month=ini_date.getMonth()+1;
var now_day=ini_date.getDate();

var tags_year=$('#selcet_year_month span em');
var tags_month=$('#selcet_year_month span cite');

tags_year.html(now_year);
tags_month.html(now_month);


var get_year=parseInt(tags_year.html());
var get_month=parseInt(tags_month.html()-1);

change_show_data(now_year,now_month,now_day,get_year,get_month);


$('#last_year').click(function(){
if (tags_year.html()>1901) {
tags_year.html(tags_year.html()-1);
get_year=parseInt(tags_year.html());

change_show_data(now_year,now_month,now_day,get_year,get_month);
}

});

$('#next_year').click(function(){
if (tags_year.html()<2099) {
tags_year.html(parseInt(tags_year.html())+1);
get_year=parseInt(tags_year.html());
change_show_data(now_year,now_month,now_day,get_year,get_month);
}
});


$('#last_month').click(function(){
if (tags_month.html()>1) {
tags_month.html(tags_month.html()-1);
get_month=parseInt(tags_month.html()-1);
change_show_data(now_year,now_month,now_day,get_year,get_month);
}
});

$('#next_month').click(function(){
if (tags_month.html()<12) {
tags_month.html(parseInt(tags_month.html())+1);
get_month=parseInt(tags_month.html()-1);
change_show_data(now_year,now_month,now_day,get_year,get_month);
}
});

$('#selcet_year_month b, #selcet_year_month i').mouseover(function(){
$(this).css('color','red');
}).mouseout(function(){
$(this).css('color','white');
});


$('#table_calendar td').mouseover(function(){
if ($(this).html()!='&nbsp;') {
$(this).css({color:'white',background:'#6ecefe'});
}
}).mouseout(function(){
$today_color=(now_year==get_year && now_month==(get_month+1) && $(this).html()==now_day)?'red':'gray';
$(this).css({color:$today_color,background:'white'});
});


});


function change_show_data(now_year,now_month,now_day,get_year,get_month){

var m_f_w=new Date(get_year,get_month,1).getDay();

var lucky_my_days=(((get_year%4==0) && (get_year%100!=0)) || (get_year%400==0))?29:28;
var md_arr=new Array(31,lucky_my_days,31,30,31,30,31,31,30,31,30,31);

var i=0;
$('#table_calendar td').each(function(td){

if (td>=m_f_w) i++;
if (i>0 && i<=md_arr[get_month]) {

$(this).html(i);

var show_color=(now_year==get_year && now_month==(get_month+1) && i==now_day)?'red':'gray';
$(this).css('color',show_color);

} else {
var $other_str=(td==41)?'<span class="fcr fs15 fsb">×</span>':'&nbsp;';
$(this).html($other_str);
}
});

}