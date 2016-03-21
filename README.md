欢迎使用 EQPHP Framework
===========================
EQPHP，一款简单易用（Easy）且安全高效（Quick）的PHP开源框架，SP-MVC架构思想；

涵盖：[日志调试](http://www.eqphp.com/file/manual/#22)、[性能分析](http://www.eqphp.com/file/manual/#22)、[请求响应](http://www.eqphp.com/file/manual/#16)、[上传下载](http://www.eqphp.com/file/manual/#25)、[验证过滤](http://www.eqphp.com/file/manual/#17)、[加密解密](http://www.eqphp.com/file/manual/#21)、[缓存静态化](http://www.eqphp.com/file/manual/#20)、[国际化](http://www.eqphp.com/file/manual/#28)等技术点；

囊括：[文件目录操作](http://www.eqphp.com/file/manual/#18)、[数据库使用](http://www.eqphp.com/file/manual/#15)、[图形图像处理](http://www.eqphp.com/file/manual/#21)、[邮件短信发送](http://www.eqphp.com/file/manual/#21)、[DOM表单构建](http://www.eqphp.com/file/manual/#19)、[模板引擎解析](http://www.eqphp.com/file/manual/#24)等解决方案；

结构简洁（单一入口、自动加载、类库丰富）、体积小（1.58MB），部署灵活，可任意调整等特性，适合所有Web项目开发。

为什么选择 EQPHP ？
===========================

* 简单
    * 命名简洁、语法规范（符合psr4规则），让你愉悦阅览
    * 有手册，用法齐全；参考demo、开发得心应手
    * 兼容php5.3以上所有版本（包括php7），版本升级、扩展更加容易

* 自由
    * 免费开源，遵循Apache2开源协议发布
    * 没有严格或额外的约束，一切按你的规范、习惯来
    * 架构思想来源于众多项目总结，你的需求决定框架结构，无论怎么玩都行

* 安全
    * 从接收到运行输出，验证、过滤、SQL注入、XSS、CSRF安全预防
    * 无论是事务、加密签名，还是异常追溯、日志让你的系统有迹可循，永不变朽

* 性能出众
    * 自动加载、按需加载
    * 不放弃每一毫秒CPU性能优化、也不放过每一字节内存消耗
    * 大到每一个模块、类库，小至每一个函数、语句都经过精雕细作，以求完美

    * Acer（2核 AMD-1.5GHz、4G内存）+ Ubuntu(14.04)系统
    * 从mysql(5.0.5)取一字段（Hello world）使用MVC模式渲染到浏览器页面，性能报告：

    * | PHP版本 | 5.3.22 | 5.4.12 | 5.5.33 | 5.6.19 | 7.0.4|
      | :------ | :----- | :----- | :----- | :----- | :----|
      |CPU(%) | 1.07 | 1.11 | 1.09 | 1.06 | 0.71|
      |时间(s) | 0.017 | 0.014 | 0.014 | 0.015 | 0.011|
      |内存(KB) | 1584.625 | 1516.312 | 1579.118 | 1580.215 | 1209.496|
      |内存峰值(KB) | 6748.625 | 6518.324 | 6589.115 | 6689.079 | 4448.151|

* 优雅
    * 你有更多的时间品茶、喝咖啡、陪家人
    * 当然，你也会有漂亮女朋友、帅气的老公
    * 拥有时间、拥有金钱，甚至拥有整个世界……

* 最后
    * 选择EQPHP，就是对我们最大限度的支持，我们再次感谢你！

花絮
===========================
#####TPS-MVC：调用流程与执行原理
![](http://www.eqphp.com/file/manual/image/eqphp_frame_relation.gif)

#####数据库：点、线、面、体查询模型
```php
//查询用户ID为8的邮箱：
db::field('member','email',8); //tom88@tom.com

//查询用户ID为8的用户信息
query('user')->select('avatar,nick_name,sign')->where(['user_id'=>8])->out('record');
//['avatar' => 'member.png', 'nick_name' => 'EQPHP', 'sign' => 'EQPHP,一个神级框架！']

//查询年龄大于30岁的前20位女性会员的基本信息并按年龄降序排列
query('user_info')-> select('avatar,nick_name,avatar,sign')
-> where(['sex'=>'female','age'=>['gt',30]])-> order('age desc')-> limit(20)-> out('batch')
//[['avatar' => 'member.png', 'nick_name' => 'EQPHP', 'sign' => 'EQPHP,一个神级框架！'],
//['avatar' => '14030215.png', 'nick_name' => 'Art', 'sign' => '我想你们都懂我']]
```



加入我们
===========================
