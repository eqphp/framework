message {留言表}
id int AI PK
is_view tinyint D(0) [index] {是否查看}
pub_time timestamp {留言时间}
user_name varchar(24) null {用户称呼}
tel char(11) null {手机号码}
phone char(12) null {固定电话}
email varchar(32) null {电子邮箱}
message varchar(512) not null D(--) {留言内容}

CREATE TABLE message (
  id int(11) NOT NULL AUTO_INCREMENT,
  is_view tinyint(4) NOT NULL DEFAULT '0',
  pub_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  user_name varchar(24) COLLATE utf8_bin DEFAULT NULL,
  tel char(11) COLLATE utf8_bin DEFAULT NULL,
  phone char(12) COLLATE utf8_bin DEFAULT NULL,
  email varchar(32) COLLATE utf8_bin DEFAULT NULL,
  message varchar(512) COLLATE utf8_bin NOT NULL DEFAULT '--',
  PRIMARY KEY (id),
  KEY is_view (is_view)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


INSERT INTO message (id, is_view, pub_time, user_name, tel, phone, email, message) VALUES
(1, 0, '2013-10-01 22:29:06', 'EQPHP', '13032935175', '029-83210084', 'gkz1024@126.com', 'EQPHP案例留言本首次测试！'),
(2, 0, '2013-10-01 22:49:21', 'jenny', '18945612233', '010-84113322', 'jenny_007@tom.com', 'monday, too busy! I am very tired.'),
(3, 0, '2013-10-01 23:00:28', 'jim', '16545612233', '010-12353322', '2581221391@qq.com', 'yes, I''m very tired too.'),
(4, 0, '2013-10-01 23:36:14', '印度阿三', '15875412155', '026-77441122', 'ansan@126.com', '小黑三我来了！');