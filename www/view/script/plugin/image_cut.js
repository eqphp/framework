$(function(){

var _change=false;
var _move=false;//移动标记
var _x,_y;//鼠标离控件左上角的相对位置
var _w,_h;//drag的宽高
var w_x,w_y;//鼠标的绝对位置

var before_w=$("#img_show img").width();
$(".start").html("Start:"+before_w+" : "+$("#img_show img").height());
set_size("#img_view",300,300);
var d1_w=$("#img_div").width();
var d1_h=$("#img_div").height();
_w=$("#drag").width();
_h=$("#drag").height();

$("#drive").css({top:_h-10,left:_w-10});
$("#img_show img").width(d1_w/_w*$("#img_show").width());
$(".end").html("E n d:"+$("#img_show img").width()+" : "+$("#img_show img").height());
var large_small=$("#img_show img").width()/d1_w;//大小图的比例
$("#drag").fadeTo(20, 0.3);


$("#drag").mousemove(function(){
$("#drive").css("display","block");
}).mousedown(function(e){
_move=true;
_x=e.pageX-parseInt($(this).css("left"));
_y=e.pageY-parseInt($(this).css("top"));
_w=$("#drag").width();
_h=$("#drag").height();
}).mouseout(function(){
$("#drive").css("display","none");
});


$(document).mousemove(function(e){

if(_move){
var x=e.pageX-_x;//移动时根据鼠标位置计算控件左上角的绝对位置
var y=e.pageY-_y;
x=x>0?x:0;//限制移动范围
y=y>0?y:0;
_w=$("#drag").width();
_h=$("#drag").height();
x=x>d1_w-_w?d1_w-_w:x;
y=y>d1_h-_h?d1_h-_h:y;
$("#drag").css({top:y,left:x});//控件新位置
//移动大图
var l_x=-large_small*parseInt($("#drag").css("left"));
var l_y=-large_small*parseInt($("#drag").css("top"));
$("#img_show img").css({top:l_y,left:l_x});
}


if(_change){_move=false;
var x=e.pageX-w_x;//计算鼠标移动的距离
var new_w,new_h;
var max_w=d1_w-parseInt($("#drag").css("left"));//在当前位置所能达到的最大宽高
var max_h=d1_h-parseInt($("#drag").css("top"));
new_w=_w+x;
new_w=new_w>16?new_w:16;//宽最小为16
new_h=new_w/(3/3);

if(new_w>max_w){
new_w=max_w;
new_h=new_w/(3/3);
}

if(new_h>max_h){
new_h=max_h;
new_w=new_h*(3/3);
}

$("#drag").css({width:new_w,height:new_h});
$("#drive").css({top:new_h-10,left:new_w-10});
$("#drive").css("display","block"); 
$("#img_show img").width(d1_w/new_w*$("#img_show").width());//调整大图比例
large_small=$("#img_show img").width()/d1_w;//重新计算大小图的比例
var l_x=-large_small*parseInt($("#drag").css("left"));
var l_y=-large_small*parseInt($("#drag").css("top"));
$("#img_show img").css({top:l_y,left:l_x}); 
$(".end").html("now:"+$("#img_show img").width()+" : "+$("#img_show img").height());
}



   
}).mouseup(function(){
_move=false;
_change=false;
$("#drag").fadeTo("fast",0.3);//松开鼠标后停止移动并恢复成不透明
});


$("#drive").mousemove(function(){_move=false;}).mousedown(function(e){
_w=$("#drag").width();
_h=$("#drag").height();
_change=true;
w_x=e.pageX;//获取鼠标的绝对位置
$("#drag").fadeTo(20,0.5);//点击后开始拖动并透明显示
});



$(".sure").click(function(){
var t_x=-parseInt($("#img_show img").css("left"));
var t_y=-parseInt($("#img_show img").css("top"));
var n_w=$("#img_show").width();
var n_h=$("#img_show").height();
var p_w=$("#img_show img").width();
var p_h=$("#img_show img").height();
var img_name=$("#img_show img").attr("src");
$(this).css("display","none");
$("#acting").css("display","block");


var act_url=system_url+'upload/edit';
var json={t_x:t_x,t_y:t_y,img_name:img_name,p_w:p_w,p_h:p_h,n_w:n_w,n_h:n_h};
$.post(act_url,json,function(data){

if (data.error==0) {
$("#img_res img").attr('src',system_url+'file/create/'+data.info);
}
$("#acting").css("display","none");
$(".sure").css("display","block");
});


});

});


//图片等比例缩放
function set_size(obj_id,_w,_h){
var wh=($(obj_id+" img").width()-_w)/$(obj_id+" img").width();
var ht=($(obj_id+" img").height()-_h)/$(obj_id+" img").height();

if (wh>ht) {

if(wh>0){
$(obj_id+" img").attr("width",_w); 
$(obj_id+" div").attr("width",_w);
}

} else {

if(ht>0){
$(obj_id+" img").attr("height",_h); 
$(obj_id+" div").attr("height",_h);
}

}

var m_top=(_h-$(obj_id+" img").height())/2;
var m_left=(_w-$(obj_id+" img").width())/2;
$(obj_id+" #img_div").css("margin",m_top+"px auto auto "+m_left+"px");
$(obj_id+" img").css("display","block");
}