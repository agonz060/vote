-- MySQL dump 10.13  Distrib 5.7.15, for FreeBSD11.0 (amd64)
--
-- Host: localhost    Database: voting
-- ------------------------------------------------------
-- Server version	5.7.15-log

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
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED='5c255620-d458-11e6-b7b8-1866da87d872:1-61491';

--
-- Table structure for table `assistant_data`
--

DROP TABLE IF EXISTS `assistant_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assistant_data` (
  `poll_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `voteCmt` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`poll_id`,`user_id`),
  CONSTRAINT `assistant_data_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`poll_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assistant_data`
--

LOCK TABLES `assistant_data` WRITE;
/*!40000 ALTER TABLE `assistant_data` DISABLE KEYS */;
INSERT INTO `assistant_data` VALUES (12,3,''),(13,3,'');
/*!40000 ALTER TABLE `assistant_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `associate_promotion_data`
--

DROP TABLE IF EXISTS `associate_promotion_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `associate_promotion_data` (
  `poll_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vote` int(11) NOT NULL,
  `voteCmt` varchar(500) DEFAULT NULL,
  `action_num` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`poll_id`,`action_num`),
  KEY `poll_id` (`poll_id`),
  CONSTRAINT `associate_promotion_data_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`poll_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `associate_promotion_data`
--

LOCK TABLES `associate_promotion_data` WRITE;
/*!40000 ALTER TABLE `associate_promotion_data` DISABLE KEYS */;
INSERT INTO `associate_promotion_data` VALUES (11,3,2,'you are\n',1),(11,3,2,'GREAT!!!',2),(11,4,2,'opposed action 1 for promotion',1),(11,4,1,'in favor action 2 for promotion',2);
/*!40000 ALTER TABLE `associate_promotion_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fifth_year_appraisal_data`
--

DROP TABLE IF EXISTS `fifth_year_appraisal_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fifth_year_appraisal_data` (
  `poll_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vote` int(11) NOT NULL,
  `teachingCmts` varchar(500) DEFAULT NULL,
  `researchCmts` varchar(500) DEFAULT NULL,
  `pubServiceCmts` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`user_id`,`poll_id`),
  KEY `poll_id` (`poll_id`),
  CONSTRAINT `fifth_year_appraisal_data_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`poll_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fifth_year_appraisal_data`
--

LOCK TABLES `fifth_year_appraisal_data` WRITE;
/*!40000 ALTER TABLE `fifth_year_appraisal_data` DISABLE KEYS */;
INSERT INTO `fifth_year_appraisal_data` VALUES (9,3,4,'','',''),(9,4,2,'positive teaching qualification','positive research qualification','positive public service qualifications');
/*!40000 ALTER TABLE `fifth_year_appraisal_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fifth_year_review_data`
--

DROP TABLE IF EXISTS `fifth_year_review_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fifth_year_review_data` (
  `poll_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vote` int(11) NOT NULL,
  `qualificationsCmt` varchar(500) DEFAULT NULL,
  `voteCmt` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`poll_id`,`user_id`),
  CONSTRAINT `fifth_year_review_data_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`poll_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fifth_year_review_data`
--

LOCK TABLES `fifth_year_review_data` WRITE;
/*!40000 ALTER TABLE `fifth_year_review_data` DISABLE KEYS */;
INSERT INTO `fifth_year_review_data` VALUES (8,3,2,NULL,''),(8,4,2,NULL,'Comments for satisfactory with qualifications');
/*!40000 ALTER TABLE `fifth_year_review_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `merit_data`
--

DROP TABLE IF EXISTS `merit_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `merit_data` (
  `poll_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vote` int(11) NOT NULL,
  `voteCmt` varchar(500) DEFAULT NULL,
  `action_num` int(11) NOT NULL,
  PRIMARY KEY (`poll_id`,`user_id`,`action_num`),
  CONSTRAINT `merit_data_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`poll_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `merit_data`
--

LOCK TABLES `merit_data` WRITE;
/*!40000 ALTER TABLE `merit_data` DISABLE KEYS */;
INSERT INTO `merit_data` VALUES (10,3,1,'',1),(10,3,3,'',2),(10,4,1,'in favor action 1 in merit',1),(10,4,3,'abstain action 1 in merit',2);
/*!40000 ALTER TABLE `merit_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `other_poll_data`
--

DROP TABLE IF EXISTS `other_poll_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `other_poll_data` (
  `poll_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vote` int(11) NOT NULL,
  `voteCmt` varchar(500) DEFAULT NULL,
  `action_num` int(11) NOT NULL,
  PRIMARY KEY (`poll_id`,`user_id`,`action_num`),
  CONSTRAINT `other_poll_data_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`poll_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `other_poll_data`
--

LOCK TABLES `other_poll_data` WRITE;
/*!40000 ALTER TABLE `other_poll_data` DISABLE KEYS */;
INSERT INTO `other_poll_data` VALUES (13,4,2,'Other poll vote: satisfactory with qualifications vote',1),(13,4,1,'other poll data: satisfactory vote',2);
/*!40000 ALTER TABLE `other_poll_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `poll_actions`
--

DROP TABLE IF EXISTS `poll_actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `poll_actions` (
  `poll_id` int(11) NOT NULL,
  `action_num` int(11) NOT NULL,
  `fromTitle` varchar(100) NOT NULL,
  `fromStep` varchar(10) NOT NULL,
  `toTitle` varchar(100) NOT NULL,
  `toStep` varchar(10) NOT NULL,
  `accelerated` int(11) NOT NULL,
  PRIMARY KEY (`poll_id`,`action_num`),
  CONSTRAINT `poll_actions_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`poll_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `poll_actions`
--

LOCK TABLES `poll_actions` WRITE;
/*!40000 ALTER TABLE `poll_actions` DISABLE KEYS */;
INSERT INTO `poll_actions` VALUES (10,1,'fromTitle','1','toTitle','11',0),(10,2,'fromTitle2','2','toTitle22','22',1),(11,1,'fromTitle1','1','toTitle1','11',0),(11,2,'fromTitle2','2','toTitle2','22',1),(13,1,'fromTitle1','1','toTitle1','11',1),(13,2,'fromTitle2','2','toTitle2','22',0);
/*!40000 ALTER TABLE `poll_actions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `polls`
--

DROP TABLE IF EXISTS `polls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `polls` (
  `poll_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL,
  `description` varchar(300) DEFAULT NULL,
  `actDate` date NOT NULL,
  `deactDate` date NOT NULL,
  `effDate` date NOT NULL,
  `name` varchar(40) NOT NULL,
  `pollType` varchar(30) NOT NULL,
  `otherPollTypeInput` varchar(100) DEFAULT NULL,
  `dept` varchar(50) NOT NULL,
  `history` text,
  `dateModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `profTitle` varchar(30) NOT NULL,
  `votingOptions` int(11) DEFAULT '1',
  `notice` varchar(20) DEFAULT 'None',
  `assistantForm` int(11) DEFAULT '0',
  `associateForm` int(11) DEFAULT '0',
  `fullForm` int(11) DEFAULT '0',
  `assistantEvaluationNum` int(11) DEFAULT '0',
  PRIMARY KEY (`poll_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `polls`
--

LOCK TABLES `polls` WRITE;
/*!40000 ALTER TABLE `polls` DISABLE KEYS */;
INSERT INTO `polls` VALUES (8,'Fifth Year review test','Testing fifth year review','2017-06-02','2017-06-12','2017-06-22','Armando Gonzalez','Fifth Year Review','','Chemical and Enviromental Engineering','create:Armando Gonzalez:2017-06-02:testing fifth year review','2017-06-08 07:12:24','Associate Professor',1,'CEE',3,1,1,0),(9,'Fifth Year Appraisal ','Fifth year appraisal testing','2017-06-02','2017-06-12','2017-06-14','Armando Gonzalez','Fifth Year Appraisal','','Electrical Engineering','create:Armando Gonzalez:2017-06-02:Appraisal test','2017-06-08 07:12:24','Associate Professor',1,'ECE',1,2,1,0),(10,'Merit Testing','Testing merit ','2017-06-02','2017-06-12','2017-06-21','Armando Gonzalez','Merit','','Mechanical Engineering','create:Armando Gonzalez:2017-06-02:testing merit','2017-06-08 07:12:24','Assistant Professor',1,'ECE',1,1,2,0),(11,'Promotion test','Testing promotion','2017-06-02','2017-06-12','2017-06-21','Armando Gonzalez','Promotion','','Chemical and Enviromental Engineering','create:Armando Gonzalez:2017-06-02:Testing promotion','2017-06-08 07:12:24','Assistant Professor',1,'ECE',1,1,1,0),(12,'Reappointment Testing','Testing reappointment','2017-06-02','2017-06-12','2017-06-15','Armando Gonzalez','Reappointment','','Mechanical Engineering','create:Armando Gonzalez:2017-06-02:Reappointment Test','2017-06-08 07:12:24','Associate Professor',1,'CEE',3,1,1,0),(13,'Other Poll Type Testing','Testing other poll type','2017-06-02','2017-06-12','2017-06-16','Armando Gonzalez','Other','Other Poll Type Input','CE-CERT','create:Armando Gonzalez:2017-06-02:Other poll type testing','2017-06-08 07:12:24','Assistant Professor',3,'BOTH',3,2,1,0);
/*!40000 ALTER TABLE `polls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reappointment_data`
--

DROP TABLE IF EXISTS `reappointment_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reappointment_data` (
  `poll_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vote` int(11) NOT NULL,
  `voteCmt` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`poll_id`,`user_id`),
  CONSTRAINT `reappointment_data_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`poll_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reappointment_data`
--

LOCK TABLES `reappointment_data` WRITE;
/*!40000 ALTER TABLE `reappointment_data` DISABLE KEYS */;
INSERT INTO `reappointment_data` VALUES (12,4,1,'in favor of reappointment');
/*!40000 ALTER TABLE `reappointment_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `fName` varchar(30) NOT NULL,
  `lName` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `title` varchar(30) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'kzhen002@ucr.edu','Kevin','Zhen','$2y$10$Hw/sEjf7Lpankj/WxmdEEOhCqLHP/KCq7u6ZC/gmXYe9tcAxK8LQS','Administrator'),(2,'agonz060@ucr.edu','Armando','Gonzalez','$2y$10$OcDTpl32Bxc80cncN8xfZu4U9Os9Eru9.nc6HeM/A8O9YqcVCgNQe','Administrator'),(3,'assistant@gmail.com','Assistant','Professor','$2y$10$ng9UGovoM1Eoh.ENcZayLukebtc1Ch0aGR6xf33LVAN/Grfu5Rcpq','Assistant Professor'),(4,'associate@gmail.com','Associate','Professor','$2y$10$w7qrsc7JZiobmWfjVn9kgOU12/GbwOQy288tAptEVg/DVlXLGyDDW','Associate Professor');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `voters`
--

DROP TABLE IF EXISTS `voters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `voters` (
  `user_id` int(11) NOT NULL,
  `poll_id` int(11) NOT NULL,
  `pollEndDate` date NOT NULL,
  `comment` varchar(300) DEFAULT NULL,
  `voteFlag` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`,`poll_id`),
  KEY `poll_id` (`poll_id`),
  CONSTRAINT `voters_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `polls` (`poll_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `voters`
--

LOCK TABLES `voters` WRITE;
/*!40000 ALTER TABLE `voters` DISABLE KEYS */;
INSERT INTO `voters` VALUES (3,8,'2017-06-07',NULL,1),(3,9,'2017-06-07',NULL,1),(3,10,'2017-06-07',NULL,1),(3,11,'2017-06-07',NULL,1),(3,12,'2017-06-07',NULL,1),(3,13,'2017-06-07',NULL,1),(4,8,'2017-06-07',NULL,1),(4,9,'2017-06-07',NULL,1),(4,10,'2017-06-07',NULL,1),(4,11,'2017-06-07',NULL,1),(4,12,'2017-06-07',NULL,1),(4,13,'2017-06-07',NULL,1);
/*!40000 ALTER TABLE `voters` ENABLE KEYS */;
UNLOCK TABLES;
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-07-24 10:36:18
