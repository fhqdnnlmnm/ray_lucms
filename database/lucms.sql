/**
* @table  users 用户表
* @table  admin_users 管理员用户表
* @table  attachments 附件件表
* @table  attachments 附件件表
* @table  advertisement_positions 广告位表
* @table  advertisements 广告表
* @table  categories 分类表
* @table  model_has_categories 多态分类表
* @table  tags 标签表
* @table  model_has_tags 多态标签表
* @table  articles 文章表
* @table  logs 日志表
* @table  ip_filters ip 过滤表
 */
-- create database if not exists lucms default character set utf8mb4 collate utf8mb4_unicode_ci;
use lucms;

drop table if exists users;
CREATE TABLE users (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(50) NOT NULL default  '',
  email varchar(255)  NOT NULL default  '',
  password varchar(255)  NOT NULL default '',
  enable enum ('T','F')  NOT NULL  DEFAULT  'F' comment '启用状态：F禁用，T启用',
  is_admin enum ('T','F')  NOT NULL  DEFAULT  'F' comment '是否可登录后台：F否，是',
  description varchar(50)  NOT NULL  DEFAULT  '' comment '一句话描述',
  head_image int(11)  NOT NULL  DEFAULT  0 comment '头像',
  remember_token varchar(100)  DEFAULT NULL,
  last_login_at timestamp NULL DEFAULT NULL,
  created_at timestamp NULL DEFAULT NULL,
  updated_at timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY users_email_unique (email),
   KEY users_head_image_index (head_image)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
insert into users (name,email,password,enable,is_admin,created_at) values ('ucer','dev@lucms.com','$2y$10$fsOBO.HAhsZ9wM8R9vARi..MST.yqpxu4z4ikefR17srjyPhRfulS','T','T','2018-06-08 13:21:21');


drop table if exists admin_users;
CREATE TABLE admin_users (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(255) NOT NULL,
  email varchar(255)  NOT NULL,
  password varchar(255)  NOT NULL,
  enable enum ('T','F')  NOT NULL  DEFAULT  'F' comment '启用状态：F禁用，T启用',
  is_admin enum ('T','F')  NOT NULL  DEFAULT  'F' comment '是否可登录后台：F否，是',
  description varchar(50)  NOT NULL  DEFAULT  '' comment '一句话描述',
  head_image int(11)  NOT NULL  DEFAULT  0 comment '头像',
  remember_token varchar(100)  DEFAULT NULL,
  last_login_at timestamp NULL DEFAULT NULL,
  created_at timestamp NULL DEFAULT NULL,
  updated_at timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY users_email_unique (email),
   KEY users_head_image_index (head_image)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
insert into admin_users (id,name,email,password,enable,is_admin,created_at) values (2,'ucer_admin','dev@lucms.com','$2y$10$fsOBO.HAhsZ9wM8R9vARi..MST.yqpxu4z4ikefR17srjyPhRfulS','T','T','2018-06-08 13:21:21');

drop table if exists attachments;
CREATE TABLE attachments (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  user_id int(11) NOT NULL DEFAULT 0 COMMENT '上传用户id',
  ip varchar(18)  NOT NULL DEFAULT '' COMMENT '附件上传者IP',
  original_name varchar(255) NOT NULL DEFAULT '' COMMENT '原始名称',
  mime_type varchar(255) NOT NULL DEFAULT '' COMMENT '原始名称',
  size varchar(10)  NOT NULL DEFAULT '0' COMMENT '大小/kb',
  type enum('avatars','identifies','advertisements','editors','others') NOT NULL DEFAULT 'others' COMMENT '类型',
  storage_position enum('oss','local') NOT NULL DEFAULT 'local' COMMENT '存储位置',
  domain varchar(255) NOT NULL DEFAULT '' COMMENT '域名地址,https://jiayouhaoshi.com',
  storage_path varchar(255)  NOT NULL DEFAULT '' COMMENT '附件相对 storage 目录,app/public/images/avatars',
  link_path varchar(255)  NOT NULL DEFAULT '' COMMENT '附件相对网站根目录,访问路径：storage/images/avatars',
  storage_name varchar(255) NOT NULL DEFAULT '' COMMENT '存储名称',
  enable enum ('T','F')  NOT NULL  DEFAULT  'T' comment '启用状态：F禁用，T启用',
  use_status enum ('T','F')  NOT NULL  DEFAULT  'F' comment '使用状态：F临时图片，T使用中',
  remark varchar(255)  NOT NULL DEFAULT '' COMMENT '附件备注',
  created_at timestamp NULL DEFAULT NULL,
  updated_at timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id),
   KEY attachments_user_id_index (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci comment '附件表';


alter table roles add column description varchar(50) NOT NULL DEFAULT '' COMMENT '说明';
alter table permissions add column description varchar(50) NOT NULL DEFAULT '' COMMENT '说明';


drop table if exists advertisement_positions;
CREATE TABLE advertisement_positions (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(20) NOT NULL DEFAULT '' COMMENT '广告位名称',
  description varchar(100) NOT NULL DEFAULT '' COMMENT '广告位描述',
  type enum( 'default', 'model', 'spa') NOT NULL DEFAULT 'default' COMMENT '广告位类型:默认、跳转到模型、单页面',
  created_at timestamp NULL DEFAULT NULL,
  updated_at timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci comment '广告位表';


drop table if exists advertisements;
CREATE TABLE advertisements (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(20) NOT NULL DEFAULT '' COMMENT '广告标题',
  cover_image int(11) NOT NULL DEFAULT 0 COMMENT '广告封面图片',
  content text COMMENT '广告内容',
  weight int(11) NOT NULL DEFAULT 20 COMMENT '广告权重',
  advertisement_positions_id int(11) NOT NULL DEFAULT 0 COMMENT '所属广告位',
  link_url varchar(255) NOT NULL DEFAULT '' COMMENT '跳转 url:为空则不跳转',
  model_column_value varchar(80) NOT NULL DEFAULT '' COMMENT 'json_encode([model=>article,column=>slug,value=markdown-language)',
  start_at timestamp NULL DEFAULT NULL,
  end_at timestamp NULL DEFAULT NULL,
  enable enum ('T','F')  NOT NULL  DEFAULT  'F' comment '启用状态：F禁用，T启用',
  created_at timestamp NULL DEFAULT NULL,
  updated_at timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY advertisements_cover_image_index (cover_image),
  KEY advertisements_advertisement_positions_id_index (advertisement_positions_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci comment '广告位表';

drop table if exists categories;
CREATE TABLE categories (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(20) NOT NULL DEFAULT '' COMMENT '分类名称',
  cover_image int(11) NOT NULL DEFAULT 0 COMMENT '封面图片',
  description varchar(100) NOT NULL DEFAULT '' COMMENT '广告位描述',
  created_at timestamp NULL DEFAULT NULL,
  updated_at timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci comment '文章分类表';


drop table if exists tags;
CREATE TABLE tags (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(20) NOT NULL DEFAULT '' COMMENT '标签名称',
  created_at timestamp NULL DEFAULT NULL,
  updated_at timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci comment '标签表';

drop table if exists model_has_tags;
CREATE TABLE model_has_tags (
  tag_id int(11) unsigned NOT NULL DEFAULT 0 COMMENT '',
  model_id int(11)  NOT NULL DEFAULT 0 COMMENT '',
  model_type varchar(50) NOT NULL DEFAULT '' COMMENT '',
  primary key (tag_id, model_id, model_type),
  KEY model_has_tags_model_id_index (model_id),
  KEY model_has_tags_model_type_index (model_type),
  constraint model_has_tags_tag_id_foreign foreign key (tag_id) references tags (id) on delete cascade
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci comment '多态标签表';


drop table if exists articles;
CREATE TABLE articles (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  title varchar(100)  NOT NULL DEFAULT  '' COMMENT '文章标题',
  slug varchar(100)  NOT NULL DEFAULT  '' COMMENT 'slug',
  keywords varchar(255) NOT NULL DEFAULT '' COMMENT '关键词,以英文逗号隔开',
  description varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  cover_image int(11)  NOT NULL DEFAULT  0 COMMENT '封面图片',
  content text  COMMENT '内容',
  user_id int(11) DEFAULT NULL DEFAULT 0 COMMENT '作者ID',
  category_id int(11) DEFAULT NULL DEFAULT 0 COMMENT '分类ID',
  view_count int(11) unsigned DEFAULT NULL DEFAULT 0 COMMENT '查看数量',
  vote_count int(11) unsigned DEFAULT NULL DEFAULT 0 COMMENT '点赞数量',
  comment_count int(11) unsigned DEFAULT NULL DEFAULT 0 COMMENT '评论数量',
  collection_count int(11) unsigned DEFAULT NULL DEFAULT 0 COMMENT '收藏数量',
  enable enum ('T','F')  NOT NULL  DEFAULT  'F' comment '启用状态：F禁用，T启用',
  recommend enum ('T','F')  NOT NULL  DEFAULT  'F' comment '是否推荐到首页',
  top enum ('T','F')  NOT NULL  DEFAULT  'F' comment '是否置顶',
  weight int(6) NOT NULL DEFAULT 20 COMMENT '权重',
  access_type enum('PUB','PRI','PWD') NOT NULL DEFAULT 'PUB' COMMENT '访问权限类型：公开、私密、密码访问',
  access_value varchar(255) NOT NULL DEFAULT '' COMMENT '访问权限值：PUB->不公开的用户ids,PRI->公开的用户ids,PWD->访问密码',
  created_year varchar(4)  DEFAULT NULL DEFAULT '' COMMENT '创建年:2018',
  created_month varchar(2)  DEFAULT NULL DEFAULT '' COMMENT '创建月:01',
  created_at timestamp NULL DEFAULT NULL,
  updated_at timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY articles_weight_index (weight),
  KEY articles_category_id_index (category_id),
  KEY articles_user_id_index (user_id),
  KEY articles_created_year_index (created_year),
  KEY articles_created_month_index (created_month),
  KEY articles_access_type_index (access_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci comment '文章表';


drop table if exists logs;
CREATE TABLE logs (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  user_id int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '操作人ID',
  type enum('C','U','R','D','L','O') NOT NULL DEFAULT 'O' COMMENT '日志所属操作类型:模型 CURD 操作,后台登录,其它操作',
  table_name varchar(10) NOT NULL DEFAULT '' COMMENT '表名:articles',
  ip varchar(18)  NOT NULL DEFAULT '' COMMENT 'IP',
  content text NOT NULL COMMENT '日志内容,json_encode([data=>insert into ... ,message=>添加数据)',
  created_at timestamp NULL DEFAULT NULL,
  updated_at timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY logs_type_index (type),
  KEY logs_table_name_index (table_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci comment '日志表';


drop table if exists ip_filters;
CREATE TABLE ip_filters (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  type enum('white','black') NOT NULL DEFAULT 'white' COMMENT '类型',
  ip varchar(18)  NOT NULL DEFAULT '' COMMENT 'IP',
  created_at timestamp NULL DEFAULT NULL,
  updated_at timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id),
  KEY ip_filters_type_index (type),
  UNIQUE ip_filters_ip_unique (ip)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci comment 'ip 过滤';

