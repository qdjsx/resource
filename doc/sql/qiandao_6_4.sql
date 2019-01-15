alter table `cats` add column `status`  smallint(1) NOT NULL DEFAULT '1' COMMENT '1:开启;-1:关闭';
 CREATE TABLE `lanren_category` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `parent_id` varchar(64) DEFAULT NULL,
          `name` varchar(256) DEFAULT NULL,
          `is_parent` tinyint(1) DEFAULT NULL,
          `level` tinyint(1) DEFAULT NULL,
          `status` tinyint(1) DEFAULT '1' COMMENT '启用1，禁用-1',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `lanren_cats` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`lanren_id` int(11) NOT NULL,
`cats_cid` int(11) NOT NULL,
 `is_deleted` smallint(1) DEFAULT '-1' COMMENT '1:删除;-1:未删除',
PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;



