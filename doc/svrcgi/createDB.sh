#!/bin/bash

# 本文脚本是用于服务器创建，小直播业务数据库以及表结构。
# 1. 确认服务器，安装了MySQL5.6 及以上版本，并且mysql服务处在开启状态
#        > ps -ef | grep mysqld
# 
# 2. 将本脚本，上传到服务器，并运行。

CREATE database live_demo;
CREATE USER live_user IDENTIFIED BY 'live_pwd';
GRANT ALL PRIVILEGES ON live_demo.* TO 'live_user'@localhost IDENTIFIED BY 'live_pwd';
use live_demo;
CREATE TABLE live_data (
  userid varchar(128) NOT NULL,
  stream_id varchar(128) NOT NULL,
  groupid varchar(128) DEFAULT NULL,
  title varchar(128) DEFAULT NULL,
  nickname varchar(128) DEFAULT NULL,
  headpic varchar(255) DEFAULT NULL,
  frontcover varchar(255) DEFAULT NULL,
  location varchar(128) DEFAULT NULL,
  push_url varchar(255) NOT NULL DEFAULT '',
  status int(4) NOT NULL DEFAULT '0',
  like_count int(11) NOT NULL DEFAULT '0',
  viewer_count int(11) NOT NULL DEFAULT '0',
  check_status int(4) DEFAULT '0',
  forbidflag int(2) DEFAULT NULL,
   `desc` varchar(255) DEFAULT NULL,
  create_time timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  play_url varchar(255) NOT NULL,
  PRIMARY KEY (userid),
  hls_play_url varchar(255) NOT NULL,
  KEY stream_id (stream_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE tape_data (
  userid varchar(50) NOT NULL,
  file_id varchar(150) NOT NULL,
  title varchar(128) DEFAULT NULL,
  nickname varchar(128) DEFAULT NULL,
  headpic varchar(255) DEFAULT NULL,
  frontcover varchar(255) DEFAULT NULL,
  location varchar(128) DEFAULT NULL,
  play_url varchar(255) DEFAULT NULL,
  like_count int(11) NOT NULL DEFAULT '0',
  viewer_count int(11) NOT NULL DEFAULT '0',
  `desc` varchar(255) DEFAULT NULL,
  create_time timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  hls_play_url varchar(255) DEFAULT NULL,
  start_time timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (userid,file_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE group_info (
  groupid varchar(128) NOT NULL DEFAULT '',
  userid varchar(50) NOT NULL,
  liveuserid varchar(50) NOT NULL,
  nickname varchar(128) DEFAULT NULL,
  headpic varchar(255) DEFAULT NULL,
  PRIMARY KEY (groupid,userid,liveuserid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE UGC_data (
  userid varchar(50) NOT NULL,
  file_id varchar(150) NOT NULL,
  title varchar(128) DEFAULT NULL,
  nickname varchar(128) DEFAULT NULL,
  headpic varchar(255) DEFAULT NULL,
  frontcover varchar(255) DEFAULT NULL,
  location varchar(128) DEFAULT NULL,
  play_url varchar(255) DEFAULT NULL,
  `desc` varchar(255) DEFAULT NULL,
  create_time timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (userid,file_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;