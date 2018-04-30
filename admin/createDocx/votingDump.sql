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
use Voting;
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

/*SET @@GLOBAL.GTID_PURGED='5c255620-d458-11e6-b7b8-1866da87d872:1-71740';*/

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
INSERT INTO `assistant_data` VALUES (16,3,'Assistant Comment for Steve Harvey\'s Fifth Year Review.'),(17,3,'Confidential comment for Steve Jobs fifth year appraisal by assistant professor'),(18,3,'Confidential for Tony Stark\'s Merit from an Assistant Professor'),(19,3,'Confidential comments for silver surfer\'s promotion from an assistant professor'),(19,4,'Action 2: Associate Professor casting comments regarding\nSilver Surfer\'s Promotion'),(20,3,'Confidential comment regarding Bruce Willis\'s reappointment\nComment made by assistant professor'),(21,3,'Confidential comment for Jared Leto\'s custom poll type\nComment by assistant professor'),(21,4,'\"Associate Professor\" - action 2: commenting on Custom Multi-action Poll Type for Jared leto');
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
INSERT INTO `associate_promotion_data` VALUES (19,7,1,'Full professor - action 1: in favor\n`~!@#$%^&*()-_=+{}[]\\|;:\'\",<.>?/',1),(19,7,2,'Full Professor - action 2: opposed',2),(19,7,3,'Full Professor - action 3: abstain',3);
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
INSERT INTO `fifth_year_appraisal_data` VALUES (17,4,4,'teaching comments for steve job\'s 5th year appraisal\n\nPositive with qualifications vote\n\n\"Associate Professor\"','research comments for steve job\'s 5th year appraisal\n\nPositive w/ qualifications vote\n\n\"Associate Professor\"','public service comments for steve job\'s 5th year appraisal\n\nPositive\n\n\"Associate Professor\"\n\n');
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
INSERT INTO `fifth_year_review_data` VALUES (16,4,4,NULL,'Satisfactory with qualifcations vote for steve harvey\'s 5th year review\nVote by Associate Professor');
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
INSERT INTO `merit_data` VALUES (18,4,2,'Associate Professor\n\nAction1: Opposed to Tony Stark\'s Accelerated Merit advancement\n\n',1),(18,4,3,'Associate Professor\n\nAction 2: Abstain from Tony Starks Merit\n',2),(18,4,2,'Associate Professor\n\nAction 3: Opposed from Tony Stark\'s accelerated merit advancement',3);
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
INSERT INTO `other_poll_data` VALUES (21,7,4,'Full professor - other poll type - action 1: satisfactory with qualifications vote',1),(21,7,2,'Full professor - other poll type - action 2: opposed vote',2),(21,7,3,'Full professor - other poll type - action 3: abstain',3);
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
INSERT INTO `poll_actions` VALUES (18,1,'FromTitle','1','ToTitle','1',1),(18,2,'FromTitle','22','ToTitle','22',0),(18,3,'FromTitle','333','ToTitle','333',1),(19,1,'FromTitle','1','ToTitle','1',1),(19,2,'FromTitle','22','ToTitle','22',0),(19,3,'FromTitle','333','ToTitle','333',0),(21,1,'FromTitle','1','ToTitle','1',1),(21,2,'FromTitle','22','ToTitle','22',0),(21,3,'FromTitle','333','ToTitle','333',1);
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
  `assistantEvaluationNum` int(10) DEFAULT '0',
  `associateEvaluationNum` int(10) DEFAULT '0',
  `fullEvaluationNum` int(10) DEFAULT '0',
  PRIMARY KEY (`poll_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `polls`
--

LOCK TABLES `polls` WRITE;
/*!40000 ALTER TABLE `polls` DISABLE KEYS */;
INSERT INTO `polls` VALUES (16,'Fifth Year Review Test','Testing fix year review on assistant and associate users\nassistant will only make comments\nassociate has full vote','2017-09-11','2017-09-15','2017-09-13','Steve Harvey','Fifth Year Review','','Chemical and Enviromental Engineering','create::2017-09-11:testing','2017-09-14 16:35:36','Assistant Professor',1,'CEE',3,1,1,0,0,0),(17,'Testing Fifth Year Appraisal','Testing fifth year appraisal on associate and assistant professors\nassistants will make comments\nassociate cast full votes','2017-09-11','2017-09-15','2017-09-13','Steve Jobs','Fifth Year Appraisal','','Computer Engineering','create::2017-09-11:testing fifth year appraisals','2017-09-14 16:35:36','Associate Professor',1,'BOTH',3,1,1,0,0,0),(18,'Testing Multi-action Merit Pol','Testing 3 action merit poll on assistants and associates\nAssistant will be able to make comments on 2nd action\nassociates will be cast votes on all actions\n','2017-09-11','2017-09-15','2017-09-14','Tony Starks','Merit','','Electrical Engineering','create::2017-09-11:testing merit poll','2017-09-14 16:35:36','Assistant Professor',1,'CEE',3,1,1,1,1,1),(19,'Testing Promotion w/ Multi act','Testing Promotion with 3 actions\nassistants can make comments on 1st action\nassociates make comments on 2nd action\nfull professors can vote on all actions','2017-09-11','2017-09-15','2017-09-13','Silver Surfer','Promotion','','Electrical Engineering','create:Armando Gonzalez:2017-09-11:testing multiaction merit poll','2017-09-14 16:35:36','Assistant Professor',1,'CEE',3,3,1,1,2,1),(20,'Testing Reappointment Poll','Testing Reappointment Poll\nassistant will make comments\nassociates and full professors will make normal votes','2017-09-11','2017-09-15','2017-09-13','Bruce Willis','Reappointment','','Mechanical Engineering','create:Armando Gonzalez:2017-09-11:testing reappointment poll','2017-09-14 16:35:36','Associate Professor',1,'BOTH',3,1,1,0,0,0),(21,'Testing other poll type','Testing multi-action other poll type\nassistants will make comments\nassociates will make comments on 2nd action \nfull professors will vote on all actions - 4 poll voting options: satisfactory, satisfactory w/ qualifications, unsatisfactory, abstain','2017-09-11','2017-09-15','2017-09-13','Jared Leto','Other','Custom Multi-action Poll Type','CE-CERT','create::2017-09-11:testing other poll type','2017-09-14 16:35:36','Assistant Professor',3,'BOTH',3,3,1,1,2,1);
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
INSERT INTO `reappointment_data` VALUES (20,4,1,'\"Associate Professor\": in favor of Bruce Willis\'s Reappointment'),(20,7,2,'Full Professor opposes Bruce Willis\'s Reappointment ');
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'kzhen002@ucr.edu','Kevin','Zhen','$2y$10$Hw/sEjf7Lpankj/WxmdEEOhCqLHP/KCq7u6ZC/gmXYe9tcAxK8LQS','Administrator'),(2,'agonz060@ucr.edu','Armando','Gonzalez','$2y$10$OcDTpl32Bxc80cncN8xfZu4U9Os9Eru9.nc6HeM/A8O9YqcVCgNQe','Administrator'),(3,'assistant@gmail.com','Bevis','Buffet','$2y$10$n5zvulUyA8bnl9OnEU3UJeU7jGv8dfdIJg97h.tm/Dk8Mlz/sxq26','Assistant Professor'),(4,'associate@gmail.com','Lewis','Clark','$2y$10$ZgvD.12bNfPKK2aTOX0UcO9/HM83TqB.s9TZybLQPdwBQeL77Tqra','Associate Professor'),(7,'full@gmail.com','Luke','Cage','$2y$10$rMA6.ogkDM9L3xFtxnp2a.kOIqLDBK1QKev7gy8NmGsHaPgMdc/26','Full Professor'),(8,'admin@gmail.com','Administrator','Account','$2y$10$LDpSxzDkK9yGE.rESsDSWeuXXZNyWTsnL9hQGrrEYVEy3cypL59Je','Administrator');
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
INSERT INTO `voters` VALUES (3,16,'2017-09-12',NULL,1),(3,17,'2017-09-12',NULL,1),(3,18,'2017-09-12',NULL,1),(3,19,'2017-09-12',NULL,1),(3,20,'2017-09-12',NULL,1),(3,21,'2017-09-12',NULL,1),(4,16,'2017-09-12',NULL,1),(4,17,'2017-09-12',NULL,1),(4,18,'2017-09-12',NULL,1),(4,19,'2017-09-12',NULL,1),(4,20,'2017-09-12',NULL,1),(4,21,'2017-09-12',NULL,1),(7,19,'2017-09-12',NULL,1),(7,20,'2017-09-12',NULL,1),(7,21,'2017-09-12',NULL,1);
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

-- Dump completed on 2017-09-18 17:48:26
