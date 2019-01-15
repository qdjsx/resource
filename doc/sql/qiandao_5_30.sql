alter table `coupon` add column `fixed`  varchar(50) null comment '固态兑换码，空是动态';
alter table `coupon` add column `dynamic_excel`  varchar(250) null comment '上传excel路径，空是没有';
alter table `coupon` add column `need_cardnum`  smallint(1) not null default 0  comment '是否需要卡号，1需要';