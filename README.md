欢迎使用 EQPHP Framework
===========================
EQPHP，一款简单易用（Easy）且安全高效（Quick）的PHP开源框架，SP-MVC架构思想；

涵盖：[请求响应](http://www.eqphp.com/file/manual/#16)、[验证过滤](http://www.eqphp.com/file/manual/#17)、[上传下载](http://www.eqphp.com/file/manual/#25)、[加密解密](http://www.eqphp.com/file/manual/#21)、[日志调试](http://www.eqphp.com/file/manual/#22)、[性能测试](http://www.eqphp.com/file/manual/#22)、[缓存静态化](http://www.eqphp.com/file/manual/#20)、[国际化](http://www.eqphp.com/file/manual/#28)等技术点；

囊括：[文件目录操作](http://www.eqphp.com/file/manual/#18)、[数据库使用](http://www.eqphp.com/file/manual/#15)、[图形图像处理](http://www.eqphp.com/file/manual/#21)、[邮件短信发送](http://www.eqphp.com/file/manual/#21)、[DOM表单构建](http://www.eqphp.com/file/manual/#19)、[模板引擎解析](http://www.eqphp.com/file/manual/#24)等解决方案；

结构简洁（单一入口、自动加载、双模多分组）、类库丰富、部署灵活，可任意调整等特性，适合所有Web项目开发。

为什么选择 EQPHP ？
===========================

* 简单
    * 命名简洁、语法规范（符合psr4），阅览愉快
    * 有手册，用法齐全；参考demo、开发得心应手
    * 兼容php5.3以上所有版本，版本升级、扩展更加容易

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

* 优雅
    * 你有更多的时间品茶、喝咖啡、陪家人
    * 当然，你也会有漂亮女朋友、帅气的老公
    * 拥有时间、拥有金钱，甚至拥有整个世界……

加入我们
===========================

| 技术交流QQ群 | 67693067　278464325　264386791 |
| :------------- | :----------- |
| [博客](http://www.eqphp.com/blog) | http://www.eqphp.com/blog |
| [微博](http://weibo.com/eq80) | http://weibo.com/eq80 |
| [getter聊天](https://gitter.im/eqphp/framework) | https://gitter.im/eqphp/framework |
| [加入我们](http://www.eqphp.com/user/register) | http://www.eqphp.com/user/register |
| 公众号(eqphpBlog) | ![](http://eqphp.oschina.mopaasapp.com/file/manual/image/weixin_eqphp_blog.gif) |

花絮
===========================
##### TPS-MVC：调用流程与执行原理 #####
![](http://eqphp.oschina.mopaasapp.com/file/manual/image/eqphp_frame_relation.gif)

##### 性能：各php版本输出 Hello world 测试报告 #####
    * Acer（2核 AMD-1.5GHz、4G内存）+ Ubuntu(14.04)系统
    * 从mysql(5.6.17)取一字段（Hello world）使用MVC模式渲染到浏览器页面，性能报告：

| PHP版本 | 5.3.22 | 5.4.12 | 5.5.33 | 5.6.19 | 7.0.4|
| :------ | :----- | :----- | :----- | :----- | :----|
|时间(s) | 0.017 | 0.014 | 0.014 | 0.015 | 0.011|
|CPU(%) | 1.07 | 1.11 | 1.09 | 1.06 | 0.71|
|内存(KB) | 1584.625 | 1516.312 | 1579.118 | 1580.215 | 1209.496|
|内存峰值(KB) | 6748.625 | 6518.324 | 6589.115 | 6689.079 | 4448.151|

##### 数据库：点、线、面、体查询模型 #####
```php
//查询用户ID为8的邮箱：
db::field('member','email',8);

//查询用户ID为8的用户信息
query('user')->select('avatar,nick_name,sign')->where(['user_id'=>8])->out('record');

//查询年龄大于30岁的前20位女性会员的基本信息并按年龄降序排列
query('user_info')-> select('avatar,nick_name,avatar,sign')
-> where(['sex'=>'female','age'=>['gt',30]])-> order('age desc')-> limit(20)-> out('batch');

//分页查询用户的充值记录
query(table::PREPAY_PROCESS)->select('id,trade_no,method,status,amount,time')
->where($condition)->order('id desc')->out('page', $record_count, $page, $page_size);
```

##### 缓存：友好支持session、file、memcache、redis等常用缓存类型 #####
```php
//session存取
session(['register' => ['captcha' => 'u44s8']]);
session('register.captcha');

//file存取
$cache = with('cache','8.json','user_profile',3600);
//$cache->save(['id'=>8,'profile'=>['name'=>'art','avatar'=>'8_1408031532.gif']]);
$cache->get('profile.avatar');

//memcache集群
$memcache=memory::cluster();
$memcache->set('version','3.0',0,0);
$memcache->replace('memcache','EQPHP is a PHP framework!',0,300);
$memcache->delete('memcache');
$memcache->get('version');

 //redis主从：
$master=memory::group(true);
$slave=memory::group(false);
$master->set('version','3.0',0,0);
$master->replace('redis','EQPHP is a PHP framework!',0,300);
//10s内(0,立即)删除memcache
$master->delete('redis',10);
$slave->get('version');
```

##### 验证、过滤：安全从输入开始、隔离危险 #####
```php
//基本的输入、过滤
input::get('page','int');
input::post('details','text');
input::request('amount','money');
input::cookie('auto_login','number');
input::server('request_method','/^(GET|POST|PUT|DELETE)$/i');

//批量验证，数据模型代替逻辑判断
$input=input::fetch('id,name,date,sex','get');
$option=[
    'id'=>[['in',[1,2,3,4,8]],[1,'id error']],
    'name'=>[['length','2,18'],[2,'name length error']],
    'date'=>[['equal',date('Y-m-d')],[3,'date error']],
    'sex'=>[['callback',[$this,'check',[$data['id']]]],[4,'sex error']],
];
validate::verify($input,$option);

//批量接收过滤、键值映射
//$_POST=['a' => 'Art', 'p' => '125**%24', 'id' => '8']
$filter = ['a' => 'account', 'p' => 'post', 'id' => 'int'];
$map = ['a' => 'author', 'p' => 'password', 't' => 'type'];
$data = input::filter($filter, 'get', $map);
//['author' => 'art', 'password' => '125**%24', 'id' => 8]
```

##### 模板：扩展方式无缝接入smarty模板引擎 #####
```html
<!--======= 母版 =======-->
{head script="jquery|common|center" style="basic|plugin/popup|center"}
{center_header_banner user_id=$user_id}

<div class="stage">
<!--定义母版可编写区域-->
{block name="main"}{/block}

{include file="user/block/guide_tags.html"}
</div>

{include file="user/block/center_footer.html"}
{include file="plugin/popup.html"}
</body></html>

<!--======= 子视图 =======-->
{extends file="user/layout/center.html"}

{block name="main"}
<!--管理员信息-->
管理员：<a href="{$manager.blog}" title="访问博客">{$manager.name}</a>

<!--用户信息-->
<ul class="{#user_list_style#}">
{section key $user}
<li><a href="{$url.'user/'}{$user[key].id}">{$user[key].name}</a>,
{if $user[key].sex eq 'male'}男{elseif $user[key].sex === 'female'}女{/if}，
年龄：{echo abs($user[key].age - 3)}，注册时间：{$user[key].time|date_format}</li>
{/section}
</ul>
{/block}
```

##### restful：快速创建restful风格的API #####
```php
class news extends restful{

    private $cycle;
    protected $category;

    function __construct($category, $cycle = 60){
        parent::__construct();
        $this->cycle = $cycle;
        $this->category = $category;
        $this->model = with('model.news', $category, $cycle);
    }

    function get(){
        $no = url('no', 'uuid');
        $user = $this->model->get($no);
        $this->response(0, 'ok', $user);
    }

    function post(){
        $option = ['name' => 'title', 'no' => 'uuid', 'manager' => 'account'];
        $data = input::filter($option, 'post');
        try {
            $manager_id = $this->model->create($data);
            $this->response(0, 'ok', $manager_id);
        } catch (Exception $e) {
            logger::exception('heartbeat', $e->getMessage());
            $this->response(1, 'create manager fail: ' . $e->getMessage());
        }
    }

    function put(){
        //TODO
    }

    function delete(){
        //TODO
    }

    function head(){
        //TODO
    }

    function __destruct(){
    }

}
```

##### 其他：更多精彩内容待你发现 #####
```php
//读取配置
config('mysql.master.host');

//短信、邮件
with('message',$provider, $channel)->take(['code'=>6781],'withdraw','message_code')->send('1500123****');
with('mail')->take(['user_id'=>8,'url'=>route('user/register')],'invite_friend')->send('xxx@eqphp.com');

//创建DOM、form
html::dl(['MVC','控制器','视图模板'],['id'=>'menu', 'class'=>"dl-dd"]);
$option=[1=>'数学','语文','英语','化学','地理','历史'];
form::checkbox('subject',$option,null,[2,3,4]);

//记录日志、调试追溯
logger::info('hello world');
debug::out($model);
debug::trace($data,'user_info.ini');

//加密、解密
with('crypt','#*!@secret%$&')->encrypt($order_info);
with('crypt','#*!@secret%$&')->decrypt($input);

//mongoDB
with('mg','user')->document('uuid,wallet,login_history',['id'=>8]);
$data['profile']=['name'=>'Art','age'=>26,'sex'=>'female'];
$data['wallet']=['money'=>118.89,'point'=>1121];
$this->collection->post($data,'insert');
```