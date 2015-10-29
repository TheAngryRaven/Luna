/*
Navicat MySQL Data Transfer

Source Server         : WAMP
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : moon

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2015-10-27 18:58:51
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `t_contact`
-- ----------------------------
DROP TABLE IF EXISTS `t_contact`;
CREATE TABLE `t_contact` (
  `userID` varchar(128) NOT NULL,
  `contactID` varchar(128) NOT NULL,
  `encryptedPrivateKey` longtext NOT NULL,
  `publicKey` mediumtext NOT NULL,
  `lastRotation` datetime NOT NULL,
  PRIMARY KEY (`userID`),
  KEY `fk_contact` (`contactID`),
  CONSTRAINT `fk_contact` FOREIGN KEY (`contactID`) REFERENCES `t_user` (`userID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_contact_user` FOREIGN KEY (`userID`) REFERENCES `t_user` (`userID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of t_contact
-- ----------------------------

-- ----------------------------
-- Table structure for `t_drop`
-- ----------------------------
DROP TABLE IF EXISTS `t_drop`;
CREATE TABLE `t_drop` (
  `dropID` varchar(128) NOT NULL,
  `creationDate` datetime NOT NULL,
  `passwordHash` varchar(256) DEFAULT NULL,
  `messageType` int(4) NOT NULL DEFAULT '0',
  `message` longtext NOT NULL,
  PRIMARY KEY (`dropID`),
  KEY `fk_drop_type` (`messageType`),
  CONSTRAINT `fk_drop_type` FOREIGN KEY (`messageType`) REFERENCES `t_drop_type` (`typeID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of t_drop
-- ----------------------------

-- ----------------------------
-- Table structure for `t_drop_type`
-- ----------------------------
DROP TABLE IF EXISTS `t_drop_type`;
CREATE TABLE `t_drop_type` (
  `typeID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`typeID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of t_drop_type
-- ----------------------------
INSERT INTO `t_drop_type` VALUES ('0', 'text');
INSERT INTO `t_drop_type` VALUES ('1', 'image');

-- ----------------------------
-- Table structure for `t_email_type`
-- ----------------------------
DROP TABLE IF EXISTS `t_email_type`;
CREATE TABLE `t_email_type` (
  `typeID` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`typeID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of t_email_type
-- ----------------------------
INSERT INTO `t_email_type` VALUES ('0', 'no');
INSERT INTO `t_email_type` VALUES ('1', 'message');
INSERT INTO `t_email_type` VALUES ('2', 'daily');

-- ----------------------------
-- Table structure for `t_global_key`
-- ----------------------------
DROP TABLE IF EXISTS `t_global_key`;
CREATE TABLE `t_global_key` (
  `userID` varchar(128) NOT NULL,
  `encryptedPrivateKey` longtext NOT NULL,
  `publicKey` mediumtext NOT NULL,
  `lastRotation` datetime NOT NULL,
  PRIMARY KEY (`userID`),
  CONSTRAINT `fk_global_user` FOREIGN KEY (`userID`) REFERENCES `t_user` (`userID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of t_global_key
-- ----------------------------

-- ----------------------------
-- Table structure for `t_key_type`
-- ----------------------------
DROP TABLE IF EXISTS `t_key_type`;
CREATE TABLE `t_key_type` (
  `typeID` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`typeID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Records of t_key_type
-- ----------------------------
INSERT INTO `t_key_type` VALUES ('0', 'global');
INSERT INTO `t_key_type` VALUES ('1', 'contact');

-- ----------------------------
-- Table structure for `t_message`
-- ----------------------------
DROP TABLE IF EXISTS `t_message`;
CREATE TABLE `t_message` (
  `senderID` varchar(128) NOT NULL,
  `recipientID` varchar(128) NOT NULL,
  `creationDate` datetime NOT NULL,
  `keyType` int(11) NOT NULL DEFAULT '0',
  `key` varchar(255) NOT NULL,
  `messageType` int(11) NOT NULL DEFAULT '0',
  `message` longtext NOT NULL,
  PRIMARY KEY (`senderID`),
  KEY `fk_message_recipient` (`recipientID`),
  KEY `fk_message_key_type` (`keyType`),
  KEY `fk_message_type` (`messageType`),
  CONSTRAINT `fk_message_key_type` FOREIGN KEY (`keyType`) REFERENCES `t_key_type` (`typeID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_message_recipient` FOREIGN KEY (`recipientID`) REFERENCES `t_user` (`userID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_message_sender` FOREIGN KEY (`senderID`) REFERENCES `t_user` (`userID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_message_type` FOREIGN KEY (`messageType`) REFERENCES `t_message_type` (`typeID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of t_message
-- ----------------------------

-- ----------------------------
-- Table structure for `t_message_type`
-- ----------------------------
DROP TABLE IF EXISTS `t_message_type`;
CREATE TABLE `t_message_type` (
  `typeID` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`typeID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of t_message_type
-- ----------------------------
INSERT INTO `t_message_type` VALUES ('0', 'text');
INSERT INTO `t_message_type` VALUES ('1', 'image');

-- ----------------------------
-- Table structure for `t_setting_email`
-- ----------------------------
DROP TABLE IF EXISTS `t_setting_email`;
CREATE TABLE `t_setting_email` (
  `userID` varchar(128) NOT NULL,
  `type` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`userID`),
  KEY `type` (`type`),
  CONSTRAINT `fk_email` FOREIGN KEY (`userID`) REFERENCES `t_user` (`userID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_email_type` FOREIGN KEY (`type`) REFERENCES `t_email_type` (`typeID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of t_setting_email
-- ----------------------------

-- ----------------------------
-- Table structure for `t_user`
-- ----------------------------
DROP TABLE IF EXISTS `t_user`;
CREATE TABLE `t_user` (
  `userID` varchar(128) NOT NULL,
  `userName` varchar(256) NOT NULL,
  `email` varchar(256) DEFAULT NULL,
  `passwordHash` varchar(256) NOT NULL,
  `encryptionHash` varchar(256) NOT NULL,
  PRIMARY KEY (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of t_user
-- ----------------------------
