DROP TABLE IF EXISTS dbs_ad;
CREATE TABLE `dbs_ad` (
  `ad_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `position_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `media_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ad_name` varchar(60) NOT NULL DEFAULT '',
  `ad_link` varchar(255) NOT NULL DEFAULT '',
  `ad_code` text NOT NULL,
  `start_time` int(11) NOT NULL DEFAULT '0',
  `end_time` int(11) NOT NULL DEFAULT '0',
  `click_count` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `enabled` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`ad_id`),
  KEY `position_id` (`position_id`),
  KEY `enabled` (`enabled`)
) ENGINE=MyISAM ;

DROP TABLE IF EXISTS dbs_admin_role;
CREATE TABLE `dbs_admin_role` (
  `role_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(100) NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=MyISAM ;

DROP TABLE IF EXISTS dbs_admin_users;
CREATE TABLE `dbs_admin_users` (
  `user_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(60) NOT NULL DEFAULT '',
  `user_name` varchar(60) NOT NULL DEFAULT '',
  `name` varchar(40) CHARACTER SET ucs2 NOT NULL,
  `password` char(32) NOT NULL,
  `last_login` int(11) unsigned NOT NULL DEFAULT '0',
  `last_ip` varchar(15) NOT NULL DEFAULT '',
  `msn` varchar(60) NOT NULL,
  `qq` varchar(20) NOT NULL,
  `tel_phone` varchar(20) NOT NULL,
  `mobile_phone` varchar(20) NOT NULL,
  `addr` varchar(100) NOT NULL COMMENT '地址',
  `company` varchar(100) NOT NULL COMMENT '公司名称',
  `company_website` varchar(100) NOT NULL COMMENT '公司网站',
  `is_locked` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '封锁',
  `is_super` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '超级管理员',
  PRIMARY KEY (`user_id`),
  KEY `email` (`email`),
  KEY `is_super` (`is_super`)
) ENGINE=MyISAM ;

DROP TABLE IF EXISTS dbs_admin_user_role;
CREATE TABLE `dbs_admin_user_role` (
  `userid` mediumint(8) unsigned NOT NULL,
  `roleid` smallint(5) unsigned NOT NULL,
  KEY `userid` (`userid`),
  KEY `roleid` (`roleid`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS dbs_admin_user_role_priv;
CREATE TABLE `dbs_admin_user_role_priv` (
  `roleid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `field` char(15) NOT NULL,
  `value` char(30) NOT NULL,
  `priv` char(50) NOT NULL,
  PRIMARY KEY (`roleid`,`field`,`value`,`priv`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS dbs_ad_position;
CREATE TABLE `dbs_ad_position` (
  `position_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `position_name` varchar(60) NOT NULL DEFAULT '',
  `ad_width` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ad_height` smallint(5) unsigned NOT NULL DEFAULT '0',
  `position_desc` varchar(255) NOT NULL DEFAULT '',
  `position_style` text NOT NULL,
  PRIMARY KEY (`position_id`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS dbs_article;
CREATE TABLE `dbs_article` (
  `article_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `cate_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `title` varchar(100) NOT NULL,
  `alias` varchar(50) NOT NULL COMMENT '文章别名',
  `content` text NOT NULL,
  `addtime` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`article_id`)
) ENGINE=MyISAM ;

DROP TABLE IF EXISTS dbs_article_category;
CREATE TABLE `dbs_article_category` (
  `cate_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `cate_name` varchar(50) NOT NULL,
  `sort_order` smallint(5) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`cate_id`)
) ENGINE=MyISAM ;

DROP TABLE IF EXISTS dbs_city;
CREATE TABLE `dbs_city` (
  `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `level` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '等级',
  `parent_id` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '父ID',
  `code` varchar(30) NOT NULL COMMENT '编码',
  `name` varchar(50) NOT NULL COMMENT '名称',
  `admin_uid` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '站长ID',
  `sort_order` smallint(4) unsigned NOT NULL DEFAULT '1' COMMENT '排序',
  PRIMARY KEY (`id`),
  KEY `admin_uid` (`admin_uid`),
  KEY `level` (`level`,`parent_id`)
) ENGINE=MyISAM ;

DROP TABLE IF EXISTS dbs_consume_records;
CREATE TABLE `dbs_consume_records` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `type` enum('spend','increase') NOT NULL,
  `money_type` enum('money','credit') NOT NULL,
  `amount` float unsigned NOT NULL DEFAULT '0',
  `addtime` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM;

DROP TABLE IF EXISTS dbs_coupon_code;
CREATE TABLE `dbs_coupon_code` (
  `c_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `m_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '商家ID',
  `m_name` varchar(255) NOT NULL COMMENT '商家名称',
  `c_type` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `money_max` float unsigned NOT NULL DEFAULT '0',
  `money_reduce` float unsigned NOT NULL DEFAULT '0',
  `money_amount` float unsigned NOT NULL DEFAULT '0',
  `expiry_type` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `expiry` int(11) unsigned NOT NULL DEFAULT '0',
  `price_type` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `price` float unsigned NOT NULL DEFAULT '0',
  `amount` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `fetched_amount` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `sort_order` smallint(5) unsigned NOT NULL DEFAULT '9999',
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `addtime` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`c_id`),
  KEY `m_id` (`m_id`)
) ENGINE=MyISAM ;

DROP TABLE IF EXISTS dbs_coupon_code_best;
CREATE TABLE `dbs_coupon_code_best` (
  `c_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `expiry` int(11) unsigned NOT NULL DEFAULT '0',
  `sort_order` smallint(5) unsigned NOT NULL DEFAULT '0',
  KEY `sort_order` (`sort_order`),
  KEY `c_id` (`c_id`)
) ENGINE=MyISAM ;

DROP TABLE IF EXISTS dbs_coupon_code_category;
CREATE TABLE `dbs_coupon_code_category` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `sort_order` smallint(5) unsigned NOT NULL DEFAULT '0',
  `parent_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `show_index` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `parent_dept_id` (`parent_id`)
) ENGINE=MyISAM ;

DROP TABLE IF EXISTS dbs_coupon_code_codes;
CREATE TABLE `dbs_coupon_code_codes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `c_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `code` varchar(30) NOT NULL,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `nick` varchar(50) NOT NULL,
  `fetch_time` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `c_id` (`c_id`,`user_id`),
  KEY `fetch_time` (`fetch_time`)
) ENGINE=MyISAM ;

DROP TABLE IF EXISTS dbs_coupon_code_data;
CREATE TABLE `dbs_coupon_code_data` (
  `c_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '优惠码ID',
  `fetch_limit` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `directions` text NOT NULL COMMENT '使用说明',
  `prompt` text NOT NULL COMMENT '温馨提示',
  `yesterdayfetched` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `dayfetched` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `weekfetched` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `monthfetched` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(11) unsigned NOT NULL DEFAULT '0',
  KEY `c_id` (`c_id`),
  KEY `yesterdayfetched` (`yesterdayfetched`,`dayfetched`,`weekfetched`,`monthfetched`)
) ENGINE=MyISAM ;

DROP TABLE IF EXISTS dbs_coupon_code_mall;
CREATE TABLE `dbs_coupon_code_mall` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `c_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `name` varchar(200) NOT NULL,
  `name_match` varchar(255) NOT NULL,
  `website` varchar(200) NOT NULL,
  `gourl` varchar(150) NOT NULL DEFAULT '' COMMENT '购买跳转地址',
  `tel` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `how2use` text NOT NULL,
  `logo` varchar(100) NOT NULL,
  `figure_image` varchar(100) NOT NULL,
  `sort_order` smallint(5) unsigned NOT NULL DEFAULT '9999',
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `hitnum` mediumint(8) unsigned NOT NULL DEFAULT '1',
  `yesterdaysearched` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `daysearched` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `weeksearched` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `monthsearched` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `c_id` (`c_id`),
  KEY `is_active` (`is_active`),
  KEY `yesterdaysearched` (`yesterdaysearched`,`daysearched`,`weeksearched`,`monthsearched`),
  FULLTEXT KEY `name_match` (`name_match`)
) ENGINE=MyISAM ;

DROP TABLE IF EXISTS dbs_coupon_mall_rec;
CREATE TABLE `dbs_coupon_mall_rec` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `c_id` smallint(5) unsigned NOT NULL,
  `position` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `sort_order` smallint(5) unsigned NOT NULL DEFAULT '10000',
  PRIMARY KEY (`id`),
  KEY `position` (`position`,`sort_order`)
) ENGINE=MyISAM ;

DROP TABLE IF EXISTS dbs_friend_link;
CREATE TABLE `dbs_friend_link` (
  `link_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `site_name` varchar(100) NOT NULL,
  `position_id` tinyint(3) unsigned NOT NULL,
  `link_type` tinyint(1) unsigned NOT NULL,
  `link_url` varchar(200) NOT NULL,
  `sort_order` smallint(5) unsigned NOT NULL DEFAULT '1',
  `link_code` varchar(150) NOT NULL,
  PRIMARY KEY (`link_id`),
  KEY `position_id` (`position_id`,`link_type`,`sort_order`)
) ENGINE=MyISAM ;

DROP TABLE IF EXISTS dbs_mall_promotion;
CREATE TABLE `dbs_mall_promotion` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `cate_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `m_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `m_name` varchar(200) NOT NULL,
  `title` varchar(200) NOT NULL,
  `gourl` varchar(150) NOT NULL DEFAULT '' COMMENT '购买跳转地址',
  `description` text NOT NULL,
  `logo` varchar(100) NOT NULL,
  `sort_order` smallint(5) unsigned NOT NULL DEFAULT '9999',
  `expiry` int(11) unsigned NOT NULL DEFAULT '0',
  `addtime` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `c_id` (`cate_id`),
  KEY `m_id` (`m_id`)
) ENGINE=MyISAM ;

DROP TABLE IF EXISTS dbs_mall_zhekou;
CREATE TABLE `dbs_mall_zhekou` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `cate_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `m_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `m_name` varchar(200) NOT NULL,
  `title` varchar(200) NOT NULL,
  `gourl` varchar(150) NOT NULL DEFAULT '' COMMENT '购买跳转地址',
  `description` text NOT NULL,
  `logo` varchar(100) NOT NULL,
  `sort_order` smallint(5) unsigned NOT NULL DEFAULT '9999',
  `price` float unsigned NOT NULL DEFAULT '0',
  `addtime` int(11) unsigned NOT NULL DEFAULT '0',
  `use_coupon` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `c_id` (`cate_id`),
  KEY `m_id` (`m_id`)
) ENGINE=MyISAM  ;

DROP TABLE IF EXISTS dbs_payment;
CREATE TABLE `dbs_payment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `nick` varchar(50) NOT NULL,
  `out_trade_no` varchar(50) NOT NULL,
  `trade_no` varchar(50) NOT NULL,
  `amount` float unsigned NOT NULL DEFAULT '0',
  `content` varchar(255) NOT NULL,
  `addtime` int(11) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '101',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`addtime`)
) ENGINE=MyISAM ;

DROP TABLE IF EXISTS dbs_site_config;
CREATE TABLE `dbs_site_config` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `code` varchar(30) NOT NULL DEFAULT '',
  `type` varchar(10) NOT NULL DEFAULT '',
  `store_range` varchar(255) NOT NULL DEFAULT '',
  `store_dir` varchar(255) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM ;

DROP TABLE IF EXISTS dbs_user;
CREATE TABLE `dbs_user` (
  `user_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(60) NOT NULL DEFAULT '',
  `nick` varchar(60) NOT NULL DEFAULT '',
  `name` varchar(40) CHARACTER SET ucs2 NOT NULL,
  `password` char(32) NOT NULL,
  `last_login` int(11) unsigned NOT NULL DEFAULT '0',
  `last_ip` varchar(15) NOT NULL DEFAULT '',
  `msn` varchar(60) NOT NULL,
  `qq` varchar(20) NOT NULL,
  `tel_phone` varchar(20) NOT NULL,
  `mobile_phone` varchar(20) NOT NULL,
  `addr` varchar(100) NOT NULL COMMENT '地址',
  `is_locked` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '封锁',
  `money` float unsigned NOT NULL DEFAULT '0' COMMENT '金额',
  `credit` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '积分',
  `addtime` int(11) unsigned NOT NULL DEFAULT '0',
  `invite` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  KEY `invite` (`invite`)
) ENGINE=MyISAM ;

DROP TABLE IF EXISTS dbs_user_platform;
CREATE TABLE `dbs_user_platform` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `type` varchar(10) NOT NULL,
  `openid` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM ;

DROP TABLE IF EXISTS dbs_zhekou_category;
CREATE TABLE `dbs_zhekou_category` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `sort_order` smallint(5) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM ;