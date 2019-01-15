CREATE TABLE `activity_template` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `inside_title` varchar(256) DEFAULT NULL COMMENT '内部标题',
  `page_title` varchar(256) DEFAULT NULL COMMENT '页面标题',
  `landing_page` varchar(512) DEFAULT NULL COMMENT '落地页',
  `platform` smallint(1) DEFAULT '1' COMMENT ' 平台  1懒人，2呼呼',
  `content` longtext COMMENT 'patch_image补位图,background_color背景色,items里面的template_type 1:通用图片,2:栏位,3:商品列表区',
  `start_date` timestamp NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '开始时间',
  `end_date` timestamp NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '结束时间',
  `status` smallint(1) DEFAULT '1' COMMENT '1:未开始，2:进行中，-1：已结束',
  `is_deleted` smallint(1) DEFAULT '-1' COMMENT '1删除，-1未删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



alter table `channel` add column `first_extract` decimal(10,2)  NOT NULL DEFAULT '0.00' COMMENT '首次提现金额';
alter table `channel` add column `not_first_extract` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '非首次提现金额';


CREATE TABLE `category_template` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(256) DEFAULT NULL COMMENT '模板名称',
  `remark` varchar(256) DEFAULT NULL COMMENT '模板备注',
  `platform` smallint(1) DEFAULT '1' COMMENT '平台  1淘宝，2天猫，3京东，123全部',
   `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

alter table `channel` add column `category_template_id` int(10) DEFAULT NULL COMMENT '分类模板，导航模板';

CREATE TABLE `lanrencategory_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lanren_id` int(11) NOT NULL,
  `category_template_id` int(11) NOT NULL COMMENT '分类模板id',
  `weight` int(11)  DEFAULT NULL COMMENT '权重',
  `is_display` smallint(1) NOT NULL DEFAULT '-1' COMMENT '是否展示：1展示，-1不展示',
  `is_deleted` smallint(1) DEFAULT '-1' COMMENT '1:删除;-1:未删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


alter tabel `goods_sort` add column  `start_date` timestamp NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '开始时间',
 alter tabel `goods_sort` add column  `end_date` timestamp NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '结束时间',
   alter tabel `goods_sort` add column `status` smallint(1) DEFAULT '1' COMMENT '1:未开始，2:进行中，-1：已结束',



CREATE TABLE `goods_sort_custom` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) NOT NULL COMMENT '懒人商品id',
  `position` smallint(1) NOT NULL COMMENT '位置,0：首页精选，1:1级分类，2:2级分类，',
  `rank` int(11) NOT NULL COMMENT '顺序， 把商品放置的具体地方',
  `start_date` timestamp NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '开始时间',
   `end_date` timestamp NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '结束时间',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8



  alter tabel `admin` add column  `phone` varchar(11) NOT NULL COMMENT '手机号，必填进行验证',
  alter tabel `admin` add column `tries_limit` int(11) NOT NULL DEFAULT '10' COMMENT '限制发短信次数',



CREATE TABLE `lanrencategory_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lanren_id` int(11) NOT NULL,
  `category_template_id` int(11) NOT NULL COMMENT '分类模板id',
  `weight` int(11)  DEFAULT NULL COMMENT '权重',
  `is_display` smallint(1) NOT NULL DEFAULT '-1' COMMENT '是否展示：1展示，-1不展示',
  `is_deleted` smallint(1) DEFAULT '-1' COMMENT '1:删除;-1:未删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `admin_change_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_uid` int(11) NOT NULL DEFAULT '0' COMMENT '后台操作用户uid',
  `model`  varchar(128) NOT NULL DEFAULT '0' COMMENT '所在的model',
  `url`  varchar(128) NOT NULL DEFAULT '0' COMMENT '路由',
  `before` longtext COMMENT '修改前数据',
  `after` longtext COMMENT '修改后数据',
  `created_at` datetime  NOT NULL DEFAULT '2018-08-21 00:00:00' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;