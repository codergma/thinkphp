-- MySQL dump 10.13  Distrib 5.5.44, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: thinkphp
-- ------------------------------------------------------
-- Server version	5.5.44-0ubuntu0.14.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cg_log_user_logins`
--

DROP TABLE IF EXISTS `cg_log_user_logins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cg_log_user_logins` (
  `login_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `login_time` datetime NOT NULL,
  `login_ip` varchar(16) NOT NULL,
  `login_src` tinyint(3) unsigned DEFAULT '0' COMMENT '0:商城  1:webapp  2:App',
  `login_remark` varchar(30) DEFAULT NULL COMMENT '登录备注信息',
  PRIMARY KEY (`login_id`),
  KEY `loginTime` (`login_time`),
  KEY `userId` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cg_log_user_logins`
--

LOCK TABLES `cg_log_user_logins` WRITE;
/*!40000 ALTER TABLE `cg_log_user_logins` DISABLE KEYS */;
/*!40000 ALTER TABLE `cg_log_user_logins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cg_user_tokens`
--

DROP TABLE IF EXISTS `cg_user_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cg_user_tokens` (
  `id` int(10) unsigned NOT NULL,
  `userId` int(10) unsigned NOT NULL,
  `userAgent` varchar(40) NOT NULL,
  `token` varchar(40) NOT NULL,
  `expire` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='自动登录';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cg_user_tokens`
--

LOCK TABLES `cg_user_tokens` WRITE;
/*!40000 ALTER TABLE `cg_user_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `cg_user_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cg_users`
--

DROP TABLE IF EXISTS `cg_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cg_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `login_name` varchar(20) NOT NULL,
  `login_secret` int(11) NOT NULL,
  `login_pwd` varchar(50) NOT NULL,
  `user_sex` tinyint(4) DEFAULT '0',
  `user_type` tinyint(4) DEFAULT '0',
  `user_name` varchar(20) DEFAULT NULL,
  `user_qq` varchar(20) DEFAULT NULL,
  `user_phone` char(11) DEFAULT NULL,
  `user_email` varchar(50) DEFAULT NULL,
  `user_score` int(11) DEFAULT '0',
  `user_photo` varchar(150) DEFAULT NULL,
  `user_total_score` int(11) DEFAULT '0',
  `user_status` tinyint(4) DEFAULT '1',
  `user_flag` tinyint(4) DEFAULT '1',
  `create_time` datetime DEFAULT NULL,
  `last_ip` varchar(16) DEFAULT NULL,
  `last_time` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `userStatus` (`user_status`,`user_flag`),
  KEY `loginName` (`login_name`),
  KEY `userPhone` (`user_phone`),
  KEY `userEmail` (`user_email`),
  KEY `userType` (`user_type`,`user_flag`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cg_users`
--

LOCK TABLES `cg_users` WRITE;
/*!40000 ALTER TABLE `cg_users` DISABLE KEYS */;
INSERT INTO `cg_users` VALUES (9,'gd_guangzhou',7902,'d6a3fe736d32101b2070e4d7667db639',0,1,'广东广州店铺',NULL,'15918671994',NULL,0,NULL,0,1,1,'2015-05-08 10:29:39','127.0.0.1','2015-10-12 13:54:06'),(10,'gd_shenzhen',1679,'7fdc1a0615463a521a59d1da3fb49e5b',0,1,'广东深圳店铺',NULL,'15918671194',NULL,0,NULL,0,1,1,'2015-05-08 10:34:26',NULL,NULL),(11,'gd_zhuhai',9254,'5dd44b6e13011dcaec803ebb71eee9ed',0,1,'广东珠海店铺',NULL,'15918671294',NULL,0,NULL,0,1,1,'2015-05-08 10:35:55',NULL,NULL),(12,'gd_shantou',9880,'ab9496339c7c82a54e04ed88cef3a88e',0,1,'广东汕头店铺',NULL,'15918671394',NULL,0,NULL,0,1,1,'2015-05-08 10:37:31',NULL,NULL),(13,'gd_shaoguan',5234,'6dc085c2ffe8d99a1cce1f7d772ab23b',0,1,'广东韶关店铺',NULL,'15918671494',NULL,0,NULL,0,1,1,'2015-05-08 10:38:47',NULL,NULL),(14,'gd_foshan',3896,'f40f40cbe332a15292f9324d962422a2',0,1,'广东佛山店铺',NULL,'15918671594',NULL,0,NULL,0,1,1,'2015-05-08 10:39:51',NULL,NULL),(15,'13763316008_b',6834,'fc9fb3a4db2ebb524be31925435987dc',0,1,'张测试','','13763316008','',0,NULL,0,1,1,'2015-05-10 22:49:23','101.46.63.120','2015-06-01 21:22:19'),(16,'ceshi1',2885,'7e9b1a3e2a390f5da94b1b36e754c976',0,1,'测试店铺1',NULL,'15918671993',NULL,0,NULL,0,1,1,'2015-05-12 22:16:17','103.199.87.218','2015-05-31 22:43:31'),(17,'ceshi2',4644,'202cd234d8619d8b26ab8b2197e965ad',0,1,'测试店铺2','','15918671694','',0,NULL,0,1,1,'2015-05-12 22:17:50','103.199.87.218','2015-05-31 17:18:26'),(18,'ceshi3',8150,'eabc4d39c913b6e78f4756ae2a1daf2a',0,1,'测试店铺3','','15918671794','',0,NULL,0,1,1,'2015-05-12 22:32:48','103.199.87.218','2015-05-31 21:58:08'),(19,'ceshi21',4334,'060ee46489799f04ad97b3d11f49f2b8',0,1,'测试店铺21',NULL,'15918671894',NULL,0,NULL,0,1,1,'2015-05-13 15:07:05','223.73.155.87','2015-05-26 21:52:51'),(20,'ceshi22',1466,'453e4fd1d9a7d33a6f5c4bd0bb1fc44b',0,1,'测试店铺22',NULL,'15918671094',NULL,0,NULL,0,1,1,'2015-05-13 15:08:14','103.199.87.218','2015-06-01 08:32:52'),(21,'ceshi23',4380,'cd9a2715fdb1c8771122f978903ca74e',0,1,'测试店铺23',NULL,'15918670994',NULL,0,NULL,0,1,1,'2015-05-13 15:09:24',NULL,NULL),(22,'ceshi24',3677,'06521bce3982f140212989e332488e72',0,1,'测试店铺24',NULL,'15928671994',NULL,0,NULL,0,1,1,'2015-05-13 15:10:47',NULL,NULL),(23,'865984518_g',9799,'d5e8ee584602dce671a305c69da92f0e',0,0,'','','','865984518@qq.com',0,NULL,0,1,1,'2015-05-21 14:08:24',NULL,NULL),(24,'ceshi31',2518,'fc9fb3a4db2ebb524be31925435987dc',0,1,'测试店铺31','','15918671996','',0,NULL,0,1,1,'2015-05-26 21:06:03','211.136.253.232','2015-05-27 10:17:08'),(25,'ceshi32',8937,'2e59831193563c21a51b275788229f98',0,1,'测试店铺32','','15918671997','',0,NULL,0,1,1,'2015-05-26 21:08:13','103.199.87.218','2015-06-01 11:34:34'),(26,'ceshi33',1867,'845aa09ad90841fccdf865def2c80755',0,1,'测试店铺33','','15918671991','',0,NULL,0,1,1,'2015-05-26 21:09:53','103.199.87.218','2015-06-01 11:53:52'),(27,'ceshi34',4588,'fc9fb3a4db2ebb524be31925435987dc',0,1,'测试店铺34','','15918671998','',0,NULL,0,1,1,'2015-05-26 21:11:23',NULL,NULL),(28,'ceshi41',7274,'fc9fb3a4db2ebb524be31925435987dc',0,1,'测试店铺41','','15918671992','',0,NULL,0,1,1,'2015-05-26 21:14:00','211.136.253.198','2015-05-27 14:54:22'),(29,'ceshi42',4880,'947a7d3678d8bd05c7f21cfd0c5be50d',0,1,'测试店铺42','','15918671990','',0,NULL,0,1,1,'2015-05-26 21:20:27','103.199.87.218','2015-06-01 19:04:36'),(30,'ceshi43',1630,'fc9fb3a4db2ebb524be31925435987dc',0,1,'测试店铺43','','15918671989','',0,NULL,0,1,1,'2015-05-26 21:22:06',NULL,NULL),(31,'ceshi44',4123,'fc9fb3a4db2ebb524be31925435987dc',0,1,'测试店铺44','','15918671980','',0,NULL,0,1,1,'2015-05-26 21:24:36',NULL,NULL),(32,'ceshi51',3092,'fc9fb3a4db2ebb524be31925435987dc',0,1,'测试店铺51','','15918671988','',0,NULL,0,1,1,'2015-05-26 21:30:17','223.73.155.87','2015-05-27 20:55:46'),(33,'ceshi52',9160,'6276f96ad761e03f633d1a1b484156d8',0,1,'测试店铺52','','15918671881','',0,NULL,0,1,1,'2015-05-26 21:32:01','103.199.87.218','2015-06-01 20:32:34'),(34,'ceshi53',9425,'fc9fb3a4db2ebb524be31925435987dc',0,1,'测试店铺53','','15918671812','',0,NULL,0,1,1,'2015-05-26 21:33:27',NULL,NULL),(35,'ceshi54',3157,'fc9fb3a4db2ebb524be31925435987dc',0,1,'测试店铺54','','15918671834','',0,NULL,0,1,1,'2015-05-26 21:40:20',NULL,NULL),(36,'ceshi61',1934,'fc9fb3a4db2ebb524be31925435987dc',0,1,'测试店铺61','','15918671833','',0,NULL,0,1,1,'2015-05-26 21:42:59','223.73.155.87','2015-05-27 22:24:04'),(37,'ceshi62',1996,'0c3a4081280a5b80af5b70967cb6252a',0,1,'测试店铺62','','15918671867','',0,NULL,0,1,1,'2015-05-26 21:45:22','103.199.87.218','2015-06-01 21:44:20'),(38,'ceshi63',8163,'fc9fb3a4db2ebb524be31925435987dc',0,1,'测试店铺63','','15918671987','',0,NULL,0,1,1,'2015-05-26 21:47:10',NULL,NULL),(39,'ceshi64',3204,'5d50361a4cc2359475c8d08c34fbfcd4',0,1,'测试店铺64','','15918671857','',0,NULL,0,1,1,'2015-05-26 21:48:43',NULL,NULL),(40,'codergma',9060,'eb0b0cfa61f79d9f4df74517e231028a',0,0,'','','','',0,NULL,0,1,1,'2015-12-21 20:47:25',NULL,NULL),(42,'codergma2',9310,'e80a9d9d3c734b47aa7d0bf9584fbc63',0,0,NULL,NULL,NULL,'codergma@163.com',0,NULL,0,1,1,NULL,NULL,NULL);
/*!40000 ALTER TABLE `cg_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-01-05 18:02:08
