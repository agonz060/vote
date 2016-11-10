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
-- Table structure for table `Polls`
--

DROP TABLE IF EXISTS `Polls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Polls` (
  `poll_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `actDate` date NOT NULL,
  `deactDate` date NOT NULL,
  `dateModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `history` text,
  PRIMARY KEY (`poll_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Polls`
--

LOCK TABLES `Polls` WRITE;
/*!40000 ALTER TABLE `Polls` DISABLE KEYS */;
INSERT INTO `Polls` VALUES (1,'New Promotion','pick who gets a bonus','1996-10-10','2017-10-20','2016-11-08 05:43:04','create:kzhen002:2016-11-7:created new poll'),(2,'New Promotion1','pick who gets a bonus','1999-01-01','2012-01-20','2016-11-08 05:43:40','create:bsmith001:2016-12-7:created new poll for promotion'),(3,'New Promotion2','pick who gets a big bonus','2016-12-11','2017-12-20','2016-11-08 05:44:13','create:jsmith023:2013-10-12:created new poll for fun'),(4,'Can Edit','Test1','2016-11-07','2030-11-07','2016-11-08 08:00:59','create:kzhen002:2016-11-7:just because');
/*!40000 ALTER TABLE `Polls` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Professors`
--

DROP TABLE IF EXISTS `Professors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Professors` (
  `prof_id` int(11) NOT NULL AUTO_INCREMENT,
  `lName` varchar(20) NOT NULL,
  `fName` varchar(20) NOT NULL,
  `title` varchar(30) NOT NULL,
  PRIMARY KEY (`prof_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Professors`
--

LOCK TABLES `Professors` WRITE;
/*!40000 ALTER TABLE `Professors` DISABLE KEYS */;
INSERT INTO `Professors` VALUES (1,'Zhen','Kevin','Professor'),(2,'Smith','Bob','Professor'),(3,'Cumberbatch','Benedict','Assistant Professor');
/*!40000 ALTER TABLE `Professors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Saved`
--

DROP TABLE IF EXISTS `Saved`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Saved` (
  `Title` varchar(50) DEFAULT NULL,
  `Description` varchar(300) DEFAULT NULL,
  `ActDate` date DEFAULT NULL,
  `DeactDate` date DEFAULT NULL,
  `ParticipatingProfs` varchar(300) DEFAULT NULL,
  `ProfComments` varchar(300) DEFAULT NULL,
  `DateModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Saved`
--

LOCK TABLES `Saved` WRITE;
/*!40000 ALTER TABLE `Saved` DISABLE KEYS */;
INSERT INTO `Saved` VALUES ('voting Session 1','This is a test 1','2016-09-20','2016-10-26','Jill Smith, Minnie Mouse, Bill','They\'re smart','2016-11-03 05:06:52'),('voting Session 1','This is a test 1','2016-09-20','2016-10-26','Jill Smith, Minnie Mouse, Bill','They\'re smart','2016-11-03 05:06:53'),('voting Session 1','This is a test 1','2016-09-20','2016-10-26','Jill Smith, Minnie Mouse, Bill','They\'re smart','2016-11-03 05:06:53'),('voting Session 1','This is a test 1','2016-09-20','2016-10-26','Jill Smith, Minnie Mouse, Bill','They\'re smart','2016-11-03 05:51:53'),('voting Session 1','This is a test 1','2016-09-20','2016-10-26','Jill Smith, Minnie Mouse, Bill','They\'re smart','2016-11-03 05:51:53'),('voting Session 1','This is a test 1','2016-09-20','2016-10-26','Jill Smith, Minnie Mouse, Bill','They\'re smart','2016-11-03 05:51:54'),('voting Session 1','This is a test 1','2016-09-20','2016-10-26','Jill Smith, Minnie Mouse, Bill','They\'re smart','2016-11-03 05:51:54'),('voting Session 1','This is a test 1','2016-09-20','2016-10-26','Jill Smith, Minnie Mouse, Bill','They\'re smart','2016-11-03 05:51:55');
/*!40000 ALTER TABLE `Saved` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Votes`
--

DROP TABLE IF EXISTS `Votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Votes` (
  `prof_id` int(11) NOT NULL,
  `poll_id` int(11) NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`prof_id`,`poll_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Votes`
--

LOCK TABLES `Votes` WRITE;
/*!40000 ALTER TABLE `Votes` DISABLE KEYS */;
INSERT INTO `Votes` VALUES (1,1,'prof_id1 poll_id1'),(2,1,'prof_id2 poll_id1'),(3,1,'prof_id3 poll_id1');
/*!40000 ALTER TABLE `Votes` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-11-10  0:29:29
