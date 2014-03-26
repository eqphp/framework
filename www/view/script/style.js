//0-1、模拟A(按钮)
function like_a_btn(btn){
$(btn).mouseover(function(){
$(this).css({color:"red",borderColor:"red"});
}).mouseout(function(){
$(this).css({color:'gray',borderColor:"#efefef"});
});
}

//0-2、模拟A(列表、文本)
function like_a_li(obj,e_color,s_color){
$(obj).mouseover(function(){
$(this).css({color:e_color,borderColor:"red"});
}).mouseout(function(){
$(this).css({color:s_color,borderColor:"#efefef"});
});
}

//0-3、模拟A(图标)
function like_a_icon(obj,pe,ps){
$(obj).mouseover(function(){
$(this).css('background-position',pe);
}).mouseout(function(){
$(this).css('background-position',ps);
});
}

//0-4、模拟A(over/click/out)
function like_a_bg(obj,s_color,c_color,e_color){
$(obj).mouseover(function(){
var now_color=$(this).css("backgroundColor");
if (now_color=='rgb(255, 255, 255)' || now_color=='white') {
$(this).css("backgroundColor",e_color);
}
}).click(function(){
$(this).css("backgroundColor",c_color);
}).mouseout(function(){
var now_color=$(this).css("backgroundColor");

if (now_color=='rgb(250, 255, 210)' || now_color=='#faffd2') {
$(this).css("backgroundColor",s_color);
}
});
}

//1-5、控制层
function set_layer(btn,layer,mode){
$(btn).click(function(){
if (mode=='show') {
$(layer).show();
} else {
$(layer).hide();
}
});
}

//1-6、模拟下拉列表
function create_select(select_id){
$(select_id).mouseover(function(){
$(this).css({'cursor':'pointer','z-index':'99999'});
$(select_id+' dd').show(); 

$(select_id+' dd').mouseover(function(){
$(this).css({'color':'red','background':'#efefef'});
}).mouseout(function(){
$(this).css({'color':'#666666','background':'white'});
}).click(function(){
$(select_id+' dt').html($(this).html());
$(select_id+'_value').val($(this).attr('value'));
$(select_id+' dd').hide(); 
});

}).mouseleave(function(){
$(this).css('z-index','9999');
$(select_id+' dd').hide(); 
});
}

//1-7、设置两列等高
function equal_height(column,other_column){
var column_height=$(column).height();
var other_height=$(other_column).height();
if (column_height>other_height) {
$(other_column).height(column_height);
} else {
$(column).height(other_height);
}
}