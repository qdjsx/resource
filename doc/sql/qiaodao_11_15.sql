CREATE TABLE `sign_popup_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `channel_id` int(11) NOT NULL DEFAULT '0' COMMENT '渠道id',
  `status` smallint(1) NOT NULL DEFAULT '1' COMMENT '1:开启;-1:关闭',
  `sum` smallint(1) DEFAULT '1' COMMENT '运营位个数',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `created_at` datetime NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '创建时间',

  PRIMARY KEY (`id`) USING BTREE,
  KEY `channel_id` (`channel_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='签到弹窗配置表';
CREATE TABLE `sign_popup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
	`name` varchar(512) NOT NULL DEFAULT '' COMMENT '内部名称',
	`title` varchar(256) NOT NULL DEFAULT '' COMMENT '标题',
	`title_color` varchar(28) NOT NULL DEFAULT '#666666' COMMENT '标题颜色',
	`sub_title` varchar(512) NOT NULL DEFAULT '' COMMENT '副标题',
	`sub_title_color` varchar(28) NOT NULL DEFAULT '#838383' COMMENT '副标题颜色',
	`image_path` varchar(128) DEFAULT NULL COMMENT 'icon地址',
	`button_name` varchar(512) NOT NULL DEFAULT '' COMMENT '按钮名称',
	`weight` int(11) DEFAULT '0' COMMENT '权重',
	`click_type` varchar(512) NOT NULL DEFAULT '' COMMENT '命中类型',
	`click_content` varchar(512) NOT NULL DEFAULT '' COMMENT '命中内容',
	`orientation_channel_id` int(11) DEFAULT '0' COMMENT '定向渠道',
	`start_date` datetime  NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '开始时间',
	`end_date` datetime NOT  NULL DEFAULT '2018-08-21 00:00:00' COMMENT '结束时间',
	`remark` varchar(128) DEFAULT NULL COMMENT '备注',
	`updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  	`created_at` datetime NOT NULL DEFAULT '1000-01-01 00:00:00' COMMENT '创建时间',
	`is_deleted` smallint(1) DEFAULT '-1' COMMENT '是否删除1是-1否',

  PRIMARY KEY (`id`) USING BTREE,
  KEY `orientation_channel_id` (`orientation_channel_id`),
	KEY `start_date` (`start_date`),
	KEY `end_date` (`end_date`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='签到弹窗表';
ALTER TABLE coupon   ADD COLUMN `sign_popup_status` SMALLINT(1) DEFAULT '-1' COMMENT '是否是红包弹窗运营位';




