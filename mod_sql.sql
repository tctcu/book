 CREATE TABLE `admin_user` (
  `uid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(12) NOT NULL COMMENT '姓名',
  `password` varchar(64) NOT NULL COMMENT '密码',
  `mobile` char(11) NOT NULL DEFAULT '0' COMMENT '手机号',
  `salt` char(4) NOT NULL,
  `type` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '类型 1-普通 6-管理员',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态 2-禁止登录',
  `created_at` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated_at` int(11) unsigned NOT NULL COMMENT '更新时间戳',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `mobile` (`mobile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='后台用户表';

 CREATE TABLE `admin_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `access_id` int(11) DEFAULT '0',
  `created_at` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `access_id` (`access_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户权限关联表';

CREATE TABLE `admin_access` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(40) DEFAULT '',
  `m` varchar(20) DEFAULT '' COMMENT 'module',
  `c` varchar(20) DEFAULT '' COMMENT 'controller',
  `a` varchar(30) DEFAULT '' COMMENT 'action',
  PRIMARY KEY (`id`),
  KEY `m` (`m`),
  KEY `c` (`c`),
  KEY `a` (`a`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='后台权限表';



insert into admin_user (uid, name, password, mobile, salt, type, created_at, updated_at)
values ('1', '张顺灵', '43e708ad2d43852f8c496f83b05a2f13', '15305634799', '1234', '9', '1530981900', '1530981900');


insert into admin_access (id, title, m, c, a) values (1001, '书籍列表', 'admin', 'book', 'index');
insert into admin_access (id, title, m, c, a) values (1002, '新增/编辑书籍', 'admin', 'book', 'create');
insert into admin_access (id, title, m, c, a) values (1003, '删除书籍', 'admin', 'book', 'delete');
insert into admin_access (id, title, m, c, a) values (1004, '书籍上/下架', 'admin', 'book', 'checkStatus');
insert into admin_access (id, title, m, c, a) values (1005, '书籍借阅', 'admin', 'book', 'borrow');
insert into admin_access (id, title, m, c, a) values (1006, '书籍借阅记录', 'admin', 'book', 'borrowList');
insert into admin_access (id, title, m, c, a) values (1007, '书籍归还', 'admin', 'book', 'back');


insert into admin_access (id, title, m, c, a) values (2001, '用户列表', 'admin', 'user', 'index');
insert into admin_access (id, title, m, c, a) values (2002, '新增/编辑用户', 'admin', 'user', 'create');
insert into admin_access (id, title, m, c, a) values (2003, '用户禁封/解封', 'admin', 'user', 'checkStatus');
insert into admin_access (id, title, m, c, a) values (2004, '用户押金操作', 'admin', 'user', 'pledge');
insert into admin_access (id, title, m, c, a) values (2005, '用户密码重置', 'admin', 'user', 'reset');

insert into admin_access (id, title, m, c, a) values (3001, '书籍出借排行', 'admin', 'stat', 'index');
insert into admin_access (id, title, m, c, a) values (3002, '押金记录', 'admin', 'stat', 'pledge');

insert into admin_access (id, title, m, c, a) values (8001, '后台用户管理', 'admin', 'adminuser', 'index');
insert into admin_access (id, title, m, c, a) values (8002, '编辑用户', 'admin', 'adminuser', 'create');
insert into admin_access (id, title, m, c, a) values (8003, '权限控制', 'admin', 'adminuser', 'role');
insert into admin_access (id, title, m, c, a) values (8004, '密码重置', 'admin', 'adminuser', 'reset');


INSERT INTO admin_roles (uid,access_id,created_at) VALUES (1,1001,1530981900);
INSERT INTO admin_roles (uid,access_id,created_at) VALUES (1,8001,1530981900);
INSERT INTO admin_roles (uid,access_id,created_at) VALUES (1,8002,1530981900);
INSERT INTO admin_roles (uid,access_id,created_at) VALUES (1,8003,1530981900);


CREATE TABLE `book` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL COMMENT '书名',
  `author` varchar(100) NOT NULL DEFAULT '' COMMENT '作者',
  `press` varchar(100) NOT NULL DEFAULT '' COMMENT '出版社',
  `version` varchar(50) NOT NULL DEFAULT '' COMMENT '版本(中文版 和合本等)',
  `price` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT'定价',
  `type` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '类型 1-书 2-期刊 3-课件资料',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态 1-正常 2-下架 3-已借出',
  `delete` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '软删除 1-正常 2-删除',
  `category` varchar(100) NOT NULL DEFAULT '' COMMENT '种类',
  `created_at` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated_at` int(11) unsigned NOT NULL COMMENT '更新时间戳',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='书籍资料表';

 CREATE TABLE `user` (
  `uid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(12) NOT NULL COMMENT '姓名',
  `password` varchar(64) NOT NULL COMMENT '密码',
  `mobile` char(11) NOT NULL DEFAULT '0' COMMENT '手机号',
  `salt` char(4) NOT NULL,
  `pledge` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '押金 1-未付 2-已付',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '状态 1-正常 2-禁止登录',
  `created_at` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated_at` int(11) unsigned NOT NULL COMMENT '更新时间戳',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `mobile` (`mobile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表';

CREATE TABLE `user_pledge` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL COMMENT '用户uid',
  `money` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT '操作押金金额',
  `type` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '操作类型 1-充值押金 2-退还押金',
  `created_at` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户押金记录表';

CREATE TABLE `user_borrow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL COMMENT '用户uid',
  `bid` int(11) unsigned NOT NULL COMMENT '书籍id',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '操作类型 1-借阅 2-归还',
  `start_time` int(11) unsigned NOT NULL COMMENT '借阅开始时间',
  `end_time` int(11) unsigned NOT NULL COMMENT '计划归还时间',
  `return_time` int(11) unsigned NOT NULL COMMENT '实际归还时间',
  `created_at` int(11) unsigned NOT NULL COMMENT '创建时间戳',
  `updated_at` int(11) unsigned NOT NULL COMMENT '更新时间戳',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户借阅书籍记录表';

