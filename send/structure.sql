-- phpMyAdmin SQL Dump
-- version 2.6.4-pl3
-- http://www.phpmyadmin.net
-- 
-- Host: db01.server.com
-- Generation Time: Feb 25, 2013 at 08:51 PM
-- Server version: 5.0.96
-- PHP Version: 5.3.3-7+squeeze14
-- 
-- Database: `db01`
-- 
CREATE DATABASE `db01` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE db01;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_bankitems`
-- 

CREATE TABLE `dvoo_bankitems` (
  `tid` int(11) NOT NULL,
  `cday` tinyint(4) NOT NULL,
  `iid` smallint(6) NOT NULL,
  `icount` smallint(6) NOT NULL,
  `ibroken` tinyint(4) NOT NULL,
  PRIMARY KEY  (`tid`,`cday`,`iid`,`ibroken`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_buildings`
-- 

CREATE TABLE `dvoo_buildings` (
  `id` int(11) NOT NULL,
  `active` tinyint(4) NOT NULL default '1',
  `name` varchar(128) NOT NULL,
  `temporary` tinyint(4) NOT NULL,
  `img` varchar(32) NOT NULL,
  `parent` int(11) NOT NULL,
  `vp` mediumint(9) NOT NULL default '0',
  `ap` mediumint(9) NOT NULL default '0',
  `bp` tinyint(4) default NULL,
  `rsc` varchar(128) default NULL,
  `desc` text,
  PRIMARY KEY  (`id`),
  KEY `parent` (`parent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_buildings_old`
-- 

CREATE TABLE `dvoo_buildings_old` (
  `id` int(11) NOT NULL,
  `active` tinyint(4) NOT NULL default '1',
  `name` varchar(128) NOT NULL,
  `temporary` tinyint(4) NOT NULL,
  `img` varchar(32) NOT NULL,
  `parent` int(11) NOT NULL,
  `vp` mediumint(9) NOT NULL default '0',
  `ap` mediumint(9) NOT NULL default '0',
  `bp` tinyint(4) default NULL,
  `rsc` varchar(128) default NULL,
  PRIMARY KEY  (`id`),
  KEY `parent` (`parent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_citizen_rewards`
-- 

CREATE TABLE `dvoo_citizen_rewards` (
  `uid` int(11) NOT NULL,
  `reward` varchar(64) NOT NULL,
  `count` int(11) NOT NULL,
  UNIQUE KEY `Index 2` (`uid`,`reward`),
  KEY `count` (`count`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_citizens`
-- 

CREATE TABLE `dvoo_citizens` (
  `id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `code` varchar(64) NOT NULL,
  `scode` varchar(64) NOT NULL,
  `avatar` varchar(64) NOT NULL,
  `oldnames` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `code` (`code`),
  KEY `scode` (`scode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_eastereggs`
-- 

CREATE TABLE `dvoo_eastereggs` (
  `ee` varchar(64) NOT NULL default '',
  `cc` varchar(96) NOT NULL default '',
  PRIMARY KEY  (`ee`,`cc`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_events_signup`
-- 

CREATE TABLE `dvoo_events_signup` (
  `event` int(10) NOT NULL,
  `user` int(11) NOT NULL,
  `option` varchar(64) NOT NULL,
  `stamp` int(11) NOT NULL,
  PRIMARY KEY  (`event`,`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_expeditions`
-- 

CREATE TABLE `dvoo_expeditions` (
  `id` int(11) NOT NULL auto_increment,
  `tid` int(11) NOT NULL,
  `day` tinyint(4) NOT NULL,
  `cid` int(11) NOT NULL,
  `length` tinyint(4) NOT NULL,
  `name` varchar(128) NOT NULL,
  `route` text NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `tid` (`tid`,`day`,`cid`,`name`)
) ENGINE=MyISAM AUTO_INCREMENT=18309 DEFAULT CHARSET=utf8 AUTO_INCREMENT=18309 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_feedback`
-- 

CREATE TABLE `dvoo_feedback` (
  `uid` varchar(32) NOT NULL,
  `time` int(11) NOT NULL,
  `feedback` text NOT NULL,
  PRIMARY KEY  (`uid`,`time`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_fl_friend`
-- 

CREATE TABLE `dvoo_fl_friend` (
  `a` int(11) NOT NULL,
  `b` int(11) NOT NULL,
  PRIMARY KEY  (`a`,`b`),
  KEY `a` (`a`),
  KEY `b` (`b`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_fl_ignore`
-- 

CREATE TABLE `dvoo_fl_ignore` (
  `a` int(11) NOT NULL,
  `b` int(11) NOT NULL,
  PRIMARY KEY  (`a`,`b`),
  KEY `a` (`a`),
  KEY `b` (`b`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_fl_invite`
-- 

CREATE TABLE `dvoo_fl_invite` (
  `a` int(11) NOT NULL,
  `b` int(11) NOT NULL,
  `stamp` int(11) NOT NULL,
  PRIMARY KEY  (`a`,`b`),
  KEY `ifrom` (`a`),
  KEY `ito` (`b`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_fl_mailbox`
-- 

CREATE TABLE `dvoo_fl_mailbox` (
  `id` int(11) NOT NULL auto_increment,
  `sender` int(11) NOT NULL,
  `receiver` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL default '(Kein Betreff)',
  `message` longtext NOT NULL,
  `send` int(11) default NULL,
  `read` int(11) default NULL,
  `deleted` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `sender` (`sender`),
  KEY `receiver` (`receiver`),
  KEY `send` (`send`),
  KEY `read` (`read`),
  KEY `new` (`receiver`,`read`)
) ENGINE=MyISAM AUTO_INCREMENT=4212 DEFAULT CHARSET=utf8 AUTO_INCREMENT=4212 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_group_citizens`
-- 

CREATE TABLE `dvoo_group_citizens` (
  `gid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  PRIMARY KEY  (`gid`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_groups`
-- 

CREATE TABLE `dvoo_groups` (
  `gid` int(11) NOT NULL auto_increment,
  `tid` int(11) NOT NULL,
  `uid` varchar(64) NOT NULL,
  `name` varchar(128) NOT NULL,
  `route` varchar(128) NOT NULL,
  `persistent` tinyint(4) NOT NULL,
  `stamp` int(11) NOT NULL,
  PRIMARY KEY  (`gid`)
) ENGINE=MyISAM AUTO_INCREMENT=1557 DEFAULT CHARSET=utf8 AUTO_INCREMENT=1557 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_items`
-- 

CREATE TABLE `dvoo_items` (
  `iid` mediumint(9) NOT NULL,
  `iname` varchar(128) NOT NULL,
  `iimg` varchar(32) NOT NULL,
  `icat` varchar(64) NOT NULL,
  PRIMARY KEY  (`iid`),
  FULLTEXT KEY `iname` (`iname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_login_log`
-- 

CREATE TABLE `dvoo_login_log` (
  `id` int(11) NOT NULL auto_increment,
  `time` int(11) NOT NULL,
  `ip` varchar(24) NOT NULL,
  `ref` varchar(512) NOT NULL,
  `p` tinyint(4) NOT NULL,
  `k` varchar(128) NOT NULL,
  `n` varchar(128) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `ip` (`ip`),
  KEY `k` (`k`)
) ENGINE=MyISAM AUTO_INCREMENT=460996 DEFAULT CHARSET=utf8 AUTO_INCREMENT=460996 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_rawdata`
-- 

CREATE TABLE `dvoo_rawdata` (
  `id` int(11) NOT NULL COMMENT 'DV id',
  `time` int(11) NOT NULL COMMENT 'call time',
  `xml` mediumtext NOT NULL COMMENT 'data',
  PRIMARY KEY  (`id`,`time`),
  KEY `uid` (`id`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_rewards`
-- 

CREATE TABLE `dvoo_rewards` (
  `name` varchar(64) NOT NULL,
  `img` varchar(16) NOT NULL,
  `rare` tinyint(4) NOT NULL,
  PRIMARY KEY  (`name`),
  KEY `rare` (`rare`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_soul`
-- 

CREATE TABLE `dvoo_soul` (
  `uid` int(11) NOT NULL,
  `xml` longtext character set latin1 NOT NULL,
  `stamp` int(11) NOT NULL,
  PRIMARY KEY  (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_stat_camouflage`
-- 

CREATE TABLE `dvoo_stat_camouflage` (
  `uid` mediumint(9) NOT NULL,
  `tid` mediumint(9) NOT NULL,
  `day` tinyint(4) NOT NULL,
  `x` tinyint(4) NOT NULL,
  `y` tinyint(4) NOT NULL,
  `ze` smallint(6) NOT NULL COMMENT 'zone exploration level',
  `zc` smallint(6) NOT NULL COMMENT 'zombie count',
  `gc` tinyint(4) NOT NULL COMMENT 'got caught?',
  PRIMARY KEY  (`uid`,`tid`,`day`,`x`,`y`),
  KEY `ze` (`ze`),
  KEY `zc` (`zc`),
  KEY `gc` (`gc`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_stat_soul`
-- 

CREATE TABLE `dvoo_stat_soul` (
  `uid` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  PRIMARY KEY  (`uid`),
  KEY `score` (`score`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_stat_zombies`
-- 

CREATE TABLE `dvoo_stat_zombies` (
  `tid` int(11) NOT NULL,
  `day` smallint(6) NOT NULL,
  `z` int(11) NOT NULL,
  `v` int(11) NOT NULL,
  PRIMARY KEY  (`tid`,`day`),
  KEY `day` (`day`,`z`),
  KEY `z` (`z`),
  KEY `day_2` (`day`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_storm`
-- 

CREATE TABLE `dvoo_storm` (
  `tid` int(10) NOT NULL,
  `day` int(3) NOT NULL,
  `dir` int(1) NOT NULL,
  `uid` int(10) NOT NULL,
  PRIMARY KEY  (`tid`,`uid`,`day`),
  UNIQUE KEY `UserInput` (`tid`,`day`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_timeplanner`
-- 

CREATE TABLE `dvoo_timeplanner` (
  `tid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `day` smallint(6) NOT NULL,
  `tp0` tinyint(4) NOT NULL,
  `tp1` tinyint(4) NOT NULL,
  `tp2` tinyint(4) NOT NULL,
  `tp3` tinyint(4) NOT NULL,
  `tp4` tinyint(4) NOT NULL,
  `tp5` tinyint(4) NOT NULL,
  `tp6` tinyint(4) NOT NULL,
  `tp7` tinyint(4) NOT NULL,
  `tp8` tinyint(4) NOT NULL,
  `tp9` tinyint(4) NOT NULL,
  `tp10` tinyint(4) NOT NULL,
  `tp11` tinyint(4) NOT NULL,
  `tp12` tinyint(4) NOT NULL,
  `tp13` tinyint(4) NOT NULL,
  `tp14` tinyint(4) NOT NULL,
  `tp15` tinyint(4) NOT NULL,
  `tp16` tinyint(4) NOT NULL,
  `tp17` tinyint(4) NOT NULL,
  `tp18` tinyint(4) NOT NULL,
  `tp19` tinyint(4) NOT NULL,
  `tp20` tinyint(4) NOT NULL,
  `tp21` tinyint(4) NOT NULL,
  `tp22` tinyint(4) NOT NULL,
  `tp23` tinyint(4) NOT NULL,
  `water` tinyint(4) NOT NULL default '0',
  `food` tinyint(4) NOT NULL default '0',
  `drug` tinyint(4) NOT NULL default '0',
  `drug2` tinyint(4) NOT NULL,
  `alcohol` tinyint(4) NOT NULL,
  `coffee` tinyint(4) NOT NULL default '0',
  `gamble` tinyint(4) NOT NULL,
  `alarm` tinyint(4) NOT NULL,
  `lunge` tinyint(4) NOT NULL,
  `thirsty` tinyint(4) NOT NULL,
  `hangover` tinyint(4) NOT NULL,
  `paralyzed` tinyint(4) NOT NULL,
  `clean` tinyint(4) NOT NULL,
  `topform` tinyint(4) NOT NULL,
  `safe` tinyint(4) NOT NULL,
  `sleep` tinyint(4) NOT NULL,
  PRIMARY KEY  (`tid`,`uid`,`day`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_titles`
-- 

CREATE TABLE `dvoo_titles` (
  `name` varchar(64) character set latin1 NOT NULL,
  `reward` varchar(64) character set latin1 NOT NULL,
  `min` int(11) NOT NULL,
  `max` int(11) NOT NULL,
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_town_blueprints`
-- 

CREATE TABLE `dvoo_town_blueprints` (
  `tid` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  PRIMARY KEY  (`tid`,`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_town_buildings`
-- 

CREATE TABLE `dvoo_town_buildings` (
  `tid` int(11) NOT NULL,
  `day` tinyint(4) NOT NULL,
  `bid` int(11) NOT NULL,
  PRIMARY KEY  (`tid`,`day`,`bid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_town_citizens`
-- 

CREATE TABLE `dvoo_town_citizens` (
  `town_id` int(11) NOT NULL,
  `citizen_id` int(11) NOT NULL,
  `ban` tinyint(4) NOT NULL,
  `hero` tinyint(4) NOT NULL,
  `job` varchar(32) NOT NULL,
  `dead` tinyint(4) NOT NULL,
  `out` tinyint(4) NOT NULL,
  `x` tinyint(4) NOT NULL,
  `y` tinyint(4) NOT NULL,
  PRIMARY KEY  (`town_id`,`citizen_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_towns`
-- 

CREATE TABLE `dvoo_towns` (
  `id` int(11) NOT NULL COMMENT 'town id',
  `name` varchar(128) NOT NULL COMMENT 'name',
  `hard` tinyint(4) NOT NULL default '0' COMMENT 'name',
  `x` tinyint(4) NOT NULL,
  `y` tinyint(4) NOT NULL,
  `h` tinyint(4) NOT NULL,
  `w` tinyint(4) NOT NULL,
  `day` tinyint(4) NOT NULL,
  `citizens` tinyint(2) NOT NULL,
  `water` tinyint(4) NOT NULL,
  `door` tinyint(4) NOT NULL,
  `chaos` tinyint(4) NOT NULL,
  `devast` tinyint(4) NOT NULL,
  `devast_on` int(11) default NULL,
  `stamp` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='towns';

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_xml`
-- 

CREATE TABLE `dvoo_xml` (
  `uid` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  `day` mediumint(9) NOT NULL,
  `xml` longtext character set latin1 NOT NULL,
  `scode` varchar(64) character set latin1 NOT NULL,
  `stamp` int(11) NOT NULL,
  PRIMARY KEY  (`uid`,`tid`,`day`),
  KEY `scode` (`scode`),
  KEY `stamp` (`stamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_zones_buildings`
-- 

CREATE TABLE `dvoo_zones_buildings` (
  `tid` int(11) NOT NULL,
  `x` tinyint(4) NOT NULL,
  `y` tinyint(4) NOT NULL,
  `name` varchar(128) default NULL,
  `type` tinyint(4) default NULL,
  `dig` tinyint(4) NOT NULL default '0',
  `depleted` tinyint(1) NOT NULL default '0',
  `content` text,
  `items` text,
  `stamp` int(11) NOT NULL,
  PRIMARY KEY  (`tid`,`x`,`y`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_zones_regen`
-- 

CREATE TABLE `dvoo_zones_regen` (
  `tid` int(11) NOT NULL,
  `day` tinyint(4) NOT NULL,
  `x` tinyint(4) NOT NULL,
  `y` tinyint(4) NOT NULL,
  `r` tinyint(4) NOT NULL,
  `on` int(11) NOT NULL,
  `by` varchar(64) NOT NULL,
  PRIMARY KEY  (`tid`,`day`,`x`,`y`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_zones_scout`
-- 

CREATE TABLE `dvoo_zones_scout` (
  `tid` int(11) NOT NULL,
  `day` tinyint(4) NOT NULL,
  `x` tinyint(4) NOT NULL,
  `y` tinyint(4) NOT NULL,
  `z` tinyint(4) NOT NULL,
  `on` int(11) NOT NULL,
  `by` varchar(64) NOT NULL,
  PRIMARY KEY  (`tid`,`day`,`x`,`y`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_zones_visit`
-- 

CREATE TABLE `dvoo_zones_visit` (
  `tid` int(11) NOT NULL,
  `day` tinyint(4) NOT NULL,
  `x` tinyint(4) NOT NULL,
  `y` tinyint(4) NOT NULL,
  `auto` tinyint(4) NOT NULL,
  `dried` tinyint(4) NOT NULL,
  `z` tinyint(4) NOT NULL,
  `items` text NOT NULL,
  `on` int(11) NOT NULL,
  `by` varchar(64) NOT NULL,
  PRIMARY KEY  (`tid`,`day`,`x`,`y`,`auto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `dvoo_zones_zones`
-- 

CREATE TABLE `dvoo_zones_zones` (
  `tid` int(11) NOT NULL,
  `day` tinyint(4) NOT NULL,
  `x` tinyint(4) NOT NULL,
  `y` tinyint(4) NOT NULL,
  `nvt` tinyint(4) NOT NULL,
  `tag` tinyint(4) default NULL,
  `danger` tinyint(4) default NULL,
  `z` tinyint(4) default NULL,
  `on` int(11) NOT NULL,
  `by` varchar(64) NOT NULL default 'Blick aus dem Tor',
  `stamp` int(11) NOT NULL,
  PRIMARY KEY  (`tid`,`day`,`x`,`y`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='zone data as default in xml';
