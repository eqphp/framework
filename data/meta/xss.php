<?php
//9,29,35
return array(

	//1,Finds html breaking injections including whitespace attacks
	'/(?:"[^"]*[^-]?>)|(?:[^\w\s]\s*\/>)|(?:>")/',

	//2,Finds attribute breaking injections including whitespace attacks
	'/(?:"+.*[<=]\s*"[^"]+")|(?:"\s*\w+\s*=)|(?:>\w=\/)|(?:#.+\)["\s]*>)|(?:"\s*(?:src|style|on\w+)\s*=\s*")|(?:[^"]?"[,;\s]+\w*[\[\(])/',

	//3,Finds unquoted attribute breaking injections
	'/(?:^>[\w\s]*<\/?\w{2,}>)/',

	//4,Detects url-, name-, JSON, and referrer-contained payload attacks
	'/(?:[+\/]\s*name[\W\d]*[)+])|(?:;\W*url\s*=)|(?:[^\w\s\/?:>]\s*(?:location|referrer|name)\s*[^\/\w\s-])/',

	//5,Detects hash-contained xss payload attacks, setter usage and property overloading
 	'/(?:\W\s*hash\s*[^\w\s-])|(?:\w+=\W*[^,]*,[^\s(]\s*\()|(?:\?"[^\s"]":)|(?:(?<!\/)__[a-z]+__)|(?:(?:^|[\s)\]\}])(?:s|g)etter\s*=)/',

	//6,Detects self contained xss, common loops and regex to string conversion
	'/(?:with\s*\(\s*.+\s*\)\s*\w+\s*\()|(?:(?:do|while|for)\s*\([^)]*\)\s*\{)|(?:\/[\w\s]*\[\W*\w)/',

	//7,Detects JavaScript with, ternary operators and XML predicate attacks
	'/(?:[=(].+\?.+:)|(?:with\([^)]*\)\))|(?:\.\s*source\W)/',//TODO

	//8,Detects self-executing JavaScript functions
 	'/(?:\/\w*\s*\)\s*\()|(?:\([\w\s]+\([\w\s]+\)[\w\s]+\))|(?:(?<!(?:mozilla\/\d\.\d\s))\([^)[]+\[[^\]]+\][^)]*\))|(?:[^\s!][{([][^({[]+[{([][^}\])]+[}\])][\s+",\d]*[}\])])|(?:"\)?\]\W*\[)|(?:=\s*[^\s:;]+\s*[{([][^}\])]+[}\])];)/',

	//9,Detects the IE octal, hex and unicode entities
	//'/(?:\\u00[a-f0-9]{2})|(?:\\x0*[a-f0-9]{2})|(?:\\\d{2,3})/',

	//13,Detects halfwidth/fullwidth encoded unicode HTML breaking attempts
	'/(?:%u(?:ff|00|e\d)\w\w)|(?:(?:%(?:e\w|c[^3\W]|))(?:%\w\w)(?:%\w\w)?)/',

	//14,Detects possible includes, VBSCript/JScript encodeed and packed functions
	'/(?:#@~\^\w+)|(?:\w+script:|@import[^\w]|;base64|base64,)|(?:\w\s*\([\w\s]+,[\w\s]+,[\w\s]+,[\w\s]+,[\w\s]+,[\w\s]+\))/',

	//15,Detects JavaScript DOM\/miscellaneous properties and methods
 	'/([^*:\s\w,.\/?+-]\s*)?(?<![a-z]\s)(?<![a-z\/_@\-\|])(\s*return\s*)?(?:create(?:element|attribute|textnode)|[a-z]+events?|setattribute|getelement\w+|appendchild|createrange|createcontextualfragment|removenode|parentnode|decodeuricomponent|\wettimeout|(?:ms)?setimmediate|option|useragent)(?(1)[^\w%"]|(?:\s*[^@\s\w%",.+\-]))/',

	//16,Detects possible includes and typical script methods
 	'/([^*\s\w,.\/?+-]\s*)?(?<![a-mo-z]\s)(?<![a-z\/_@])(\s*return\s*)?(?:alert|inputbox|showmod(?:al|eless)dialog|showhelp|infinity|isnan|isnull|iterator|msgbox|executeglobal|expression|prompt|write(?:ln)?|confirm|dialog|urn|(?:un)?eval|exec|execscript|tostring|status|execute|window|unescape|navigate|jquery|getscript|extend|prototype)(?(1)[^\w%"]|(?:\s*[^@\s\w%",.:\/+\-]))/',

	//17,Detects JavaScript object properties and methods
 	'/([^*:\s\w,.\/?+-]\s*)?(?<![a-z]\s)(?<![a-z\/_@])(\s*return\s*)?(?:hash|name|href|navigateandfind|source|pathname|close|constructor|port|protocol|assign|replace|back|forward|document|ownerdocument|window|top|this|self|parent|frames|_?content|date|cookie|innerhtml|innertext|csstext+?|outerhtml|print|moveby|resizeto|createstylesheet|stylesheets)(?(1)[^\w%"]|(?:\s*[^@\/\s\w%.+\-]))/',

	//18,Detects JavaScript array properties and methods
 	'/([^*:\s\w,.\/?+-]\s*)?(?<![a-z]\s)(?<![a-z\/_@\-\|])(\s*return\s*)?(?:join|pop|push|reverse|reduce|concat|map|shift|sp?lice|sort|unshift)(?(1)[^\w%"]|(?:\s*[^@\s\w%,.+\-]))/',

	//19,Detects JavaScript string properties and methods
 	'/([^*:\s\w,.\/?+-]\s*)?(?<![a-z]\s)(?<![a-z\/_@\-\|])(\s*return\s*)?(?:set|atob|btoa|charat|charcodeat|charset|concat|crypto|frames|fromcharcode|indexof|lastindexof|match|navigator|toolbar|menubar|replace|regexp|slice|split|substr|substring|escape|\w+codeuri\w*)(?(1)[^\w%"]|(?:\s*[^@\s\w%,.+\-]))/',

	//20、Detects JavaScript language constructs
 	'/(?:\)\s*\[)|([^*":\s\w,.\/?+-]\s*)?(?<![a-z]\s)(?<![a-z_@\|])(\s*return\s*)?(?:globalstorage|sessionstorage|postmessage|callee|constructor|content|domain|prototype|try|catch|top|call|apply|url|function|object|array|string|math|if|for\s*(?:each)?|elseif|case|switch|regex|boolean|location|(?:ms)?setimmediate|settimeout|setinterval|void|setexpression|namespace|while)(?(1)[^\w%"]|(?:\s*[^@\s\w%".+\-\/]))/',

	//21,Detects very basic XSS probings
 	'/(?:,\s*(?:alert|showmodaldialog|eval)\s*,)|(?::\s*eval\s*[^\s])|([^:\s\w,.\/?+-]\s*)?(?<![a-z\/_@])(\s*return\s*)?(?:(?:document\s*\.)?(?:.+\/)?(?:alert|eval|msgbox|showmod(?:al|eless)dialog|showhelp|prompt|write(?:ln)?|confirm|dialog|open))\s*(?:[^.a-z\s\-]|(?:\s*[^\s\w,.@\/+-]))|(?:java[\s\/]*\.[\s\/]*lang)|(?:\w\s*=\s*new\s+\w+)|(?:&\s*\w+\s*\)[^,])|(?:\+[\W\d]*new\s+\w+[\W\d]*\+)|(?:document\.\w)/',

	//22,Detects advanced XSS probings via Script(), RexExp, constructors and XML namespaces
	'/(?:=\s*(?:top|this|window|content|self|frames|_content))|(?:\/\s*[gimx]*\s*[)}])|(?:[^\s]\s*=\s*script)|(?:\.\s*constructor)|(?:default\s+xml\s+namespace\s*=)|(?:\/\s*\+[^+]+\s*\+\s*\/)/',

	//23,Detects JavaScript location/document property access and window access obfuscation
	'/(?:\.\s*\w+\W*=)|(?:\W\s*(?:location|document)\s*\W[^({[;]+[({[;])|(?:\(\w+\?[:\w]+\))|(?:\w{2,}\s*=\s*\d+[^&\w]\w+)|(?:\]\s*\(\s*\w+)/',

	//24,Detects basic obfuscated JavaScript script injections
	'/(?:[".]script\s*\()|(?:\$\$?\s*\(\s*[\w"])|(?:\/[\w\s]+\/\.)|(?:=\s*\/\w+\/\s*\.)|(?:(?:this|window|top|parent|frames|self|content)\[\s*[(,"]*\s*[\w\$])|(?:,\s*new\s+\w+\s*[,;)])/',

	//25,Detects obfuscated JavaScript script injections
	'/(?:=\s*[$\w]\s*[\(\[])|(?:\(\s*(?:this|top|window|self|parent|_?content)\s*\))|(?:src\s*=s*(?:\w+:|\/\/))|(?:\w+\[("\w+"|\w+\|\|))|(?:[\d\W]\|\|[\d\W]|\W=\w+,)|(?:\/\s*\+\s*[a-z"])|(?:=\s*\$[^([]*\()|(?:=\s*\(\s*")/',

	//26,Detects JavaScript cookie stealing and redirection attempts
	'/(?:[^:\s\w]+\s*[^\w\/](href|protocol|host|hostname|pathname|hash|port|cookie)[^\w])/',

	//27,Detects data: URL injections, VBS injections and common URI schemes
	'/(?:(?:vbs|vbscript|data):.*[,+])|(?:\w+\s*=\W*(?!https?)\w+:)|(jar:\w+:)|(=\s*"?\s*vbs(?:ript)?:)|(language\s*=\s?"?\s*vbs(?:ript)?)|on\w+\s*=\*\w+\-"?/',

	//28,Detects IE firefoxurl injections, cache poisoning attempts and local file inclusion/execution
	'/(?:firefoxurl:\w+\|)|(?:(?:file|res|telnet|nntp|news|mailto|chrome)\s*:\s*[%&#xu\/]+)|(wyciwyg|firefoxurl\s*:\s*\/\s*\/)/',

	//29,Detects bindings and behavior injections
	//'/(?:binding\s?=|moz-binding|behavior\s?=)|(?:[\s\/]style\s*=\s*[-\\])/',

	//30,Detects common XSS concatenation patterns 1/2
	'/(?:=\s*\w+\s*\+\s*")|(?:\+=\s*\(\s")|(?:!+\s*[\d.,]+\w?\d*\s*\?)|(?:=\s*\[s*\])|(?:"\s*\+\s*")|(?:[^\s]\[\s*\d+\s*\]\s*[;+])|(?:"\s*[&|]+\s*")|(?:\/\s*\?\s*")|(?:\/\s*\)\s*\[)|(?:\d\?.+:\d)|(?:]\s*\[\W*\w)|(?:[^\s]\s*=\s*\/)/',

	//31,Detects common XSS concatenation patterns 2/2
	'/(?:=\s*\d*\.\d*\?\d*\.\d*)|(?:[|&]{2,}\s*")|(?:!\d+\.\d*\?")|(?:\/:[\w.]+,)|(?:=[\d\W\s]*\[[^]]+\])|(?:\?\w+:\w+)/',

	//32,Detects possible event handlers
	'/(?:[^\w\s=]on(?!g\>)\w+[^=_+-]*=[^$]+(?:\W|\>)?)/',

	//34,Detects attributes in closing tags and conditional compilation tokens
	'/(?:\<\/\w+\s\w+)|(?:@(?:cc_on|set)[\s@,"=])/',

	//35,Detects common comment types
	//'/(?:--[^\n]*$)|(?:\<!-|-->)|(?:[^*]\/\*|\*\/[^*])|(?:(?:[\W\d]#|--|{)$)|(?:\/{3,}.*$)|(?:<!\[\W)|(?:\]!>)/'

	//37,Detects base href injections and XML entity injections
 	'/(?:\<base\s+)|(?:<!(?:element|entity|\[CDATA))/',

	//38,Detects possibly malicious html elements including some attributes
	'/(?:\<[\/]?(?:[i]?frame|applet|isindex|marquee|keygen|script|audio|video|input|button|textarea|style|base|body|meta|link|object|embed|param|plaintext|xm\w+|image|im(?:g|port)))/',

	//39,Detects nullbytes and other dangerous characters
	'/(?:\\x[01fe][\db-ce-f])|(?:%[01fe][\db-ce-f])|(?:&#[01fe][\db-ce-f])|(?:\\[01fe][\db-ce-f])|(?:&#x[01fe][\db-ce-f])/',

	//67,Detects unknown attack vectors based on PHPIDS Centrifuge detection
	'/(?:\({2,}\+{2,}:{2,})|(?:\({2,}\+{2,}:+)|(?:\({3,}\++:{2,})|(?:\$\[!!!\])/',

	//68,Finds attribute breaking injections including obfuscated attributes
	'/(?:[\s\/"]+[-\w\/\\\*]+\s*=.+(?:\/\s*>))/',

	//69,Finds basic VBScript injection attempts
	'/(?:(?:msgbox|eval)\s*\+|(?:language\s*=\*vbscript))/',

	//71,Finds malicious attribute injection attempts and MHTML attacks
	'/(?:[\s\d\/"]+(?:on\w+|style|poster|background)=[$"\w])|(?:-type\s*:\s*multipart)/',


	//1，查找HTML打破注入包括空格攻击
	//2，查找属性打破注入包括空格攻击
	//3，查找不带引号的属性打破注入
	//4，检测URL-，名字 - ，JSON和引用包含有效载荷攻击
	//5，检测散列式XSS攻击的有效载荷，二传手的使用情况和财产超载
	//6，检测自包含的XSS通过与（），常见的循环和正则表达式来串转换
	//7，检测与JavaScript的（），三元运营商和XML谓词攻击
	//8，检测自动执行的JavaScript函数
	//9，检测IE的八进制，十六进制和Uni​​code实体
	//13，检测半角/全角Unicode编码的HTML打破尝试
	//14，检测可能包含的VBScript、JScript的encodeed和包装功能
	//15，检测JavaScript的DOM、杂项属性和方法
	//16，检测可能存在的包括和典型的脚本方法
	//17，检测JavaScript对象的属性和方法
	//18，检测JavaScript数组的属性和方法
	//19，检测的JavaScript字符串的属性和方法
	//20，检测的JavaScript语言结构
	//21，检测非常基本的XSS探索中
	//22，通过脚本（），RexExp，建设者和XML命名空间检测先进的XSS探索中
	//23，检测JavaScript的位置/文档属性的访问和窗口访问混淆
	//24，检测基本模糊JavaScript脚本注入
	//25，检测模糊JavaScript脚本注入
	//26，检测的JavaScript的Cookie窃取和重定向的尝试
	//27，检测数据：URL注入，注入VBS和共同的URI方案
	//28，检测IE firefoxurl注入，缓存中毒尝试和本地文件包含/执行
	//29，检测绑定和行为注入
	//30，检测常见的XSS串联模式1/2
	//31，检测常见的XSS串联模式2/2
	//32，检测可能存在的事件处理程序
	//34，检测结束标签和条件编译令牌属性
	//35，检测常见的注释类型
	//37，检测基地的HREF注入和XML实体注入
	//38，检测可能是恶意的HTML元素，包括一些属性
	//39，检测nullbytes等危险人物
	//67，检测的基础上PHPIDS离心机检测未知攻击向量
	//68，查找属性打破注入包括模糊属性
	//69，查找基本VBScript注入尝试
	//71，查找恶意属性注入尝试和MHTML攻击

);