/*
Navicat MySQL Data Transfer

Source Server         : local
Source Server Version : 50527
Source Host           : 127.0.0.1:3306
Source Database       : fxdemo

Target Server Type    : MYSQL
Target Server Version : 50527
File Encoding         : 65001

Date: 2015-10-08 11:03:04
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for dmo_user
-- ----------------------------
DROP TABLE IF EXISTS `dmo_user`;
CREATE TABLE `dmo_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主',
  `name` varchar(30) DEFAULT NULL COMMENT '名字',
  `psw` varchar(32) DEFAULT NULL COMMENT '密码',
  `lastlogin` int(11) DEFAULT NULL COMMENT '最后登录',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of dmo_user
-- ----------------------------
INSERT INTO `dmo_user` VALUES ('1', 'admin', '0192023a7bbd73250516f069df18b500', '1');
INSERT INTO `dmo_user` VALUES ('2', 'admin2', '0192023a7bbd73250516f069df18b500', '1');
