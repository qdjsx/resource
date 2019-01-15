alter table `menu` add column `is_parent`  smallint(1) not null default 0 comment '0:无子类；1有子类';



CREATE TABLE `lanren_jingdongcats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lanren_id` int(11) NOT NULL,
  `cats_cid` int(11) NOT NULL,
  `is_deleted` smallint(1) DEFAULT '-1' COMMENT '1:删除;-1:未删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;