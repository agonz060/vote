-- MySQL dump 10.13  Distrib 5.6.33, for FreeBSD10.1 (amd64)
--
-- Host: localhost    Database: Voting
-- ------------------------------------------------------
-- Server version	5.6.33

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
-- Table structure for table `Assistant_Data`
--

DROP TABLE IF EXISTS `Assistant_Data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Assistant_Data` (
  `poll_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `voteCmt` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`poll_id`,`user_id`),
  CONSTRAINT `Assistant_Data_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `Polls` (`poll_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Assistant_Data`
--

LOCK TABLES `Assistant_Data` WRITE;
/*!40000 ALTER TABLE `Assistant_Data` DISABLE KEYS */;
/*!40000 ALTER TABLE `Assistant_Data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Associate_Promotion_Data`
--

DROP TABLE IF EXISTS `Associate_Promotion_Data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Associate_Promotion_Data` (
  `poll_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vote` int(11) NOT NULL,
  `voteCmt` varchar(500) DEFAULT NULL,
  `action_num` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`poll_id`,`action_num`),
  KEY `poll_id` (`poll_id`),
  CONSTRAINT `Associate_Promotion_Data_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `Polls` (`poll_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Associate_Promotion_Data`
--

LOCK TABLES `Associate_Promotion_Data` WRITE;
/*!40000 ALTER TABLE `Associate_Promotion_Data` DISABLE KEYS */;
/*!40000 ALTER TABLE `Associate_Promotion_Data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Fifth_Year_Appraisal_Data`
--

DROP TABLE IF EXISTS `Fifth_Year_Appraisal_Data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Fifth_Year_Appraisal_Data` (
  `poll_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vote` int(11) NOT NULL,
  `teachingCmts` varchar(500) DEFAULT NULL,
  `researchCmts` varchar(500) DEFAULT NULL,
  `pubServiceCmts` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`user_id`,`poll_id`),
  KEY `poll_id` (`poll_id`),
  CONSTRAINT `Fifth_Year_Appraisal_Data_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `Polls` (`poll_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Fifth_Year_Appraisal_Data`
--

LOCK TABLES `Fifth_Year_Appraisal_Data` WRITE;
/*!40000 ALTER TABLE `Fifth_Year_Appraisal_Data` DISABLE KEYS */;
/*!40000 ALTER TABLE `Fifth_Year_Appraisal_Data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Fifth_Year_Review_Data`
--

DROP TABLE IF EXISTS `Fifth_Year_Review_Data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Fifth_Year_Review_Data` (
  `poll_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vote` int(11) NOT NULL,
  `qualificationsCmt` varchar(500) DEFAULT NULL,
  `voteCmt` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`poll_id`,`user_id`),
  CONSTRAINT `Fifth_Year_Review_Data_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `Polls` (`poll_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Fifth_Year_Review_Data`
--

LOCK TABLES `Fifth_Year_Review_Data` WRITE;
/*!40000 ALTER TABLE `Fifth_Year_Review_Data` DISABLE KEYS */;
/*!40000 ALTER TABLE `Fifth_Year_Review_Data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Merit_Data`
--

DROP TABLE IF EXISTS `Merit_Data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Merit_Data` (
  `poll_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vote` int(11) NOT NULL,
  `voteCmt` varchar(500) DEFAULT NULL,
  `action_num` int(11) NOT NULL,
  PRIMARY KEY (`poll_id`,`user_id`,`action_num`),
  CONSTRAINT `Merit_Data_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `Polls` (`poll_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Merit_Data`
--

LOCK TABLES `Merit_Data` WRITE;
/*!40000 ALTER TABLE `Merit_Data` DISABLE KEYS */;
/*!40000 ALTER TABLE `Merit_Data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Poll_Actions`
--

DROP TABLE IF EXISTS `Poll_Actions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Poll_Actions` (
  `poll_id` int(11) NOT NULL,
  `action_num` int(11) NOT NULL,
  `fromLevel` int(11) NOT NULL,
  `toLevel` int(11) NOT NULL,
  `accelerated` int(11) NOT NULL,
  PRIMARY KEY (`poll_id`,`action_num`),
  CONSTRAINT `Poll_Actions_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `Polls` (`poll_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Poll_Actions`
--

LOCK TABLES `Poll_Actions` WRITE;
/*!40000 ALTER TABLE `Poll_Actions` DISABLE KEYS */;
/*!40000 ALTER TABLE `Poll_Actions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Polls`
--

DROP TABLE IF EXISTS `Polls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Polls` (
  `poll_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL,
  `description` varchar(300) DEFAULT NULL,
  `actDate` date NOT NULL,
  `deactDate` date NOT NULL,
  `effDate` date NOT NULL,
  `name` varchar(40) NOT NULL,
  `pollType` varchar(30) NOT NULL,
  `dept` varchar(30) NOT NULL,
  `history` text,
  `dateModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `profTitle` varchar(30) NOT NULL,
  PRIMARY KEY (`poll_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Polls`
--

LOCK TABLES `Polls` WRITE;
/*!40000 ALTER TABLE `Polls` DISABLE KEYS */;
/*!40000 ALTER TABLE `Polls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Reappointment_Data`
--

DROP TABLE IF EXISTS `Reappointment_Data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Reappointment_Data` (
  `poll_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vote` int(11) NOT NULL,
  `voteCmt` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`poll_id`,`user_id`),
  CONSTRAINT `Reappointment_Data_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `Polls` (`poll_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Reappointment_Data`
--

LOCK TABLES `Reappointment_Data` WRITE;
/*!40000 ALTER TABLE `Reappointment_Data` DISABLE KEYS */;
/*!40000 ALTER TABLE `Reappointment_Data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Users`
--

DROP TABLE IF EXISTS `Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `fName` varchar(30) NOT NULL,
  `lName` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `title` varchar(30) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Users`
--

LOCK TABLES `Users` WRITE;
/*!40000 ALTER TABLE `Users` DISABLE KEYS */;
INSERT INTO `Users` VALUES (1,'kzhen002@ucr.edu','Kevin','Zhen','$2y$10$aLHehmjfOZmnPTfkPSLflOCGC4x968XqBfEcySv2DgHvJpEYS.NP.','Administrator');
/*!40000 ALTER TABLE `Users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Voters`
--

DROP TABLE IF EXISTS `Voters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Voters` (
  `user_id` int(11) NOT NULL,
  `poll_id` int(11) NOT NULL,
  `pollEndDate` date NOT NULL,
  `comment` varchar(300) DEFAULT NULL,
  `voteFlag` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`,`poll_id`),
  KEY `poll_id` (`poll_id`),
  CONSTRAINT `Voters_ibfk_1` FOREIGN KEY (`poll_id`) REFERENCES `Polls` (`poll_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Voters`
--

LOCK TABLES `Voters` WRITE;
/*!40000 ALTER TABLE `Voters` DISABLE KEYS */;
/*!40000 ALTER TABLE `Voters` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-03-15 16:48:48
