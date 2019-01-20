create database if not exists kefu default character set "utf8";
drop table if exists member;
create table member(
	id int(10) unsigned not null auto_increment primary key comment '主键',
	username char(30) unique not null default '' comment '帐号',
	tcp_number int unsigned not null default 0 comment 'TCP序号',
	bind_id int unsigned not null default 0 comment '绑定好友',
	allot int unsigned not null default 0 comment '游客配给'
)ENGINE=MyISAM DEFAULT CHARSET='UTF8';