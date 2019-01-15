alter table `game` add column `type`  varchar(50) null comment '游戏类型';
alter table `game` add column `number`  int(10) not null default 0 comment '抽奖次数';
alter table `game` add column `money`  varchar(50) not null default 0 comment '抽奖金额';
alter table `game` add column `weight` decimal(10,2) NOT NULL DEFAULT '0.00' comment '权重';
CREATE TABLE `slot` (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
`title` varchar(128) NOT NULL DEFAULT '' COMMENT '标题',
`channel_id` int(11) NOT NULL COMMENT '可投放渠道id',
`landing_page` varchar(256) NOT NULL DEFAULT '' COMMENT '落地页',
`updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
`created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
`status` smallint(1) NOT NULL DEFAULT '1' COMMENT '1:开启;-1:关闭',
`remark` varchar(128) DEFAULT NULL COMMENT '备注',
`is_deleted` smallint(1) DEFAULT '-1' COMMENT '是否删除1是-1否',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `slot_channel` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`slot_id` int(11) NOT NULL,
`channel_id` int(11) NOT NULL,
`status` smallint(1) NOT NULL DEFAULT '-1' COMMENT '黑白名单：黑名单-1，白名单1',
PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
alter table `channel` add column `qq_group`  int(11) null comment 'qq群';
alter table `channel` add column `qq_group_status`  smallint(1) NOT NULL DEFAULT '0' COMMENT '1:开启;0:关闭';
alter table `channel` add column `rich_text`  longtext comment '我要赚钱富文本';
alter table `coupon` add column `tries_limit` int(10) not null default 0  comment '总限制，0：不限制，大于0限制次数';
alter table `coupon` add column `day_tries_limit` int(10) null comment '每日限制';
alter table `coupon` add column `cash`  decimal(10,2) NOT NULL DEFAULT '0.00' comment '现金(充值的)';
alter table `coupon` add column `cash_status`  smallint(1) not null default 0 comment '现金展示，0:不展示；1展示';
alter table `coupon` add column `weight` decimal(10,2) NOT NULL DEFAULT '0.00' comment '权重';



alter table `coupon` add column `fixed`  varchar(50) null comment '固态兑换码，空是动态';
alter table `coupon` add column `dynamic_excel`  varchar(250) null comment '上传excel路径，空是没有';
alter table `coupon` add column `need_cardnum`  smallint(1) not null default 0  comment '是否需要卡号，1需要';