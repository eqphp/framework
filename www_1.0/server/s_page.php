<?php

class s_page{

//返回分页导航,$page_num取值为2至10(备选)
static function mark($str_url,$page_count,$page=1,$page_num=5){
if ($page_count<2) { return null; }
$mark_info='';
if ($page_count>1 && $page_count<11) {
for($i=1;$i<=$page_count;$i++){
$mark_info.=($i==$page)?'<a id="page_now">'.$i.'</a>':'<a href="'.$str_url.$i.'">'.$i.'</a>';
}
} else {
if ($page!=1) { $mark_info.='<a href="'.$str_url.'1">1</a>';}
if ($page>1) { $mark_info.='<a href="'.$str_url.($page-1).'">Last</a>';}

for ($i=1;$i<=$page_count;$i++) {
if ($i>$page-($page_num+1) && $i<$page+($page_num+1)) {
if($i==$page) { $mark_info.='<a id="page_now">'.$i.'</a>'; continue; }
if($i!=$page_count && $i!=1) { $mark_info.='<a href="'.$str_url.$i.'">'.$i.'</a>'; }
}
}

if ($page<$page_count){ $mark_info.='<a href="'.$str_url.($page+1).'">Next</a>';}
if ($page!=$page_count){ $mark_info.='<a href="'.$str_url.$page_count.'">'.$page_count.'</a>';}

if ($page_count>0){
$mark_info.="<input id=\"skip_input\" type=\"text\" maxlength=\"4\" onblur=\"window.open(('".$str_url."'+this.value),'_self');\" onclick=\"if (this.value){window.open(('".$str_url."'+this.value),'_self');}\" />";
}

}
return $mark_info;
}




}