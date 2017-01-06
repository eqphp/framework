//定义系统全局属性
var system = {'domain': 'www.eqphp.com'};
system.url = '/';//'http://' + system.domain + '/';

//语言包
var lang = {};

//表单处理结果提示
var exception_message = ['操作成功','未登陆','已登陆','无权限操作','服务器繁忙，请稍后再试','','','','',''];


//正则表达式
var regexp = {
    "phone": /^((1[3,5,8][0-9])|(14[5,7])|(17[0,3,6,7,8]))\d{8}$/,
    "email": /^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/,
    "telephone": /^(0\d{2,3})?-?([2-9]\d{6,7})(-\d{1,5})?$/,
    "hot_line": /^(400|800)-(\d{3})-(\d{4})?$/,
    "qq": /^[1-9]\d{4,9}$/,
    "account": /^[a-zA-Z][a-zA-Z0-9_]{4,17}$/,
    "alpha": /^[a-zA-Z][a-zA-Z0-9_]+$/,
    "md5": /^[a-z0-9]{32}$/,
    "password": /^(.){6,18}$/,
    "money": /^[0-9]+([.][0-9]{1,2})?$/,
    "number": /^\-?[0-9]*\.?[0-9]*$/,
    "numeric": /^\d+$/,
    "url": /^http(s?):\/\/([\w-]+\.)+[\w-]+(\/[\w\- \.\/?%&=]*)?/,
    "cid": /^\d{15}$|^\d{17}(\d|X|x)$/,
    "zip": /^\d{6}$/,
    "address": /^(.){0,64}$/,
    "int": /^[-\+]?\d+$/,
    "float": /^[-\+]?\d+(\.\d+)?$/,
    "letter": /^[A-Za-z]+$/,
    "chinese": /^[\u4E00-\u9FA5]+$/,
    "chinese_name": /^[\u4E00-\u9FA5]{2,5}$/,
    "name": /^[\u4E00-\u9FA5\uf900-\ufa2d\w]+$/,
    "file_name": /^[^\/:*?"<>|,\\]+$/,
    "uuid": /^[a-f0-9]{8}(-[a-f0-9]{4}){3}-[a-f0-9]{12}$/,
    "business_license": /^\d{13}$|^\d{14}([0-9]|X|x)$|^\d{6}(N|n)(A|a|B|b)\d{6}(X|x)$/
};

//定义cookie对象
var cookie = {
    get: function (name) {
        var cv = document.cookie.split("; ");
        var cva = [], temp;
        for (var i = 0; i < cv.length; i++) {
            temp = cv[i].split("=");
            cva[temp[0]] = decodeURI(temp[1]);
        }
        return name ? cva[name] : cva;
    },

    set: function (name, value, expires, path, domain, secure) {
        if (!name || !value || name == "" || value == "") return false;
        if (expires) {
            if (/^[0-9]+$/.test(expires)) {
                var today = new Date();
                expires = new Date(today.getTime() + expires * 1000).toUTCString();
            } else if (!/^wed, \d{2} \w{3} \d{4} \d{2}:\d{2}:\d{2} GMT$/.test(expires)) {
                expires = undefined;
            }
        }
        //合并cookie的相关值
        var cv = name + "=" + encodeURI(value) + ";"
            + (expires ? " expires=" + expires + ";" : "")
            + (path ? "path=" + path + ";" : "")
            + (domain ? "domain=" + domain + ";" : "")
            + ((secure && secure != 0) ? "secure" : "");
        if (cv.length < 4096) {
            document.cookie = cv;
            return true;
        } else {
            return false;
        }
    },

    move: function (name, path, domain) {
        if (!name || name == "" || !this.get(name)) return false;
        document.cookie = name + "=;"
        + (path ? "path=" + path + ";" : "")
        + (domain ? "domain=" + domain + ";" : "")
        + "expires=Thu, 01-Jan-1970 00:00:01 GMT;";
        return true;
    }
};