$(function(){

$('.main h1:eq(0), .main p:eq(0)').show();

$('.nav_title').mouseover(function(){
$('.nav_title dl dd').show();
}).mouseleave(function(){
$('.nav_title dl dd').hide();
});

$('.nav_title dl dd').each(function(i){
$(this).click(function(){
$('.nav_title dl dd').css('color','#005EAC');
$(this).css('color','red');
$('.main h1, .main p').hide();
$('.main h1:eq('+i+'), .main p:eq('+i+')').show();
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