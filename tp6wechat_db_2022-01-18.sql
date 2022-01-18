# ************************************************************
# Sequel Pro SQL dump
# Version 5446
#
# https://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.01 (MySQL 5.7.26)
# Database: tp6wechat_db
# Generation Time: 2022-01-18 10:46:07 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table wechat_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wechat_user`;

CREATE TABLE `wechat_user` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '用户手机号',
  `nick_name` varchar(255) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `avatar_url` varchar(255) NOT NULL DEFAULT '' COMMENT '用户头像',
  `gender` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '性别',
  `country` varchar(50) NOT NULL DEFAULT '' COMMENT '国家',
  `province` varchar(50) NOT NULL DEFAULT '' COMMENT '省份',
  `city` varchar(50) NOT NULL DEFAULT '' COMMENT '城市',
  `platform` varchar(20) NOT NULL DEFAULT '' COMMENT '注册来源的平台 (APP、H5、小程序等)',
  `last_login_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `is_delete` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`user_id`),
  KEY `mobile` (`mobile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户记录表';

LOCK TABLES `wechat_user` WRITE;
/*!40000 ALTER TABLE `wechat_user` DISABLE KEYS */;

INSERT INTO `wechat_user` (`user_id`, `mobile`, `nick_name`, `avatar_url`, `gender`, `country`, `province`, `city`, `platform`, `last_login_time`, `is_delete`, `create_time`, `update_time`)
VALUES
	(4,'18576692238','theScoreONE','https://thirdwx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTK40cFN9aJ7V3ewD6d80z71Pp1J5r7YOiaCUL5otp9UHv1AicQLgPTqDMyr4ZNI3pehPtX0DZsnklZw/132',0,'','','','MP-WEIXIN',1642502730,0,1642502650,1642502730);

/*!40000 ALTER TABLE `wechat_user` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table wechat_user_oauth
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wechat_user_oauth`;

CREATE TABLE `wechat_user_oauth` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
  `oauth_type` varchar(255) NOT NULL DEFAULT '' COMMENT '第三方登陆类型(MP-WEIXIN)',
  `oauth_id` varchar(100) NOT NULL DEFAULT '' COMMENT '第三方用户唯一标识 (uid openid)',
  `unionid` varchar(100) DEFAULT '' COMMENT '微信unionID',
  `is_delete` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `oauth_type` (`oauth_type`),
  KEY `oauth_type_2` (`oauth_type`,`oauth_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='第三方用户信息表';

LOCK TABLES `wechat_user_oauth` WRITE;
/*!40000 ALTER TABLE `wechat_user_oauth` DISABLE KEYS */;

INSERT INTO `wechat_user_oauth` (`id`, `user_id`, `oauth_type`, `oauth_id`, `unionid`, `is_delete`, `create_time`, `update_time`)
VALUES
	(4,4,'MP-WEIXIN','o2F935LIRJrwZk_BBp7Tjfs9Os60','',0,1642502650,1642502650);

/*!40000 ALTER TABLE `wechat_user_oauth` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
