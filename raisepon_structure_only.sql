-- MySQL dump 10.13  Distrib 5.6.42, for FreeBSD11.2 (amd64)
--
-- Host: localhost    Database: raisepon
-- ------------------------------------------------------
-- Server version	5.6.42

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
-- Table structure for table `ACCOUNTS`
--

DROP TABLE IF EXISTS `ACCOUNTS`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ACCOUNTS` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `USERNAME` varchar(20) CHARACTER SET latin1 NOT NULL,
  `PASSWORD` char(40) CHARACTER SET latin1 NOT NULL,
  `TYPE` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `USERNAME` (`USERNAME`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `CARDS`
--

DROP TABLE IF EXISTS `CARDS`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CARDS` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `OLT_ID` int(11) NOT NULL,
  `SLOT` smallint(6) NOT NULL,
  `CARDS_MODEL_ID` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `CARDS_MODEL`
--

DROP TABLE IF EXISTS `CARDS_MODEL`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CARDS_MODEL` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(50) NOT NULL,
  `PON_TYPE` enum('EPON','GPON') NOT NULL,
  `PORTS` smallint(6) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `CUSTOMERS`
--

DROP TABLE IF EXISTS `CUSTOMERS`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CUSTOMERS` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(255) NOT NULL,
  `ADDRESS` varchar(255) NOT NULL,
  `EGN` bigint(10) unsigned DEFAULT NULL,
  `OLT` int(11) DEFAULT NULL,
  `PON_PORT` int(11) DEFAULT NULL,
  `PON_ONU_ID` tinyint(4) DEFAULT NULL,
  `SERVICE` tinyint(4) DEFAULT NULL,
  `SN` varchar(255) NOT NULL,
  `AUTO` enum('YES','NO') NOT NULL DEFAULT 'NO',
  `STATE` enum('YES','NO') NOT NULL DEFAULT 'NO',
  `STATE_RF` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `OLT` (`OLT`),
  KEY `PON_PORT` (`PON_PORT`)
) ENGINE=InnoDB AUTO_INCREMENT=266 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `HISTORY`
--

DROP TABLE IF EXISTS `HISTORY`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `HISTORY` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CUSTOMERS_ID` int(11) NOT NULL,
  `DATE` datetime NOT NULL,
  `ACTION` varchar(255) CHARACTER SET utf8 NOT NULL,
  `SN` varchar(255) NOT NULL,
  `USER_ID` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `CUSTOMERS_ID` (`CUSTOMERS_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=598 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `LINE_PROFILE`
--

DROP TABLE IF EXISTS `LINE_PROFILE`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LINE_PROFILE` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(255) NOT NULL,
  `LINE_PROFILE_ID` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `TEMPLATE_ID` (`LINE_PROFILE_ID`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `OLT`
--

DROP TABLE IF EXISTS `OLT`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OLT` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(255) NOT NULL,
  `MODEL` tinyint(4) NOT NULL,
  `IP_ADDRESS` int(10) unsigned NOT NULL,
  `RO` varchar(50) NOT NULL,
  `RW` varchar(50) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `OLT_CARDS`
--

DROP TABLE IF EXISTS `OLT_CARDS`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OLT_CARDS` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `OLT_MODEL_ID` int(11) NOT NULL,
  `CARDS_MODEL_ID` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `OLT_MODEL`
--

DROP TABLE IF EXISTS `OLT_MODEL`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OLT_MODEL` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(50) NOT NULL,
  `SLOTS` smallint(6) NOT NULL,
  `TYPE` enum('EPON','GPON','XPON') NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ONU`
--

DROP TABLE IF EXISTS `ONU`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ONU` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(50) CHARACTER SET utf8 NOT NULL,
  `PORTS` tinyint(4) NOT NULL,
  `RF` tinyint(1) NOT NULL,
  `PSE` tinyint(1) NOT NULL,
  `HGU` tinyint(1) NOT NULL,
  `PON_TYPE` enum('EPON','GPON') NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ONU_RX_POWER`
--

DROP TABLE IF EXISTS `ONU_RX_POWER`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ONU_RX_POWER` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CUSTOMERS_ID` int(11) NOT NULL,
  `RX_POWER` float NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `PON`
--

DROP TABLE IF EXISTS `PON`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PON` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(255) CHARACTER SET utf8 NOT NULL,
  `OLT` int(11) NOT NULL,
  `SLOT_ID` tinyint(4) NOT NULL,
  `PORT_ID` tinyint(4) NOT NULL,
  `CARDS_MODEL_ID` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `OLT` (`OLT`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SERVICES`
--

DROP TABLE IF EXISTS `SERVICES`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SERVICES` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(255) NOT NULL,
  `LINE_PROFILE_ID` int(11) NOT NULL,
  `SERVICE_PROFILE_ID` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `SERVICE_PROFILE`
--

DROP TABLE IF EXISTS `SERVICE_PROFILE`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SERVICE_PROFILE` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(255) NOT NULL,
  `PORTS` tinyint(4) NOT NULL,
  `SERVICE_PROFILE_ID` tinyint(4) NOT NULL,
  `HGU` enum('Yes','No') NOT NULL DEFAULT 'No',
  `RF` enum('Yes','No') NOT NULL DEFAULT 'No',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `OLT` (`HGU`),
  KEY `TEMPLATE_ID` (`SERVICE_PROFILE_ID`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-02-07 15:42:17
