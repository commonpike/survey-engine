-- MySQL dump 10.13  Distrib 5.1.73, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: surveyengine
-- ------------------------------------------------------
-- Server version	5.1.73-1

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
-- Table structure for table `survey`
--

DROP TABLE IF EXISTS `survey`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `survey` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bogus` boolean,
  `ip` char(32) DEFAULT NULL,
  `cdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `field01` varchar(255) DEFAULT NULL,
  `field02` varchar(255) DEFAULT NULL,
  `field03` varchar(255) DEFAULT NULL,
  `field04` varchar(255) DEFAULT NULL,
  `field05` varchar(255) DEFAULT NULL,
  `field06` varchar(255) DEFAULT NULL,
  `field07` varchar(255) DEFAULT NULL,
  `field08` varchar(255) DEFAULT NULL,
  `cat01` tinyint(4) DEFAULT NULL,
  `cat02` tinyint(4) DEFAULT NULL,
  `cat03` tinyint(4) DEFAULT NULL,
  `cat04` tinyint(4) DEFAULT NULL,
  `cat05` tinyint(4) DEFAULT NULL,
  `cat06` tinyint(4) DEFAULT NULL,
  `cat07` tinyint(4) DEFAULT NULL,
  `cat08` tinyint(4) DEFAULT NULL,
  `cat09` tinyint(4) DEFAULT NULL,
  `cat10` tinyint(4) DEFAULT NULL,
  `cat11` tinyint(4) DEFAULT NULL,
  `cat12` tinyint(4) DEFAULT NULL,
  `cat13` tinyint(4) DEFAULT NULL,
  `cat14` tinyint(4) DEFAULT NULL,
  `cat15` tinyint(4) DEFAULT NULL,
  `cat16` tinyint(4) DEFAULT NULL,
  `pos01` tinyint(4) DEFAULT NULL,
  `pos02` tinyint(4) DEFAULT NULL,
  `pos03` tinyint(4) DEFAULT NULL,
  `pos04` tinyint(4) DEFAULT NULL,
  `pos05` tinyint(4) DEFAULT NULL,
  `pos06` tinyint(4) DEFAULT NULL,
  `pos07` tinyint(4) DEFAULT NULL,
  `pos08` tinyint(4) DEFAULT NULL,
  `pos09` tinyint(4) DEFAULT NULL,
  `pos10` tinyint(4) DEFAULT NULL,
  `pos11` tinyint(4) DEFAULT NULL,
  `pos12` tinyint(4) DEFAULT NULL,
  `pos13` tinyint(4) DEFAULT NULL,
  `pos14` tinyint(4) DEFAULT NULL,
  `pos15` tinyint(4) DEFAULT NULL,
  `pos16` tinyint(4) DEFAULT NULL,
  `pos17` tinyint(4) DEFAULT NULL,
  `pos18` tinyint(4) DEFAULT NULL,
  `pos19` tinyint(4) DEFAULT NULL,
  `pos20` tinyint(4) DEFAULT NULL,
  `pos21` tinyint(4) DEFAULT NULL,
  `pos22` tinyint(4) DEFAULT NULL,
  `pos23` tinyint(4) DEFAULT NULL,
  `pos24` tinyint(4) DEFAULT NULL,
  `pos25` tinyint(4) DEFAULT NULL,
  `pos26` tinyint(4) DEFAULT NULL,
  `pos27` tinyint(4) DEFAULT NULL,
  `pos28` tinyint(4) DEFAULT NULL,
  `pos29` tinyint(4) DEFAULT NULL,
  `pos30` tinyint(4) DEFAULT NULL,
  `pos31` tinyint(4) DEFAULT NULL,
  `pos32` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `survey`
--

LOCK TABLES `survey` WRITE;
/*!40000 ALTER TABLE `survey` DISABLE KEYS */;
/*!40000 ALTER TABLE `survey` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-07-19 16:23:05
