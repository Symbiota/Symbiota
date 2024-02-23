-- MySQL dump 10.13  Distrib 5.7.41, for Linux (x86_64)
--
-- Host: localhost    Database: symbiota
-- ------------------------------------------------------
-- Server version	5.7.41-0ubuntu0.18.04.1

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
-- Table structure for table `actionrequest`
--

DROP TABLE IF EXISTS `actionrequest`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `actionrequest` (
  `actionrequestid` bigint(20) NOT NULL AUTO_INCREMENT,
  `fk` int(11) NOT NULL,
  `tablename` varchar(255) DEFAULT NULL,
  `requesttype` varchar(30) NOT NULL,
  `uid_requestor` int(10) unsigned NOT NULL,
  `requestdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `requestremarks` varchar(900) DEFAULT NULL,
  `priority` int(11) DEFAULT NULL,
  `uid_fullfillor` int(10) unsigned NOT NULL,
  `state` varchar(12) DEFAULT NULL,
  `resolution` varchar(12) DEFAULT NULL,
  `statesetdate` datetime DEFAULT NULL,
  `resolutionremarks` varchar(900) DEFAULT NULL,
  PRIMARY KEY (`actionrequestid`),
  KEY `FK_actionreq_uid1_idx` (`uid_requestor`),
  KEY `FK_actionreq_uid2_idx` (`uid_fullfillor`),
  KEY `FK_actionreq_type_idx` (`requesttype`),
  CONSTRAINT `FK_actionreq_type` FOREIGN KEY (`requesttype`) REFERENCES `actionrequesttype` (`requesttype`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_actionreq_uid1` FOREIGN KEY (`uid_requestor`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_actionreq_uid2` FOREIGN KEY (`uid_fullfillor`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `actionrequest`
--

LOCK TABLES `actionrequest` WRITE;
/*!40000 ALTER TABLE `actionrequest` DISABLE KEYS */;
/*!40000 ALTER TABLE `actionrequest` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `actionrequesttype`
--

DROP TABLE IF EXISTS `actionrequesttype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `actionrequesttype` (
  `requesttype` varchar(30) NOT NULL,
  `context` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`requesttype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `actionrequesttype`
--

LOCK TABLES `actionrequesttype` WRITE;
/*!40000 ALTER TABLE `actionrequesttype` DISABLE KEYS */;
/*!40000 ALTER TABLE `actionrequesttype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `adminlanguages`
--

DROP TABLE IF EXISTS `adminlanguages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adminlanguages` (
  `langid` int(11) NOT NULL AUTO_INCREMENT,
  `langname` varchar(45) NOT NULL,
  `iso639_1` varchar(10) DEFAULT NULL,
  `iso639_2` varchar(10) DEFAULT NULL,
  `ISO 639-3` varchar(3) DEFAULT NULL,
  `notes` varchar(45) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`langid`),
  UNIQUE KEY `index_langname_unique` (`langname`)
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `adminlanguages`
--

LOCK TABLES `adminlanguages` WRITE;
/*!40000 ALTER TABLE `adminlanguages` DISABLE KEYS */;
INSERT INTO `adminlanguages` VALUES (1,'English','en',NULL,NULL,NULL,'2022-10-18 23:21:42'),(2,'German','de',NULL,NULL,NULL,'2022-10-18 23:21:42'),(3,'French','fr',NULL,NULL,NULL,'2022-10-18 23:21:42'),(4,'Dutch','nl',NULL,NULL,NULL,'2022-10-18 23:21:42'),(5,'Italian','it',NULL,NULL,NULL,'2022-10-18 23:21:42'),(6,'Spanish','es',NULL,NULL,NULL,'2022-10-18 23:21:42'),(7,'Polish','pl',NULL,NULL,NULL,'2022-10-18 23:21:42'),(8,'Russian','ru',NULL,NULL,NULL,'2022-10-18 23:21:42'),(9,'Japanese','ja',NULL,NULL,NULL,'2022-10-18 23:21:42'),(10,'Portuguese','pt',NULL,NULL,NULL,'2022-10-18 23:21:42'),(11,'Swedish','sv',NULL,NULL,NULL,'2022-10-18 23:21:42'),(12,'Chinese','zh',NULL,NULL,NULL,'2022-10-18 23:21:42'),(13,'Catalan','ca',NULL,NULL,NULL,'2022-10-18 23:21:42'),(14,'Ukrainian','uk',NULL,NULL,NULL,'2022-10-18 23:21:42'),(15,'Norwegian (Bokm�l)','no',NULL,NULL,NULL,'2022-10-18 23:21:42'),(16,'Finnish','fi',NULL,NULL,NULL,'2022-10-18 23:21:42'),(17,'Vietnamese','vi',NULL,NULL,NULL,'2022-10-18 23:21:42'),(18,'Czech','cs',NULL,NULL,NULL,'2022-10-18 23:21:42'),(19,'Hungarian','hu',NULL,NULL,NULL,'2022-10-18 23:21:42'),(20,'Korean','ko',NULL,NULL,NULL,'2022-10-18 23:21:42'),(21,'Indonesian','id',NULL,NULL,NULL,'2022-10-18 23:21:42'),(22,'Turkish','tr',NULL,NULL,NULL,'2022-10-18 23:21:42'),(23,'Romanian','ro',NULL,NULL,NULL,'2022-10-18 23:21:42'),(24,'Persian','fa',NULL,NULL,NULL,'2022-10-18 23:21:42'),(25,'Arabic','ar',NULL,NULL,NULL,'2022-10-18 23:21:42'),(26,'Danish','da',NULL,NULL,NULL,'2022-10-18 23:21:42'),(27,'Esperanto','eo',NULL,NULL,NULL,'2022-10-18 23:21:42'),(28,'Serbian','sr',NULL,NULL,NULL,'2022-10-18 23:21:42'),(29,'Lithuanian','lt',NULL,NULL,NULL,'2022-10-18 23:21:42'),(30,'Slovak','sk',NULL,NULL,NULL,'2022-10-18 23:21:42'),(31,'Malay','ms',NULL,NULL,NULL,'2022-10-18 23:21:42'),(32,'Hebrew','he',NULL,NULL,NULL,'2022-10-18 23:21:42'),(33,'Bulgarian','bg',NULL,NULL,NULL,'2022-10-18 23:21:42'),(34,'Slovenian','sl',NULL,NULL,NULL,'2022-10-18 23:21:42'),(35,'Volap�k','vo',NULL,NULL,NULL,'2022-10-18 23:21:42'),(36,'Kazakh','kk',NULL,NULL,NULL,'2022-10-18 23:21:42'),(37,'Waray-Waray','war',NULL,NULL,NULL,'2022-10-18 23:21:42'),(38,'Basque','eu',NULL,NULL,NULL,'2022-10-18 23:21:42'),(39,'Croatian','hr',NULL,NULL,NULL,'2022-10-18 23:21:42'),(40,'Hindi','hi',NULL,NULL,NULL,'2022-10-18 23:21:42'),(41,'Estonian','et',NULL,NULL,NULL,'2022-10-18 23:21:42'),(42,'Azerbaijani','az',NULL,NULL,NULL,'2022-10-18 23:21:42'),(43,'Galician','gl',NULL,NULL,NULL,'2022-10-18 23:21:42'),(44,'Simple English','simple',NULL,NULL,NULL,'2022-10-18 23:21:42'),(45,'Norwegian (Nynorsk)','nn',NULL,NULL,NULL,'2022-10-18 23:21:42'),(46,'Thai','th',NULL,NULL,NULL,'2022-10-18 23:21:42'),(47,'Newar / Nepal Bhasa','new',NULL,NULL,NULL,'2022-10-18 23:21:42'),(48,'Greek','el',NULL,NULL,NULL,'2022-10-18 23:21:42'),(49,'Aromanian','roa-rup',NULL,NULL,NULL,'2022-10-18 23:21:42'),(50,'Latin','la',NULL,NULL,NULL,'2022-10-18 23:21:42'),(51,'Occitan','oc',NULL,NULL,NULL,'2022-10-18 23:21:42'),(52,'Tagalog','tl',NULL,NULL,NULL,'2022-10-18 23:21:42'),(53,'Haitian','ht',NULL,NULL,NULL,'2022-10-18 23:21:42'),(54,'Macedonian','mk',NULL,NULL,NULL,'2022-10-18 23:21:42'),(55,'Georgian','ka',NULL,NULL,NULL,'2022-10-18 23:21:42'),(56,'Serbo-Croatian','sh',NULL,NULL,NULL,'2022-10-18 23:21:42'),(57,'Telugu','te',NULL,NULL,NULL,'2022-10-18 23:21:42'),(58,'Piedmontese','pms',NULL,NULL,NULL,'2022-10-18 23:21:42'),(59,'Cebuano','ceb',NULL,NULL,NULL,'2022-10-18 23:21:42'),(60,'Tamil','ta',NULL,NULL,NULL,'2022-10-18 23:21:42'),(61,'Belarusian (Tara�kievica)','be-x-old',NULL,NULL,NULL,'2022-10-18 23:21:42'),(62,'Breton','br',NULL,NULL,NULL,'2022-10-18 23:21:42'),(63,'Latvian','lv',NULL,NULL,NULL,'2022-10-18 23:21:42'),(64,'Javanese','jv',NULL,NULL,NULL,'2022-10-18 23:21:42'),(65,'Albanian','sq',NULL,NULL,NULL,'2022-10-18 23:21:42'),(66,'Belarusian','be',NULL,NULL,NULL,'2022-10-18 23:21:42'),(67,'Marathi','mr',NULL,NULL,NULL,'2022-10-18 23:21:42'),(68,'Welsh','cy',NULL,NULL,NULL,'2022-10-18 23:21:42'),(69,'Luxembourgish','lb',NULL,NULL,NULL,'2022-10-18 23:21:42'),(70,'Icelandic','is',NULL,NULL,NULL,'2022-10-18 23:21:42'),(71,'Bosnian','bs',NULL,NULL,NULL,'2022-10-18 23:21:42'),(72,'Yoruba','yo',NULL,NULL,NULL,'2022-10-18 23:21:42'),(73,'Malagasy','mg',NULL,NULL,NULL,'2022-10-18 23:21:42'),(74,'Aragonese','an',NULL,NULL,NULL,'2022-10-18 23:21:42'),(75,'Bishnupriya Manipuri','bpy',NULL,NULL,NULL,'2022-10-18 23:21:42'),(76,'Lombard','lmo',NULL,NULL,NULL,'2022-10-18 23:21:42'),(77,'West Frisian','fy',NULL,NULL,NULL,'2022-10-18 23:21:42'),(78,'Bengali','bn',NULL,NULL,NULL,'2022-10-18 23:21:42'),(79,'Ido','io',NULL,NULL,NULL,'2022-10-18 23:21:42'),(80,'Swahili','sw',NULL,NULL,NULL,'2022-10-18 23:21:42'),(81,'Gujarati','gu',NULL,NULL,NULL,'2022-10-18 23:21:42'),(82,'Malayalam','ml',NULL,NULL,NULL,'2022-10-18 23:21:42'),(83,'Western Panjabi','pnb',NULL,NULL,NULL,'2022-10-18 23:21:42'),(84,'Afrikaans','af',NULL,NULL,NULL,'2022-10-18 23:21:42'),(85,'Low Saxon','nds',NULL,NULL,NULL,'2022-10-18 23:21:42'),(86,'Sicilian','scn',NULL,NULL,NULL,'2022-10-18 23:21:42'),(87,'Urdu','ur',NULL,NULL,NULL,'2022-10-18 23:21:42'),(88,'Kurdish','ku',NULL,NULL,NULL,'2022-10-18 23:21:42'),(89,'Cantonese','zh-yue',NULL,NULL,NULL,'2022-10-18 23:21:42'),(90,'Armenian','hy',NULL,NULL,NULL,'2022-10-18 23:21:42'),(91,'Quechua','qu',NULL,NULL,NULL,'2022-10-18 23:21:42'),(92,'Sundanese','su',NULL,NULL,NULL,'2022-10-18 23:21:42'),(93,'Nepali','ne',NULL,NULL,NULL,'2022-10-18 23:21:42'),(94,'Zazaki','diq',NULL,NULL,NULL,'2022-10-18 23:21:42'),(95,'Asturian','ast',NULL,NULL,NULL,'2022-10-18 23:21:42'),(96,'Tatar','tt',NULL,NULL,NULL,'2022-10-18 23:21:42'),(97,'Neapolitan','nap',NULL,NULL,NULL,'2022-10-18 23:21:42'),(98,'Irish','ga',NULL,NULL,NULL,'2022-10-18 23:21:42'),(99,'Chuvash','cv',NULL,NULL,NULL,'2022-10-18 23:21:42'),(100,'Samogitian','bat-smg',NULL,NULL,NULL,'2022-10-18 23:21:42'),(101,'Walloon','wa',NULL,NULL,NULL,'2022-10-18 23:21:42'),(102,'Amharic','am',NULL,NULL,NULL,'2022-10-18 23:21:42'),(103,'Kannada','kn',NULL,NULL,NULL,'2022-10-18 23:21:42'),(104,'Alemannic','als',NULL,NULL,NULL,'2022-10-18 23:21:42'),(105,'Buginese','bug',NULL,NULL,NULL,'2022-10-18 23:21:42'),(106,'Burmese','my',NULL,NULL,NULL,'2022-10-18 23:21:42'),(107,'Interlingua','ia',NULL,NULL,NULL,'2022-10-18 23:21:42');
/*!40000 ALTER TABLE `adminlanguages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `adminstats`
--

DROP TABLE IF EXISTS `adminstats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adminstats` (
  `idadminstats` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(45) NOT NULL,
  `statname` varchar(45) NOT NULL,
  `statvalue` int(11) DEFAULT NULL,
  `statpercentage` int(11) DEFAULT NULL,
  `dynamicProperties` text,
  `groupid` int(11) NOT NULL,
  `collid` int(10) unsigned DEFAULT NULL,
  `uid` int(10) unsigned DEFAULT NULL,
  `note` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idadminstats`),
  KEY `FK_adminstats_collid_idx` (`collid`),
  KEY `FK_adminstats_uid_idx` (`uid`),
  KEY `Index_adminstats_ts` (`initialtimestamp`),
  KEY `Index_category` (`category`),
  CONSTRAINT `FK_adminstats_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_adminstats_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `adminstats`
--

LOCK TABLES `adminstats` WRITE;
/*!40000 ALTER TABLE `adminstats` DISABLE KEYS */;
/*!40000 ALTER TABLE `adminstats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `agentlinks`
--

DROP TABLE IF EXISTS `agentlinks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agentlinks` (
  `agentLinksID` bigint(20) NOT NULL AUTO_INCREMENT,
  `agentID` bigint(20) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `link` varchar(900) DEFAULT NULL,
  `isprimarytopicof` tinyint(1) NOT NULL DEFAULT '1',
  `text` varchar(50) DEFAULT NULL,
  `createdUid` int(11) unsigned DEFAULT NULL,
  `modifiedUid` int(11) unsigned DEFAULT NULL,
  `dateLastModified` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`agentLinksID`),
  KEY `FK_agentlinks_agentID_idx` (`agentID`),
  KEY `FK_agentlinks_modUid_idx` (`modifiedUid`),
  KEY `FK_agentlinks_createdUid_idx` (`createdUid`),
  CONSTRAINT `FK_agentlinks_agentID` FOREIGN KEY (`agentID`) REFERENCES `agents` (`agentID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_agentlinks_createdUid` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_agentlinks_modUid` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agentlinks`
--

LOCK TABLES `agentlinks` WRITE;
/*!40000 ALTER TABLE `agentlinks` DISABLE KEYS */;
/*!40000 ALTER TABLE `agentlinks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `agentnames`
--

DROP TABLE IF EXISTS `agentnames`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agentnames` (
  `agentNamesID` bigint(20) NOT NULL AUTO_INCREMENT,
  `agentID` int(11) NOT NULL,
  `type` varchar(32) NOT NULL DEFAULT 'Full Name',
  `name` varchar(255) DEFAULT NULL,
  `language` varchar(6) DEFAULT 'en_us',
  `createdUid` int(11) DEFAULT NULL,
  `modifiedUid` int(11) DEFAULT NULL,
  `dateLastModified` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`agentNamesID`),
  UNIQUE KEY `agentid` (`agentID`,`type`,`name`),
  KEY `type` (`type`),
  FULLTEXT KEY `ft_collectorname` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agentnames`
--

LOCK TABLES `agentnames` WRITE;
/*!40000 ALTER TABLE `agentnames` DISABLE KEYS */;
/*!40000 ALTER TABLE `agentnames` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `agentnumberpattern`
--

DROP TABLE IF EXISTS `agentnumberpattern`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agentnumberpattern` (
  `agentNumberPatternID` bigint(20) NOT NULL,
  `agentID` bigint(20) NOT NULL,
  `numberType` varchar(50) DEFAULT 'Collector number',
  `numberPattern` varchar(255) DEFAULT NULL,
  `numberPatternDescription` varchar(900) DEFAULT NULL,
  `startYear` int(11) DEFAULT NULL,
  `endYear` int(11) DEFAULT NULL,
  `integerIncrement` int(11) DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`agentNumberPatternID`),
  KEY `agentid` (`agentID`),
  CONSTRAINT `agentnumberpattern_ibfk_1` FOREIGN KEY (`agentID`) REFERENCES `agents` (`agentID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agentnumberpattern`
--

LOCK TABLES `agentnumberpattern` WRITE;
/*!40000 ALTER TABLE `agentnumberpattern` DISABLE KEYS */;
/*!40000 ALTER TABLE `agentnumberpattern` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `agentrelations`
--

DROP TABLE IF EXISTS `agentrelations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agentrelations` (
  `agentRelationsID` bigint(20) NOT NULL AUTO_INCREMENT,
  `fromAgentID` bigint(20) NOT NULL,
  `toAgentID` bigint(20) NOT NULL,
  `relationship` varchar(50) NOT NULL,
  `notes` varchar(900) DEFAULT NULL,
  `createdUid` int(11) unsigned DEFAULT NULL,
  `dateLastModified` datetime DEFAULT NULL,
  `modifiedUid` int(11) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`agentRelationsID`),
  KEY `fromagentid` (`fromAgentID`),
  KEY `toagentid` (`toAgentID`),
  KEY `relationship` (`relationship`),
  KEY `FK_agentrelations_modUid_idx` (`modifiedUid`),
  KEY `FK_agentrelations_createUid_idx` (`createdUid`),
  CONSTRAINT `FK_agentrelations_createUid` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_agentrelations_ibfk_1` FOREIGN KEY (`fromAgentID`) REFERENCES `agents` (`agentID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_agentrelations_ibfk_2` FOREIGN KEY (`toAgentID`) REFERENCES `agents` (`agentID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_agentrelations_ibfk_3` FOREIGN KEY (`relationship`) REFERENCES `ctrelationshiptypes` (`relationship`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `FK_agentrelations_modUid` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agentrelations`
--

LOCK TABLES `agentrelations` WRITE;
/*!40000 ALTER TABLE `agentrelations` DISABLE KEYS */;
/*!40000 ALTER TABLE `agentrelations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `agents`
--

DROP TABLE IF EXISTS `agents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agents` (
  `agentID` bigint(20) NOT NULL AUTO_INCREMENT,
  `familyName` varchar(45) NOT NULL,
  `firstName` varchar(45) DEFAULT NULL,
  `middleName` varchar(45) DEFAULT NULL,
  `startYearActive` int(11) DEFAULT NULL,
  `endYearActive` int(11) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `rating` int(11) DEFAULT '10',
  `guid` varchar(900) DEFAULT NULL,
  `preferredRecByID` bigint(20) DEFAULT NULL,
  `biography` text,
  `taxonomicgroups` varchar(900) DEFAULT NULL,
  `collectionsat` varchar(900) DEFAULT NULL,
  `curated` tinyint(1) DEFAULT '0',
  `nototherwisespecified` tinyint(1) DEFAULT '0',
  `type` enum('Individual','Team','Organization') DEFAULT NULL,
  `prefix` varchar(32) DEFAULT NULL,
  `suffix` varchar(32) DEFAULT NULL,
  `nameString` text,
  `mbox_sha1sum` char(40) DEFAULT NULL,
  `yearOfBirth` int(11) DEFAULT NULL,
  `yearOfBirthModifier` varchar(12) DEFAULT '',
  `yearOfDeath` int(11) DEFAULT NULL,
  `yearOfDeathModifier` varchar(12) DEFAULT '',
  `living` enum('Y','N','?') NOT NULL DEFAULT '?',
  `recordID` char(43) DEFAULT NULL,
  `dateLastModified` datetime DEFAULT NULL,
  `modifiedUid` int(11) unsigned DEFAULT NULL,
  `createdUid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`agentID`),
  KEY `firstname` (`firstName`),
  KEY `FK_agents_preferred_recby` (`preferredRecByID`),
  KEY `FK_agents_modUid_idx` (`modifiedUid`),
  KEY `FK_agents_createdUid_idx` (`createdUid`),
  CONSTRAINT `FK_agents_createdUid` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_agents_modUid` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_agents_preferred_recby` FOREIGN KEY (`preferredRecByID`) REFERENCES `agents` (`agentID`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agents`
--

LOCK TABLES `agents` WRITE;
/*!40000 ALTER TABLE `agents` DISABLE KEYS */;
/*!40000 ALTER TABLE `agents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `agentsfulltext`
--

DROP TABLE IF EXISTS `agentsfulltext`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agentsfulltext` (
  `agentsFulltextID` bigint(20) NOT NULL AUTO_INCREMENT,
  `agentID` int(11) NOT NULL,
  `biography` text,
  `taxonomicGroups` text,
  `collectionsAt` text,
  `notes` text,
  `name` text,
  PRIMARY KEY (`agentsFulltextID`),
  FULLTEXT KEY `ft_collectorbio` (`biography`,`taxonomicGroups`,`collectionsAt`,`notes`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agentsfulltext`
--

LOCK TABLES `agentsfulltext` WRITE;
/*!40000 ALTER TABLE `agentsfulltext` DISABLE KEYS */;
/*!40000 ALTER TABLE `agentsfulltext` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `agentteams`
--

DROP TABLE IF EXISTS `agentteams`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agentteams` (
  `agentTeamID` bigint(20) NOT NULL AUTO_INCREMENT,
  `teamAgentID` bigint(20) NOT NULL,
  `memberAgentID` bigint(20) NOT NULL,
  `ordinal` int(11) DEFAULT NULL,
  PRIMARY KEY (`agentTeamID`),
  KEY `teamagentid` (`teamAgentID`),
  KEY `memberagentid` (`memberAgentID`),
  CONSTRAINT `agentteams_ibfk_1` FOREIGN KEY (`teamAgentID`) REFERENCES `agents` (`agentID`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `agentteams_ibfk_2` FOREIGN KEY (`memberAgentID`) REFERENCES `agents` (`agentID`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agentteams`
--

LOCK TABLES `agentteams` WRITE;
/*!40000 ALTER TABLE `agentteams` DISABLE KEYS */;
/*!40000 ALTER TABLE `agentteams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chotomouskey`
--

DROP TABLE IF EXISTS `chotomouskey`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chotomouskey` (
  `stmtid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `statement` varchar(300) NOT NULL,
  `nodeid` int(10) unsigned NOT NULL,
  `parentid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`stmtid`),
  KEY `FK_chotomouskey_taxa` (`tid`),
  CONSTRAINT `FK_chotomouskey_taxa` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chotomouskey`
--

LOCK TABLES `chotomouskey` WRITE;
/*!40000 ALTER TABLE `chotomouskey` DISABLE KEYS */;
/*!40000 ALTER TABLE `chotomouskey` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configpage`
--

DROP TABLE IF EXISTS `configpage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configpage` (
  `configpageid` int(11) NOT NULL AUTO_INCREMENT,
  `pagename` varchar(45) NOT NULL,
  `title` varchar(150) NOT NULL,
  `cssname` varchar(45) DEFAULT NULL,
  `language` varchar(45) NOT NULL DEFAULT 'english',
  `displaymode` int(11) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `modifiedUid` int(10) unsigned NOT NULL,
  `modifiedtimestamp` datetime DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`configpageid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configpage`
--

LOCK TABLES `configpage` WRITE;
/*!40000 ALTER TABLE `configpage` DISABLE KEYS */;
/*!40000 ALTER TABLE `configpage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configpageattributes`
--

DROP TABLE IF EXISTS `configpageattributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configpageattributes` (
  `attributeid` int(11) NOT NULL AUTO_INCREMENT,
  `configpageid` int(11) NOT NULL,
  `objid` varchar(45) DEFAULT NULL,
  `objname` varchar(45) NOT NULL,
  `value` varchar(45) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL COMMENT 'text, submit, div',
  `width` int(11) DEFAULT NULL,
  `top` int(11) DEFAULT NULL,
  `left` int(11) DEFAULT NULL,
  `stylestr` varchar(45) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `modifiedUid` int(10) unsigned NOT NULL,
  `modifiedtimestamp` datetime DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`attributeid`),
  KEY `FK_configpageattributes_id_idx` (`configpageid`),
  CONSTRAINT `FK_configpageattributes_id` FOREIGN KEY (`configpageid`) REFERENCES `configpage` (`configpageid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configpageattributes`
--

LOCK TABLES `configpageattributes` WRITE;
/*!40000 ALTER TABLE `configpageattributes` DISABLE KEYS */;
/*!40000 ALTER TABLE `configpageattributes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ctcontrolvocab`
--

DROP TABLE IF EXISTS `ctcontrolvocab`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ctcontrolvocab` (
  `cvID` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(45) DEFAULT NULL,
  `definition` varchar(250) DEFAULT NULL,
  `authors` varchar(150) DEFAULT NULL,
  `tableName` varchar(45) DEFAULT NULL,
  `fieldName` varchar(45) DEFAULT NULL,
  `resourceUrl` varchar(150) DEFAULT NULL,
  `ontologyClass` varchar(150) DEFAULT NULL,
  `ontologyUrl` varchar(150) DEFAULT NULL,
  `limitToList` int(2) DEFAULT '0',
  `dynamicProperties` text,
  `notes` varchar(45) DEFAULT NULL,
  `createdUid` int(10) unsigned DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `modifiedTimestamp` datetime DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cvID`),
  KEY `FK_ctControlVocab_createUid_idx` (`createdUid`),
  KEY `FK_ctControlVocab_modUid_idx` (`modifiedUid`),
  CONSTRAINT `FK_ctControlVocab_createUid` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_ctControlVocab_modUid` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ctcontrolvocab`
--

LOCK TABLES `ctcontrolvocab` WRITE;
/*!40000 ALTER TABLE `ctcontrolvocab` DISABLE KEYS */;
INSERT INTO `ctcontrolvocab` VALUES (1,'Occurrence Relationship Terms',NULL,NULL,'omoccurassociations','relationship',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,'2020-12-02 21:35:38'),(2,'Occurrence Relationship subTypes',NULL,NULL,'omoccurassociations','subType',NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,'2020-12-02 22:56:13');
/*!40000 ALTER TABLE `ctcontrolvocab` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ctcontrolvocabterm`
--

DROP TABLE IF EXISTS `ctcontrolvocabterm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ctcontrolvocabterm` (
  `cvTermID` int(11) NOT NULL AUTO_INCREMENT,
  `cvID` int(11) NOT NULL,
  `parentCvTermID` int(11) DEFAULT NULL,
  `term` varchar(45) NOT NULL,
  `termDisplay` varchar(75) DEFAULT NULL,
  `inverseRelationship` varchar(45) DEFAULT NULL,
  `collective` varchar(45) DEFAULT NULL,
  `definition` varchar(250) DEFAULT NULL,
  `resourceUrl` varchar(150) DEFAULT NULL,
  `ontologyClass` varchar(150) DEFAULT NULL,
  `ontologyUrl` varchar(150) DEFAULT NULL,
  `activeStatus` int(11) DEFAULT '1',
  `notes` varchar(250) DEFAULT NULL,
  `createdUid` int(10) unsigned DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `modifiedTimestamp` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cvTermID`),
  UNIQUE KEY `UQ_controlVocabTerm` (`cvID`,`term`),
  KEY `FK_ctcontrolVocabTerm_cvID_idx` (`cvID`),
  KEY `FK_ctControlVocabTerm_createUid_idx` (`createdUid`),
  KEY `FK_ctControlVocabTerm_modUid_idx` (`modifiedUid`),
  KEY `IX_controlVocabTerm_term` (`term`),
  KEY `FK_ctControlVocabTerm_cvTermID` (`parentCvTermID`),
  CONSTRAINT `FK_ctControlVocabTerm_createUid` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_ctControlVocabTerm_cvID` FOREIGN KEY (`cvID`) REFERENCES `ctcontrolvocab` (`cvID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ctControlVocabTerm_cvTermID` FOREIGN KEY (`parentCvTermID`) REFERENCES `ctcontrolvocabterm` (`cvTermID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_ctControlVocabTerm_modUid` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ctcontrolvocabterm`
--

LOCK TABLES `ctcontrolvocabterm` WRITE;
/*!40000 ALTER TABLE `ctcontrolvocabterm` DISABLE KEYS */;
INSERT INTO `ctcontrolvocabterm` VALUES (1,1,NULL,'subsampleOf',NULL,'originatingSampleOf',NULL,'a sample or occurrence that was subsequently derived from an originating sample',NULL,'has part: http://purl.obolibrary.org/obo/BFO_0000050',NULL,1,NULL,NULL,NULL,NULL,'2020-12-02 21:36:51'),(2,1,NULL,'partOf',NULL,'partOf',NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2020-12-02 21:38:32'),(3,1,NULL,'siblingOf',NULL,'siblingOf',NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2020-12-02 21:38:32'),(4,1,NULL,'originatingSampleOf',NULL,'subsampleOf',NULL,'a sample or occurrence that is the originator of a subsequently modified or partial sample',NULL,'partOf: http://purl.obolibrary.org/obo/BFO_0000051',NULL,1,'originatingSourceOf ??  It isn\'t necessarily a sample.  Could be an observation or occurrence or individual etc',NULL,NULL,NULL,'2020-12-02 23:27:02'),(5,1,NULL,'sharesOriginatingSample',NULL,'sharesOriginatingSample',NULL,'two samples or occurrences that were subsequently derived from the same originating sample',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2020-12-02 23:44:23'),(6,2,NULL,'tissue',NULL,NULL,NULL,'a tissue sample or occurrence that was subsequently derived from an originating sample',NULL,'partOf: http://purl.obolibrary.org/obo/BFO_0000051',NULL,1,NULL,NULL,NULL,NULL,'2020-12-02 23:44:23'),(7,2,NULL,'blood',NULL,NULL,NULL,'a blood-tissue sample or occurrence that was subsequently derived from an originating sample',NULL,'partOf: http://purl.obolibrary.org/obo/BFO_0000051',NULL,1,NULL,NULL,NULL,NULL,'2020-12-02 23:44:23'),(8,2,NULL,'fecal',NULL,NULL,NULL,'a fecal sample or occurrence that was subsequently derived from an originating sample',NULL,'partOf: http://purl.obolibrary.org/obo/BFO_0000051',NULL,1,NULL,NULL,NULL,NULL,'2020-12-02 23:44:23'),(9,2,NULL,'hair',NULL,NULL,NULL,'a hair sample or occurrence that was subsequently derived from an originating sample',NULL,'partOf: http://purl.obolibrary.org/obo/BFO_0000051',NULL,1,NULL,NULL,NULL,NULL,'2020-12-02 23:44:23'),(10,2,NULL,'genetic',NULL,NULL,NULL,'a genetic extraction sample or occurrence that was subsequently derived from an originating sample',NULL,'partOf: http://purl.obolibrary.org/obo/BFO_0000051',NULL,1,NULL,NULL,NULL,NULL,'2020-12-02 23:44:23'),(11,1,NULL,'derivedFromSameIndividual',NULL,'derivedFromSameIndividual',NULL,'a sample or occurrence that is derived from the same biological individual as another occurrence or sample',NULL,'partOf: http://purl.obolibrary.org/obo/BFO_0000051',NULL,1,NULL,NULL,NULL,NULL,'2020-12-02 23:48:45'),(12,1,NULL,'analyticalStandardOf',NULL,'hasAnalyticalStandard',NULL,'a sample or occurrence that serves as an analytical standard or control for another occurrence or sample',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2020-12-02 23:48:45'),(13,1,NULL,'hasAnalyticalStandard',NULL,'analyticalStandardof',NULL,'a sample or occurrence that has an available analytical standard or control',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2020-12-02 23:48:45'),(14,1,NULL,'hasHost',NULL,'hostOf',NULL,'X \'has host\' y if and only if: x is an organism, y is an organism, and x can live on the surface of or within the body of y',NULL,'ecologically related to: http://purl.obolibrary.org/obo/RO_0008506','http://purl.obolibrary.org/obo/RO_0002454',1,NULL,NULL,NULL,NULL,'2020-12-02 23:58:18'),(15,1,NULL,'hostOf',NULL,'hasHost',NULL,'X is \'Host of\' y if and only if: x is an organism, y is an organism, and y can live on the surface of or within the body of x',NULL,'ecologically related to: http://purl.obolibrary.org/obo/RO_0008506','http://purl.obolibrary.org/obo/RO_0002453',1,NULL,NULL,NULL,NULL,'2020-12-02 23:58:18'),(16,1,NULL,'ecologicallyOccursWith',NULL,'ecologicallyOccursWith',NULL,'An interaction relationship describing an occurrence occurring with another organism in the same time and space or same environment',NULL,'ecologically related to: http://purl.obolibrary.org/obo/RO_0008506','http://purl.obolibrary.org/obo/RO_0008506',1,NULL,NULL,NULL,NULL,'2020-12-02 23:58:18');
/*!40000 ALTER TABLE `ctcontrolvocabterm` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ctnametypes`
--

DROP TABLE IF EXISTS `ctnametypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ctnametypes` (
  `type` varchar(32) NOT NULL,
  PRIMARY KEY (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ctnametypes`
--

LOCK TABLES `ctnametypes` WRITE;
/*!40000 ALTER TABLE `ctnametypes` DISABLE KEYS */;
INSERT INTO `ctnametypes` VALUES ('Also Known As'),('First Initials Last'),('First Last'),('Full Name'),('Initials Last Name'),('Last Name, Initials'),('Standard Abbreviation'),('Standard DwC List');
/*!40000 ALTER TABLE `ctnametypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ctrelationshiptypes`
--

DROP TABLE IF EXISTS `ctrelationshiptypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ctrelationshiptypes` (
  `relationship` varchar(50) NOT NULL,
  `inverse` varchar(50) DEFAULT NULL,
  `collective` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`relationship`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ctrelationshiptypes`
--

LOCK TABLES `ctrelationshiptypes` WRITE;
/*!40000 ALTER TABLE `ctrelationshiptypes` DISABLE KEYS */;
INSERT INTO `ctrelationshiptypes` VALUES ('Child of','Parent of','Children'),('Could be','Confused with','Confused with'),('Spouse of','Spouse of','Married to'),('Student of','Teacher of','Students');
/*!40000 ALTER TABLE `ctrelationshiptypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fmchecklists`
--

DROP TABLE IF EXISTS `fmchecklists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fmchecklists` (
  `CLID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Title` varchar(150) DEFAULT NULL,
  `Locality` varchar(500) DEFAULT NULL,
  `Publication` varchar(500) DEFAULT NULL,
  `Abstract` text,
  `Authors` varchar(250) DEFAULT NULL,
  `Type` varchar(50) DEFAULT 'static',
  `politicalDivision` varchar(45) DEFAULT NULL,
  `dynamicsql` varchar(500) DEFAULT NULL,
  `Parent` varchar(50) DEFAULT NULL,
  `parentclid` int(10) unsigned DEFAULT NULL,
  `Notes` varchar(500) DEFAULT NULL,
  `LatCentroid` double(9,6) DEFAULT NULL,
  `LongCentroid` double(9,6) DEFAULT NULL,
  `pointradiusmeters` int(10) unsigned DEFAULT NULL,
  `footprintWKT` text,
  `percenteffort` int(11) DEFAULT NULL,
  `Access` varchar(45) DEFAULT 'private',
  `cidKeyLimits` varchar(250) DEFAULT NULL,
  `defaultSettings` varchar(250) DEFAULT NULL,
  `iconUrl` varchar(150) DEFAULT NULL,
  `headerUrl` varchar(150) DEFAULT NULL,
  `uid` int(10) unsigned DEFAULT NULL,
  `SortSequence` int(10) unsigned NOT NULL DEFAULT '50',
  `expiration` int(10) unsigned DEFAULT NULL,
  `DateLastModified` datetime DEFAULT NULL,
  `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`CLID`),
  KEY `FK_checklists_uid` (`uid`),
  KEY `name` (`Name`,`Type`) USING BTREE,
  CONSTRAINT `FK_checklists_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fmchecklists`
--

LOCK TABLES `fmchecklists` WRITE;
/*!40000 ALTER TABLE `fmchecklists` DISABLE KEYS */;
/*!40000 ALTER TABLE `fmchecklists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fmchklstchildren`
--

DROP TABLE IF EXISTS `fmchklstchildren`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fmchklstchildren` (
  `clid` int(10) unsigned NOT NULL,
  `clidchild` int(10) unsigned NOT NULL,
  `modifiedUid` int(10) unsigned NOT NULL,
  `modifiedtimestamp` datetime DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`clid`,`clidchild`),
  KEY `FK_fmchklstchild_clid_idx` (`clid`),
  KEY `FK_fmchklstchild_child_idx` (`clidchild`),
  CONSTRAINT `FK_fmchklstchild_child` FOREIGN KEY (`clidchild`) REFERENCES `fmchecklists` (`CLID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_fmchklstchild_clid` FOREIGN KEY (`clid`) REFERENCES `fmchecklists` (`CLID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fmchklstchildren`
--

LOCK TABLES `fmchklstchildren` WRITE;
/*!40000 ALTER TABLE `fmchklstchildren` DISABLE KEYS */;
/*!40000 ALTER TABLE `fmchklstchildren` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fmchklstcoordinates`
--

DROP TABLE IF EXISTS `fmchklstcoordinates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fmchklstcoordinates` (
  `chklstcoordid` int(11) NOT NULL AUTO_INCREMENT,
  `clid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `decimallatitude` double NOT NULL,
  `decimallongitude` double NOT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`chklstcoordid`),
  UNIQUE KEY `IndexUnique` (`clid`,`tid`,`decimallatitude`,`decimallongitude`),
  KEY `FKchklsttaxalink` (`clid`,`tid`),
  CONSTRAINT `FKchklsttaxalink` FOREIGN KEY (`clid`, `tid`) REFERENCES `fmchklsttaxalink` (`CLID`, `TID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fmchklstcoordinates`
--

LOCK TABLES `fmchklstcoordinates` WRITE;
/*!40000 ALTER TABLE `fmchklstcoordinates` DISABLE KEYS */;
/*!40000 ALTER TABLE `fmchklstcoordinates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fmchklstprojlink`
--

DROP TABLE IF EXISTS `fmchklstprojlink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fmchklstprojlink` (
  `pid` int(10) unsigned NOT NULL,
  `clid` int(10) unsigned NOT NULL,
  `clNameOverride` varchar(100) DEFAULT NULL,
  `mapChecklist` smallint(6) DEFAULT '1',
  `sortSequence` int(11) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pid`,`clid`),
  KEY `FK_chklst` (`clid`),
  CONSTRAINT `FK_chklstprojlink_clid` FOREIGN KEY (`clid`) REFERENCES `fmchecklists` (`CLID`),
  CONSTRAINT `FK_chklstprojlink_proj` FOREIGN KEY (`pid`) REFERENCES `fmprojects` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fmchklstprojlink`
--

LOCK TABLES `fmchklstprojlink` WRITE;
/*!40000 ALTER TABLE `fmchklstprojlink` DISABLE KEYS */;
/*!40000 ALTER TABLE `fmchklstprojlink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fmchklsttaxalink`
--

DROP TABLE IF EXISTS `fmchklsttaxalink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fmchklsttaxalink` (
  `TID` int(10) unsigned NOT NULL DEFAULT '0',
  `CLID` int(10) unsigned NOT NULL DEFAULT '0',
  `morphospecies` varchar(45) NOT NULL DEFAULT '',
  `familyoverride` varchar(50) DEFAULT NULL,
  `Habitat` varchar(250) DEFAULT NULL,
  `Abundance` varchar(50) DEFAULT NULL,
  `Notes` varchar(2000) DEFAULT NULL,
  `explicitExclude` smallint(6) DEFAULT NULL,
  `source` varchar(250) DEFAULT NULL,
  `Nativity` varchar(50) DEFAULT NULL COMMENT 'native, introducted',
  `Endemic` varchar(45) DEFAULT NULL,
  `invasive` varchar(45) DEFAULT NULL,
  `internalnotes` varchar(250) DEFAULT NULL,
  `dynamicProperties` text,
  `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`TID`,`CLID`,`morphospecies`),
  KEY `FK_chklsttaxalink_cid` (`CLID`),
  KEY `FK_chklsttaxalink_tid` (`TID`),
  CONSTRAINT `FK_chklsttaxalink_cid` FOREIGN KEY (`CLID`) REFERENCES `fmchecklists` (`CLID`),
  CONSTRAINT `FK_chklsttaxalink_tid` FOREIGN KEY (`TID`) REFERENCES `taxa` (`TID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fmchklsttaxalink`
--

LOCK TABLES `fmchklsttaxalink` WRITE;
/*!40000 ALTER TABLE `fmchklsttaxalink` DISABLE KEYS */;
/*!40000 ALTER TABLE `fmchklsttaxalink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fmchklsttaxastatus`
--

DROP TABLE IF EXISTS `fmchklsttaxastatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fmchklsttaxastatus` (
  `clid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `geographicRange` int(11) NOT NULL DEFAULT '0',
  `populationRank` int(11) NOT NULL DEFAULT '0',
  `abundance` int(11) NOT NULL DEFAULT '0',
  `habitatSpecificity` int(11) NOT NULL DEFAULT '0',
  `intrinsicRarity` int(11) NOT NULL DEFAULT '0',
  `threatImminence` int(11) NOT NULL DEFAULT '0',
  `populationTrends` int(11) NOT NULL DEFAULT '0',
  `nativeStatus` varchar(45) DEFAULT NULL,
  `endemicStatus` int(11) NOT NULL DEFAULT '0',
  `protectedStatus` varchar(45) DEFAULT NULL,
  `localitySecurity` int(11) DEFAULT NULL,
  `localitySecurityReason` varchar(45) DEFAULT NULL,
  `invasiveStatus` varchar(45) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `modifiedtimestamp` datetime DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`clid`,`tid`),
  KEY `FK_fmchklsttaxastatus_clid_idx` (`clid`,`tid`),
  CONSTRAINT `FK_fmchklsttaxastatus_clidtid` FOREIGN KEY (`clid`, `tid`) REFERENCES `fmchklsttaxalink` (`CLID`, `TID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fmchklsttaxastatus`
--

LOCK TABLES `fmchklsttaxastatus` WRITE;
/*!40000 ALTER TABLE `fmchklsttaxastatus` DISABLE KEYS */;
/*!40000 ALTER TABLE `fmchklsttaxastatus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fmcltaxacomments`
--

DROP TABLE IF EXISTS `fmcltaxacomments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fmcltaxacomments` (
  `cltaxacommentsid` int(11) NOT NULL AUTO_INCREMENT,
  `clid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `comment` text NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `ispublic` int(11) NOT NULL DEFAULT '1',
  `parentid` int(11) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cltaxacommentsid`),
  KEY `FK_clcomment_users` (`uid`),
  KEY `FK_clcomment_cltaxa` (`clid`,`tid`),
  CONSTRAINT `FK_clcomment_cltaxa` FOREIGN KEY (`clid`, `tid`) REFERENCES `fmchklsttaxalink` (`CLID`, `TID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_clcomment_users` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fmcltaxacomments`
--

LOCK TABLES `fmcltaxacomments` WRITE;
/*!40000 ALTER TABLE `fmcltaxacomments` DISABLE KEYS */;
/*!40000 ALTER TABLE `fmcltaxacomments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fmdynamicchecklists`
--

DROP TABLE IF EXISTS `fmdynamicchecklists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fmdynamicchecklists` (
  `dynclid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `details` varchar(250) DEFAULT NULL,
  `uid` varchar(45) DEFAULT NULL,
  `type` varchar(45) NOT NULL DEFAULT 'DynamicList',
  `notes` varchar(250) DEFAULT NULL,
  `expiration` datetime NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`dynclid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fmdynamicchecklists`
--

LOCK TABLES `fmdynamicchecklists` WRITE;
/*!40000 ALTER TABLE `fmdynamicchecklists` DISABLE KEYS */;
/*!40000 ALTER TABLE `fmdynamicchecklists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fmdyncltaxalink`
--

DROP TABLE IF EXISTS `fmdyncltaxalink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fmdyncltaxalink` (
  `dynclid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`dynclid`,`tid`),
  KEY `FK_dyncltaxalink_taxa` (`tid`),
  CONSTRAINT `FK_dyncltaxalink_dynclid` FOREIGN KEY (`dynclid`) REFERENCES `fmdynamicchecklists` (`dynclid`) ON DELETE CASCADE,
  CONSTRAINT `FK_dyncltaxalink_taxa` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fmdyncltaxalink`
--

LOCK TABLES `fmdyncltaxalink` WRITE;
/*!40000 ALTER TABLE `fmdyncltaxalink` DISABLE KEYS */;
/*!40000 ALTER TABLE `fmdyncltaxalink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fmprojectcategories`
--

DROP TABLE IF EXISTS `fmprojectcategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fmprojectcategories` (
  `projcatid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL,
  `categoryname` varchar(150) NOT NULL,
  `managers` varchar(100) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `parentpid` int(11) DEFAULT NULL,
  `occurrencesearch` int(11) DEFAULT '0',
  `ispublic` int(11) DEFAULT '1',
  `notes` varchar(250) DEFAULT NULL,
  `sortsequence` int(11) DEFAULT NULL,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`projcatid`),
  KEY `FK_fmprojcat_pid_idx` (`pid`),
  CONSTRAINT `FK_fmprojcat_pid` FOREIGN KEY (`pid`) REFERENCES `fmprojects` (`pid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fmprojectcategories`
--

LOCK TABLES `fmprojectcategories` WRITE;
/*!40000 ALTER TABLE `fmprojectcategories` DISABLE KEYS */;
/*!40000 ALTER TABLE `fmprojectcategories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fmprojects`
--

DROP TABLE IF EXISTS `fmprojects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fmprojects` (
  `pid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `projname` varchar(45) NOT NULL,
  `displayname` varchar(150) DEFAULT NULL,
  `managers` varchar(150) DEFAULT NULL,
  `briefdescription` varchar(300) DEFAULT NULL,
  `fulldescription` varchar(5000) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `iconUrl` varchar(150) DEFAULT NULL,
  `headerUrl` varchar(150) DEFAULT NULL,
  `occurrencesearch` int(10) unsigned NOT NULL DEFAULT '0',
  `ispublic` int(10) unsigned NOT NULL DEFAULT '0',
  `dynamicProperties` text,
  `parentpid` int(10) unsigned DEFAULT NULL,
  `SortSequence` int(10) unsigned NOT NULL DEFAULT '50',
  `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pid`),
  KEY `FK_parentpid_proj` (`parentpid`),
  CONSTRAINT `FK_parentpid_proj` FOREIGN KEY (`parentpid`) REFERENCES `fmprojects` (`pid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fmprojects`
--

LOCK TABLES `fmprojects` WRITE;
/*!40000 ALTER TABLE `fmprojects` DISABLE KEYS */;
/*!40000 ALTER TABLE `fmprojects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fmvouchers`
--

DROP TABLE IF EXISTS `fmvouchers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fmvouchers` (
  `TID` int(10) unsigned DEFAULT NULL,
  `CLID` int(10) unsigned NOT NULL,
  `occid` int(10) unsigned NOT NULL,
  `editornotes` varchar(50) DEFAULT NULL,
  `preferredImage` int(11) DEFAULT '0',
  `Notes` varchar(250) DEFAULT NULL,
  `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`occid`,`CLID`),
  KEY `chklst_taxavouchers` (`TID`,`CLID`),
  CONSTRAINT `FK_fmvouchers_occ` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_vouchers_cl` FOREIGN KEY (`TID`, `CLID`) REFERENCES `fmchklsttaxalink` (`TID`, `CLID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fmvouchers`
--

LOCK TABLES `fmvouchers` WRITE;
/*!40000 ALTER TABLE `fmvouchers` DISABLE KEYS */;
/*!40000 ALTER TABLE `fmvouchers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `geographicpolygon`
--

DROP TABLE IF EXISTS `geographicpolygon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `geographicpolygon` (
  `geoThesID` int(11) NOT NULL,
  `footprintPolygon` polygon NOT NULL,
  `footprintWKT` longtext,
  `geoJSON` longtext,
  `initialTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`geoThesID`),
  SPATIAL KEY `IX_geopoly_polygon` (`footprintPolygon`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `geographicpolygon`
--

LOCK TABLES `geographicpolygon` WRITE;
/*!40000 ALTER TABLE `geographicpolygon` DISABLE KEYS */;
/*!40000 ALTER TABLE `geographicpolygon` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `geographicthesaurus`
--

DROP TABLE IF EXISTS `geographicthesaurus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `geographicthesaurus` (
  `geoThesID` int(11) NOT NULL AUTO_INCREMENT,
  `geoterm` varchar(100) DEFAULT NULL,
  `abbreviation` varchar(45) DEFAULT NULL,
  `iso2` varchar(45) DEFAULT NULL,
  `iso3` varchar(45) DEFAULT NULL,
  `numcode` int(11) DEFAULT NULL,
  `category` varchar(45) DEFAULT NULL,
  `termstatus` int(11) DEFAULT NULL,
  `acceptedID` int(11) DEFAULT NULL,
  `parentID` int(11) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `dynamicProps` text,
  `footprintWKT` text,
  `initialTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`geoThesID`),
  KEY `IX_geothes_termname` (`geoterm`),
  KEY `IX_geothes_abbreviation` (`abbreviation`),
  KEY `IX_geothes_iso2` (`iso2`),
  KEY `IX_geothes_iso3` (`iso3`),
  KEY `FK_geothes_acceptedID_idx` (`acceptedID`),
  KEY `FK_geothes_parentID_idx` (`parentID`),
  CONSTRAINT `FK_geothes_acceptedID` FOREIGN KEY (`acceptedID`) REFERENCES `geographicthesaurus` (`geoThesID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_geothes_parentID` FOREIGN KEY (`parentID`) REFERENCES `geographicthesaurus` (`geoThesID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `geographicthesaurus`
--

LOCK TABLES `geographicthesaurus` WRITE;
/*!40000 ALTER TABLE `geographicthesaurus` DISABLE KEYS */;
/*!40000 ALTER TABLE `geographicthesaurus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `geothescontinent`
--

DROP TABLE IF EXISTS `geothescontinent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `geothescontinent` (
  `gtcid` int(11) NOT NULL AUTO_INCREMENT,
  `continentterm` varchar(45) NOT NULL,
  `abbreviation` varchar(45) DEFAULT NULL,
  `code` varchar(45) DEFAULT NULL,
  `lookupterm` int(11) NOT NULL DEFAULT '1',
  `acceptedid` int(11) DEFAULT NULL,
  `footprintWKT` text,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`gtcid`),
  KEY `FK_geothescontinent_accepted_idx` (`acceptedid`),
  CONSTRAINT `FK_geothescontinent_accepted` FOREIGN KEY (`acceptedid`) REFERENCES `geothescontinent` (`gtcid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `geothescontinent`
--

LOCK TABLES `geothescontinent` WRITE;
/*!40000 ALTER TABLE `geothescontinent` DISABLE KEYS */;
/*!40000 ALTER TABLE `geothescontinent` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `geothescountry`
--

DROP TABLE IF EXISTS `geothescountry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `geothescountry` (
  `gtcid` int(11) NOT NULL AUTO_INCREMENT,
  `countryterm` varchar(45) NOT NULL,
  `abbreviation` varchar(45) DEFAULT NULL,
  `iso` varchar(2) DEFAULT NULL,
  `iso3` varchar(3) DEFAULT NULL,
  `numcode` int(11) DEFAULT NULL,
  `lookupterm` int(11) NOT NULL DEFAULT '1',
  `acceptedid` int(11) DEFAULT NULL,
  `continentid` int(11) DEFAULT NULL,
  `footprintWKT` text,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`gtcid`),
  KEY `FK_geothescountry__idx` (`continentid`),
  KEY `FK_geothescountry_parent_idx` (`acceptedid`),
  CONSTRAINT `FK_geothescountry_accepted` FOREIGN KEY (`acceptedid`) REFERENCES `geothescountry` (`gtcid`),
  CONSTRAINT `FK_geothescountry_gtcid` FOREIGN KEY (`continentid`) REFERENCES `geothescontinent` (`gtcid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `geothescountry`
--

LOCK TABLES `geothescountry` WRITE;
/*!40000 ALTER TABLE `geothescountry` DISABLE KEYS */;
/*!40000 ALTER TABLE `geothescountry` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `geothescounty`
--

DROP TABLE IF EXISTS `geothescounty`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `geothescounty` (
  `gtcoid` int(11) NOT NULL AUTO_INCREMENT,
  `countyterm` varchar(45) NOT NULL,
  `abbreviation` varchar(45) DEFAULT NULL,
  `code` varchar(45) DEFAULT NULL,
  `lookupterm` int(11) NOT NULL DEFAULT '1',
  `acceptedid` int(11) DEFAULT NULL,
  `stateid` int(11) DEFAULT NULL,
  `footprintWKT` text,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`gtcoid`),
  KEY `FK_geothescounty_state_idx` (`stateid`),
  KEY `FK_geothescounty_accepted_idx` (`acceptedid`),
  CONSTRAINT `FK_geothescounty_accepted` FOREIGN KEY (`acceptedid`) REFERENCES `geothescounty` (`gtcoid`),
  CONSTRAINT `FK_geothescounty_state` FOREIGN KEY (`stateid`) REFERENCES `geothesstateprovince` (`gtspid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `geothescounty`
--

LOCK TABLES `geothescounty` WRITE;
/*!40000 ALTER TABLE `geothescounty` DISABLE KEYS */;
/*!40000 ALTER TABLE `geothescounty` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `geothesmunicipality`
--

DROP TABLE IF EXISTS `geothesmunicipality`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `geothesmunicipality` (
  `gtmid` int(11) NOT NULL AUTO_INCREMENT,
  `municipalityterm` varchar(45) NOT NULL,
  `abbreviation` varchar(45) DEFAULT NULL,
  `code` varchar(45) DEFAULT NULL,
  `lookupterm` int(11) NOT NULL DEFAULT '1',
  `acceptedid` int(11) DEFAULT NULL,
  `countyid` int(11) DEFAULT NULL,
  `footprintWKT` text,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`gtmid`),
  KEY `FK_geothesmunicipality_county_idx` (`countyid`),
  KEY `FK_geothesmunicipality_accepted_idx` (`acceptedid`),
  CONSTRAINT `FK_geothesmunicipality_accepted` FOREIGN KEY (`acceptedid`) REFERENCES `geothesmunicipality` (`gtmid`),
  CONSTRAINT `FK_geothesmunicipality_county` FOREIGN KEY (`countyid`) REFERENCES `geothescounty` (`gtcoid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `geothesmunicipality`
--

LOCK TABLES `geothesmunicipality` WRITE;
/*!40000 ALTER TABLE `geothesmunicipality` DISABLE KEYS */;
/*!40000 ALTER TABLE `geothesmunicipality` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `geothesstateprovince`
--

DROP TABLE IF EXISTS `geothesstateprovince`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `geothesstateprovince` (
  `gtspid` int(11) NOT NULL AUTO_INCREMENT,
  `stateterm` varchar(45) NOT NULL,
  `abbreviation` varchar(45) DEFAULT NULL,
  `code` varchar(45) DEFAULT NULL,
  `lookupterm` int(11) NOT NULL DEFAULT '1',
  `acceptedid` int(11) DEFAULT NULL,
  `countryid` int(11) DEFAULT NULL,
  `footprintWKT` text,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`gtspid`),
  KEY `FK_geothesstate_country_idx` (`countryid`),
  KEY `FK_geothesstate_accepted_idx` (`acceptedid`),
  CONSTRAINT `FK_geothesstate_accepted` FOREIGN KEY (`acceptedid`) REFERENCES `geothesstateprovince` (`gtspid`),
  CONSTRAINT `FK_geothesstate_country` FOREIGN KEY (`countryid`) REFERENCES `geothescountry` (`gtcid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `geothesstateprovince`
--

LOCK TABLES `geothesstateprovince` WRITE;
/*!40000 ALTER TABLE `geothesstateprovince` DISABLE KEYS */;
/*!40000 ALTER TABLE `geothesstateprovince` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `glossary`
--

DROP TABLE IF EXISTS `glossary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `glossary` (
  `glossid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `term` varchar(150) NOT NULL,
  `definition` varchar(2000) DEFAULT NULL,
  `language` varchar(45) NOT NULL DEFAULT 'English',
  `langid` int(10) unsigned DEFAULT NULL,
  `source` varchar(1000) DEFAULT NULL,
  `translator` varchar(250) DEFAULT NULL,
  `author` varchar(250) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `resourceurl` varchar(600) DEFAULT NULL,
  `uid` int(10) unsigned DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`glossid`),
  KEY `Index_term` (`term`),
  KEY `Index_glossary_lang` (`language`),
  KEY `FK_glossary_uid_idx` (`uid`),
  CONSTRAINT `FK_glossary_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `glossary`
--

LOCK TABLES `glossary` WRITE;
/*!40000 ALTER TABLE `glossary` DISABLE KEYS */;
/*!40000 ALTER TABLE `glossary` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `glossaryimages`
--

DROP TABLE IF EXISTS `glossaryimages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `glossaryimages` (
  `glimgid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `glossid` int(10) unsigned NOT NULL,
  `url` varchar(255) NOT NULL,
  `thumbnailurl` varchar(255) DEFAULT NULL,
  `structures` varchar(150) DEFAULT NULL,
  `sortSequence` int(11) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `createdBy` varchar(250) DEFAULT NULL,
  `uid` int(10) unsigned DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`glimgid`),
  KEY `FK_glossaryimages_gloss` (`glossid`),
  KEY `FK_glossaryimages_uid_idx` (`uid`),
  CONSTRAINT `FK_glossaryimages_glossid` FOREIGN KEY (`glossid`) REFERENCES `glossary` (`glossid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_glossaryimages_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `glossaryimages`
--

LOCK TABLES `glossaryimages` WRITE;
/*!40000 ALTER TABLE `glossaryimages` DISABLE KEYS */;
/*!40000 ALTER TABLE `glossaryimages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `glossarysources`
--

DROP TABLE IF EXISTS `glossarysources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `glossarysources` (
  `tid` int(10) unsigned NOT NULL,
  `contributorTerm` varchar(1000) DEFAULT NULL,
  `contributorImage` varchar(1000) DEFAULT NULL,
  `translator` varchar(1000) DEFAULT NULL,
  `additionalSources` varchar(1000) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tid`),
  CONSTRAINT `FK_glossarysources_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `glossarysources`
--

LOCK TABLES `glossarysources` WRITE;
/*!40000 ALTER TABLE `glossarysources` DISABLE KEYS */;
/*!40000 ALTER TABLE `glossarysources` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `glossarytaxalink`
--

DROP TABLE IF EXISTS `glossarytaxalink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `glossarytaxalink` (
  `glossid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`glossid`,`tid`),
  KEY `glossarytaxalink_ibfk_1` (`tid`),
  CONSTRAINT `FK_glossarytaxa_glossid` FOREIGN KEY (`glossid`) REFERENCES `glossary` (`glossid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_glossarytaxa_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `glossarytaxalink`
--

LOCK TABLES `glossarytaxalink` WRITE;
/*!40000 ALTER TABLE `glossarytaxalink` DISABLE KEYS */;
/*!40000 ALTER TABLE `glossarytaxalink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `glossarytermlink`
--

DROP TABLE IF EXISTS `glossarytermlink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `glossarytermlink` (
  `gltlinkid` int(10) NOT NULL AUTO_INCREMENT,
  `glossgrpid` int(10) unsigned NOT NULL,
  `glossid` int(10) unsigned NOT NULL,
  `relationshipType` varchar(45) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`gltlinkid`),
  UNIQUE KEY `Unique_termkeys` (`glossgrpid`,`glossid`),
  KEY `glossarytermlink_ibfk_1` (`glossid`),
  CONSTRAINT `FK_glossarytermlink_glossgrpid` FOREIGN KEY (`glossgrpid`) REFERENCES `glossary` (`glossid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_glossarytermlink_glossid` FOREIGN KEY (`glossid`) REFERENCES `glossary` (`glossid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `glossarytermlink`
--

LOCK TABLES `glossarytermlink` WRITE;
/*!40000 ALTER TABLE `glossarytermlink` DISABLE KEYS */;
/*!40000 ALTER TABLE `glossarytermlink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guidimages`
--

DROP TABLE IF EXISTS `guidimages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `guidimages` (
  `guid` varchar(45) NOT NULL,
  `imgid` int(10) unsigned DEFAULT NULL,
  `archivestatus` int(3) NOT NULL DEFAULT '0',
  `archiveobj` text,
  `notes` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`guid`),
  UNIQUE KEY `guidimages_imgid_unique` (`imgid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guidimages`
--

LOCK TABLES `guidimages` WRITE;
/*!40000 ALTER TABLE `guidimages` DISABLE KEYS */;
/*!40000 ALTER TABLE `guidimages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guidoccurdeterminations`
--

DROP TABLE IF EXISTS `guidoccurdeterminations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `guidoccurdeterminations` (
  `guid` varchar(45) NOT NULL,
  `detid` int(10) unsigned DEFAULT NULL,
  `archivestatus` int(3) NOT NULL DEFAULT '0',
  `archiveobj` text,
  `notes` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`guid`),
  UNIQUE KEY `guidoccurdet_detid_unique` (`detid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guidoccurdeterminations`
--

LOCK TABLES `guidoccurdeterminations` WRITE;
/*!40000 ALTER TABLE `guidoccurdeterminations` DISABLE KEYS */;
/*!40000 ALTER TABLE `guidoccurdeterminations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guidoccurrences`
--

DROP TABLE IF EXISTS `guidoccurrences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `guidoccurrences` (
  `guid` varchar(45) NOT NULL,
  `occid` int(10) unsigned DEFAULT NULL,
  `archivestatus` int(3) NOT NULL DEFAULT '0',
  `archiveobj` text,
  `notes` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`guid`),
  UNIQUE KEY `guidoccurrences_occid_unique` (`occid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guidoccurrences`
--

LOCK TABLES `guidoccurrences` WRITE;
/*!40000 ALTER TABLE `guidoccurrences` DISABLE KEYS */;
INSERT INTO `guidoccurrences` VALUES ('148acae8-1059-4ce6-85bc-740db732c9be',19,0,NULL,NULL,'2023-04-14 22:00:44'),('1914b848-331e-4c7d-9816-1d586f3ea20d',26,0,NULL,NULL,'2023-04-28 17:44:03'),('1e5a16c1-c899-4647-90bd-dc5af0eba3ff',1,0,NULL,NULL,'2023-04-02 02:41:45'),('26734bcf-5d13-44da-bea4-180cae8b3fda',24,0,NULL,NULL,'2023-04-14 22:17:02'),('40947cb7-f878-492a-801a-eb7a3f8e3625',13,0,NULL,NULL,'2023-04-13 19:39:36'),('4231578e-2a32-49ac-921f-e138f956e940',12,0,NULL,NULL,'2023-04-13 19:37:45'),('443256a9-fa41-496b-a846-ec144f244e05',4,0,NULL,NULL,'2023-04-02 19:43:14'),('5070e0ac-273d-4d26-8ffe-07b854e86808',23,0,NULL,NULL,'2023-04-14 22:13:56'),('5304e58c-c824-4ed0-91b8-59e08d333fe9',11,0,NULL,NULL,'2023-04-13 19:36:37'),('55354954-fdfa-4ee7-9a99-52c3b74d54a7',8,0,NULL,NULL,'2023-04-13 14:53:10'),('5cbea89b-9cea-4d65-b04b-011f459558f8',20,0,NULL,NULL,'2023-04-14 22:03:20'),('650fb6e8-ecaf-444f-aa6f-62fd15d62847',21,0,NULL,NULL,'2023-04-14 22:05:17'),('68f19bfa-866d-4ef9-98cf-be36f57a23ac',2,0,NULL,NULL,'2023-04-02 19:04:17'),('7df514d5-0ac5-4f1f-9c1d-ec95bbc8cdc9',15,0,NULL,NULL,'2023-04-13 19:55:16'),('8b434ce1-f040-4a54-ad51-3f6a2bc09167',3,0,NULL,NULL,'2023-04-02 19:41:34'),('9797054d-0d9d-45a1-bc6b-5ad979a31b3d',6,0,NULL,NULL,'2023-04-05 16:22:28'),('9bdb574b-9acc-480b-8e7c-577b81f3003b',14,0,NULL,NULL,'2023-04-13 19:54:22'),('acc1cd60-5afa-4cde-b7cf-86ce90e5791e',22,0,NULL,NULL,'2023-04-14 22:08:24'),('aee5a2c4-9372-4b81-b019-7384e0f2e32b',7,0,NULL,NULL,'2023-04-12 17:20:00'),('ba5ba0dc-2041-416e-b486-bb185d918e37',17,0,NULL,NULL,'2023-04-13 20:03:34'),('ba622b3a-a5ee-46a1-bb09-dfd248c9137e',18,0,NULL,NULL,'2023-04-14 21:57:33'),('bfe1d031-0e20-4bf1-9932-01e2f8a7e169',9,0,NULL,NULL,'2023-04-13 14:53:50'),('d78cee64-61c6-48ef-bdf0-5e7ebc6cd0dc',10,0,NULL,NULL,'2023-04-13 19:33:42'),('e059e30d-283f-41d5-92ea-b3f721107a1a',16,0,NULL,NULL,'2023-04-13 20:02:53'),('e6a376ee-00bc-4687-bce8-6315316d5462',25,0,NULL,NULL,'2023-04-14 22:19:22'),('f41f6eb4-5521-4987-a906-19192800289d',5,0,NULL,NULL,'2023-04-02 19:45:25');
/*!40000 ALTER TABLE `guidoccurrences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `igsnverification`
--

DROP TABLE IF EXISTS `igsnverification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `igsnverification` (
  `igsn` varchar(15) NOT NULL,
  `occid` int(10) unsigned DEFAULT NULL,
  `catalogNumber` varchar(45) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `FK_igsn_occid_idx` (`occid`),
  KEY `INDEX_igsn` (`igsn`),
  CONSTRAINT `FK_igsn_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `igsnverification`
--

LOCK TABLES `igsnverification` WRITE;
/*!40000 ALTER TABLE `igsnverification` DISABLE KEYS */;
/*!40000 ALTER TABLE `igsnverification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `imageannotations`
--

DROP TABLE IF EXISTS `imageannotations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `imageannotations` (
  `tid` int(10) unsigned DEFAULT NULL,
  `imgid` int(10) unsigned NOT NULL DEFAULT '0',
  `AnnDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Annotator` varchar(100) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`imgid`,`AnnDate`) USING BTREE,
  KEY `TID` (`tid`) USING BTREE,
  CONSTRAINT `FK_resourceannotations_imgid` FOREIGN KEY (`imgid`) REFERENCES `images` (`imgid`),
  CONSTRAINT `FK_resourceannotations_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `imageannotations`
--

LOCK TABLES `imageannotations` WRITE;
/*!40000 ALTER TABLE `imageannotations` DISABLE KEYS */;
/*!40000 ALTER TABLE `imageannotations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `imagekeywords`
--

DROP TABLE IF EXISTS `imagekeywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `imagekeywords` (
  `imgkeywordid` int(11) NOT NULL AUTO_INCREMENT,
  `imgid` int(10) unsigned NOT NULL,
  `keyword` varchar(45) NOT NULL,
  `uidassignedby` int(10) unsigned DEFAULT NULL,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`imgkeywordid`),
  KEY `FK_imagekeywords_imgid_idx` (`imgid`),
  KEY `FK_imagekeyword_uid_idx` (`uidassignedby`),
  KEY `INDEX_imagekeyword` (`keyword`),
  CONSTRAINT `FK_imagekeyword_uid` FOREIGN KEY (`uidassignedby`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_imagekeywords_imgid` FOREIGN KEY (`imgid`) REFERENCES `images` (`imgid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `imagekeywords`
--

LOCK TABLES `imagekeywords` WRITE;
/*!40000 ALTER TABLE `imagekeywords` DISABLE KEYS */;
/*!40000 ALTER TABLE `imagekeywords` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `imageprojectlink`
--

DROP TABLE IF EXISTS `imageprojectlink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `imageprojectlink` (
  `imgid` int(10) unsigned NOT NULL,
  `imgprojid` int(11) NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`imgid`,`imgprojid`),
  KEY `FK_imageprojlink_imgprojid_idx` (`imgprojid`),
  CONSTRAINT `FK_imageprojectlink_imgid` FOREIGN KEY (`imgid`) REFERENCES `images` (`imgid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_imageprojlink_imgprojid` FOREIGN KEY (`imgprojid`) REFERENCES `imageprojects` (`imgprojid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `imageprojectlink`
--

LOCK TABLES `imageprojectlink` WRITE;
/*!40000 ALTER TABLE `imageprojectlink` DISABLE KEYS */;
/*!40000 ALTER TABLE `imageprojectlink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `imageprojects`
--

DROP TABLE IF EXISTS `imageprojects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `imageprojects` (
  `imgprojid` int(11) NOT NULL AUTO_INCREMENT,
  `projectname` varchar(75) NOT NULL,
  `managers` varchar(150) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `ispublic` int(11) NOT NULL DEFAULT '1',
  `notes` varchar(250) DEFAULT NULL,
  `uidcreated` int(11) DEFAULT NULL,
  `sortsequence` int(11) DEFAULT '50',
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`imgprojid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `imageprojects`
--

LOCK TABLES `imageprojects` WRITE;
/*!40000 ALTER TABLE `imageprojects` DISABLE KEYS */;
/*!40000 ALTER TABLE `imageprojects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `images`
--

DROP TABLE IF EXISTS `images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `images` (
  `imgid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `thumbnailurl` varchar(255) DEFAULT NULL,
  `originalurl` varchar(255) DEFAULT NULL,
  `archiveurl` varchar(255) DEFAULT NULL,
  `photographer` varchar(100) DEFAULT NULL,
  `photographeruid` int(10) unsigned DEFAULT NULL,
  `imagetype` varchar(50) DEFAULT NULL,
  `format` varchar(45) DEFAULT NULL,
  `caption` varchar(100) DEFAULT NULL,
  `owner` varchar(250) DEFAULT NULL,
  `sourceurl` varchar(255) DEFAULT NULL,
  `referenceUrl` varchar(255) DEFAULT NULL,
  `copyright` varchar(255) DEFAULT NULL,
  `rights` varchar(255) DEFAULT NULL,
  `accessrights` varchar(255) DEFAULT NULL,
  `locality` varchar(250) DEFAULT NULL,
  `occid` int(10) unsigned DEFAULT NULL,
  `notes` varchar(350) DEFAULT NULL,
  `anatomy` varchar(100) DEFAULT NULL,
  `username` varchar(45) DEFAULT NULL,
  `sourceIdentifier` varchar(150) DEFAULT NULL,
  `mediaMD5` varchar(45) DEFAULT NULL,
  `dynamicProperties` text,
  `defaultDisplay` int(11) DEFAULT NULL,
  `sortsequence` int(10) unsigned NOT NULL DEFAULT '50',
  `sortOccurrence` int(11) DEFAULT '5',
  `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`imgid`) USING BTREE,
  KEY `Index_tid` (`tid`),
  KEY `FK_images_occ` (`occid`),
  KEY `FK_photographeruid` (`photographeruid`),
  KEY `Index_images_datelastmod` (`InitialTimeStamp`),
  CONSTRAINT `FK_images_occ` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`),
  CONSTRAINT `FK_photographeruid` FOREIGN KEY (`photographeruid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_taxaimagestid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `images`
--

LOCK TABLES `images` WRITE;
/*!40000 ALTER TABLE `images` DISABLE KEYS */;
/*!40000 ALTER TABLE `images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `imagetag`
--

DROP TABLE IF EXISTS `imagetag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `imagetag` (
  `imagetagid` bigint(20) NOT NULL AUTO_INCREMENT,
  `imgid` int(10) unsigned NOT NULL,
  `keyvalue` varchar(30) NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`imagetagid`),
  UNIQUE KEY `imgid` (`imgid`,`keyvalue`),
  KEY `keyvalue` (`keyvalue`),
  KEY `FK_imagetag_imgid_idx` (`imgid`),
  CONSTRAINT `FK_imagetag_imgid` FOREIGN KEY (`imgid`) REFERENCES `images` (`imgid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_imagetag_tagkey` FOREIGN KEY (`keyvalue`) REFERENCES `imagetagkey` (`tagkey`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `imagetag`
--

LOCK TABLES `imagetag` WRITE;
/*!40000 ALTER TABLE `imagetag` DISABLE KEYS */;
/*!40000 ALTER TABLE `imagetag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `imagetagkey`
--

DROP TABLE IF EXISTS `imagetagkey`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `imagetagkey` (
  `tagkey` varchar(30) NOT NULL,
  `shortlabel` varchar(30) NOT NULL,
  `description_en` varchar(255) NOT NULL,
  `sortorder` int(11) NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tagkey`),
  KEY `sortorder` (`sortorder`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `imagetagkey`
--

LOCK TABLES `imagetagkey` WRITE;
/*!40000 ALTER TABLE `imagetagkey` DISABLE KEYS */;
INSERT INTO `imagetagkey` VALUES ('Diagnostic','Diagnostic','Image contains a diagnostic character.',70,'2022-10-18 23:21:42'),('Handwriting','Handwritten','Image has handwritten label text.',40,'2022-10-18 23:21:42'),('HasIDLabel','Annotation','Image shows an annotation/identification label.',20,'2022-10-18 23:21:42'),('HasLabel','Label','Image shows label data.',10,'2022-10-18 23:21:42'),('HasOrganism','Organism','Image shows an organism.',0,'2022-10-18 23:21:42'),('HasProblem','QC Problem','There is a problem with this image.',60,'2022-10-18 23:21:42'),('ImageOfAdult','Adult','Image contains the adult organism.',80,'2022-10-18 23:21:42'),('ImageOfImmature','Immature','Image contains the immature organism.',90,'2022-10-18 23:21:42'),('ShowsHabitat','Habitat','Field image of habitat.',50,'2022-10-18 23:21:42'),('TypedText','Typed/Printed','Image has typed or printed text.',30,'2022-10-18 23:21:42');
/*!40000 ALTER TABLE `imagetagkey` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `institutions`
--

DROP TABLE IF EXISTS `institutions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `institutions` (
  `iid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `InstitutionCode` varchar(45) NOT NULL,
  `InstitutionName` varchar(150) NOT NULL,
  `InstitutionName2` varchar(150) DEFAULT NULL,
  `Address1` varchar(150) DEFAULT NULL,
  `Address2` varchar(150) DEFAULT NULL,
  `City` varchar(45) DEFAULT NULL,
  `StateProvince` varchar(45) DEFAULT NULL,
  `PostalCode` varchar(45) DEFAULT NULL,
  `Country` varchar(45) DEFAULT NULL,
  `Phone` varchar(45) DEFAULT NULL,
  `Contact` varchar(65) DEFAULT NULL,
  `Email` varchar(45) DEFAULT NULL,
  `Url` varchar(250) DEFAULT NULL,
  `Notes` varchar(250) DEFAULT NULL,
  `modifieduid` int(10) unsigned DEFAULT NULL,
  `modifiedTimeStamp` datetime DEFAULT NULL,
  `IntialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`iid`),
  KEY `FK_inst_uid_idx` (`modifieduid`),
  CONSTRAINT `FK_inst_uid` FOREIGN KEY (`modifieduid`) REFERENCES `users` (`uid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `institutions`
--

LOCK TABLES `institutions` WRITE;
/*!40000 ALTER TABLE `institutions` DISABLE KEYS */;
/*!40000 ALTER TABLE `institutions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kmcharacterlang`
--

DROP TABLE IF EXISTS `kmcharacterlang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kmcharacterlang` (
  `cid` int(10) unsigned NOT NULL,
  `charname` varchar(150) NOT NULL,
  `language` varchar(45) DEFAULT NULL,
  `langid` int(11) NOT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `helpurl` varchar(500) DEFAULT NULL,
  `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cid`,`langid`) USING BTREE,
  KEY `FK_charlang_lang_idx` (`langid`),
  CONSTRAINT `FK_characterlang_1` FOREIGN KEY (`cid`) REFERENCES `kmcharacters` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_charlang_lang` FOREIGN KEY (`langid`) REFERENCES `adminlanguages` (`langid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kmcharacterlang`
--

LOCK TABLES `kmcharacterlang` WRITE;
/*!40000 ALTER TABLE `kmcharacterlang` DISABLE KEYS */;
/*!40000 ALTER TABLE `kmcharacterlang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kmcharacters`
--

DROP TABLE IF EXISTS `kmcharacters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kmcharacters` (
  `cid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `charname` varchar(150) NOT NULL,
  `chartype` varchar(2) NOT NULL DEFAULT 'UM',
  `defaultlang` varchar(45) NOT NULL DEFAULT 'English',
  `difficultyrank` smallint(5) unsigned NOT NULL DEFAULT '1',
  `hid` int(10) unsigned DEFAULT NULL,
  `units` varchar(45) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `glossid` int(10) unsigned DEFAULT NULL,
  `helpurl` varchar(500) DEFAULT NULL,
  `referenceUrl` varchar(250) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `activationCode` int(11) DEFAULT NULL,
  `sortsequence` int(10) unsigned DEFAULT NULL,
  `enteredby` varchar(45) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cid`),
  KEY `Index_charname` (`charname`),
  KEY `Index_sort` (`sortsequence`),
  KEY `FK_charheading_idx` (`hid`),
  KEY `FK_kmchar_glossary_idx` (`glossid`),
  CONSTRAINT `FK_charheading` FOREIGN KEY (`hid`) REFERENCES `kmcharheading` (`hid`) ON UPDATE CASCADE,
  CONSTRAINT `FK_kmchar_glossary` FOREIGN KEY (`glossid`) REFERENCES `glossary` (`glossid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kmcharacters`
--

LOCK TABLES `kmcharacters` WRITE;
/*!40000 ALTER TABLE `kmcharacters` DISABLE KEYS */;
/*!40000 ALTER TABLE `kmcharacters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kmchardependance`
--

DROP TABLE IF EXISTS `kmchardependance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kmchardependance` (
  `CID` int(10) unsigned NOT NULL,
  `CIDDependance` int(10) unsigned NOT NULL,
  `CSDependance` varchar(16) NOT NULL,
  `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`CSDependance`,`CIDDependance`,`CID`) USING BTREE,
  KEY `FK_chardependance_cid_idx` (`CID`),
  KEY `FK_chardependance_cs_idx` (`CIDDependance`,`CSDependance`),
  CONSTRAINT `FK_chardependance_cid` FOREIGN KEY (`CID`) REFERENCES `kmcharacters` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_chardependance_cs` FOREIGN KEY (`CIDDependance`, `CSDependance`) REFERENCES `kmcs` (`cid`, `cs`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kmchardependance`
--

LOCK TABLES `kmchardependance` WRITE;
/*!40000 ALTER TABLE `kmchardependance` DISABLE KEYS */;
/*!40000 ALTER TABLE `kmchardependance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kmcharheading`
--

DROP TABLE IF EXISTS `kmcharheading`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kmcharheading` (
  `hid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `headingname` varchar(255) NOT NULL,
  `language` varchar(45) DEFAULT 'English',
  `langid` int(11) NOT NULL,
  `notes` longtext,
  `sortsequence` int(11) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`hid`,`langid`) USING BTREE,
  UNIQUE KEY `unique_kmcharheading` (`headingname`,`langid`),
  KEY `HeadingName` (`headingname`) USING BTREE,
  KEY `FK_kmcharheading_lang_idx` (`langid`),
  CONSTRAINT `FK_kmcharheading_lang` FOREIGN KEY (`langid`) REFERENCES `adminlanguages` (`langid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kmcharheading`
--

LOCK TABLES `kmcharheading` WRITE;
/*!40000 ALTER TABLE `kmcharheading` DISABLE KEYS */;
/*!40000 ALTER TABLE `kmcharheading` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kmcharheadinglang`
--

DROP TABLE IF EXISTS `kmcharheadinglang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kmcharheadinglang` (
  `hid` int(10) unsigned NOT NULL,
  `langid` int(11) NOT NULL,
  `headingname` varchar(100) NOT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`hid`,`langid`),
  KEY `FK_kmcharheadinglang_langid` (`langid`),
  CONSTRAINT `FK_kmcharheadinglang_hid` FOREIGN KEY (`hid`) REFERENCES `kmcharheading` (`hid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_kmcharheadinglang_langid` FOREIGN KEY (`langid`) REFERENCES `adminlanguages` (`langid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kmcharheadinglang`
--

LOCK TABLES `kmcharheadinglang` WRITE;
/*!40000 ALTER TABLE `kmcharheadinglang` DISABLE KEYS */;
/*!40000 ALTER TABLE `kmcharheadinglang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kmchartaxalink`
--

DROP TABLE IF EXISTS `kmchartaxalink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kmchartaxalink` (
  `CID` int(10) unsigned NOT NULL DEFAULT '0',
  `TID` int(10) unsigned NOT NULL DEFAULT '0',
  `Status` varchar(50) DEFAULT NULL,
  `Notes` varchar(255) DEFAULT NULL,
  `Relation` varchar(45) NOT NULL DEFAULT 'include',
  `EditabilityInherited` bit(1) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`CID`,`TID`),
  KEY `FK_CharTaxaLink-TID` (`TID`),
  CONSTRAINT `FK_chartaxalink_cid` FOREIGN KEY (`CID`) REFERENCES `kmcharacters` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_chartaxalink_tid` FOREIGN KEY (`TID`) REFERENCES `taxa` (`TID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kmchartaxalink`
--

LOCK TABLES `kmchartaxalink` WRITE;
/*!40000 ALTER TABLE `kmchartaxalink` DISABLE KEYS */;
/*!40000 ALTER TABLE `kmchartaxalink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kmcs`
--

DROP TABLE IF EXISTS `kmcs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kmcs` (
  `cid` int(10) unsigned NOT NULL DEFAULT '0',
  `cs` varchar(16) NOT NULL,
  `CharStateName` varchar(255) DEFAULT NULL,
  `Implicit` tinyint(1) NOT NULL DEFAULT '0',
  `Notes` longtext,
  `Description` varchar(255) DEFAULT NULL,
  `IllustrationUrl` varchar(250) DEFAULT NULL,
  `referenceUrl` varchar(250) DEFAULT NULL,
  `glossid` int(10) unsigned DEFAULT NULL,
  `StateID` int(10) unsigned DEFAULT NULL,
  `SortSequence` int(10) unsigned DEFAULT NULL,
  `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `EnteredBy` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`cs`,`cid`),
  KEY `FK_cs_chars` (`cid`),
  KEY `FK_kmcs_glossid_idx` (`glossid`),
  CONSTRAINT `FK_cs_chars` FOREIGN KEY (`cid`) REFERENCES `kmcharacters` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_kmcs_glossid` FOREIGN KEY (`glossid`) REFERENCES `glossary` (`glossid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kmcs`
--

LOCK TABLES `kmcs` WRITE;
/*!40000 ALTER TABLE `kmcs` DISABLE KEYS */;
/*!40000 ALTER TABLE `kmcs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kmcsimages`
--

DROP TABLE IF EXISTS `kmcsimages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kmcsimages` (
  `csimgid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cid` int(10) unsigned NOT NULL,
  `cs` varchar(16) NOT NULL,
  `url` varchar(255) NOT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `sortsequence` varchar(45) NOT NULL DEFAULT '50',
  `username` varchar(45) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`csimgid`),
  KEY `FK_kscsimages_kscs_idx` (`cid`,`cs`),
  CONSTRAINT `FK_kscsimages_kscs` FOREIGN KEY (`cid`, `cs`) REFERENCES `kmcs` (`cid`, `cs`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kmcsimages`
--

LOCK TABLES `kmcsimages` WRITE;
/*!40000 ALTER TABLE `kmcsimages` DISABLE KEYS */;
/*!40000 ALTER TABLE `kmcsimages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kmcslang`
--

DROP TABLE IF EXISTS `kmcslang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kmcslang` (
  `cid` int(10) unsigned NOT NULL,
  `cs` varchar(16) NOT NULL,
  `charstatename` varchar(150) NOT NULL,
  `language` varchar(45) NOT NULL,
  `langid` int(11) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `intialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cid`,`cs`,`langid`),
  KEY `FK_cslang_lang_idx` (`langid`),
  CONSTRAINT `FK_cslang_1` FOREIGN KEY (`cid`, `cs`) REFERENCES `kmcs` (`cid`, `cs`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_cslang_lang` FOREIGN KEY (`langid`) REFERENCES `adminlanguages` (`langid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kmcslang`
--

LOCK TABLES `kmcslang` WRITE;
/*!40000 ALTER TABLE `kmcslang` DISABLE KEYS */;
/*!40000 ALTER TABLE `kmcslang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kmdescr`
--

DROP TABLE IF EXISTS `kmdescr`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kmdescr` (
  `TID` int(10) unsigned NOT NULL DEFAULT '0',
  `CID` int(10) unsigned NOT NULL DEFAULT '0',
  `Modifier` varchar(255) DEFAULT NULL,
  `CS` varchar(16) NOT NULL,
  `X` double(15,5) DEFAULT NULL,
  `TXT` longtext,
  `PseudoTrait` int(5) unsigned DEFAULT '0',
  `Frequency` int(5) unsigned NOT NULL DEFAULT '5' COMMENT 'Frequency of occurrence; 1 = rare... 5 = common',
  `Inherited` varchar(50) DEFAULT NULL,
  `Source` varchar(100) DEFAULT NULL,
  `Seq` int(10) DEFAULT NULL,
  `Notes` longtext,
  `DateEntered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`TID`,`CID`,`CS`),
  KEY `CSDescr` (`CID`,`CS`),
  CONSTRAINT `FK_descr_cs` FOREIGN KEY (`CID`, `CS`) REFERENCES `kmcs` (`cid`, `cs`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_descr_tid` FOREIGN KEY (`TID`) REFERENCES `taxa` (`TID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kmdescr`
--

LOCK TABLES `kmdescr` WRITE;
/*!40000 ALTER TABLE `kmdescr` DISABLE KEYS */;
/*!40000 ALTER TABLE `kmdescr` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kmdescrdeletions`
--

DROP TABLE IF EXISTS `kmdescrdeletions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kmdescrdeletions` (
  `TID` int(10) unsigned NOT NULL,
  `CID` int(10) unsigned NOT NULL,
  `CS` varchar(16) NOT NULL,
  `Modifier` varchar(255) DEFAULT NULL,
  `X` double(15,5) DEFAULT NULL,
  `TXT` longtext,
  `Inherited` varchar(50) DEFAULT NULL,
  `Source` varchar(100) DEFAULT NULL,
  `Seq` int(10) unsigned DEFAULT NULL,
  `Notes` longtext,
  `InitialTimeStamp` datetime DEFAULT NULL,
  `DeletedBy` varchar(100) NOT NULL,
  `DeletedTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `PK` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`PK`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kmdescrdeletions`
--

LOCK TABLES `kmdescrdeletions` WRITE;
/*!40000 ALTER TABLE `kmdescrdeletions` DISABLE KEYS */;
/*!40000 ALTER TABLE `kmdescrdeletions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lkupcountry`
--

DROP TABLE IF EXISTS `lkupcountry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lkupcountry` (
  `countryId` int(11) NOT NULL AUTO_INCREMENT,
  `countryName` varchar(100) NOT NULL,
  `iso` varchar(2) DEFAULT NULL,
  `iso3` varchar(3) DEFAULT NULL,
  `numcode` int(11) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`countryId`),
  UNIQUE KEY `country_unique` (`countryName`),
  KEY `Index_lkupcountry_iso` (`iso`),
  KEY `Index_lkupcountry_iso3` (`iso3`)
) ENGINE=InnoDB AUTO_INCREMENT=265 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lkupcountry`
--

LOCK TABLES `lkupcountry` WRITE;
/*!40000 ALTER TABLE `lkupcountry` DISABLE KEYS */;
INSERT INTO `lkupcountry` VALUES (1,'Andorra','AD','AND',20,'2022-10-18 23:21:41'),(2,'United Arab Emirates','AE','ARE',784,'2022-10-18 23:21:41'),(3,'Afghanistan','AF','AFG',4,'2022-10-18 23:21:41'),(4,'Antigua and Barbuda','AG','ATG',28,'2022-10-18 23:21:41'),(5,'Anguilla','AI','AIA',660,'2022-10-18 23:21:41'),(6,'Albania','AL','ALB',8,'2022-10-18 23:21:41'),(7,'Armenia','AM','ARM',51,'2022-10-18 23:21:41'),(8,'Netherlands Antilles','AN','ANT',530,'2022-10-18 23:21:41'),(9,'Angola','AO','AGO',24,'2022-10-18 23:21:41'),(10,'Antarctica','AQ',NULL,NULL,'2022-10-18 23:21:41'),(11,'Argentina','AR','ARG',32,'2022-10-18 23:21:41'),(12,'American Samoa','AS','ASM',16,'2022-10-18 23:21:41'),(13,'Austria','AT','AUT',40,'2022-10-18 23:21:41'),(14,'Australia','AU','AUS',36,'2022-10-18 23:21:41'),(15,'Aruba','AW','ABW',533,'2022-10-18 23:21:41'),(16,'Azerbaijan','AZ','AZE',31,'2022-10-18 23:21:41'),(17,'Bosnia and Herzegovina','BA','BIH',70,'2022-10-18 23:21:41'),(18,'Barbados','BB','BRB',52,'2022-10-18 23:21:41'),(19,'Bangladesh','BD','BGD',50,'2022-10-18 23:21:41'),(20,'Belgium','BE','BEL',56,'2022-10-18 23:21:41'),(21,'Burkina Faso','BF','BFA',854,'2022-10-18 23:21:41'),(22,'Bulgaria','BG','BGR',100,'2022-10-18 23:21:41'),(23,'Bahrain','BH','BHR',48,'2022-10-18 23:21:41'),(24,'Burundi','BI','BDI',108,'2022-10-18 23:21:41'),(25,'Benin','BJ','BEN',204,'2022-10-18 23:21:41'),(26,'Bermuda','BM','BMU',60,'2022-10-18 23:21:41'),(27,'Brunei Darussalam','BN','BRN',96,'2022-10-18 23:21:41'),(28,'Bolivia','BO','BOL',68,'2022-10-18 23:21:41'),(29,'Brazil','BR','BRA',76,'2022-10-18 23:21:41'),(30,'Bahamas','BS','BHS',44,'2022-10-18 23:21:41'),(31,'Bhutan','BT','BTN',64,'2022-10-18 23:21:41'),(32,'Bouvet Island','BV',NULL,NULL,'2022-10-18 23:21:41'),(33,'Botswana','BW','BWA',72,'2022-10-18 23:21:41'),(34,'Belarus','BY','BLR',112,'2022-10-18 23:21:41'),(35,'Belize','BZ','BLZ',84,'2022-10-18 23:21:41'),(36,'Canada','CA','CAN',124,'2022-10-18 23:21:41'),(37,'Cocos (Keeling) Islands','CC',NULL,NULL,'2022-10-18 23:21:41'),(38,'Congo, the Democratic Republic of the','CD','COD',180,'2022-10-18 23:21:41'),(39,'Central African Republic','CF','CAF',140,'2022-10-18 23:21:41'),(40,'Congo','CG','COG',178,'2022-10-18 23:21:41'),(41,'Switzerland','CH','CHE',756,'2022-10-18 23:21:41'),(42,'Cote D\'Ivoire','CI','CIV',384,'2022-10-18 23:21:41'),(43,'Cook Islands','CK','COK',184,'2022-10-18 23:21:41'),(44,'Chile','CL','CHL',152,'2022-10-18 23:21:41'),(45,'Cameroon','CM','CMR',120,'2022-10-18 23:21:41'),(46,'China','CN','CHN',156,'2022-10-18 23:21:41'),(47,'Colombia','CO','COL',170,'2022-10-18 23:21:41'),(48,'Costa Rica','CR','CRI',188,'2022-10-18 23:21:41'),(49,'Serbia and Montenegro','CS',NULL,NULL,'2022-10-18 23:21:41'),(50,'Cuba','CU','CUB',192,'2022-10-18 23:21:41'),(51,'Cape Verde','CV','CPV',132,'2022-10-18 23:21:41'),(52,'Christmas Island','CX',NULL,NULL,'2022-10-18 23:21:41'),(53,'Cyprus','CY','CYP',196,'2022-10-18 23:21:41'),(54,'Czech Republic','CZ','CZE',203,'2022-10-18 23:21:41'),(55,'Germany','DE','DEU',276,'2022-10-18 23:21:41'),(56,'Djibouti','DJ','DJI',262,'2022-10-18 23:21:41'),(57,'Denmark','DK','DNK',208,'2022-10-18 23:21:41'),(58,'Dominica','DM','DMA',212,'2022-10-18 23:21:41'),(59,'Dominican Republic','DO','DOM',214,'2022-10-18 23:21:41'),(60,'Algeria','DZ','DZA',12,'2022-10-18 23:21:41'),(61,'Ecuador','EC','ECU',218,'2022-10-18 23:21:41'),(62,'Estonia','EE','EST',233,'2022-10-18 23:21:41'),(63,'Egypt','EG','EGY',818,'2022-10-18 23:21:41'),(64,'Western Sahara','EH','ESH',732,'2022-10-18 23:21:41'),(65,'Eritrea','ER','ERI',232,'2022-10-18 23:21:41'),(66,'Spain','ES','ESP',724,'2022-10-18 23:21:41'),(67,'Ethiopia','ET','ETH',231,'2022-10-18 23:21:41'),(68,'Finland','FI','FIN',246,'2022-10-18 23:21:41'),(69,'Fiji','FJ','FJI',242,'2022-10-18 23:21:41'),(70,'Falkland  Islands (Malvinas)','FK','FLK',238,'2022-10-18 23:21:41'),(71,'Micronesia, Federated States of','FM','FSM',583,'2022-10-18 23:21:41'),(72,'Faroe Islands','FO','FRO',234,'2022-10-18 23:21:41'),(73,'France','FR','FRA',250,'2022-10-18 23:21:41'),(74,'Gabon','GA','GAB',266,'2022-10-18 23:21:41'),(75,'United Kingdom','GB','GBR',826,'2022-10-18 23:21:41'),(76,'Grenada','GD','GRD',308,'2022-10-18 23:21:41'),(77,'Georgia','GE','GEO',268,'2022-10-18 23:21:41'),(78,'French Guiana','GF','GUF',254,'2022-10-18 23:21:41'),(79,'Ghana','GH','GHA',288,'2022-10-18 23:21:41'),(80,'Gibraltar','GI','GIB',292,'2022-10-18 23:21:41'),(81,'Greenland','GL','GRL',304,'2022-10-18 23:21:41'),(82,'Gambia','GM','GMB',270,'2022-10-18 23:21:41'),(83,'Guinea','GN','GIN',324,'2022-10-18 23:21:41'),(84,'Guadeloupe','GP','GLP',312,'2022-10-18 23:21:41'),(85,'Equatorial Guinea','GQ','GNQ',226,'2022-10-18 23:21:41'),(86,'Greece','GR','GRC',300,'2022-10-18 23:21:41'),(87,'South Georgia and the South Sandwich Islands','GS',NULL,NULL,'2022-10-18 23:21:41'),(88,'Guatemala','GT','GTM',320,'2022-10-18 23:21:41'),(89,'Guam','GU','GUM',316,'2022-10-18 23:21:41'),(90,'Guinea-Bissau','GW','GNB',624,'2022-10-18 23:21:41'),(91,'Guyana','GY','GUY',328,'2022-10-18 23:21:41'),(92,'Hong Kong','HK','HKG',344,'2022-10-18 23:21:41'),(93,'Heard Island and Mcdonald Islands','HM',NULL,NULL,'2022-10-18 23:21:41'),(94,'Honduras','HN','HND',340,'2022-10-18 23:21:41'),(95,'Croatia','HR','HRV',191,'2022-10-18 23:21:41'),(96,'Haiti','HT','HTI',332,'2022-10-18 23:21:41'),(97,'Hungary','HU','HUN',348,'2022-10-18 23:21:41'),(98,'Indonesia','ID','IDN',360,'2022-10-18 23:21:41'),(99,'Ireland','IE','IRL',372,'2022-10-18 23:21:41'),(100,'Israel','IL','ISR',376,'2022-10-18 23:21:41'),(101,'India','IN','IND',356,'2022-10-18 23:21:41'),(102,'British Indian Ocean Territory','IO',NULL,NULL,'2022-10-18 23:21:41'),(103,'Iraq','IQ','IRQ',368,'2022-10-18 23:21:41'),(104,'Iran, Islamic Republic of','IR','IRN',364,'2022-10-18 23:21:41'),(105,'Iceland','IS','ISL',352,'2022-10-18 23:21:41'),(106,'Italy','IT','ITA',380,'2022-10-18 23:21:41'),(107,'Jamaica','JM','JAM',388,'2022-10-18 23:21:41'),(108,'Jordan','JO','JOR',400,'2022-10-18 23:21:41'),(109,'Japan','JP','JPN',392,'2022-10-18 23:21:41'),(110,'Kenya','KE','KEN',404,'2022-10-18 23:21:41'),(111,'Kyrgyzstan','KG','KGZ',417,'2022-10-18 23:21:41'),(112,'Cambodia','KH','KHM',116,'2022-10-18 23:21:41'),(113,'Kiribati','KI','KIR',296,'2022-10-18 23:21:41'),(114,'Comoros','KM','COM',174,'2022-10-18 23:21:41'),(115,'Saint Kitts and Nevis','KN','KNA',659,'2022-10-18 23:21:41'),(116,'Korea, Democratic People\'s Republic of','KP','PRK',408,'2022-10-18 23:21:41'),(117,'Korea, Republic of','KR','KOR',410,'2022-10-18 23:21:41'),(118,'Kuwait','KW','KWT',414,'2022-10-18 23:21:41'),(119,'Cayman Islands','KY','CYM',136,'2022-10-18 23:21:41'),(120,'Kazakhstan','KZ','KAZ',398,'2022-10-18 23:21:41'),(121,'Lao People\'s Democratic Republic','LA','LAO',418,'2022-10-18 23:21:41'),(122,'Lebanon','LB','LBN',422,'2022-10-18 23:21:41'),(123,'Saint Lucia','LC','LCA',662,'2022-10-18 23:21:41'),(124,'Liechtenstein','LI','LIE',438,'2022-10-18 23:21:41'),(125,'Sri Lanka','LK','LKA',144,'2022-10-18 23:21:41'),(126,'Liberia','LR','LBR',430,'2022-10-18 23:21:41'),(127,'Lesotho','LS','LSO',426,'2022-10-18 23:21:41'),(128,'Lithuania','LT','LTU',440,'2022-10-18 23:21:41'),(129,'Luxembourg','LU','LUX',442,'2022-10-18 23:21:41'),(130,'Latvia','LV','LVA',428,'2022-10-18 23:21:41'),(131,'Libyan Arab Jamahiriya','LY','LBY',434,'2022-10-18 23:21:41'),(132,'Morocco','MA','MAR',504,'2022-10-18 23:21:41'),(133,'Monaco','MC','MCO',492,'2022-10-18 23:21:41'),(134,'Moldova, Republic of','MD','MDA',498,'2022-10-18 23:21:41'),(135,'Madagascar','MG','MDG',450,'2022-10-18 23:21:41'),(136,'Marshall Islands','MH','MHL',584,'2022-10-18 23:21:41'),(137,'Macedonia, the Former Yugoslav Republic of','MK','MKD',807,'2022-10-18 23:21:41'),(138,'Mali','ML','MLI',466,'2022-10-18 23:21:41'),(139,'Myanmar','MM','MMR',104,'2022-10-18 23:21:41'),(140,'Mongolia','MN','MNG',496,'2022-10-18 23:21:41'),(141,'Macao','MO','MAC',446,'2022-10-18 23:21:41'),(142,'Northern Mariana Islands','MP','MNP',580,'2022-10-18 23:21:41'),(143,'Martinique','MQ','MTQ',474,'2022-10-18 23:21:41'),(144,'Mauritania','MR','MRT',478,'2022-10-18 23:21:41'),(145,'Montserrat','MS','MSR',500,'2022-10-18 23:21:41'),(146,'Malta','MT','MLT',470,'2022-10-18 23:21:41'),(147,'Mauritius','MU','MUS',480,'2022-10-18 23:21:41'),(148,'Maldives','MV','MDV',462,'2022-10-18 23:21:41'),(149,'Malawi','MW','MWI',454,'2022-10-18 23:21:41'),(150,'Mexico','MX','MEX',484,'2022-10-18 23:21:41'),(151,'Malaysia','MY','MYS',458,'2022-10-18 23:21:41'),(152,'Mozambique','MZ','MOZ',508,'2022-10-18 23:21:41'),(153,'Namibia','NA','NAM',516,'2022-10-18 23:21:41'),(154,'New Caledonia','NC','NCL',540,'2022-10-18 23:21:41'),(155,'Niger','NE','NER',562,'2022-10-18 23:21:41'),(156,'Norfolk Island','NF','NFK',574,'2022-10-18 23:21:41'),(157,'Nigeria','NG','NGA',566,'2022-10-18 23:21:41'),(158,'Nicaragua','NI','NIC',558,'2022-10-18 23:21:41'),(159,'Netherlands','NL','NLD',528,'2022-10-18 23:21:41'),(160,'Norway','NO','NOR',578,'2022-10-18 23:21:41'),(161,'Nepal','NP','NPL',524,'2022-10-18 23:21:41'),(162,'Nauru','NR','NRU',520,'2022-10-18 23:21:41'),(163,'Niue','NU','NIU',570,'2022-10-18 23:21:41'),(164,'New Zealand','NZ','NZL',554,'2022-10-18 23:21:41'),(165,'Oman','OM','OMN',512,'2022-10-18 23:21:41'),(166,'Panama','PA','PAN',591,'2022-10-18 23:21:41'),(167,'Peru','PE','PER',604,'2022-10-18 23:21:41'),(168,'French Polynesia','PF','PYF',258,'2022-10-18 23:21:41'),(169,'Papua New Guinea','PG','PNG',598,'2022-10-18 23:21:41'),(170,'Philippines','PH','PHL',608,'2022-10-18 23:21:41'),(171,'Pakistan','PK','PAK',586,'2022-10-18 23:21:41'),(172,'Poland','PL','POL',616,'2022-10-18 23:21:41'),(173,'Saint Pierre and Miquelon','PM','SPM',666,'2022-10-18 23:21:41'),(174,'Pitcairn','PN','PCN',612,'2022-10-18 23:21:41'),(175,'Puerto Rico','PR','PRI',630,'2022-10-18 23:21:41'),(176,'Palestinian Territory, Occupied','PS',NULL,NULL,'2022-10-18 23:21:41'),(177,'Portugal','PT','PRT',620,'2022-10-18 23:21:41'),(178,'Palau','PW','PLW',585,'2022-10-18 23:21:41'),(179,'Paraguay','PY','PRY',600,'2022-10-18 23:21:41'),(180,'Qatar','QA','QAT',634,'2022-10-18 23:21:41'),(181,'Reunion','RE','REU',638,'2022-10-18 23:21:41'),(182,'Romania','RO','ROM',642,'2022-10-18 23:21:41'),(183,'Russian Federation','RU','RUS',643,'2022-10-18 23:21:41'),(184,'Rwanda','RW','RWA',646,'2022-10-18 23:21:41'),(185,'Saudi Arabia','SA','SAU',682,'2022-10-18 23:21:41'),(186,'Solomon Islands','SB','SLB',90,'2022-10-18 23:21:41'),(187,'Seychelles','SC','SYC',690,'2022-10-18 23:21:41'),(188,'Sudan','SD','SDN',736,'2022-10-18 23:21:41'),(189,'Sweden','SE','SWE',752,'2022-10-18 23:21:41'),(190,'Singapore','SG','SGP',702,'2022-10-18 23:21:41'),(191,'Saint Helena','SH','SHN',654,'2022-10-18 23:21:41'),(192,'Slovenia','SI','SVN',705,'2022-10-18 23:21:41'),(193,'Svalbard and Jan Mayen','SJ','SJM',744,'2022-10-18 23:21:41'),(194,'Slovakia','SK','SVK',703,'2022-10-18 23:21:41'),(195,'Sierra Leone','SL','SLE',694,'2022-10-18 23:21:41'),(196,'San Marino','SM','SMR',674,'2022-10-18 23:21:41'),(197,'Senegal','SN','SEN',686,'2022-10-18 23:21:41'),(198,'Somalia','SO','SOM',706,'2022-10-18 23:21:41'),(199,'Suriname','SR','SUR',740,'2022-10-18 23:21:41'),(200,'Sao Tome and Principe','ST','STP',678,'2022-10-18 23:21:41'),(201,'El Salvador','SV','SLV',222,'2022-10-18 23:21:41'),(202,'Syrian Arab Republic','SY','SYR',760,'2022-10-18 23:21:41'),(203,'Swaziland','SZ','SWZ',748,'2022-10-18 23:21:41'),(204,'Turks and Caicos Islands','TC','TCA',796,'2022-10-18 23:21:41'),(205,'Chad','TD','TCD',148,'2022-10-18 23:21:41'),(206,'French Southern Territories','TF',NULL,NULL,'2022-10-18 23:21:41'),(207,'Togo','TG','TGO',768,'2022-10-18 23:21:41'),(208,'Thailand','TH','THA',764,'2022-10-18 23:21:41'),(209,'Tajikistan','TJ','TJK',762,'2022-10-18 23:21:41'),(210,'Tokelau','TK','TKL',772,'2022-10-18 23:21:41'),(211,'Timor-Leste','TL',NULL,NULL,'2022-10-18 23:21:41'),(212,'Turkmenistan','TM','TKM',795,'2022-10-18 23:21:41'),(213,'Tunisia','TN','TUN',788,'2022-10-18 23:21:41'),(214,'Tonga','TO','TON',776,'2022-10-18 23:21:41'),(215,'Turkey','TR','TUR',792,'2022-10-18 23:21:41'),(216,'Trinidad and Tobago','TT','TTO',780,'2022-10-18 23:21:41'),(217,'Tuvalu','TV','TUV',798,'2022-10-18 23:21:41'),(218,'Taiwan, Province of China','TW','TWN',158,'2022-10-18 23:21:41'),(219,'Tanzania, United Republic of','TZ','TZA',834,'2022-10-18 23:21:41'),(220,'Ukraine','UA','UKR',804,'2022-10-18 23:21:41'),(221,'Uganda','UG','UGA',800,'2022-10-18 23:21:41'),(222,'United States Minor Outlying Islands','UM',NULL,NULL,'2022-10-18 23:21:41'),(223,'United States','US','USA',840,'2022-10-18 23:21:41'),(224,'Uruguay','UY','URY',858,'2022-10-18 23:21:41'),(225,'Uzbekistan','UZ','UZB',860,'2022-10-18 23:21:41'),(226,'Holy See (Vatican City State)','VA','VAT',336,'2022-10-18 23:21:41'),(227,'Saint Vincent and the Grenadines','VC','VCT',670,'2022-10-18 23:21:41'),(228,'Venezuela','VE','VEN',862,'2022-10-18 23:21:41'),(229,'Virgin Islands, British','VG','VGB',92,'2022-10-18 23:21:41'),(230,'Virgin Islands,  U.s.','VI','VIR',850,'2022-10-18 23:21:41'),(231,'Viet Nam','VN','VNM',704,'2022-10-18 23:21:41'),(232,'Vanuatu','VU','VUT',548,'2022-10-18 23:21:41'),(233,'Wallis and Futuna','WF','WLF',876,'2022-10-18 23:21:41'),(234,'Samoa','WS','WSM',882,'2022-10-18 23:21:41'),(235,'Yemen','YE','YEM',887,'2022-10-18 23:21:41'),(236,'Mayotte','YT',NULL,NULL,'2022-10-18 23:21:41'),(237,'South Africa','ZA','ZAF',710,'2022-10-18 23:21:41'),(238,'Zambia','ZM','ZMB',894,'2022-10-18 23:21:41'),(239,'Zimbabwe','ZW','ZWE',716,'2022-10-18 23:21:41'),(256,'USA','US','USA',840,'2022-10-18 23:21:41'),(262,'Russia',NULL,NULL,NULL,'2022-10-18 23:21:41'),(263,'Canary Islands','IC',NULL,NULL,'2022-10-18 23:21:41'),(264,'Brasil','BR','BRA',76,'2022-10-18 23:21:41');
/*!40000 ALTER TABLE `lkupcountry` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lkupcounty`
--

DROP TABLE IF EXISTS `lkupcounty`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lkupcounty` (
  `countyId` int(11) NOT NULL AUTO_INCREMENT,
  `stateId` int(11) NOT NULL,
  `countyName` varchar(100) NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`countyId`),
  UNIQUE KEY `unique_county` (`stateId`,`countyName`),
  KEY `fk_stateprovince` (`stateId`),
  KEY `index_countyname` (`countyName`),
  CONSTRAINT `fk_stateprovince` FOREIGN KEY (`stateId`) REFERENCES `lkupstateprovince` (`stateId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5804 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lkupcounty`
--

LOCK TABLES `lkupcounty` WRITE;
/*!40000 ALTER TABLE `lkupcounty` DISABLE KEYS */;
INSERT INTO `lkupcounty` VALUES (1,164,'Suffolk','2022-10-18 23:21:41'),(2,173,'Adjuntas','2022-10-18 23:21:41'),(3,173,'Aguada','2022-10-18 23:21:41'),(4,173,'Aguadilla','2022-10-18 23:21:41'),(5,155,'Mower','2022-10-18 23:21:41'),(6,172,'Susquehanna','2022-10-18 23:21:41'),(7,158,'Glacier','2022-10-18 23:21:41'),(8,179,'Garfield','2022-10-18 23:21:41'),(9,173,'Maricao','2022-10-18 23:21:41'),(10,173,'Anasco','2022-10-18 23:21:41'),(11,173,'Utuado','2022-10-18 23:21:41'),(12,173,'Arecibo','2022-10-18 23:21:41'),(13,173,'Barceloneta','2022-10-18 23:21:41'),(14,173,'Cabo Rojo','2022-10-18 23:21:41'),(15,173,'Penuelas','2022-10-18 23:21:41'),(16,173,'Camuy','2022-10-18 23:21:41'),(17,173,'Lares','2022-10-18 23:21:41'),(18,173,'San German','2022-10-18 23:21:41'),(19,173,'Sabana Grande','2022-10-18 23:21:41'),(20,173,'Ciales','2022-10-18 23:21:41'),(21,173,'Dorado','2022-10-18 23:21:41'),(22,173,'Guanica','2022-10-18 23:21:41'),(23,173,'Florida','2022-10-18 23:21:41'),(24,173,'Guayanilla','2022-10-18 23:21:41'),(25,173,'Hatillo','2022-10-18 23:21:41'),(26,173,'Hormigueros','2022-10-18 23:21:41'),(27,173,'Isabela','2022-10-18 23:21:41'),(28,173,'Jayuya','2022-10-18 23:21:41'),(29,173,'Lajas','2022-10-18 23:21:41'),(30,173,'Las Marias','2022-10-18 23:21:41'),(31,173,'Manati','2022-10-18 23:21:41'),(32,173,'Moca','2022-10-18 23:21:41'),(33,173,'Rincon','2022-10-18 23:21:41'),(34,173,'Quebradillas','2022-10-18 23:21:41'),(35,173,'Mayaguez','2022-10-18 23:21:41'),(36,173,'San Sebastian','2022-10-18 23:21:41'),(37,173,'Morovis','2022-10-18 23:21:41'),(38,173,'Vega Alta','2022-10-18 23:21:41'),(39,173,'Vega Baja','2022-10-18 23:21:41'),(40,173,'Yauco','2022-10-18 23:21:41'),(41,173,'Aguas Buenas','2022-10-18 23:21:41'),(42,173,'Guayama','2022-10-18 23:21:41'),(43,173,'Aibonito','2022-10-18 23:21:41'),(44,173,'Maunabo','2022-10-18 23:21:41'),(45,173,'Arroyo','2022-10-18 23:21:41'),(46,173,'Ponce','2022-10-18 23:21:41'),(47,173,'Naguabo','2022-10-18 23:21:41'),(48,173,'Naranjito','2022-10-18 23:21:41'),(49,173,'Orocovis','2022-10-18 23:21:41'),(50,173,'Rio Grande','2022-10-18 23:21:41'),(51,173,'Patillas','2022-10-18 23:21:41'),(52,173,'Caguas','2022-10-18 23:21:41'),(53,173,'Canovanas','2022-10-18 23:21:41'),(54,173,'Ceiba','2022-10-18 23:21:41'),(55,173,'Cayey','2022-10-18 23:21:41'),(56,173,'Fajardo','2022-10-18 23:21:41'),(57,173,'Cidra','2022-10-18 23:21:41'),(58,173,'Humacao','2022-10-18 23:21:41'),(59,173,'Salinas','2022-10-18 23:21:41'),(60,173,'San Lorenzo','2022-10-18 23:21:41'),(61,173,'Santa Isabel','2022-10-18 23:21:41'),(62,173,'Vieques','2022-10-18 23:21:41'),(63,173,'Villalba','2022-10-18 23:21:41'),(64,173,'Yabucoa','2022-10-18 23:21:41'),(65,173,'Coamo','2022-10-18 23:21:41'),(66,173,'Las Piedras','2022-10-18 23:21:41'),(67,173,'Loiza','2022-10-18 23:21:41'),(68,173,'Luquillo','2022-10-18 23:21:41'),(69,173,'Culebra','2022-10-18 23:21:41'),(70,173,'Juncos','2022-10-18 23:21:41'),(71,173,'Gurabo','2022-10-18 23:21:41'),(72,173,'Comerio','2022-10-18 23:21:41'),(73,173,'Corozal','2022-10-18 23:21:41'),(74,173,'Barranquitas','2022-10-18 23:21:41'),(75,173,'Juana Diaz','2022-10-18 23:21:41'),(76,181,'Saint Thomas','2022-10-18 23:21:41'),(77,181,'Saint Croix','2022-10-18 23:21:41'),(78,181,'Saint John','2022-10-18 23:21:41'),(79,173,'San Juan','2022-10-18 23:21:41'),(80,173,'Bayamon','2022-10-18 23:21:41'),(81,173,'Toa Baja','2022-10-18 23:21:41'),(82,173,'Toa Alta','2022-10-18 23:21:41'),(83,173,'Catano','2022-10-18 23:21:41'),(84,173,'Guaynabo','2022-10-18 23:21:41'),(85,173,'Trujillo Alto','2022-10-18 23:21:41'),(86,173,'Carolina','2022-10-18 23:21:41'),(87,153,'Hampden','2022-10-18 23:21:41'),(88,153,'Hampshire','2022-10-18 23:21:41'),(89,153,'Worcester','2022-10-18 23:21:41'),(90,153,'Berkshire','2022-10-18 23:21:41'),(91,153,'Franklin','2022-10-18 23:21:41'),(92,153,'Middlesex','2022-10-18 23:21:41'),(93,153,'Essex','2022-10-18 23:21:41'),(94,153,'Plymouth','2022-10-18 23:21:41'),(95,153,'Norfolk','2022-10-18 23:21:41'),(96,153,'Bristol','2022-10-18 23:21:41'),(97,153,'Suffolk','2022-10-18 23:21:41'),(98,153,'Barnstable','2022-10-18 23:21:41'),(99,153,'Dukes','2022-10-18 23:21:41'),(100,153,'Nantucket','2022-10-18 23:21:41'),(101,174,'Newport','2022-10-18 23:21:41'),(102,174,'Providence','2022-10-18 23:21:41'),(103,174,'Washington','2022-10-18 23:21:41'),(104,174,'Bristol','2022-10-18 23:21:41'),(105,174,'Kent','2022-10-18 23:21:41'),(106,161,'Hillsborough','2022-10-18 23:21:41'),(107,161,'Rockingham','2022-10-18 23:21:41'),(108,161,'Merrimack','2022-10-18 23:21:41'),(109,161,'Grafton','2022-10-18 23:21:41'),(110,161,'Belknap','2022-10-18 23:21:41'),(111,161,'Carroll','2022-10-18 23:21:41'),(112,161,'Sullivan','2022-10-18 23:21:41'),(113,161,'Cheshire','2022-10-18 23:21:41'),(114,161,'Coos','2022-10-18 23:21:41'),(115,161,'Strafford','2022-10-18 23:21:41'),(116,150,'York','2022-10-18 23:21:41'),(117,150,'Cumberland','2022-10-18 23:21:41'),(118,150,'Sagadahoc','2022-10-18 23:21:41'),(119,150,'Oxford','2022-10-18 23:21:41'),(120,150,'Androscoggin','2022-10-18 23:21:41'),(121,150,'Franklin','2022-10-18 23:21:41'),(122,150,'Kennebec','2022-10-18 23:21:41'),(123,150,'Lincoln','2022-10-18 23:21:41'),(124,150,'Waldo','2022-10-18 23:21:41'),(125,150,'Penobscot','2022-10-18 23:21:41'),(126,150,'Piscataquis','2022-10-18 23:21:41'),(127,150,'Hancock','2022-10-18 23:21:41'),(128,150,'Washington','2022-10-18 23:21:41'),(129,150,'Aroostook','2022-10-18 23:21:41'),(130,150,'Somerset','2022-10-18 23:21:41'),(132,150,'Knox','2022-10-18 23:21:41'),(133,180,'Windsor','2022-10-18 23:21:41'),(134,180,'Orange','2022-10-18 23:21:41'),(135,180,'Caledonia','2022-10-18 23:21:41'),(136,180,'Windham','2022-10-18 23:21:41'),(137,180,'Bennington','2022-10-18 23:21:41'),(138,180,'Chittenden','2022-10-18 23:21:41'),(139,180,'Grand Isle','2022-10-18 23:21:41'),(140,180,'Franklin','2022-10-18 23:21:41'),(141,180,'Lamoille','2022-10-18 23:21:41'),(142,180,'Addison','2022-10-18 23:21:41'),(143,180,'Washington','2022-10-18 23:21:41'),(144,180,'Rutland','2022-10-18 23:21:41'),(145,180,'Orleans','2022-10-18 23:21:41'),(146,180,'Essex','2022-10-18 23:21:41'),(147,135,'Hartford','2022-10-18 23:21:41'),(148,135,'Litchfield','2022-10-18 23:21:41'),(149,135,'Tolland','2022-10-18 23:21:41'),(150,135,'Windham','2022-10-18 23:21:41'),(151,135,'New London','2022-10-18 23:21:41'),(152,135,'New Haven','2022-10-18 23:21:41'),(153,135,'Fairfield','2022-10-18 23:21:41'),(154,135,'Middlesex','2022-10-18 23:21:41'),(155,162,'Middlesex','2022-10-18 23:21:41'),(156,162,'Hudson','2022-10-18 23:21:41'),(157,162,'Essex','2022-10-18 23:21:41'),(158,162,'Morris','2022-10-18 23:21:41'),(159,162,'Bergen','2022-10-18 23:21:41'),(160,162,'Passaic','2022-10-18 23:21:41'),(161,162,'Union','2022-10-18 23:21:41'),(162,162,'Somerset','2022-10-18 23:21:41'),(163,162,'Sussex','2022-10-18 23:21:41'),(164,162,'Monmouth','2022-10-18 23:21:41'),(165,162,'Warren','2022-10-18 23:21:41'),(166,162,'Hunterdon','2022-10-18 23:21:41'),(167,162,'Salem','2022-10-18 23:21:41'),(168,162,'Camden','2022-10-18 23:21:41'),(169,162,'Ocean','2022-10-18 23:21:41'),(170,162,'Burlington','2022-10-18 23:21:41'),(171,162,'Gloucester','2022-10-18 23:21:41'),(172,162,'Atlantic','2022-10-18 23:21:41'),(173,162,'Cape May','2022-10-18 23:21:41'),(174,162,'Cumberland','2022-10-18 23:21:41'),(175,162,'Mercer','2022-10-18 23:21:41'),(176,164,'New York','2022-10-18 23:21:41'),(177,164,'Richmond','2022-10-18 23:21:41'),(178,164,'Bronx','2022-10-18 23:21:41'),(179,164,'Westchester','2022-10-18 23:21:41'),(180,164,'Putnam','2022-10-18 23:21:41'),(181,164,'Rockland','2022-10-18 23:21:41'),(182,164,'Orange','2022-10-18 23:21:41'),(183,164,'Nassau','2022-10-18 23:21:41'),(184,164,'Queens','2022-10-18 23:21:41'),(185,164,'Kings','2022-10-18 23:21:41'),(186,164,'Albany','2022-10-18 23:21:41'),(187,164,'Schenectady','2022-10-18 23:21:41'),(188,164,'Montgomery','2022-10-18 23:21:41'),(189,164,'Greene','2022-10-18 23:21:41'),(190,164,'Columbia','2022-10-18 23:21:41'),(191,164,'Rensselaer','2022-10-18 23:21:41'),(192,164,'Saratoga','2022-10-18 23:21:41'),(193,164,'Fulton','2022-10-18 23:21:41'),(194,164,'Schoharie','2022-10-18 23:21:41'),(195,164,'Washington','2022-10-18 23:21:41'),(196,164,'Otsego','2022-10-18 23:21:41'),(197,164,'Hamilton','2022-10-18 23:21:41'),(198,164,'Delaware','2022-10-18 23:21:41'),(199,164,'Ulster','2022-10-18 23:21:41'),(200,164,'Dutchess','2022-10-18 23:21:41'),(201,164,'Sullivan','2022-10-18 23:21:41'),(202,164,'Warren','2022-10-18 23:21:41'),(203,164,'Essex','2022-10-18 23:21:41'),(204,164,'Clinton','2022-10-18 23:21:41'),(205,164,'Franklin','2022-10-18 23:21:41'),(206,164,'Saint Lawrence','2022-10-18 23:21:41'),(207,164,'Onondaga','2022-10-18 23:21:41'),(208,164,'Cayuga','2022-10-18 23:21:41'),(209,164,'Oswego','2022-10-18 23:21:41'),(210,164,'Madison','2022-10-18 23:21:41'),(211,164,'Cortland','2022-10-18 23:21:41'),(212,164,'Tompkins','2022-10-18 23:21:41'),(213,164,'Oneida','2022-10-18 23:21:41'),(214,164,'Seneca','2022-10-18 23:21:41'),(215,164,'Chenango','2022-10-18 23:21:41'),(216,164,'Wayne','2022-10-18 23:21:41'),(217,164,'Lewis','2022-10-18 23:21:41'),(218,164,'Herkimer','2022-10-18 23:21:41'),(219,164,'Jefferson','2022-10-18 23:21:41'),(220,164,'Tioga','2022-10-18 23:21:41'),(221,164,'Broome','2022-10-18 23:21:41'),(222,164,'Erie','2022-10-18 23:21:41'),(223,164,'Genesee','2022-10-18 23:21:41'),(224,164,'Niagara','2022-10-18 23:21:41'),(225,164,'Wyoming','2022-10-18 23:21:41'),(226,164,'Allegany','2022-10-18 23:21:41'),(227,164,'Cattaraugus','2022-10-18 23:21:41'),(228,164,'Chautauqua','2022-10-18 23:21:41'),(229,164,'Orleans','2022-10-18 23:21:41'),(230,164,'Monroe','2022-10-18 23:21:41'),(231,164,'Livingston','2022-10-18 23:21:41'),(232,164,'Yates','2022-10-18 23:21:41'),(233,164,'Ontario','2022-10-18 23:21:41'),(234,164,'Steuben','2022-10-18 23:21:41'),(235,164,'Schuyler','2022-10-18 23:21:41'),(236,164,'Chemung','2022-10-18 23:21:41'),(237,172,'Beaver','2022-10-18 23:21:41'),(238,172,'Washington','2022-10-18 23:21:41'),(239,172,'Allegheny','2022-10-18 23:21:41'),(240,172,'Fayette','2022-10-18 23:21:41'),(241,172,'Westmoreland','2022-10-18 23:21:41'),(242,172,'Greene','2022-10-18 23:21:41'),(243,172,'Somerset','2022-10-18 23:21:41'),(244,172,'Bedford','2022-10-18 23:21:41'),(245,172,'Fulton','2022-10-18 23:21:41'),(246,172,'Armstrong','2022-10-18 23:21:41'),(247,172,'Indiana','2022-10-18 23:21:41'),(248,172,'Jefferson','2022-10-18 23:21:41'),(249,172,'Cambria','2022-10-18 23:21:41'),(250,172,'Clearfield','2022-10-18 23:21:41'),(251,172,'Elk','2022-10-18 23:21:41'),(252,172,'Forest','2022-10-18 23:21:41'),(253,172,'Cameron','2022-10-18 23:21:41'),(254,172,'Butler','2022-10-18 23:21:41'),(255,172,'Clarion','2022-10-18 23:21:41'),(256,172,'Lawrence','2022-10-18 23:21:41'),(257,172,'Crawford','2022-10-18 23:21:41'),(258,172,'Mercer','2022-10-18 23:21:41'),(259,172,'Venango','2022-10-18 23:21:41'),(260,172,'Warren','2022-10-18 23:21:41'),(261,172,'McKean','2022-10-18 23:21:41'),(262,172,'Erie','2022-10-18 23:21:41'),(263,172,'Blair','2022-10-18 23:21:41'),(264,172,'Huntingdon','2022-10-18 23:21:41'),(265,172,'Centre','2022-10-18 23:21:41'),(266,172,'Potter','2022-10-18 23:21:41'),(267,172,'Clinton','2022-10-18 23:21:41'),(268,172,'Tioga','2022-10-18 23:21:41'),(269,172,'Bradford','2022-10-18 23:21:41'),(270,172,'Cumberland','2022-10-18 23:21:41'),(271,172,'Mifflin','2022-10-18 23:21:41'),(272,172,'Lebanon','2022-10-18 23:21:41'),(273,172,'Dauphin','2022-10-18 23:21:41'),(274,172,'Perry','2022-10-18 23:21:41'),(275,172,'Juniata','2022-10-18 23:21:41'),(276,172,'Northumberland','2022-10-18 23:21:41'),(277,172,'York','2022-10-18 23:21:41'),(278,172,'Lancaster','2022-10-18 23:21:41'),(279,172,'Franklin','2022-10-18 23:21:41'),(280,172,'Adams','2022-10-18 23:21:41'),(281,172,'Lycoming','2022-10-18 23:21:41'),(282,172,'Sullivan','2022-10-18 23:21:41'),(283,172,'Union','2022-10-18 23:21:41'),(284,172,'Snyder','2022-10-18 23:21:41'),(285,172,'Columbia','2022-10-18 23:21:41'),(286,172,'Montour','2022-10-18 23:21:41'),(287,172,'Schuylkill','2022-10-18 23:21:41'),(288,172,'Northampton','2022-10-18 23:21:41'),(289,172,'Lehigh','2022-10-18 23:21:41'),(290,172,'Carbon','2022-10-18 23:21:41'),(291,172,'Bucks','2022-10-18 23:21:41'),(292,172,'Montgomery','2022-10-18 23:21:41'),(293,172,'Berks','2022-10-18 23:21:41'),(294,172,'Monroe','2022-10-18 23:21:41'),(295,172,'Luzerne','2022-10-18 23:21:41'),(296,172,'Pike','2022-10-18 23:21:41'),(297,172,'Lackawanna','2022-10-18 23:21:41'),(298,172,'Wayne','2022-10-18 23:21:41'),(299,172,'Wyoming','2022-10-18 23:21:41'),(300,172,'Delaware','2022-10-18 23:21:41'),(301,172,'Philadelphia','2022-10-18 23:21:41'),(302,172,'Chester','2022-10-18 23:21:41'),(303,136,'New Castle','2022-10-18 23:21:41'),(304,136,'Kent','2022-10-18 23:21:41'),(305,136,'Sussex','2022-10-18 23:21:41'),(306,137,'District of Columbia','2022-10-18 23:21:41'),(307,182,'Loudoun','2022-10-18 23:21:41'),(308,182,'Rappahannock','2022-10-18 23:21:41'),(309,182,'Manassas City','2022-10-18 23:21:41'),(310,182,'Manassas Park City','2022-10-18 23:21:41'),(311,182,'Fauquier','2022-10-18 23:21:41'),(312,182,'Fairfax','2022-10-18 23:21:41'),(313,182,'Prince William','2022-10-18 23:21:41'),(314,152,'Charles','2022-10-18 23:21:41'),(315,152,'Saint Marys','2022-10-18 23:21:41'),(316,152,'Prince Georges','2022-10-18 23:21:41'),(317,152,'Calvert','2022-10-18 23:21:41'),(318,152,'Howard','2022-10-18 23:21:41'),(319,152,'Anne Arundel','2022-10-18 23:21:41'),(320,152,'Montgomery','2022-10-18 23:21:41'),(321,152,'Harford','2022-10-18 23:21:41'),(322,152,'Baltimore','2022-10-18 23:21:41'),(323,152,'Carroll','2022-10-18 23:21:41'),(324,152,'Baltimore City','2022-10-18 23:21:41'),(325,152,'Allegany','2022-10-18 23:21:41'),(326,152,'Garrett','2022-10-18 23:21:41'),(327,152,'Talbot','2022-10-18 23:21:41'),(328,152,'Queen Annes','2022-10-18 23:21:41'),(329,152,'Caroline','2022-10-18 23:21:41'),(330,152,'Kent','2022-10-18 23:21:41'),(331,152,'Dorchester','2022-10-18 23:21:41'),(332,152,'Frederick','2022-10-18 23:21:41'),(333,152,'Washington','2022-10-18 23:21:41'),(334,152,'Wicomico','2022-10-18 23:21:41'),(335,152,'Worcester','2022-10-18 23:21:41'),(336,152,'Somerset','2022-10-18 23:21:41'),(337,152,'Cecil','2022-10-18 23:21:41'),(338,182,'Fairfax City','2022-10-18 23:21:41'),(339,182,'Falls Church City','2022-10-18 23:21:41'),(340,182,'Arlington','2022-10-18 23:21:41'),(341,182,'Alexandria City','2022-10-18 23:21:41'),(342,182,'Fredericksburg City','2022-10-18 23:21:41'),(343,182,'Stafford','2022-10-18 23:21:41'),(344,182,'Spotsylvania','2022-10-18 23:21:41'),(345,182,'Caroline','2022-10-18 23:21:41'),(346,182,'Northumberland','2022-10-18 23:21:41'),(347,182,'Orange','2022-10-18 23:21:41'),(348,182,'Essex','2022-10-18 23:21:41'),(349,182,'Westmoreland','2022-10-18 23:21:41'),(350,182,'King George','2022-10-18 23:21:41'),(351,182,'Richmond','2022-10-18 23:21:41'),(352,182,'Lancaster','2022-10-18 23:21:41'),(353,182,'Winchester City','2022-10-18 23:21:41'),(354,182,'Frederick','2022-10-18 23:21:41'),(355,182,'Warren','2022-10-18 23:21:41'),(356,182,'Clarke','2022-10-18 23:21:41'),(357,182,'Shenandoah','2022-10-18 23:21:41'),(358,182,'Page','2022-10-18 23:21:41'),(359,182,'Culpeper','2022-10-18 23:21:41'),(360,182,'Madison','2022-10-18 23:21:41'),(361,182,'Harrisonburg City','2022-10-18 23:21:41'),(362,182,'Rockingham','2022-10-18 23:21:41'),(363,182,'Augusta','2022-10-18 23:21:41'),(364,182,'Albemarle','2022-10-18 23:21:41'),(365,182,'Charlottesville City','2022-10-18 23:21:41'),(366,182,'Nelson','2022-10-18 23:21:41'),(367,182,'Greene','2022-10-18 23:21:41'),(368,182,'Fluvanna','2022-10-18 23:21:41'),(369,182,'Waynesboro City','2022-10-18 23:21:41'),(370,182,'Gloucester','2022-10-18 23:21:41'),(371,182,'Amelia','2022-10-18 23:21:41'),(372,182,'Buckingham','2022-10-18 23:21:41'),(373,182,'Hanover','2022-10-18 23:21:41'),(374,182,'King William','2022-10-18 23:21:41'),(375,182,'New Kent','2022-10-18 23:21:41'),(376,182,'Goochland','2022-10-18 23:21:41'),(377,182,'Mathews','2022-10-18 23:21:41'),(378,182,'King And Queen','2022-10-18 23:21:41'),(379,182,'Louisa','2022-10-18 23:21:41'),(380,182,'Cumberland','2022-10-18 23:21:41'),(381,182,'Charles City','2022-10-18 23:21:41'),(382,182,'Middlesex','2022-10-18 23:21:41'),(383,182,'Henrico','2022-10-18 23:21:41'),(384,182,'James City','2022-10-18 23:21:41'),(385,182,'York','2022-10-18 23:21:41'),(386,182,'Powhatan','2022-10-18 23:21:41'),(387,182,'Chesterfield','2022-10-18 23:21:41'),(388,182,'Richmond City','2022-10-18 23:21:41'),(389,182,'Williamsburg City','2022-10-18 23:21:41'),(390,182,'Accomack','2022-10-18 23:21:41'),(391,182,'Isle of Wight','2022-10-18 23:21:41'),(392,182,'Northampton','2022-10-18 23:21:41'),(393,182,'Chesapeake City','2022-10-18 23:21:41'),(394,182,'Suffolk City','2022-10-18 23:21:41'),(395,182,'Virginia Beach City','2022-10-18 23:21:41'),(396,182,'Norfolk City','2022-10-18 23:21:41'),(397,182,'Newport News City','2022-10-18 23:21:41'),(398,182,'Hampton City','2022-10-18 23:21:41'),(399,182,'Poquoson City','2022-10-18 23:21:41'),(400,182,'Portsmouth City','2022-10-18 23:21:41'),(401,182,'Prince George','2022-10-18 23:21:41'),(402,182,'Petersburg City','2022-10-18 23:21:41'),(403,182,'Brunswick','2022-10-18 23:21:41'),(404,182,'Dinwiddie','2022-10-18 23:21:41'),(405,182,'Nottoway','2022-10-18 23:21:41'),(406,182,'Southampton','2022-10-18 23:21:41'),(407,182,'Colonial Heights City','2022-10-18 23:21:41'),(408,182,'Surry','2022-10-18 23:21:41'),(409,182,'Emporia City','2022-10-18 23:21:41'),(410,182,'Franklin City','2022-10-18 23:21:41'),(411,182,'Hopewell City','2022-10-18 23:21:41'),(412,182,'Sussex','2022-10-18 23:21:41'),(413,182,'Greensville','2022-10-18 23:21:41'),(414,182,'Prince Edward','2022-10-18 23:21:41'),(415,182,'Mecklenburg','2022-10-18 23:21:41'),(416,182,'Charlotte','2022-10-18 23:21:41'),(417,182,'Lunenburg','2022-10-18 23:21:41'),(418,182,'Appomattox','2022-10-18 23:21:41'),(419,182,'Roanoke City','2022-10-18 23:21:41'),(420,182,'Roanoke','2022-10-18 23:21:41'),(421,182,'Botetourt','2022-10-18 23:21:41'),(422,182,'Montgomery','2022-10-18 23:21:41'),(423,182,'Patrick','2022-10-18 23:21:41'),(424,182,'Henry','2022-10-18 23:21:41'),(425,182,'Pulaski','2022-10-18 23:21:41'),(426,182,'Franklin','2022-10-18 23:21:41'),(427,182,'Pittsylvania','2022-10-18 23:21:41'),(428,182,'Floyd','2022-10-18 23:21:41'),(429,182,'Giles','2022-10-18 23:21:41'),(430,182,'Bedford','2022-10-18 23:21:41'),(431,182,'Martinsville City','2022-10-18 23:21:41'),(432,182,'Craig','2022-10-18 23:21:41'),(433,182,'Salem','2022-10-18 23:21:41'),(434,182,'Bristol','2022-10-18 23:21:41'),(435,182,'Washington','2022-10-18 23:21:41'),(436,182,'Wise','2022-10-18 23:21:41'),(437,182,'Dickenson','2022-10-18 23:21:41'),(438,182,'Lee','2022-10-18 23:21:41'),(439,182,'Russell','2022-10-18 23:21:41'),(440,182,'Buchanan','2022-10-18 23:21:41'),(441,182,'Scott','2022-10-18 23:21:41'),(442,182,'Norton City','2022-10-18 23:21:41'),(443,182,'Grayson','2022-10-18 23:21:41'),(444,182,'Smyth','2022-10-18 23:21:41'),(445,182,'Wythe','2022-10-18 23:21:41'),(446,182,'Bland','2022-10-18 23:21:41'),(447,182,'Carroll','2022-10-18 23:21:41'),(448,182,'Galax City','2022-10-18 23:21:41'),(449,182,'Tazewell','2022-10-18 23:21:41'),(450,182,'Staunton City','2022-10-18 23:21:41'),(451,182,'Bath','2022-10-18 23:21:41'),(452,182,'Highland','2022-10-18 23:21:41'),(453,182,'Rockbridge','2022-10-18 23:21:41'),(454,182,'Buena Vista City','2022-10-18 23:21:41'),(455,182,'Clifton Forge City','2022-10-18 23:21:41'),(456,182,'Covington City','2022-10-18 23:21:41'),(457,182,'Alleghany','2022-10-18 23:21:41'),(458,182,'Lexington City','2022-10-18 23:21:41'),(459,182,'Lynchburg City','2022-10-18 23:21:41'),(460,182,'Campbell','2022-10-18 23:21:41'),(461,182,'Halifax','2022-10-18 23:21:41'),(462,182,'Amherst','2022-10-18 23:21:41'),(463,182,'Bedford City','2022-10-18 23:21:41'),(464,182,'Danville City','2022-10-18 23:21:41'),(465,184,'Mercer','2022-10-18 23:21:41'),(466,184,'Wyoming','2022-10-18 23:21:41'),(467,184,'McDowell','2022-10-18 23:21:41'),(468,184,'Mingo','2022-10-18 23:21:41'),(469,184,'Greenbrier','2022-10-18 23:21:41'),(470,184,'Pocahontas','2022-10-18 23:21:41'),(471,184,'Monroe','2022-10-18 23:21:41'),(472,184,'Summers','2022-10-18 23:21:41'),(473,184,'Fayette','2022-10-18 23:21:41'),(474,184,'Kanawha','2022-10-18 23:21:41'),(475,184,'Roane','2022-10-18 23:21:41'),(476,184,'Raleigh','2022-10-18 23:21:41'),(477,184,'Boone','2022-10-18 23:21:41'),(478,184,'Putnam','2022-10-18 23:21:41'),(479,184,'Clay','2022-10-18 23:21:41'),(480,184,'Logan','2022-10-18 23:21:41'),(481,184,'Nicholas','2022-10-18 23:21:41'),(482,184,'Mason','2022-10-18 23:21:41'),(483,184,'Jackson','2022-10-18 23:21:41'),(484,184,'Calhoun','2022-10-18 23:21:41'),(485,184,'Gilmer','2022-10-18 23:21:41'),(486,184,'Berkeley','2022-10-18 23:21:41'),(487,184,'Jefferson','2022-10-18 23:21:41'),(488,184,'Morgan','2022-10-18 23:21:41'),(489,184,'Hampshire','2022-10-18 23:21:41'),(490,184,'Lincoln','2022-10-18 23:21:41'),(491,184,'Cabell','2022-10-18 23:21:41'),(492,184,'Wayne','2022-10-18 23:21:41'),(493,184,'Ohio','2022-10-18 23:21:41'),(494,184,'Brooke','2022-10-18 23:21:41'),(495,184,'Marshall','2022-10-18 23:21:41'),(496,184,'Hancock','2022-10-18 23:21:41'),(497,184,'Wood','2022-10-18 23:21:41'),(498,184,'Pleasants','2022-10-18 23:21:41'),(499,184,'Wirt','2022-10-18 23:21:41'),(500,184,'Tyler','2022-10-18 23:21:41'),(501,184,'Ritchie','2022-10-18 23:21:41'),(502,184,'Wetzel','2022-10-18 23:21:41'),(503,184,'Upshur','2022-10-18 23:21:41'),(504,184,'Webster','2022-10-18 23:21:41'),(505,184,'Randolph','2022-10-18 23:21:41'),(506,184,'Barbour','2022-10-18 23:21:41'),(507,184,'Tucker','2022-10-18 23:21:41'),(508,184,'Harrison','2022-10-18 23:21:41'),(509,184,'Lewis','2022-10-18 23:21:41'),(510,184,'Braxton','2022-10-18 23:21:41'),(511,184,'Doddridge','2022-10-18 23:21:41'),(512,184,'Taylor','2022-10-18 23:21:41'),(513,184,'Preston','2022-10-18 23:21:41'),(514,184,'Monongalia','2022-10-18 23:21:41'),(515,184,'Marion','2022-10-18 23:21:41'),(516,184,'Grant','2022-10-18 23:21:41'),(517,184,'Mineral','2022-10-18 23:21:41'),(518,184,'Hardy','2022-10-18 23:21:41'),(519,184,'Pendleton','2022-10-18 23:21:41'),(520,165,'Davie','2022-10-18 23:21:41'),(521,165,'Surry','2022-10-18 23:21:41'),(522,165,'Forsyth','2022-10-18 23:21:41'),(523,165,'Yadkin','2022-10-18 23:21:41'),(524,165,'Rowan','2022-10-18 23:21:41'),(525,165,'Stokes','2022-10-18 23:21:41'),(526,165,'Rockingham','2022-10-18 23:21:41'),(527,165,'Alamance','2022-10-18 23:21:41'),(528,165,'Randolph','2022-10-18 23:21:41'),(529,165,'Chatham','2022-10-18 23:21:41'),(530,165,'Montgomery','2022-10-18 23:21:41'),(531,165,'Caswell','2022-10-18 23:21:41'),(532,165,'Guilford','2022-10-18 23:21:41'),(533,165,'Orange','2022-10-18 23:21:41'),(534,165,'Lee','2022-10-18 23:21:41'),(535,165,'Davidson','2022-10-18 23:21:41'),(536,165,'Moore','2022-10-18 23:21:41'),(537,165,'Person','2022-10-18 23:21:41'),(538,165,'Harnett','2022-10-18 23:21:41'),(539,165,'Wake','2022-10-18 23:21:41'),(540,165,'Durham','2022-10-18 23:21:41'),(541,165,'Johnston','2022-10-18 23:21:41'),(542,165,'Granville','2022-10-18 23:21:41'),(543,165,'Franklin','2022-10-18 23:21:41'),(544,165,'Wayne','2022-10-18 23:21:41'),(545,165,'Vance','2022-10-18 23:21:41'),(546,165,'Warren','2022-10-18 23:21:41'),(547,165,'Edgecombe','2022-10-18 23:21:41'),(548,165,'Nash','2022-10-18 23:21:41'),(549,165,'Bertie','2022-10-18 23:21:41'),(550,165,'Beaufort','2022-10-18 23:21:41'),(551,165,'Pitt','2022-10-18 23:21:41'),(552,165,'Wilson','2022-10-18 23:21:41'),(553,165,'Hertford','2022-10-18 23:21:41'),(554,165,'Northampton','2022-10-18 23:21:41'),(555,165,'Halifax','2022-10-18 23:21:41'),(556,165,'Hyde','2022-10-18 23:21:41'),(557,165,'Martin','2022-10-18 23:21:41'),(558,165,'Greene','2022-10-18 23:21:41'),(559,165,'Pasquotank','2022-10-18 23:21:41'),(560,165,'Dare','2022-10-18 23:21:41'),(561,165,'Currituck','2022-10-18 23:21:41'),(562,165,'Perquimans','2022-10-18 23:21:41'),(563,165,'Camden','2022-10-18 23:21:41'),(564,165,'Tyrrell','2022-10-18 23:21:41'),(565,165,'Gates','2022-10-18 23:21:41'),(566,165,'Washington','2022-10-18 23:21:41'),(567,165,'Chowan','2022-10-18 23:21:41'),(568,165,'Stanly','2022-10-18 23:21:41'),(569,165,'Gaston','2022-10-18 23:21:41'),(570,165,'Anson','2022-10-18 23:21:41'),(571,165,'Iredell','2022-10-18 23:21:41'),(572,165,'Cleveland','2022-10-18 23:21:41'),(573,165,'Rutherford','2022-10-18 23:21:41'),(574,165,'Cabarrus','2022-10-18 23:21:41'),(575,165,'Mecklenburg','2022-10-18 23:21:41'),(576,165,'Lincoln','2022-10-18 23:21:41'),(577,165,'Union','2022-10-18 23:21:41'),(578,165,'Cumberland','2022-10-18 23:21:41'),(579,165,'Sampson','2022-10-18 23:21:41'),(580,165,'Robeson','2022-10-18 23:21:41'),(581,165,'Bladen','2022-10-18 23:21:41'),(582,165,'Duplin','2022-10-18 23:21:41'),(583,165,'Richmond','2022-10-18 23:21:41'),(584,165,'Scotland','2022-10-18 23:21:41'),(585,165,'Hoke','2022-10-18 23:21:41'),(586,165,'New Hanover','2022-10-18 23:21:41'),(587,165,'Brunswick','2022-10-18 23:21:41'),(588,165,'Pender','2022-10-18 23:21:41'),(589,165,'Columbus','2022-10-18 23:21:41'),(590,165,'Onslow','2022-10-18 23:21:41'),(591,165,'Lenoir','2022-10-18 23:21:41'),(592,165,'Pamlico','2022-10-18 23:21:41'),(593,165,'Carteret','2022-10-18 23:21:41'),(594,165,'Craven','2022-10-18 23:21:41'),(595,165,'Jones','2022-10-18 23:21:41'),(596,165,'Catawba','2022-10-18 23:21:41'),(597,165,'Avery','2022-10-18 23:21:41'),(598,165,'Watauga','2022-10-18 23:21:41'),(599,165,'Wilkes','2022-10-18 23:21:41'),(600,165,'Caldwell','2022-10-18 23:21:41'),(601,165,'Burke','2022-10-18 23:21:41'),(602,165,'Ashe','2022-10-18 23:21:41'),(603,165,'Alleghany','2022-10-18 23:21:41'),(604,165,'Alexander','2022-10-18 23:21:41'),(605,165,'Buncombe','2022-10-18 23:21:41'),(606,165,'Swain','2022-10-18 23:21:41'),(607,165,'Mitchell','2022-10-18 23:21:41'),(608,165,'Jackson','2022-10-18 23:21:41'),(609,165,'Transylvania','2022-10-18 23:21:41'),(610,165,'Henderson','2022-10-18 23:21:41'),(611,165,'Yancey','2022-10-18 23:21:41'),(612,165,'Haywood','2022-10-18 23:21:41'),(613,165,'Polk','2022-10-18 23:21:41'),(614,165,'Graham','2022-10-18 23:21:41'),(615,165,'Macon','2022-10-18 23:21:41'),(616,165,'McDowell','2022-10-18 23:21:41'),(617,165,'Madison','2022-10-18 23:21:41'),(618,165,'Cherokee','2022-10-18 23:21:41'),(619,165,'Clay','2022-10-18 23:21:41'),(620,175,'Clarendon','2022-10-18 23:21:41'),(621,175,'Richland','2022-10-18 23:21:41'),(622,175,'Bamberg','2022-10-18 23:21:41'),(623,175,'Lexington','2022-10-18 23:21:41'),(624,175,'Kershaw','2022-10-18 23:21:41'),(625,175,'Lee','2022-10-18 23:21:41'),(626,175,'Chester','2022-10-18 23:21:41'),(627,175,'Fairfield','2022-10-18 23:21:41'),(628,175,'Orangeburg','2022-10-18 23:21:41'),(629,175,'Calhoun','2022-10-18 23:21:41'),(630,175,'Union','2022-10-18 23:21:41'),(631,175,'Newberry','2022-10-18 23:21:41'),(632,175,'Sumter','2022-10-18 23:21:41'),(633,175,'Williamsburg','2022-10-18 23:21:41'),(634,175,'Lancaster','2022-10-18 23:21:41'),(635,175,'Darlington','2022-10-18 23:21:41'),(636,175,'Colleton','2022-10-18 23:21:41'),(637,175,'Chesterfield','2022-10-18 23:21:41'),(638,175,'Saluda','2022-10-18 23:21:41'),(639,175,'Florence','2022-10-18 23:21:41'),(640,175,'Aiken','2022-10-18 23:21:41'),(641,175,'Spartanburg','2022-10-18 23:21:41'),(642,175,'Laurens','2022-10-18 23:21:41'),(643,175,'Cherokee','2022-10-18 23:21:41'),(644,175,'Charleston','2022-10-18 23:21:41'),(645,175,'Berkeley','2022-10-18 23:21:41'),(646,175,'Dorchester','2022-10-18 23:21:41'),(647,175,'Georgetown','2022-10-18 23:21:41'),(648,175,'Horry','2022-10-18 23:21:41'),(649,175,'Marlboro','2022-10-18 23:21:41'),(650,175,'Marion','2022-10-18 23:21:41'),(651,175,'Dillon','2022-10-18 23:21:41'),(652,175,'Greenville','2022-10-18 23:21:41'),(653,175,'Abbeville','2022-10-18 23:21:41'),(654,175,'Anderson','2022-10-18 23:21:41'),(655,175,'Pickens','2022-10-18 23:21:41'),(656,175,'Oconee','2022-10-18 23:21:41'),(657,175,'Greenwood','2022-10-18 23:21:41'),(658,175,'York','2022-10-18 23:21:41'),(659,175,'Allendale','2022-10-18 23:21:41'),(660,175,'Barnwell','2022-10-18 23:21:41'),(661,175,'McCormick','2022-10-18 23:21:41'),(662,175,'Edgefield','2022-10-18 23:21:41'),(663,175,'Beaufort','2022-10-18 23:21:41'),(664,175,'Hampton','2022-10-18 23:21:41'),(665,175,'Jasper','2022-10-18 23:21:41'),(666,140,'Dekalb','2022-10-18 23:21:41'),(667,140,'Gwinnett','2022-10-18 23:21:41'),(668,140,'Fulton','2022-10-18 23:21:41'),(669,140,'Cobb','2022-10-18 23:21:41'),(670,140,'Barrow','2022-10-18 23:21:41'),(671,140,'Rockdale','2022-10-18 23:21:41'),(672,140,'Newton','2022-10-18 23:21:41'),(673,140,'Walton','2022-10-18 23:21:41'),(674,140,'Forsyth','2022-10-18 23:21:41'),(675,140,'Jasper','2022-10-18 23:21:41'),(676,140,'Bartow','2022-10-18 23:21:41'),(677,140,'Polk','2022-10-18 23:21:41'),(678,140,'Floyd','2022-10-18 23:21:41'),(679,140,'Cherokee','2022-10-18 23:21:41'),(680,140,'Carroll','2022-10-18 23:21:41'),(681,140,'Haralson','2022-10-18 23:21:41'),(682,140,'Douglas','2022-10-18 23:21:41'),(683,140,'Paulding','2022-10-18 23:21:41'),(684,140,'Gordon','2022-10-18 23:21:41'),(685,140,'Pickens','2022-10-18 23:21:41'),(686,140,'Lamar','2022-10-18 23:21:41'),(687,140,'Fayette','2022-10-18 23:21:41'),(688,140,'Pike','2022-10-18 23:21:41'),(689,140,'Spalding','2022-10-18 23:21:41'),(690,140,'Butts','2022-10-18 23:21:41'),(691,140,'Heard','2022-10-18 23:21:41'),(692,140,'Meriwether','2022-10-18 23:21:41'),(693,140,'Coweta','2022-10-18 23:21:41'),(694,140,'Henry','2022-10-18 23:21:41'),(695,140,'Troup','2022-10-18 23:21:41'),(696,140,'Clayton','2022-10-18 23:21:41'),(697,140,'Upson','2022-10-18 23:21:41'),(698,140,'Emanuel','2022-10-18 23:21:41'),(699,140,'Montgomery','2022-10-18 23:21:41'),(700,140,'Wheeler','2022-10-18 23:21:41'),(701,140,'Jefferson','2022-10-18 23:21:41'),(702,140,'Evans','2022-10-18 23:21:41'),(703,140,'Bulloch','2022-10-18 23:21:41'),(704,140,'Tattnall','2022-10-18 23:21:41'),(705,140,'Screven','2022-10-18 23:21:41'),(706,140,'Burke','2022-10-18 23:21:41'),(707,140,'Toombs','2022-10-18 23:21:41'),(708,140,'Candler','2022-10-18 23:21:41'),(709,140,'Jenkins','2022-10-18 23:21:41'),(710,140,'Laurens','2022-10-18 23:21:41'),(711,140,'Treutlen','2022-10-18 23:21:41'),(712,140,'Hall','2022-10-18 23:21:41'),(713,140,'Habersham','2022-10-18 23:21:41'),(714,140,'Banks','2022-10-18 23:21:41'),(715,140,'Union','2022-10-18 23:21:41'),(716,140,'Fannin','2022-10-18 23:21:41'),(717,140,'Hart','2022-10-18 23:21:41'),(718,140,'Jackson','2022-10-18 23:21:41'),(719,140,'Franklin','2022-10-18 23:21:41'),(720,140,'Gilmer','2022-10-18 23:21:41'),(721,140,'Rabun','2022-10-18 23:21:41'),(722,140,'White','2022-10-18 23:21:41'),(723,140,'Lumpkin','2022-10-18 23:21:41'),(724,140,'Dawson','2022-10-18 23:21:41'),(725,140,'Stephens','2022-10-18 23:21:41'),(726,140,'Towns','2022-10-18 23:21:41'),(727,140,'Clarke','2022-10-18 23:21:41'),(728,140,'Oglethorpe','2022-10-18 23:21:41'),(729,140,'Oconee','2022-10-18 23:21:41'),(730,140,'Morgan','2022-10-18 23:21:41'),(731,140,'Elbert','2022-10-18 23:21:41'),(732,140,'Madison','2022-10-18 23:21:41'),(733,140,'Taliaferro','2022-10-18 23:21:41'),(734,140,'Greene','2022-10-18 23:21:41'),(735,140,'Wilkes','2022-10-18 23:21:41'),(736,140,'Murray','2022-10-18 23:21:41'),(737,140,'Walker','2022-10-18 23:21:41'),(738,140,'Whitfield','2022-10-18 23:21:41'),(739,140,'Catoosa','2022-10-18 23:21:41'),(740,140,'Chattooga','2022-10-18 23:21:41'),(741,140,'Dade','2022-10-18 23:21:41'),(742,140,'Columbia','2022-10-18 23:21:41'),(743,140,'Richmond','2022-10-18 23:21:41'),(744,140,'McDuffie','2022-10-18 23:21:41'),(745,140,'Warren','2022-10-18 23:21:41'),(746,140,'Glascock','2022-10-18 23:21:41'),(747,140,'Lincoln','2022-10-18 23:21:41'),(748,140,'Wilcox','2022-10-18 23:21:41'),(749,140,'Wilkinson','2022-10-18 23:21:41'),(750,140,'Monroe','2022-10-18 23:21:41'),(751,140,'Houston','2022-10-18 23:21:41'),(752,140,'Taylor','2022-10-18 23:21:41'),(753,140,'Dooly','2022-10-18 23:21:41'),(754,140,'Peach','2022-10-18 23:21:41'),(755,140,'Crisp','2022-10-18 23:21:41'),(756,140,'Dodge','2022-10-18 23:21:41'),(757,140,'Bleckley','2022-10-18 23:21:41'),(758,140,'Twiggs','2022-10-18 23:21:41'),(759,140,'Washington','2022-10-18 23:21:41'),(760,140,'Putnam','2022-10-18 23:21:41'),(761,140,'Jones','2022-10-18 23:21:41'),(762,140,'Baldwin','2022-10-18 23:21:41'),(763,140,'Pulaski','2022-10-18 23:21:41'),(764,140,'Telfair','2022-10-18 23:21:41'),(765,140,'Macon','2022-10-18 23:21:41'),(766,140,'Johnson','2022-10-18 23:21:41'),(767,140,'Crawford','2022-10-18 23:21:41'),(768,140,'Hancock','2022-10-18 23:21:41'),(769,140,'Bibb','2022-10-18 23:21:41'),(770,140,'Liberty','2022-10-18 23:21:41'),(771,140,'Chatham','2022-10-18 23:21:41'),(772,140,'Effingham','2022-10-18 23:21:41'),(773,140,'McIntosh','2022-10-18 23:21:41'),(774,140,'Bryan','2022-10-18 23:21:41'),(775,140,'Long','2022-10-18 23:21:41'),(776,140,'Ware','2022-10-18 23:21:41'),(777,140,'Bacon','2022-10-18 23:21:41'),(778,140,'Coffee','2022-10-18 23:21:41'),(779,140,'Appling','2022-10-18 23:21:41'),(780,140,'Pierce','2022-10-18 23:21:41'),(781,140,'Glynn','2022-10-18 23:21:41'),(782,140,'Jeff Davis','2022-10-18 23:21:41'),(783,140,'Charlton','2022-10-18 23:21:41'),(784,140,'Brantley','2022-10-18 23:21:41'),(785,140,'Wayne','2022-10-18 23:21:41'),(786,140,'Camden','2022-10-18 23:21:41'),(787,140,'Decatur','2022-10-18 23:21:41'),(788,140,'Lowndes','2022-10-18 23:21:41'),(789,140,'Cook','2022-10-18 23:21:41'),(790,140,'Berrien','2022-10-18 23:21:41'),(791,140,'Clinch','2022-10-18 23:21:41'),(792,140,'Atkinson','2022-10-18 23:21:41'),(793,140,'Brooks','2022-10-18 23:21:41'),(794,140,'Thomas','2022-10-18 23:21:41'),(795,140,'Lanier','2022-10-18 23:21:41'),(796,140,'Echols','2022-10-18 23:21:41'),(797,140,'Dougherty','2022-10-18 23:21:41'),(798,140,'Sumter','2022-10-18 23:21:41'),(799,140,'Turner','2022-10-18 23:21:41'),(800,140,'Mitchell','2022-10-18 23:21:41'),(801,140,'Colquitt','2022-10-18 23:21:41'),(802,140,'Tift','2022-10-18 23:21:41'),(803,140,'Ben Hill','2022-10-18 23:21:41'),(804,140,'Irwin','2022-10-18 23:21:41'),(805,140,'Lee','2022-10-18 23:21:41'),(806,140,'Worth','2022-10-18 23:21:41'),(807,140,'Talbot','2022-10-18 23:21:41'),(808,140,'Marion','2022-10-18 23:21:41'),(809,140,'Harris','2022-10-18 23:21:41'),(810,140,'Chattahoochee','2022-10-18 23:21:41'),(811,140,'Schley','2022-10-18 23:21:41'),(812,140,'Muscogee','2022-10-18 23:21:41'),(813,140,'Stewart','2022-10-18 23:21:41'),(814,140,'Webster','2022-10-18 23:21:41'),(815,139,'Clay','2022-10-18 23:21:41'),(816,139,'Saint Johns','2022-10-18 23:21:41'),(817,139,'Putnam','2022-10-18 23:21:41'),(818,139,'Suwannee','2022-10-18 23:21:41'),(819,139,'Nassau','2022-10-18 23:21:41'),(820,139,'Lafayette','2022-10-18 23:21:41'),(821,139,'Columbia','2022-10-18 23:21:41'),(822,139,'Union','2022-10-18 23:21:41'),(823,139,'Baker','2022-10-18 23:21:41'),(824,139,'Bradford','2022-10-18 23:21:41'),(825,139,'Hamilton','2022-10-18 23:21:41'),(826,139,'Madison','2022-10-18 23:21:41'),(827,139,'Duval','2022-10-18 23:21:41'),(828,139,'Lake','2022-10-18 23:21:41'),(829,139,'Volusia','2022-10-18 23:21:41'),(830,139,'Flagler','2022-10-18 23:21:41'),(831,139,'Marion','2022-10-18 23:21:41'),(832,139,'Sumter','2022-10-18 23:21:41'),(833,139,'Leon','2022-10-18 23:21:41'),(834,139,'Wakulla','2022-10-18 23:21:41'),(835,139,'Franklin','2022-10-18 23:21:41'),(836,139,'Liberty','2022-10-18 23:21:41'),(837,139,'Gadsden','2022-10-18 23:21:41'),(838,139,'Jefferson','2022-10-18 23:21:41'),(839,139,'Taylor','2022-10-18 23:21:41'),(840,139,'Bay','2022-10-18 23:21:41'),(841,139,'Jackson','2022-10-18 23:21:41'),(842,139,'Calhoun','2022-10-18 23:21:41'),(843,139,'Walton','2022-10-18 23:21:41'),(844,139,'Holmes','2022-10-18 23:21:41'),(845,139,'Washington','2022-10-18 23:21:41'),(846,139,'Gulf','2022-10-18 23:21:41'),(847,139,'Escambia','2022-10-18 23:21:41'),(848,139,'Santa Rosa','2022-10-18 23:21:41'),(849,139,'Okaloosa','2022-10-18 23:21:41'),(850,139,'Alachua','2022-10-18 23:21:41'),(851,139,'Gilchrist','2022-10-18 23:21:41'),(852,139,'Levy','2022-10-18 23:21:41'),(853,139,'Dixie','2022-10-18 23:21:41'),(854,139,'Seminole','2022-10-18 23:21:41'),(855,139,'Orange','2022-10-18 23:21:41'),(856,139,'Brevard','2022-10-18 23:21:41'),(857,139,'Indian River','2022-10-18 23:21:41'),(858,139,'Monroe','2022-10-18 23:21:41'),(859,139,'Miami Dade','2022-10-18 23:21:41'),(860,139,'Broward','2022-10-18 23:21:41'),(861,139,'Palm Beach','2022-10-18 23:21:41'),(862,139,'Hendry','2022-10-18 23:21:41'),(863,139,'Martin','2022-10-18 23:21:41'),(864,139,'Glades','2022-10-18 23:21:41'),(865,139,'Hillsborough','2022-10-18 23:21:41'),(866,139,'Pasco','2022-10-18 23:21:41'),(867,139,'Pinellas','2022-10-18 23:21:41'),(868,139,'Polk','2022-10-18 23:21:41'),(869,139,'Highlands','2022-10-18 23:21:41'),(870,139,'Hardee','2022-10-18 23:21:41'),(871,139,'Osceola','2022-10-18 23:21:41'),(872,139,'Lee','2022-10-18 23:21:41'),(873,139,'Charlotte','2022-10-18 23:21:41'),(874,139,'Collier','2022-10-18 23:21:41'),(875,139,'Manatee','2022-10-18 23:21:41'),(876,139,'Sarasota','2022-10-18 23:21:41'),(877,139,'De Soto','2022-10-18 23:21:41'),(878,139,'Citrus','2022-10-18 23:21:41'),(879,139,'Hernando','2022-10-18 23:21:41'),(880,139,'Saint Lucie','2022-10-18 23:21:41'),(881,139,'Okeechobee','2022-10-18 23:21:41'),(882,129,'Saint Clair','2022-10-18 23:21:41'),(883,129,'Jefferson','2022-10-18 23:21:41'),(884,129,'Shelby','2022-10-18 23:21:41'),(885,129,'Tallapoosa','2022-10-18 23:21:41'),(886,129,'Blount','2022-10-18 23:21:41'),(887,129,'Talladega','2022-10-18 23:21:41'),(888,129,'Marshall','2022-10-18 23:21:41'),(889,129,'Cullman','2022-10-18 23:21:41'),(890,129,'Bibb','2022-10-18 23:21:41'),(891,129,'Walker','2022-10-18 23:21:41'),(892,129,'Chilton','2022-10-18 23:21:41'),(893,129,'Coosa','2022-10-18 23:21:41'),(894,129,'Clay','2022-10-18 23:21:41'),(895,129,'Tuscaloosa','2022-10-18 23:21:41'),(896,129,'Hale','2022-10-18 23:21:41'),(897,129,'Pickens','2022-10-18 23:21:41'),(898,129,'Greene','2022-10-18 23:21:41'),(899,129,'Sumter','2022-10-18 23:21:41'),(900,129,'Winston','2022-10-18 23:21:41'),(901,129,'Fayette','2022-10-18 23:21:41'),(902,129,'Marion','2022-10-18 23:21:41'),(903,129,'Lamar','2022-10-18 23:21:41'),(904,129,'Franklin','2022-10-18 23:21:41'),(905,129,'Morgan','2022-10-18 23:21:41'),(906,129,'Lauderdale','2022-10-18 23:21:41'),(907,129,'Limestone','2022-10-18 23:21:41'),(908,129,'Colbert','2022-10-18 23:21:41'),(909,129,'Lawrence','2022-10-18 23:21:41'),(910,129,'Jackson','2022-10-18 23:21:41'),(911,129,'Madison','2022-10-18 23:21:41'),(912,129,'Etowah','2022-10-18 23:21:41'),(913,129,'Cherokee','2022-10-18 23:21:41'),(914,129,'De Kalb','2022-10-18 23:21:41'),(915,129,'Autauga','2022-10-18 23:21:41'),(916,129,'Pike','2022-10-18 23:21:41'),(917,129,'Crenshaw','2022-10-18 23:21:41'),(918,129,'Montgomery','2022-10-18 23:21:41'),(919,129,'Butler','2022-10-18 23:21:41'),(920,129,'Barbour','2022-10-18 23:21:41'),(921,129,'Elmore','2022-10-18 23:21:41'),(922,129,'Bullock','2022-10-18 23:21:41'),(923,129,'Macon','2022-10-18 23:21:41'),(924,129,'Lowndes','2022-10-18 23:21:41'),(925,129,'Covington','2022-10-18 23:21:41'),(926,129,'Calhoun','2022-10-18 23:21:41'),(927,129,'Cleburne','2022-10-18 23:21:41'),(928,129,'Randolph','2022-10-18 23:21:41'),(929,129,'Houston','2022-10-18 23:21:41'),(930,129,'Henry','2022-10-18 23:21:41'),(931,129,'Dale','2022-10-18 23:21:41'),(932,129,'Geneva','2022-10-18 23:21:41'),(933,129,'Coffee','2022-10-18 23:21:41'),(934,129,'Conecuh','2022-10-18 23:21:41'),(935,129,'Monroe','2022-10-18 23:21:41'),(936,129,'Escambia','2022-10-18 23:21:41'),(937,129,'Wilcox','2022-10-18 23:21:41'),(938,129,'Clarke','2022-10-18 23:21:41'),(939,129,'Mobile','2022-10-18 23:21:41'),(940,129,'Baldwin','2022-10-18 23:21:41'),(941,129,'Washington','2022-10-18 23:21:41'),(942,129,'Dallas','2022-10-18 23:21:41'),(943,129,'Marengo','2022-10-18 23:21:41'),(944,129,'Perry','2022-10-18 23:21:41'),(945,129,'Lee','2022-10-18 23:21:41'),(946,129,'Russell','2022-10-18 23:21:41'),(947,129,'Chambers','2022-10-18 23:21:41'),(948,129,'Choctaw','2022-10-18 23:21:41'),(949,177,'Robertson','2022-10-18 23:21:41'),(950,177,'Davidson','2022-10-18 23:21:41'),(951,177,'Dekalb','2022-10-18 23:21:41'),(952,177,'Williamson','2022-10-18 23:21:41'),(953,177,'Cheatham','2022-10-18 23:21:41'),(954,177,'Cannon','2022-10-18 23:21:41'),(955,177,'Coffee','2022-10-18 23:21:41'),(956,177,'Marshall','2022-10-18 23:21:41'),(957,177,'Bedford','2022-10-18 23:21:41'),(958,177,'Sumner','2022-10-18 23:21:41'),(959,177,'Stewart','2022-10-18 23:21:41'),(960,177,'Hickman','2022-10-18 23:21:41'),(961,177,'Dickson','2022-10-18 23:21:41'),(962,177,'Smith','2022-10-18 23:21:41'),(963,177,'Rutherford','2022-10-18 23:21:41'),(964,177,'Montgomery','2022-10-18 23:21:41'),(965,177,'Houston','2022-10-18 23:21:41'),(966,177,'Wilson','2022-10-18 23:21:41'),(967,177,'Trousdale','2022-10-18 23:21:41'),(968,177,'Humphreys','2022-10-18 23:21:41'),(969,177,'Macon','2022-10-18 23:21:41'),(970,177,'Perry','2022-10-18 23:21:41'),(971,177,'Warren','2022-10-18 23:21:41'),(972,177,'Lincoln','2022-10-18 23:21:41'),(973,177,'Maury','2022-10-18 23:21:41'),(974,177,'Grundy','2022-10-18 23:21:41'),(975,177,'Hamilton','2022-10-18 23:21:41'),(976,177,'McMinn','2022-10-18 23:21:41'),(977,177,'Franklin','2022-10-18 23:21:41'),(978,177,'Polk','2022-10-18 23:21:41'),(979,177,'Bradley','2022-10-18 23:21:41'),(980,177,'Monroe','2022-10-18 23:21:41'),(981,177,'Rhea','2022-10-18 23:21:41'),(982,177,'Meigs','2022-10-18 23:21:41'),(983,177,'Sequatchie','2022-10-18 23:21:41'),(984,177,'Marion','2022-10-18 23:21:41'),(985,177,'Moore','2022-10-18 23:21:41'),(986,177,'Bledsoe','2022-10-18 23:21:41'),(987,177,'Shelby','2022-10-18 23:21:41'),(988,177,'Washington','2022-10-18 23:21:41'),(989,177,'Greene','2022-10-18 23:21:41'),(990,177,'Sullivan','2022-10-18 23:21:41'),(991,177,'Johnson','2022-10-18 23:21:41'),(992,177,'Hawkins','2022-10-18 23:21:41'),(993,177,'Carter','2022-10-18 23:21:41'),(994,177,'Unicoi','2022-10-18 23:21:41'),(995,177,'Blount','2022-10-18 23:21:41'),(996,177,'Anderson','2022-10-18 23:21:41'),(997,177,'Claiborne','2022-10-18 23:21:41'),(998,177,'Grainger','2022-10-18 23:21:41'),(999,177,'Cocke','2022-10-18 23:21:41'),(1000,177,'Campbell','2022-10-18 23:21:41'),(1001,177,'Morgan','2022-10-18 23:21:41'),(1002,177,'Knox','2022-10-18 23:21:41'),(1003,177,'Cumberland','2022-10-18 23:21:41'),(1004,177,'Jefferson','2022-10-18 23:21:41'),(1005,177,'Scott','2022-10-18 23:21:41'),(1006,177,'Sevier','2022-10-18 23:21:41'),(1007,177,'Loudon','2022-10-18 23:21:41'),(1008,177,'Roane','2022-10-18 23:21:41'),(1009,177,'Hancock','2022-10-18 23:21:41'),(1010,177,'Hamblen','2022-10-18 23:21:41'),(1011,177,'Union','2022-10-18 23:21:41'),(1012,177,'Crockett','2022-10-18 23:21:41'),(1013,177,'Fayette','2022-10-18 23:21:41'),(1014,177,'Tipton','2022-10-18 23:21:41'),(1015,177,'Dyer','2022-10-18 23:21:41'),(1016,177,'Hardeman','2022-10-18 23:21:41'),(1017,177,'Haywood','2022-10-18 23:21:41'),(1018,177,'Lauderdale','2022-10-18 23:21:41'),(1019,177,'Lake','2022-10-18 23:21:41'),(1020,177,'Carroll','2022-10-18 23:21:41'),(1021,177,'Benton','2022-10-18 23:21:41'),(1022,177,'Henry','2022-10-18 23:21:41'),(1023,177,'Weakley','2022-10-18 23:21:41'),(1024,177,'Obion','2022-10-18 23:21:41'),(1025,177,'Gibson','2022-10-18 23:21:41'),(1026,177,'Madison','2022-10-18 23:21:41'),(1027,177,'McNairy','2022-10-18 23:21:41'),(1028,177,'Decatur','2022-10-18 23:21:41'),(1029,177,'Hardin','2022-10-18 23:21:41'),(1030,177,'Henderson','2022-10-18 23:21:41'),(1031,177,'Chester','2022-10-18 23:21:41'),(1032,177,'Wayne','2022-10-18 23:21:41'),(1033,177,'Giles','2022-10-18 23:21:41'),(1034,177,'Lawrence','2022-10-18 23:21:41'),(1035,177,'Lewis','2022-10-18 23:21:41'),(1036,177,'Putnam','2022-10-18 23:21:41'),(1037,177,'Fentress','2022-10-18 23:21:41'),(1038,177,'Overton','2022-10-18 23:21:41'),(1039,177,'Pickett','2022-10-18 23:21:41'),(1040,177,'Clay','2022-10-18 23:21:41'),(1041,177,'White','2022-10-18 23:21:41'),(1042,177,'Jackson','2022-10-18 23:21:41'),(1043,177,'Van Buren','2022-10-18 23:21:41'),(1044,156,'Lafayette','2022-10-18 23:21:41'),(1045,156,'Tate','2022-10-18 23:21:41'),(1046,156,'Benton','2022-10-18 23:21:41'),(1047,156,'Panola','2022-10-18 23:21:41'),(1048,156,'Quitman','2022-10-18 23:21:41'),(1049,156,'Tippah','2022-10-18 23:21:41'),(1050,156,'Marshall','2022-10-18 23:21:41'),(1051,156,'Coahoma','2022-10-18 23:21:41'),(1052,156,'Tunica','2022-10-18 23:21:41'),(1053,156,'Union','2022-10-18 23:21:41'),(1054,156,'De Soto','2022-10-18 23:21:41'),(1055,156,'Washington','2022-10-18 23:21:41'),(1056,156,'Bolivar','2022-10-18 23:21:41'),(1057,156,'Sharkey','2022-10-18 23:21:41'),(1058,156,'Sunflower','2022-10-18 23:21:41'),(1059,156,'Issaquena','2022-10-18 23:21:41'),(1060,156,'Humphreys','2022-10-18 23:21:41'),(1061,156,'Lee','2022-10-18 23:21:41'),(1062,156,'Pontotoc','2022-10-18 23:21:41'),(1063,156,'Monroe','2022-10-18 23:21:41'),(1064,156,'Tishomingo','2022-10-18 23:21:41'),(1065,156,'Prentiss','2022-10-18 23:21:41'),(1066,156,'Alcorn','2022-10-18 23:21:41'),(1067,156,'Calhoun','2022-10-18 23:21:41'),(1068,156,'Itawamba','2022-10-18 23:21:41'),(1069,156,'Chickasaw','2022-10-18 23:21:41'),(1070,156,'Grenada','2022-10-18 23:21:41'),(1071,156,'Carroll','2022-10-18 23:21:41'),(1072,156,'Tallahatchie','2022-10-18 23:21:41'),(1073,156,'Yalobusha','2022-10-18 23:21:41'),(1074,156,'Holmes','2022-10-18 23:21:41'),(1075,156,'Montgomery','2022-10-18 23:21:41'),(1076,156,'Leflore','2022-10-18 23:21:41'),(1077,156,'Yazoo','2022-10-18 23:21:41'),(1078,156,'Hinds','2022-10-18 23:21:41'),(1079,156,'Rankin','2022-10-18 23:21:41'),(1080,156,'Simpson','2022-10-18 23:21:41'),(1081,156,'Madison','2022-10-18 23:21:41'),(1082,156,'Leake','2022-10-18 23:21:41'),(1083,156,'Newton','2022-10-18 23:21:41'),(1084,156,'Copiah','2022-10-18 23:21:41'),(1085,156,'Attala','2022-10-18 23:21:41'),(1086,156,'Jefferson','2022-10-18 23:21:41'),(1087,156,'Scott','2022-10-18 23:21:41'),(1088,156,'Claiborne','2022-10-18 23:21:41'),(1089,156,'Smith','2022-10-18 23:21:41'),(1090,156,'Covington','2022-10-18 23:21:41'),(1091,156,'Adams','2022-10-18 23:21:41'),(1092,156,'Lawrence','2022-10-18 23:21:41'),(1093,156,'Warren','2022-10-18 23:21:41'),(1094,156,'Lauderdale','2022-10-18 23:21:41'),(1095,156,'Wayne','2022-10-18 23:21:41'),(1096,156,'Kemper','2022-10-18 23:21:41'),(1097,156,'Clarke','2022-10-18 23:21:41'),(1098,156,'Jasper','2022-10-18 23:21:41'),(1099,156,'Winston','2022-10-18 23:21:41'),(1100,156,'Noxubee','2022-10-18 23:21:41'),(1101,156,'Neshoba','2022-10-18 23:21:41'),(1102,156,'Greene','2022-10-18 23:21:41'),(1103,156,'Forrest','2022-10-18 23:21:41'),(1104,156,'Jefferson Davis','2022-10-18 23:21:41'),(1105,156,'Perry','2022-10-18 23:21:41'),(1106,156,'Pearl River','2022-10-18 23:21:41'),(1107,156,'Marion','2022-10-18 23:21:41'),(1108,156,'Jones','2022-10-18 23:21:41'),(1109,156,'George','2022-10-18 23:21:41'),(1110,156,'Lamar','2022-10-18 23:21:41'),(1111,156,'Harrison','2022-10-18 23:21:41'),(1112,156,'Hancock','2022-10-18 23:21:41'),(1113,156,'Jackson','2022-10-18 23:21:41'),(1114,156,'Stone','2022-10-18 23:21:41'),(1115,156,'Lincoln','2022-10-18 23:21:41'),(1116,156,'Franklin','2022-10-18 23:21:41'),(1117,156,'Wilkinson','2022-10-18 23:21:41'),(1118,156,'Pike','2022-10-18 23:21:41'),(1119,156,'Amite','2022-10-18 23:21:41'),(1120,156,'Walthall','2022-10-18 23:21:41'),(1121,156,'Lowndes','2022-10-18 23:21:41'),(1122,156,'Choctaw','2022-10-18 23:21:41'),(1123,156,'Webster','2022-10-18 23:21:41'),(1124,156,'Clay','2022-10-18 23:21:41'),(1125,156,'Oktibbeha','2022-10-18 23:21:41'),(1126,140,'Calhoun','2022-10-18 23:21:41'),(1127,140,'Early','2022-10-18 23:21:41'),(1128,140,'Clay','2022-10-18 23:21:41'),(1129,140,'Phelps','2022-10-18 23:21:41'),(1130,140,'Terrell','2022-10-18 23:21:41'),(1131,140,'Grady','2022-10-18 23:21:41'),(1132,140,'Seminole','2022-10-18 23:21:41'),(1133,140,'Quitman','2022-10-18 23:21:41'),(1134,140,'Baker','2022-10-18 23:21:41'),(1135,140,'Randolph','2022-10-18 23:21:41'),(1136,148,'Shelby','2022-10-18 23:21:41'),(1137,148,'Nelson','2022-10-18 23:21:41'),(1138,148,'Trimble','2022-10-18 23:21:41'),(1139,148,'Henry','2022-10-18 23:21:41'),(1140,148,'Marion','2022-10-18 23:21:41'),(1141,148,'Oldham','2022-10-18 23:21:41'),(1142,148,'Jefferson','2022-10-18 23:21:41'),(1143,148,'Washington','2022-10-18 23:21:41'),(1144,148,'Spencer','2022-10-18 23:21:41'),(1145,148,'Bullitt','2022-10-18 23:21:41'),(1146,148,'Meade','2022-10-18 23:21:41'),(1147,148,'Breckinridge','2022-10-18 23:21:41'),(1148,148,'Grayson','2022-10-18 23:21:41'),(1149,148,'Hardin','2022-10-18 23:21:41'),(1150,148,'Mercer','2022-10-18 23:21:41'),(1151,148,'Nicholas','2022-10-18 23:21:41'),(1152,148,'Powell','2022-10-18 23:21:41'),(1153,148,'Rowan','2022-10-18 23:21:41'),(1154,148,'Menifee','2022-10-18 23:21:41'),(1155,148,'Scott','2022-10-18 23:21:41'),(1156,148,'Montgomery','2022-10-18 23:21:41'),(1157,148,'Estill','2022-10-18 23:21:41'),(1158,148,'Jessamine','2022-10-18 23:21:41'),(1159,148,'Anderson','2022-10-18 23:21:41'),(1160,148,'Woodford','2022-10-18 23:21:41'),(1161,148,'Bourbon','2022-10-18 23:21:41'),(1162,148,'Owen','2022-10-18 23:21:41'),(1163,148,'Bath','2022-10-18 23:21:41'),(1164,148,'Madison','2022-10-18 23:21:41'),(1165,148,'Clark','2022-10-18 23:21:41'),(1166,148,'Jackson','2022-10-18 23:21:41'),(1167,148,'Rockcastle','2022-10-18 23:21:41'),(1168,148,'Garrard','2022-10-18 23:21:41'),(1169,148,'Lincoln','2022-10-18 23:21:41'),(1170,148,'Boyle','2022-10-18 23:21:41'),(1171,148,'Fayette','2022-10-18 23:21:41'),(1172,148,'Franklin','2022-10-18 23:21:41'),(1173,148,'Whitley','2022-10-18 23:21:41'),(1174,148,'Laurel','2022-10-18 23:21:41'),(1175,148,'Knox','2022-10-18 23:21:41'),(1176,148,'Harlan','2022-10-18 23:21:41'),(1177,148,'Leslie','2022-10-18 23:21:41'),(1178,148,'Bell','2022-10-18 23:21:41'),(1179,148,'Letcher','2022-10-18 23:21:41'),(1180,148,'Clay','2022-10-18 23:21:41'),(1181,148,'Perry','2022-10-18 23:21:41'),(1182,148,'Campbell','2022-10-18 23:21:41'),(1183,148,'Bracken','2022-10-18 23:21:41'),(1184,148,'Harrison','2022-10-18 23:21:41'),(1185,148,'Boone','2022-10-18 23:21:41'),(1186,148,'Pendleton','2022-10-18 23:21:41'),(1187,148,'Carroll','2022-10-18 23:21:41'),(1188,148,'Grant','2022-10-18 23:21:41'),(1189,148,'Kenton','2022-10-18 23:21:41'),(1190,148,'Mason','2022-10-18 23:21:41'),(1191,148,'Fleming','2022-10-18 23:21:41'),(1192,148,'Gallatin','2022-10-18 23:21:41'),(1193,148,'Robertson','2022-10-18 23:21:41'),(1194,148,'Boyd','2022-10-18 23:21:41'),(1195,148,'Greenup','2022-10-18 23:21:41'),(1196,148,'Lawrence','2022-10-18 23:21:41'),(1197,148,'Carter','2022-10-18 23:21:41'),(1198,148,'Lewis','2022-10-18 23:21:41'),(1199,148,'Elliott','2022-10-18 23:21:41'),(1200,148,'Martin','2022-10-18 23:21:41'),(1201,148,'Johnson','2022-10-18 23:21:41'),(1202,148,'Wolfe','2022-10-18 23:21:41'),(1203,148,'Breathitt','2022-10-18 23:21:41'),(1204,148,'Lee','2022-10-18 23:21:41'),(1205,148,'Owsley','2022-10-18 23:21:41'),(1206,148,'Morgan','2022-10-18 23:21:41'),(1207,148,'Magoffin','2022-10-18 23:21:41'),(1208,148,'Pike','2022-10-18 23:21:41'),(1209,148,'Floyd','2022-10-18 23:21:41'),(1210,148,'Knott','2022-10-18 23:21:41'),(1211,148,'McCracken','2022-10-18 23:21:41'),(1212,148,'Calloway','2022-10-18 23:21:41'),(1213,148,'Carlisle','2022-10-18 23:21:41'),(1214,148,'Ballard','2022-10-18 23:21:41'),(1215,148,'Marshall','2022-10-18 23:21:41'),(1216,148,'Graves','2022-10-18 23:21:41'),(1217,148,'Livingston','2022-10-18 23:21:41'),(1218,148,'Hickman','2022-10-18 23:21:41'),(1219,148,'Crittenden','2022-10-18 23:21:41'),(1220,148,'Lyon','2022-10-18 23:21:41'),(1221,148,'Fulton','2022-10-18 23:21:41'),(1222,148,'Warren','2022-10-18 23:21:41'),(1223,148,'Allen','2022-10-18 23:21:41'),(1224,148,'Barren','2022-10-18 23:21:41'),(1225,148,'Metcalfe','2022-10-18 23:21:41'),(1226,148,'Monroe','2022-10-18 23:21:41'),(1227,148,'Simpson','2022-10-18 23:21:41'),(1228,148,'Edmonson','2022-10-18 23:21:41'),(1229,148,'Butler','2022-10-18 23:21:41'),(1230,148,'Logan','2022-10-18 23:21:41'),(1231,148,'Todd','2022-10-18 23:21:41'),(1232,148,'Trigg','2022-10-18 23:21:41'),(1233,148,'Christian','2022-10-18 23:21:41'),(1234,148,'Daviess','2022-10-18 23:21:41'),(1235,148,'Ohio','2022-10-18 23:21:41'),(1236,148,'Muhlenberg','2022-10-18 23:21:41'),(1237,148,'McLean','2022-10-18 23:21:41'),(1238,148,'Hancock','2022-10-18 23:21:41'),(1239,148,'Henderson','2022-10-18 23:21:41'),(1240,148,'Webster','2022-10-18 23:21:41'),(1241,148,'Hopkins','2022-10-18 23:21:41'),(1242,148,'Caldwell','2022-10-18 23:21:41'),(1243,148,'Union','2022-10-18 23:21:41'),(1244,148,'Pulaski','2022-10-18 23:21:41'),(1245,148,'Casey','2022-10-18 23:21:41'),(1246,148,'Clinton','2022-10-18 23:21:41'),(1247,148,'Russell','2022-10-18 23:21:41'),(1248,148,'McCreary','2022-10-18 23:21:41'),(1249,148,'Wayne','2022-10-18 23:21:41'),(1250,148,'Hart','2022-10-18 23:21:41'),(1251,148,'Adair','2022-10-18 23:21:41'),(1252,148,'Larue','2022-10-18 23:21:41'),(1253,148,'Cumberland','2022-10-18 23:21:41'),(1254,148,'Taylor','2022-10-18 23:21:41'),(1255,148,'Green','2022-10-18 23:21:41'),(1256,168,'Licking','2022-10-18 23:21:41'),(1257,168,'Franklin','2022-10-18 23:21:41'),(1258,168,'Delaware','2022-10-18 23:21:41'),(1259,168,'Knox','2022-10-18 23:21:41'),(1260,168,'Union','2022-10-18 23:21:41'),(1261,168,'Champaign','2022-10-18 23:21:41'),(1262,168,'Clark','2022-10-18 23:21:41'),(1263,168,'Fairfield','2022-10-18 23:21:41'),(1264,168,'Madison','2022-10-18 23:21:41'),(1265,168,'Perry','2022-10-18 23:21:41'),(1266,168,'Ross','2022-10-18 23:21:41'),(1267,168,'Pickaway','2022-10-18 23:21:41'),(1268,168,'Fayette','2022-10-18 23:21:41'),(1269,168,'Hocking','2022-10-18 23:21:41'),(1270,168,'Marion','2022-10-18 23:21:41'),(1271,168,'Logan','2022-10-18 23:21:41'),(1272,168,'Morrow','2022-10-18 23:21:41'),(1273,168,'Wyandot','2022-10-18 23:21:41'),(1274,168,'Hardin','2022-10-18 23:21:41'),(1275,168,'Wood','2022-10-18 23:21:41'),(1276,168,'Sandusky','2022-10-18 23:21:41'),(1277,168,'Ottawa','2022-10-18 23:21:41'),(1278,168,'Lucas','2022-10-18 23:21:41'),(1279,168,'Erie','2022-10-18 23:21:41'),(1280,168,'Williams','2022-10-18 23:21:41'),(1281,168,'Fulton','2022-10-18 23:21:41'),(1282,168,'Henry','2022-10-18 23:21:41'),(1283,168,'Defiance','2022-10-18 23:21:41'),(1284,168,'Muskingum','2022-10-18 23:21:41'),(1285,168,'Noble','2022-10-18 23:21:41'),(1286,168,'Belmont','2022-10-18 23:21:41'),(1287,168,'Monroe','2022-10-18 23:21:41'),(1288,168,'Guernsey','2022-10-18 23:21:41'),(1289,168,'Morgan','2022-10-18 23:21:41'),(1290,168,'Coshocton','2022-10-18 23:21:41'),(1291,168,'Tuscarawas','2022-10-18 23:21:41'),(1292,168,'Jefferson','2022-10-18 23:21:41'),(1293,168,'Harrison','2022-10-18 23:21:41'),(1294,168,'Columbiana','2022-10-18 23:21:41'),(1295,168,'Lorain','2022-10-18 23:21:41'),(1296,168,'Ashtabula','2022-10-18 23:21:41'),(1297,168,'Cuyahoga','2022-10-18 23:21:41'),(1298,168,'Geauga','2022-10-18 23:21:41'),(1299,168,'Lake','2022-10-18 23:21:41'),(1300,168,'Summit','2022-10-18 23:21:41'),(1301,168,'Portage','2022-10-18 23:21:41'),(1302,168,'Medina','2022-10-18 23:21:41'),(1303,168,'Wayne','2022-10-18 23:21:41'),(1304,168,'Mahoning','2022-10-18 23:21:41'),(1305,168,'Trumbull','2022-10-18 23:21:41'),(1306,168,'Stark','2022-10-18 23:21:41'),(1307,168,'Carroll','2022-10-18 23:21:41'),(1308,168,'Holmes','2022-10-18 23:21:41'),(1309,168,'Seneca','2022-10-18 23:21:41'),(1310,168,'Hancock','2022-10-18 23:21:41'),(1311,168,'Ashland','2022-10-18 23:21:41'),(1312,168,'Huron','2022-10-18 23:21:41'),(1313,168,'Richland','2022-10-18 23:21:41'),(1314,168,'Crawford','2022-10-18 23:21:41'),(1315,168,'Hamilton','2022-10-18 23:21:41'),(1316,168,'Butler','2022-10-18 23:21:41'),(1317,168,'Warren','2022-10-18 23:21:41'),(1318,168,'Preble','2022-10-18 23:21:41'),(1319,168,'Brown','2022-10-18 23:21:41'),(1320,168,'Clermont','2022-10-18 23:21:41'),(1321,168,'Adams','2022-10-18 23:21:41'),(1322,168,'Clinton','2022-10-18 23:21:41'),(1323,168,'Highland','2022-10-18 23:21:41'),(1324,168,'Greene','2022-10-18 23:21:41'),(1325,168,'Shelby','2022-10-18 23:21:41'),(1326,168,'Darke','2022-10-18 23:21:41'),(1327,168,'Miami','2022-10-18 23:21:41'),(1328,168,'Montgomery','2022-10-18 23:21:41'),(1329,168,'Mercer','2022-10-18 23:21:41'),(1330,168,'Pike','2022-10-18 23:21:41'),(1331,168,'Gallia','2022-10-18 23:21:41'),(1332,168,'Lawrence','2022-10-18 23:21:41'),(1333,168,'Jackson','2022-10-18 23:21:41'),(1334,168,'Vinton','2022-10-18 23:21:41'),(1335,168,'Scioto','2022-10-18 23:21:41'),(1336,168,'Athens','2022-10-18 23:21:41'),(1337,168,'Washington','2022-10-18 23:21:41'),(1338,168,'Meigs','2022-10-18 23:21:41'),(1339,168,'Allen','2022-10-18 23:21:41'),(1340,168,'Auglaize','2022-10-18 23:21:41'),(1341,168,'Paulding','2022-10-18 23:21:41'),(1342,168,'Putnam','2022-10-18 23:21:41'),(1343,168,'Van Wert','2022-10-18 23:21:41'),(1344,145,'Madison','2022-10-18 23:21:41'),(1345,145,'Hamilton','2022-10-18 23:21:41'),(1346,145,'Clinton','2022-10-18 23:21:41'),(1347,145,'Hancock','2022-10-18 23:21:41'),(1348,145,'Tipton','2022-10-18 23:21:41'),(1349,145,'Boone','2022-10-18 23:21:41'),(1350,145,'Hendricks','2022-10-18 23:21:41'),(1351,145,'Rush','2022-10-18 23:21:41'),(1352,145,'Putnam','2022-10-18 23:21:41'),(1353,145,'Johnson','2022-10-18 23:21:41'),(1354,145,'Marion','2022-10-18 23:21:41'),(1355,145,'Shelby','2022-10-18 23:21:41'),(1356,145,'Morgan','2022-10-18 23:21:41'),(1357,145,'Fayette','2022-10-18 23:21:41'),(1358,145,'Henry','2022-10-18 23:21:41'),(1359,145,'Brown','2022-10-18 23:21:41'),(1360,145,'Porter','2022-10-18 23:21:41'),(1361,145,'Lake','2022-10-18 23:21:41'),(1362,145,'Jasper','2022-10-18 23:21:41'),(1363,145,'La Porte','2022-10-18 23:21:41'),(1364,145,'Newton','2022-10-18 23:21:41'),(1365,145,'Starke','2022-10-18 23:21:41'),(1366,145,'Marshall','2022-10-18 23:21:41'),(1367,145,'Kosciusko','2022-10-18 23:21:41'),(1368,145,'Elkhart','2022-10-18 23:21:41'),(1369,145,'St Joseph','2022-10-18 23:21:41'),(1370,145,'Lagrange','2022-10-18 23:21:41'),(1371,145,'Noble','2022-10-18 23:21:41'),(1372,145,'Huntington','2022-10-18 23:21:41'),(1373,145,'Steuben','2022-10-18 23:21:41'),(1374,145,'Allen','2022-10-18 23:21:41'),(1375,145,'De Kalb','2022-10-18 23:21:41'),(1376,145,'Adams','2022-10-18 23:21:41'),(1377,145,'Wells','2022-10-18 23:21:41'),(1378,145,'Whitley','2022-10-18 23:21:41'),(1379,145,'Howard','2022-10-18 23:21:41'),(1380,145,'Fulton','2022-10-18 23:21:41'),(1381,145,'Miami','2022-10-18 23:21:41'),(1382,145,'Carroll','2022-10-18 23:21:41'),(1383,145,'Grant','2022-10-18 23:21:41'),(1384,145,'Cass','2022-10-18 23:21:41'),(1385,145,'Wabash','2022-10-18 23:21:41'),(1386,145,'Pulaski','2022-10-18 23:21:41'),(1387,145,'Dearborn','2022-10-18 23:21:41'),(1388,145,'Union','2022-10-18 23:21:41'),(1389,145,'Ripley','2022-10-18 23:21:41'),(1390,145,'Franklin','2022-10-18 23:21:41'),(1391,145,'Switzerland','2022-10-18 23:21:41'),(1392,145,'Ohio','2022-10-18 23:21:41'),(1393,145,'Scott','2022-10-18 23:21:41'),(1394,145,'Clark','2022-10-18 23:21:41'),(1395,145,'Harrison','2022-10-18 23:21:41'),(1396,145,'Washington','2022-10-18 23:21:41'),(1397,145,'Crawford','2022-10-18 23:21:41'),(1398,145,'Floyd','2022-10-18 23:21:41'),(1399,145,'Bartholomew','2022-10-18 23:21:41'),(1400,145,'Jackson','2022-10-18 23:21:41'),(1401,145,'Jennings','2022-10-18 23:21:41'),(1402,145,'Jefferson','2022-10-18 23:21:41'),(1403,145,'Decatur','2022-10-18 23:21:41'),(1404,145,'Delaware','2022-10-18 23:21:41'),(1405,145,'Wayne','2022-10-18 23:21:41'),(1406,145,'Jay','2022-10-18 23:21:41'),(1407,145,'Randolph','2022-10-18 23:21:41'),(1408,145,'Blackford','2022-10-18 23:21:41'),(1409,145,'Monroe','2022-10-18 23:21:41'),(1410,145,'Lawrence','2022-10-18 23:21:41'),(1411,145,'Greene','2022-10-18 23:21:41'),(1412,145,'Owen','2022-10-18 23:21:41'),(1413,145,'Orange','2022-10-18 23:21:41'),(1414,145,'Daviess','2022-10-18 23:21:41'),(1415,145,'Knox','2022-10-18 23:21:41'),(1416,145,'Dubois','2022-10-18 23:21:41'),(1417,145,'Perry','2022-10-18 23:21:41'),(1418,145,'Martin','2022-10-18 23:21:41'),(1419,145,'Spencer','2022-10-18 23:21:41'),(1420,145,'Pike','2022-10-18 23:21:41'),(1421,145,'Warrick','2022-10-18 23:21:41'),(1422,145,'Posey','2022-10-18 23:21:41'),(1423,145,'Vanderburgh','2022-10-18 23:21:41'),(1424,145,'Gibson','2022-10-18 23:21:41'),(1425,145,'Vigo','2022-10-18 23:21:41'),(1426,145,'Parke','2022-10-18 23:21:41'),(1427,145,'Vermillion','2022-10-18 23:21:41'),(1428,145,'Clay','2022-10-18 23:21:41'),(1429,145,'Sullivan','2022-10-18 23:21:41'),(1430,145,'Tippecanoe','2022-10-18 23:21:41'),(1431,145,'Montgomery','2022-10-18 23:21:41'),(1432,145,'Benton','2022-10-18 23:21:41'),(1433,145,'Fountain','2022-10-18 23:21:41'),(1434,145,'White','2022-10-18 23:21:41'),(1435,145,'Warren','2022-10-18 23:21:41'),(1436,154,'Saint Clair','2022-10-18 23:21:41'),(1437,154,'Lapeer','2022-10-18 23:21:41'),(1438,154,'Macomb','2022-10-18 23:21:41'),(1439,154,'Oakland','2022-10-18 23:21:41'),(1440,154,'Wayne','2022-10-18 23:21:41'),(1441,154,'Washtenaw','2022-10-18 23:21:41'),(1442,154,'Monroe','2022-10-18 23:21:41'),(1443,154,'Livingston','2022-10-18 23:21:41'),(1444,154,'Sanilac','2022-10-18 23:21:41'),(1445,154,'Genesee','2022-10-18 23:21:41'),(1446,154,'Huron','2022-10-18 23:21:41'),(1447,154,'Shiawassee','2022-10-18 23:21:41'),(1448,154,'Saginaw','2022-10-18 23:21:41'),(1449,154,'Tuscola','2022-10-18 23:21:41'),(1450,154,'Ogemaw','2022-10-18 23:21:41'),(1451,154,'Bay','2022-10-18 23:21:41'),(1452,154,'Gladwin','2022-10-18 23:21:41'),(1453,154,'Gratiot','2022-10-18 23:21:41'),(1454,154,'Clare','2022-10-18 23:21:41'),(1455,154,'Midland','2022-10-18 23:21:41'),(1456,154,'Oscoda','2022-10-18 23:21:41'),(1457,154,'Roscommon','2022-10-18 23:21:41'),(1458,154,'Arenac','2022-10-18 23:21:41'),(1459,154,'Alcona','2022-10-18 23:21:41'),(1460,154,'Iosco','2022-10-18 23:21:41'),(1461,154,'Isabella','2022-10-18 23:21:41'),(1462,154,'Ingham','2022-10-18 23:21:41'),(1463,154,'Clinton','2022-10-18 23:21:41'),(1464,154,'Ionia','2022-10-18 23:21:41'),(1465,154,'Montcalm','2022-10-18 23:21:41'),(1466,154,'Eaton','2022-10-18 23:21:41'),(1467,154,'Barry','2022-10-18 23:21:41'),(1468,154,'Kalamazoo','2022-10-18 23:21:41'),(1469,154,'Allegan','2022-10-18 23:21:41'),(1470,154,'Calhoun','2022-10-18 23:21:41'),(1471,154,'Van Buren','2022-10-18 23:21:41'),(1472,154,'Berrien','2022-10-18 23:21:41'),(1473,154,'Branch','2022-10-18 23:21:41'),(1474,154,'Saint Joseph','2022-10-18 23:21:41'),(1475,154,'Cass','2022-10-18 23:21:41'),(1476,154,'Jackson','2022-10-18 23:21:41'),(1477,154,'Lenawee','2022-10-18 23:21:41'),(1478,154,'Hillsdale','2022-10-18 23:21:41'),(1479,154,'Kent','2022-10-18 23:21:41'),(1480,154,'Muskegon','2022-10-18 23:21:41'),(1481,154,'Lake','2022-10-18 23:21:41'),(1482,154,'Mecosta','2022-10-18 23:21:41'),(1483,154,'Newaygo','2022-10-18 23:21:41'),(1484,154,'Ottawa','2022-10-18 23:21:41'),(1485,154,'Mason','2022-10-18 23:21:41'),(1486,154,'Oceana','2022-10-18 23:21:41'),(1487,154,'Wexford','2022-10-18 23:21:41'),(1488,154,'Grand Traverse','2022-10-18 23:21:41'),(1489,154,'Antrim','2022-10-18 23:21:41'),(1490,154,'Manistee','2022-10-18 23:21:41'),(1491,154,'Benzie','2022-10-18 23:21:41'),(1492,154,'Leelanau','2022-10-18 23:21:41'),(1493,154,'Osceola','2022-10-18 23:21:41'),(1494,154,'Missaukee','2022-10-18 23:21:41'),(1495,154,'Kalkaska','2022-10-18 23:21:41'),(1496,154,'Cheboygan','2022-10-18 23:21:41'),(1497,154,'Emmet','2022-10-18 23:21:41'),(1498,154,'Alpena','2022-10-18 23:21:41'),(1499,154,'Montmorency','2022-10-18 23:21:41'),(1500,154,'Chippewa','2022-10-18 23:21:41'),(1501,154,'Charlevoix','2022-10-18 23:21:41'),(1502,154,'Mackinac','2022-10-18 23:21:41'),(1503,154,'Otsego','2022-10-18 23:21:41'),(1504,154,'Crawford','2022-10-18 23:21:41'),(1505,154,'Presque Isle','2022-10-18 23:21:41'),(1506,154,'Dickinson','2022-10-18 23:21:41'),(1507,154,'Keweenaw','2022-10-18 23:21:41'),(1508,154,'Alger','2022-10-18 23:21:41'),(1509,154,'Delta','2022-10-18 23:21:41'),(1510,154,'Marquette','2022-10-18 23:21:41'),(1511,154,'Menominee','2022-10-18 23:21:41'),(1512,154,'Schoolcraft','2022-10-18 23:21:41'),(1513,154,'Luce','2022-10-18 23:21:41'),(1514,154,'Iron','2022-10-18 23:21:41'),(1515,154,'Houghton','2022-10-18 23:21:41'),(1516,154,'Baraga','2022-10-18 23:21:41'),(1517,154,'Ontonagon','2022-10-18 23:21:41'),(1518,154,'Gogebic','2022-10-18 23:21:41'),(1519,146,'Warren','2022-10-18 23:21:41'),(1520,146,'Adair','2022-10-18 23:21:41'),(1521,146,'Dallas','2022-10-18 23:21:41'),(1522,146,'Marshall','2022-10-18 23:21:41'),(1523,146,'Hardin','2022-10-18 23:21:41'),(1524,146,'Polk','2022-10-18 23:21:41'),(1525,146,'Wayne','2022-10-18 23:21:41'),(1526,146,'Story','2022-10-18 23:21:41'),(1527,146,'Cass','2022-10-18 23:21:41'),(1528,146,'Audubon','2022-10-18 23:21:41'),(1529,146,'Guthrie','2022-10-18 23:21:41'),(1530,146,'Mahaska','2022-10-18 23:21:41'),(1531,146,'Jasper','2022-10-18 23:21:41'),(1532,146,'Boone','2022-10-18 23:21:41'),(1533,146,'Madison','2022-10-18 23:21:41'),(1534,146,'Hamilton','2022-10-18 23:21:41'),(1535,146,'Franklin','2022-10-18 23:21:41'),(1536,146,'Marion','2022-10-18 23:21:41'),(1537,146,'Lucas','2022-10-18 23:21:41'),(1538,146,'Greene','2022-10-18 23:21:41'),(1539,146,'Carroll','2022-10-18 23:21:41'),(1540,146,'Decatur','2022-10-18 23:21:41'),(1541,146,'Wright','2022-10-18 23:21:41'),(1542,146,'Ringgold','2022-10-18 23:21:41'),(1543,146,'Keokuk','2022-10-18 23:21:41'),(1544,146,'Poweshiek','2022-10-18 23:21:41'),(1545,146,'Union','2022-10-18 23:21:41'),(1546,146,'Monroe','2022-10-18 23:21:41'),(1547,146,'Tama','2022-10-18 23:21:41'),(1548,146,'Clarke','2022-10-18 23:21:41'),(1549,146,'Cerro Gordo','2022-10-18 23:21:41'),(1550,146,'Hancock','2022-10-18 23:21:41'),(1551,146,'Winnebago','2022-10-18 23:21:41'),(1552,146,'Mitchell','2022-10-18 23:21:41'),(1553,146,'Worth','2022-10-18 23:21:41'),(1554,146,'Floyd','2022-10-18 23:21:41'),(1555,146,'Kossuth','2022-10-18 23:21:41'),(1556,146,'Howard','2022-10-18 23:21:41'),(1557,146,'Webster','2022-10-18 23:21:41'),(1558,146,'Buena Vista','2022-10-18 23:21:41'),(1559,146,'Emmet','2022-10-18 23:21:41'),(1560,146,'Palo Alto','2022-10-18 23:21:41'),(1561,146,'Humboldt','2022-10-18 23:21:41'),(1562,146,'Sac','2022-10-18 23:21:41'),(1563,146,'Calhoun','2022-10-18 23:21:41'),(1564,146,'Pocahontas','2022-10-18 23:21:41'),(1565,146,'Butler','2022-10-18 23:21:41'),(1566,146,'Chickasaw','2022-10-18 23:21:41'),(1567,146,'Fayette','2022-10-18 23:21:41'),(1568,146,'Buchanan','2022-10-18 23:21:41'),(1569,146,'Grundy','2022-10-18 23:21:41'),(1570,146,'Black Hawk','2022-10-18 23:21:41'),(1571,146,'Bremer','2022-10-18 23:21:41'),(1572,146,'Delaware','2022-10-18 23:21:41'),(1573,146,'Taylor','2022-10-18 23:21:41'),(1574,146,'Adams','2022-10-18 23:21:41'),(1575,146,'Montgomery','2022-10-18 23:21:41'),(1576,146,'Plymouth','2022-10-18 23:21:41'),(1577,146,'Sioux','2022-10-18 23:21:41'),(1578,146,'Woodbury','2022-10-18 23:21:41'),(1579,146,'Cherokee','2022-10-18 23:21:41'),(1580,146,'Ida','2022-10-18 23:21:41'),(1581,146,'Obrien','2022-10-18 23:21:41'),(1582,146,'Monona','2022-10-18 23:21:41'),(1583,146,'Clay','2022-10-18 23:21:41'),(1584,146,'Lyon','2022-10-18 23:21:41'),(1585,146,'Osceola','2022-10-18 23:21:41'),(1586,146,'Dickinson','2022-10-18 23:21:41'),(1587,146,'Crawford','2022-10-18 23:21:41'),(1588,146,'Shelby','2022-10-18 23:21:41'),(1589,146,'Pottawattamie','2022-10-18 23:21:41'),(1590,146,'Harrison','2022-10-18 23:21:41'),(1591,146,'Mills','2022-10-18 23:21:41'),(1592,146,'Page','2022-10-18 23:21:41'),(1593,146,'Fremont','2022-10-18 23:21:41'),(1594,146,'Dubuque','2022-10-18 23:21:41'),(1595,146,'Jackson','2022-10-18 23:21:41'),(1596,146,'Clinton','2022-10-18 23:21:41'),(1597,146,'Clayton','2022-10-18 23:21:41'),(1598,146,'Winneshiek','2022-10-18 23:21:41'),(1599,146,'Allamakee','2022-10-18 23:21:41'),(1600,146,'Washington','2022-10-18 23:21:41'),(1601,146,'Linn','2022-10-18 23:21:41'),(1602,146,'Iowa','2022-10-18 23:21:41'),(1603,146,'Jones','2022-10-18 23:21:41'),(1604,146,'Benton','2022-10-18 23:21:41'),(1605,146,'Cedar','2022-10-18 23:21:41'),(1606,146,'Johnson','2022-10-18 23:21:41'),(1607,146,'Wapello','2022-10-18 23:21:41'),(1608,146,'Jefferson','2022-10-18 23:21:41'),(1609,146,'Van Buren','2022-10-18 23:21:41'),(1610,146,'Davis','2022-10-18 23:21:41'),(1611,146,'Appanoose','2022-10-18 23:21:41'),(1612,146,'Des Moines','2022-10-18 23:21:41'),(1613,146,'Lee','2022-10-18 23:21:41'),(1614,146,'Henry','2022-10-18 23:21:41'),(1615,146,'Louisa','2022-10-18 23:21:41'),(1616,146,'Muscatine','2022-10-18 23:21:41'),(1617,146,'Scott','2022-10-18 23:21:41'),(1618,185,'Sheboygan','2022-10-18 23:21:41'),(1619,185,'Washington','2022-10-18 23:21:41'),(1620,185,'Dodge','2022-10-18 23:21:41'),(1621,185,'Ozaukee','2022-10-18 23:21:41'),(1622,185,'Waukesha','2022-10-18 23:21:41'),(1623,185,'Fond Du Lac','2022-10-18 23:21:41'),(1624,185,'Calumet','2022-10-18 23:21:41'),(1625,185,'Manitowoc','2022-10-18 23:21:41'),(1626,185,'Jefferson','2022-10-18 23:21:41'),(1627,185,'Kenosha','2022-10-18 23:21:41'),(1628,185,'Racine','2022-10-18 23:21:41'),(1629,185,'Milwaukee','2022-10-18 23:21:41'),(1630,185,'Walworth','2022-10-18 23:21:41'),(1631,185,'Rock','2022-10-18 23:21:41'),(1632,185,'Green','2022-10-18 23:21:41'),(1633,185,'Iowa','2022-10-18 23:21:41'),(1634,185,'Lafayette','2022-10-18 23:21:41'),(1635,185,'Dane','2022-10-18 23:21:41'),(1636,185,'Grant','2022-10-18 23:21:41'),(1637,185,'Richland','2022-10-18 23:21:41'),(1638,185,'Columbia','2022-10-18 23:21:41'),(1639,185,'Sauk','2022-10-18 23:21:41'),(1640,185,'Crawford','2022-10-18 23:21:41'),(1641,185,'Adams','2022-10-18 23:21:41'),(1642,185,'Marquette','2022-10-18 23:21:41'),(1643,185,'Green Lake','2022-10-18 23:21:41'),(1644,185,'Juneau','2022-10-18 23:21:41'),(1645,185,'Polk','2022-10-18 23:21:41'),(1646,185,'Saint Croix','2022-10-18 23:21:41'),(1647,185,'Pierce','2022-10-18 23:21:41'),(1648,185,'Oconto','2022-10-18 23:21:41'),(1649,185,'Marinette','2022-10-18 23:21:41'),(1650,185,'Forest','2022-10-18 23:21:41'),(1651,185,'Outagamie','2022-10-18 23:21:41'),(1652,185,'Shawano','2022-10-18 23:21:41'),(1653,185,'Brown','2022-10-18 23:21:41'),(1654,185,'Florence','2022-10-18 23:21:41'),(1655,185,'Menominee','2022-10-18 23:21:41'),(1656,185,'Kewaunee','2022-10-18 23:21:41'),(1657,185,'Door','2022-10-18 23:21:41'),(1658,185,'Marathon','2022-10-18 23:21:41'),(1659,185,'Wood','2022-10-18 23:21:41'),(1660,185,'Clark','2022-10-18 23:21:41'),(1661,185,'Portage','2022-10-18 23:21:41'),(1662,185,'Langlade','2022-10-18 23:21:41'),(1663,185,'Taylor','2022-10-18 23:21:41'),(1664,185,'Lincoln','2022-10-18 23:21:41'),(1665,185,'Price','2022-10-18 23:21:41'),(1666,185,'Oneida','2022-10-18 23:21:41'),(1667,185,'Vilas','2022-10-18 23:21:41'),(1668,185,'Ashland','2022-10-18 23:21:41'),(1669,185,'Iron','2022-10-18 23:21:41'),(1670,185,'Rusk','2022-10-18 23:21:41'),(1671,185,'La Crosse','2022-10-18 23:21:41'),(1672,185,'Buffalo','2022-10-18 23:21:41'),(1673,185,'Jackson','2022-10-18 23:21:41'),(1674,185,'Trempealeau','2022-10-18 23:21:41'),(1675,185,'Monroe','2022-10-18 23:21:41'),(1676,185,'Vernon','2022-10-18 23:21:41'),(1677,185,'Eau Claire','2022-10-18 23:21:41'),(1678,185,'Pepin','2022-10-18 23:21:41'),(1679,185,'Chippewa','2022-10-18 23:21:41'),(1680,185,'Dunn','2022-10-18 23:21:41'),(1681,185,'Barron','2022-10-18 23:21:41'),(1682,185,'Washburn','2022-10-18 23:21:41'),(1683,185,'Bayfield','2022-10-18 23:21:41'),(1684,185,'Douglas','2022-10-18 23:21:41'),(1685,185,'Sawyer','2022-10-18 23:21:41'),(1686,185,'Burnett','2022-10-18 23:21:41'),(1687,185,'Winnebago','2022-10-18 23:21:41'),(1688,185,'Waupaca','2022-10-18 23:21:41'),(1689,185,'Waushara','2022-10-18 23:21:41'),(1690,155,'Washington','2022-10-18 23:21:41'),(1691,155,'Chisago','2022-10-18 23:21:41'),(1692,155,'Anoka','2022-10-18 23:21:41'),(1693,155,'Isanti','2022-10-18 23:21:41'),(1694,155,'Pine','2022-10-18 23:21:41'),(1695,155,'Goodhue','2022-10-18 23:21:41'),(1696,155,'Dakota','2022-10-18 23:21:41'),(1697,155,'Rice','2022-10-18 23:21:41'),(1698,155,'Scott','2022-10-18 23:21:41'),(1699,155,'Wabasha','2022-10-18 23:21:41'),(1700,155,'Steele','2022-10-18 23:21:41'),(1701,155,'Kanabec','2022-10-18 23:21:41'),(1702,155,'Ramsey','2022-10-18 23:21:41'),(1703,155,'Hennepin','2022-10-18 23:21:41'),(1704,155,'Wright','2022-10-18 23:21:41'),(1705,155,'Sibley','2022-10-18 23:21:41'),(1706,155,'Sherburne','2022-10-18 23:21:41'),(1707,155,'Renville','2022-10-18 23:21:41'),(1708,155,'McLeod','2022-10-18 23:21:41'),(1709,155,'Carver','2022-10-18 23:21:41'),(1710,155,'Meeker','2022-10-18 23:21:41'),(1711,155,'Stearns','2022-10-18 23:21:41'),(1712,155,'Mille Lacs','2022-10-18 23:21:41'),(1713,155,'Lake','2022-10-18 23:21:41'),(1714,155,'Saint Louis','2022-10-18 23:21:41'),(1715,155,'Cook','2022-10-18 23:21:41'),(1716,155,'Carlton','2022-10-18 23:21:41'),(1717,155,'Itasca','2022-10-18 23:21:41'),(1718,155,'Aitkin','2022-10-18 23:21:41'),(1719,155,'Olmsted','2022-10-18 23:21:41'),(1720,155,'Winona','2022-10-18 23:21:41'),(1721,155,'Houston','2022-10-18 23:21:41'),(1722,155,'Fillmore','2022-10-18 23:21:41'),(1723,155,'Dodge','2022-10-18 23:21:41'),(1724,155,'Blue Earth','2022-10-18 23:21:41'),(1725,155,'Nicollet','2022-10-18 23:21:41'),(1726,155,'Freeborn','2022-10-18 23:21:41'),(1727,155,'Faribault','2022-10-18 23:21:41'),(1728,155,'Le Sueur','2022-10-18 23:21:41'),(1729,155,'Brown','2022-10-18 23:21:41'),(1730,155,'Watonwan','2022-10-18 23:21:41'),(1731,155,'Martin','2022-10-18 23:21:41'),(1732,155,'Waseca','2022-10-18 23:21:41'),(1733,155,'Redwood','2022-10-18 23:21:41'),(1734,155,'Cottonwood','2022-10-18 23:21:41'),(1735,155,'Nobles','2022-10-18 23:21:41'),(1736,155,'Jackson','2022-10-18 23:21:41'),(1737,155,'Lincoln','2022-10-18 23:21:41'),(1738,155,'Murray','2022-10-18 23:21:41'),(1739,155,'Lyon','2022-10-18 23:21:41'),(1740,155,'Rock','2022-10-18 23:21:41'),(1741,155,'Pipestone','2022-10-18 23:21:41'),(1742,155,'Kandiyohi','2022-10-18 23:21:41'),(1743,155,'Stevens','2022-10-18 23:21:41'),(1744,155,'Swift','2022-10-18 23:21:41'),(1745,155,'Big Stone','2022-10-18 23:21:41'),(1746,155,'Lac Qui Parle','2022-10-18 23:21:41'),(1747,155,'Traverse','2022-10-18 23:21:41'),(1748,155,'Yellow Medicine','2022-10-18 23:21:41'),(1749,155,'Chippewa','2022-10-18 23:21:41'),(1750,155,'Grant','2022-10-18 23:21:41'),(1751,155,'Douglas','2022-10-18 23:21:41'),(1752,155,'Morrison','2022-10-18 23:21:41'),(1753,155,'Todd','2022-10-18 23:21:41'),(1754,155,'Pope','2022-10-18 23:21:41'),(1755,155,'Otter Tail','2022-10-18 23:21:41'),(1756,155,'Benton','2022-10-18 23:21:41'),(1757,155,'Crow Wing','2022-10-18 23:21:41'),(1758,155,'Cass','2022-10-18 23:21:41'),(1759,155,'Hubbard','2022-10-18 23:21:41'),(1760,155,'Wadena','2022-10-18 23:21:41'),(1761,155,'Becker','2022-10-18 23:21:41'),(1762,155,'Norman','2022-10-18 23:21:41'),(1763,155,'Clay','2022-10-18 23:21:41'),(1764,155,'Mahnomen','2022-10-18 23:21:41'),(1765,155,'Polk','2022-10-18 23:21:41'),(1766,155,'Wilkin','2022-10-18 23:21:41'),(1767,155,'Beltrami','2022-10-18 23:21:41'),(1768,155,'Clearwater','2022-10-18 23:21:41'),(1769,155,'Lake of the Woods','2022-10-18 23:21:41'),(1770,155,'Koochiching','2022-10-18 23:21:41'),(1771,155,'Roseau','2022-10-18 23:21:41'),(1772,155,'Pennington','2022-10-18 23:21:41'),(1773,155,'Marshall','2022-10-18 23:21:41'),(1774,155,'Red Lake','2022-10-18 23:21:41'),(1775,155,'Kittson','2022-10-18 23:21:41'),(1776,176,'Union','2022-10-18 23:21:41'),(1777,176,'Brookings','2022-10-18 23:21:41'),(1778,176,'Minnehaha','2022-10-18 23:21:41'),(1779,176,'Clay','2022-10-18 23:21:41'),(1780,176,'McCook','2022-10-18 23:21:41'),(1781,176,'Lincoln','2022-10-18 23:21:41'),(1782,176,'Turner','2022-10-18 23:21:41'),(1783,176,'Lake','2022-10-18 23:21:41'),(1784,176,'Moody','2022-10-18 23:21:41'),(1785,176,'Hutchinson','2022-10-18 23:21:41'),(1786,176,'Yankton','2022-10-18 23:21:41'),(1787,176,'Kingsbury','2022-10-18 23:21:41'),(1788,176,'Bon Homme','2022-10-18 23:21:41'),(1789,176,'Codington','2022-10-18 23:21:41'),(1790,176,'Deuel','2022-10-18 23:21:41'),(1791,176,'Grant','2022-10-18 23:21:41'),(1792,176,'Clark','2022-10-18 23:21:41'),(1793,176,'Day','2022-10-18 23:21:41'),(1794,176,'Hamlin','2022-10-18 23:21:41'),(1795,176,'Roberts','2022-10-18 23:21:41'),(1796,176,'Marshall','2022-10-18 23:21:41'),(1797,176,'Davison','2022-10-18 23:21:41'),(1798,176,'Hanson','2022-10-18 23:21:41'),(1799,176,'Jerauld','2022-10-18 23:21:41'),(1800,176,'Douglas','2022-10-18 23:21:41'),(1801,176,'Sanborn','2022-10-18 23:21:41'),(1802,176,'Gregory','2022-10-18 23:21:41'),(1803,176,'Miner','2022-10-18 23:21:41'),(1804,176,'Beadle','2022-10-18 23:21:41'),(1805,176,'Brule','2022-10-18 23:21:41'),(1806,176,'Charles Mix','2022-10-18 23:21:41'),(1807,176,'Buffalo','2022-10-18 23:21:41'),(1808,176,'Hyde','2022-10-18 23:21:41'),(1809,176,'Hand','2022-10-18 23:21:41'),(1810,176,'Lyman','2022-10-18 23:21:41'),(1811,176,'Aurora','2022-10-18 23:21:41'),(1812,176,'Brown','2022-10-18 23:21:41'),(1813,176,'Walworth','2022-10-18 23:21:41'),(1814,176,'Spink','2022-10-18 23:21:41'),(1815,176,'Edmunds','2022-10-18 23:21:41'),(1816,176,'Faulk','2022-10-18 23:21:41'),(1817,176,'McPherson','2022-10-18 23:21:41'),(1818,176,'Potter','2022-10-18 23:21:41'),(1819,176,'Hughes','2022-10-18 23:21:41'),(1820,176,'Sully','2022-10-18 23:21:41'),(1821,176,'Jackson','2022-10-18 23:21:41'),(1822,176,'Tripp','2022-10-18 23:21:41'),(1823,176,'Jones','2022-10-18 23:21:41'),(1824,176,'Stanley','2022-10-18 23:21:41'),(1825,176,'Bennett','2022-10-18 23:21:41'),(1826,176,'Haakon','2022-10-18 23:21:41'),(1827,176,'Todd','2022-10-18 23:21:41'),(1828,176,'Mellette','2022-10-18 23:21:41'),(1829,176,'Perkins','2022-10-18 23:21:41'),(1830,176,'Corson','2022-10-18 23:21:41'),(1831,176,'Ziebach','2022-10-18 23:21:41'),(1832,176,'Dewey','2022-10-18 23:21:41'),(1833,176,'Meade','2022-10-18 23:21:41'),(1834,176,'Campbell','2022-10-18 23:21:41'),(1835,176,'Harding','2022-10-18 23:21:41'),(1836,176,'Pennington','2022-10-18 23:21:41'),(1837,176,'Shannon','2022-10-18 23:21:41'),(1838,176,'Butte','2022-10-18 23:21:41'),(1839,176,'Custer','2022-10-18 23:21:41'),(1840,176,'Lawrence','2022-10-18 23:21:41'),(1841,176,'Fall River','2022-10-18 23:21:41'),(1842,166,'Richland','2022-10-18 23:21:41'),(1843,166,'Cass','2022-10-18 23:21:41'),(1844,166,'Traill','2022-10-18 23:21:41'),(1845,166,'Sargent','2022-10-18 23:21:41'),(1846,166,'Ransom','2022-10-18 23:21:41'),(1847,166,'Barnes','2022-10-18 23:21:41'),(1848,166,'Steele','2022-10-18 23:21:41'),(1849,166,'Grand Forks','2022-10-18 23:21:41'),(1850,166,'Walsh','2022-10-18 23:21:41'),(1851,166,'Nelson','2022-10-18 23:21:41'),(1852,166,'Pembina','2022-10-18 23:21:41'),(1853,166,'Cavalier','2022-10-18 23:21:41'),(1854,166,'Ramsey','2022-10-18 23:21:41'),(1855,166,'Rolette','2022-10-18 23:21:41'),(1856,166,'Pierce','2022-10-18 23:21:41'),(1857,166,'Towner','2022-10-18 23:21:41'),(1858,166,'Bottineau','2022-10-18 23:21:41'),(1859,166,'Wells','2022-10-18 23:21:41'),(1860,166,'Benson','2022-10-18 23:21:41'),(1861,166,'Eddy','2022-10-18 23:21:41'),(1862,166,'Stutsman','2022-10-18 23:21:41'),(1863,166,'McIntosh','2022-10-18 23:21:41'),(1864,166,'Lamoure','2022-10-18 23:21:41'),(1865,166,'Griggs','2022-10-18 23:21:41'),(1866,166,'Foster','2022-10-18 23:21:41'),(1867,166,'Kidder','2022-10-18 23:21:41'),(1868,166,'Sheridan','2022-10-18 23:21:41'),(1869,166,'Dickey','2022-10-18 23:21:41'),(1870,166,'Logan','2022-10-18 23:21:41'),(1871,166,'Burleigh','2022-10-18 23:21:41'),(1872,166,'Morton','2022-10-18 23:21:41'),(1873,166,'Mercer','2022-10-18 23:21:41'),(1874,166,'Emmons','2022-10-18 23:21:41'),(1875,166,'Sioux','2022-10-18 23:21:41'),(1876,166,'Grant','2022-10-18 23:21:41'),(1877,166,'Oliver','2022-10-18 23:21:41'),(1878,166,'McLean','2022-10-18 23:21:41'),(1879,166,'Stark','2022-10-18 23:21:41'),(1880,166,'Slope','2022-10-18 23:21:41'),(1881,166,'Golden Valley','2022-10-18 23:21:41'),(1882,166,'Bowman','2022-10-18 23:21:41'),(1883,166,'Dunn','2022-10-18 23:21:41'),(1884,166,'Billings','2022-10-18 23:21:41'),(1885,166,'McKenzie','2022-10-18 23:21:41'),(1886,166,'Adams','2022-10-18 23:21:41'),(1887,166,'Hettinger','2022-10-18 23:21:41'),(1888,166,'Ward','2022-10-18 23:21:41'),(1889,166,'McHenry','2022-10-18 23:21:41'),(1890,166,'Burke','2022-10-18 23:21:41'),(1891,166,'Divide','2022-10-18 23:21:41'),(1892,166,'Renville','2022-10-18 23:21:41'),(1893,166,'Williams','2022-10-18 23:21:41'),(1894,166,'Mountrail','2022-10-18 23:21:41'),(1895,158,'Stillwater','2022-10-18 23:21:41'),(1896,158,'Yellowstone','2022-10-18 23:21:41'),(1897,158,'Rosebud','2022-10-18 23:21:41'),(1898,158,'Carbon','2022-10-18 23:21:41'),(1899,158,'Treasure','2022-10-18 23:21:41'),(1900,158,'Sweet Grass','2022-10-18 23:21:41'),(1901,158,'Big Horn','2022-10-18 23:21:41'),(1902,158,'Park','2022-10-18 23:21:41'),(1903,158,'Fergus','2022-10-18 23:21:41'),(1904,158,'Wheatland','2022-10-18 23:21:41'),(1905,158,'Golden Valley','2022-10-18 23:21:41'),(1906,158,'Meagher','2022-10-18 23:21:41'),(1907,158,'Musselshell','2022-10-18 23:21:41'),(1908,158,'Garfield','2022-10-18 23:21:41'),(1909,158,'Powder River','2022-10-18 23:21:41'),(1910,158,'Petroleum','2022-10-18 23:21:41'),(1911,158,'Roosevelt','2022-10-18 23:21:41'),(1912,158,'Sheridan','2022-10-18 23:21:41'),(1913,158,'McCone','2022-10-18 23:21:41'),(1914,158,'Richland','2022-10-18 23:21:41'),(1915,158,'Daniels','2022-10-18 23:21:41'),(1916,158,'Valley','2022-10-18 23:21:41'),(1917,158,'Dawson','2022-10-18 23:21:41'),(1918,158,'Phillips','2022-10-18 23:21:41'),(1919,158,'Custer','2022-10-18 23:21:41'),(1920,158,'Carter','2022-10-18 23:21:41'),(1921,158,'Fallon','2022-10-18 23:21:41'),(1922,158,'Prairie','2022-10-18 23:21:41'),(1923,158,'Wibaux','2022-10-18 23:21:41'),(1924,158,'Cascade','2022-10-18 23:21:41'),(1925,158,'Lewis And Clark','2022-10-18 23:21:41'),(1926,158,'Pondera','2022-10-18 23:21:41'),(1927,158,'Teton','2022-10-18 23:21:41'),(1928,158,'Chouteau','2022-10-18 23:21:41'),(1929,158,'Toole','2022-10-18 23:21:41'),(1930,158,'Judith Basin','2022-10-18 23:21:41'),(1931,158,'Liberty','2022-10-18 23:21:41'),(1932,158,'Hill','2022-10-18 23:21:41'),(1933,158,'Blaine','2022-10-18 23:21:41'),(1934,158,'Jefferson','2022-10-18 23:21:41'),(1935,158,'Broadwater','2022-10-18 23:21:41'),(1936,158,'Silver Bow','2022-10-18 23:21:41'),(1937,158,'Madison','2022-10-18 23:21:41'),(1938,158,'Deer Lodge','2022-10-18 23:21:41'),(1939,158,'Powell','2022-10-18 23:21:41'),(1940,158,'Gallatin','2022-10-18 23:21:41'),(1941,158,'Beaverhead','2022-10-18 23:21:41'),(1942,158,'Missoula','2022-10-18 23:21:41'),(1943,158,'Mineral','2022-10-18 23:21:41'),(1944,158,'Lake','2022-10-18 23:21:41'),(1945,158,'Ravalli','2022-10-18 23:21:41'),(1946,158,'Sanders','2022-10-18 23:21:41'),(1947,158,'Granite','2022-10-18 23:21:41'),(1948,158,'Flathead','2022-10-18 23:21:41'),(1949,158,'Lincoln','2022-10-18 23:21:41'),(1950,144,'McHenry','2022-10-18 23:21:41'),(1951,144,'Lake','2022-10-18 23:21:41'),(1952,144,'Cook','2022-10-18 23:21:41'),(1953,144,'Du Page','2022-10-18 23:21:41'),(1954,144,'Kane','2022-10-18 23:21:41'),(1955,144,'De Kalb','2022-10-18 23:21:41'),(1956,144,'Ogle','2022-10-18 23:21:41'),(1957,144,'Will','2022-10-18 23:21:41'),(1958,144,'Grundy','2022-10-18 23:21:41'),(1959,144,'Livingston','2022-10-18 23:21:41'),(1960,144,'La Salle','2022-10-18 23:21:41'),(1961,144,'Kendall','2022-10-18 23:21:41'),(1962,144,'Lee','2022-10-18 23:21:41'),(1963,144,'Kankakee','2022-10-18 23:21:41'),(1964,144,'Iroquois','2022-10-18 23:21:41'),(1965,144,'Ford','2022-10-18 23:21:41'),(1966,144,'Vermilion','2022-10-18 23:21:41'),(1967,144,'Champaign','2022-10-18 23:21:41'),(1968,144,'Jo Daviess','2022-10-18 23:21:41'),(1969,144,'Boone','2022-10-18 23:21:41'),(1970,144,'Stephenson','2022-10-18 23:21:41'),(1971,144,'Carroll','2022-10-18 23:21:41'),(1972,144,'Winnebago','2022-10-18 23:21:41'),(1973,144,'Whiteside','2022-10-18 23:21:41'),(1974,144,'Rock Island','2022-10-18 23:21:41'),(1975,144,'Mercer','2022-10-18 23:21:41'),(1976,144,'Henry','2022-10-18 23:21:41'),(1977,144,'Bureau','2022-10-18 23:21:41'),(1978,144,'Putnam','2022-10-18 23:21:41'),(1979,144,'Marshall','2022-10-18 23:21:41'),(1980,144,'Knox','2022-10-18 23:21:41'),(1981,144,'McDonough','2022-10-18 23:21:41'),(1982,144,'Fulton','2022-10-18 23:21:41'),(1983,144,'Warren','2022-10-18 23:21:41'),(1984,144,'Henderson','2022-10-18 23:21:41'),(1985,144,'Stark','2022-10-18 23:21:41'),(1986,144,'Hancock','2022-10-18 23:21:41'),(1987,144,'Peoria','2022-10-18 23:21:41'),(1988,144,'Schuyler','2022-10-18 23:21:41'),(1989,144,'Woodford','2022-10-18 23:21:41'),(1990,144,'Mason','2022-10-18 23:21:41'),(1991,144,'Tazewell','2022-10-18 23:21:41'),(1992,144,'McLean','2022-10-18 23:21:41'),(1993,144,'Logan','2022-10-18 23:21:41'),(1994,144,'Dewitt','2022-10-18 23:21:41'),(1995,144,'Macon','2022-10-18 23:21:41'),(1996,144,'Piatt','2022-10-18 23:21:41'),(1997,144,'Douglas','2022-10-18 23:21:41'),(1998,144,'Coles','2022-10-18 23:21:41'),(1999,144,'Moultrie','2022-10-18 23:21:41'),(2000,144,'Edgar','2022-10-18 23:21:41'),(2001,144,'Shelby','2022-10-18 23:21:41'),(2002,144,'Madison','2022-10-18 23:21:41'),(2003,144,'Calhoun','2022-10-18 23:21:41'),(2004,144,'Macoupin','2022-10-18 23:21:41'),(2005,144,'Fayette','2022-10-18 23:21:41'),(2006,144,'Jersey','2022-10-18 23:21:41'),(2007,144,'Montgomery','2022-10-18 23:21:41'),(2008,144,'Greene','2022-10-18 23:21:41'),(2009,144,'Bond','2022-10-18 23:21:41'),(2010,144,'Saint Clair','2022-10-18 23:21:41'),(2011,144,'Christian','2022-10-18 23:21:41'),(2012,144,'Washington','2022-10-18 23:21:41'),(2013,144,'Clinton','2022-10-18 23:21:41'),(2014,144,'Randolph','2022-10-18 23:21:41'),(2015,144,'Monroe','2022-10-18 23:21:41'),(2016,144,'Perry','2022-10-18 23:21:41'),(2017,144,'Adams','2022-10-18 23:21:41'),(2018,144,'Pike','2022-10-18 23:21:41'),(2019,144,'Brown','2022-10-18 23:21:41'),(2020,144,'Effingham','2022-10-18 23:21:41'),(2021,144,'Wabash','2022-10-18 23:21:41'),(2022,144,'Crawford','2022-10-18 23:21:41'),(2023,144,'Lawrence','2022-10-18 23:21:41'),(2024,144,'Richland','2022-10-18 23:21:41'),(2025,144,'Clark','2022-10-18 23:21:41'),(2026,144,'Cumberland','2022-10-18 23:21:41'),(2027,144,'Jasper','2022-10-18 23:21:41'),(2028,144,'Clay','2022-10-18 23:21:41'),(2029,144,'Wayne','2022-10-18 23:21:41'),(2030,144,'Edwards','2022-10-18 23:21:41'),(2031,144,'Sangamon','2022-10-18 23:21:41'),(2032,144,'Morgan','2022-10-18 23:21:41'),(2033,144,'Scott','2022-10-18 23:21:41'),(2034,144,'Cass','2022-10-18 23:21:41'),(2035,144,'Menard','2022-10-18 23:21:41'),(2036,144,'Marion','2022-10-18 23:21:41'),(2037,144,'Franklin','2022-10-18 23:21:41'),(2038,144,'Jefferson','2022-10-18 23:21:41'),(2039,144,'Hamilton','2022-10-18 23:21:41'),(2040,144,'White','2022-10-18 23:21:41'),(2041,144,'Williamson','2022-10-18 23:21:41'),(2042,144,'Gallatin','2022-10-18 23:21:41'),(2043,144,'Jackson','2022-10-18 23:21:41'),(2044,144,'Union','2022-10-18 23:21:41'),(2045,144,'Johnson','2022-10-18 23:21:41'),(2046,144,'Massac','2022-10-18 23:21:41'),(2047,144,'Alexander','2022-10-18 23:21:41'),(2048,144,'Saline','2022-10-18 23:21:41'),(2049,144,'Hardin','2022-10-18 23:21:41'),(2050,144,'Pope','2022-10-18 23:21:41'),(2051,144,'Pulaski','2022-10-18 23:21:41'),(2052,157,'Saint Louis','2022-10-18 23:21:41'),(2053,157,'Jefferson','2022-10-18 23:21:41'),(2054,157,'Franklin','2022-10-18 23:21:41'),(2055,157,'Saint Francois','2022-10-18 23:21:41'),(2056,157,'Washington','2022-10-18 23:21:41'),(2057,157,'Gasconade','2022-10-18 23:21:41'),(2058,157,'Saint Louis City','2022-10-18 23:21:41'),(2059,157,'Saint Charles','2022-10-18 23:21:41'),(2060,157,'Pike','2022-10-18 23:21:41'),(2061,157,'Montgomery','2022-10-18 23:21:41'),(2062,157,'Warren','2022-10-18 23:21:41'),(2063,157,'Lincoln','2022-10-18 23:21:41'),(2064,157,'Audrain','2022-10-18 23:21:41'),(2065,157,'Callaway','2022-10-18 23:21:41'),(2066,157,'Marion','2022-10-18 23:21:41'),(2067,157,'Clark','2022-10-18 23:21:41'),(2068,157,'Macon','2022-10-18 23:21:41'),(2069,157,'Scotland','2022-10-18 23:21:41'),(2070,157,'Shelby','2022-10-18 23:21:41'),(2071,157,'Lewis','2022-10-18 23:21:41'),(2072,157,'Ralls','2022-10-18 23:21:41'),(2073,157,'Knox','2022-10-18 23:21:41'),(2074,157,'Monroe','2022-10-18 23:21:41'),(2075,157,'Adair','2022-10-18 23:21:41'),(2076,157,'Schuyler','2022-10-18 23:21:41'),(2077,157,'Sullivan','2022-10-18 23:21:41'),(2078,157,'Putnam','2022-10-18 23:21:41'),(2079,157,'Linn','2022-10-18 23:21:41'),(2080,157,'Iron','2022-10-18 23:21:41'),(2081,157,'Reynolds','2022-10-18 23:21:41'),(2082,157,'Sainte Genevieve','2022-10-18 23:21:41'),(2083,157,'Wayne','2022-10-18 23:21:41'),(2084,157,'Madison','2022-10-18 23:21:41'),(2085,157,'Bollinger','2022-10-18 23:21:41'),(2086,157,'Cape Girardeau','2022-10-18 23:21:41'),(2087,157,'Stoddard','2022-10-18 23:21:41'),(2088,157,'Perry','2022-10-18 23:21:41'),(2089,157,'Scott','2022-10-18 23:21:41'),(2090,157,'Mississippi','2022-10-18 23:21:41'),(2091,157,'Dunklin','2022-10-18 23:21:41'),(2092,157,'Pemiscot','2022-10-18 23:21:41'),(2093,157,'New Madrid','2022-10-18 23:21:41'),(2094,157,'Butler','2022-10-18 23:21:41'),(2095,157,'Ripley','2022-10-18 23:21:41'),(2096,157,'Carter','2022-10-18 23:21:41'),(2097,157,'Lafayette','2022-10-18 23:21:41'),(2098,157,'Cass','2022-10-18 23:21:41'),(2099,157,'Jackson','2022-10-18 23:21:41'),(2100,157,'Ray','2022-10-18 23:21:41'),(2101,157,'Platte','2022-10-18 23:21:41'),(2102,157,'Johnson','2022-10-18 23:21:41'),(2103,157,'Clay','2022-10-18 23:21:41'),(2104,157,'Buchanan','2022-10-18 23:21:41'),(2105,157,'Gentry','2022-10-18 23:21:41'),(2106,157,'Worth','2022-10-18 23:21:41'),(2107,157,'Andrew','2022-10-18 23:21:41'),(2108,157,'Dekalb','2022-10-18 23:21:41'),(2109,157,'Nodaway','2022-10-18 23:21:41'),(2110,157,'Harrison','2022-10-18 23:21:41'),(2111,157,'Clinton','2022-10-18 23:21:41'),(2112,157,'Holt','2022-10-18 23:21:41'),(2113,157,'Atchison','2022-10-18 23:21:41'),(2114,157,'Livingston','2022-10-18 23:21:41'),(2115,157,'Daviess','2022-10-18 23:21:41'),(2116,157,'Carroll','2022-10-18 23:21:41'),(2117,157,'Caldwell','2022-10-18 23:21:41'),(2118,157,'Grundy','2022-10-18 23:21:41'),(2119,157,'Chariton','2022-10-18 23:21:41'),(2120,157,'Mercer','2022-10-18 23:21:41'),(2121,157,'Bates','2022-10-18 23:21:41'),(2122,157,'Saint Clair','2022-10-18 23:21:41'),(2123,157,'Henry','2022-10-18 23:21:41'),(2124,157,'Vernon','2022-10-18 23:21:41'),(2125,157,'Cedar','2022-10-18 23:21:41'),(2126,157,'Barton','2022-10-18 23:21:41'),(2127,157,'Jasper','2022-10-18 23:21:41'),(2128,157,'McDonald','2022-10-18 23:21:41'),(2129,157,'Newton','2022-10-18 23:21:41'),(2130,157,'Barry','2022-10-18 23:21:41'),(2131,157,'Osage','2022-10-18 23:21:41'),(2132,157,'Boone','2022-10-18 23:21:41'),(2133,157,'Morgan','2022-10-18 23:21:41'),(2134,157,'Maries','2022-10-18 23:21:41'),(2135,157,'Miller','2022-10-18 23:21:41'),(2136,157,'Moniteau','2022-10-18 23:21:41'),(2137,157,'Camden','2022-10-18 23:21:41'),(2138,157,'Cole','2022-10-18 23:21:41'),(2139,157,'Cooper','2022-10-18 23:21:41'),(2140,157,'Howard','2022-10-18 23:21:41'),(2141,157,'Randolph','2022-10-18 23:21:41'),(2142,157,'Pettis','2022-10-18 23:21:41'),(2143,157,'Saline','2022-10-18 23:21:41'),(2144,157,'Benton','2022-10-18 23:21:41'),(2145,157,'Phelps','2022-10-18 23:21:41'),(2146,157,'Shannon','2022-10-18 23:21:41'),(2147,157,'Dent','2022-10-18 23:21:41'),(2148,157,'Crawford','2022-10-18 23:21:41'),(2149,157,'Texas','2022-10-18 23:21:41'),(2150,157,'Pulaski','2022-10-18 23:21:41'),(2151,157,'Laclede','2022-10-18 23:21:41'),(2152,157,'Howell','2022-10-18 23:21:41'),(2153,157,'Dallas','2022-10-18 23:21:41'),(2154,157,'Polk','2022-10-18 23:21:41'),(2155,157,'Dade','2022-10-18 23:21:41'),(2156,157,'Greene','2022-10-18 23:21:41'),(2157,157,'Lawrence','2022-10-18 23:21:41'),(2158,157,'Oregon','2022-10-18 23:21:41'),(2159,157,'Douglas','2022-10-18 23:21:41'),(2160,157,'Ozark','2022-10-18 23:21:41'),(2161,157,'Christian','2022-10-18 23:21:41'),(2162,157,'Stone','2022-10-18 23:21:41'),(2163,157,'Taney','2022-10-18 23:21:41'),(2164,157,'Hickory','2022-10-18 23:21:41'),(2165,157,'Webster','2022-10-18 23:21:41'),(2166,157,'Wright','2022-10-18 23:21:41'),(2167,147,'Atchison','2022-10-18 23:21:41'),(2168,147,'Douglas','2022-10-18 23:21:41'),(2169,147,'Leavenworth','2022-10-18 23:21:41'),(2170,147,'Doniphan','2022-10-18 23:21:41'),(2171,147,'Linn','2022-10-18 23:21:41'),(2172,147,'Wyandotte','2022-10-18 23:21:41'),(2173,147,'Miami','2022-10-18 23:21:41'),(2174,147,'Anderson','2022-10-18 23:21:41'),(2175,147,'Johnson','2022-10-18 23:21:41'),(2176,147,'Franklin','2022-10-18 23:21:41'),(2177,147,'Jefferson','2022-10-18 23:21:41'),(2178,147,'Wabaunsee','2022-10-18 23:21:41'),(2179,147,'Shawnee','2022-10-18 23:21:41'),(2180,147,'Marshall','2022-10-18 23:21:41'),(2181,147,'Nemaha','2022-10-18 23:21:41'),(2182,147,'Pottawatomie','2022-10-18 23:21:41'),(2183,147,'Osage','2022-10-18 23:21:41'),(2184,147,'Jackson','2022-10-18 23:21:41'),(2185,147,'Brown','2022-10-18 23:21:41'),(2186,147,'Geary','2022-10-18 23:21:41'),(2187,147,'Riley','2022-10-18 23:21:41'),(2188,147,'Bourbon','2022-10-18 23:21:41'),(2189,147,'Wilson','2022-10-18 23:21:41'),(2190,147,'Crawford','2022-10-18 23:21:41'),(2191,147,'Cherokee','2022-10-18 23:21:41'),(2192,147,'Neosho','2022-10-18 23:21:41'),(2193,147,'Allen','2022-10-18 23:21:41'),(2194,147,'Woodson','2022-10-18 23:21:41'),(2195,147,'Lyon','2022-10-18 23:21:41'),(2196,147,'Morris','2022-10-18 23:21:41'),(2197,147,'Coffey','2022-10-18 23:21:41'),(2198,147,'Marion','2022-10-18 23:21:41'),(2199,147,'Butler','2022-10-18 23:21:41'),(2200,147,'Chase','2022-10-18 23:21:41'),(2201,147,'Greenwood','2022-10-18 23:21:41'),(2202,147,'Cloud','2022-10-18 23:21:41'),(2203,147,'Republic','2022-10-18 23:21:41'),(2204,147,'Smith','2022-10-18 23:21:41'),(2205,147,'Washington','2022-10-18 23:21:41'),(2206,147,'Jewell','2022-10-18 23:21:41'),(2207,147,'Sedgwick','2022-10-18 23:21:41'),(2208,147,'Harper','2022-10-18 23:21:41'),(2209,147,'Sumner','2022-10-18 23:21:41'),(2210,147,'Cowley','2022-10-18 23:21:41'),(2211,147,'Harvey','2022-10-18 23:21:41'),(2212,147,'Pratt','2022-10-18 23:21:41'),(2213,147,'Chautauqua','2022-10-18 23:21:41'),(2214,147,'Comanche','2022-10-18 23:21:41'),(2215,147,'Kingman','2022-10-18 23:21:41'),(2216,147,'Kiowa','2022-10-18 23:21:41'),(2217,147,'Barber','2022-10-18 23:21:41'),(2218,147,'McPherson','2022-10-18 23:21:41'),(2219,147,'Montgomery','2022-10-18 23:21:41'),(2220,147,'Labette','2022-10-18 23:21:41'),(2221,147,'Elk','2022-10-18 23:21:41'),(2222,147,'Saline','2022-10-18 23:21:41'),(2223,147,'Dickinson','2022-10-18 23:21:41'),(2224,147,'Lincoln','2022-10-18 23:21:41'),(2225,147,'Mitchell','2022-10-18 23:21:41'),(2226,147,'Ottawa','2022-10-18 23:21:41'),(2227,147,'Rice','2022-10-18 23:21:41'),(2228,147,'Clay','2022-10-18 23:21:41'),(2229,147,'Osborne','2022-10-18 23:21:41'),(2230,147,'Ellsworth','2022-10-18 23:21:41'),(2231,147,'Reno','2022-10-18 23:21:41'),(2232,147,'Barton','2022-10-18 23:21:41'),(2233,147,'Rush','2022-10-18 23:21:41'),(2234,147,'Ness','2022-10-18 23:21:41'),(2235,147,'Edwards','2022-10-18 23:21:41'),(2236,147,'Pawnee','2022-10-18 23:21:41'),(2237,147,'Stafford','2022-10-18 23:21:41'),(2238,147,'Ellis','2022-10-18 23:21:41'),(2239,147,'Phillips','2022-10-18 23:21:41'),(2240,147,'Norton','2022-10-18 23:21:41'),(2241,147,'Graham','2022-10-18 23:21:41'),(2242,147,'Russell','2022-10-18 23:21:41'),(2243,147,'Trego','2022-10-18 23:21:41'),(2244,147,'Rooks','2022-10-18 23:21:41'),(2245,147,'Decatur','2022-10-18 23:21:41'),(2246,147,'Thomas','2022-10-18 23:21:41'),(2247,147,'Rawlins','2022-10-18 23:21:41'),(2248,147,'Cheyenne','2022-10-18 23:21:41'),(2249,147,'Sherman','2022-10-18 23:21:41'),(2250,147,'Gove','2022-10-18 23:21:41'),(2251,147,'Sheridan','2022-10-18 23:21:41'),(2252,147,'Logan','2022-10-18 23:21:41'),(2253,147,'Wallace','2022-10-18 23:21:41'),(2254,147,'Ford','2022-10-18 23:21:41'),(2255,147,'Clark','2022-10-18 23:21:41'),(2256,147,'Gray','2022-10-18 23:21:41'),(2257,147,'Hamilton','2022-10-18 23:21:41'),(2258,147,'Kearny','2022-10-18 23:21:41'),(2259,147,'Lane','2022-10-18 23:21:41'),(2260,147,'Meade','2022-10-18 23:21:41'),(2261,147,'Finney','2022-10-18 23:21:41'),(2262,147,'Hodgeman','2022-10-18 23:21:41'),(2263,147,'Stanton','2022-10-18 23:21:41'),(2264,147,'Seward','2022-10-18 23:21:41'),(2265,147,'Wichita','2022-10-18 23:21:41'),(2266,147,'Haskell','2022-10-18 23:21:41'),(2267,147,'Scott','2022-10-18 23:21:41'),(2268,147,'Greeley','2022-10-18 23:21:41'),(2269,147,'Grant','2022-10-18 23:21:41'),(2270,147,'Morton','2022-10-18 23:21:41'),(2271,147,'Stevens','2022-10-18 23:21:41'),(2272,159,'Butler','2022-10-18 23:21:41'),(2273,159,'Washington','2022-10-18 23:21:41'),(2274,159,'Saunders','2022-10-18 23:21:41'),(2275,159,'Cuming','2022-10-18 23:21:41'),(2276,159,'Sarpy','2022-10-18 23:21:41'),(2277,159,'Douglas','2022-10-18 23:21:41'),(2278,159,'Cass','2022-10-18 23:21:41'),(2279,159,'Burt','2022-10-18 23:21:41'),(2280,159,'Dodge','2022-10-18 23:21:41'),(2281,159,'Dakota','2022-10-18 23:21:41'),(2282,159,'Thurston','2022-10-18 23:21:41'),(2283,159,'Gage','2022-10-18 23:21:41'),(2284,159,'Thayer','2022-10-18 23:21:41'),(2285,159,'Nemaha','2022-10-18 23:21:41'),(2286,159,'Seward','2022-10-18 23:21:41'),(2287,159,'York','2022-10-18 23:21:41'),(2288,159,'Lancaster','2022-10-18 23:21:41'),(2289,159,'Pawnee','2022-10-18 23:21:41'),(2290,159,'Otoe','2022-10-18 23:21:41'),(2291,159,'Johnson','2022-10-18 23:21:41'),(2292,159,'Saline','2022-10-18 23:21:41'),(2293,159,'Richardson','2022-10-18 23:21:41'),(2294,159,'Jefferson','2022-10-18 23:21:41'),(2295,159,'Fillmore','2022-10-18 23:21:41'),(2296,159,'Clay','2022-10-18 23:21:41'),(2297,159,'Platte','2022-10-18 23:21:41'),(2298,159,'Boone','2022-10-18 23:21:41'),(2299,159,'Wheeler','2022-10-18 23:21:41'),(2300,159,'Nance','2022-10-18 23:21:41'),(2301,159,'Merrick','2022-10-18 23:21:41'),(2302,159,'Colfax','2022-10-18 23:21:41'),(2303,159,'Antelope','2022-10-18 23:21:41'),(2304,159,'Polk','2022-10-18 23:21:41'),(2305,159,'Greeley','2022-10-18 23:21:41'),(2306,159,'Madison','2022-10-18 23:21:41'),(2307,159,'Dixon','2022-10-18 23:21:41'),(2308,159,'Holt','2022-10-18 23:21:41'),(2309,159,'Rock','2022-10-18 23:21:41'),(2310,159,'Cedar','2022-10-18 23:21:41'),(2311,159,'Knox','2022-10-18 23:21:41'),(2312,159,'Boyd','2022-10-18 23:21:41'),(2313,159,'Wayne','2022-10-18 23:21:41'),(2314,159,'Pierce','2022-10-18 23:21:41'),(2315,159,'Keya Paha','2022-10-18 23:21:41'),(2316,159,'Stanton','2022-10-18 23:21:41'),(2317,159,'Hall','2022-10-18 23:21:41'),(2318,159,'Buffalo','2022-10-18 23:21:41'),(2319,159,'Custer','2022-10-18 23:21:41'),(2320,159,'Valley','2022-10-18 23:21:41'),(2321,159,'Sherman','2022-10-18 23:21:41'),(2322,159,'Hamilton','2022-10-18 23:21:41'),(2323,159,'Howard','2022-10-18 23:21:41'),(2324,159,'Blaine','2022-10-18 23:21:41'),(2325,159,'Garfield','2022-10-18 23:21:41'),(2326,159,'Dawson','2022-10-18 23:21:41'),(2327,159,'Loup','2022-10-18 23:21:41'),(2328,159,'Adams','2022-10-18 23:21:41'),(2329,159,'Harlan','2022-10-18 23:21:41'),(2330,159,'Furnas','2022-10-18 23:21:41'),(2331,159,'Phelps','2022-10-18 23:21:41'),(2332,159,'Kearney','2022-10-18 23:21:41'),(2333,159,'Webster','2022-10-18 23:21:41'),(2334,159,'Franklin','2022-10-18 23:21:41'),(2335,159,'Gosper','2022-10-18 23:21:41'),(2336,159,'Nuckolls','2022-10-18 23:21:41'),(2337,159,'Red Willow','2022-10-18 23:21:41'),(2338,159,'Dundy','2022-10-18 23:21:41'),(2339,159,'Chase','2022-10-18 23:21:41'),(2340,159,'Hitchcock','2022-10-18 23:21:41'),(2341,159,'Frontier','2022-10-18 23:21:41'),(2342,159,'Hayes','2022-10-18 23:21:41'),(2343,159,'Lincoln','2022-10-18 23:21:41'),(2344,159,'Arthur','2022-10-18 23:21:41'),(2345,159,'Deuel','2022-10-18 23:21:41'),(2346,159,'Morrill','2022-10-18 23:21:41'),(2347,159,'Keith','2022-10-18 23:21:41'),(2348,159,'Kimball','2022-10-18 23:21:41'),(2349,159,'Cheyenne','2022-10-18 23:21:41'),(2350,159,'Perkins','2022-10-18 23:21:41'),(2351,159,'Cherry','2022-10-18 23:21:41'),(2352,159,'Thomas','2022-10-18 23:21:41'),(2353,159,'Garden','2022-10-18 23:21:41'),(2354,159,'Hooker','2022-10-18 23:21:41'),(2355,159,'Logan','2022-10-18 23:21:41'),(2356,159,'McPherson','2022-10-18 23:21:41'),(2357,159,'Brown','2022-10-18 23:21:41'),(2358,159,'Box Butte','2022-10-18 23:21:41'),(2359,159,'Grant','2022-10-18 23:21:41'),(2360,159,'Sheridan','2022-10-18 23:21:41'),(2361,159,'Dawes','2022-10-18 23:21:41'),(2362,159,'Scotts Bluff','2022-10-18 23:21:41'),(2363,159,'Banner','2022-10-18 23:21:41'),(2364,159,'Sioux','2022-10-18 23:21:41'),(2365,149,'Jefferson','2022-10-18 23:21:41'),(2366,149,'Saint Charles','2022-10-18 23:21:41'),(2367,149,'Saint Bernard','2022-10-18 23:21:41'),(2368,149,'Plaquemines','2022-10-18 23:21:41'),(2369,149,'St John the Baptist','2022-10-18 23:21:41'),(2370,149,'Saint James','2022-10-18 23:21:41'),(2371,149,'Orleans','2022-10-18 23:21:41'),(2372,149,'Lafourche','2022-10-18 23:21:41'),(2373,149,'Assumption','2022-10-18 23:21:41'),(2374,149,'Saint Mary','2022-10-18 23:21:41'),(2375,149,'Terrebonne','2022-10-18 23:21:41'),(2376,149,'Ascension','2022-10-18 23:21:41'),(2377,149,'Tangipahoa','2022-10-18 23:21:41'),(2378,149,'Saint Tammany','2022-10-18 23:21:41'),(2379,149,'Washington','2022-10-18 23:21:41'),(2380,149,'Saint Helena','2022-10-18 23:21:41'),(2381,149,'Livingston','2022-10-18 23:21:41'),(2382,149,'Lafayette','2022-10-18 23:21:41'),(2383,149,'Vermilion','2022-10-18 23:21:41'),(2384,149,'Saint Landry','2022-10-18 23:21:41'),(2385,149,'Iberia','2022-10-18 23:21:41'),(2386,149,'Evangeline','2022-10-18 23:21:41'),(2387,149,'Acadia','2022-10-18 23:21:41'),(2388,149,'Saint Martin','2022-10-18 23:21:41'),(2389,149,'Jefferson Davis','2022-10-18 23:21:41'),(2390,149,'Calcasieu','2022-10-18 23:21:41'),(2391,149,'Cameron','2022-10-18 23:21:41'),(2392,149,'Beauregard','2022-10-18 23:21:41'),(2393,149,'Allen','2022-10-18 23:21:41'),(2394,149,'Vernon','2022-10-18 23:21:41'),(2395,149,'East Baton Rouge','2022-10-18 23:21:41'),(2396,149,'West Baton Rouge','2022-10-18 23:21:41'),(2397,149,'West Feliciana','2022-10-18 23:21:41'),(2398,149,'Pointe Coupee','2022-10-18 23:21:41'),(2399,149,'Iberville','2022-10-18 23:21:41'),(2400,149,'East Feliciana','2022-10-18 23:21:41'),(2401,149,'Bienville','2022-10-18 23:21:41'),(2402,149,'Natchitoches','2022-10-18 23:21:41'),(2403,149,'Claiborne','2022-10-18 23:21:41'),(2404,149,'Caddo','2022-10-18 23:21:41'),(2405,149,'Bossier','2022-10-18 23:21:41'),(2406,149,'Webster','2022-10-18 23:21:41'),(2407,149,'Red River','2022-10-18 23:21:41'),(2408,149,'De Soto','2022-10-18 23:21:41'),(2409,149,'Sabine','2022-10-18 23:21:41'),(2410,149,'Ouachita','2022-10-18 23:21:41'),(2411,149,'Richland','2022-10-18 23:21:41'),(2412,149,'Franklin','2022-10-18 23:21:41'),(2413,149,'Morehouse','2022-10-18 23:21:41'),(2414,149,'Union','2022-10-18 23:21:41'),(2415,149,'Jackson','2022-10-18 23:21:41'),(2416,149,'Lincoln','2022-10-18 23:21:41'),(2417,149,'Madison','2022-10-18 23:21:41'),(2418,149,'West Carroll','2022-10-18 23:21:41'),(2419,149,'East Carroll','2022-10-18 23:21:41'),(2420,149,'Rapides','2022-10-18 23:21:41'),(2421,149,'Concordia','2022-10-18 23:21:41'),(2422,149,'Avoyelles','2022-10-18 23:21:41'),(2423,149,'Catahoula','2022-10-18 23:21:41'),(2424,149,'La Salle','2022-10-18 23:21:41'),(2425,149,'Tensas','2022-10-18 23:21:41'),(2426,149,'Winn','2022-10-18 23:21:41'),(2427,149,'Grant','2022-10-18 23:21:41'),(2428,149,'Caldwell','2022-10-18 23:21:41'),(2429,132,'Jefferson','2022-10-18 23:21:41'),(2430,132,'Desha','2022-10-18 23:21:41'),(2431,132,'Bradley','2022-10-18 23:21:41'),(2432,132,'Ashley','2022-10-18 23:21:41'),(2433,132,'Chicot','2022-10-18 23:21:41'),(2434,132,'Lincoln','2022-10-18 23:21:41'),(2435,132,'Cleveland','2022-10-18 23:21:41'),(2436,132,'Drew','2022-10-18 23:21:41'),(2437,132,'Ouachita','2022-10-18 23:21:41'),(2438,132,'Clark','2022-10-18 23:21:41'),(2439,132,'Nevada','2022-10-18 23:21:41'),(2440,132,'Union','2022-10-18 23:21:41'),(2441,132,'Dallas','2022-10-18 23:21:41'),(2442,132,'Columbia','2022-10-18 23:21:41'),(2443,132,'Calhoun','2022-10-18 23:21:41'),(2444,132,'Hempstead','2022-10-18 23:21:41'),(2445,132,'Little River','2022-10-18 23:21:41'),(2446,132,'Sevier','2022-10-18 23:21:41'),(2447,132,'Lafayette','2022-10-18 23:21:41'),(2448,132,'Howard','2022-10-18 23:21:41'),(2449,132,'Miller','2022-10-18 23:21:41'),(2450,132,'Garland','2022-10-18 23:21:41'),(2451,132,'Pike','2022-10-18 23:21:41'),(2452,132,'Hot Spring','2022-10-18 23:21:41'),(2453,132,'Polk','2022-10-18 23:21:41'),(2454,132,'Montgomery','2022-10-18 23:21:41'),(2455,132,'Perry','2022-10-18 23:21:41'),(2456,132,'Pulaski','2022-10-18 23:21:41'),(2457,132,'Arkansas','2022-10-18 23:21:41'),(2458,132,'Jackson','2022-10-18 23:21:41'),(2459,132,'Woodruff','2022-10-18 23:21:41'),(2460,132,'Lonoke','2022-10-18 23:21:41'),(2461,132,'White','2022-10-18 23:21:41'),(2462,132,'Saline','2022-10-18 23:21:41'),(2463,132,'Van Buren','2022-10-18 23:21:41'),(2464,132,'Prairie','2022-10-18 23:21:41'),(2465,132,'Monroe','2022-10-18 23:21:41'),(2466,132,'Conway','2022-10-18 23:21:41'),(2467,132,'Faulkner','2022-10-18 23:21:41'),(2468,132,'Cleburne','2022-10-18 23:21:41'),(2469,132,'Stone','2022-10-18 23:21:41'),(2470,132,'Grant','2022-10-18 23:21:41'),(2471,132,'Independence','2022-10-18 23:21:41'),(2472,132,'Crittenden','2022-10-18 23:21:41'),(2473,132,'Mississippi','2022-10-18 23:21:41'),(2474,132,'Lee','2022-10-18 23:21:41'),(2475,132,'Phillips','2022-10-18 23:21:41'),(2476,132,'Saint Francis','2022-10-18 23:21:41'),(2477,132,'Cross','2022-10-18 23:21:41'),(2478,132,'Poinsett','2022-10-18 23:21:41'),(2479,132,'Craighead','2022-10-18 23:21:41'),(2480,132,'Lawrence','2022-10-18 23:21:41'),(2481,132,'Greene','2022-10-18 23:21:41'),(2482,132,'Randolph','2022-10-18 23:21:41'),(2483,132,'Clay','2022-10-18 23:21:41'),(2484,132,'Sharp','2022-10-18 23:21:41'),(2485,132,'Izard','2022-10-18 23:21:41'),(2486,132,'Fulton','2022-10-18 23:21:41'),(2487,132,'Baxter','2022-10-18 23:21:41'),(2488,132,'Boone','2022-10-18 23:21:41'),(2489,132,'Carroll','2022-10-18 23:21:41'),(2490,132,'Marion','2022-10-18 23:21:41'),(2491,132,'Newton','2022-10-18 23:21:41'),(2492,132,'Searcy','2022-10-18 23:21:41'),(2493,132,'Pope','2022-10-18 23:21:41'),(2494,132,'Washington','2022-10-18 23:21:41'),(2495,132,'Benton','2022-10-18 23:21:41'),(2496,132,'Madison','2022-10-18 23:21:41'),(2497,132,'Franklin','2022-10-18 23:21:41'),(2498,132,'Yell','2022-10-18 23:21:41'),(2499,132,'Logan','2022-10-18 23:21:41'),(2500,132,'Johnson','2022-10-18 23:21:41'),(2501,132,'Scott','2022-10-18 23:21:41'),(2502,132,'Sebastian','2022-10-18 23:21:41'),(2503,132,'Crawford','2022-10-18 23:21:41'),(2504,169,'Caddo','2022-10-18 23:21:41'),(2505,169,'Grady','2022-10-18 23:21:41'),(2506,169,'Oklahoma','2022-10-18 23:21:41'),(2507,169,'McClain','2022-10-18 23:21:41'),(2508,169,'Stephens','2022-10-18 23:21:41'),(2509,169,'Canadian','2022-10-18 23:21:41'),(2510,169,'Kingfisher','2022-10-18 23:21:41'),(2511,169,'Cleveland','2022-10-18 23:21:41'),(2512,169,'Washita','2022-10-18 23:21:41'),(2513,169,'Logan','2022-10-18 23:21:41'),(2514,169,'Murray','2022-10-18 23:21:41'),(2515,169,'Blaine','2022-10-18 23:21:41'),(2516,169,'Kiowa','2022-10-18 23:21:41'),(2517,169,'Garvin','2022-10-18 23:21:41'),(2518,169,'Noble','2022-10-18 23:21:41'),(2519,169,'Custer','2022-10-18 23:21:41'),(2520,178,'Travis','2022-10-18 23:21:41'),(2521,169,'Carter','2022-10-18 23:21:41'),(2522,169,'Love','2022-10-18 23:21:41'),(2523,169,'Johnston','2022-10-18 23:21:41'),(2524,169,'Marshall','2022-10-18 23:21:41'),(2525,169,'Bryan','2022-10-18 23:21:41'),(2526,169,'Jefferson','2022-10-18 23:21:41'),(2527,169,'Comanche','2022-10-18 23:21:41'),(2528,169,'Jackson','2022-10-18 23:21:41'),(2529,169,'Tillman','2022-10-18 23:21:41'),(2530,169,'Cotton','2022-10-18 23:21:41'),(2531,169,'Harmon','2022-10-18 23:21:41'),(2532,169,'Greer','2022-10-18 23:21:41'),(2533,169,'Beckham','2022-10-18 23:21:41'),(2534,169,'Roger Mills','2022-10-18 23:21:41'),(2535,169,'Dewey','2022-10-18 23:21:41'),(2536,169,'Garfield','2022-10-18 23:21:41'),(2537,169,'Alfalfa','2022-10-18 23:21:41'),(2538,169,'Woods','2022-10-18 23:21:41'),(2539,169,'Major','2022-10-18 23:21:41'),(2540,169,'Grant','2022-10-18 23:21:41'),(2541,169,'Woodward','2022-10-18 23:21:41'),(2542,169,'Ellis','2022-10-18 23:21:41'),(2543,169,'Harper','2022-10-18 23:21:41'),(2544,169,'Beaver','2022-10-18 23:21:41'),(2545,169,'Texas','2022-10-18 23:21:41'),(2546,169,'Cimarron','2022-10-18 23:21:41'),(2547,169,'Osage','2022-10-18 23:21:41'),(2548,169,'Washington','2022-10-18 23:21:41'),(2549,169,'Tulsa','2022-10-18 23:21:41'),(2550,169,'Creek','2022-10-18 23:21:41'),(2551,169,'Wagoner','2022-10-18 23:21:41'),(2552,169,'Rogers','2022-10-18 23:21:41'),(2553,169,'Pawnee','2022-10-18 23:21:41'),(2554,169,'Payne','2022-10-18 23:21:41'),(2555,169,'Lincoln','2022-10-18 23:21:41'),(2556,169,'Nowata','2022-10-18 23:21:41'),(2557,169,'Craig','2022-10-18 23:21:41'),(2558,169,'Mayes','2022-10-18 23:21:41'),(2559,169,'Ottawa','2022-10-18 23:21:41'),(2560,169,'Delaware','2022-10-18 23:21:41'),(2561,169,'Muskogee','2022-10-18 23:21:41'),(2562,169,'Okmulgee','2022-10-18 23:21:41'),(2563,169,'Pittsburg','2022-10-18 23:21:41'),(2564,169,'McIntosh','2022-10-18 23:21:41'),(2565,169,'Cherokee','2022-10-18 23:21:41'),(2566,169,'Sequoyah','2022-10-18 23:21:41'),(2567,169,'Haskell','2022-10-18 23:21:41'),(2568,169,'Adair','2022-10-18 23:21:41'),(2569,169,'Pushmataha','2022-10-18 23:21:41'),(2570,169,'Atoka','2022-10-18 23:21:41'),(2571,169,'Hughes','2022-10-18 23:21:41'),(2572,169,'Coal','2022-10-18 23:21:41'),(2573,169,'Latimer','2022-10-18 23:21:41'),(2574,169,'Le Flore','2022-10-18 23:21:41'),(2575,169,'Kay','2022-10-18 23:21:41'),(2576,169,'McCurtain','2022-10-18 23:21:41'),(2577,169,'Choctaw','2022-10-18 23:21:41'),(2578,169,'Pottawatomie','2022-10-18 23:21:41'),(2579,169,'Seminole','2022-10-18 23:21:41'),(2580,169,'Pontotoc','2022-10-18 23:21:41'),(2581,169,'Okfuskee','2022-10-18 23:21:41'),(2582,178,'Dallas','2022-10-18 23:21:41'),(2583,178,'Collin','2022-10-18 23:21:41'),(2584,178,'Denton','2022-10-18 23:21:41'),(2585,178,'Grayson','2022-10-18 23:21:41'),(2586,178,'Rockwall','2022-10-18 23:21:41'),(2587,178,'Ellis','2022-10-18 23:21:41'),(2588,178,'Navarro','2022-10-18 23:21:41'),(2589,178,'Van Zandt','2022-10-18 23:21:41'),(2590,178,'Kaufman','2022-10-18 23:21:41'),(2591,178,'Henderson','2022-10-18 23:21:41'),(2592,178,'Hunt','2022-10-18 23:21:41'),(2593,178,'Wood','2022-10-18 23:21:41'),(2594,178,'Lamar','2022-10-18 23:21:41'),(2595,178,'Red River','2022-10-18 23:21:41'),(2596,178,'Fannin','2022-10-18 23:21:41'),(2597,178,'Delta','2022-10-18 23:21:41'),(2598,178,'Hopkins','2022-10-18 23:21:41'),(2599,178,'Rains','2022-10-18 23:21:41'),(2600,178,'Camp','2022-10-18 23:21:41'),(2601,178,'Titus','2022-10-18 23:21:41'),(2602,178,'Franklin','2022-10-18 23:21:41'),(2603,178,'Bowie','2022-10-18 23:21:41'),(2604,178,'Cass','2022-10-18 23:21:41'),(2605,178,'Marion','2022-10-18 23:21:41'),(2606,178,'Morris','2022-10-18 23:21:41'),(2607,178,'Gregg','2022-10-18 23:21:41'),(2608,178,'Panola','2022-10-18 23:21:41'),(2609,178,'Upshur','2022-10-18 23:21:41'),(2610,178,'Harrison','2022-10-18 23:21:41'),(2611,178,'Rusk','2022-10-18 23:21:41'),(2612,178,'Smith','2022-10-18 23:21:41'),(2613,178,'Cherokee','2022-10-18 23:21:41'),(2614,178,'Nacogdoches','2022-10-18 23:21:41'),(2615,178,'Anderson','2022-10-18 23:21:41'),(2616,178,'Leon','2022-10-18 23:21:41'),(2617,178,'Trinity','2022-10-18 23:21:41'),(2618,178,'Houston','2022-10-18 23:21:41'),(2619,178,'Freestone','2022-10-18 23:21:41'),(2620,178,'Madison','2022-10-18 23:21:41'),(2621,178,'Angelina','2022-10-18 23:21:41'),(2622,178,'Newton','2022-10-18 23:21:41'),(2623,178,'San Augustine','2022-10-18 23:21:41'),(2624,178,'Sabine','2022-10-18 23:21:41'),(2625,178,'Polk','2022-10-18 23:21:41'),(2626,178,'Shelby','2022-10-18 23:21:41'),(2627,178,'Tyler','2022-10-18 23:21:41'),(2628,178,'Jasper','2022-10-18 23:21:41'),(2629,178,'Tarrant','2022-10-18 23:21:41'),(2630,178,'Parker','2022-10-18 23:21:41'),(2631,178,'Johnson','2022-10-18 23:21:41'),(2632,178,'Wise','2022-10-18 23:21:41'),(2633,178,'Hood','2022-10-18 23:21:41'),(2634,178,'Somervell','2022-10-18 23:21:41'),(2635,178,'Hill','2022-10-18 23:21:41'),(2636,178,'Palo Pinto','2022-10-18 23:21:41'),(2637,178,'Clay','2022-10-18 23:21:41'),(2638,178,'Montague','2022-10-18 23:21:41'),(2639,178,'Cooke','2022-10-18 23:21:41'),(2640,178,'Wichita','2022-10-18 23:21:41'),(2641,178,'Archer','2022-10-18 23:21:41'),(2642,178,'Knox','2022-10-18 23:21:41'),(2643,178,'Wilbarger','2022-10-18 23:21:41'),(2644,178,'Young','2022-10-18 23:21:41'),(2645,178,'Baylor','2022-10-18 23:21:41'),(2646,178,'Haskell','2022-10-18 23:21:41'),(2647,178,'Erath','2022-10-18 23:21:41'),(2648,178,'Stephens','2022-10-18 23:21:41'),(2649,178,'Jack','2022-10-18 23:21:41'),(2650,178,'Shackelford','2022-10-18 23:21:41'),(2651,178,'Brown','2022-10-18 23:21:41'),(2652,178,'Eastland','2022-10-18 23:21:41'),(2653,178,'Hamilton','2022-10-18 23:21:41'),(2654,178,'Comanche','2022-10-18 23:21:41'),(2655,178,'Callahan','2022-10-18 23:21:41'),(2656,178,'Throckmorton','2022-10-18 23:21:41'),(2657,178,'Bell','2022-10-18 23:21:41'),(2658,178,'Milam','2022-10-18 23:21:41'),(2659,178,'Coryell','2022-10-18 23:21:41'),(2660,178,'McLennan','2022-10-18 23:21:41'),(2661,178,'Williamson','2022-10-18 23:21:41'),(2662,178,'Lampasas','2022-10-18 23:21:41'),(2663,178,'Falls','2022-10-18 23:21:41'),(2664,178,'Robertson','2022-10-18 23:21:41'),(2665,178,'Bosque','2022-10-18 23:21:41'),(2666,178,'Limestone','2022-10-18 23:21:41'),(2667,178,'Mason','2022-10-18 23:21:41'),(2668,178,'Runnels','2022-10-18 23:21:41'),(2669,178,'McCulloch','2022-10-18 23:21:41'),(2670,178,'Coleman','2022-10-18 23:21:41'),(2671,178,'Llano','2022-10-18 23:21:41'),(2672,178,'San Saba','2022-10-18 23:21:41'),(2673,178,'Concho','2022-10-18 23:21:41'),(2674,178,'Menard','2022-10-18 23:21:41'),(2675,178,'Mills','2022-10-18 23:21:41'),(2676,178,'Kimble','2022-10-18 23:21:41'),(2677,178,'Edwards','2022-10-18 23:21:41'),(2678,178,'Tom Green','2022-10-18 23:21:41'),(2679,178,'Irion','2022-10-18 23:21:41'),(2680,178,'Reagan','2022-10-18 23:21:41'),(2681,178,'Coke','2022-10-18 23:21:41'),(2682,178,'Schleicher','2022-10-18 23:21:41'),(2683,178,'Crockett','2022-10-18 23:21:41'),(2684,178,'Sutton','2022-10-18 23:21:41'),(2685,178,'Sterling','2022-10-18 23:21:41'),(2686,178,'Harris','2022-10-18 23:21:41'),(2687,178,'Montgomery','2022-10-18 23:21:41'),(2688,178,'Walker','2022-10-18 23:21:41'),(2689,178,'Liberty','2022-10-18 23:21:41'),(2690,178,'San Jacinto','2022-10-18 23:21:41'),(2691,178,'Grimes','2022-10-18 23:21:41'),(2692,178,'Hardin','2022-10-18 23:21:41'),(2693,178,'Matagorda','2022-10-18 23:21:41'),(2694,178,'Fort Bend','2022-10-18 23:21:41'),(2695,178,'Colorado','2022-10-18 23:21:41'),(2696,178,'Austin','2022-10-18 23:21:41'),(2697,178,'Wharton','2022-10-18 23:21:41'),(2698,178,'Brazoria','2022-10-18 23:21:41'),(2699,178,'Waller','2022-10-18 23:21:41'),(2700,178,'Washington','2022-10-18 23:21:41'),(2701,178,'Galveston','2022-10-18 23:21:41'),(2702,178,'Chambers','2022-10-18 23:21:41'),(2703,178,'Orange','2022-10-18 23:21:41'),(2704,178,'Jefferson','2022-10-18 23:21:41'),(2705,178,'Brazos','2022-10-18 23:21:41'),(2706,178,'Burleson','2022-10-18 23:21:41'),(2707,178,'Lee','2022-10-18 23:21:41'),(2708,178,'Victoria','2022-10-18 23:21:41'),(2709,178,'Refugio','2022-10-18 23:21:41'),(2710,178,'De Witt','2022-10-18 23:21:41'),(2711,178,'Jackson','2022-10-18 23:21:41'),(2712,178,'Goliad','2022-10-18 23:21:41'),(2713,178,'Lavaca','2022-10-18 23:21:41'),(2714,178,'Calhoun','2022-10-18 23:21:41'),(2715,178,'La Salle','2022-10-18 23:21:41'),(2716,178,'Bexar','2022-10-18 23:21:41'),(2717,178,'Bandera','2022-10-18 23:21:41'),(2718,178,'Kendall','2022-10-18 23:21:41'),(2719,178,'Frio','2022-10-18 23:21:41'),(2720,178,'McMullen','2022-10-18 23:21:41'),(2721,178,'Atascosa','2022-10-18 23:21:41'),(2722,178,'Medina','2022-10-18 23:21:41'),(2723,178,'Kerr','2022-10-18 23:21:41'),(2724,178,'Live Oak','2022-10-18 23:21:41'),(2725,178,'Webb','2022-10-18 23:21:41'),(2726,178,'Zapata','2022-10-18 23:21:41'),(2727,178,'Comal','2022-10-18 23:21:41'),(2728,178,'Bee','2022-10-18 23:21:41'),(2729,178,'Guadalupe','2022-10-18 23:21:41'),(2730,178,'Karnes','2022-10-18 23:21:41'),(2731,178,'Wilson','2022-10-18 23:21:41'),(2732,178,'Gonzales','2022-10-18 23:21:41'),(2733,178,'Nueces','2022-10-18 23:21:41'),(2734,178,'Jim Wells','2022-10-18 23:21:41'),(2735,178,'San Patricio','2022-10-18 23:21:41'),(2736,178,'Kenedy','2022-10-18 23:21:41'),(2737,178,'Duval','2022-10-18 23:21:41'),(2738,178,'Brooks','2022-10-18 23:21:41'),(2739,178,'Aransas','2022-10-18 23:21:41'),(2740,178,'Jim Hogg','2022-10-18 23:21:41'),(2741,178,'Kleberg','2022-10-18 23:21:41'),(2742,178,'Hidalgo','2022-10-18 23:21:41'),(2743,178,'Cameron','2022-10-18 23:21:41'),(2744,178,'Starr','2022-10-18 23:21:41'),(2745,178,'Willacy','2022-10-18 23:21:41'),(2746,178,'Bastrop','2022-10-18 23:21:41'),(2747,178,'Burnet','2022-10-18 23:21:41'),(2748,178,'Blanco','2022-10-18 23:21:41'),(2749,178,'Hays','2022-10-18 23:21:41'),(2750,178,'Caldwell','2022-10-18 23:21:41'),(2751,178,'Gillespie','2022-10-18 23:21:41'),(2752,178,'Uvalde','2022-10-18 23:21:41'),(2753,178,'Dimmit','2022-10-18 23:21:41'),(2754,178,'Zavala','2022-10-18 23:21:41'),(2755,178,'Kinney','2022-10-18 23:21:41'),(2756,178,'Real','2022-10-18 23:21:41'),(2757,178,'Val Verde','2022-10-18 23:21:41'),(2758,178,'Terrell','2022-10-18 23:21:41'),(2759,178,'Maverick','2022-10-18 23:21:41'),(2760,178,'Fayette','2022-10-18 23:21:41'),(2761,178,'Oldham','2022-10-18 23:21:41'),(2762,178,'Gray','2022-10-18 23:21:41'),(2763,178,'Wheeler','2022-10-18 23:21:41'),(2764,178,'Lipscomb','2022-10-18 23:21:41'),(2765,178,'Hutchinson','2022-10-18 23:21:41'),(2766,178,'Parmer','2022-10-18 23:21:41'),(2767,178,'Potter','2022-10-18 23:21:41'),(2768,178,'Moore','2022-10-18 23:21:41'),(2769,178,'Hemphill','2022-10-18 23:21:41'),(2770,178,'Randall','2022-10-18 23:21:41'),(2771,178,'Hartley','2022-10-18 23:21:41'),(2772,178,'Armstrong','2022-10-18 23:21:41'),(2773,178,'Hale','2022-10-18 23:21:41'),(2774,178,'Dallam','2022-10-18 23:21:41'),(2775,178,'Deaf Smith','2022-10-18 23:21:41'),(2776,178,'Castro','2022-10-18 23:21:41'),(2777,178,'Lamb','2022-10-18 23:21:41'),(2778,178,'Ochiltree','2022-10-18 23:21:41'),(2779,178,'Carson','2022-10-18 23:21:41'),(2780,178,'Hansford','2022-10-18 23:21:41'),(2781,178,'Swisher','2022-10-18 23:21:41'),(2782,178,'Roberts','2022-10-18 23:21:41'),(2783,178,'Collingsworth','2022-10-18 23:21:41'),(2784,178,'Sherman','2022-10-18 23:21:41'),(2785,178,'Childress','2022-10-18 23:21:41'),(2786,178,'Dickens','2022-10-18 23:21:41'),(2787,178,'Floyd','2022-10-18 23:21:41'),(2788,178,'Cottle','2022-10-18 23:21:41'),(2789,178,'Hardeman','2022-10-18 23:21:41'),(2790,178,'Donley','2022-10-18 23:21:41'),(2791,178,'Foard','2022-10-18 23:21:41'),(2792,178,'Hall','2022-10-18 23:21:41'),(2793,178,'Motley','2022-10-18 23:21:41'),(2794,178,'King','2022-10-18 23:21:41'),(2795,178,'Briscoe','2022-10-18 23:21:41'),(2796,178,'Hockley','2022-10-18 23:21:41'),(2797,178,'Cochran','2022-10-18 23:21:41'),(2798,178,'Terry','2022-10-18 23:21:41'),(2799,178,'Bailey','2022-10-18 23:21:41'),(2800,178,'Crosby','2022-10-18 23:21:41'),(2801,178,'Yoakum','2022-10-18 23:21:41'),(2802,178,'Lubbock','2022-10-18 23:21:41'),(2803,178,'Garza','2022-10-18 23:21:41'),(2804,178,'Dawson','2022-10-18 23:21:41'),(2805,178,'Gaines','2022-10-18 23:21:41'),(2806,178,'Lynn','2022-10-18 23:21:41'),(2807,178,'Jones','2022-10-18 23:21:41'),(2808,178,'Stonewall','2022-10-18 23:21:41'),(2809,178,'Nolan','2022-10-18 23:21:41'),(2810,178,'Taylor','2022-10-18 23:21:41'),(2811,178,'Howard','2022-10-18 23:21:41'),(2812,178,'Mitchell','2022-10-18 23:21:41'),(2813,178,'Scurry','2022-10-18 23:21:41'),(2814,178,'Kent','2022-10-18 23:21:41'),(2815,178,'Fisher','2022-10-18 23:21:41'),(2816,178,'Midland','2022-10-18 23:21:41'),(2817,178,'Andrews','2022-10-18 23:21:41'),(2818,178,'Reeves','2022-10-18 23:21:41'),(2819,178,'Ward','2022-10-18 23:21:41'),(2820,178,'Pecos','2022-10-18 23:21:41'),(2821,178,'Crane','2022-10-18 23:21:41'),(2822,178,'Jeff Davis','2022-10-18 23:21:41'),(2823,178,'Borden','2022-10-18 23:21:41'),(2824,178,'Glasscock','2022-10-18 23:21:41'),(2825,178,'Ector','2022-10-18 23:21:41'),(2826,178,'Winkler','2022-10-18 23:21:41'),(2827,178,'Martin','2022-10-18 23:21:41'),(2828,178,'Upton','2022-10-18 23:21:41'),(2829,178,'Loving','2022-10-18 23:21:41'),(2830,178,'El Paso','2022-10-18 23:21:41'),(2831,178,'Brewster','2022-10-18 23:21:41'),(2832,178,'Hudspeth','2022-10-18 23:21:41'),(2833,178,'Presidio','2022-10-18 23:21:41'),(2834,178,'Culberson','2022-10-18 23:21:41'),(2835,134,'Jefferson','2022-10-18 23:21:41'),(2836,134,'Arapahoe','2022-10-18 23:21:41'),(2837,134,'Adams','2022-10-18 23:21:41'),(2838,134,'Boulder','2022-10-18 23:21:41'),(2839,134,'Elbert','2022-10-18 23:21:41'),(2840,134,'Douglas','2022-10-18 23:21:41'),(2841,134,'Denver','2022-10-18 23:21:41'),(2842,134,'El Paso','2022-10-18 23:21:41'),(2843,134,'Park','2022-10-18 23:21:41'),(2844,134,'Gilpin','2022-10-18 23:21:41'),(2845,134,'Eagle','2022-10-18 23:21:41'),(2846,134,'Summit','2022-10-18 23:21:41'),(2847,134,'Routt','2022-10-18 23:21:41'),(2848,134,'Lake','2022-10-18 23:21:41'),(2849,134,'Jackson','2022-10-18 23:21:41'),(2850,134,'Clear Creek','2022-10-18 23:21:41'),(2851,134,'Grand','2022-10-18 23:21:41'),(2852,134,'Weld','2022-10-18 23:21:41'),(2853,134,'Larimer','2022-10-18 23:21:41'),(2854,134,'Morgan','2022-10-18 23:21:41'),(2855,134,'Washington','2022-10-18 23:21:41'),(2856,134,'Phillips','2022-10-18 23:21:41'),(2857,134,'Logan','2022-10-18 23:21:41'),(2858,134,'Yuma','2022-10-18 23:21:41'),(2859,134,'Sedgwick','2022-10-18 23:21:41'),(2860,134,'Cheyenne','2022-10-18 23:21:41'),(2861,134,'Lincoln','2022-10-18 23:21:41'),(2862,134,'Kit Carson','2022-10-18 23:21:41'),(2863,134,'Teller','2022-10-18 23:21:41'),(2864,134,'Mohave','2022-10-18 23:21:41'),(2865,134,'Pueblo','2022-10-18 23:21:41'),(2866,134,'Las Animas','2022-10-18 23:21:41'),(2867,134,'Kiowa','2022-10-18 23:21:41'),(2868,134,'Baca','2022-10-18 23:21:41'),(2869,134,'Otero','2022-10-18 23:21:41'),(2870,134,'Crowley','2022-10-18 23:21:41'),(2871,134,'Bent','2022-10-18 23:21:41'),(2872,134,'Huerfano','2022-10-18 23:21:41'),(2873,134,'Prowers','2022-10-18 23:21:41'),(2874,134,'Alamosa','2022-10-18 23:21:41'),(2875,134,'Conejos','2022-10-18 23:21:41'),(2876,134,'Archuleta','2022-10-18 23:21:41'),(2877,134,'La Plata','2022-10-18 23:21:41'),(2878,134,'Costilla','2022-10-18 23:21:41'),(2879,134,'Saguache','2022-10-18 23:21:41'),(2880,134,'Mineral','2022-10-18 23:21:41'),(2881,134,'Rio Grande','2022-10-18 23:21:41'),(2882,134,'Chaffee','2022-10-18 23:21:41'),(2883,134,'Gunnison','2022-10-18 23:21:41'),(2884,134,'Fremont','2022-10-18 23:21:41'),(2885,134,'Montrose','2022-10-18 23:21:41'),(2886,134,'Hinsdale','2022-10-18 23:21:41'),(2887,134,'Custer','2022-10-18 23:21:41'),(2888,134,'Dolores','2022-10-18 23:21:41'),(2889,134,'Montezuma','2022-10-18 23:21:41'),(2890,134,'San Miguel','2022-10-18 23:21:41'),(2891,134,'Delta','2022-10-18 23:21:41'),(2892,134,'Ouray','2022-10-18 23:21:41'),(2893,134,'San Juan','2022-10-18 23:21:41'),(2894,134,'Mesa','2022-10-18 23:21:41'),(2895,134,'Garfield','2022-10-18 23:21:41'),(2896,134,'Moffat','2022-10-18 23:21:41'),(2897,134,'Pitkin','2022-10-18 23:21:41'),(2898,134,'Rio Blanco','2022-10-18 23:21:41'),(2899,186,'Laramie','2022-10-18 23:21:41'),(2900,186,'Albany','2022-10-18 23:21:41'),(2901,186,'Park','2022-10-18 23:21:41'),(2902,186,'Platte','2022-10-18 23:21:41'),(2903,186,'Goshen','2022-10-18 23:21:41'),(2904,186,'Niobrara','2022-10-18 23:21:41'),(2905,186,'Converse','2022-10-18 23:21:41'),(2906,186,'Carbon','2022-10-18 23:21:41'),(2907,186,'Fremont','2022-10-18 23:21:41'),(2908,186,'Sweetwater','2022-10-18 23:21:41'),(2909,186,'Washakie','2022-10-18 23:21:41'),(2910,186,'Big Horn','2022-10-18 23:21:41'),(2911,186,'Hot Springs','2022-10-18 23:21:41'),(2912,186,'Natrona','2022-10-18 23:21:41'),(2913,186,'Johnson','2022-10-18 23:21:41'),(2914,186,'Weston','2022-10-18 23:21:41'),(2915,186,'Crook','2022-10-18 23:21:41'),(2916,186,'Campbell','2022-10-18 23:21:41'),(2917,186,'Sheridan','2022-10-18 23:21:41'),(2918,186,'Sublette','2022-10-18 23:21:41'),(2919,186,'Uinta','2022-10-18 23:21:41'),(2920,186,'Teton','2022-10-18 23:21:41'),(2921,186,'Lincoln','2022-10-18 23:21:41'),(2922,143,'Bannock','2022-10-18 23:21:41'),(2923,143,'Bingham','2022-10-18 23:21:41'),(2924,143,'Power','2022-10-18 23:21:41'),(2925,143,'Butte','2022-10-18 23:21:41'),(2926,143,'Caribou','2022-10-18 23:21:41'),(2927,143,'Bear Lake','2022-10-18 23:21:41'),(2928,143,'Custer','2022-10-18 23:21:41'),(2929,143,'Franklin','2022-10-18 23:21:41'),(2930,143,'Lemhi','2022-10-18 23:21:41'),(2931,143,'Oneida','2022-10-18 23:21:41'),(2932,143,'Twin Falls','2022-10-18 23:21:41'),(2933,143,'Cassia','2022-10-18 23:21:41'),(2934,143,'Blaine','2022-10-18 23:21:41'),(2935,143,'Gooding','2022-10-18 23:21:41'),(2936,143,'Camas','2022-10-18 23:21:41'),(2937,143,'Lincoln','2022-10-18 23:21:41'),(2938,143,'Jerome','2022-10-18 23:21:41'),(2939,143,'Minidoka','2022-10-18 23:21:41'),(2940,143,'Bonneville','2022-10-18 23:21:41'),(2941,143,'Fremont','2022-10-18 23:21:41'),(2942,143,'Teton','2022-10-18 23:21:41'),(2943,143,'Clark','2022-10-18 23:21:41'),(2944,143,'Jefferson','2022-10-18 23:21:41'),(2945,143,'Madison','2022-10-18 23:21:41'),(2946,143,'Nez Perce','2022-10-18 23:21:41'),(2947,143,'Clearwater','2022-10-18 23:21:41'),(2948,143,'Idaho','2022-10-18 23:21:41'),(2949,143,'Lewis','2022-10-18 23:21:41'),(2950,143,'Latah','2022-10-18 23:21:41'),(2951,143,'Elmore','2022-10-18 23:21:41'),(2952,143,'Boise','2022-10-18 23:21:41'),(2953,143,'Owyhee','2022-10-18 23:21:41'),(2954,143,'Canyon','2022-10-18 23:21:41'),(2955,143,'Washington','2022-10-18 23:21:41'),(2956,143,'Valley','2022-10-18 23:21:41'),(2957,143,'Adams','2022-10-18 23:21:41'),(2958,143,'Ada','2022-10-18 23:21:41'),(2959,143,'Gem','2022-10-18 23:21:41'),(2960,143,'Payette','2022-10-18 23:21:41'),(2961,143,'Kootenai','2022-10-18 23:21:41'),(2962,143,'Shoshone','2022-10-18 23:21:41'),(2963,143,'Bonner','2022-10-18 23:21:41'),(2964,143,'Boundary','2022-10-18 23:21:41'),(2965,143,'Benewah','2022-10-18 23:21:41'),(2966,179,'Duchesne','2022-10-18 23:21:41'),(2967,179,'Utah','2022-10-18 23:21:41'),(2968,179,'Salt Lake','2022-10-18 23:21:41'),(2969,179,'Uintah','2022-10-18 23:21:41'),(2970,179,'Davis','2022-10-18 23:21:41'),(2971,179,'Summit','2022-10-18 23:21:41'),(2972,179,'Morgan','2022-10-18 23:21:41'),(2973,179,'Tooele','2022-10-18 23:21:41'),(2974,179,'Daggett','2022-10-18 23:21:41'),(2975,179,'Rich','2022-10-18 23:21:41'),(2976,179,'Wasatch','2022-10-18 23:21:41'),(2977,179,'Weber','2022-10-18 23:21:41'),(2978,179,'Box Elder','2022-10-18 23:21:41'),(2979,179,'Cache','2022-10-18 23:21:41'),(2980,179,'Carbon','2022-10-18 23:21:41'),(2981,179,'San Juan','2022-10-18 23:21:41'),(2982,179,'Emery','2022-10-18 23:21:41'),(2983,179,'Grand','2022-10-18 23:21:41'),(2984,179,'Sevier','2022-10-18 23:21:41'),(2985,179,'Sanpete','2022-10-18 23:21:41'),(2986,179,'Millard','2022-10-18 23:21:41'),(2987,179,'Juab','2022-10-18 23:21:41'),(2988,179,'Kane','2022-10-18 23:21:41'),(2989,179,'Beaver','2022-10-18 23:21:41'),(2990,179,'Iron','2022-10-18 23:21:41'),(2991,179,'Wayne','2022-10-18 23:21:41'),(2992,179,'Washington','2022-10-18 23:21:41'),(2993,179,'Piute','2022-10-18 23:21:41'),(2994,131,'Maricopa','2022-10-18 23:21:41'),(2995,131,'Pinal','2022-10-18 23:21:41'),(2996,131,'Gila','2022-10-18 23:21:41'),(2997,131,'Pima','2022-10-18 23:21:41'),(2998,131,'Yavapai','2022-10-18 23:21:41'),(2999,131,'La Paz','2022-10-18 23:21:41'),(3000,131,'Yuma','2022-10-18 23:21:41'),(3001,131,'Mohave','2022-10-18 23:21:41'),(3002,131,'Graham','2022-10-18 23:21:41'),(3003,131,'Greenlee','2022-10-18 23:21:41'),(3004,131,'Cochise','2022-10-18 23:21:41'),(3005,131,'Santa Cruz','2022-10-18 23:21:41'),(3006,131,'Navajo','2022-10-18 23:21:41'),(3007,131,'Apache','2022-10-18 23:21:41'),(3008,131,'Coconino','2022-10-18 23:21:41'),(3009,163,'Sandoval','2022-10-18 23:21:41'),(3010,163,'Valencia','2022-10-18 23:21:41'),(3011,163,'Cibola','2022-10-18 23:21:41'),(3012,163,'Bernalillo','2022-10-18 23:21:41'),(3013,163,'Torrance','2022-10-18 23:21:41'),(3014,163,'Santa Fe','2022-10-18 23:21:41'),(3015,163,'Socorro','2022-10-18 23:21:41'),(3016,163,'Rio Arriba','2022-10-18 23:21:41'),(3017,163,'San Juan','2022-10-18 23:21:41'),(3018,163,'McKinley','2022-10-18 23:21:41'),(3019,163,'Taos','2022-10-18 23:21:41'),(3020,163,'San Miguel','2022-10-18 23:21:41'),(3021,163,'Los Alamos','2022-10-18 23:21:41'),(3022,163,'Colfax','2022-10-18 23:21:41'),(3023,163,'Guadalupe','2022-10-18 23:21:41'),(3024,163,'Mora','2022-10-18 23:21:41'),(3025,163,'Harding','2022-10-18 23:21:41'),(3026,163,'Catron','2022-10-18 23:21:41'),(3027,163,'Sierra','2022-10-18 23:21:41'),(3028,163,'Dona Ana','2022-10-18 23:21:41'),(3029,163,'Hidalgo','2022-10-18 23:21:41'),(3030,163,'Grant','2022-10-18 23:21:41'),(3031,163,'Luna','2022-10-18 23:21:41'),(3032,163,'Curry','2022-10-18 23:21:41'),(3033,163,'Roosevelt','2022-10-18 23:21:41'),(3034,163,'Lea','2022-10-18 23:21:41'),(3035,163,'De Baca','2022-10-18 23:21:41'),(3036,163,'Quay','2022-10-18 23:21:41'),(3037,163,'Chaves','2022-10-18 23:21:41'),(3038,163,'Eddy','2022-10-18 23:21:41'),(3039,163,'Lincoln','2022-10-18 23:21:41'),(3040,163,'Otero','2022-10-18 23:21:41'),(3041,163,'Union','2022-10-18 23:21:41'),(3042,160,'Clark','2022-10-18 23:21:41'),(3043,160,'Lincoln','2022-10-18 23:21:41'),(3044,160,'Nye','2022-10-18 23:21:41'),(3045,160,'Esmeralda','2022-10-18 23:21:41'),(3046,160,'White Pine','2022-10-18 23:21:41'),(3047,160,'Lander','2022-10-18 23:21:41'),(3048,160,'Eureka','2022-10-18 23:21:41'),(3049,160,'Washoe','2022-10-18 23:21:41'),(3050,160,'Lyon','2022-10-18 23:21:41'),(3051,160,'Humboldt','2022-10-18 23:21:41'),(3052,160,'Churchill','2022-10-18 23:21:41'),(3053,160,'Douglas','2022-10-18 23:21:41'),(3054,160,'Mineral','2022-10-18 23:21:41'),(3055,160,'Pershing','2022-10-18 23:21:41'),(3056,160,'Storey','2022-10-18 23:21:41'),(3057,160,'Carson City','2022-10-18 23:21:41'),(3058,160,'Elko','2022-10-18 23:21:41'),(3059,133,'Los Angeles','2022-10-18 23:21:41'),(3060,133,'Orange','2022-10-18 23:21:41'),(3061,133,'Ventura','2022-10-18 23:21:41'),(3062,133,'San Bernardino','2022-10-18 23:21:41'),(3063,133,'Riverside','2022-10-18 23:21:41'),(3064,133,'San Diego','2022-10-18 23:21:41'),(3065,133,'Imperial','2022-10-18 23:21:41'),(3066,133,'Inyo','2022-10-18 23:21:41'),(3067,133,'Santa Barbara','2022-10-18 23:21:41'),(3068,133,'Tulare','2022-10-18 23:21:41'),(3069,133,'Kings','2022-10-18 23:21:41'),(3070,133,'Kern','2022-10-18 23:21:41'),(3071,133,'Fresno','2022-10-18 23:21:41'),(3072,133,'San Luis Obispo','2022-10-18 23:21:41'),(3073,133,'Monterey','2022-10-18 23:21:41'),(3074,133,'Mono','2022-10-18 23:21:41'),(3075,133,'Madera','2022-10-18 23:21:41'),(3076,133,'Merced','2022-10-18 23:21:41'),(3077,133,'Mariposa','2022-10-18 23:21:41'),(3078,133,'San Mateo','2022-10-18 23:21:41'),(3079,133,'Santa Clara','2022-10-18 23:21:41'),(3080,133,'San Francisco','2022-10-18 23:21:41'),(3081,133,'Sacramento','2022-10-18 23:21:41'),(3082,133,'Alameda','2022-10-18 23:21:41'),(3083,133,'Napa','2022-10-18 23:21:41'),(3084,133,'Contra Costa','2022-10-18 23:21:41'),(3085,133,'Solano','2022-10-18 23:21:41'),(3086,133,'Marin','2022-10-18 23:21:41'),(3087,133,'Sonoma','2022-10-18 23:21:41'),(3088,133,'Santa Cruz','2022-10-18 23:21:41'),(3089,133,'San Benito','2022-10-18 23:21:41'),(3090,133,'San Joaquin','2022-10-18 23:21:41'),(3091,133,'Calaveras','2022-10-18 23:21:41'),(3092,133,'Tuolumne','2022-10-18 23:21:41'),(3093,133,'Stanislaus','2022-10-18 23:21:41'),(3094,133,'Mendocino','2022-10-18 23:21:41'),(3095,133,'Lake','2022-10-18 23:21:41'),(3096,133,'Humboldt','2022-10-18 23:21:41'),(3097,133,'Trinity','2022-10-18 23:21:41'),(3098,133,'Del Norte','2022-10-18 23:21:41'),(3099,133,'Siskiyou','2022-10-18 23:21:41'),(3100,133,'Amador','2022-10-18 23:21:41'),(3101,133,'Placer','2022-10-18 23:21:41'),(3102,133,'Yolo','2022-10-18 23:21:41'),(3103,133,'El Dorado','2022-10-18 23:21:41'),(3104,133,'Alpine','2022-10-18 23:21:41'),(3105,133,'Sutter','2022-10-18 23:21:41'),(3106,133,'Yuba','2022-10-18 23:21:41'),(3107,133,'Nevada','2022-10-18 23:21:41'),(3108,133,'Sierra','2022-10-18 23:21:41'),(3109,133,'Colusa','2022-10-18 23:21:41'),(3110,133,'Glenn','2022-10-18 23:21:41'),(3111,133,'Butte','2022-10-18 23:21:41'),(3112,133,'Plumas','2022-10-18 23:21:41'),(3113,133,'Shasta','2022-10-18 23:21:41'),(3114,133,'Modoc','2022-10-18 23:21:41'),(3115,133,'Lassen','2022-10-18 23:21:41'),(3116,133,'Tehama','2022-10-18 23:21:41'),(3117,142,'Honolulu','2022-10-18 23:21:41'),(3118,142,'Kauai','2022-10-18 23:21:41'),(3119,142,'Hawaii','2022-10-18 23:21:41'),(3120,142,'Maui','2022-10-18 23:21:41'),(3121,130,'American Samoa','2022-10-18 23:21:41'),(3122,141,'Guam','2022-10-18 23:21:41'),(3123,171,'Palau','2022-10-18 23:21:41'),(3124,138,'Federated States of Micro','2022-10-18 23:21:41'),(3125,167,'Northern Mariana Islands','2022-10-18 23:21:41'),(3126,151,'Marshall Islands','2022-10-18 23:21:41'),(3127,170,'Wasco','2022-10-18 23:21:41'),(3128,170,'Marion','2022-10-18 23:21:41'),(3129,170,'Clackamas','2022-10-18 23:21:41'),(3130,170,'Washington','2022-10-18 23:21:41'),(3131,170,'Multnomah','2022-10-18 23:21:41'),(3132,170,'Hood River','2022-10-18 23:21:41'),(3133,170,'Columbia','2022-10-18 23:21:41'),(3134,170,'Sherman','2022-10-18 23:21:41'),(3135,170,'Yamhill','2022-10-18 23:21:41'),(3136,170,'Clatsop','2022-10-18 23:21:41'),(3137,170,'Tillamook','2022-10-18 23:21:41'),(3138,170,'Polk','2022-10-18 23:21:41'),(3139,170,'Linn','2022-10-18 23:21:41'),(3140,170,'Benton','2022-10-18 23:21:41'),(3141,170,'Lincoln','2022-10-18 23:21:41'),(3142,170,'Lane','2022-10-18 23:21:41'),(3143,170,'Curry','2022-10-18 23:21:41'),(3144,170,'Coos','2022-10-18 23:21:41'),(3145,170,'Douglas','2022-10-18 23:21:41'),(3146,170,'Klamath','2022-10-18 23:21:41'),(3147,170,'Josephine','2022-10-18 23:21:41'),(3148,170,'Jackson','2022-10-18 23:21:41'),(3149,170,'Lake','2022-10-18 23:21:41'),(3150,170,'Deschutes','2022-10-18 23:21:41'),(3151,170,'Harney','2022-10-18 23:21:41'),(3152,170,'Jefferson','2022-10-18 23:21:41'),(3153,170,'Wheeler','2022-10-18 23:21:41'),(3154,170,'Crook','2022-10-18 23:21:41'),(3155,170,'Umatilla','2022-10-18 23:21:41'),(3156,170,'Gilliam','2022-10-18 23:21:41'),(3157,170,'Baker','2022-10-18 23:21:41'),(3158,170,'Grant','2022-10-18 23:21:41'),(3159,170,'Morrow','2022-10-18 23:21:41'),(3160,170,'Union','2022-10-18 23:21:41'),(3161,170,'Wallowa','2022-10-18 23:21:41'),(3162,170,'Malheur','2022-10-18 23:21:41'),(3163,183,'King','2022-10-18 23:21:41'),(3164,183,'Snohomish','2022-10-18 23:21:41'),(3165,183,'Kitsap','2022-10-18 23:21:41'),(3166,183,'Whatcom','2022-10-18 23:21:41'),(3167,183,'Skagit','2022-10-18 23:21:41'),(3168,183,'San Juan','2022-10-18 23:21:41'),(3169,183,'Island','2022-10-18 23:21:41'),(3170,183,'Pierce','2022-10-18 23:21:41'),(3171,183,'Clallam','2022-10-18 23:21:41'),(3172,183,'Jefferson','2022-10-18 23:21:41'),(3173,183,'Lewis','2022-10-18 23:21:41'),(3174,183,'Thurston','2022-10-18 23:21:41'),(3175,183,'Grays Harbor','2022-10-18 23:21:41'),(3176,183,'Mason','2022-10-18 23:21:41'),(3177,183,'Pacific','2022-10-18 23:21:41'),(3178,183,'Cowlitz','2022-10-18 23:21:41'),(3179,183,'Clark','2022-10-18 23:21:41'),(3180,183,'Klickitat','2022-10-18 23:21:41'),(3181,183,'Skamania','2022-10-18 23:21:41'),(3182,183,'Wahkiakum','2022-10-18 23:21:41'),(3183,183,'Chelan','2022-10-18 23:21:41'),(3184,183,'Douglas','2022-10-18 23:21:41'),(3185,183,'Okanogan','2022-10-18 23:21:41'),(3186,183,'Grant','2022-10-18 23:21:41'),(3187,183,'Yakima','2022-10-18 23:21:41'),(3188,183,'Kittitas','2022-10-18 23:21:41'),(3189,183,'Spokane','2022-10-18 23:21:41'),(3190,183,'Lincoln','2022-10-18 23:21:41'),(3191,183,'Stevens','2022-10-18 23:21:41'),(3192,183,'Whitman','2022-10-18 23:21:41'),(3193,183,'Adams','2022-10-18 23:21:41'),(3194,183,'Ferry','2022-10-18 23:21:41'),(3195,183,'Pend Oreille','2022-10-18 23:21:41'),(3196,183,'Franklin','2022-10-18 23:21:41'),(3197,183,'Benton','2022-10-18 23:21:41'),(3198,183,'Walla Walla','2022-10-18 23:21:41'),(3199,183,'Columbia','2022-10-18 23:21:41'),(3200,183,'Garfield','2022-10-18 23:21:41'),(3201,183,'Asotin','2022-10-18 23:21:41'),(3202,128,'Anchorage','2022-10-18 23:21:41'),(3203,128,'Bethel','2022-10-18 23:21:41'),(3204,128,'Aleutians West','2022-10-18 23:21:41'),(3205,128,'Lake And Peninsula','2022-10-18 23:21:41'),(3206,128,'Kodiak Island','2022-10-18 23:21:41'),(3207,128,'Aleutians East','2022-10-18 23:21:41'),(3208,128,'Wade Hampton','2022-10-18 23:21:41'),(3209,128,'Dillingham','2022-10-18 23:21:41'),(3210,128,'Kenai Peninsula','2022-10-18 23:21:41'),(3211,128,'Yukon Koyukuk','2022-10-18 23:21:41'),(3212,128,'Valdez Cordova','2022-10-18 23:21:41'),(3213,128,'Matanuska Susitna','2022-10-18 23:21:41'),(3214,128,'Bristol Bay','2022-10-18 23:21:41'),(3215,128,'Nome','2022-10-18 23:21:41'),(3216,128,'Yakutat','2022-10-18 23:21:41'),(3217,128,'Fairbanks North Star','2022-10-18 23:21:41'),(3218,128,'Denali','2022-10-18 23:21:41'),(3219,128,'North Slope','2022-10-18 23:21:41'),(3220,128,'Northwest Arctic','2022-10-18 23:21:41'),(3221,128,'Southeast Fairbanks','2022-10-18 23:21:41'),(3222,128,'Juneau','2022-10-18 23:21:41'),(3223,128,'Skagway Hoonah Angoon','2022-10-18 23:21:41'),(3224,128,'Haines','2022-10-18 23:21:41'),(3225,128,'Wrangell Petersburg','2022-10-18 23:21:41'),(3226,128,'Sitka','2022-10-18 23:21:41'),(3227,128,'Ketchikan Gateway','2022-10-18 23:21:41'),(3228,128,'Prince Wales Ketchikan','2022-10-18 23:21:41'),(3229,4,'Coconino County','2022-10-18 23:21:41'),(3230,131,'Coconino County','2022-10-18 23:21:41'),(3232,209,'Carleton','2022-10-18 23:21:41'),(3233,4,'Yavapai County','2022-10-18 23:21:41'),(3234,131,'Yavapai County','2022-10-18 23:21:41'),(3236,4,'Maricopa County','2022-10-18 23:21:41'),(3237,131,'Maricopa County','2022-10-18 23:21:41'),(3239,33,'Pershing County','2022-10-18 23:21:41'),(3240,160,'Pershing County','2022-10-18 23:21:41'),(3242,51,'Collingsworth County','2022-10-18 23:21:41'),(3243,178,'Collingsworth County','2022-10-18 23:21:41'),(3245,4,'Mohave County','2022-10-18 23:21:41'),(3246,131,'Mohave County','2022-10-18 23:21:41'),(3248,33,'Humboldt County','2022-10-18 23:21:41'),(3249,160,'Humboldt County','2022-10-18 23:21:41'),(3251,33,'Lyon County','2022-10-18 23:21:41'),(3252,160,'Lyon County','2022-10-18 23:21:41'),(3254,33,'Washoe County','2022-10-18 23:21:41'),(3255,160,'Washoe County','2022-10-18 23:21:41'),(3257,33,'Nye County','2022-10-18 23:21:41'),(3258,160,'Nye County','2022-10-18 23:21:41'),(3260,33,'Eureka County','2022-10-18 23:21:41'),(3261,160,'Eureka County','2022-10-18 23:21:41'),(3263,33,'Lander County','2022-10-18 23:21:41'),(3264,160,'Lander County','2022-10-18 23:21:41'),(3266,33,'White Pine County','2022-10-18 23:21:41'),(3267,160,'White Pine County','2022-10-18 23:21:41'),(3269,33,'Douglas County','2022-10-18 23:21:41'),(3270,160,'Douglas County','2022-10-18 23:21:41'),(3272,33,'Clark County','2022-10-18 23:21:41'),(3273,160,'Clark County','2022-10-18 23:21:41'),(3275,4,'Cochise County','2022-10-18 23:21:41'),(3276,131,'Cochise County','2022-10-18 23:21:41'),(3278,4,'Navajo County','2022-10-18 23:21:41'),(3279,131,'Navajo County','2022-10-18 23:21:41'),(3281,50,'Travis','2022-10-18 23:21:41'),(3282,177,'Travis','2022-10-18 23:21:41'),(3284,33,'Churchill County','2022-10-18 23:21:41'),(3285,160,'Churchill County','2022-10-18 23:21:41'),(3287,33,'Storey County','2022-10-18 23:21:41'),(3288,160,'Storey County','2022-10-18 23:21:41'),(3290,43,'Owyhee','2022-10-18 23:21:41'),(3291,170,'Owyhee','2022-10-18 23:21:41'),(3293,6,'Mendocino County','2022-10-18 23:21:41'),(3294,133,'Mendocino County','2022-10-18 23:21:41'),(3296,6,'Inyo County','2022-10-18 23:21:41'),(3297,133,'Inyo County','2022-10-18 23:21:41'),(3299,6,'Mono County','2022-10-18 23:21:41'),(3300,133,'Mono County','2022-10-18 23:21:41'),(3302,4,'Pima County','2022-10-18 23:21:41'),(3303,131,'Pima County','2022-10-18 23:21:41'),(3305,424,'Matale','2022-10-18 23:21:41'),(3306,52,'Garfield County','2022-10-18 23:21:41'),(3307,179,'Garfield County','2022-10-18 23:21:41'),(3309,447,'Starogard Gdaski','2022-10-18 23:21:41'),(3310,449,'Zwickau','2022-10-18 23:21:41'),(3311,4,'Pinal County','2022-10-18 23:21:41'),(3312,131,'Pinal County','2022-10-18 23:21:41'),(3314,4,'Graham County','2022-10-18 23:21:41'),(3315,131,'Graham County','2022-10-18 23:21:41'),(3317,52,'San Juan County','2022-10-18 23:21:41'),(3318,179,'San Juan County','2022-10-18 23:21:41'),(3320,197,'San Felipe de JesÃºs','2022-10-18 23:21:41'),(3321,211,'San Felipe de JesÃºs','2022-10-18 23:21:41'),(3323,197,'YÃ©cora','2022-10-18 23:21:41'),(3324,211,'YÃ©cora','2022-10-18 23:21:41'),(3326,197,'Cucurpe','2022-10-18 23:21:41'),(3327,211,'Cucurpe','2022-10-18 23:21:41'),(3329,197,'Ãlamos','2022-10-18 23:21:41'),(3330,211,'Alamos','2022-10-18 23:21:41'),(3331,231,'Aguascalientes','2022-10-18 23:21:41'),(3332,231,'Asientos','2022-10-18 23:21:41'),(3333,231,'Calvillo','2022-10-18 23:21:41'),(3334,231,'CosÃ­o','2022-10-18 23:21:41'),(3335,231,'JesÃºs MarÃ­a','2022-10-18 23:21:41'),(3336,231,'PabellÃ³n de Arteaga','2022-10-18 23:21:41'),(3337,231,'RincÃ³n de Romos','2022-10-18 23:21:41'),(3338,231,'San JosÃ© de Gracia','2022-10-18 23:21:41'),(3339,231,'TepezalÃ¡','2022-10-18 23:21:41'),(3340,231,'El Llano','2022-10-18 23:21:41'),(3341,231,'San Francisco de los Romo','2022-10-18 23:21:41'),(3342,198,'Ensenada','2022-10-18 23:21:41'),(3343,198,'Mexicali','2022-10-18 23:21:41'),(3344,198,'Tecate','2022-10-18 23:21:41'),(3345,198,'Tijuana','2022-10-18 23:21:41'),(3346,198,'Playas de Rosarito','2022-10-18 23:21:41'),(3347,253,'ComondÃº','2022-10-18 23:21:41'),(3348,253,'MulegÃ©','2022-10-18 23:21:41'),(3349,253,'La Paz','2022-10-18 23:21:41'),(3350,253,'Los Cabos','2022-10-18 23:21:41'),(3351,253,'Loreto','2022-10-18 23:21:41'),(3352,263,'CalkinÃ­','2022-10-18 23:21:41'),(3353,263,'Campeche','2022-10-18 23:21:41'),(3354,263,'Carmen','2022-10-18 23:21:41'),(3355,263,'ChampotÃ³n','2022-10-18 23:21:41'),(3356,263,'HecelchakÃ¡n','2022-10-18 23:21:41'),(3357,263,'HopelchÃ©n','2022-10-18 23:21:41'),(3358,263,'Palizada','2022-10-18 23:21:41'),(3359,263,'Tenabo','2022-10-18 23:21:41'),(3360,263,'EscÃ¡rcega','2022-10-18 23:21:41'),(3361,263,'Calakmul','2022-10-18 23:21:41'),(3362,263,'Candelaria','2022-10-18 23:21:41'),(3363,273,'Acacoyagua','2022-10-18 23:21:41'),(3364,273,'Acala','2022-10-18 23:21:41'),(3365,273,'Acapetahua','2022-10-18 23:21:41'),(3366,273,'Altamirano','2022-10-18 23:21:41'),(3367,273,'AmatÃ¡n','2022-10-18 23:21:41'),(3368,273,'Amatenango de la Frontera','2022-10-18 23:21:41'),(3369,273,'Amatenango del Valle','2022-10-18 23:21:41'),(3370,273,'Angel Albino Corzo','2022-10-18 23:21:41'),(3371,273,'Arriaga','2022-10-18 23:21:41'),(3372,273,'Bejucal de Ocampo','2022-10-18 23:21:41'),(3373,273,'Bella Vista','2022-10-18 23:21:41'),(3374,273,'BerriozÃ¡bal','2022-10-18 23:21:41'),(3375,273,'Bochil','2022-10-18 23:21:41'),(3376,273,'El Bosque','2022-10-18 23:21:41'),(3377,273,'CacahoatÃ¡n','2022-10-18 23:21:41'),(3378,273,'CatazajÃ¡','2022-10-18 23:21:41'),(3379,273,'Cintalapa','2022-10-18 23:21:41'),(3380,273,'Coapilla','2022-10-18 23:21:41'),(3381,273,'ComitÃ¡n de DomÃ­nguez','2022-10-18 23:21:41'),(3382,273,'La Concordia','2022-10-18 23:21:41'),(3383,273,'CopainalÃ¡','2022-10-18 23:21:41'),(3384,273,'ChalchihuitÃ¡n','2022-10-18 23:21:41'),(3385,273,'Chamula','2022-10-18 23:21:41'),(3386,273,'Chanal','2022-10-18 23:21:41'),(3387,273,'Chapultenango','2022-10-18 23:21:41'),(3388,273,'ChenalhÃ³','2022-10-18 23:21:41'),(3389,273,'Chiapa de Corzo','2022-10-18 23:21:41'),(3390,273,'Chiapilla','2022-10-18 23:21:41'),(3391,273,'ChicoasÃ©n','2022-10-18 23:21:41'),(3392,273,'Chicomuselo','2022-10-18 23:21:41'),(3393,273,'ChilÃ³n','2022-10-18 23:21:41'),(3394,273,'Escuintla','2022-10-18 23:21:41'),(3395,273,'Francisco LeÃ³n','2022-10-18 23:21:41'),(3396,273,'Frontera Comalapa','2022-10-18 23:21:41'),(3397,273,'Frontera Hidalgo','2022-10-18 23:21:41'),(3398,273,'La Grandeza','2022-10-18 23:21:41'),(3399,273,'HuehuetÃ¡n','2022-10-18 23:21:41'),(3400,273,'HuixtÃ¡n','2022-10-18 23:21:41'),(3401,273,'HuitiupÃ¡n','2022-10-18 23:21:41'),(3402,273,'Huixtla','2022-10-18 23:21:41'),(3403,273,'La Independencia','2022-10-18 23:21:41'),(3404,273,'IxhuatÃ¡n','2022-10-18 23:21:41'),(3405,273,'IxtacomitÃ¡n','2022-10-18 23:21:41'),(3406,273,'Ixtapa','2022-10-18 23:21:41'),(3407,273,'Ixtapangajoya','2022-10-18 23:21:41'),(3408,273,'Jiquipilas','2022-10-18 23:21:41'),(3409,273,'Jitotol','2022-10-18 23:21:41'),(3410,273,'JuÃ¡rez','2022-10-18 23:21:41'),(3411,273,'LarrÃ¡inzar','2022-10-18 23:21:41'),(3412,273,'La Libertad','2022-10-18 23:21:41'),(3413,273,'Mapastepec','2022-10-18 23:21:41'),(3414,273,'Las Margaritas','2022-10-18 23:21:41'),(3415,273,'Mazapa de Madero','2022-10-18 23:21:41'),(3416,273,'MazatÃ¡n','2022-10-18 23:21:41'),(3417,273,'Metapa','2022-10-18 23:21:41'),(3418,273,'Mitontic','2022-10-18 23:21:41'),(3419,273,'Motozintla','2022-10-18 23:21:41'),(3420,273,'NicolÃ¡s RuÃ­z','2022-10-18 23:21:41'),(3421,273,'Ocosingo','2022-10-18 23:21:41'),(3422,273,'Ocotepec','2022-10-18 23:21:41'),(3423,273,'Ocozocoautla de Espinosa','2022-10-18 23:21:41'),(3424,273,'OstuacÃ¡n','2022-10-18 23:21:41'),(3425,273,'Osumacinta','2022-10-18 23:21:41'),(3426,273,'Oxchuc','2022-10-18 23:21:41'),(3427,273,'Palenque','2022-10-18 23:21:41'),(3428,273,'PantelhÃ³','2022-10-18 23:21:41'),(3429,273,'Pantepec','2022-10-18 23:21:41'),(3430,273,'Pichucalco','2022-10-18 23:21:41'),(3431,273,'Pijijiapan','2022-10-18 23:21:41'),(3432,273,'El Porvenir','2022-10-18 23:21:41'),(3433,273,'Villa ComaltitlÃ¡n','2022-10-18 23:21:41'),(3434,273,'Pueblo Nuevo SolistahuacÃ¡n','2022-10-18 23:21:41'),(3435,273,'RayÃ³n','2022-10-18 23:21:41'),(3436,273,'Reforma','2022-10-18 23:21:41'),(3437,273,'Las Rosas','2022-10-18 23:21:41'),(3438,273,'Sabanilla','2022-10-18 23:21:41'),(3439,273,'Salto de Agua','2022-10-18 23:21:41'),(3440,273,'San CristÃ³bal de las Casas','2022-10-18 23:21:41'),(3441,273,'San Fernando','2022-10-18 23:21:41'),(3442,273,'Siltepec','2022-10-18 23:21:41'),(3443,273,'Simojovel','2022-10-18 23:21:41'),(3444,273,'SitalÃ¡','2022-10-18 23:21:41'),(3445,273,'Socoltenango','2022-10-18 23:21:41'),(3446,273,'Solosuchiapa','2022-10-18 23:21:41'),(3447,273,'SoyalÃ³','2022-10-18 23:21:41'),(3448,273,'Suchiapa','2022-10-18 23:21:41'),(3449,273,'Suchiate','2022-10-18 23:21:41'),(3450,273,'Sunuapa','2022-10-18 23:21:41'),(3451,273,'Tapachula','2022-10-18 23:21:41'),(3452,273,'Tapalapa','2022-10-18 23:21:41'),(3453,273,'Tapilula','2022-10-18 23:21:41'),(3454,273,'TecpatÃ¡n','2022-10-18 23:21:41'),(3455,273,'Tenejapa','2022-10-18 23:21:41'),(3456,273,'Teopisca','2022-10-18 23:21:41'),(3457,273,'Tila','2022-10-18 23:21:41'),(3458,273,'TonalÃ¡','2022-10-18 23:21:41'),(3459,273,'Totolapa','2022-10-18 23:21:41'),(3460,273,'La Trinitaria','2022-10-18 23:21:41'),(3461,273,'TumbalÃ¡','2022-10-18 23:21:41'),(3462,273,'Tuxtla GutiÃ©rrez','2022-10-18 23:21:41'),(3463,273,'Tuxtla Chico','2022-10-18 23:21:41'),(3464,273,'TuzantÃ¡n','2022-10-18 23:21:41'),(3465,273,'Tzimol','2022-10-18 23:21:41'),(3466,273,'UniÃ³n JuÃ¡rez','2022-10-18 23:21:41'),(3467,273,'Venustiano Carranza','2022-10-18 23:21:41'),(3468,273,'Villa Corzo','2022-10-18 23:21:41'),(3469,273,'Villaflores','2022-10-18 23:21:41'),(3470,273,'YajalÃ³n','2022-10-18 23:21:41'),(3471,273,'San Lucas','2022-10-18 23:21:41'),(3472,273,'ZinacantÃ¡n','2022-10-18 23:21:41'),(3473,273,'San Juan Cancuc','2022-10-18 23:21:41'),(3474,273,'Aldama','2022-10-18 23:21:41'),(3475,273,'BenemÃ©rito de las AmÃ©ricas','2022-10-18 23:21:41'),(3476,273,'Maravilla Tenejapa','2022-10-18 23:21:41'),(3477,273,'MarquÃ©s de Comillas','2022-10-18 23:21:41'),(3478,273,'Montecristo de Guerrero','2022-10-18 23:21:41'),(3479,273,'San AndrÃ©s Duraznal','2022-10-18 23:21:41'),(3480,273,'Santiago el Pinar','2022-10-18 23:21:41'),(3481,273,'Belisario DomÃ­nguez','2022-10-18 23:21:41'),(3482,273,'Emiliano Zapata','2022-10-18 23:21:41'),(3483,273,'El Parral','2022-10-18 23:21:41'),(3484,273,'Mezcalapa','2022-10-18 23:21:41'),(3485,206,'Ahumada','2022-10-18 23:21:41'),(3486,206,'Aldama','2022-10-18 23:21:41'),(3487,206,'Allende','2022-10-18 23:21:41'),(3488,206,'Aquiles SerdÃ¡n','2022-10-18 23:21:41'),(3489,206,'AscensiÃ³n','2022-10-18 23:21:41'),(3490,206,'Bachiniva','2022-10-18 23:21:41'),(3491,206,'Balleza','2022-10-18 23:21:41'),(3492,206,'Batopilas','2022-10-18 23:21:41'),(3493,206,'Bocoyna','2022-10-18 23:21:41'),(3494,206,'Buenaventura','2022-10-18 23:21:41'),(3495,206,'Camargo','2022-10-18 23:21:41'),(3496,206,'Carichi','2022-10-18 23:21:41'),(3497,206,'Casas Grandes','2022-10-18 23:21:41'),(3498,206,'Coronado','2022-10-18 23:21:41'),(3499,206,'Coyame del Sotol','2022-10-18 23:21:41'),(3500,206,'La Cruz','2022-10-18 23:21:41'),(3501,206,'CuauhtÃ©moc','2022-10-18 23:21:41'),(3502,206,'Cusihuiriachi','2022-10-18 23:21:41'),(3503,206,'Chihuahua','2022-10-18 23:21:41'),(3504,206,'ChÃ­nipas','2022-10-18 23:21:41'),(3505,206,'Delicias','2022-10-18 23:21:41'),(3506,206,'Dr. Belisario DomÃ­nguez','2022-10-18 23:21:41'),(3507,206,'Galeana','2022-10-18 23:21:41'),(3508,206,'Santa Isabel','2022-10-18 23:21:41'),(3509,206,'GÃ³mez FarÃ­as','2022-10-18 23:21:41'),(3510,206,'Gran Morelos','2022-10-18 23:21:41'),(3511,206,'Guachochi','2022-10-18 23:21:41'),(3512,206,'Guadalupe','2022-10-18 23:21:41'),(3513,206,'Guadalupe y Calvo','2022-10-18 23:21:41'),(3514,206,'Guazapares','2022-10-18 23:21:41'),(3515,206,'Guerrero','2022-10-18 23:21:41'),(3516,206,'Hidalgo del Parral','2022-10-18 23:21:41'),(3517,206,'HuejotitÃ¡n','2022-10-18 23:21:41'),(3518,206,'Ignacio Zaragoza','2022-10-18 23:21:41'),(3519,206,'Janos','2022-10-18 23:21:41'),(3520,206,'JimÃ©nez','2022-10-18 23:21:41'),(3521,206,'JuÃ¡rez','2022-10-18 23:21:41'),(3522,206,'Julimes','2022-10-18 23:21:41'),(3523,206,'LÃ³pez','2022-10-18 23:21:41'),(3524,206,'Madera','2022-10-18 23:21:41'),(3525,206,'Maguarichi','2022-10-18 23:21:41'),(3526,206,'Manuel Benavides','2022-10-18 23:21:41'),(3527,206,'Matachi','2022-10-18 23:21:41'),(3528,206,'Matamoros','2022-10-18 23:21:41'),(3529,206,'Meoqui','2022-10-18 23:21:41'),(3530,206,'Morelos','2022-10-18 23:21:41'),(3531,206,'Moris','2022-10-18 23:21:41'),(3532,206,'Namiquipa','2022-10-18 23:21:41'),(3533,206,'Nonoava','2022-10-18 23:21:41'),(3534,206,'Nuevo Casas Grandes','2022-10-18 23:21:41'),(3535,206,'Ocampo','2022-10-18 23:21:41'),(3536,206,'Ojinaga','2022-10-18 23:21:41'),(3537,206,'PrÃ¡xedis G. Guerrero','2022-10-18 23:21:41'),(3538,206,'Riva Palacio','2022-10-18 23:21:41'),(3539,206,'Rosales','2022-10-18 23:21:41'),(3540,206,'Rosario','2022-10-18 23:21:41'),(3541,206,'San Francisco de Borja','2022-10-18 23:21:41'),(3542,206,'San Francisco de Conchos','2022-10-18 23:21:41'),(3543,206,'San Francisco del Oro','2022-10-18 23:21:41'),(3544,206,'Santa BÃ¡rbara','2022-10-18 23:21:41'),(3545,206,'Satevo','2022-10-18 23:21:41'),(3546,206,'Saucillo','2022-10-18 23:21:41'),(3547,206,'TemÃ³sachi','2022-10-18 23:21:41'),(3548,206,'El Tule','2022-10-18 23:21:41'),(3549,206,'Urique','2022-10-18 23:21:41'),(3550,206,'Uruachi','2022-10-18 23:21:41'),(3551,206,'Valle de Zaragoza','2022-10-18 23:21:41'),(3552,205,'Abasolo','2022-10-18 23:21:41'),(3553,205,'AcuÃ±a','2022-10-18 23:21:41'),(3554,205,'Allende','2022-10-18 23:21:41'),(3555,205,'Arteaga','2022-10-18 23:21:41'),(3556,205,'Candela','2022-10-18 23:21:41'),(3557,205,'CastaÃ±os','2022-10-18 23:21:41'),(3558,205,'CuatrociÃ©negas','2022-10-18 23:21:41'),(3559,205,'Escobedo','2022-10-18 23:21:41'),(3560,205,'Francisco I. Madero','2022-10-18 23:21:41'),(3561,205,'Frontera','2022-10-18 23:21:41'),(3562,205,'General Cepeda','2022-10-18 23:21:41'),(3563,205,'Guerrero','2022-10-18 23:21:41'),(3564,205,'Hidalgo','2022-10-18 23:21:41'),(3565,205,'JimÃ©nez','2022-10-18 23:21:41'),(3566,205,'JuÃ¡rez','2022-10-18 23:21:41'),(3567,205,'Lamadrid','2022-10-18 23:21:41'),(3568,205,'Matamoros','2022-10-18 23:21:41'),(3569,205,'Monclova','2022-10-18 23:21:41'),(3570,205,'Morelos','2022-10-18 23:21:41'),(3571,205,'MÃºzquiz','2022-10-18 23:21:41'),(3572,205,'Nadadores','2022-10-18 23:21:41'),(3573,205,'Nava','2022-10-18 23:21:41'),(3574,205,'Ocampo','2022-10-18 23:21:41'),(3575,205,'Parras','2022-10-18 23:21:41'),(3576,205,'Piedras Negras','2022-10-18 23:21:41'),(3577,205,'Progreso','2022-10-18 23:21:41'),(3578,205,'Ramos Arizpe','2022-10-18 23:21:41'),(3579,205,'Sabinas','2022-10-18 23:21:41'),(3580,205,'Sacramento','2022-10-18 23:21:41'),(3581,205,'Saltillo','2022-10-18 23:21:41'),(3582,205,'San Buenaventura','2022-10-18 23:21:41'),(3583,205,'San Juan de Sabinas','2022-10-18 23:21:41'),(3584,205,'San Pedro de las Colonias','2022-10-18 23:21:41'),(3585,205,'Sierra Mojada','2022-10-18 23:21:41'),(3586,205,'TorreÃ³n','2022-10-18 23:21:41'),(3587,205,'Viesca','2022-10-18 23:21:41'),(3588,205,'Villa UniÃ³n','2022-10-18 23:21:41'),(3589,205,'Zaragoza','2022-10-18 23:21:41'),(3590,278,'ArmerÃ­a','2022-10-18 23:21:41'),(3591,278,'Colima','2022-10-18 23:21:41'),(3592,278,'Comala','2022-10-18 23:21:41'),(3593,278,'CoquimatlÃ¡n','2022-10-18 23:21:41'),(3594,278,'CuauhtÃ©moc','2022-10-18 23:21:41'),(3595,278,'IxtlahuacÃ¡n','2022-10-18 23:21:41'),(3596,278,'Manzanillo','2022-10-18 23:21:41'),(3597,278,'MinatitlÃ¡n','2022-10-18 23:21:41'),(3598,278,'TecomÃ¡n','2022-10-18 23:21:41'),(3599,278,'Villa de Ãlvarez','2022-10-18 23:21:41'),(3600,290,'CanatlÃ¡n','2022-10-18 23:21:41'),(3601,290,'Canelas','2022-10-18 23:21:41'),(3602,290,'Coneto de Comonfort','2022-10-18 23:21:41'),(3603,290,'CuencamÃ©','2022-10-18 23:21:41'),(3604,290,'Durango','2022-10-18 23:21:41'),(3605,290,'General SimÃ³n BolÃ­var','2022-10-18 23:21:41'),(3606,290,'GÃ³mez Palacio','2022-10-18 23:21:41'),(3607,290,'Guadalupe Victoria','2022-10-18 23:21:41'),(3608,290,'GuanacevÃ­','2022-10-18 23:21:41'),(3609,290,'Hidalgo','2022-10-18 23:21:41'),(3610,290,'IndÃ©','2022-10-18 23:21:41'),(3611,290,'Lerdo','2022-10-18 23:21:41'),(3612,290,'MapimÃ­','2022-10-18 23:21:41'),(3613,290,'Mezquital','2022-10-18 23:21:41'),(3614,290,'Nazas','2022-10-18 23:21:41'),(3615,290,'Nombre de Dios','2022-10-18 23:21:41'),(3616,290,'Ocampo','2022-10-18 23:21:41'),(3617,290,'El Oro','2022-10-18 23:21:41'),(3618,290,'OtÃ¡ez','2022-10-18 23:21:41'),(3619,290,'PÃ¡nuco de Coronado','2022-10-18 23:21:41'),(3620,290,'PeÃ±Ã³n Blanco','2022-10-18 23:21:41'),(3621,290,'Poanas','2022-10-18 23:21:41'),(3622,290,'Pueblo Nuevo','2022-10-18 23:21:41'),(3623,290,'Rodeo','2022-10-18 23:21:41'),(3624,290,'San Bernardo','2022-10-18 23:21:41'),(3625,290,'San Dimas','2022-10-18 23:21:41'),(3626,290,'San Juan de Guadalupe','2022-10-18 23:21:41'),(3627,290,'San Juan del RÃ­o','2022-10-18 23:21:41'),(3628,290,'San Luis del Cordero','2022-10-18 23:21:41'),(3629,290,'San Pedro del Gallo','2022-10-18 23:21:41'),(3630,290,'Santa Clara','2022-10-18 23:21:41'),(3631,290,'Santiago Papasquiaro','2022-10-18 23:21:41'),(3632,290,'SÃºchil','2022-10-18 23:21:41'),(3633,290,'Tamazula','2022-10-18 23:21:41'),(3634,290,'Tepehuanes','2022-10-18 23:21:41'),(3635,290,'Tlahualilo','2022-10-18 23:21:41'),(3636,290,'Topia','2022-10-18 23:21:41'),(3637,290,'Vicente Guerrero','2022-10-18 23:21:41'),(3638,290,'Nuevo Ideal','2022-10-18 23:21:41'),(3639,297,'Abasolo','2022-10-18 23:21:41'),(3640,297,'AcÃ¡mbaro','2022-10-18 23:21:41'),(3641,297,'Allende','2022-10-18 23:21:41'),(3642,297,'Apaseo el Alto','2022-10-18 23:21:41'),(3643,297,'Apaseo el Grande','2022-10-18 23:21:41'),(3644,297,'Atarjea','2022-10-18 23:21:41'),(3645,297,'Celaya','2022-10-18 23:21:41'),(3646,297,'Manuel Doblado','2022-10-18 23:21:41'),(3647,297,'Comonfort','2022-10-18 23:21:41'),(3648,297,'Coroneo','2022-10-18 23:21:41'),(3649,297,'Cortazar','2022-10-18 23:21:41'),(3650,297,'CuerÃ¡maro','2022-10-18 23:21:41'),(3651,297,'Doctor Mora','2022-10-18 23:21:41'),(3652,297,'Dolores Hidalgo','2022-10-18 23:21:41'),(3653,297,'Guanajuato','2022-10-18 23:21:41'),(3654,297,'HuanÃ­maro','2022-10-18 23:21:41'),(3655,297,'Irapuato','2022-10-18 23:21:41'),(3656,297,'Jaral del Progreso','2022-10-18 23:21:41'),(3657,297,'JerÃ©cuaro','2022-10-18 23:21:41'),(3658,297,'LeÃ³n','2022-10-18 23:21:41'),(3659,297,'MoroleÃ³n','2022-10-18 23:21:41'),(3660,297,'Ocampo','2022-10-18 23:21:41'),(3661,297,'PÃ©njamo','2022-10-18 23:21:41'),(3662,297,'Pueblo Nuevo','2022-10-18 23:21:41'),(3663,297,'PurÃ­sima del RincÃ³n','2022-10-18 23:21:41'),(3664,297,'Romita','2022-10-18 23:21:41'),(3665,297,'Salamanca','2022-10-18 23:21:41'),(3666,297,'Salvatierra','2022-10-18 23:21:41'),(3667,297,'San Diego de la UniÃ³n','2022-10-18 23:21:41'),(3668,297,'San Felipe','2022-10-18 23:21:41'),(3669,297,'San Francisco del RincÃ³n','2022-10-18 23:21:41'),(3670,297,'San JosÃ© Iturbide','2022-10-18 23:21:41'),(3671,297,'San Luis de la Paz','2022-10-18 23:21:41'),(3672,297,'Santa Catarina','2022-10-18 23:21:41'),(3673,297,'Santa Cruz de Juventino Rosas','2022-10-18 23:21:41'),(3674,297,'Santiago MaravatÃ­o','2022-10-18 23:21:41'),(3675,297,'Silao','2022-10-18 23:21:41'),(3676,297,'Tarandacuao','2022-10-18 23:21:41'),(3677,297,'Tarimoro','2022-10-18 23:21:41'),(3678,297,'Tierra Blanca','2022-10-18 23:21:41'),(3679,297,'Uriangato','2022-10-18 23:21:41'),(3680,297,'Valle de Santiago','2022-10-18 23:21:41'),(3681,297,'Victoria','2022-10-18 23:21:41'),(3682,297,'VillagrÃ¡n','2022-10-18 23:21:41'),(3683,297,'XichÃº','2022-10-18 23:21:41'),(3684,297,'Yuriria','2022-10-18 23:21:41'),(3685,300,'Acapulco de JuÃ¡rez','2022-10-18 23:21:41'),(3686,300,'Acatepec','2022-10-18 23:21:41'),(3687,300,'AjuchitlÃ¡n del Progreso','2022-10-18 23:21:41'),(3688,300,'Ahuacuotzingo','2022-10-18 23:21:41'),(3689,300,'Alcozauca de Guerrero','2022-10-18 23:21:41'),(3690,300,'Alpoyeca','2022-10-18 23:21:41'),(3691,300,'Apaxtla','2022-10-18 23:21:41'),(3692,300,'Arcelia','2022-10-18 23:21:41'),(3693,300,'Atenango del RÃ­o','2022-10-18 23:21:41'),(3694,300,'Atlamajalcingo del Monte','2022-10-18 23:21:41'),(3695,300,'Atlixtac','2022-10-18 23:21:41'),(3696,300,'Atoyac de Ãlvarez','2022-10-18 23:21:41'),(3697,300,'Ayutla de los Libres','2022-10-18 23:21:41'),(3698,300,'Azoyu','2022-10-18 23:21:41'),(3699,300,'Benito JuÃ¡rez','2022-10-18 23:21:41'),(3700,300,'Buenavista de CuÃ©llar','2022-10-18 23:21:41'),(3701,300,'Chilapa de Ãlvarez','2022-10-18 23:21:41'),(3702,300,'Chilpancingo de los Bravo','2022-10-18 23:21:41'),(3703,300,'Coahuayutla de JosÃ© MarÃ­a Izazaga','2022-10-18 23:21:41'),(3704,300,'Cocula','2022-10-18 23:21:41'),(3705,300,'Copala','2022-10-18 23:21:41'),(3706,300,'Copalillo','2022-10-18 23:21:41'),(3707,300,'Copanatoyac','2022-10-18 23:21:41'),(3708,300,'Coyuca de BenÃ­tez','2022-10-18 23:21:41'),(3709,300,'Coyuca de CatalÃ¡n','2022-10-18 23:21:41'),(3710,300,'Cuajinicuilapa','2022-10-18 23:21:41'),(3711,300,'Cualac','2022-10-18 23:21:41'),(3712,300,'Cuautepec','2022-10-18 23:21:41'),(3713,300,'Cuetzala del Progreso','2022-10-18 23:21:41'),(3714,300,'Cutzamala de PinzÃ³n','2022-10-18 23:21:41'),(3715,300,'Eduardo Neri','2022-10-18 23:21:41'),(3716,300,'Florencio Villarreal','2022-10-18 23:21:41'),(3717,300,'General Canuto A. Neri','2022-10-18 23:21:41'),(3718,300,'General Heliodoro Castillo','2022-10-18 23:21:41'),(3719,300,'HuamuxtitlÃ¡n','2022-10-18 23:21:41'),(3720,300,'Huitzuco de los Figueroa','2022-10-18 23:21:41'),(3721,300,'Iguala de la Independencia','2022-10-18 23:21:41'),(3722,300,'Igualapa','2022-10-18 23:21:41'),(3723,300,'Ixcateopan de CuauhtÃ©moc','2022-10-18 23:21:41'),(3724,300,'Zihuatanejo de Azueta','2022-10-18 23:21:41'),(3725,300,'Juan R. Escudero','2022-10-18 23:21:41'),(3726,300,'La UniÃ³n de Isidoro Montes de Oca','2022-10-18 23:21:41'),(3727,300,'Leonardo Bravo','2022-10-18 23:21:41'),(3728,300,'Malinaltepec','2022-10-18 23:21:41'),(3729,300,'MÃ¡rtir de CuilapÃ¡n','2022-10-18 23:21:41'),(3730,300,'Metlatonoc','2022-10-18 23:21:41'),(3731,300,'MochitlÃ¡n','2022-10-18 23:21:41'),(3732,300,'OlinalÃ¡','2022-10-18 23:21:41'),(3733,300,'Ometepec','2022-10-18 23:21:41'),(3734,300,'Pedro Ascencio Alquisiras','2022-10-18 23:21:41'),(3735,300,'PetatlÃ¡n','2022-10-18 23:21:41'),(3736,300,'Pilcaya','2022-10-18 23:21:41'),(3737,300,'Pungarabato','2022-10-18 23:21:41'),(3738,300,'Quechultenango','2022-10-18 23:21:41'),(3739,300,'San Luis AcatlÃ¡n','2022-10-18 23:21:41'),(3740,300,'San Marcos','2022-10-18 23:21:41'),(3741,300,'San Miguel Totolapan','2022-10-18 23:21:41'),(3742,300,'Taxco de AlarcÃ³n','2022-10-18 23:21:41'),(3743,300,'Tecoanapa','2022-10-18 23:21:41'),(3744,300,'TecpÃ¡n de Galeana','2022-10-18 23:21:41'),(3745,300,'Teloloapan','2022-10-18 23:21:41'),(3746,300,'Tepecoacuilco de Trujano','2022-10-18 23:21:41'),(3747,300,'Tetipac','2022-10-18 23:21:41'),(3748,300,'Tixtla de Guerrero','2022-10-18 23:21:41'),(3749,300,'Tlacoachistlahuaca','2022-10-18 23:21:41'),(3750,300,'Tlacoapa','2022-10-18 23:21:41'),(3751,300,'Tlalchapa','2022-10-18 23:21:41'),(3752,300,'Tlalixtaquilla de Maldonado','2022-10-18 23:21:41'),(3753,300,'Tlapa de Comonfort','2022-10-18 23:21:41'),(3754,300,'Tlapehuala','2022-10-18 23:21:41'),(3755,300,'Xalpatlahuac','2022-10-18 23:21:41'),(3756,300,'Xochihuehuetlan','2022-10-18 23:21:41'),(3757,300,'Xochistlahuaca','2022-10-18 23:21:41'),(3758,300,'ZapotitlÃ¡n Tablas','2022-10-18 23:21:41'),(3759,300,'ZirÃ¡ndaro','2022-10-18 23:21:41'),(3760,300,'Zitlala','2022-10-18 23:21:41'),(3761,300,'Marquelia','2022-10-18 23:21:41'),(3762,300,'Cochoapa el Grande','2022-10-18 23:21:41'),(3763,300,'JosÃ© JoaquÃ­n de Herrera','2022-10-18 23:21:41'),(3764,300,'JuchitÃ¡n','2022-10-18 23:21:41'),(3765,300,'Iliatenco','2022-10-18 23:21:41'),(3766,215,'AcatlÃ¡n','2022-10-18 23:21:41'),(3767,215,'AcaxochitlÃ¡n','2022-10-18 23:21:41'),(3768,215,'Actopan','2022-10-18 23:21:41'),(3769,215,'Agua Blanca de Iturbide','2022-10-18 23:21:41'),(3770,215,'Ajacuba','2022-10-18 23:21:41'),(3771,215,'Alfajayucan','2022-10-18 23:21:41'),(3772,215,'Almoloya','2022-10-18 23:21:41'),(3773,215,'Apan','2022-10-18 23:21:41'),(3774,215,'El Arenal','2022-10-18 23:21:41'),(3775,215,'AtitalaquÃ­a','2022-10-18 23:21:41'),(3776,215,'Atlapexco','2022-10-18 23:21:41'),(3777,215,'Atotonilco de Tula','2022-10-18 23:21:41'),(3778,215,'Atotonilco El Grande','2022-10-18 23:21:41'),(3779,215,'Calnali','2022-10-18 23:21:41'),(3780,215,'Cardonal','2022-10-18 23:21:41'),(3781,215,'Chapantongo','2022-10-18 23:21:41'),(3782,215,'ChapulhuacÃ¡n','2022-10-18 23:21:41'),(3783,215,'Chilcuautla','2022-10-18 23:21:41'),(3784,215,'Cuautepec de Hinojosa','2022-10-18 23:21:41'),(3785,215,'EloxochitlÃ¡n','2022-10-18 23:21:41'),(3786,215,'Emiliano Zapata','2022-10-18 23:21:41'),(3787,215,'Epazoyucan','2022-10-18 23:21:41'),(3788,215,'Francisco I. Madero','2022-10-18 23:21:41'),(3789,215,'Huasca de Ocampo','2022-10-18 23:21:41'),(3790,215,'Huautla','2022-10-18 23:21:41'),(3791,215,'Huazalingo','2022-10-18 23:21:41'),(3792,215,'Huehuetla','2022-10-18 23:21:41'),(3793,215,'Huejutla de Reyes','2022-10-18 23:21:41'),(3794,215,'Huichapan','2022-10-18 23:21:41'),(3795,215,'Ixmiquilpan','2022-10-18 23:21:41'),(3796,215,'Jacala de Ledezma','2022-10-18 23:21:41'),(3797,215,'Jaltocan','2022-10-18 23:21:41'),(3798,215,'JuÃ¡rez','2022-10-18 23:21:41'),(3799,215,'Lolotla','2022-10-18 23:21:41'),(3800,215,'Metepec','2022-10-18 23:21:41'),(3801,215,'MetztitlÃ¡n','2022-10-18 23:21:41'),(3802,215,'Mineral de la Reforma','2022-10-18 23:21:41'),(3803,215,'Mineral del Chico','2022-10-18 23:21:41'),(3804,215,'Mineral del Monte','2022-10-18 23:21:41'),(3805,215,'La MisiÃ³n','2022-10-18 23:21:41'),(3806,215,'Mixquiahuala de JuÃ¡rez','2022-10-18 23:21:41'),(3807,215,'Molango de Escamilla','2022-10-18 23:21:41'),(3808,215,'NicolÃ¡s Flores','2022-10-18 23:21:41'),(3809,215,'Nopala de VillagrÃ¡n','2022-10-18 23:21:41'),(3810,215,'OmitlÃ¡n de JuÃ¡rez','2022-10-18 23:21:41'),(3811,215,'Pisaflores','2022-10-18 23:21:41'),(3812,215,'Pacula','2022-10-18 23:21:41'),(3813,215,'Pachuca de Soto','2022-10-18 23:21:41'),(3814,215,'Progreso de ObregÃ³n','2022-10-18 23:21:41'),(3815,215,'San AgustÃ­n MetzquititlÃ¡n','2022-10-18 23:21:41'),(3816,215,'San AgustÃ­n Tlaxiaca','2022-10-18 23:21:41'),(3817,215,'San Bartolo Tutotepec','2022-10-18 23:21:41'),(3818,215,'San Felipe OrizatlÃ¡n','2022-10-18 23:21:41'),(3819,215,'San Salvador','2022-10-18 23:21:41'),(3820,215,'Santiago de Anaya','2022-10-18 23:21:41'),(3821,215,'Singuilucan','2022-10-18 23:21:41'),(3822,215,'Tasquillo','2022-10-18 23:21:41'),(3823,215,'Tecozautla','2022-10-18 23:21:41'),(3824,215,'Tenango de Doria','2022-10-18 23:21:41'),(3825,215,'Tepeapulco','2022-10-18 23:21:41'),(3826,215,'TepehuacÃ¡n de Guerrero','2022-10-18 23:21:41'),(3827,215,'Tepeji del Rio de Ocampo','2022-10-18 23:21:41'),(3828,215,'TepetitlÃ¡n','2022-10-18 23:21:41'),(3829,215,'Tetepango','2022-10-18 23:21:41'),(3830,215,'Tezontepec de Aldama','2022-10-18 23:21:41'),(3831,215,'Tianguistengo','2022-10-18 23:21:41'),(3832,215,'Tizayuca','2022-10-18 23:21:41'),(3833,215,'Tlahuelilpan','2022-10-18 23:21:41'),(3834,215,'Tlahuiltepa','2022-10-18 23:21:41'),(3835,215,'Tlanalapa','2022-10-18 23:21:41'),(3836,215,'Tlanchinol','2022-10-18 23:21:41'),(3837,215,'Tlaxcoapan','2022-10-18 23:21:41'),(3838,215,'Tolcayuca','2022-10-18 23:21:41'),(3839,215,'Tula de Allende','2022-10-18 23:21:41'),(3840,215,'Tulancingo de Bravo','2022-10-18 23:21:41'),(3841,215,'Tulantepec de Lugo Guerrero','2022-10-18 23:21:41'),(3842,215,'Villa de Tezontepec','2022-10-18 23:21:41'),(3843,215,'Xochiatipan','2022-10-18 23:21:41'),(3844,215,'XochicoatlÃ¡n','2022-10-18 23:21:41'),(3845,215,'Yahualica','2022-10-18 23:21:41'),(3846,215,'Zacualtipan de Ãngeles','2022-10-18 23:21:41'),(3847,215,'ZapotlÃ¡n de JuÃ¡rez','2022-10-18 23:21:41'),(3848,215,'Zempoala','2022-10-18 23:21:41'),(3849,215,'Zimapan','2022-10-18 23:21:41'),(3850,306,'Acatic','2022-10-18 23:21:41'),(3851,306,'AcatlÃ¡n de JuÃ¡rez','2022-10-18 23:21:41'),(3852,306,'Ahualulco de Mercado','2022-10-18 23:21:41'),(3853,306,'Amacueca','2022-10-18 23:21:41'),(3854,306,'AmatitÃ¡n','2022-10-18 23:21:41'),(3855,306,'Ameca','2022-10-18 23:21:41'),(3856,306,'Arandas','2022-10-18 23:21:41'),(3857,306,'Atemajac de Brizuela','2022-10-18 23:21:41'),(3858,306,'Atengo','2022-10-18 23:21:41'),(3859,306,'Atenguillo','2022-10-18 23:21:41'),(3860,306,'Atotonilco El Alto','2022-10-18 23:21:41'),(3861,306,'Atoyac','2022-10-18 23:21:41'),(3862,306,'AutlÃ¡n de Navarro','2022-10-18 23:21:41'),(3863,306,'AyotlÃ¡n','2022-10-18 23:21:41'),(3864,306,'Ayutla','2022-10-18 23:21:41'),(3865,306,'BolaÃ±os','2022-10-18 23:21:41'),(3866,306,'Cabo Corrientes','2022-10-18 23:21:41'),(3867,306,'CaÃ±adas de ObregÃ³n','2022-10-18 23:21:41'),(3868,306,'Casimiro Castillo','2022-10-18 23:21:41'),(3869,306,'Chapala','2022-10-18 23:21:41'),(3870,306,'ChimaltitÃ¡n','2022-10-18 23:21:41'),(3871,306,'ChiquilistlÃ¡n','2022-10-18 23:21:41'),(3872,306,'CihuatlÃ¡n','2022-10-18 23:21:41'),(3873,306,'Cocula','2022-10-18 23:21:41'),(3874,306,'ColotlÃ¡n','2022-10-18 23:21:41'),(3875,306,'ConcepciÃ³n de Buenos Aires','2022-10-18 23:21:41'),(3876,306,'CuautitlÃ¡n de GarcÃ­a BarragÃ¡n','2022-10-18 23:21:41'),(3877,306,'Cuautla','2022-10-18 23:21:41'),(3878,306,'CuquÃ­o','2022-10-18 23:21:41'),(3879,306,'Degollado','2022-10-18 23:21:41'),(3880,306,'Ejutla','2022-10-18 23:21:41'),(3881,306,'El Arenal','2022-10-18 23:21:41'),(3882,306,'El Grullo','2022-10-18 23:21:41'),(3883,306,'El LimÃ³n','2022-10-18 23:21:41'),(3884,306,'El Salto','2022-10-18 23:21:41'),(3885,306,'EncarnaciÃ³n de DÃ­az','2022-10-18 23:21:41'),(3886,306,'EtzatlÃ¡n','2022-10-18 23:21:41'),(3887,306,'GÃ³mez FarÃ­as','2022-10-18 23:21:41'),(3888,306,'Guachinango','2022-10-18 23:21:41'),(3889,306,'Guadalajara','2022-10-18 23:21:41'),(3890,306,'Hostotipaquillo','2022-10-18 23:21:41'),(3891,306,'HuejÃºcar','2022-10-18 23:21:41'),(3892,306,'Huejuquilla El Alto','2022-10-18 23:21:41'),(3893,306,'IxtlahuacÃ¡n de los Membrillos','2022-10-18 23:21:41'),(3894,306,'Ixtlahuacan del RÃ­o','2022-10-18 23:21:41'),(3895,306,'JalostotitlÃ¡n','2022-10-18 23:21:41'),(3896,306,'Jamay','2022-10-18 23:21:41'),(3897,306,'JesÃºs MarÃ­a','2022-10-18 23:21:41'),(3898,306,'JilotlÃ¡n de los Dolores','2022-10-18 23:21:41'),(3899,306,'Jocotepec','2022-10-18 23:21:41'),(3900,306,'JuanacatlÃ¡n','2022-10-18 23:21:41'),(3901,306,'JuchitlÃ¡n','2022-10-18 23:21:41'),(3902,306,'La Barca','2022-10-18 23:21:41'),(3903,306,'La Huerta','2022-10-18 23:21:41'),(3904,306,'La Manzanilla de La Paz','2022-10-18 23:21:41'),(3905,306,'Lagos de Moreno','2022-10-18 23:21:41'),(3906,306,'Magdalena','2022-10-18 23:21:41'),(3907,306,'Mascota','2022-10-18 23:21:41'),(3908,306,'Mazamitla','2022-10-18 23:21:41'),(3909,306,'Mexticacan','2022-10-18 23:21:41'),(3910,306,'Mezquitic','2022-10-18 23:21:41'),(3911,306,'MixtlÃ¡n','2022-10-18 23:21:41'),(3912,306,'OcotlÃ¡n','2022-10-18 23:21:41'),(3913,306,'Ojuelos de Jalisco','2022-10-18 23:21:41'),(3914,306,'PÃ­huamo','2022-10-18 23:21:41'),(3915,306,'PoncitlÃ¡n','2022-10-18 23:21:41'),(3916,306,'Puerto Vallarta','2022-10-18 23:21:41'),(3917,306,'Quitupan','2022-10-18 23:21:41'),(3918,306,'San Cristobal de la Barranca','2022-10-18 23:21:41'),(3919,306,'San Diego de AlejandrÃ­a','2022-10-18 23:21:41'),(3920,306,'San Gabriel','2022-10-18 23:21:41'),(3921,306,'San Juan de los Lagos','2022-10-18 23:21:41'),(3922,306,'San Juanito de Escobedo','2022-10-18 23:21:41'),(3923,306,'San JuliÃ¡n','2022-10-18 23:21:41'),(3924,306,'San Marcos','2022-10-18 23:21:41'),(3925,306,'San MartÃ­n de BolaÃ±os','2022-10-18 23:21:41'),(3926,306,'San MartÃ­n de Hidalgo','2022-10-18 23:21:41'),(3927,306,'San Miguel El Alto','2022-10-18 23:21:41'),(3928,306,'San SebastiÃ¡n del Oeste','2022-10-18 23:21:41'),(3929,306,'Santa MarÃ­a del Oro','2022-10-18 23:21:41'),(3930,306,'Santa MarÃ­a de los Angeles','2022-10-18 23:21:41'),(3931,306,'Sayula','2022-10-18 23:21:41'),(3932,306,'Tala','2022-10-18 23:21:41'),(3933,306,'Talpa de Allende','2022-10-18 23:21:41'),(3934,306,'Tamazula de Gordiano','2022-10-18 23:21:41'),(3935,306,'Tapalpa','2022-10-18 23:21:41'),(3936,306,'TecalitlÃ¡n','2022-10-18 23:21:41'),(3937,306,'Techaluta de Montenegro','2022-10-18 23:21:41'),(3938,306,'TecolotlÃ¡n','2022-10-18 23:21:41'),(3939,306,'TenamaxtlÃ¡n','2022-10-18 23:21:41'),(3940,306,'Teocaltiche','2022-10-18 23:21:41'),(3941,306,'TeocuitatlÃ¡n de Corona','2022-10-18 23:21:41'),(3942,306,'TepatitlÃ¡n de Morelos','2022-10-18 23:21:41'),(3943,306,'Tequila','2022-10-18 23:21:41'),(3944,306,'TeuchitlÃ¡n','2022-10-18 23:21:41'),(3945,306,'Tizapan El Alto','2022-10-18 23:21:41'),(3946,306,'Tlajomulco de ZuÃ±iga','2022-10-18 23:21:41'),(3947,306,'Tlaquepaque','2022-10-18 23:21:41'),(3948,306,'TolimÃ¡n','2022-10-18 23:21:41'),(3949,306,'TomatlÃ¡n','2022-10-18 23:21:41'),(3950,306,'TonalÃ¡','2022-10-18 23:21:41'),(3951,306,'Tonaya','2022-10-18 23:21:41'),(3952,306,'Tonila','2022-10-18 23:21:41'),(3953,306,'Totatiche','2022-10-18 23:21:41'),(3954,306,'TototlÃ¡n','2022-10-18 23:21:41'),(3955,306,'Tuxcacuesco','2022-10-18 23:21:41'),(3956,306,'Tuxcueca','2022-10-18 23:21:41'),(3957,306,'Tuxpan','2022-10-18 23:21:41'),(3958,306,'UniÃ³n de San Antonio','2022-10-18 23:21:41'),(3959,306,'UniÃ³n de Tula','2022-10-18 23:21:41'),(3960,306,'Valle de Guadalupe','2022-10-18 23:21:41'),(3961,306,'Valle de JuÃ¡rez','2022-10-18 23:21:41'),(3962,306,'Villa Corona','2022-10-18 23:21:41'),(3963,306,'Villa Guerrero','2022-10-18 23:21:41'),(3964,306,'Villa Hidalgo','2022-10-18 23:21:41'),(3965,306,'Villa PurificaciÃ³n','2022-10-18 23:21:41'),(3966,306,'Yahualica de GonzÃ¡lez Gallo','2022-10-18 23:21:41'),(3967,306,'Zacoalco de Torres','2022-10-18 23:21:41'),(3968,306,'Zapopan','2022-10-18 23:21:41'),(3969,306,'Zapotiltic','2022-10-18 23:21:41'),(3970,306,'ZapotitlÃ¡n de Vadillo','2022-10-18 23:21:41'),(3971,306,'ZapotlÃ¡n del Rey','2022-10-18 23:21:41'),(3972,306,'ZapotlÃ¡n el Grande','2022-10-18 23:21:41'),(3973,306,'Zapotlanejo','2022-10-18 23:21:41'),(3974,306,'San Ignacio Cerro Gordo','2022-10-18 23:21:41'),(3975,335,'Amacuzac','2022-10-18 23:21:41'),(3976,335,'Atlatlahucan','2022-10-18 23:21:41'),(3977,335,'Axochiapan','2022-10-18 23:21:41'),(3978,335,'Ciudad Ayala','2022-10-18 23:21:41'),(3979,335,'CoatlÃ¡n del RÃ­o','2022-10-18 23:21:41'),(3980,335,'Cuautla','2022-10-18 23:21:41'),(3981,335,'Cuernavaca','2022-10-18 23:21:41'),(3982,335,'Emiliano Zapata','2022-10-18 23:21:41'),(3983,335,'Huitzilac','2022-10-18 23:21:41'),(3984,335,'Jantetelco','2022-10-18 23:21:41'),(3985,335,'Jiutepec','2022-10-18 23:21:41'),(3986,335,'Jojutla','2022-10-18 23:21:41'),(3987,335,'Jonacatepec','2022-10-18 23:21:41'),(3988,335,'Mazatepec','2022-10-18 23:21:41'),(3989,335,'Miacatlan','2022-10-18 23:21:41'),(3990,335,'Ocuituco','2022-10-18 23:21:41'),(3991,335,'Puente de Ixtla','2022-10-18 23:21:41'),(3992,335,'Temixco','2022-10-18 23:21:41'),(3993,335,'Temoac','2022-10-18 23:21:41'),(3994,335,'Tepalcingo','2022-10-18 23:21:41'),(3995,335,'TepoztlÃ¡n','2022-10-18 23:21:41'),(3996,335,'Tetecala','2022-10-18 23:21:41'),(3997,335,'Tetela del VolcÃ¡n','2022-10-18 23:21:41'),(3998,335,'Tlalnepantla','2022-10-18 23:21:41'),(3999,335,'TlaltizapÃ¡n','2022-10-18 23:21:41'),(4000,335,'Tlaquiltenango','2022-10-18 23:21:41'),(4001,335,'Tlayacapan','2022-10-18 23:21:41'),(4002,335,'Totolapan','2022-10-18 23:21:41'),(4003,335,'Xochitepec','2022-10-18 23:21:41'),(4004,335,'Yautepec','2022-10-18 23:21:41'),(4005,335,'Yecapixtla','2022-10-18 23:21:41'),(4006,335,'Zacatepec de Hidalgo','2022-10-18 23:21:41'),(4007,335,'Zacualpan de Amilpas','2022-10-18 23:21:41'),(4008,337,'Acaponeta','2022-10-18 23:21:41'),(4009,337,'AhuacatlÃ¡n','2022-10-18 23:21:41'),(4010,337,'AmatlÃ¡n de CaÃ±as','2022-10-18 23:21:41'),(4011,337,'BahÃ­a de Banderas','2022-10-18 23:21:41'),(4012,337,'Compostela','2022-10-18 23:21:41'),(4013,337,'El Nayar','2022-10-18 23:21:41'),(4014,337,'Huajicori','2022-10-18 23:21:41'),(4015,337,'IxtlÃ¡n del RÃ­o','2022-10-18 23:21:41'),(4016,337,'Jala','2022-10-18 23:21:41'),(4017,337,'La Yesca','2022-10-18 23:21:41'),(4018,337,'Rosamorada','2022-10-18 23:21:41'),(4019,337,'Ruiz','2022-10-18 23:21:41'),(4020,337,'San Blas','2022-10-18 23:21:41'),(4021,337,'San Pedro Lagunillas','2022-10-18 23:21:41'),(4022,337,'Santa MarÃ­a del Oro','2022-10-18 23:21:41'),(4023,337,'Santiago Ixcuintla','2022-10-18 23:21:41'),(4024,337,'Tecuala','2022-10-18 23:21:41'),(4025,337,'Tepic','2022-10-18 23:21:41'),(4026,337,'Tuxpan','2022-10-18 23:21:41'),(4027,337,'Xalisco','2022-10-18 23:21:41'),(4028,340,'Abejones','2022-10-18 23:21:41'),(4029,340,'AcatlÃ¡n de PÃ©rez Figueroa','2022-10-18 23:21:41'),(4030,340,'Animas Trujano, Oaxaca','2022-10-18 23:21:41'),(4031,340,'AsunciÃ³n Cacalotepec','2022-10-18 23:21:41'),(4032,340,'AsunciÃ³n Cuyotepeji','2022-10-18 23:21:41'),(4033,340,'AsunciÃ³n Ixtaltepec','2022-10-18 23:21:41'),(4034,340,'AsunciÃ³n NochixtlÃ¡n','2022-10-18 23:21:41'),(4035,340,'AsunciÃ³n OcotlÃ¡n','2022-10-18 23:21:41'),(4036,340,'AsunciÃ³n Tlacolulita','2022-10-18 23:21:41'),(4037,340,'Ayoquezco de Aldama','2022-10-18 23:21:41'),(4038,340,'Ayotzintepec','2022-10-18 23:21:41'),(4039,340,'CalihualÃ¡','2022-10-18 23:21:41'),(4040,340,'Candelaria Loxicha','2022-10-18 23:21:41'),(4041,340,'Capulalpam de MÃ©ndez','2022-10-18 23:21:41'),(4042,340,'Chahuites','2022-10-18 23:21:41'),(4043,340,'Chalcatongo de Hidalgo','2022-10-18 23:21:41'),(4044,340,'Chilapa de Diaz','2022-10-18 23:21:41'),(4045,340,'ChiquihuitlÃ¡n de Benito JuÃ¡rez','2022-10-18 23:21:41'),(4046,340,'CiÃ©nega de ZimatlÃ¡n','2022-10-18 23:21:41'),(4047,340,'Ciudad Ixtepec','2022-10-18 23:21:41'),(4048,340,'Coatecas Altas','2022-10-18 23:21:41'),(4049,340,'CoicoyÃ¡n de las Flores','2022-10-18 23:21:41'),(4050,340,'ConcepciÃ³n Buenavista','2022-10-18 23:21:41'),(4051,340,'ConcepciÃ³n PÃ¡palo','2022-10-18 23:21:41'),(4052,340,'Constancia del Rosario','2022-10-18 23:21:41'),(4053,340,'Cosolapa','2022-10-18 23:21:41'),(4054,340,'Cosoltepec','2022-10-18 23:21:41'),(4055,340,'Cuilapan de Guerrero','2022-10-18 23:21:41'),(4056,340,'Ejutla de Crespo','2022-10-18 23:21:41'),(4057,340,'EloxochitlÃ¡n de Flores MagÃ³n','2022-10-18 23:21:41'),(4058,340,'El Barrio de La Soledad','2022-10-18 23:21:41'),(4059,340,'El Espinal','2022-10-18 23:21:41'),(4060,340,'Evangelista Analco','2022-10-18 23:21:41'),(4061,340,'Fresnillo de Trujano','2022-10-18 23:21:41'),(4062,340,'Guadalupe de RamÃ­rez','2022-10-18 23:21:41'),(4063,340,'Guadalupe Etla','2022-10-18 23:21:41'),(4064,340,'Guelatao de JuÃ¡rez','2022-10-18 23:21:41'),(4065,340,'Guevea de Humboldt','2022-10-18 23:21:41'),(4066,340,'Huajuapan de LeÃ³n','2022-10-18 23:21:41'),(4067,340,'Huautepec','2022-10-18 23:21:41'),(4068,340,'Huautla de JimÃ©nez','2022-10-18 23:21:41'),(4069,340,'Ixpantepec Nieves','2022-10-18 23:21:41'),(4070,340,'IxtlÃ¡n de JuÃ¡rez','2022-10-18 23:21:41'),(4071,340,'JuchitÃ¡n de Zaragoza','2022-10-18 23:21:41'),(4072,340,'La CompaÃ±ia','2022-10-18 23:21:41'),(4073,340,'La Pe','2022-10-18 23:21:41'),(4074,340,'La Reforma','2022-10-18 23:21:41'),(4075,340,'La Trinidad Vista Hermosa','2022-10-18 23:21:41'),(4076,340,'Loma Bonita','2022-10-18 23:21:41'),(4077,340,'Magdalena Apasco','2022-10-18 23:21:41'),(4078,340,'Magdalena Jaltepec','2022-10-18 23:21:41'),(4079,340,'Magdalena Mixtepec','2022-10-18 23:21:41'),(4080,340,'Magdalena OcotlÃ¡n','2022-10-18 23:21:41'),(4081,340,'Magdalena PeÃ±asco','2022-10-18 23:21:41'),(4082,340,'Magdalena Teitipac','2022-10-18 23:21:41'),(4083,340,'Magdalena TequisistlÃ¡n','2022-10-18 23:21:41'),(4084,340,'Magdalena Tlacotepec','2022-10-18 23:21:41'),(4085,340,'Magdalena ZahuatlÃ¡n','2022-10-18 23:21:41'),(4086,340,'Mariscala de JuÃ¡rez','2022-10-18 23:21:41'),(4087,340,'MÃ¡rtires de Tacubaya','2022-10-18 23:21:41'),(4088,340,'MatÃ­as Romero','2022-10-18 23:21:41'),(4089,340,'MazatlÃ¡n Villa de Flores','2022-10-18 23:21:41'),(4090,340,'Mesones Hidalgo','2022-10-18 23:21:41'),(4091,340,'MiahuatlÃ¡n de Porfirio DÃ­az','2022-10-18 23:21:41'),(4092,340,'MixistlÃ¡n de la Reforma','2022-10-18 23:21:41'),(4093,340,'Monjas','2022-10-18 23:21:41'),(4094,340,'Natividad','2022-10-18 23:21:41'),(4095,340,'Nazareno Etla','2022-10-18 23:21:41'),(4096,340,'Nejapa de Madero','2022-10-18 23:21:41'),(4097,340,'Nuevo Zoquiapam','2022-10-18 23:21:41'),(4098,340,'Oaxaca de JuÃ¡rez','2022-10-18 23:21:41'),(4099,340,'OcotlÃ¡n de Morelos','2022-10-18 23:21:41'),(4100,340,'Pinotepa de Don Luis','2022-10-18 23:21:41'),(4101,340,'Pinotepa Nacional','2022-10-18 23:21:41'),(4102,340,'Pluma Hidalgo','2022-10-18 23:21:41'),(4103,340,'Putla Villa de Guerrero','2022-10-18 23:21:41'),(4104,340,'Reforma de Pineda','2022-10-18 23:21:41'),(4105,340,'Reyes Etla','2022-10-18 23:21:41'),(4106,340,'Rojas de CuauhtÃ©moc','2022-10-18 23:21:41'),(4107,340,'Salina Cruz','2022-10-18 23:21:41'),(4108,340,'San AgustÃ­n Amatengo','2022-10-18 23:21:41'),(4109,340,'San AgustÃ­n Atenango','2022-10-18 23:21:41'),(4110,340,'San AgustÃ­n Chayuco','2022-10-18 23:21:41'),(4111,340,'San AgustÃ­n de las Juntas','2022-10-18 23:21:41'),(4112,340,'San AgustÃ­n Etla','2022-10-18 23:21:41'),(4113,340,'San AgustÃ­n Loxicha','2022-10-18 23:21:41'),(4114,340,'San AgustÃ­n Tlacotepec','2022-10-18 23:21:41'),(4115,340,'San AgustÃ­n Yatareni','2022-10-18 23:21:41'),(4116,340,'San AndrÃ©s Cabecera Nueva','2022-10-18 23:21:41'),(4117,340,'San AndrÃ©s Dinicuiti','2022-10-18 23:21:41'),(4118,340,'San AndrÃ©s Huaxpaltepec','2022-10-18 23:21:41'),(4119,340,'San AndrÃ©s Huayapam','2022-10-18 23:21:41'),(4120,340,'San AndrÃ©s Ixtlahuaca','2022-10-18 23:21:41'),(4121,340,'San AndrÃ©s Lagunas','2022-10-18 23:21:41'),(4122,340,'San AndrÃ©s NuxiÃ±o','2022-10-18 23:21:41'),(4123,340,'San AndrÃ©s PaxtlÃ¡n','2022-10-18 23:21:41'),(4124,340,'San AndrÃ©s Sinaxtla','2022-10-18 23:21:41'),(4125,340,'San AndrÃ©s Solaga','2022-10-18 23:21:41'),(4126,340,'San AndrÃ©s Teotilalpam','2022-10-18 23:21:41'),(4127,340,'San AndrÃ©s Tepetlapa','2022-10-18 23:21:41'),(4128,340,'San AndrÃ©s YaÃ¡','2022-10-18 23:21:41'),(4129,340,'San AndrÃ©s Zabache','2022-10-18 23:21:41'),(4130,340,'San AndrÃ©s Zautla','2022-10-18 23:21:41'),(4131,340,'San Antonino Castillo Velasco','2022-10-18 23:21:41'),(4132,340,'San Antonino El Alto','2022-10-18 23:21:41'),(4133,340,'San Antonino Monte Verde','2022-10-18 23:21:41'),(4134,340,'San Antonio Acutla','2022-10-18 23:21:41'),(4135,340,'San Antonio de la Cal','2022-10-18 23:21:41'),(4136,340,'San Antonio Huitepec','2022-10-18 23:21:41'),(4137,340,'San Antonio Nanahuatipam','2022-10-18 23:21:41'),(4138,340,'San Antonio Sinicahua','2022-10-18 23:21:41'),(4139,340,'San Antonio Tepetlapa','2022-10-18 23:21:41'),(4140,340,'San Baltazar Chichicapam','2022-10-18 23:21:41'),(4141,340,'San Baltazar Loxicha','2022-10-18 23:21:41'),(4142,340,'San Baltazar Yatzachi el Bajo','2022-10-18 23:21:41'),(4143,340,'San Bartolo Coyotepec','2022-10-18 23:21:41'),(4144,340,'San BartolomÃ© Ayautla','2022-10-18 23:21:41'),(4145,340,'San BartolomÃ© Loxicha','2022-10-18 23:21:41'),(4146,340,'San BartolomÃ© Quialana','2022-10-18 23:21:41'),(4147,340,'San BartolomÃ© YucuaÃ±e','2022-10-18 23:21:41'),(4148,340,'San BartolomÃ© Zoogocho','2022-10-18 23:21:41'),(4149,340,'San Bartolo Soyaltepec','2022-10-18 23:21:41'),(4150,340,'San Bartolo Yautepec','2022-10-18 23:21:41'),(4151,340,'San Bernardo Mixtepec','2022-10-18 23:21:41'),(4152,340,'San Blas Atempa','2022-10-18 23:21:41'),(4153,340,'San Carlos Yautepec','2022-10-18 23:21:41'),(4154,340,'San CristÃ³bal AmatlÃ¡n','2022-10-18 23:21:41'),(4155,340,'San CristÃ³bal Amoltepec','2022-10-18 23:21:41'),(4156,340,'San CristÃ³bal Lachirioag','2022-10-18 23:21:41'),(4157,340,'San CristÃ³bal Suchixtlahuaca','2022-10-18 23:21:41'),(4158,340,'San Dionisio del Mar','2022-10-18 23:21:41'),(4159,340,'San Dionisio Ocotepec','2022-10-18 23:21:41'),(4160,340,'San Dionisio OcotlÃ¡n','2022-10-18 23:21:41'),(4161,340,'San Esteban Atatlahuca','2022-10-18 23:21:41'),(4162,340,'San Felipe Jalapa de DÃ­az','2022-10-18 23:21:41'),(4163,340,'San Felipe Tejalapam','2022-10-18 23:21:41'),(4164,340,'San Felipe Usila','2022-10-18 23:21:41'),(4165,340,'San Francisco CahuacÃºa','2022-10-18 23:21:41'),(4166,340,'San Francisco Cajonos','2022-10-18 23:21:41'),(4167,340,'San Francisco Chapulapa','2022-10-18 23:21:41'),(4168,340,'San Francisco ChindÃºa','2022-10-18 23:21:41'),(4169,340,'San Francisco del Mar','2022-10-18 23:21:41'),(4170,340,'San Francisco HuehuetlÃ¡n','2022-10-18 23:21:41'),(4171,340,'San Francisco IxhuatÃ¡n','2022-10-18 23:21:41'),(4172,340,'San Francisco Jaltepetongo','2022-10-18 23:21:41'),(4173,340,'San Francisco LachigolÃ³','2022-10-18 23:21:41'),(4174,340,'San Francisco Logueche','2022-10-18 23:21:41'),(4175,340,'San Francisco NuxaÃ±o','2022-10-18 23:21:41'),(4176,340,'San Francisco Ozolotepec','2022-10-18 23:21:41'),(4177,340,'San Francisco SolÃ¡','2022-10-18 23:21:41'),(4178,340,'San Francisco Telixtlahuaca','2022-10-18 23:21:41'),(4179,340,'San Francisco Teopan','2022-10-18 23:21:41'),(4180,340,'San Francisco Tlapancingo','2022-10-18 23:21:41'),(4181,340,'San Gabriel Mixtepec','2022-10-18 23:21:41'),(4182,340,'San Ildefonso AmatlÃ¡n','2022-10-18 23:21:41'),(4183,340,'San Ildefonso SolÃ¡','2022-10-18 23:21:41'),(4184,340,'San Ildefonso Villa Alta','2022-10-18 23:21:41'),(4185,340,'San Jacinto Amilpas','2022-10-18 23:21:41'),(4186,340,'San Jacinto Tlacotepec','2022-10-18 23:21:41'),(4187,340,'San JerÃ³nimo CoatlÃ¡n','2022-10-18 23:21:41'),(4188,340,'San JerÃ³nimo Silacayoapilla','2022-10-18 23:21:41'),(4189,340,'San JerÃ³nimo Sosola','2022-10-18 23:21:41'),(4190,340,'San JerÃ³nimo Taviche','2022-10-18 23:21:41'),(4191,340,'San JerÃ³nimo Tecoatl','2022-10-18 23:21:41'),(4192,340,'San JerÃ³nimo Tlacochahuaya','2022-10-18 23:21:41'),(4193,340,'San Jorge Nuchita','2022-10-18 23:21:41'),(4194,340,'San JosÃ© Ayuquila','2022-10-18 23:21:41'),(4195,340,'San JosÃ© Chinantequilla (Oaxaca)','2022-10-18 23:21:41'),(4196,340,'San JosÃ© Chiltepec','2022-10-18 23:21:41'),(4197,340,'San JosÃ© del PeÃ±asco','2022-10-18 23:21:41'),(4198,340,'San JosÃ© del Progreso','2022-10-18 23:21:41'),(4199,340,'San JosÃ© Estancia Grande','2022-10-18 23:21:41'),(4200,340,'San JosÃ© Independencia','2022-10-18 23:21:41'),(4201,340,'San JosÃ© Lachiguiri','2022-10-18 23:21:41'),(4202,340,'San JosÃ© Tenango','2022-10-18 23:21:41'),(4203,340,'San Juan Achiutla','2022-10-18 23:21:41'),(4204,340,'San Juan Atepec','2022-10-18 23:21:41'),(4205,340,'San Juan Bautista Atatlahuca','2022-10-18 23:21:41'),(4206,340,'San Juan Bautista Coixtlahuaca','2022-10-18 23:21:41'),(4207,340,'San Juan Bautista Cuicatlan','2022-10-18 23:21:41'),(4208,340,'San Juan Bautista Guelache','2022-10-18 23:21:41'),(4209,340,'San Juan Bautista JayacatlÃ¡n','2022-10-18 23:21:41'),(4210,340,'San Juan Bautista lo de Soto','2022-10-18 23:21:41'),(4211,340,'San Juan Bautista Suchitepec','2022-10-18 23:21:41'),(4212,340,'San Juan Bautista Tlachichilco','2022-10-18 23:21:41'),(4213,340,'San Juan Bautista Tlacoatzintepec','2022-10-18 23:21:41'),(4214,340,'San Juan Bautista Tuxtepec','2022-10-18 23:21:41'),(4215,340,'San Juan Bautista Valle Nacional','2022-10-18 23:21:41'),(4216,340,'San Juan Cacahuatepec','2022-10-18 23:21:41'),(4217,340,'San Juan ChicomezÃºchil','2022-10-18 23:21:41'),(4218,340,'San Juan Chilateca','2022-10-18 23:21:41'),(4219,340,'San Juan Cieneguilla','2022-10-18 23:21:41'),(4220,340,'San Juan Coatzospam','2022-10-18 23:21:41'),(4221,340,'San Juan Colorado','2022-10-18 23:21:41'),(4222,340,'San Juan Comaltepec','2022-10-18 23:21:41'),(4223,340,'San Juan CotzocÃ³n','2022-10-18 23:21:41'),(4224,340,'San Juan del Estado','2022-10-18 23:21:41'),(4225,340,'San Juan de los Cues','2022-10-18 23:21:41'),(4226,340,'San Juan del RÃ­o','2022-10-18 23:21:41'),(4227,340,'San Juan Diuxi','2022-10-18 23:21:41'),(4228,340,'San Juan GuelavÃ­a','2022-10-18 23:21:41'),(4229,340,'San Juan Guichicovi','2022-10-18 23:21:41'),(4230,340,'San Juan Ihualtepec','2022-10-18 23:21:41'),(4231,340,'San Juan Juquila Mixes','2022-10-18 23:21:41'),(4232,340,'San Juan Juquila Vijanos','2022-10-18 23:21:41'),(4233,340,'San Juan Lachao','2022-10-18 23:21:41'),(4234,340,'San Juan Lachigalla','2022-10-18 23:21:41'),(4235,340,'San Juan Lajarcia','2022-10-18 23:21:41'),(4236,340,'San Juan Lalana','2022-10-18 23:21:41'),(4237,340,'San Juan MazatlÃ¡n','2022-10-18 23:21:41'),(4238,340,'San Juan Mixtepec, Mixteca','2022-10-18 23:21:41'),(4239,340,'San Juan Mixtepec, MiahuatlÃ¡n','2022-10-18 23:21:41'),(4240,340,'San Juan Ã‘umÃ­','2022-10-18 23:21:41'),(4241,340,'San Juan Ozolotepec','2022-10-18 23:21:41'),(4242,340,'San Juan Petlapa','2022-10-18 23:21:41'),(4243,340,'San Juan Quiahije','2022-10-18 23:21:41'),(4244,340,'San Juan Quiotepec','2022-10-18 23:21:41'),(4245,340,'San Juan Sayultepec','2022-10-18 23:21:41'),(4246,340,'San Juan TabaÃ¡','2022-10-18 23:21:41'),(4247,340,'San Juan Tamazola','2022-10-18 23:21:41'),(4248,340,'San Juan Teita','2022-10-18 23:21:41'),(4249,340,'San Juan Teitipac','2022-10-18 23:21:41'),(4250,340,'San Juan Tepeuxila','2022-10-18 23:21:41'),(4251,340,'San Juan Teposcolula','2022-10-18 23:21:41'),(4252,340,'San Juan YaeÃ©','2022-10-18 23:21:41'),(4253,340,'San Juan Yatzona','2022-10-18 23:21:41'),(4254,340,'San Juan Yucuita','2022-10-18 23:21:41'),(4255,340,'San Lorenzo','2022-10-18 23:21:41'),(4256,340,'San Lorenzo Albarradas','2022-10-18 23:21:41'),(4257,340,'San Lorenzo Cacaotepec','2022-10-18 23:21:41'),(4258,340,'San Lorenzo Cuaunecuiltitla','2022-10-18 23:21:41'),(4259,340,'San Lorenzo Texmelucan','2022-10-18 23:21:41'),(4260,340,'San Lorenzo Victoria','2022-10-18 23:21:41'),(4261,340,'San Lucas CamotlÃ¡n','2022-10-18 23:21:41'),(4262,340,'San Lucas OjitlÃ¡n','2022-10-18 23:21:41'),(4263,340,'San Lucas QuiavinÃ­','2022-10-18 23:21:41'),(4264,340,'San Lucas Zoquiapam','2022-10-18 23:21:41'),(4265,340,'San Luis AmatlÃ¡n','2022-10-18 23:21:41'),(4266,340,'San Marcial Ozolotepec','2022-10-18 23:21:41'),(4267,340,'San Marcos Arteaga','2022-10-18 23:21:41'),(4268,340,'San MartÃ­n de los Cansecos','2022-10-18 23:21:41'),(4269,340,'San MartÃ­n Huamelulpam','2022-10-18 23:21:41'),(4270,340,'San MartÃ­n Itunyoso','2022-10-18 23:21:41'),(4271,340,'San MartÃ­n LachilÃ¡','2022-10-18 23:21:41'),(4272,340,'San MartÃ­n Peras','2022-10-18 23:21:41'),(4273,340,'San MartÃ­n Tilcajete','2022-10-18 23:21:41'),(4274,340,'San MartÃ­n Toxpalan','2022-10-18 23:21:41'),(4275,340,'San MartÃ­n Zacatepec','2022-10-18 23:21:41'),(4276,340,'San Mateo Cajonos','2022-10-18 23:21:41'),(4277,340,'San Mateo del Mar','2022-10-18 23:21:41'),(4278,340,'San Mateo Etlatongo','2022-10-18 23:21:41'),(4279,340,'San Mateo Nejapam','2022-10-18 23:21:41'),(4280,340,'San Mateo PeÃ±asco','2022-10-18 23:21:41'),(4281,340,'San Mateo PiÃ±as','2022-10-18 23:21:41'),(4282,340,'San Mateo RÃ­o Hondo','2022-10-18 23:21:41'),(4283,340,'San Mateo Sindihui','2022-10-18 23:21:41'),(4284,340,'San Mateo Tlapiltepec','2022-10-18 23:21:41'),(4285,340,'San Mateo YoloxochitlÃ¡n','2022-10-18 23:21:41'),(4286,340,'San Melchor Betaza','2022-10-18 23:21:41'),(4287,340,'San Miguel Achiutla','2022-10-18 23:21:41'),(4288,340,'San Miguel AhuehuetitlÃ¡n','2022-10-18 23:21:41'),(4289,340,'San Miguel AloÃ¡pam','2022-10-18 23:21:41'),(4290,340,'San Miguel AmatitlÃ¡n','2022-10-18 23:21:41'),(4291,340,'San Miguel AmatlÃ¡n','2022-10-18 23:21:41'),(4292,340,'San Miguel Chicahua','2022-10-18 23:21:41'),(4293,340,'San Miguel Chimalapa','2022-10-18 23:21:41'),(4294,340,'San Miguel CoatlÃ¡n','2022-10-18 23:21:41'),(4295,340,'San Miguel del Puerto','2022-10-18 23:21:41'),(4296,340,'San Miguel del RÃ­o','2022-10-18 23:21:41'),(4297,340,'San Miguel Ejutla','2022-10-18 23:21:41'),(4298,340,'San Miguel El Grande','2022-10-18 23:21:41'),(4299,340,'San Miguel Huautla','2022-10-18 23:21:41'),(4300,340,'San Miguel Mixtepec','2022-10-18 23:21:41'),(4301,340,'San Miguel Panixtlahuaca','2022-10-18 23:21:41'),(4302,340,'San Miguel Peras','2022-10-18 23:21:41'),(4303,340,'San Miguel Piedras','2022-10-18 23:21:41'),(4304,340,'San Miguel Quetzaltepec','2022-10-18 23:21:41'),(4305,340,'San Miguel Santa Flor','2022-10-18 23:21:41'),(4306,340,'San Miguel Soyaltepec','2022-10-18 23:21:41'),(4307,340,'San Miguel Suchixtepec','2022-10-18 23:21:41'),(4308,340,'San Miguel TecomatlÃ¡n','2022-10-18 23:21:41'),(4309,340,'San Miguel Tenango','2022-10-18 23:21:41'),(4310,340,'San Miguel Tequixtepec','2022-10-18 23:21:41'),(4311,340,'San Miguel Tilquiapam','2022-10-18 23:21:41'),(4312,340,'San Miguel Tlacamama','2022-10-18 23:21:41'),(4313,340,'San Miguel Tlacotepec','2022-10-18 23:21:41'),(4314,340,'San Miguel Tulancingo','2022-10-18 23:21:41'),(4315,340,'San Miguel Yotao','2022-10-18 23:21:41'),(4316,340,'San NicolÃ¡s','2022-10-18 23:21:41'),(4317,340,'San NicolÃ¡s Hidalgo','2022-10-18 23:21:41'),(4318,340,'San Pablo CoatlÃ¡n','2022-10-18 23:21:41'),(4319,340,'San Pablo Cuatro Venados','2022-10-18 23:21:41'),(4320,340,'San Pablo Etla','2022-10-18 23:21:41'),(4321,340,'San Pablo Huitzo','2022-10-18 23:21:41'),(4322,340,'San Pablo Huixtepec','2022-10-18 23:21:41'),(4323,340,'San Pablo Macuiltianguis','2022-10-18 23:21:41'),(4324,340,'San Pablo Tijaltepec','2022-10-18 23:21:41'),(4325,340,'San Pablo Villa de Mitla','2022-10-18 23:21:41'),(4326,340,'San Pablo Yaganiza','2022-10-18 23:21:41'),(4327,340,'San Pedro Amuzgos','2022-10-18 23:21:41'),(4328,340,'San Pedro ApÃ³stol','2022-10-18 23:21:41'),(4329,340,'San Pedro Atoyac','2022-10-18 23:21:41'),(4330,340,'San Pedro Cajonos','2022-10-18 23:21:41'),(4331,340,'San Pedro Comitancillo','2022-10-18 23:21:41'),(4332,340,'San Pedro Coxcaltepec CÃ¡ntaros','2022-10-18 23:21:41'),(4333,340,'San Pedro El Alto','2022-10-18 23:21:41'),(4334,340,'San Pedro Huamelula','2022-10-18 23:21:41'),(4335,340,'San Pedro Huilotepec','2022-10-18 23:21:41'),(4336,340,'San Pedro IxcatlÃ¡n','2022-10-18 23:21:41'),(4337,340,'San Pedro Ixtlahuaca','2022-10-18 23:21:41'),(4338,340,'San Pedro Jaltepetongo','2022-10-18 23:21:41'),(4339,340,'San Pedro Jicayan','2022-10-18 23:21:41'),(4340,340,'San Pedro Jocotipac','2022-10-18 23:21:41'),(4341,340,'San Pedro Juchatengo','2022-10-18 23:21:41'),(4342,340,'San Pedro MÃ¡rtir','2022-10-18 23:21:41'),(4343,340,'San Pedro MÃ¡rtir Quiechapa','2022-10-18 23:21:41'),(4344,340,'San Pedro MÃ¡rtir Yucuxaco','2022-10-18 23:21:41'),(4345,340,'San Pedro Mixtepec, Juquila','2022-10-18 23:21:41'),(4346,340,'San Pedro Mixtepec, MiahuatlÃ¡n','2022-10-18 23:21:41'),(4347,340,'San Pedro Molinos','2022-10-18 23:21:41'),(4348,340,'San Pedro Nopala','2022-10-18 23:21:41'),(4349,340,'San Pedro Ocopetatillo','2022-10-18 23:21:41'),(4350,340,'San Pedro Ocotepec','2022-10-18 23:21:41'),(4351,340,'San Pedro Pochutla','2022-10-18 23:21:41'),(4352,340,'San Pedro Quiatoni','2022-10-18 23:21:41'),(4353,340,'San Pedro Sochiapam','2022-10-18 23:21:41'),(4354,340,'San Pedro Tapanatepec','2022-10-18 23:21:41'),(4355,340,'San Pedro Taviche','2022-10-18 23:21:41'),(4356,340,'San Pedro Teozacoalco','2022-10-18 23:21:41'),(4357,340,'San Pedro Teutila','2022-10-18 23:21:41'),(4358,340,'San Pedro Tidaa','2022-10-18 23:21:41'),(4359,340,'San Pedro Topiltepec','2022-10-18 23:21:41'),(4360,340,'San Pedro Totolapa','2022-10-18 23:21:41'),(4361,340,'San Pedro Yaneri','2022-10-18 23:21:41'),(4362,340,'San Pedro YÃ³lox','2022-10-18 23:21:41'),(4363,340,'San Pedro y San Pablo Ayutla','2022-10-18 23:21:41'),(4364,340,'San Pedro y San Pablo Teposcolula','2022-10-18 23:21:41'),(4365,340,'San Pedro y San Pablo Tequixtepec','2022-10-18 23:21:41'),(4366,340,'San Pedro Yucunama','2022-10-18 23:21:41'),(4367,340,'San Raymundo Jalpan','2022-10-18 23:21:41'),(4368,340,'San SebastiÃ¡n Abasolo','2022-10-18 23:21:41'),(4369,340,'San SebastiÃ¡n CoatlÃ¡n','2022-10-18 23:21:41'),(4370,340,'San SebastiÃ¡n Ixcapa','2022-10-18 23:21:41'),(4371,340,'San SebastiÃ¡n Nicananduta','2022-10-18 23:21:41'),(4372,340,'San SebastiÃ¡n RÃ­o Hondo','2022-10-18 23:21:41'),(4373,340,'San SebastiÃ¡n Tecomaxtlahuaca','2022-10-18 23:21:41'),(4374,340,'San SebastiÃ¡n Teitipac','2022-10-18 23:21:41'),(4375,340,'San SebastiÃ¡n Tutla','2022-10-18 23:21:41'),(4376,340,'San SimÃ³n Almolongas','2022-10-18 23:21:41'),(4377,340,'San SimÃ³n Zahuatlan','2022-10-18 23:21:41'),(4378,340,'Santa Ana','2022-10-18 23:21:41'),(4379,340,'Santa Ana Ateixtlahuaca','2022-10-18 23:21:41'),(4380,340,'Santa Ana CuauhtÃ©moc','2022-10-18 23:21:41'),(4381,340,'Santa Ana del Valle','2022-10-18 23:21:41'),(4382,340,'Santa Ana Tavela','2022-10-18 23:21:41'),(4383,340,'Santa Ana Tlapacoyan','2022-10-18 23:21:41'),(4384,340,'Santa Ana Yareni','2022-10-18 23:21:41'),(4385,340,'Santa Ana Zegache','2022-10-18 23:21:41'),(4386,340,'Santa Catalina Quieri','2022-10-18 23:21:41'),(4387,340,'Santa Catarina Cuixtla','2022-10-18 23:21:41'),(4388,340,'Santa Catarina Ixtepeji','2022-10-18 23:21:41'),(4389,340,'Santa Catarina Juquila','2022-10-18 23:21:41'),(4390,340,'Santa Catarina Lachatao','2022-10-18 23:21:41'),(4391,340,'Santa Catarina Loxicha','2022-10-18 23:21:41'),(4392,340,'Santa Catarina MechoacÃ¡n','2022-10-18 23:21:41'),(4393,340,'Santa Catarina Minas','2022-10-18 23:21:41'),(4394,340,'Santa Catarina QuianÃ©','2022-10-18 23:21:41'),(4395,340,'Santa Catarina Quioquitani','2022-10-18 23:21:41'),(4396,340,'Santa Catarina Tayata','2022-10-18 23:21:41'),(4397,340,'Santa Catarina Ticua','2022-10-18 23:21:41'),(4398,340,'Santa Catarina YosonotÃº','2022-10-18 23:21:41'),(4399,340,'Santa Catarina Zapoquila','2022-10-18 23:21:41'),(4400,340,'Santa Cruz Acatepec','2022-10-18 23:21:41'),(4401,340,'Santa Cruz Amilpas','2022-10-18 23:21:41'),(4402,340,'Santa Cruz de Bravo','2022-10-18 23:21:41'),(4403,340,'Santa Cruz Itundujia','2022-10-18 23:21:41'),(4404,340,'Santa Cruz Mixtepec','2022-10-18 23:21:41'),(4405,340,'Santa Cruz Nundaco','2022-10-18 23:21:41'),(4406,340,'Santa Cruz Papalutla','2022-10-18 23:21:41'),(4407,340,'Santa Cruz Tacache de Mina','2022-10-18 23:21:41'),(4408,340,'Santa Cruz Tacahua','2022-10-18 23:21:41'),(4409,340,'Santa Cruz Tayata','2022-10-18 23:21:41'),(4410,340,'Santa Cruz Xitla','2022-10-18 23:21:41'),(4411,340,'Santa Cruz XoxocotlÃ¡n','2022-10-18 23:21:41'),(4412,340,'Santa Cruz Zenzontepec','2022-10-18 23:21:41'),(4413,340,'Santa Gertrudis','2022-10-18 23:21:41'),(4414,340,'Santa InÃ©s del Monte','2022-10-18 23:21:41'),(4415,340,'Santa InÃ©s de Zaragoza','2022-10-18 23:21:41'),(4416,340,'Santa InÃ©s Yatzeche','2022-10-18 23:21:41'),(4417,340,'Santa LucÃ­a del Camino','2022-10-18 23:21:41'),(4418,340,'Santa LucÃ­a MiahuatlÃ¡n','2022-10-18 23:21:41'),(4419,340,'Santa LucÃ­a Monteverde','2022-10-18 23:21:41'),(4420,340,'Santa LucÃ­a OcotlÃ¡n','2022-10-18 23:21:41'),(4421,340,'Santa Magdalena JicotlÃ¡n','2022-10-18 23:21:41'),(4422,340,'Santa MarÃ­a Alotepec','2022-10-18 23:21:41'),(4423,340,'Santa MarÃ­a Apazco','2022-10-18 23:21:41'),(4424,340,'Santa MarÃ­a Atzompa','2022-10-18 23:21:41'),(4425,340,'Santa MarÃ­a CamotlÃ¡n','2022-10-18 23:21:41'),(4426,340,'Santa MarÃ­a Chachoapam','2022-10-18 23:21:41'),(4427,340,'Santa MarÃ­a Chilchotla','2022-10-18 23:21:41'),(4428,340,'Santa MarÃ­a Chimalapa','2022-10-18 23:21:41'),(4429,340,'Santa MarÃ­a Colotepec','2022-10-18 23:21:41'),(4430,340,'Santa MarÃ­a Cortijo','2022-10-18 23:21:41'),(4431,340,'Santa MarÃ­a Coyotepec','2022-10-18 23:21:41'),(4432,340,'Santa MarÃ­a del Rosario','2022-10-18 23:21:41'),(4433,340,'Santa MarÃ­a del Tule','2022-10-18 23:21:41'),(4434,340,'Santa MarÃ­a Ecatepec','2022-10-18 23:21:41'),(4435,340,'Santa MarÃ­a GuelacÃ©','2022-10-18 23:21:41'),(4436,340,'Santa MarÃ­a Guienagati','2022-10-18 23:21:41'),(4437,340,'Santa MarÃ­a Huatulco','2022-10-18 23:21:41'),(4438,340,'Santa MarÃ­a HuazolotitlÃ¡n','2022-10-18 23:21:41'),(4439,340,'Santa MarÃ­a Ipalapa','2022-10-18 23:21:41'),(4440,340,'Santa MarÃ­a IxcatlÃ¡n','2022-10-18 23:21:41'),(4441,340,'Santa MarÃ­a Jacatepec','2022-10-18 23:21:41'),(4442,340,'Santa MarÃ­a Jalapa del MarquÃ©s','2022-10-18 23:21:41'),(4443,340,'Santa MarÃ­a Jaltianguis','2022-10-18 23:21:41'),(4444,340,'Santa MarÃ­a la AsunciÃ³n','2022-10-18 23:21:41'),(4445,340,'Santa MarÃ­a LachixÃ­o','2022-10-18 23:21:41'),(4446,340,'Santa MarÃ­a Mixtequilla','2022-10-18 23:21:41'),(4447,340,'Santa MarÃ­a Nativitas','2022-10-18 23:21:41'),(4448,340,'Santa MarÃ­a Nduayaco','2022-10-18 23:21:41'),(4449,340,'Santa MarÃ­a Ozolotepec','2022-10-18 23:21:41'),(4450,340,'Santa MarÃ­a PÃ¡palo','2022-10-18 23:21:41'),(4451,340,'Santa MarÃ­a PeÃ±oles','2022-10-18 23:21:41'),(4452,340,'Santa MarÃ­a Petapa','2022-10-18 23:21:41'),(4453,340,'Santa MarÃ­a Quiegolani','2022-10-18 23:21:41'),(4454,340,'Santa MarÃ­a Sola','2022-10-18 23:21:41'),(4455,340,'Santa MarÃ­a Tataltepec','2022-10-18 23:21:41'),(4456,340,'Santa MarÃ­a Tecomavaca','2022-10-18 23:21:41'),(4457,340,'Santa MarÃ­a Temaxcalapa','2022-10-18 23:21:41'),(4458,340,'Santa MarÃ­a Temaxcaltepec','2022-10-18 23:21:41'),(4459,340,'Santa MarÃ­a Teopoxco','2022-10-18 23:21:41'),(4460,340,'Santa MarÃ­a Tepantlali','2022-10-18 23:21:41'),(4461,340,'Santa MarÃ­a TexcatitlÃ¡n','2022-10-18 23:21:41'),(4462,340,'Santa MarÃ­a Tlahuitoltepec','2022-10-18 23:21:41'),(4463,340,'Santa MarÃ­a Tlalixtac','2022-10-18 23:21:41'),(4464,340,'Santa MarÃ­a Tonameca','2022-10-18 23:21:41'),(4465,340,'Santa MarÃ­a Totolapilla','2022-10-18 23:21:41'),(4466,340,'Santa MarÃ­a Xadani','2022-10-18 23:21:41'),(4467,340,'Santa MarÃ­a Yalina','2022-10-18 23:21:41'),(4468,340,'Santa MarÃ­a YavesÃ­a','2022-10-18 23:21:41'),(4469,340,'Santa MarÃ­a Yolotepec','2022-10-18 23:21:41'),(4470,340,'Santa MarÃ­a YosoyÃºa','2022-10-18 23:21:41'),(4471,340,'Santa MarÃ­a Yucuhiti','2022-10-18 23:21:41'),(4472,340,'Santa MarÃ­a Zacatepec','2022-10-18 23:21:41'),(4473,340,'Santa MarÃ­a Zaniza','2022-10-18 23:21:41'),(4474,340,'Santa MarÃ­a ZoquitlÃ¡n','2022-10-18 23:21:41'),(4475,340,'Santiago Amoltepec','2022-10-18 23:21:41'),(4476,340,'Santiago Apoala','2022-10-18 23:21:41'),(4477,340,'Santiago ApÃ³stol','2022-10-18 23:21:41'),(4478,340,'Santiago Astata','2022-10-18 23:21:41'),(4479,340,'Santiago AtitlÃ¡n','2022-10-18 23:21:41'),(4480,340,'Santiago Ayuquililla','2022-10-18 23:21:41'),(4481,340,'Santiago Cacaloxtepec','2022-10-18 23:21:41'),(4482,340,'Santiago CamotlÃ¡n','2022-10-18 23:21:41'),(4483,340,'Santiago Chazumba','2022-10-18 23:21:41'),(4484,340,'Santiago Choapam','2022-10-18 23:21:41'),(4485,340,'Santiago Comaltepec','2022-10-18 23:21:41'),(4486,340,'Santiago del RÃ­o','2022-10-18 23:21:41'),(4487,340,'Santiago HuajolotitlÃ¡n','2022-10-18 23:21:41'),(4488,340,'Santiago Huauclilla','2022-10-18 23:21:41'),(4489,340,'Santiago IhuitlÃ¡n Plumas','2022-10-18 23:21:41'),(4490,340,'Santiago Ixcuintepec','2022-10-18 23:21:41'),(4491,340,'Santiago Ixtayutla','2022-10-18 23:21:41'),(4492,340,'Santiago Jamiltepec','2022-10-18 23:21:41'),(4493,340,'Santiago Jocotepec','2022-10-18 23:21:41'),(4494,340,'Santiago Juxtlahuaca','2022-10-18 23:21:41'),(4495,340,'Santiago Lachiguiri','2022-10-18 23:21:41'),(4496,340,'Santiago Lalopa','2022-10-18 23:21:41'),(4497,340,'Santiago Laollaga','2022-10-18 23:21:41'),(4498,340,'Santiago Laxopa','2022-10-18 23:21:41'),(4499,340,'Santiago Llano Grande','2022-10-18 23:21:41'),(4500,340,'Santiago MatatlÃ¡n','2022-10-18 23:21:41'),(4501,340,'Santiago Miltepec','2022-10-18 23:21:41'),(4502,340,'Santiago Minas','2022-10-18 23:21:41'),(4503,340,'Santiago Nacaltepec','2022-10-18 23:21:41'),(4504,340,'Santiago Nejapilla','2022-10-18 23:21:41'),(4505,340,'Santiago Niltepec','2022-10-18 23:21:41'),(4506,340,'Santiago Nundiche','2022-10-18 23:21:41'),(4507,340,'Santiago NuyoÃ³','2022-10-18 23:21:41'),(4508,340,'Santiago Suchilquitongo','2022-10-18 23:21:41'),(4509,340,'Santiago Tamazola','2022-10-18 23:21:41'),(4510,340,'Santiago Tapextla','2022-10-18 23:21:41'),(4511,340,'Santiago Tenango','2022-10-18 23:21:41'),(4512,340,'Santiago Tepetlapa','2022-10-18 23:21:41'),(4513,340,'Santiago Tetepec','2022-10-18 23:21:41'),(4514,340,'Santiago Texcalcingo','2022-10-18 23:21:41'),(4515,340,'Santiago TextitlÃ¡n','2022-10-18 23:21:41'),(4516,340,'Santiago Tilantongo','2022-10-18 23:21:41'),(4517,340,'Santiago Tillo','2022-10-18 23:21:41'),(4518,340,'Santiago Tlazoyaltepec','2022-10-18 23:21:41'),(4519,340,'Santiago Xanica','2022-10-18 23:21:41'),(4520,340,'Santiago XiacuÃ­','2022-10-18 23:21:41'),(4521,340,'Santiago Yaitepec','2022-10-18 23:21:41'),(4522,340,'Santiago Yaveo','2022-10-18 23:21:41'),(4523,340,'Santiago YolomÃ©catl','2022-10-18 23:21:41'),(4524,340,'Santiago YosondÃºa','2022-10-18 23:21:41'),(4525,340,'Santiago Yucuyachi','2022-10-18 23:21:41'),(4526,340,'Santiago Zacatepec','2022-10-18 23:21:41'),(4527,340,'Santiago Zoochila','2022-10-18 23:21:41'),(4528,340,'Santo Domingo Albarradas','2022-10-18 23:21:41'),(4529,340,'Santo Domingo Armenta','2022-10-18 23:21:41'),(4530,340,'Santo Domingo ChihuitÃ¡n','2022-10-18 23:21:41'),(4531,340,'Santo Domingo de Morelos','2022-10-18 23:21:41'),(4532,340,'Santo Domingo Ingenio','2022-10-18 23:21:41'),(4533,340,'Santo Domingo IxcatlÃ¡n','2022-10-18 23:21:41'),(4534,340,'Santo Domingo NuxaÃ¡','2022-10-18 23:21:41'),(4535,340,'Santo Domingo Ozolotepec','2022-10-18 23:21:41'),(4536,340,'Santo Domingo Petapa','2022-10-18 23:21:41'),(4537,340,'Santo Domingo Roayaga','2022-10-18 23:21:41'),(4538,340,'Santo Domingo Tehuantepec','2022-10-18 23:21:41'),(4539,340,'Santo Domingo Teojomulco','2022-10-18 23:21:41'),(4540,340,'Santo Domingo Tepuxtepec','2022-10-18 23:21:41'),(4541,340,'Santo Domingo Tlatayapam','2022-10-18 23:21:41'),(4542,340,'Santo Domingo Tomaltepec','2022-10-18 23:21:41'),(4543,340,'Santo Domingo TonalÃ¡','2022-10-18 23:21:41'),(4544,340,'Santo Domingo Tonaltepec','2022-10-18 23:21:41'),(4545,340,'Santo Domingo XagacÃ­a','2022-10-18 23:21:41'),(4546,340,'Santo Domingo YanhuitlÃ¡n','2022-10-18 23:21:41'),(4547,340,'Santo Domingo Yodohino','2022-10-18 23:21:41'),(4548,340,'Santo Domingo Zanatepec','2022-10-18 23:21:41'),(4549,340,'Santos Reyes Nopala','2022-10-18 23:21:41'),(4550,340,'Santos Reyes PÃ¡palo','2022-10-18 23:21:41'),(4551,340,'Santos Reyes Tepejillo','2022-10-18 23:21:41'),(4552,340,'Santos Reyes YucunÃ¡','2022-10-18 23:21:41'),(4553,340,'Santo TomÃ¡s Jalieza','2022-10-18 23:21:41'),(4554,340,'Santo TomÃ¡s Mazaltepec','2022-10-18 23:21:41'),(4555,340,'Santo TomÃ¡s Ocotepec','2022-10-18 23:21:41'),(4556,340,'Santo TomÃ¡s Tamazulapan','2022-10-18 23:21:41'),(4557,340,'San Vicente CoatlÃ¡n','2022-10-18 23:21:41'),(4558,340,'San Vicente LachixÃ­o','2022-10-18 23:21:41'),(4559,340,'San Vicente NuÃ±Ãº','2022-10-18 23:21:41'),(4560,340,'Silacayoapam','2022-10-18 23:21:41'),(4561,340,'Sitio de Xitlapehua','2022-10-18 23:21:41'),(4562,340,'Soledad Etla','2022-10-18 23:21:41'),(4563,340,'Tamazulapam del EspÃ­ritu Santo','2022-10-18 23:21:41'),(4564,340,'Tamazulapam del Progreso','2022-10-18 23:21:41'),(4565,340,'Tanetze de Zaragoza','2022-10-18 23:21:41'),(4566,340,'Taniche','2022-10-18 23:21:41'),(4567,340,'Tataltepec de ValdÃ©s','2022-10-18 23:21:41'),(4568,340,'Teococuilco de Marcos PÃ©rez','2022-10-18 23:21:41'),(4569,340,'TeotitlÃ¡n de Flores MagÃ³n','2022-10-18 23:21:41'),(4570,340,'TeotitlÃ¡n del Valle','2022-10-18 23:21:41'),(4571,340,'Teotongo','2022-10-18 23:21:41'),(4572,340,'Tepelmeme Villa de Morelos','2022-10-18 23:21:41'),(4573,340,'TezoatlÃ¡n de Segura y Luna','2022-10-18 23:21:41'),(4574,340,'Tlacolula de Matamoros','2022-10-18 23:21:41'),(4575,340,'Tlacotepec Plumas','2022-10-18 23:21:41'),(4576,340,'Tlalixtac de Cabrera','2022-10-18 23:21:41'),(4577,340,'Tlaxiaco','2022-10-18 23:21:41'),(4578,340,'Totontepec Villa de Morelos','2022-10-18 23:21:41'),(4579,340,'Trinidad Zaachila','2022-10-18 23:21:41'),(4580,340,'UniÃ³n Hidalgo','2022-10-18 23:21:41'),(4581,340,'Valerio Trujano','2022-10-18 23:21:41'),(4582,340,'Villa de Etla','2022-10-18 23:21:41'),(4583,340,'Villa de Tututepec de Melchor Ocampo','2022-10-18 23:21:41'),(4584,340,'Villa de Zaachila','2022-10-18 23:21:41'),(4585,340,'Cuyamecalco Villa de Zaragoza','2022-10-18 23:21:41'),(4586,340,'Villa DÃ­az Ordaz','2022-10-18 23:21:41'),(4587,340,'Villa Hidalgo','2022-10-18 23:21:41'),(4588,340,'Villa Sola de Vega','2022-10-18 23:21:41'),(4589,340,'Villa Talea de Castro','2022-10-18 23:21:41'),(4590,340,'Villa Tejupam de la UniÃ³n','2022-10-18 23:21:41'),(4591,340,'Yaxe Magdalena','2022-10-18 23:21:41'),(4592,340,'Magdalena Yodocono de Porfirio DÃ­az','2022-10-18 23:21:41'),(4593,340,'Yogana','2022-10-18 23:21:41'),(4594,340,'Yutanduchi de Guerrero','2022-10-18 23:21:41'),(4595,340,'ZapotitlÃ¡n del RÃ­o','2022-10-18 23:21:41'),(4596,340,'ZapotitlÃ¡n Lagunas','2022-10-18 23:21:41'),(4597,340,'ZapotitlÃ¡n Palmas','2022-10-18 23:21:41'),(4598,340,'ZimatlÃ¡n de Alvarez','2022-10-18 23:21:41'),(4599,353,'Acajete','2022-10-18 23:21:41'),(4600,353,'Acateno','2022-10-18 23:21:41'),(4601,353,'AcatlÃ¡n de Osorio','2022-10-18 23:21:41'),(4602,353,'Acatzingo','2022-10-18 23:21:41'),(4603,353,'Acteopan','2022-10-18 23:21:41'),(4604,353,'AhuacatlÃ¡n','2022-10-18 23:21:41'),(4605,353,'AhuatlÃ¡n','2022-10-18 23:21:41'),(4606,353,'Ahuazotepec','2022-10-18 23:21:41'),(4607,353,'Ahuehuetitla','2022-10-18 23:21:41'),(4608,353,'Ajalpan','2022-10-18 23:21:41'),(4609,353,'Albino Zertuche','2022-10-18 23:21:41'),(4610,353,'Aljojuca','2022-10-18 23:21:41'),(4611,353,'Altepexi','2022-10-18 23:21:41'),(4612,353,'Amixtlan','2022-10-18 23:21:41'),(4613,353,'Amozoc','2022-10-18 23:21:41'),(4614,353,'Aquixtla','2022-10-18 23:21:41'),(4615,353,'Atempan','2022-10-18 23:21:41'),(4616,353,'Atexcal','2022-10-18 23:21:41'),(4617,353,'Atlequizayan','2022-10-18 23:21:41'),(4618,353,'Atlixco','2022-10-18 23:21:41'),(4619,353,'Atoyatempan','2022-10-18 23:21:41'),(4620,353,'Atzala','2022-10-18 23:21:41'),(4621,353,'AtzitzihuacÃ¡n','2022-10-18 23:21:41'),(4622,353,'Atzitzintla','2022-10-18 23:21:41'),(4623,353,'Axutla','2022-10-18 23:21:41'),(4624,353,'Ayotoxco de Guerrero','2022-10-18 23:21:41'),(4625,353,'Calpan','2022-10-18 23:21:41'),(4626,353,'Caltepec','2022-10-18 23:21:41'),(4627,353,'Camocuautla','2022-10-18 23:21:41'),(4628,353,'CaÃ±ada Morelos','2022-10-18 23:21:41'),(4629,353,'CaxhuacÃ¡n','2022-10-18 23:21:41'),(4630,353,'Chalchicomula de Sesma','2022-10-18 23:21:41'),(4631,353,'Chapulco','2022-10-18 23:21:41'),(4632,353,'Chiautla','2022-10-18 23:21:41'),(4633,353,'Chiautzingo','2022-10-18 23:21:41'),(4634,353,'Chichiquila','2022-10-18 23:21:41'),(4635,353,'Chiconcuautla','2022-10-18 23:21:41'),(4636,353,'Chietla','2022-10-18 23:21:41'),(4637,353,'ChigmecatitlÃ¡n','2022-10-18 23:21:41'),(4638,353,'Chignahuapan','2022-10-18 23:21:41'),(4639,353,'Chignautla','2022-10-18 23:21:41'),(4640,353,'Chila','2022-10-18 23:21:41'),(4641,353,'Chila de la Sal','2022-10-18 23:21:41'),(4642,353,'Chilchotla','2022-10-18 23:21:41'),(4643,353,'Chinantla','2022-10-18 23:21:41'),(4644,353,'Coatepec','2022-10-18 23:21:41'),(4645,353,'Coatzingo','2022-10-18 23:21:41'),(4646,353,'Cohetzala','2022-10-18 23:21:41'),(4647,353,'CohuecÃ¡n','2022-10-18 23:21:41'),(4648,353,'Coronango','2022-10-18 23:21:41'),(4649,353,'CoxcatlÃ¡n','2022-10-18 23:21:41'),(4650,353,'Coyomeapan','2022-10-18 23:21:41'),(4651,353,'Coyotepec','2022-10-18 23:21:41'),(4652,353,'Cuapiaxtla de Madero','2022-10-18 23:21:41'),(4653,353,'Cuautempan','2022-10-18 23:21:41'),(4654,353,'Cuautinchan','2022-10-18 23:21:41'),(4655,353,'Cuautlancingo','2022-10-18 23:21:41'),(4656,353,'Cuayuca de Andrade','2022-10-18 23:21:41'),(4657,353,'CuetzalÃ¡n del Progreso','2022-10-18 23:21:41'),(4658,353,'Cuyoaco','2022-10-18 23:21:41'),(4659,353,'Domingo Arenas','2022-10-18 23:21:41'),(4660,353,'EloxochitlÃ¡n','2022-10-18 23:21:41'),(4661,353,'EpatlÃ¡n','2022-10-18 23:21:41'),(4662,353,'Esperanza','2022-10-18 23:21:41'),(4663,353,'Francisco Z. Mena','2022-10-18 23:21:41'),(4664,353,'General Felipe Ãngeles','2022-10-18 23:21:41'),(4665,353,'Guadalupe','2022-10-18 23:21:41'),(4666,353,'Guadalupe Victoria','2022-10-18 23:21:41'),(4667,353,'Hermenegildo Galeana','2022-10-18 23:21:41'),(4668,353,'Honey','2022-10-18 23:21:41'),(4669,353,'Huaquechula','2022-10-18 23:21:41'),(4670,353,'Huatlatlauca','2022-10-18 23:21:41'),(4671,353,'Huauchinango','2022-10-18 23:21:41'),(4672,353,'Huehuetla','2022-10-18 23:21:41'),(4673,353,'HuehuetlÃ¡n El Chico','2022-10-18 23:21:41'),(4674,353,'HuehuetlÃ¡n El Grande','2022-10-18 23:21:41'),(4675,353,'Huejotzingo','2022-10-18 23:21:41'),(4676,353,'Hueyapan','2022-10-18 23:21:41'),(4677,353,'Hueytamalco','2022-10-18 23:21:41'),(4678,353,'Hueytlalpan','2022-10-18 23:21:41'),(4679,353,'Huitzilan de SerdÃ¡n','2022-10-18 23:21:41'),(4680,353,'Huitziltepec','2022-10-18 23:21:41'),(4681,353,'Ixcamilpa','2022-10-18 23:21:41'),(4682,353,'Ixcaquixtla','2022-10-18 23:21:41'),(4683,353,'IxtacamaxtitlÃ¡n','2022-10-18 23:21:41'),(4684,353,'Ixtepec','2022-10-18 23:21:41'),(4685,353,'IzÃºcar de Matamoros','2022-10-18 23:21:41'),(4686,353,'Jalpan','2022-10-18 23:21:41'),(4687,353,'Jolalpan','2022-10-18 23:21:41'),(4688,353,'Jopala','2022-10-18 23:21:41'),(4689,353,'Juan C. Bonilla','2022-10-18 23:21:41'),(4690,353,'Juan Galindo','2022-10-18 23:21:41'),(4691,353,'Juan N. MÃ©ndez','2022-10-18 23:21:41'),(4692,353,'Lafragua','2022-10-18 23:21:41'),(4693,353,'Libres','2022-10-18 23:21:41'),(4694,353,'La Magdalena Tlatlauquitepec','2022-10-18 23:21:41'),(4695,353,'Los Reyes de JuÃ¡rez','2022-10-18 23:21:41'),(4696,353,'Mazapiltepec de JuÃ¡rez','2022-10-18 23:21:41'),(4697,353,'Mixtla','2022-10-18 23:21:41'),(4698,353,'Molcaxac','2022-10-18 23:21:41'),(4699,353,'Naupan','2022-10-18 23:21:41'),(4700,353,'Nauzontla','2022-10-18 23:21:41'),(4701,353,'NealticÃ¡n','2022-10-18 23:21:41'),(4702,353,'NicolÃ¡s Bravo','2022-10-18 23:21:41'),(4703,353,'Nopalucan','2022-10-18 23:21:41'),(4704,353,'Ocotepec','2022-10-18 23:21:41'),(4705,353,'Ocoyucan','2022-10-18 23:21:41'),(4706,353,'Olintla','2022-10-18 23:21:41'),(4707,353,'Oriental','2022-10-18 23:21:41'),(4708,353,'PahuatlÃ¡n','2022-10-18 23:21:41'),(4709,353,'Palmar de Bravo','2022-10-18 23:21:41'),(4710,353,'Pantepec','2022-10-18 23:21:41'),(4711,353,'Petlalcingo','2022-10-18 23:21:41'),(4712,353,'Piaxtla','2022-10-18 23:21:41'),(4713,353,'Puebla','2022-10-18 23:21:41'),(4714,353,'Quecholac','2022-10-18 23:21:41'),(4715,353,'QuimixtlÃ¡n','2022-10-18 23:21:41'),(4716,353,'Rafael Lara Grajales','2022-10-18 23:21:41'),(4717,353,'San AndrÃ©s Cholula','2022-10-18 23:21:41'),(4718,353,'San Antonio CaÃ±ada','2022-10-18 23:21:41'),(4719,353,'San Diego La Mesa Tochimiltzingo','2022-10-18 23:21:41'),(4720,353,'San Felipe Teotlalcingo','2022-10-18 23:21:41'),(4721,353,'San Felipe TepatlÃ¡n','2022-10-18 23:21:41'),(4722,353,'San Gabriel Chilac','2022-10-18 23:21:41'),(4723,353,'San Gregorio Atzompa','2022-10-18 23:21:41'),(4724,353,'San JerÃ³nimo Tecuanipan','2022-10-18 23:21:41'),(4725,353,'San JerÃ³nimo XayacatlÃ¡n','2022-10-18 23:21:41'),(4726,353,'San JosÃ© Chiapa','2022-10-18 23:21:41'),(4727,353,'San JosÃ© MiahuatlÃ¡n','2022-10-18 23:21:41'),(4728,353,'San Juan Atenco','2022-10-18 23:21:41'),(4729,353,'San Juan Atzompa','2022-10-18 23:21:41'),(4730,353,'San MartÃ­n Texmelucan','2022-10-18 23:21:41'),(4731,353,'San MartÃ­n Totoltepec','2022-10-18 23:21:41'),(4732,353,'San MatÃ­as Tlalancaleca','2022-10-18 23:21:41'),(4733,353,'San Miguel IxitlÃ¡n','2022-10-18 23:21:41'),(4734,353,'San Miguel Xoxtla','2022-10-18 23:21:41'),(4735,353,'San NicolÃ¡s de Buenos Aires','2022-10-18 23:21:41'),(4736,353,'San NicolÃ¡s de los Ranchos','2022-10-18 23:21:41'),(4737,353,'San Pablo Anicano','2022-10-18 23:21:41'),(4738,353,'San Pedro Cholula','2022-10-18 23:21:41'),(4739,353,'San Pedro Yeloixtlahuacan','2022-10-18 23:21:41'),(4740,353,'San Salvador El Seco','2022-10-18 23:21:41'),(4741,353,'San Salvador El Verde','2022-10-18 23:21:41'),(4742,353,'San Salvador Huixcolotla','2022-10-18 23:21:41'),(4743,353,'San SebastiÃ¡n Tlacotepec','2022-10-18 23:21:41'),(4744,353,'Santa Catarina Tlaltempan','2022-10-18 23:21:41'),(4745,353,'Santa InÃ©s Ahuatempan','2022-10-18 23:21:41'),(4746,353,'Santa Isabel Cholula','2022-10-18 23:21:41'),(4747,353,'Santiago MiahuatlÃ¡n','2022-10-18 23:21:41'),(4748,353,'Santo TomÃ¡s HueyotlipÃ¡n','2022-10-18 23:21:41'),(4749,353,'Soltepec','2022-10-18 23:21:41'),(4750,353,'Tecali','2022-10-18 23:21:41'),(4751,353,'Tecamachalco','2022-10-18 23:21:41'),(4752,353,'TecomatlÃ¡n','2022-10-18 23:21:41'),(4753,353,'TehuacÃ¡n','2022-10-18 23:21:41'),(4754,353,'Tehuitzingo','2022-10-18 23:21:41'),(4755,353,'Tenampulco','2022-10-18 23:21:41'),(4756,353,'TeopantlÃ¡n','2022-10-18 23:21:41'),(4757,353,'Teotlalco','2022-10-18 23:21:41'),(4758,353,'Tepanco de LÃ³pez','2022-10-18 23:21:41'),(4759,353,'Tepango de RodrÃ­guez','2022-10-18 23:21:41'),(4760,353,'Tepatlaxco de Hidalgo','2022-10-18 23:21:41'),(4761,353,'Tepeaca','2022-10-18 23:21:41'),(4762,353,'Tepemaxalco','2022-10-18 23:21:41'),(4763,353,'Tepeojuma','2022-10-18 23:21:41'),(4764,353,'Tepetzintla','2022-10-18 23:21:41'),(4765,353,'Tepexco','2022-10-18 23:21:41'),(4766,353,'Tepexi de RodrÃ­guez','2022-10-18 23:21:41'),(4767,353,'Tepeyahualco','2022-10-18 23:21:41'),(4768,353,'Tepeyahualco de CuauhtÃ©moc','2022-10-18 23:21:41'),(4769,353,'Tetela de Ocampo','2022-10-18 23:21:41'),(4770,353,'Teteles de Ãvila Castillo','2022-10-18 23:21:41'),(4771,353,'TezuitlÃ¡n','2022-10-18 23:21:41'),(4772,353,'Tianguismanalco','2022-10-18 23:21:41'),(4773,353,'Tilapa','2022-10-18 23:21:41'),(4774,353,'Tlachichuca','2022-10-18 23:21:41'),(4775,353,'Tlacotepec de Benito JuÃ¡rez','2022-10-18 23:21:41'),(4776,353,'Tlacuilotepec','2022-10-18 23:21:41'),(4777,353,'Tlahuapan','2022-10-18 23:21:41'),(4778,353,'Tlaltenango','2022-10-18 23:21:41'),(4779,353,'Tlanepantla','2022-10-18 23:21:41'),(4780,353,'Tlaola','2022-10-18 23:21:41'),(4781,353,'Tlapacoya','2022-10-18 23:21:41'),(4782,353,'Tlapanala','2022-10-18 23:21:41'),(4783,353,'Tlatlauquitepec','2022-10-18 23:21:41'),(4784,353,'Tlaxco','2022-10-18 23:21:41'),(4785,353,'Tochimilco','2022-10-18 23:21:41'),(4786,353,'Tochtepec','2022-10-18 23:21:41'),(4787,353,'Totoltepec de Guerrero','2022-10-18 23:21:41'),(4788,353,'Tulcingo','2022-10-18 23:21:41'),(4789,353,'TuzamapÃ¡n de Galeana','2022-10-18 23:21:41'),(4790,353,'Tzicatlacoyan','2022-10-18 23:21:41'),(4791,353,'Venustiano Carranza','2022-10-18 23:21:41'),(4792,353,'Vicente Guerrero','2022-10-18 23:21:41'),(4793,353,'XayacatlÃ¡n de Bravo','2022-10-18 23:21:41'),(4794,353,'Xicotepec','2022-10-18 23:21:41'),(4795,353,'XicotlÃ¡n','2022-10-18 23:21:41'),(4796,353,'Xiutetelco','2022-10-18 23:21:41'),(4797,353,'Xochiapulco','2022-10-18 23:21:41'),(4798,353,'Xochiltepec','2022-10-18 23:21:41'),(4799,353,'XochitlÃ¡n de Vicente SuÃ¡rez','2022-10-18 23:21:41'),(4800,353,'XochitlÃ¡n Todos Santos','2022-10-18 23:21:41'),(4801,353,'Xonotla','2022-10-18 23:21:41'),(4802,353,'Yaonahuac','2022-10-18 23:21:41'),(4803,353,'Yehualtepec','2022-10-18 23:21:41'),(4804,353,'Zacapala','2022-10-18 23:21:41'),(4805,353,'Zacapoaxtla','2022-10-18 23:21:41'),(4806,353,'ZacatlÃ¡n','2022-10-18 23:21:41'),(4807,353,'ZapotitlÃ¡n','2022-10-18 23:21:41'),(4808,353,'ZapotitlÃ¡n de MÃ©ndez','2022-10-18 23:21:41'),(4809,353,'Zaragoza','2022-10-18 23:21:41'),(4810,353,'Zautla','2022-10-18 23:21:41'),(4811,353,'Zihuateutla','2022-10-18 23:21:41'),(4812,353,'Zinacatepec','2022-10-18 23:21:41'),(4813,353,'Zongozotla','2022-10-18 23:21:41'),(4814,353,'Zoquiapan','2022-10-18 23:21:41'),(4815,353,'ZoquitlÃ¡n','2022-10-18 23:21:41'),(4816,193,'Cozumel','2022-10-18 23:21:41'),(4817,193,'Felipe Carrillo Puerto','2022-10-18 23:21:41'),(4818,193,'Isla Mujeres','2022-10-18 23:21:41'),(4819,193,'OthÃ³n P. Blanco','2022-10-18 23:21:41'),(4820,193,'Benito JuÃ¡rez','2022-10-18 23:21:41'),(4821,193,'JosÃ© MarÃ­a Morelos','2022-10-18 23:21:41'),(4822,193,'LÃ¡zaro CÃ¡rdenas','2022-10-18 23:21:41'),(4823,193,'Solidaridad','2022-10-18 23:21:41'),(4824,193,'Tulum','2022-10-18 23:21:41'),(4825,193,'Bacalar','2022-10-18 23:21:41'),(4826,200,'Ahome','2022-10-18 23:21:41'),(4827,200,'Angostura','2022-10-18 23:21:41'),(4828,200,'Badiraguato','2022-10-18 23:21:41'),(4829,200,'Concordia','2022-10-18 23:21:41'),(4830,200,'CosalÃ¡','2022-10-18 23:21:41'),(4831,200,'CuliacÃ¡n','2022-10-18 23:21:41'),(4832,200,'Choix','2022-10-18 23:21:41'),(4833,200,'Elota','2022-10-18 23:21:41'),(4834,200,'Escuinapa','2022-10-18 23:21:41'),(4835,200,'El Fuerte','2022-10-18 23:21:41'),(4836,200,'Guasave','2022-10-18 23:21:41'),(4837,200,'MazatlÃ¡n','2022-10-18 23:21:41'),(4838,200,'Mocorito','2022-10-18 23:21:41'),(4839,200,'Rosario','2022-10-18 23:21:41'),(4840,200,'Salvador Alvarado','2022-10-18 23:21:41'),(4841,200,'San Ignacio','2022-10-18 23:21:41'),(4842,200,'Sinaloa','2022-10-18 23:21:41'),(4843,200,'Navolato','2022-10-18 23:21:41'),(4844,197,'Aconchi','2022-10-18 23:21:41'),(4845,197,'Agua Prieta','2022-10-18 23:21:41'),(4846,197,'Altar','2022-10-18 23:21:41'),(4847,197,'Arivechi','2022-10-18 23:21:41'),(4848,197,'Arizpe','2022-10-18 23:21:41'),(4849,197,'Atil','2022-10-18 23:21:41'),(4850,197,'BacadÃ©huachi','2022-10-18 23:21:41'),(4851,197,'Bacanora','2022-10-18 23:21:41'),(4852,197,'Bacerac','2022-10-18 23:21:41'),(4853,197,'Bacoachi','2022-10-18 23:21:41'),(4854,197,'BÃ¡cum','2022-10-18 23:21:41'),(4855,197,'BanÃ¡michi','2022-10-18 23:21:41'),(4856,197,'BaviÃ¡cora','2022-10-18 23:21:41'),(4857,197,'Bavispe','2022-10-18 23:21:41'),(4858,197,'Benito JuÃ¡rez','2022-10-18 23:21:41'),(4859,197,'BenjamÃ­n Hill','2022-10-18 23:21:41'),(4860,197,'Caborca','2022-10-18 23:21:41'),(4861,197,'Cajeme','2022-10-18 23:21:41'),(4862,197,'Cananea','2022-10-18 23:21:41'),(4863,197,'CarbÃ³','2022-10-18 23:21:41'),(4864,197,'Cumpas','2022-10-18 23:21:41'),(4865,197,'Divisaderos','2022-10-18 23:21:41'),(4866,197,'Empalme','2022-10-18 23:21:41'),(4867,197,'Etchojoa','2022-10-18 23:21:41'),(4868,197,'Fronteras','2022-10-18 23:21:41'),(4869,197,'Granados','2022-10-18 23:21:41'),(4870,197,'Guaymas','2022-10-18 23:21:41'),(4871,197,'Hermosillo','2022-10-18 23:21:41'),(4872,197,'Huachinera','2022-10-18 23:21:41'),(4873,197,'HuÃ¡sabas','2022-10-18 23:21:41'),(4874,197,'Huatabampo','2022-10-18 23:21:41'),(4875,197,'HuÃ©pac','2022-10-18 23:21:41'),(4876,197,'Imuris','2022-10-18 23:21:41'),(4877,197,'La Colorada','2022-10-18 23:21:41'),(4878,197,'Magdalena de Kino','2022-10-18 23:21:41'),(4879,197,'MazatÃ¡n','2022-10-18 23:21:41'),(4880,197,'Moctezuma','2022-10-18 23:21:41'),(4881,197,'Naco','2022-10-18 23:21:41'),(4882,197,'NÃ¡cori Chico','2022-10-18 23:21:41'),(4883,197,'Nacozari de GarcÃ­a','2022-10-18 23:21:41'),(4884,197,'Navojoa','2022-10-18 23:21:41'),(4885,197,'Nogales','2022-10-18 23:21:41'),(4886,197,'Onavas','2022-10-18 23:21:41'),(4887,197,'Opodepe','2022-10-18 23:21:41'),(4888,197,'Oquitoa','2022-10-18 23:21:41'),(4889,197,'Pitiquito','2022-10-18 23:21:41'),(4890,197,'Puerto PeÃ±asco','2022-10-18 23:21:41'),(4891,197,'Plutarco ElÃ­as Calles','2022-10-18 23:21:41'),(4892,197,'Quiriego','2022-10-18 23:21:41'),(4893,197,'RayÃ³n','2022-10-18 23:21:41'),(4894,197,'Rosario de Tesopaco','2022-10-18 23:21:41'),(4895,197,'Sahuaripa','2022-10-18 23:21:41'),(4896,197,'San Ignacio RÃ­o Muerto','2022-10-18 23:21:41'),(4897,197,'San Javier','2022-10-18 23:21:41'),(4898,197,'San Luis RÃ­o Colorado','2022-10-18 23:21:41'),(4899,197,'San Miguel de Horcasitas','2022-10-18 23:21:41'),(4900,197,'San Pedro de la Cueva','2022-10-18 23:21:41'),(4901,197,'Santa Ana','2022-10-18 23:21:41'),(4902,197,'Santa Cruz','2022-10-18 23:21:41'),(4903,197,'SÃ¡ric','2022-10-18 23:21:41'),(4904,197,'Soyopa','2022-10-18 23:21:41'),(4905,197,'Suaqui Grande','2022-10-18 23:21:41'),(4906,197,'Tepache','2022-10-18 23:21:41'),(4907,197,'Trincheras','2022-10-18 23:21:41'),(4908,197,'Tubutama','2022-10-18 23:21:41'),(4909,197,'Ures','2022-10-18 23:21:41'),(4910,197,'Villa Hidalgo','2022-10-18 23:21:41'),(4911,197,'Villa Pesqueira','2022-10-18 23:21:41'),(4912,380,'BalancÃ¡n','2022-10-18 23:21:41'),(4913,380,'CÃ¡rdenas','2022-10-18 23:21:41'),(4914,380,'Centla','2022-10-18 23:21:41'),(4915,380,'Centro','2022-10-18 23:21:41'),(4916,380,'Comalcalco','2022-10-18 23:21:41'),(4917,380,'CunduacÃ¡n','2022-10-18 23:21:41'),(4918,380,'Emiliano Zapata','2022-10-18 23:21:41'),(4919,380,'Huimanguillo','2022-10-18 23:21:41'),(4920,380,'Jalapa','2022-10-18 23:21:41'),(4921,380,'Jalpa de MÃ©ndez','2022-10-18 23:21:41'),(4922,380,'Jonuta','2022-10-18 23:21:41'),(4923,380,'Macuspana','2022-10-18 23:21:41'),(4924,380,'Nacajuca','2022-10-18 23:21:41'),(4925,380,'ParaÃ­so','2022-10-18 23:21:41'),(4926,380,'Tacotalpa','2022-10-18 23:21:41'),(4927,380,'Teapa','2022-10-18 23:21:41'),(4928,380,'Tenosique','2022-10-18 23:21:41'),(4929,214,'Abasolo','2022-10-18 23:21:41'),(4930,214,'Aldama','2022-10-18 23:21:41'),(4931,214,'Altamira','2022-10-18 23:21:41'),(4932,214,'Antiguo Morelos','2022-10-18 23:21:41'),(4933,214,'Burgos','2022-10-18 23:21:41'),(4934,214,'Bustamante','2022-10-18 23:21:41'),(4935,214,'Camargo','2022-10-18 23:21:41'),(4936,214,'Casas','2022-10-18 23:21:41'),(4937,214,'Ciudad Madero','2022-10-18 23:21:41'),(4938,214,'Cruillas','2022-10-18 23:21:41'),(4939,214,'GÃ³mez FarÃ­as','2022-10-18 23:21:41'),(4940,214,'GonzÃ¡lez','2022-10-18 23:21:41'),(4941,214,'GÃ¼Ã©mez','2022-10-18 23:21:41'),(4942,214,'Guerrero','2022-10-18 23:21:41'),(4943,214,'Gustavo DÃ­az Ordaz','2022-10-18 23:21:41'),(4944,214,'Hidalgo','2022-10-18 23:21:41'),(4945,214,'Juamave','2022-10-18 23:21:41'),(4946,214,'JimÃ©nez','2022-10-18 23:21:41'),(4947,214,'Llera','2022-10-18 23:21:41'),(4948,214,'Mainero','2022-10-18 23:21:41'),(4949,214,'El Mante','2022-10-18 23:21:41'),(4950,214,'Matamoros','2022-10-18 23:21:41'),(4951,214,'MÃ©ndez','2022-10-18 23:21:41'),(4952,214,'Mier','2022-10-18 23:21:41'),(4953,214,'Miguel AlemÃ¡n','2022-10-18 23:21:41'),(4954,214,'Miquihuana','2022-10-18 23:21:41'),(4955,214,'Nuevo Laredo','2022-10-18 23:21:41'),(4956,214,'Nuevo Morelos','2022-10-18 23:21:41'),(4957,214,'Ocampo','2022-10-18 23:21:41'),(4958,214,'Padilla','2022-10-18 23:21:41'),(4959,214,'Palmillas','2022-10-18 23:21:41'),(4960,214,'Reynosa','2022-10-18 23:21:41'),(4961,214,'RÃ­o Bravo','2022-10-18 23:21:41'),(4962,214,'San Carlos','2022-10-18 23:21:41'),(4963,214,'San Fernando','2022-10-18 23:21:41'),(4964,214,'San NicolÃ¡s','2022-10-18 23:21:41'),(4965,214,'Soto la Marina','2022-10-18 23:21:41'),(4966,214,'Tampico','2022-10-18 23:21:41'),(4967,214,'Tula','2022-10-18 23:21:41'),(4968,214,'Valle Hermoso','2022-10-18 23:21:41'),(4969,214,'Victoria','2022-10-18 23:21:41'),(4970,214,'VillagrÃ¡n','2022-10-18 23:21:41'),(4971,214,'XicotÃ©ncatl','2022-10-18 23:21:41'),(4972,386,'Acuamanala de Miguel Hidalgo','2022-10-18 23:21:41'),(4973,386,'Altzayanca','2022-10-18 23:21:41'),(4974,386,'Amaxac de Guerrero','2022-10-18 23:21:41'),(4975,386,'ApetatitlÃ¡n de Antonio Carvajal','2022-10-18 23:21:41'),(4976,386,'Apizaco','2022-10-18 23:21:41'),(4977,386,'Atlangatepec','2022-10-18 23:21:41'),(4978,386,'Benito JuÃ¡rez','2022-10-18 23:21:41'),(4979,386,'Calpulalpan','2022-10-18 23:21:41'),(4980,386,'Chiautempan','2022-10-18 23:21:41'),(4981,386,'Contla de Juan Cuamatzi','2022-10-18 23:21:41'),(4982,386,'Cuapiaxtla','2022-10-18 23:21:41'),(4983,386,'Cuaxomulco','2022-10-18 23:21:41'),(4984,386,'El Carmen Tequexquitla','2022-10-18 23:21:41'),(4985,386,'Emiliano Zapata','2022-10-18 23:21:41'),(4986,386,'EspaÃ±ita','2022-10-18 23:21:41'),(4987,386,'Huamantla','2022-10-18 23:21:41'),(4988,386,'Hueyotlipan','2022-10-18 23:21:41'),(4989,386,'Ixtacuixtla de Mariano Matamoros','2022-10-18 23:21:41'),(4990,386,'Ixtenco','2022-10-18 23:21:41'),(4991,386,'La Magdalena Tlaltelulco','2022-10-18 23:21:41'),(4992,386,'LÃ¡zaro CÃ¡rdenas','2022-10-18 23:21:41'),(4993,386,'Mazatecochco de JosÃ© MarÃ­a Morelos','2022-10-18 23:21:41'),(4994,386,'MuÃ±oz de Domingo Arenas','2022-10-18 23:21:41'),(4995,386,'Nanacamilpa de Mariano Arista','2022-10-18 23:21:41'),(4996,386,'Nativitas','2022-10-18 23:21:41'),(4997,386,'Panotla','2022-10-18 23:21:41'),(4998,386,'Papalotla de Xicohtencatl','2022-10-18 23:21:41'),(4999,386,'Sanctorum de LÃ¡zaro CÃ¡rdenas','2022-10-18 23:21:41'),(5000,386,'San DamiÃ¡n Texoloc','2022-10-18 23:21:41'),(5001,386,'San Francisco Tetlanohcan','2022-10-18 23:21:41'),(5002,386,'San JerÃ³nimo Zacualpan','2022-10-18 23:21:41'),(5003,386,'San JosÃ© Teacalco','2022-10-18 23:21:41'),(5004,386,'San Juan Huactzinco','2022-10-18 23:21:41'),(5005,386,'San Lorenzo Axocomanitla','2022-10-18 23:21:41'),(5006,386,'San Lucas Tecopilco','2022-10-18 23:21:41'),(5007,386,'San Pablo del Monte','2022-10-18 23:21:41'),(5008,386,'Santa Ana Nopalucan','2022-10-18 23:21:41'),(5009,386,'Santa Apolonia Teacalco','2022-10-18 23:21:41'),(5010,386,'Santa Catarina Ayometla','2022-10-18 23:21:41'),(5011,386,'Santa Cruz Quilehtla','2022-10-18 23:21:41'),(5012,386,'Santa Cruz Tlaxcala','2022-10-18 23:21:41'),(5013,386,'Santa Isabel Xiloxoxtla','2022-10-18 23:21:41'),(5014,386,'Tenancingo','2022-10-18 23:21:41'),(5015,386,'Teolocholco','2022-10-18 23:21:41'),(5016,386,'Tepetitla de Lardizabal','2022-10-18 23:21:41'),(5017,386,'Tepeyanco','2022-10-18 23:21:41'),(5018,386,'Terrenate','2022-10-18 23:21:41'),(5019,386,'Tetla de la Solidaridad','2022-10-18 23:21:41'),(5020,386,'Tetlatlahuca','2022-10-18 23:21:41'),(5021,386,'Tlaxcala','2022-10-18 23:21:41'),(5022,386,'Tlaxco','2022-10-18 23:21:41'),(5023,386,'TocatlÃ¡n','2022-10-18 23:21:41'),(5024,386,'Totolac','2022-10-18 23:21:41'),(5025,386,'Tzompantepec','2022-10-18 23:21:41'),(5026,386,'Xaloztoc','2022-10-18 23:21:41'),(5027,386,'Xaltocan','2022-10-18 23:21:41'),(5028,386,'Xicohtzinco','2022-10-18 23:21:41'),(5029,386,'Yauhquemecan','2022-10-18 23:21:41'),(5030,386,'Zacatelco','2022-10-18 23:21:41'),(5031,386,'Zitlaltepec de Trinidad SÃ¡nchez Santos','2022-10-18 23:21:41'),(5032,397,'Acajete','2022-10-18 23:21:41'),(5033,397,'AcatlÃ¡n','2022-10-18 23:21:41'),(5034,397,'Acayucan','2022-10-18 23:21:41'),(5035,397,'Actopan','2022-10-18 23:21:41'),(5036,397,'Acula','2022-10-18 23:21:41'),(5037,397,'Acultzingo','2022-10-18 23:21:41'),(5038,397,'Agua Dulce','2022-10-18 23:21:41'),(5039,397,'Alpatlahuac','2022-10-18 23:21:41'),(5040,397,'Alto Lucero de GutiÃ©rrez Barrios','2022-10-18 23:21:41'),(5041,397,'Altotonga','2022-10-18 23:21:41'),(5042,397,'Alvarado','2022-10-18 23:21:41'),(5043,397,'AmatitlÃ¡n','2022-10-18 23:21:41'),(5044,397,'AmatlÃ¡n de los Reyes','2022-10-18 23:21:41'),(5045,397,'Ãngel R. Cabada','2022-10-18 23:21:41'),(5046,397,'Apazapan','2022-10-18 23:21:41'),(5047,397,'Aquila','2022-10-18 23:21:41'),(5048,397,'Astacinga','2022-10-18 23:21:41'),(5049,397,'Atlahuilco','2022-10-18 23:21:41'),(5050,397,'Atoyac','2022-10-18 23:21:41'),(5051,397,'Atzacan','2022-10-18 23:21:41'),(5052,397,'AtzalÃ¡n','2022-10-18 23:21:41'),(5053,397,'Ayahualulco','2022-10-18 23:21:41'),(5054,397,'Banderilla','2022-10-18 23:21:41'),(5055,397,'Benito JuÃ¡rez','2022-10-18 23:21:41'),(5056,397,'Boca del RÃ­o','2022-10-18 23:21:41'),(5057,397,'Calcahualco','2022-10-18 23:21:41'),(5058,397,'CamarÃ³n de Tejeda','2022-10-18 23:21:41'),(5059,397,'Camerino Z. Mendoza','2022-10-18 23:21:41'),(5060,397,'Carlos A. Carrillo','2022-10-18 23:21:41'),(5061,397,'Carrillo Puerto','2022-10-18 23:21:41'),(5062,397,'Castillo de Teayo','2022-10-18 23:21:41'),(5063,397,'Catemaco','2022-10-18 23:21:41'),(5064,397,'Cazones','2022-10-18 23:21:41'),(5065,397,'Cerro Azul','2022-10-18 23:21:41'),(5066,397,'Chacaltianguis','2022-10-18 23:21:41'),(5067,397,'Chalma','2022-10-18 23:21:41'),(5068,397,'Chiconamel','2022-10-18 23:21:41'),(5069,397,'Chiconquiaco','2022-10-18 23:21:41'),(5070,397,'Chicontepec','2022-10-18 23:21:41'),(5071,397,'Chinameca','2022-10-18 23:21:41'),(5072,397,'Chinampa de Gorostiza','2022-10-18 23:21:41'),(5073,397,'Chocaman','2022-10-18 23:21:41'),(5074,397,'Chontla','2022-10-18 23:21:41'),(5075,397,'Chumatlan','2022-10-18 23:21:41'),(5076,397,'Citlaltepetl','2022-10-18 23:21:41'),(5077,397,'Coacoatzintla','2022-10-18 23:21:41'),(5078,397,'Coahuitlan','2022-10-18 23:21:41'),(5079,397,'Coatepec','2022-10-18 23:21:41'),(5080,397,'Coatzacoalcos','2022-10-18 23:21:41'),(5081,397,'Coatzintla','2022-10-18 23:21:41'),(5082,397,'Coetzala','2022-10-18 23:21:41'),(5083,397,'Colipa','2022-10-18 23:21:41'),(5084,397,'Comapa','2022-10-18 23:21:41'),(5085,397,'CÃ³rdoba','2022-10-18 23:21:41'),(5086,397,'Cosamaloapan de Carpio','2022-10-18 23:21:41'),(5087,397,'CosautlÃ¡n de Carvajal','2022-10-18 23:21:41'),(5088,397,'Coscomatepec','2022-10-18 23:21:41'),(5089,397,'Cosoleacaque','2022-10-18 23:21:41'),(5090,397,'Cotaxtla','2022-10-18 23:21:41'),(5091,397,'Coxquihui','2022-10-18 23:21:41'),(5092,397,'Coyutla','2022-10-18 23:21:41'),(5093,397,'Cuichapa','2022-10-18 23:21:41'),(5094,397,'CuitlÃ¡huac','2022-10-18 23:21:41'),(5095,397,'El Higo','2022-10-18 23:21:41'),(5096,397,'Emiliano Zapata','2022-10-18 23:21:41'),(5097,397,'Espinal','2022-10-18 23:21:41'),(5098,397,'Filomeno Mata','2022-10-18 23:21:41'),(5099,397,'FortÃ­n','2022-10-18 23:21:41'),(5100,397,'GutiÃ©rrez Zamora','2022-10-18 23:21:41'),(5101,397,'HidalgotitlÃ¡n','2022-10-18 23:21:41'),(5102,397,'Huatusco','2022-10-18 23:21:41'),(5103,397,'Huayacocotla','2022-10-18 23:21:41'),(5104,397,'Hueyapan de Ocampo','2022-10-18 23:21:41'),(5105,397,'Huiloapan','2022-10-18 23:21:41'),(5106,397,'Ignacio de la Llave','2022-10-18 23:21:41'),(5107,397,'IlamatlÃ¡n','2022-10-18 23:21:41'),(5108,397,'Isla','2022-10-18 23:21:41'),(5109,397,'Ixcatepec','2022-10-18 23:21:41'),(5110,397,'IxhuacÃ¡n de los Reyes','2022-10-18 23:21:41'),(5111,397,'Ixhuatlancillo','2022-10-18 23:21:41'),(5112,397,'IxhuatlÃ¡n del CafÃ©','2022-10-18 23:21:41'),(5113,397,'IxhuatlÃ¡n del Sureste','2022-10-18 23:21:41'),(5114,397,'IxhuatlÃ¡n de Madero','2022-10-18 23:21:41'),(5115,397,'Ixmatlahuacan','2022-10-18 23:21:41'),(5116,397,'IxtaczoquitlÃ¡n','2022-10-18 23:21:41'),(5117,397,'Jalacingo','2022-10-18 23:21:41'),(5118,397,'Jalcomulco','2022-10-18 23:21:41'),(5119,397,'Jaltipan','2022-10-18 23:21:41'),(5120,397,'Jamapa','2022-10-18 23:21:41'),(5121,397,'JesÃºs Carranza','2022-10-18 23:21:41'),(5122,397,'Jilotepec','2022-10-18 23:21:41'),(5123,397,'JosÃ© Azueta','2022-10-18 23:21:41'),(5124,397,'Juan RodrÃ­guez Clara','2022-10-18 23:21:41'),(5125,397,'Juchique de Ferrer','2022-10-18 23:21:41'),(5126,397,'Landero y Coss','2022-10-18 23:21:41'),(5127,397,'La Antigua','2022-10-18 23:21:41'),(5128,397,'La Perla','2022-10-18 23:21:41'),(5129,397,'Las Choapas','2022-10-18 23:21:41'),(5130,397,'Las Minas','2022-10-18 23:21:41'),(5131,397,'Las Vigas de RamÃ­rez','2022-10-18 23:21:41'),(5132,397,'Lerdo de Tejada','2022-10-18 23:21:41'),(5133,397,'Los Reyes','2022-10-18 23:21:41'),(5134,397,'Magdalena','2022-10-18 23:21:41'),(5135,397,'Maltrata','2022-10-18 23:21:41'),(5136,397,'Manlio Fabio Altamirano','2022-10-18 23:21:41'),(5137,397,'Mariano Escobedo','2022-10-18 23:21:41'),(5138,397,'MartÃ­nez de la Torre','2022-10-18 23:21:41'),(5139,397,'MecatlÃ¡n','2022-10-18 23:21:41'),(5140,397,'Mecayapan','2022-10-18 23:21:41'),(5141,397,'MedellÃ­n','2022-10-18 23:21:41'),(5142,397,'MiahuatlÃ¡n','2022-10-18 23:21:41'),(5143,397,'MinatitlÃ¡n','2022-10-18 23:21:41'),(5144,397,'Misantla','2022-10-18 23:21:41'),(5145,397,'Mixtla de Altamirano','2022-10-18 23:21:41'),(5146,397,'MoloacÃ¡n','2022-10-18 23:21:41'),(5147,397,'Nanchital','2022-10-18 23:21:41'),(5148,397,'Naolinco','2022-10-18 23:21:41'),(5149,397,'Naranjal','2022-10-18 23:21:41'),(5150,397,'Naranjos AmatlÃ¡n','2022-10-18 23:21:41'),(5151,397,'Nautla','2022-10-18 23:21:41'),(5152,397,'Nogales','2022-10-18 23:21:41'),(5153,397,'Oluta','2022-10-18 23:21:41'),(5154,397,'Omealca','2022-10-18 23:21:41'),(5155,397,'Orizaba','2022-10-18 23:21:41'),(5156,397,'OtatitlÃ¡n','2022-10-18 23:21:41'),(5157,397,'Oteapan','2022-10-18 23:21:41'),(5158,397,'Ozuluama de MascareÃ±as','2022-10-18 23:21:41'),(5159,397,'Pajapan','2022-10-18 23:21:41'),(5160,397,'PÃ¡nuco','2022-10-18 23:21:41'),(5161,397,'Papantla','2022-10-18 23:21:41'),(5162,397,'Paso de Ovejas','2022-10-18 23:21:41'),(5163,397,'Paso del Macho','2022-10-18 23:21:41'),(5164,397,'Perote','2022-10-18 23:21:41'),(5165,397,'PlatÃ³n SÃ¡nchez','2022-10-18 23:21:41'),(5166,397,'Playa Vicente','2022-10-18 23:21:41'),(5167,397,'Poza Rica de Hidalgo','2022-10-18 23:21:41'),(5168,397,'Pueblo Viejo','2022-10-18 23:21:41'),(5169,397,'Puente Nacional','2022-10-18 23:21:41'),(5170,397,'Rafael Delgado','2022-10-18 23:21:41'),(5171,397,'Rafael Lucio','2022-10-18 23:21:41'),(5172,397,'RÃ­o Blanco','2022-10-18 23:21:41'),(5173,397,'Saltabarranca','2022-10-18 23:21:41'),(5174,397,'San AndrÃ©s Tenejapan','2022-10-18 23:21:41'),(5175,397,'San AndrÃ©s Tuxtla','2022-10-18 23:21:41'),(5176,397,'San Juan Evangelista','2022-10-18 23:21:41'),(5177,397,'Santiago Tuxtla','2022-10-18 23:21:41'),(5178,397,'Sayula de AlemÃ¡n','2022-10-18 23:21:41'),(5179,397,'Sochiapa','2022-10-18 23:21:41'),(5180,397,'Soconusco','2022-10-18 23:21:41'),(5181,397,'Soledad Atzompa','2022-10-18 23:21:41'),(5182,397,'Soledad de Doblado','2022-10-18 23:21:41'),(5183,397,'Soteapan','2022-10-18 23:21:41'),(5184,397,'TamalÃ­n','2022-10-18 23:21:41'),(5185,397,'Tamiahua','2022-10-18 23:21:41'),(5186,397,'Tampico Alto','2022-10-18 23:21:41'),(5187,397,'Tancoco','2022-10-18 23:21:41'),(5188,397,'Tantima','2022-10-18 23:21:41'),(5189,397,'Tantoyuca','2022-10-18 23:21:41'),(5190,397,'Tatahuicapan de JuÃ¡rez','2022-10-18 23:21:41'),(5191,397,'Tatatila','2022-10-18 23:21:41'),(5192,397,'Tecolutla','2022-10-18 23:21:41'),(5193,397,'Tehuipango','2022-10-18 23:21:41'),(5194,397,'Temapache','2022-10-18 23:21:41'),(5195,397,'Tempoal','2022-10-18 23:21:41'),(5196,397,'Tenampa','2022-10-18 23:21:41'),(5197,397,'TenochtitlÃ¡n','2022-10-18 23:21:41'),(5198,397,'Teocelo','2022-10-18 23:21:41'),(5199,397,'Tepatlaxco','2022-10-18 23:21:41'),(5200,397,'TepetlÃ¡n','2022-10-18 23:21:41'),(5201,397,'Tepetzintla','2022-10-18 23:21:41'),(5202,397,'Tequila','2022-10-18 23:21:41'),(5203,397,'Texcatepec','2022-10-18 23:21:41'),(5204,397,'TexhuacÃ¡n','2022-10-18 23:21:41'),(5205,397,'Texistepec','2022-10-18 23:21:41'),(5206,397,'Tezonapa','2022-10-18 23:21:41'),(5207,397,'Tierra Blanca','2022-10-18 23:21:41'),(5208,397,'TihuatlÃ¡n','2022-10-18 23:21:41'),(5209,397,'Tlachichilco','2022-10-18 23:21:41'),(5210,397,'Tlacojalpan','2022-10-18 23:21:41'),(5211,397,'Tlacolulan','2022-10-18 23:21:41'),(5212,397,'Tlacotalpan','2022-10-18 23:21:41'),(5213,397,'Tlacotepec de MejÃ­a','2022-10-18 23:21:41'),(5214,397,'Tlalixcoyan','2022-10-18 23:21:41'),(5215,397,'Tlalnelhuayocan','2022-10-18 23:21:41'),(5216,397,'Tlaltetela','2022-10-18 23:21:41'),(5217,397,'Tlapacoyan','2022-10-18 23:21:41'),(5218,397,'Tlaquilpa','2022-10-18 23:21:41'),(5219,397,'Tlilapan','2022-10-18 23:21:41'),(5220,397,'TomatlÃ¡n','2022-10-18 23:21:41'),(5221,397,'Tonayan','2022-10-18 23:21:41'),(5222,397,'Totutla','2022-10-18 23:21:41'),(5223,397,'Tres Valles','2022-10-18 23:21:41'),(5224,397,'Tuxpam','2022-10-18 23:21:41'),(5225,397,'Tuxtilla','2022-10-18 23:21:41'),(5226,397,'Ãšrsulo GalvÃ¡n','2022-10-18 23:21:41'),(5227,397,'Uxpanapa','2022-10-18 23:21:41'),(5228,397,'Vega de Alatorre','2022-10-18 23:21:41'),(5229,397,'Veracruz','2022-10-18 23:21:41'),(5230,397,'Villa Aldama','2022-10-18 23:21:41'),(5231,397,'Xalapa','2022-10-18 23:21:41'),(5232,397,'Xico','2022-10-18 23:21:41'),(5233,397,'Xoxocotla','2022-10-18 23:21:41'),(5234,397,'Yanga','2022-10-18 23:21:41'),(5235,397,'Yecuatla','2022-10-18 23:21:41'),(5236,397,'Zacualpan','2022-10-18 23:21:41'),(5237,397,'Zaragoza','2022-10-18 23:21:41'),(5238,397,'Zentla','2022-10-18 23:21:41'),(5239,397,'Zongolica','2022-10-18 23:21:41'),(5240,397,'ZontecomatlÃ¡n de LÃ³pez y Fuentes','2022-10-18 23:21:41'),(5241,397,'Zozocolco de Hidalgo','2022-10-18 23:21:41'),(5242,397,'San Rafael','2022-10-18 23:21:41'),(5243,397,'Santiago Sochiapan','2022-10-18 23:21:41'),(5244,207,'Apozol','2022-10-18 23:21:41'),(5245,207,'Apulco','2022-10-18 23:21:41'),(5246,207,'Atolinga','2022-10-18 23:21:41'),(5247,207,'Florencia de Benito JuÃ¡rez','2022-10-18 23:21:41'),(5248,207,'Calera de VÃ­ctor Rosales','2022-10-18 23:21:41'),(5249,207,'CaÃ±itas de Felipe Pescador','2022-10-18 23:21:41'),(5250,207,'ConcepciÃ³n del Oro','2022-10-18 23:21:41'),(5251,207,'CuauhtÃ©moc','2022-10-18 23:21:41'),(5252,207,'Chalchihuites','2022-10-18 23:21:41'),(5253,207,'El Plateado de JoaquÃ­n Amaro','2022-10-18 23:21:41'),(5254,207,'El Salvador','2022-10-18 23:21:41'),(5255,207,'Fresnillo','2022-10-18 23:21:41'),(5256,207,'Genaro Codina','2022-10-18 23:21:41'),(5257,207,'General Enrique Estrada','2022-10-18 23:21:41'),(5258,207,'General Francisco R MurguÃ­a','2022-10-18 23:21:41'),(5259,207,'General PÃ¡nfilo Natera','2022-10-18 23:21:41'),(5260,207,'Guadalupe','2022-10-18 23:21:41'),(5261,207,'Huanusco','2022-10-18 23:21:41'),(5262,207,'Jalpa','2022-10-18 23:21:41'),(5263,207,'Jerez de GarcÃ­a Salinas','2022-10-18 23:21:41'),(5264,207,'JimÃ©nez del Teul','2022-10-18 23:21:41'),(5265,207,'Juan Aldama','2022-10-18 23:21:41'),(5266,207,'Juchipila','2022-10-18 23:21:41'),(5267,207,'Loreto','2022-10-18 23:21:41'),(5268,207,'Luis Moya','2022-10-18 23:21:41'),(5269,207,'Mazapil','2022-10-18 23:21:41'),(5270,207,'Melchor Ocampo','2022-10-18 23:21:41'),(5271,207,'Mezquital del Oro','2022-10-18 23:21:41'),(5272,207,'Miguel Auza','2022-10-18 23:21:41'),(5273,207,'Momax','2022-10-18 23:21:41'),(5274,207,'Monte Escobedo','2022-10-18 23:21:41'),(5275,207,'Morelos','2022-10-18 23:21:41'),(5276,207,'Moyahua de Estrada','2022-10-18 23:21:41'),(5277,207,'NochistlÃ¡n de MejÃ­a','2022-10-18 23:21:41'),(5278,207,'Noria de Ãngeles','2022-10-18 23:21:41'),(5279,207,'Ojocaliente','2022-10-18 23:21:41'),(5280,207,'PÃ¡nuco','2022-10-18 23:21:41'),(5281,207,'Pinos','2022-10-18 23:21:41'),(5282,207,'RÃ­o Grande','2022-10-18 23:21:41'),(5283,207,'Santa MarÃ­a de la Paz','2022-10-18 23:21:41'),(5289,207,'SusticacÃ¡n','2022-10-18 23:21:41'),(5316,207,'Sombrerete','2022-10-18 23:21:41'),(5317,207,'Tabasco','2022-10-18 23:21:41'),(5318,207,'TepechitlÃ¡n','2022-10-18 23:21:41'),(5319,207,'Tepetongo','2022-10-18 23:21:41'),(5320,207,'TeÃºl de GonzÃ¡lez Ortega','2022-10-18 23:21:41'),(5321,207,'Tlaltenango de SÃ¡nchez RomÃ¡n','2022-10-18 23:21:41'),(5322,207,'Valparaiso','2022-10-18 23:21:41'),(5323,207,'Trinidad GarcÃ­a de la Cadena','2022-10-18 23:21:41'),(5325,207,'Vetagrande','2022-10-18 23:21:41'),(5326,207,'Villa de Cos','2022-10-18 23:21:41'),(5327,207,'Villa GarcÃ­a','2022-10-18 23:21:41'),(5328,207,'Villa GonzÃ¡lez Ortega','2022-10-18 23:21:41'),(5329,207,'Villa Hidalgo','2022-10-18 23:21:41'),(5330,207,'Villanueva','2022-10-18 23:21:41'),(5331,207,'Zacatecas','2022-10-18 23:21:41'),(5332,328,'Acambay','2022-10-18 23:21:41'),(5333,328,'Acolman','2022-10-18 23:21:41'),(5334,328,'Aculco','2022-10-18 23:21:41'),(5335,328,'Almoloya de Alquisiras','2022-10-18 23:21:41'),(5336,328,'Almoloya de JuÃ¡rez','2022-10-18 23:21:41'),(5337,328,'Almoloya del RÃ­o','2022-10-18 23:21:41'),(5338,328,'Amanalco','2022-10-18 23:21:41'),(5339,328,'Amatepec','2022-10-18 23:21:41'),(5340,328,'Amecameca','2022-10-18 23:21:41'),(5341,328,'Apaxco','2022-10-18 23:21:41'),(5342,328,'Atenco','2022-10-18 23:21:41'),(5343,328,'AtizapÃ¡n','2022-10-18 23:21:41'),(5344,328,'AtizapÃ¡n de Zaragoza','2022-10-18 23:21:41'),(5345,328,'Atlacomulco','2022-10-18 23:21:41'),(5346,328,'Atlautla','2022-10-18 23:21:41'),(5347,328,'Axapusco','2022-10-18 23:21:41'),(5348,328,'Ayapango','2022-10-18 23:21:41'),(5349,328,'Calimaya','2022-10-18 23:21:41'),(5350,328,'Capulhuac','2022-10-18 23:21:41'),(5351,328,'Coacalco de BerriozÃ¡bal','2022-10-18 23:21:41'),(5352,328,'Coatepec Harinas','2022-10-18 23:21:41'),(5353,328,'CocotitlÃ¡n','2022-10-18 23:21:41'),(5354,328,'Coyotepec','2022-10-18 23:21:41'),(5355,328,'CuautitlÃ¡n','2022-10-18 23:21:41'),(5356,328,'Chalco','2022-10-18 23:21:41'),(5357,328,'Chapa de Mota','2022-10-18 23:21:41'),(5358,328,'Chapultepec','2022-10-18 23:21:41'),(5359,328,'Chiautla','2022-10-18 23:21:41'),(5360,328,'Chicoloapan','2022-10-18 23:21:41'),(5361,328,'Chiconcuac','2022-10-18 23:21:41'),(5362,328,'ChimalhuacÃ¡n','2022-10-18 23:21:41'),(5363,328,'Donato Guerra','2022-10-18 23:21:41'),(5364,328,'Ecatepec de Morelos','2022-10-18 23:21:41'),(5365,328,'Ecatzingo','2022-10-18 23:21:41'),(5366,328,'Huehuetoca','2022-10-18 23:21:41'),(5367,328,'Hueypoxtla','2022-10-18 23:21:41'),(5368,328,'Huixquilucan','2022-10-18 23:21:41'),(5369,328,'Isidro Fabela','2022-10-18 23:21:41'),(5370,328,'Ixtapaluca','2022-10-18 23:21:41'),(5371,328,'Ixtapan de la Sal','2022-10-18 23:21:41'),(5372,328,'Ixtapan del Oro','2022-10-18 23:21:41'),(5373,328,'Ixtlahuaca','2022-10-18 23:21:41'),(5374,328,'Xalatlaco','2022-10-18 23:21:41'),(5375,328,'Jaltenco','2022-10-18 23:21:41'),(5376,328,'Jilotepec','2022-10-18 23:21:41'),(5377,328,'Jilotzingo','2022-10-18 23:21:41'),(5378,328,'Jiquipilco','2022-10-18 23:21:41'),(5379,328,'JocotitlÃ¡n','2022-10-18 23:21:41'),(5380,328,'Joquicingo','2022-10-18 23:21:41'),(5381,328,'Juchitepec','2022-10-18 23:21:41'),(5382,328,'Lerma','2022-10-18 23:21:41'),(5383,328,'Malinalco','2022-10-18 23:21:41'),(5384,328,'Melchor Ocampo','2022-10-18 23:21:41'),(5385,328,'Metepec','2022-10-18 23:21:41'),(5386,328,'Mexicaltzingo','2022-10-18 23:21:41'),(5387,328,'Morelos','2022-10-18 23:21:41'),(5388,328,'Naucalpan','2022-10-18 23:21:41'),(5389,328,'NezahualcÃ³yotl','2022-10-18 23:21:41'),(5390,328,'Nextlalpan','2022-10-18 23:21:41'),(5391,328,'NicolÃ¡s Romero','2022-10-18 23:21:41'),(5392,328,'Nopaltepec','2022-10-18 23:21:41'),(5393,328,'Ocoyoacac','2022-10-18 23:21:41'),(5394,328,'OcuilÃ¡n','2022-10-18 23:21:41'),(5395,328,'El Oro','2022-10-18 23:21:41'),(5396,328,'Otumba','2022-10-18 23:21:41'),(5397,328,'Otzoloapan','2022-10-18 23:21:41'),(5398,328,'Otzolotepec','2022-10-18 23:21:41'),(5399,328,'Ozumba','2022-10-18 23:21:41'),(5400,328,'Papalotla','2022-10-18 23:21:41'),(5401,328,'La Paz','2022-10-18 23:21:41'),(5402,328,'PolotitlÃ¡n','2022-10-18 23:21:41'),(5403,328,'RayÃ³n','2022-10-18 23:21:41'),(5404,328,'San Antonio la Isla','2022-10-18 23:21:41'),(5405,328,'San Felipe del Progreso','2022-10-18 23:21:41'),(5406,328,'San MartÃ­n de las PirÃ¡mides','2022-10-18 23:21:41'),(5407,328,'San Mateo Atenco','2022-10-18 23:21:41'),(5408,328,'San SimÃ³n de Guerrero','2022-10-18 23:21:41'),(5409,328,'Santo TomÃ¡s','2022-10-18 23:21:41'),(5410,328,'Soyaniquilpan de JuÃ¡rez','2022-10-18 23:21:41'),(5411,328,'Sultepec','2022-10-18 23:21:41'),(5412,328,'TecÃ¡mac','2022-10-18 23:21:41'),(5413,328,'Tejupilco','2022-10-18 23:21:41'),(5414,328,'Temamatla','2022-10-18 23:21:41'),(5415,328,'Temascalapa','2022-10-18 23:21:41'),(5416,328,'Temascalcingo','2022-10-18 23:21:41'),(5417,328,'Temascaltepec','2022-10-18 23:21:41'),(5418,328,'Temoaya','2022-10-18 23:21:41'),(5419,328,'Tenancingo','2022-10-18 23:21:41'),(5420,328,'Tenango del Aire','2022-10-18 23:21:41'),(5421,328,'Tenango del Valle','2022-10-18 23:21:41'),(5422,328,'Teoloyucan','2022-10-18 23:21:41'),(5423,328,'TeotihuacÃ¡n','2022-10-18 23:21:41'),(5424,328,'Tepetlaoxtoc','2022-10-18 23:21:41'),(5425,328,'Tepetlixpa','2022-10-18 23:21:41'),(5426,328,'TepotzotlÃ¡n','2022-10-18 23:21:41'),(5427,328,'Tequixquiac','2022-10-18 23:21:41'),(5428,328,'TexcaltitlÃ¡n','2022-10-18 23:21:41'),(5429,328,'Texcalyacac','2022-10-18 23:21:41'),(5430,328,'Texcoco','2022-10-18 23:21:41'),(5431,328,'Tezoyuca','2022-10-18 23:21:41'),(5432,328,'Tianguistenco','2022-10-18 23:21:41'),(5433,328,'Timilpan','2022-10-18 23:21:41'),(5434,328,'Tlalmanalco','2022-10-18 23:21:41'),(5435,328,'Tlalnepantla de Baz','2022-10-18 23:21:41'),(5436,328,'Tlatlaya','2022-10-18 23:21:41'),(5437,328,'Toluca','2022-10-18 23:21:41'),(5438,328,'Tonatico','2022-10-18 23:21:41'),(5439,328,'Tultepec','2022-10-18 23:21:41'),(5440,328,'TultitlÃ¡n','2022-10-18 23:21:41'),(5441,328,'Valle de Bravo','2022-10-18 23:21:41'),(5442,328,'Villa de Allende','2022-10-18 23:21:41'),(5443,328,'Villa del CarbÃ³n','2022-10-18 23:21:41'),(5444,328,'Villa Guerrero','2022-10-18 23:21:41'),(5445,328,'Villa Victoria','2022-10-18 23:21:41'),(5446,328,'XonacatlÃ¡n','2022-10-18 23:21:41'),(5447,328,'Zacazonapan','2022-10-18 23:21:41'),(5448,328,'Zacualpan, State of Mexico','2022-10-18 23:21:41'),(5449,328,'Zinacantepec','2022-10-18 23:21:41'),(5450,328,'ZumpahuacÃ¡n','2022-10-18 23:21:41'),(5451,328,'Zumpango','2022-10-18 23:21:41'),(5452,328,'CuautitlÃ¡n Izcalli','2022-10-18 23:21:41'),(5453,328,'Valle de Chalco Solidaridad','2022-10-18 23:21:41'),(5454,328,'Luvianos','2022-10-18 23:21:41'),(5455,328,'San JosÃ© del RincÃ³n','2022-10-18 23:21:41'),(5456,328,'Tonanitla','2022-10-18 23:21:41'),(5457,329,'Acuitzio','2022-10-18 23:21:41'),(5458,329,'Aguililla','2022-10-18 23:21:41'),(5459,329,'Ãlvaro ObregÃ³n','2022-10-18 23:21:41'),(5460,329,'Angamacutiro','2022-10-18 23:21:41'),(5461,329,'Angangueo','2022-10-18 23:21:41'),(5462,329,'ApatzingÃ¡n','2022-10-18 23:21:41'),(5463,329,'Aporo','2022-10-18 23:21:41'),(5464,329,'Aquila','2022-10-18 23:21:41'),(5465,329,'Ario','2022-10-18 23:21:41'),(5466,329,'Arteaga','2022-10-18 23:21:41'),(5467,329,'BriseÃ±as','2022-10-18 23:21:41'),(5468,329,'Buenavista','2022-10-18 23:21:41'),(5469,329,'Caracuaro','2022-10-18 23:21:41'),(5470,329,'Charapan','2022-10-18 23:21:41'),(5471,329,'Charo','2022-10-18 23:21:41'),(5472,329,'Chavinda','2022-10-18 23:21:41'),(5473,329,'CherÃ¡n','2022-10-18 23:21:41'),(5474,329,'Chilchota','2022-10-18 23:21:41'),(5475,329,'Chinicuila','2022-10-18 23:21:41'),(5476,329,'ChucÃ¡ndiro','2022-10-18 23:21:41'),(5477,329,'Churintzio','2022-10-18 23:21:41'),(5478,329,'Churumuco','2022-10-18 23:21:41'),(5479,329,'Coahuayana','2022-10-18 23:21:41'),(5480,329,'CoalcomÃ¡n de VÃ¡zquez Pallares','2022-10-18 23:21:41'),(5481,329,'Coeneo','2022-10-18 23:21:41'),(5482,329,'CojumatlÃ¡n de RÃ©gules','2022-10-18 23:21:41'),(5483,329,'Contepec','2022-10-18 23:21:41'),(5484,329,'CopÃ¡ndaro','2022-10-18 23:21:41'),(5485,329,'Cotija','2022-10-18 23:21:41'),(5486,329,'Cuitzeo','2022-10-18 23:21:41'),(5487,329,'Ecuandureo','2022-10-18 23:21:41'),(5488,329,'EpitÃ¡cio Huerta','2022-10-18 23:21:41'),(5489,329,'Erongaricuaro','2022-10-18 23:21:41'),(5490,329,'Gabriel Zamora','2022-10-18 23:21:41'),(5491,329,'Hidalgo','2022-10-18 23:21:41'),(5492,329,'La Huacana','2022-10-18 23:21:41'),(5493,329,'Huandacareo','2022-10-18 23:21:41'),(5494,329,'Huaniqueo','2022-10-18 23:21:41'),(5495,329,'Huetamo','2022-10-18 23:21:41'),(5496,329,'Huiramba','2022-10-18 23:21:41'),(5497,329,'Indaparapeo','2022-10-18 23:21:41'),(5498,329,'Irimbo','2022-10-18 23:21:41'),(5499,329,'IxtlÃ¡n','2022-10-18 23:21:41'),(5500,329,'Jacona','2022-10-18 23:21:41'),(5501,329,'JimÃ©nez','2022-10-18 23:21:41'),(5502,329,'Jiquilpan','2022-10-18 23:21:41'),(5503,329,'JosÃ© Sixto Verduzco','2022-10-18 23:21:41'),(5504,329,'JuÃ¡rez','2022-10-18 23:21:41'),(5505,329,'Jungapeo','2022-10-18 23:21:41'),(5506,329,'Lagunillas','2022-10-18 23:21:41'),(5507,329,'La Piedad','2022-10-18 23:21:41'),(5508,329,'LÃ¡zaro CÃ¡rdenas','2022-10-18 23:21:41'),(5509,329,'Los Reyes','2022-10-18 23:21:41'),(5510,329,'Madero','2022-10-18 23:21:41'),(5511,329,'MaravatÃ­o','2022-10-18 23:21:41'),(5512,329,'Marcos','2022-10-18 23:21:41'),(5513,329,'Morelia','2022-10-18 23:21:41'),(5514,329,'Morelos','2022-10-18 23:21:41'),(5515,329,'MÃºgica','2022-10-18 23:21:41'),(5516,329,'NahuatzÃ©n','2022-10-18 23:21:41'),(5517,329,'NocupÃ©taro','2022-10-18 23:21:41'),(5518,329,'Nuevo Parangaricutiro','2022-10-18 23:21:41'),(5519,329,'Nuevo Urecho','2022-10-18 23:21:41'),(5520,329,'NumarÃ¡n','2022-10-18 23:21:41'),(5521,329,'Ocampo','2022-10-18 23:21:41'),(5522,329,'PajacuarÃ¡n','2022-10-18 23:21:41'),(5523,329,'Panindicuaro','2022-10-18 23:21:41'),(5524,329,'Paracho','2022-10-18 23:21:41'),(5525,329,'ParÃ¡cuaro','2022-10-18 23:21:41'),(5526,329,'PÃ¡tzcuaro','2022-10-18 23:21:41'),(5527,329,'Penjamillo','2022-10-18 23:21:41'),(5528,329,'PeribÃ¡n','2022-10-18 23:21:41'),(5529,329,'PurÃ©pero','2022-10-18 23:21:41'),(5530,329,'PuruÃ¡ndiro','2022-10-18 23:21:41'),(5531,329,'QuerÃ©ndaro','2022-10-18 23:21:41'),(5532,329,'Quiroga','2022-10-18 23:21:41'),(5533,329,'Sahuayo','2022-10-18 23:21:41'),(5534,329,'Salvador Escalante','2022-10-18 23:21:41'),(5535,329,'San Lucas','2022-10-18 23:21:41'),(5536,329,'Santa Ana Maya','2022-10-18 23:21:41'),(5537,329,'SenguÃ­o','2022-10-18 23:21:41'),(5538,329,'Susupuato','2022-10-18 23:21:41'),(5539,329,'TacÃ¡mbaro','2022-10-18 23:21:41'),(5540,329,'TancÃ­taro','2022-10-18 23:21:41'),(5541,329,'Tangamandapio','2022-10-18 23:21:41'),(5542,329,'TangancÃ­cuaro','2022-10-18 23:21:41'),(5543,329,'Tanhuato','2022-10-18 23:21:41'),(5544,329,'Taretan','2022-10-18 23:21:41'),(5545,329,'TarÃ­mbaro','2022-10-18 23:21:41'),(5546,329,'Tepalcatepec','2022-10-18 23:21:41'),(5547,329,'Tingambato','2022-10-18 23:21:41'),(5548,329,'TingÃ¼indÃ­n','2022-10-18 23:21:41'),(5549,329,'Tiquicheo de Nicolas Romero','2022-10-18 23:21:41'),(5550,329,'Tlalpujahua','2022-10-18 23:21:41'),(5551,329,'Tlazazalca','2022-10-18 23:21:41'),(5552,329,'Tocumbo','2022-10-18 23:21:41'),(5553,329,'TumbiscatÃ­o','2022-10-18 23:21:41'),(5554,329,'Turicato','2022-10-18 23:21:41'),(5555,329,'Tuxpan','2022-10-18 23:21:41'),(5556,329,'Tuzantla','2022-10-18 23:21:41'),(5557,329,'Tzintzuntzan','2022-10-18 23:21:41'),(5558,329,'TzitzÃ­o','2022-10-18 23:21:41'),(5559,329,'Uruapan','2022-10-18 23:21:41'),(5560,329,'Venustiano Carranza','2022-10-18 23:21:41'),(5561,329,'Villamar','2022-10-18 23:21:41'),(5562,329,'Vista Hermosa','2022-10-18 23:21:41'),(5563,329,'YurÃ©cuaro','2022-10-18 23:21:41'),(5564,329,'ZacapÃº','2022-10-18 23:21:41'),(5565,329,'Zamora','2022-10-18 23:21:41'),(5566,329,'ZinÃ¡paro','2022-10-18 23:21:41'),(5567,329,'ZinapÃ©cuaro','2022-10-18 23:21:41'),(5568,329,'Ziracuaretiro','2022-10-18 23:21:41'),(5569,329,'ZitÃ¡cuaro','2022-10-18 23:21:41'),(5570,208,'Abasolo','2022-10-18 23:21:41'),(5571,208,'Agualeguas','2022-10-18 23:21:41'),(5572,208,'Allende','2022-10-18 23:21:41'),(5573,208,'AnÃ¡huac','2022-10-18 23:21:41'),(5574,208,'Apodaca','2022-10-18 23:21:41'),(5575,208,'Aramberri','2022-10-18 23:21:41'),(5576,208,'Bustamante','2022-10-18 23:21:41'),(5577,208,'Cadereyta JimÃ©nez','2022-10-18 23:21:41'),(5578,208,'El Carmen','2022-10-18 23:21:41'),(5579,208,'Cerralvo','2022-10-18 23:21:41'),(5580,208,'China','2022-10-18 23:21:41'),(5581,208,'CiÃ©nega de Flores','2022-10-18 23:21:41'),(5582,208,'Doctor Arroyo','2022-10-18 23:21:41'),(5583,208,'Doctor Coss','2022-10-18 23:21:41'),(5584,208,'Doctor GonzÃ¡lez','2022-10-18 23:21:41'),(5585,208,'Galeana','2022-10-18 23:21:41'),(5586,208,'GarcÃ­a','2022-10-18 23:21:41'),(5587,208,'General Bravo','2022-10-18 23:21:41'),(5588,208,'General Escobedo','2022-10-18 23:21:41'),(5589,208,'General TerÃ¡n','2022-10-18 23:21:41'),(5590,208,'General TreviÃ±o','2022-10-18 23:21:41'),(5591,208,'General Zaragoza','2022-10-18 23:21:41'),(5592,208,'General Zuazua','2022-10-18 23:21:41'),(5593,208,'Guadalupe','2022-10-18 23:21:41'),(5594,208,'Hidalgo','2022-10-18 23:21:41'),(5595,208,'Higueras','2022-10-18 23:21:41'),(5596,208,'Hualahuises','2022-10-18 23:21:41'),(5597,208,'Iturbide','2022-10-18 23:21:41'),(5598,208,'JuÃ¡rez','2022-10-18 23:21:41'),(5599,208,'Lampazos de Naranjo','2022-10-18 23:21:41'),(5600,208,'Linares','2022-10-18 23:21:41'),(5601,208,'Los Aldama','2022-10-18 23:21:41'),(5602,208,'Los Herrera','2022-10-18 23:21:41'),(5603,208,'Los Ramones','2022-10-18 23:21:41'),(5604,208,'MarÃ­n','2022-10-18 23:21:41'),(5605,208,'Melchor Ocampo','2022-10-18 23:21:41'),(5606,208,'Mier y Noriega','2022-10-18 23:21:41'),(5607,208,'Mina','2022-10-18 23:21:41'),(5608,208,'Montemorelos','2022-10-18 23:21:41'),(5609,208,'Monterrey','2022-10-18 23:21:41'),(5610,208,'ParÃ¡s','2022-10-18 23:21:41'),(5611,208,'PesquerÃ­a','2022-10-18 23:21:41'),(5612,208,'Rayones','2022-10-18 23:21:41'),(5613,208,'Sabinas Hidalgo','2022-10-18 23:21:41'),(5614,208,'Salinas Victoria','2022-10-18 23:21:41'),(5615,208,'San NicolÃ¡s de los Garza','2022-10-18 23:21:41'),(5616,208,'San Pedro Garza GarcÃ­a','2022-10-18 23:21:41'),(5617,208,'Santa Catarina','2022-10-18 23:21:41'),(5618,208,'Santiago','2022-10-18 23:21:41'),(5619,208,'Vallecillo','2022-10-18 23:21:41'),(5620,208,'Villaldama','2022-10-18 23:21:41'),(5621,357,'Amealco de Bonfil','2022-10-18 23:21:41'),(5622,357,'Pinal de Amoles','2022-10-18 23:21:41'),(5623,357,'Arroyo Seco','2022-10-18 23:21:41'),(5624,357,'Cadereyta de Montes','2022-10-18 23:21:41'),(5625,357,'ColÃ³n','2022-10-18 23:21:41'),(5626,357,'Corregidora','2022-10-18 23:21:41'),(5627,357,'Ezequiel Montes','2022-10-18 23:21:41'),(5628,357,'Huimilpan','2022-10-18 23:21:41'),(5629,357,'Jalpan de Serra','2022-10-18 23:21:41'),(5630,357,'Landa de Matamoros','2022-10-18 23:21:41'),(5631,357,'El MarquÃ©s','2022-10-18 23:21:41'),(5632,357,'Pedro Escobedo','2022-10-18 23:21:41'),(5633,357,'PeÃ±amiller','2022-10-18 23:21:41'),(5634,357,'QuerÃ©taro','2022-10-18 23:21:41'),(5635,357,'San JoaquÃ­n','2022-10-18 23:21:41'),(5636,357,'San Juan del RÃ­o','2022-10-18 23:21:41'),(5637,357,'Tequisquiapan','2022-10-18 23:21:41'),(5638,357,'TolimÃ¡n','2022-10-18 23:21:41'),(5639,199,'Ahualulco','2022-10-18 23:21:41'),(5640,199,'Alaquines','2022-10-18 23:21:41'),(5641,199,'AquismÃ³n','2022-10-18 23:21:41'),(5642,199,'Armadillo de los Infante','2022-10-18 23:21:41'),(5643,199,'Axtla de Terrazas','2022-10-18 23:21:41'),(5644,199,'CÃ¡rdenas','2022-10-18 23:21:41'),(5645,199,'Catorce','2022-10-18 23:21:41'),(5646,199,'Cedral','2022-10-18 23:21:41'),(5647,199,'Cerritos','2022-10-18 23:21:41'),(5648,199,'Cerro de San Pedro','2022-10-18 23:21:41'),(5649,199,'Charcas','2022-10-18 23:21:41'),(5650,199,'Ciudad del MaÃ­z','2022-10-18 23:21:41'),(5651,199,'Ciudad FernÃ¡ndez','2022-10-18 23:21:41'),(5652,199,'Ciudad Valles','2022-10-18 23:21:41'),(5653,199,'CoxcatlÃ¡n','2022-10-18 23:21:41'),(5654,199,'Ebano','2022-10-18 23:21:41'),(5655,199,'El Naranjo','2022-10-18 23:21:41'),(5656,199,'Guadalcazar','2022-10-18 23:21:41'),(5657,199,'HuehuetlÃ¡n','2022-10-18 23:21:41'),(5658,199,'Lagunillas','2022-10-18 23:21:41'),(5659,199,'Matehuala','2022-10-18 23:21:41'),(5660,199,'Matlapa','2022-10-18 23:21:41'),(5661,199,'Mexquitic de Carmona','2022-10-18 23:21:41'),(5662,199,'Moctezuma','2022-10-18 23:21:41'),(5663,199,'RayÃ³n','2022-10-18 23:21:41'),(5664,199,'Rioverde','2022-10-18 23:21:41'),(5665,199,'Salinas','2022-10-18 23:21:41'),(5666,199,'San Antonio','2022-10-18 23:21:41'),(5667,199,'San Ciro de Acosta','2022-10-18 23:21:41'),(5668,199,'San Luis PotosÃ­','2022-10-18 23:21:41'),(5669,199,'San MartÃ­n Chalchicuautla','2022-10-18 23:21:41'),(5670,199,'San NicolÃ¡s Tolentino','2022-10-18 23:21:41'),(5671,199,'Santa Catarina','2022-10-18 23:21:41'),(5672,199,'Santa MarÃ­a del RÃ­o','2022-10-18 23:21:41'),(5673,199,'Santo Domingo','2022-10-18 23:21:41'),(5674,199,'San Vicente Tancuayalab','2022-10-18 23:21:41'),(5675,199,'Soledad de Graciano SÃ¡nchez','2022-10-18 23:21:41'),(5676,199,'Tamasopo','2022-10-18 23:21:41'),(5677,199,'Tamazunchale','2022-10-18 23:21:41'),(5678,199,'Tampacan','2022-10-18 23:21:41'),(5679,199,'TampamolÃ³n Corona','2022-10-18 23:21:41'),(5680,199,'TamuÃ­n','2022-10-18 23:21:41'),(5681,199,'Tancanhuitz de Santos','2022-10-18 23:21:41'),(5682,199,'TanlajÃ¡s','2022-10-18 23:21:41'),(5683,199,'TanquiÃ¡n de Escobedo','2022-10-18 23:21:41'),(5684,199,'Tierra Nueva','2022-10-18 23:21:41'),(5685,199,'Vanegas','2022-10-18 23:21:41'),(5686,199,'Venado','2022-10-18 23:21:41'),(5687,199,'Villa de Arista','2022-10-18 23:21:41'),(5688,199,'Villa de Arriaga','2022-10-18 23:21:41'),(5689,199,'Villa de Guadalupe','2022-10-18 23:21:41'),(5690,199,'Villa de La Paz','2022-10-18 23:21:41'),(5691,199,'Villa de Ramos','2022-10-18 23:21:41'),(5692,199,'Villa de Reyes','2022-10-18 23:21:41'),(5693,199,'Villa de Hidalgo','2022-10-18 23:21:41'),(5694,199,'Villa JuÃ¡rez','2022-10-18 23:21:41'),(5695,199,'Xilitla','2022-10-18 23:21:41'),(5696,199,'Zaragoza','2022-10-18 23:21:41'),(5697,400,'AbalÃ¡','2022-10-18 23:21:41'),(5698,400,'Acanceh','2022-10-18 23:21:41'),(5699,400,'Akil','2022-10-18 23:21:41'),(5700,400,'Baca','2022-10-18 23:21:41'),(5701,400,'BokobÃ¡','2022-10-18 23:21:41'),(5702,400,'Buctzotz','2022-10-18 23:21:41'),(5703,400,'CacalchÃ©n','2022-10-18 23:21:41'),(5704,400,'Calotmul','2022-10-18 23:21:41'),(5705,400,'Cansahcab','2022-10-18 23:21:41'),(5706,400,'Cantamayec','2022-10-18 23:21:41'),(5707,400,'CelestÃºn','2022-10-18 23:21:41'),(5708,400,'Cenotillo','2022-10-18 23:21:41'),(5709,400,'Conkal','2022-10-18 23:21:41'),(5710,400,'Cuncunul','2022-10-18 23:21:41'),(5711,400,'CuzamÃ¡','2022-10-18 23:21:41'),(5712,400,'ChacsinkÃ­n','2022-10-18 23:21:41'),(5713,400,'Chankom','2022-10-18 23:21:41'),(5714,400,'Chapab','2022-10-18 23:21:41'),(5715,400,'Chemax','2022-10-18 23:21:41'),(5716,400,'Chicxulub Pueblo','2022-10-18 23:21:41'),(5717,400,'ChichimilÃ¡','2022-10-18 23:21:41'),(5718,400,'Chikindzonot','2022-10-18 23:21:41'),(5719,400,'ChocholÃ¡','2022-10-18 23:21:41'),(5720,400,'Chumayel','2022-10-18 23:21:41'),(5721,400,'Dzan','2022-10-18 23:21:41'),(5722,400,'Dzemul','2022-10-18 23:21:41'),(5723,400,'DzidzantÃºn','2022-10-18 23:21:41'),(5724,400,'Dzilam de Bravo','2022-10-18 23:21:41'),(5725,400,'Dzilam GonzÃ¡lez','2022-10-18 23:21:41'),(5726,400,'DzitÃ¡s','2022-10-18 23:21:41'),(5727,400,'Dzoncauich','2022-10-18 23:21:41'),(5728,400,'Espita','2022-10-18 23:21:41'),(5729,400,'HalachÃ³','2022-10-18 23:21:41'),(5730,400,'HocabÃ¡','2022-10-18 23:21:41'),(5731,400,'HoctÃºn','2022-10-18 23:21:41'),(5732,400,'HomÃºn','2022-10-18 23:21:41'),(5733,400,'HuhÃ­','2022-10-18 23:21:41'),(5734,400,'HunucmÃ¡','2022-10-18 23:21:41'),(5735,400,'Ixil','2022-10-18 23:21:41'),(5736,400,'Izamal','2022-10-18 23:21:41'),(5737,400,'KanasÃ­n','2022-10-18 23:21:41'),(5738,400,'Kantunil','2022-10-18 23:21:41'),(5739,400,'Kaua','2022-10-18 23:21:41'),(5740,400,'Kinchil','2022-10-18 23:21:41'),(5741,400,'KopomÃ¡','2022-10-18 23:21:41'),(5742,400,'Mama','2022-10-18 23:21:41'),(5743,400,'ManÃ­','2022-10-18 23:21:41'),(5744,400,'MaxcanÃº','2022-10-18 23:21:41'),(5745,400,'MayapÃ¡n','2022-10-18 23:21:41'),(5746,400,'MÃ©rida','2022-10-18 23:21:41'),(5747,400,'MocochÃ¡','2022-10-18 23:21:41'),(5748,400,'Motul','2022-10-18 23:21:41'),(5749,400,'Muna','2022-10-18 23:21:41'),(5750,400,'Muxupip','2022-10-18 23:21:41'),(5751,400,'OpichÃ©n','2022-10-18 23:21:41'),(5752,400,'Oxkutzcab','2022-10-18 23:21:41'),(5753,400,'PanabÃ¡','2022-10-18 23:21:41'),(5754,400,'Peto','2022-10-18 23:21:41'),(5755,400,'Progreso','2022-10-18 23:21:41'),(5756,400,'Quintana Roo','2022-10-18 23:21:41'),(5757,400,'RÃ­o Lagartos','2022-10-18 23:21:41'),(5758,400,'Sacalum','2022-10-18 23:21:41'),(5759,400,'Samahil','2022-10-18 23:21:41'),(5760,400,'Sanahcat','2022-10-18 23:21:41'),(5761,400,'San Felipe','2022-10-18 23:21:41'),(5762,400,'Santa Elena','2022-10-18 23:21:41'),(5763,400,'SeyÃ©','2022-10-18 23:21:41'),(5764,400,'SinanchÃ©','2022-10-18 23:21:41'),(5765,400,'Sotuta','2022-10-18 23:21:41'),(5766,400,'SucilÃ¡','2022-10-18 23:21:41'),(5767,400,'Sudzal','2022-10-18 23:21:41'),(5768,400,'Suma','2022-10-18 23:21:41'),(5769,400,'TahdziÃº','2022-10-18 23:21:41'),(5770,400,'Tahmek','2022-10-18 23:21:41'),(5771,400,'Teabo','2022-10-18 23:21:41'),(5772,400,'Tecoh','2022-10-18 23:21:41'),(5773,400,'Tekal de Venegas','2022-10-18 23:21:41'),(5774,400,'TekantÃ³','2022-10-18 23:21:41'),(5775,400,'Tekax','2022-10-18 23:21:41'),(5776,400,'Tekit','2022-10-18 23:21:41'),(5777,400,'Tekom','2022-10-18 23:21:41'),(5778,400,'Telchac Pueblo','2022-10-18 23:21:41'),(5779,400,'Telchac Puerto','2022-10-18 23:21:41'),(5780,400,'Temax','2022-10-18 23:21:41'),(5781,400,'TemozÃ³n','2022-10-18 23:21:41'),(5782,400,'TepakÃ¡n','2022-10-18 23:21:41'),(5783,400,'Tetiz','2022-10-18 23:21:41'),(5784,400,'Teya','2022-10-18 23:21:41'),(5785,400,'Ticul','2022-10-18 23:21:41'),(5786,400,'Timucuy','2022-10-18 23:21:41'),(5787,400,'TinÃºm','2022-10-18 23:21:41'),(5788,400,'Tixcacalcupul','2022-10-18 23:21:41'),(5789,400,'Tixkokob','2022-10-18 23:21:41'),(5790,400,'TixmÃ©huac','2022-10-18 23:21:41'),(5791,400,'TixpÃ©hual','2022-10-18 23:21:41'),(5792,400,'TizimÃ­n','2022-10-18 23:21:41'),(5793,400,'TunkÃ¡s','2022-10-18 23:21:41'),(5794,400,'Tzucacab','2022-10-18 23:21:41'),(5795,400,'Uayma','2022-10-18 23:21:41'),(5796,400,'UcÃº','2022-10-18 23:21:41'),(5797,400,'UmÃ¡n','2022-10-18 23:21:41'),(5798,400,'Valladolid','2022-10-18 23:21:41'),(5799,400,'Xocchel','2022-10-18 23:21:41'),(5800,400,'YaxcabÃ¡','2022-10-18 23:21:41'),(5801,400,'Yaxkukul','2022-10-18 23:21:41'),(5802,400,'YobaÃ­n','2022-10-18 23:21:41'),(5803,140,'Miller','2022-10-18 23:21:41');
/*!40000 ALTER TABLE `lkupcounty` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lkupmunicipality`
--

DROP TABLE IF EXISTS `lkupmunicipality`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lkupmunicipality` (
  `municipalityId` int(11) NOT NULL AUTO_INCREMENT,
  `stateId` int(11) NOT NULL,
  `municipalityName` varchar(100) NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`municipalityId`),
  UNIQUE KEY `unique_municipality` (`stateId`,`municipalityName`),
  KEY `fk_stateprovince` (`stateId`),
  KEY `index_municipalityname` (`municipalityName`),
  CONSTRAINT `lkupmunicipality_ibfk_1` FOREIGN KEY (`stateId`) REFERENCES `lkupstateprovince` (`stateId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lkupmunicipality`
--

LOCK TABLES `lkupmunicipality` WRITE;
/*!40000 ALTER TABLE `lkupmunicipality` DISABLE KEYS */;
/*!40000 ALTER TABLE `lkupmunicipality` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lkupstateprovince`
--

DROP TABLE IF EXISTS `lkupstateprovince`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lkupstateprovince` (
  `stateId` int(11) NOT NULL AUTO_INCREMENT,
  `countryId` int(11) NOT NULL,
  `stateName` varchar(100) NOT NULL,
  `abbrev` varchar(3) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`stateId`),
  UNIQUE KEY `state_index` (`stateName`,`countryId`),
  KEY `fk_country` (`countryId`),
  KEY `index_statename` (`stateName`),
  KEY `Index_lkupstate_abbr` (`abbrev`),
  CONSTRAINT `fk_country` FOREIGN KEY (`countryId`) REFERENCES `lkupcountry` (`countryId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=976 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lkupstateprovince`
--

LOCK TABLES `lkupstateprovince` WRITE;
/*!40000 ALTER TABLE `lkupstateprovince` DISABLE KEYS */;
INSERT INTO `lkupstateprovince` VALUES (1,256,'Alaska','AK','2022-10-18 23:21:41'),(2,256,'Alabama','AL','2022-10-18 23:21:41'),(3,256,'American Samoa','AS','2022-10-18 23:21:41'),(4,256,'Arizona','AZ','2022-10-18 23:21:41'),(5,256,'Arkansas','AR','2022-10-18 23:21:41'),(6,256,'California','CA','2022-10-18 23:21:41'),(7,256,'Colorado','CO','2022-10-18 23:21:41'),(8,256,'Connecticut','CT','2022-10-18 23:21:41'),(9,256,'Delaware','DE','2022-10-18 23:21:41'),(10,256,'District of Columbia','DC','2022-10-18 23:21:41'),(11,256,'Federated States of Micronesia','FM','2022-10-18 23:21:41'),(12,256,'Florida','FL','2022-10-18 23:21:41'),(13,256,'Georgia','GA','2022-10-18 23:21:41'),(14,256,'Guam','GU','2022-10-18 23:21:41'),(15,256,'Hawaii','HI','2022-10-18 23:21:41'),(16,256,'Idaho','ID','2022-10-18 23:21:41'),(17,256,'Illinois','IL','2022-10-18 23:21:41'),(18,256,'Indiana','IN','2022-10-18 23:21:41'),(19,256,'Iowa','IA','2022-10-18 23:21:41'),(20,256,'Kansas','KS','2022-10-18 23:21:41'),(21,256,'Kentucky','KY','2022-10-18 23:21:41'),(22,256,'Louisiana','LA','2022-10-18 23:21:41'),(23,256,'Maine','ME','2022-10-18 23:21:41'),(24,256,'Marshall Islands','MH','2022-10-18 23:21:41'),(25,256,'Maryland','MD','2022-10-18 23:21:41'),(26,256,'Massachusetts','MA','2022-10-18 23:21:41'),(27,256,'Michigan','MI','2022-10-18 23:21:41'),(28,256,'Minnesota','MN','2022-10-18 23:21:41'),(29,256,'Mississippi','MS','2022-10-18 23:21:41'),(30,256,'Missouri','MO','2022-10-18 23:21:41'),(31,256,'Montana','MT','2022-10-18 23:21:41'),(32,256,'Nebraska','NE','2022-10-18 23:21:41'),(33,256,'Nevada','NV','2022-10-18 23:21:41'),(34,256,'New Hampshire','NH','2022-10-18 23:21:41'),(35,256,'New Jersey','NJ','2022-10-18 23:21:41'),(36,256,'New Mexico','NM','2022-10-18 23:21:41'),(37,256,'New York','NY','2022-10-18 23:21:41'),(38,256,'North Carolina','NC','2022-10-18 23:21:41'),(39,256,'North Dakota','ND','2022-10-18 23:21:41'),(40,256,'Northern Mariana Islands','MP','2022-10-18 23:21:41'),(41,256,'Ohio','OH','2022-10-18 23:21:41'),(42,256,'Oklahoma','OK','2022-10-18 23:21:41'),(43,256,'Oregon','OR','2022-10-18 23:21:41'),(44,256,'Palau','PW','2022-10-18 23:21:41'),(45,256,'Pennsylvania','PA','2022-10-18 23:21:41'),(46,256,'Puerto Rico','PR','2022-10-18 23:21:41'),(47,256,'Rhode Island','RI','2022-10-18 23:21:41'),(48,256,'South Carolina','SC','2022-10-18 23:21:41'),(49,256,'South Dakota','SD','2022-10-18 23:21:41'),(50,256,'Tennessee','TN','2022-10-18 23:21:41'),(51,256,'Texas','TX','2022-10-18 23:21:41'),(52,256,'Utah','UT','2022-10-18 23:21:41'),(53,256,'Vermont','VT','2022-10-18 23:21:41'),(54,256,'Virgin Islands','VI','2022-10-18 23:21:41'),(55,256,'Virginia','VA','2022-10-18 23:21:41'),(56,256,'Washington','WA','2022-10-18 23:21:41'),(57,256,'West Virginia','WV','2022-10-18 23:21:41'),(58,256,'Wisconsin','WI','2022-10-18 23:21:41'),(59,256,'Wyoming','WY','2022-10-18 23:21:41'),(60,256,'Armed Forces Africa','AE','2022-10-18 23:21:41'),(61,256,'Armed Forces Americas (except Canada)','AA','2022-10-18 23:21:41'),(62,256,'Armed Forces Canada','AE','2022-10-18 23:21:41'),(63,256,'Armed Forces Europe','AE','2022-10-18 23:21:41'),(64,256,'Armed Forces Middle East','AE','2022-10-18 23:21:41'),(65,256,'Armed Forces Pacific','AP','2022-10-18 23:21:41'),(128,223,'Alaska','AK','2022-10-18 23:21:41'),(129,223,'Alabama','AL','2022-10-18 23:21:41'),(130,223,'American Samoa','AS','2022-10-18 23:21:41'),(131,223,'Arizona','AZ','2022-10-18 23:21:41'),(132,223,'Arkansas','AR','2022-10-18 23:21:41'),(133,223,'California','CA','2022-10-18 23:21:41'),(134,223,'Colorado','CO','2022-10-18 23:21:41'),(135,223,'Connecticut','CT','2022-10-18 23:21:41'),(136,223,'Delaware','DE','2022-10-18 23:21:41'),(137,223,'District of Columbia','DC','2022-10-18 23:21:41'),(138,223,'Federated States of Micronesia','FM','2022-10-18 23:21:41'),(139,223,'Florida','FL','2022-10-18 23:21:41'),(140,223,'Georgia','GA','2022-10-18 23:21:41'),(141,223,'Guam','GU','2022-10-18 23:21:41'),(142,223,'Hawaii','HI','2022-10-18 23:21:41'),(143,223,'Idaho','ID','2022-10-18 23:21:41'),(144,223,'Illinois','IL','2022-10-18 23:21:41'),(145,223,'Indiana','IN','2022-10-18 23:21:41'),(146,223,'Iowa','IA','2022-10-18 23:21:41'),(147,223,'Kansas','KS','2022-10-18 23:21:41'),(148,223,'Kentucky','KY','2022-10-18 23:21:41'),(149,223,'Louisiana','LA','2022-10-18 23:21:41'),(150,223,'Maine','ME','2022-10-18 23:21:41'),(151,223,'Marshall Islands','MH','2022-10-18 23:21:41'),(152,223,'Maryland','MD','2022-10-18 23:21:41'),(153,223,'Massachusetts','MA','2022-10-18 23:21:41'),(154,223,'Michigan','MI','2022-10-18 23:21:41'),(155,223,'Minnesota','MN','2022-10-18 23:21:41'),(156,223,'Mississippi','MS','2022-10-18 23:21:41'),(157,223,'Missouri','MO','2022-10-18 23:21:41'),(158,223,'Montana','MT','2022-10-18 23:21:41'),(159,223,'Nebraska','NE','2022-10-18 23:21:41'),(160,223,'Nevada','NV','2022-10-18 23:21:41'),(161,223,'New Hampshire','NH','2022-10-18 23:21:41'),(162,223,'New Jersey','NJ','2022-10-18 23:21:41'),(163,223,'New Mexico','NM','2022-10-18 23:21:41'),(164,223,'New York','NY','2022-10-18 23:21:41'),(165,223,'North Carolina','NC','2022-10-18 23:21:41'),(166,223,'North Dakota','ND','2022-10-18 23:21:41'),(167,223,'Northern Mariana Islands','MP','2022-10-18 23:21:41'),(168,223,'Ohio','OH','2022-10-18 23:21:41'),(169,223,'Oklahoma','OK','2022-10-18 23:21:41'),(170,223,'Oregon','OR','2022-10-18 23:21:41'),(171,223,'Palau','PW','2022-10-18 23:21:41'),(172,223,'Pennsylvania','PA','2022-10-18 23:21:41'),(173,223,'Puerto Rico','PR','2022-10-18 23:21:41'),(174,223,'Rhode Island','RI','2022-10-18 23:21:41'),(175,223,'South Carolina','SC','2022-10-18 23:21:41'),(176,223,'South Dakota','SD','2022-10-18 23:21:41'),(177,223,'Tennessee','TN','2022-10-18 23:21:41'),(178,223,'Texas','TX','2022-10-18 23:21:41'),(179,223,'Utah','UT','2022-10-18 23:21:41'),(180,223,'Vermont','VT','2022-10-18 23:21:41'),(181,223,'Virgin Islands','VI','2022-10-18 23:21:41'),(182,223,'Virginia','VA','2022-10-18 23:21:41'),(183,223,'Washington','WA','2022-10-18 23:21:41'),(184,223,'West Virginia','WV','2022-10-18 23:21:41'),(185,223,'Wisconsin','WI','2022-10-18 23:21:41'),(186,223,'Wyoming','WY','2022-10-18 23:21:41'),(187,223,'Armed Forces Africa','AE','2022-10-18 23:21:41'),(188,223,'Armed Forces Americas (except Canada)','AA','2022-10-18 23:21:41'),(189,223,'Armed Forces Canada','AE','2022-10-18 23:21:41'),(190,223,'Armed Forces Europe','AE','2022-10-18 23:21:41'),(191,223,'Armed Forces Middle East','AE','2022-10-18 23:21:41'),(192,223,'Armed Forces Pacific','AP','2022-10-18 23:21:41'),(193,150,'Quintana Roo',NULL,'2022-10-18 23:21:41'),(194,44,'Los Rios',NULL,'2022-10-18 23:21:41'),(195,44,'Los Lagos',NULL,'2022-10-18 23:21:41'),(196,44,'Araucania',NULL,'2022-10-18 23:21:41'),(197,150,'Sonora',NULL,'2022-10-18 23:21:41'),(198,150,'Baja California',NULL,'2022-10-18 23:21:41'),(199,150,'San Luis Potosi',NULL,'2022-10-18 23:21:41'),(200,150,'Sinaloa',NULL,'2022-10-18 23:21:41'),(202,36,'Manitoba',NULL,'2022-10-18 23:21:41'),(203,36,'British Columbia',NULL,'2022-10-18 23:21:41'),(204,36,'Alberta',NULL,'2022-10-18 23:21:41'),(205,150,'Coahuila',NULL,'2022-10-18 23:21:41'),(206,150,'Chihuahua',NULL,'2022-10-18 23:21:41'),(207,150,'Zacatecas',NULL,'2022-10-18 23:21:41'),(208,150,'Nuevo Leon',NULL,'2022-10-18 23:21:41'),(209,36,'Ottawa',NULL,'2022-10-18 23:21:41'),(210,36,'Ontario',NULL,'2022-10-18 23:21:41'),(211,12,'Sonora',NULL,'2022-10-18 23:21:41'),(212,228,'Aragua',NULL,'2022-10-18 23:21:41'),(214,150,'Tamaulipas',NULL,'2022-10-18 23:21:41'),(215,150,'Hidalgo',NULL,'2022-10-18 23:21:41'),(217,48,'San Jose',NULL,'2022-10-18 23:21:41'),(218,29,'Espirito Santo',NULL,'2022-10-18 23:21:41'),(219,11,'Neuquen',NULL,'2022-10-18 23:21:41'),(221,44,'Magallanes y Antartica Chilena',NULL,'2022-10-18 23:21:41'),(222,28,'Cochabamba',NULL,'2022-10-18 23:21:41'),(223,28,'La Paz',NULL,'2022-10-18 23:21:41'),(225,167,'Cajamarca',NULL,'2022-10-18 23:21:41'),(228,29,'Rio Grande do Sul',NULL,'2022-10-18 23:21:41'),(229,256,'Flagstaff',NULL,'2022-10-18 23:21:41'),(230,29,'Acre',NULL,'2022-10-18 23:21:41'),(231,150,'Aguascalientes',NULL,'2022-10-18 23:21:41'),(232,44,'Aisen',NULL,'2022-10-18 23:21:41'),(233,29,'Alagoas',NULL,'2022-10-18 23:21:41'),(234,48,'Alajuela',NULL,'2022-10-18 23:21:41'),(235,29,'Amapa',NULL,'2022-10-18 23:21:41'),(236,29,'Amazonas',NULL,'2022-10-18 23:21:41'),(237,47,'Amazonas',NULL,'2022-10-18 23:21:41'),(238,167,'Amazonas',NULL,'2022-10-18 23:21:41'),(239,228,'Amazonas',NULL,'2022-10-18 23:21:41'),(240,167,'Ancash',NULL,'2022-10-18 23:21:41'),(241,47,'Antioquia',NULL,'2022-10-18 23:21:41'),(242,44,'Antofagasta',NULL,'2022-10-18 23:21:41'),(243,228,'Anzoategui',NULL,'2022-10-18 23:21:41'),(244,228,'Apure',NULL,'2022-10-18 23:21:41'),(245,167,'Apurimac',NULL,'2022-10-18 23:21:41'),(246,47,'Arauca',NULL,'2022-10-18 23:21:41'),(247,167,'Arequipa',NULL,'2022-10-18 23:21:41'),(248,44,'Arica y Parinacota',NULL,'2022-10-18 23:21:41'),(249,44,'Atacama',NULL,'2022-10-18 23:21:41'),(250,47,'Atlantico',NULL,'2022-10-18 23:21:41'),(251,167,'Ayacucho',NULL,'2022-10-18 23:21:41'),(252,29,'Bahia',NULL,'2022-10-18 23:21:41'),(253,150,'Baja California Sur',NULL,'2022-10-18 23:21:41'),(254,228,'Barinas',NULL,'2022-10-18 23:21:41'),(255,28,'Beni',NULL,'2022-10-18 23:21:41'),(256,44,'Bio Bio',NULL,'2022-10-18 23:21:41'),(257,47,'Bolivar',NULL,'2022-10-18 23:21:41'),(258,228,'Bolivar',NULL,'2022-10-18 23:21:41'),(259,47,'Boyaca',NULL,'2022-10-18 23:21:41'),(260,11,'Buenos Aires',NULL,'2022-10-18 23:21:41'),(261,47,'Caldas',NULL,'2022-10-18 23:21:41'),(262,167,'Callao',NULL,'2022-10-18 23:21:41'),(263,150,'Campeche',NULL,'2022-10-18 23:21:41'),(264,47,'Caqueta',NULL,'2022-10-18 23:21:41'),(265,228,'Carabobo',NULL,'2022-10-18 23:21:41'),(266,48,'Cartago',NULL,'2022-10-18 23:21:41'),(267,47,'Casanare',NULL,'2022-10-18 23:21:41'),(268,11,'Catamarca',NULL,'2022-10-18 23:21:41'),(269,47,'Cauca',NULL,'2022-10-18 23:21:41'),(270,29,'Ceara',NULL,'2022-10-18 23:21:41'),(271,47,'Cesar',NULL,'2022-10-18 23:21:41'),(272,11,'Chaco',NULL,'2022-10-18 23:21:41'),(273,150,'Chiapas',NULL,'2022-10-18 23:21:41'),(274,47,'Choco',NULL,'2022-10-18 23:21:41'),(275,11,'Chubut',NULL,'2022-10-18 23:21:41'),(276,28,'Chuquisaca',NULL,'2022-10-18 23:21:41'),(277,228,'Cojedes',NULL,'2022-10-18 23:21:41'),(278,150,'Colima',NULL,'2022-10-18 23:21:41'),(279,44,'Coquimbo',NULL,'2022-10-18 23:21:41'),(280,11,'Cordoba',NULL,'2022-10-18 23:21:41'),(281,47,'Cordoba',NULL,'2022-10-18 23:21:41'),(282,11,'Corrientes',NULL,'2022-10-18 23:21:41'),(283,47,'Cundinamarca',NULL,'2022-10-18 23:21:41'),(284,167,'Cuzco',NULL,'2022-10-18 23:21:41'),(285,228,'Delta Amacuro',NULL,'2022-10-18 23:21:41'),(286,47,'Distrito Capital',NULL,'2022-10-18 23:21:41'),(287,11,'Distrito Federal',NULL,'2022-10-18 23:21:41'),(288,29,'Distrito Federal',NULL,'2022-10-18 23:21:41'),(289,150,'Distrito Federal',NULL,'2022-10-18 23:21:41'),(290,150,'Durango',NULL,'2022-10-18 23:21:41'),(291,11,'Entre Rios',NULL,'2022-10-18 23:21:41'),(292,228,'Falcon',NULL,'2022-10-18 23:21:41'),(293,11,'Formosa',NULL,'2022-10-18 23:21:41'),(294,29,'Goias',NULL,'2022-10-18 23:21:41'),(295,47,'Guainia',NULL,'2022-10-18 23:21:41'),(296,48,'Guanacaste',NULL,'2022-10-18 23:21:41'),(297,150,'Guanajuato',NULL,'2022-10-18 23:21:41'),(298,228,'Guarico',NULL,'2022-10-18 23:21:41'),(299,47,'Guaviare',NULL,'2022-10-18 23:21:41'),(300,150,'Guerrero',NULL,'2022-10-18 23:21:41'),(301,48,'Heredia',NULL,'2022-10-18 23:21:41'),(302,167,'Huancavelica',NULL,'2022-10-18 23:21:41'),(303,167,'Huanuco',NULL,'2022-10-18 23:21:41'),(304,47,'Huila',NULL,'2022-10-18 23:21:41'),(305,167,'Ica',NULL,'2022-10-18 23:21:41'),(306,150,'Jalisco',NULL,'2022-10-18 23:21:41'),(307,11,'Jujuy',NULL,'2022-10-18 23:21:41'),(308,167,'Junin',NULL,'2022-10-18 23:21:41'),(309,47,'La Guajira',NULL,'2022-10-18 23:21:41'),(310,167,'La Libertad',NULL,'2022-10-18 23:21:41'),(311,11,'La Pampa',NULL,'2022-10-18 23:21:41'),(312,11,'La Rioja',NULL,'2022-10-18 23:21:41'),(313,167,'Lambayeque',NULL,'2022-10-18 23:21:41'),(314,228,'Lara',NULL,'2022-10-18 23:21:41'),(315,167,'Lima',NULL,'2022-10-18 23:21:41'),(316,48,'Limon',NULL,'2022-10-18 23:21:41'),(317,167,'Loreto',NULL,'2022-10-18 23:21:41'),(318,167,'Madre de Dios',NULL,'2022-10-18 23:21:41'),(319,47,'Magdalena',NULL,'2022-10-18 23:21:41'),(320,29,'Maranhao',NULL,'2022-10-18 23:21:41'),(321,29,'Mato Grosso',NULL,'2022-10-18 23:21:41'),(322,29,'Mato Grosso do Sul',NULL,'2022-10-18 23:21:41'),(323,44,'Maule',NULL,'2022-10-18 23:21:41'),(324,11,'Mendoza',NULL,'2022-10-18 23:21:41'),(325,228,'Merida',NULL,'2022-10-18 23:21:41'),(326,47,'Meta',NULL,'2022-10-18 23:21:41'),(327,44,'Metropolitana',NULL,'2022-10-18 23:21:41'),(328,150,'Mexico',NULL,'2022-10-18 23:21:41'),(329,150,'Michoacan',NULL,'2022-10-18 23:21:41'),(330,29,'Minas Gerais',NULL,'2022-10-18 23:21:41'),(331,228,'Miranda',NULL,'2022-10-18 23:21:41'),(332,11,'Misiones',NULL,'2022-10-18 23:21:41'),(333,228,'Monagas',NULL,'2022-10-18 23:21:41'),(334,167,'Moquegua',NULL,'2022-10-18 23:21:41'),(335,150,'Morelos',NULL,'2022-10-18 23:21:41'),(336,47,'Narino',NULL,'2022-10-18 23:21:41'),(337,150,'Nayarit',NULL,'2022-10-18 23:21:41'),(338,47,'Norte de Santander',NULL,'2022-10-18 23:21:41'),(339,228,'Nueva Esparta',NULL,'2022-10-18 23:21:41'),(340,150,'Oaxaca',NULL,'2022-10-18 23:21:41'),(341,44,'O\'Higgins',NULL,'2022-10-18 23:21:41'),(342,28,'Oruro',NULL,'2022-10-18 23:21:41'),(343,28,'Pando',NULL,'2022-10-18 23:21:41'),(344,29,'Para',NULL,'2022-10-18 23:21:41'),(345,29,'Paraiba',NULL,'2022-10-18 23:21:41'),(346,29,'Parana',NULL,'2022-10-18 23:21:41'),(347,167,'Pasco',NULL,'2022-10-18 23:21:41'),(348,29,'Pernambuco',NULL,'2022-10-18 23:21:41'),(349,29,'Piaui',NULL,'2022-10-18 23:21:41'),(350,167,'Piura',NULL,'2022-10-18 23:21:41'),(351,228,'Portuguesa',NULL,'2022-10-18 23:21:41'),(352,28,'Potosi',NULL,'2022-10-18 23:21:41'),(353,150,'Puebla',NULL,'2022-10-18 23:21:41'),(354,167,'Puno',NULL,'2022-10-18 23:21:41'),(355,48,'Puntarenas',NULL,'2022-10-18 23:21:41'),(356,47,'Putumayo',NULL,'2022-10-18 23:21:41'),(357,150,'Queretaro',NULL,'2022-10-18 23:21:41'),(358,47,'Quindio',NULL,'2022-10-18 23:21:41'),(359,29,'Rio de Janeiro',NULL,'2022-10-18 23:21:41'),(360,29,'Rio Grande do Norte',NULL,'2022-10-18 23:21:41'),(361,11,'Rio Negro',NULL,'2022-10-18 23:21:41'),(362,47,'Risaralda',NULL,'2022-10-18 23:21:41'),(363,29,'Rondonia',NULL,'2022-10-18 23:21:41'),(364,29,'Roraima',NULL,'2022-10-18 23:21:41'),(365,11,'Salta',NULL,'2022-10-18 23:21:41'),(366,47,'San Andres y Providencia',NULL,'2022-10-18 23:21:41'),(367,11,'San Juan',NULL,'2022-10-18 23:21:41'),(368,11,'San Luis',NULL,'2022-10-18 23:21:41'),(369,167,'San Martin',NULL,'2022-10-18 23:21:41'),(370,29,'Santa Catarina',NULL,'2022-10-18 23:21:41'),(371,11,'Santa Cruz',NULL,'2022-10-18 23:21:41'),(372,28,'Santa Cruz',NULL,'2022-10-18 23:21:41'),(373,11,'Santa Fe',NULL,'2022-10-18 23:21:41'),(374,47,'Santander',NULL,'2022-10-18 23:21:41'),(375,11,'Santiago del Estero',NULL,'2022-10-18 23:21:41'),(376,29,'Sao Paulo',NULL,'2022-10-18 23:21:41'),(377,29,'Sergipe',NULL,'2022-10-18 23:21:41'),(378,47,'Sucre',NULL,'2022-10-18 23:21:41'),(379,228,'Sucre',NULL,'2022-10-18 23:21:41'),(380,150,'Tabasco',NULL,'2022-10-18 23:21:41'),(381,228,'Tachira',NULL,'2022-10-18 23:21:41'),(382,167,'Tacna',NULL,'2022-10-18 23:21:41'),(383,44,'Tarapaca',NULL,'2022-10-18 23:21:41'),(384,28,'Tarija',NULL,'2022-10-18 23:21:41'),(385,11,'Tierra del Fuego',NULL,'2022-10-18 23:21:41'),(386,150,'Tlaxcala',NULL,'2022-10-18 23:21:41'),(387,29,'Tocantins',NULL,'2022-10-18 23:21:41'),(388,47,'Tolima',NULL,'2022-10-18 23:21:41'),(389,228,'Trujillo',NULL,'2022-10-18 23:21:41'),(390,11,'Tucuman',NULL,'2022-10-18 23:21:41'),(391,167,'Tumbes',NULL,'2022-10-18 23:21:41'),(392,167,'Ucayali',NULL,'2022-10-18 23:21:41'),(393,47,'Valle del Cauca',NULL,'2022-10-18 23:21:41'),(394,44,'Valparaiso',NULL,'2022-10-18 23:21:41'),(395,228,'Vargas',NULL,'2022-10-18 23:21:41'),(396,47,'Vaupes',NULL,'2022-10-18 23:21:41'),(397,150,'Veracruz',NULL,'2022-10-18 23:21:41'),(398,47,'Vichada',NULL,'2022-10-18 23:21:41'),(399,228,'Yaracuy',NULL,'2022-10-18 23:21:41'),(400,150,'Yucatan',NULL,'2022-10-18 23:21:41'),(401,228,'Zulia',NULL,'2022-10-18 23:21:41'),(403,36,'Newfoundland and Labrador',NULL,'2022-10-18 23:21:41'),(404,55,'Nordrhein-Westfalen',NULL,'2022-10-18 23:21:41'),(406,12,'Mexico',NULL,'2022-10-18 23:21:41'),(407,61,'Pichincha',NULL,'2022-10-18 23:21:41'),(409,159,'Overijssel',NULL,'2022-10-18 23:21:41'),(410,66,'Castile-La Mancha',NULL,'2022-10-18 23:21:41'),(411,166,'Panama',NULL,'2022-10-18 23:21:41'),(413,88,'Chiquimula',NULL,'2022-10-18 23:21:41'),(414,94,'Comayagua',NULL,'2022-10-18 23:21:41'),(415,4,'Saint George',NULL,'2022-10-18 23:21:41'),(416,143,'La Trinite',NULL,'2022-10-18 23:21:41'),(417,107,'Saint James',NULL,'2022-10-18 23:21:41'),(418,179,'Alto Parana',NULL,'2022-10-18 23:21:41'),(419,61,'Napo',NULL,'2022-10-18 23:21:41'),(421,179,'Caaguazu',NULL,'2022-10-18 23:21:41'),(422,101,'Punjab',NULL,'2022-10-18 23:21:41'),(424,125,'Central',NULL,'2022-10-18 23:21:41'),(425,79,'Tarkwa',NULL,'2022-10-18 23:21:41'),(426,79,'Western',NULL,'2022-10-18 23:21:41'),(427,109,'Miyagi',NULL,'2022-10-18 23:21:41'),(428,161,'Sagarmatha',NULL,'2022-10-18 23:21:41'),(429,35,'Cayo',NULL,'2022-10-18 23:21:41'),(430,120,'Aktobe',NULL,'2022-10-18 23:21:41'),(431,161,'Dhawalagiri',NULL,'2022-10-18 23:21:41'),(432,68,'Unknown',NULL,'2022-10-18 23:21:41'),(433,192,'Unknown',NULL,'2022-10-18 23:21:41'),(434,179,'Paraguari',NULL,'2022-10-18 23:21:41'),(435,179,'Cordillera',NULL,'2022-10-18 23:21:41'),(436,179,'Itapua',NULL,'2022-10-18 23:21:41'),(437,91,'Upper Takutu-Upper Essequibo',NULL,'2022-10-18 23:21:41'),(438,91,'Cuyuni-Mazaruni',NULL,'2022-10-18 23:21:41'),(439,59,'Elias Pina',NULL,'2022-10-18 23:21:41'),(440,59,'Azua',NULL,'2022-10-18 23:21:41'),(442,59,'La Vega',NULL,'2022-10-18 23:21:41'),(443,61,'Chimborazo',NULL,'2022-10-18 23:21:41'),(444,88,'Peten',NULL,'2022-10-18 23:21:41'),(445,201,'Chalatenango',NULL,'2022-10-18 23:21:41'),(446,224,'Maldonado',NULL,'2022-10-18 23:21:41'),(447,172,'Pomerania',NULL,'2022-10-18 23:21:41'),(448,189,'unknown',NULL,'2022-10-18 23:21:41'),(449,55,'Saxony',NULL,'2022-10-18 23:21:41'),(450,189,'SkÃƒÂ¥ne',NULL,'2022-10-18 23:21:41'),(451,256,'D.C.',NULL,'2022-10-18 23:21:41'),(453,224,'Artigas',NULL,'2022-10-18 23:21:41'),(454,224,'Canelones',NULL,'2022-10-18 23:21:41'),(455,224,'Cerro Largo',NULL,'2022-10-18 23:21:41'),(456,224,'Colonia',NULL,'2022-10-18 23:21:41'),(457,224,'Durazno',NULL,'2022-10-18 23:21:41'),(458,224,'Flores',NULL,'2022-10-18 23:21:41'),(459,224,'Florida',NULL,'2022-10-18 23:21:41'),(460,224,'Lavalleja',NULL,'2022-10-18 23:21:41'),(461,224,'Montevideo',NULL,'2022-10-18 23:21:41'),(462,224,'Paysandu',NULL,'2022-10-18 23:21:41'),(463,224,'Rio Negro',NULL,'2022-10-18 23:21:41'),(464,224,'Rivera',NULL,'2022-10-18 23:21:41'),(465,224,'Rocha',NULL,'2022-10-18 23:21:41'),(466,224,'Salto',NULL,'2022-10-18 23:21:41'),(467,224,'San Jose',NULL,'2022-10-18 23:21:41'),(468,224,'Soriano',NULL,'2022-10-18 23:21:41'),(469,224,'Tacuarembo',NULL,'2022-10-18 23:21:41'),(470,224,'Treinta y Tres',NULL,'2022-10-18 23:21:41'),(471,230,'Saint Croix',NULL,'2022-10-18 23:21:41'),(472,230,'Saint John',NULL,'2022-10-18 23:21:41'),(473,230,'Saint Thomas',NULL,'2022-10-18 23:21:41'),(474,229,'Anegada',NULL,'2022-10-18 23:21:41'),(475,229,'Jost Van Dyke',NULL,'2022-10-18 23:21:41'),(476,229,'Tortola',NULL,'2022-10-18 23:21:41'),(477,229,'Virgin Gorda',NULL,'2022-10-18 23:21:41'),(478,216,'Tobago',NULL,'2022-10-18 23:21:41'),(479,216,'Trinidad',NULL,'2022-10-18 23:21:41'),(480,227,'Charlotte',NULL,'2022-10-18 23:21:41'),(481,227,'Grenadines',NULL,'2022-10-18 23:21:41'),(482,227,'Saint Andrew',NULL,'2022-10-18 23:21:41'),(483,227,'Saint David',NULL,'2022-10-18 23:21:41'),(484,227,'Saint George',NULL,'2022-10-18 23:21:41'),(485,227,'Saint Patrick',NULL,'2022-10-18 23:21:41'),(486,123,'Anse la Raye',NULL,'2022-10-18 23:21:41'),(487,123,'Castries',NULL,'2022-10-18 23:21:41'),(488,123,'Choiseul',NULL,'2022-10-18 23:21:41'),(489,123,'Dauphin',NULL,'2022-10-18 23:21:41'),(490,123,'Dennery',NULL,'2022-10-18 23:21:41'),(491,123,'Gros Islet',NULL,'2022-10-18 23:21:41'),(492,123,'Laborie',NULL,'2022-10-18 23:21:41'),(493,123,'Micoud',NULL,'2022-10-18 23:21:41'),(494,123,'Praslin',NULL,'2022-10-18 23:21:41'),(495,123,'Soufriere',NULL,'2022-10-18 23:21:41'),(496,123,'Vieux Fort',NULL,'2022-10-18 23:21:41'),(497,115,'Nevis',NULL,'2022-10-18 23:21:41'),(498,115,'Saint Kitts',NULL,'2022-10-18 23:21:41'),(499,179,'Alto Paraguay',NULL,'2022-10-18 23:21:41'),(500,179,'Amambay',NULL,'2022-10-18 23:21:41'),(501,179,'Asuncion',NULL,'2022-10-18 23:21:41'),(502,179,'Boqueron',NULL,'2022-10-18 23:21:41'),(503,179,'Caazapa',NULL,'2022-10-18 23:21:41'),(504,179,'Canindeyu',NULL,'2022-10-18 23:21:41'),(505,179,'Central',NULL,'2022-10-18 23:21:41'),(506,179,'Concepcion',NULL,'2022-10-18 23:21:41'),(507,179,'Misiones',NULL,'2022-10-18 23:21:41'),(508,179,'Neembucu',NULL,'2022-10-18 23:21:41'),(509,179,'Presidente Hayes',NULL,'2022-10-18 23:21:41'),(510,179,'San Pedro',NULL,'2022-10-18 23:21:41'),(511,179,'Distrito Capital',NULL,'2022-10-18 23:21:41'),(512,179,'Guaira',NULL,'2022-10-18 23:21:41'),(513,166,'Bocas del Toro',NULL,'2022-10-18 23:21:41'),(514,166,'Chiriqui',NULL,'2022-10-18 23:21:41'),(515,166,'Cocle',NULL,'2022-10-18 23:21:41'),(516,166,'Colon',NULL,'2022-10-18 23:21:41'),(517,166,'Darien',NULL,'2022-10-18 23:21:41'),(518,166,'Herrera',NULL,'2022-10-18 23:21:41'),(519,166,'Los Santos',NULL,'2022-10-18 23:21:41'),(520,166,'Veraguas',NULL,'2022-10-18 23:21:41'),(521,158,'Boaco',NULL,'2022-10-18 23:21:41'),(522,158,'Carazo',NULL,'2022-10-18 23:21:41'),(523,158,'Chinandega',NULL,'2022-10-18 23:21:41'),(524,158,'Chontales',NULL,'2022-10-18 23:21:41'),(525,158,'Esteli',NULL,'2022-10-18 23:21:41'),(526,158,'Granada',NULL,'2022-10-18 23:21:41'),(527,158,'Jinotega',NULL,'2022-10-18 23:21:41'),(528,158,'Leon',NULL,'2022-10-18 23:21:41'),(529,158,'Madriz',NULL,'2022-10-18 23:21:41'),(530,158,'Managua',NULL,'2022-10-18 23:21:41'),(531,158,'Masaya',NULL,'2022-10-18 23:21:41'),(532,158,'Matagalpa',NULL,'2022-10-18 23:21:41'),(533,158,'Nueva Segovia',NULL,'2022-10-18 23:21:41'),(534,158,'Region Autonoma del Atlantica Sur',NULL,'2022-10-18 23:21:41'),(535,158,'Region Autonoma del Atlantico Norte',NULL,'2022-10-18 23:21:41'),(536,158,'Rio San Juan',NULL,'2022-10-18 23:21:41'),(537,158,'Rivas',NULL,'2022-10-18 23:21:41'),(538,8,'Aruba',NULL,'2022-10-18 23:21:41'),(539,8,'Bonaire',NULL,'2022-10-18 23:21:41'),(540,8,'Curacao',NULL,'2022-10-18 23:21:41'),(541,8,'Saba',NULL,'2022-10-18 23:21:41'),(542,8,'Sint Eustatius',NULL,'2022-10-18 23:21:41'),(543,8,'Sint Maarten',NULL,'2022-10-18 23:21:41'),(544,143,'Fort-de-France',NULL,'2022-10-18 23:21:41'),(545,143,'Le Marin',NULL,'2022-10-18 23:21:41'),(546,143,'Saint-Pierre',NULL,'2022-10-18 23:21:41'),(547,107,'Clarendon',NULL,'2022-10-18 23:21:41'),(550,107,'Kingston',NULL,'2022-10-18 23:21:41'),(551,107,'Hanover',NULL,'2022-10-18 23:21:41'),(552,107,'Manchester',NULL,'2022-10-18 23:21:41'),(553,107,'Portland',NULL,'2022-10-18 23:21:41'),(554,107,'Saint Andrew',NULL,'2022-10-18 23:21:41'),(555,107,'Saint Ann',NULL,'2022-10-18 23:21:41'),(556,107,'Saint Catherine',NULL,'2022-10-18 23:21:41'),(557,107,'Saint Elizabeth',NULL,'2022-10-18 23:21:41'),(558,107,'Saint Mary',NULL,'2022-10-18 23:21:41'),(559,107,'Saint Thomas',NULL,'2022-10-18 23:21:41'),(560,107,'Trelawny',NULL,'2022-10-18 23:21:41'),(561,107,'Westmoreland',NULL,'2022-10-18 23:21:41'),(562,94,'Atlantida',NULL,'2022-10-18 23:21:41'),(563,94,'Choluteca',NULL,'2022-10-18 23:21:41'),(564,94,'Colon',NULL,'2022-10-18 23:21:41'),(565,94,'Copan',NULL,'2022-10-18 23:21:41'),(566,94,'Cortes',NULL,'2022-10-18 23:21:41'),(567,94,'El Paraiso',NULL,'2022-10-18 23:21:41'),(568,94,'Francisco Morazan',NULL,'2022-10-18 23:21:41'),(569,94,'Gracias a Dios',NULL,'2022-10-18 23:21:41'),(570,94,'Intibuca',NULL,'2022-10-18 23:21:41'),(571,94,'Islas de la Bahia',NULL,'2022-10-18 23:21:41'),(572,94,'La Paz',NULL,'2022-10-18 23:21:41'),(573,94,'Lempira',NULL,'2022-10-18 23:21:41'),(574,94,'Ocotepeque',NULL,'2022-10-18 23:21:41'),(575,94,'Olancho',NULL,'2022-10-18 23:21:41'),(576,94,'Santa Barbara',NULL,'2022-10-18 23:21:41'),(577,94,'Valle',NULL,'2022-10-18 23:21:41'),(578,94,'Yoro',NULL,'2022-10-18 23:21:41'),(579,96,'Artibonite',NULL,'2022-10-18 23:21:41'),(580,96,'Centre',NULL,'2022-10-18 23:21:41'),(581,96,'Grand\'Anse',NULL,'2022-10-18 23:21:41'),(582,96,'Nippes',NULL,'2022-10-18 23:21:41'),(583,96,'Nord',NULL,'2022-10-18 23:21:41'),(584,96,'Nord-Est',NULL,'2022-10-18 23:21:41'),(585,96,'Nord-Ouest',NULL,'2022-10-18 23:21:41'),(586,96,'Ouest',NULL,'2022-10-18 23:21:41'),(587,96,'Sud',NULL,'2022-10-18 23:21:41'),(588,96,'Sud-Est',NULL,'2022-10-18 23:21:41'),(589,91,'Barima-Waini',NULL,'2022-10-18 23:21:41'),(590,91,'Demerara-Mahaica',NULL,'2022-10-18 23:21:41'),(591,91,'East Berbice-Corentyne',NULL,'2022-10-18 23:21:41'),(592,91,'Essequibo Islands-West Demerara',NULL,'2022-10-18 23:21:41'),(593,91,'Mahaica-Berbice',NULL,'2022-10-18 23:21:41'),(594,91,'Pomeroon-Supenaam',NULL,'2022-10-18 23:21:41'),(595,91,'Potaro-Siparuni',NULL,'2022-10-18 23:21:41'),(596,91,'Upper Demerara-Berbice',NULL,'2022-10-18 23:21:41'),(597,88,'Alta Verapaz',NULL,'2022-10-18 23:21:41'),(598,88,'Baja Verapaz',NULL,'2022-10-18 23:21:41'),(599,88,'Chimaltenango',NULL,'2022-10-18 23:21:41'),(600,88,'El Progreso',NULL,'2022-10-18 23:21:41'),(601,88,'El Quiche',NULL,'2022-10-18 23:21:41'),(602,88,'Escuintla',NULL,'2022-10-18 23:21:41'),(603,88,'Guatemala',NULL,'2022-10-18 23:21:41'),(604,88,'Huehuetenango',NULL,'2022-10-18 23:21:41'),(605,88,'Izabal',NULL,'2022-10-18 23:21:41'),(606,88,'Jalapa',NULL,'2022-10-18 23:21:41'),(607,88,'Jutiapa',NULL,'2022-10-18 23:21:41'),(608,88,'Quetzaltenango',NULL,'2022-10-18 23:21:41'),(609,88,'Retalhuleu',NULL,'2022-10-18 23:21:41'),(610,88,'Sacatepequez',NULL,'2022-10-18 23:21:41'),(611,88,'San Marcos',NULL,'2022-10-18 23:21:41'),(612,88,'Santa Rosa',NULL,'2022-10-18 23:21:41'),(613,88,'Solola',NULL,'2022-10-18 23:21:41'),(614,88,'Suchitepequez',NULL,'2022-10-18 23:21:41'),(615,88,'Totonicapan',NULL,'2022-10-18 23:21:41'),(616,88,'Zacapa',NULL,'2022-10-18 23:21:41'),(617,76,'Saint Andrew',NULL,'2022-10-18 23:21:41'),(618,76,'Saint David',NULL,'2022-10-18 23:21:41'),(619,76,'Saint George',NULL,'2022-10-18 23:21:41'),(620,76,'Saint John',NULL,'2022-10-18 23:21:41'),(621,76,'Saint Mark',NULL,'2022-10-18 23:21:41'),(622,76,'Saint Patrick',NULL,'2022-10-18 23:21:41'),(623,78,'Cayenne',NULL,'2022-10-18 23:21:41'),(624,78,'Saint Laurent du Maroni',NULL,'2022-10-18 23:21:41'),(625,201,'Ahuachapan',NULL,'2022-10-18 23:21:41'),(626,201,'Cabanas',NULL,'2022-10-18 23:21:41'),(627,201,'Cuscatlan',NULL,'2022-10-18 23:21:41'),(628,201,'La Libertad',NULL,'2022-10-18 23:21:41'),(629,201,'La Paz',NULL,'2022-10-18 23:21:41'),(630,201,'La Union',NULL,'2022-10-18 23:21:41'),(631,201,'Morazan',NULL,'2022-10-18 23:21:41'),(632,201,'San Miguel',NULL,'2022-10-18 23:21:41'),(633,201,'San Salvador',NULL,'2022-10-18 23:21:41'),(634,201,'Santa Ana',NULL,'2022-10-18 23:21:41'),(635,201,'Sonsonate',NULL,'2022-10-18 23:21:41'),(636,201,'Usulutan',NULL,'2022-10-18 23:21:41'),(637,201,'San Vicente',NULL,'2022-10-18 23:21:41'),(638,61,'Azuay',NULL,'2022-10-18 23:21:41'),(639,61,'Bolivar',NULL,'2022-10-18 23:21:41'),(640,61,'Canar',NULL,'2022-10-18 23:21:41'),(641,61,'Carchi',NULL,'2022-10-18 23:21:41'),(642,61,'Cotopaxi',NULL,'2022-10-18 23:21:41'),(643,61,'El Oro',NULL,'2022-10-18 23:21:41'),(644,61,'Esmeraldas',NULL,'2022-10-18 23:21:41'),(645,61,'Galapagos',NULL,'2022-10-18 23:21:41'),(646,61,'Guayas',NULL,'2022-10-18 23:21:41'),(647,61,'Imbabura',NULL,'2022-10-18 23:21:41'),(648,61,'Loja',NULL,'2022-10-18 23:21:41'),(649,61,'Los Rios',NULL,'2022-10-18 23:21:41'),(650,61,'Manabi',NULL,'2022-10-18 23:21:41'),(651,61,'Morona Santiago',NULL,'2022-10-18 23:21:41'),(652,61,'Orellana',NULL,'2022-10-18 23:21:41'),(653,61,'Pastaza',NULL,'2022-10-18 23:21:41'),(654,61,'Santa Elena',NULL,'2022-10-18 23:21:41'),(655,61,'Santo Domingo de los Tsachilas',NULL,'2022-10-18 23:21:41'),(656,61,'Sucumbios',NULL,'2022-10-18 23:21:41'),(657,61,'Tungurahua',NULL,'2022-10-18 23:21:41'),(658,61,'Zamora Chinchipe',NULL,'2022-10-18 23:21:41'),(659,59,'Baoruco',NULL,'2022-10-18 23:21:41'),(660,59,'Barahona',NULL,'2022-10-18 23:21:41'),(661,59,'Dajabon',NULL,'2022-10-18 23:21:41'),(662,59,'Distrito Nacional',NULL,'2022-10-18 23:21:41'),(663,59,'Duarte',NULL,'2022-10-18 23:21:41'),(664,59,'El Seibo',NULL,'2022-10-18 23:21:41'),(665,59,'Espaillat',NULL,'2022-10-18 23:21:41'),(666,59,'Hato Mayor',NULL,'2022-10-18 23:21:41'),(667,59,'Hermanas Mirabel',NULL,'2022-10-18 23:21:41'),(668,59,'Independencia',NULL,'2022-10-18 23:21:41'),(669,59,'La Altagracia',NULL,'2022-10-18 23:21:41'),(670,59,'La Romana',NULL,'2022-10-18 23:21:41'),(674,59,'Maria Trinidad Sanchez',NULL,'2022-10-18 23:21:41'),(675,59,'Monsenor Nouel',NULL,'2022-10-18 23:21:41'),(676,59,'Monte Cristi',NULL,'2022-10-18 23:21:41'),(677,59,'Monte Plata',NULL,'2022-10-18 23:21:41'),(678,59,'Pedernales',NULL,'2022-10-18 23:21:41'),(679,59,'Peravia',NULL,'2022-10-18 23:21:41'),(680,59,'Puerto Plata',NULL,'2022-10-18 23:21:41'),(681,59,'Samana',NULL,'2022-10-18 23:21:41'),(682,59,'San Cristobal',NULL,'2022-10-18 23:21:41'),(683,59,'San Jose de Ocoa',NULL,'2022-10-18 23:21:41'),(684,59,'San Juan',NULL,'2022-10-18 23:21:41'),(685,59,'San Pedro de Macoris',NULL,'2022-10-18 23:21:41'),(686,59,'Sanchez Ramirez',NULL,'2022-10-18 23:21:41'),(687,59,'Santiago',NULL,'2022-10-18 23:21:41'),(688,59,'Santiago Rodriguez',NULL,'2022-10-18 23:21:41'),(689,59,'Santo Domingo',NULL,'2022-10-18 23:21:41'),(690,59,'Valverde',NULL,'2022-10-18 23:21:41'),(691,58,'Saint Andrew',NULL,'2022-10-18 23:21:41'),(692,58,'Saint David',NULL,'2022-10-18 23:21:41'),(693,58,'Saint George',NULL,'2022-10-18 23:21:41'),(694,58,'Saint John',NULL,'2022-10-18 23:21:41'),(695,58,'Saint Joseph',NULL,'2022-10-18 23:21:41'),(696,58,'Saint Luke',NULL,'2022-10-18 23:21:41'),(697,58,'Saint Mark',NULL,'2022-10-18 23:21:41'),(698,58,'Saint Patrick',NULL,'2022-10-18 23:21:41'),(699,58,'Saint Paul',NULL,'2022-10-18 23:21:41'),(700,58,'Saint Peter',NULL,'2022-10-18 23:21:41'),(701,50,'Artemisa',NULL,'2022-10-18 23:21:41'),(702,50,'Camaguey',NULL,'2022-10-18 23:21:41'),(703,50,'Ciego de Avila',NULL,'2022-10-18 23:21:41'),(704,50,'Cienfuegos',NULL,'2022-10-18 23:21:41'),(705,50,'Granma',NULL,'2022-10-18 23:21:41'),(706,50,'Guantanamo',NULL,'2022-10-18 23:21:41'),(707,50,'Holguin',NULL,'2022-10-18 23:21:41'),(708,50,'Isla de la Juventud',NULL,'2022-10-18 23:21:41'),(709,50,'La Habana',NULL,'2022-10-18 23:21:41'),(710,50,'Las Tunas',NULL,'2022-10-18 23:21:41'),(711,50,'Matanzas',NULL,'2022-10-18 23:21:41'),(712,50,'Mayabeque',NULL,'2022-10-18 23:21:41'),(713,50,'Pinar del Rio',NULL,'2022-10-18 23:21:41'),(714,50,'Sancti Spiritus',NULL,'2022-10-18 23:21:41'),(715,50,'Santiago de Cuba',NULL,'2022-10-18 23:21:41'),(716,50,'Villa Clara',NULL,'2022-10-18 23:21:41'),(717,263,'El Hierro',NULL,'2022-10-18 23:21:41'),(718,263,'Fuerteventura',NULL,'2022-10-18 23:21:41'),(719,263,'Gran Canaria',NULL,'2022-10-18 23:21:41'),(720,263,'La Gomera',NULL,'2022-10-18 23:21:41'),(721,263,'La Palma',NULL,'2022-10-18 23:21:41'),(722,263,'Lanzarote',NULL,'2022-10-18 23:21:41'),(723,263,'Rio Negro',NULL,'2022-10-18 23:21:41'),(724,263,'Tenerife',NULL,'2022-10-18 23:21:41'),(725,35,'Belize',NULL,'2022-10-18 23:21:41'),(726,35,'Corozal',NULL,'2022-10-18 23:21:41'),(727,35,'Orange Walk',NULL,'2022-10-18 23:21:41'),(728,35,'Stann Creek',NULL,'2022-10-18 23:21:41'),(729,35,'Toledo',NULL,'2022-10-18 23:21:41'),(730,18,'Christ Church',NULL,'2022-10-18 23:21:41'),(731,18,'Saint Andrew',NULL,'2022-10-18 23:21:41'),(732,18,'Saint George',NULL,'2022-10-18 23:21:41'),(733,18,'Saint James',NULL,'2022-10-18 23:21:41'),(734,18,'Saint John',NULL,'2022-10-18 23:21:41'),(735,18,'Saint Joseph',NULL,'2022-10-18 23:21:41'),(736,18,'Saint Lucy',NULL,'2022-10-18 23:21:41'),(737,18,'Saint Michael',NULL,'2022-10-18 23:21:41'),(738,18,'Saint Peter',NULL,'2022-10-18 23:21:41'),(739,18,'Saint Philip',NULL,'2022-10-18 23:21:41'),(740,18,'Saint Thomas',NULL,'2022-10-18 23:21:41'),(741,30,'Acklins',NULL,'2022-10-18 23:21:41'),(742,30,'Berry Islands',NULL,'2022-10-18 23:21:41'),(743,30,'Bimini',NULL,'2022-10-18 23:21:41'),(744,30,'Black Point',NULL,'2022-10-18 23:21:41'),(746,30,'Cat Island',NULL,'2022-10-18 23:21:41'),(747,30,'Central Abaco',NULL,'2022-10-18 23:21:41'),(748,30,'Central Andros',NULL,'2022-10-18 23:21:41'),(749,30,'Central Eleuthera',NULL,'2022-10-18 23:21:41'),(750,30,'City of Freeport',NULL,'2022-10-18 23:21:41'),(751,30,'Crooked Island',NULL,'2022-10-18 23:21:41'),(752,30,'East Grand Bahama',NULL,'2022-10-18 23:21:41'),(753,30,'Exuma',NULL,'2022-10-18 23:21:41'),(754,30,'Grand Cay',NULL,'2022-10-18 23:21:41'),(755,30,'Green Turtle Cay',NULL,'2022-10-18 23:21:41'),(756,30,'Harbour Island',NULL,'2022-10-18 23:21:41'),(757,30,'Hope Town',NULL,'2022-10-18 23:21:41'),(758,30,'Inagua',NULL,'2022-10-18 23:21:41'),(759,30,'Long Island',NULL,'2022-10-18 23:21:41'),(760,30,'Mangrove Cay',NULL,'2022-10-18 23:21:41'),(761,30,'Mayaguana',NULL,'2022-10-18 23:21:41'),(762,30,'Moore\'s Island',NULL,'2022-10-18 23:21:41'),(763,30,'North Abaco',NULL,'2022-10-18 23:21:41'),(764,30,'North Andros',NULL,'2022-10-18 23:21:41'),(765,30,'North Eleuthera',NULL,'2022-10-18 23:21:41'),(766,30,'Ragged Island',NULL,'2022-10-18 23:21:41'),(767,30,'Rum Cay',NULL,'2022-10-18 23:21:41'),(768,30,'San Salvador',NULL,'2022-10-18 23:21:41'),(769,30,'South Abaco',NULL,'2022-10-18 23:21:41'),(770,30,'South Andros',NULL,'2022-10-18 23:21:41'),(771,30,'South Eleuthera',NULL,'2022-10-18 23:21:41'),(772,30,'Spanish Wells',NULL,'2022-10-18 23:21:41'),(773,30,'West Grand Bahama',NULL,'2022-10-18 23:21:41'),(774,4,'Barbuda',NULL,'2022-10-18 23:21:41'),(775,4,'Redonda',NULL,'2022-10-18 23:21:41'),(776,4,'Saint John',NULL,'2022-10-18 23:21:41'),(777,4,'Saint Mary',NULL,'2022-10-18 23:21:41'),(778,4,'Saint Paul',NULL,'2022-10-18 23:21:41'),(779,4,'Saint Peter',NULL,'2022-10-18 23:21:41'),(780,4,'Saint Philip',NULL,'2022-10-18 23:21:41'),(781,228,'Distrito Capital',NULL,'2022-10-18 23:21:41'),(782,100,'North',NULL,'2022-10-18 23:21:41'),(783,100,'Haifa',NULL,'2022-10-18 23:21:41'),(784,100,'Center',NULL,'2022-10-18 23:21:41'),(785,100,'Tel Aviv',NULL,'2022-10-18 23:21:41'),(786,100,'Jerusalem',NULL,'2022-10-18 23:21:41'),(787,100,'South',NULL,'2022-10-18 23:21:41'),(788,100,'Judea and Samaria',NULL,'2022-10-18 23:21:41'),(789,63,'Alexandria',NULL,'2022-10-18 23:21:41'),(790,63,'Aswan',NULL,'2022-10-18 23:21:41'),(791,63,'Asyut',NULL,'2022-10-18 23:21:41'),(792,63,'Beheira',NULL,'2022-10-18 23:21:41'),(793,63,'Beni Suef',NULL,'2022-10-18 23:21:41'),(794,63,'Cairo',NULL,'2022-10-18 23:21:41'),(795,63,'Dakahlia',NULL,'2022-10-18 23:21:41'),(796,63,'Damietta',NULL,'2022-10-18 23:21:41'),(797,63,'Faiyum',NULL,'2022-10-18 23:21:41'),(798,63,'Gharbia',NULL,'2022-10-18 23:21:41'),(799,63,'Giza',NULL,'2022-10-18 23:21:41'),(800,63,'Ismailia',NULL,'2022-10-18 23:21:41'),(801,63,'Kafr el-Sheikh',NULL,'2022-10-18 23:21:41'),(802,63,'Matruh',NULL,'2022-10-18 23:21:41'),(803,63,'Minya',NULL,'2022-10-18 23:21:41'),(804,63,'Monufia',NULL,'2022-10-18 23:21:41'),(805,63,'New Valley',NULL,'2022-10-18 23:21:41'),(806,63,'North Sinai',NULL,'2022-10-18 23:21:41'),(807,63,'Port Said',NULL,'2022-10-18 23:21:41'),(808,63,'Qalyubia',NULL,'2022-10-18 23:21:41'),(809,63,'Qena',NULL,'2022-10-18 23:21:41'),(810,63,'Red Sea',NULL,'2022-10-18 23:21:41'),(811,63,'Al Sharqia',NULL,'2022-10-18 23:21:41'),(812,63,'Sohag',NULL,'2022-10-18 23:21:41'),(813,63,'South Sinai',NULL,'2022-10-18 23:21:41'),(814,63,'Suez',NULL,'2022-10-18 23:21:41'),(815,63,'Luxor',NULL,'2022-10-18 23:21:41'),(816,36,'Quebec',NULL,'2022-10-18 23:21:41'),(817,36,'Nova Scotia',NULL,'2022-10-18 23:21:41'),(818,36,'New Brunswick',NULL,'2022-10-18 23:21:41'),(819,36,'Prince Edward Island',NULL,'2022-10-18 23:21:41'),(820,36,'Saskatchewan',NULL,'2022-10-18 23:21:41'),(821,208,'Bangkok',NULL,'2022-10-18 23:21:41'),(822,208,'Amnat Charoen',NULL,'2022-10-18 23:21:41'),(823,208,'Ang Thong',NULL,'2022-10-18 23:21:41'),(824,208,'Bueng Kan',NULL,'2022-10-18 23:21:41'),(825,208,'Buriram',NULL,'2022-10-18 23:21:41'),(826,208,'Chachoengsao',NULL,'2022-10-18 23:21:41'),(827,208,'Chainat',NULL,'2022-10-18 23:21:41'),(828,208,'Chaiyaphum',NULL,'2022-10-18 23:21:41'),(829,208,'Chanthaburi',NULL,'2022-10-18 23:21:41'),(830,208,'Chiang Mai',NULL,'2022-10-18 23:21:41'),(831,208,'Chiang Rai',NULL,'2022-10-18 23:21:41'),(832,208,'Chonburi',NULL,'2022-10-18 23:21:41'),(833,208,'Chumphon',NULL,'2022-10-18 23:21:41'),(834,208,'Kalasin',NULL,'2022-10-18 23:21:41'),(835,208,'Kamphaeng',NULL,'2022-10-18 23:21:41'),(836,208,'Kanchanaburi',NULL,'2022-10-18 23:21:41'),(837,208,'Khon Kaen',NULL,'2022-10-18 23:21:41'),(838,208,'Krabi',NULL,'2022-10-18 23:21:41'),(839,208,'Lampang',NULL,'2022-10-18 23:21:41'),(840,208,'Lamphun',NULL,'2022-10-18 23:21:41'),(841,208,'Loei',NULL,'2022-10-18 23:21:41'),(842,208,'Lopburi',NULL,'2022-10-18 23:21:41'),(843,208,'Mae Hong Son',NULL,'2022-10-18 23:21:41'),(844,208,'Maha Sarakham',NULL,'2022-10-18 23:21:41'),(845,208,'Mukdahan',NULL,'2022-10-18 23:21:41'),(846,208,'Nakhon Nayok',NULL,'2022-10-18 23:21:41'),(847,208,'Nakhon Pathom',NULL,'2022-10-18 23:21:41'),(848,208,'Nakhon Phanom',NULL,'2022-10-18 23:21:41'),(849,208,'Nakhon Ratchasima',NULL,'2022-10-18 23:21:41'),(850,208,'Nakhon Sawan',NULL,'2022-10-18 23:21:41'),(851,208,'Nakhon Si Thammarat',NULL,'2022-10-18 23:21:41'),(852,208,'Nan',NULL,'2022-10-18 23:21:41'),(853,208,'Narathiwat',NULL,'2022-10-18 23:21:41'),(854,208,'Nong Bua Lamphu',NULL,'2022-10-18 23:21:41'),(855,208,'Nong Khai',NULL,'2022-10-18 23:21:41'),(856,208,'Nonthaburi',NULL,'2022-10-18 23:21:41'),(857,208,'Pathum Thani',NULL,'2022-10-18 23:21:41'),(858,208,'Pattani',NULL,'2022-10-18 23:21:41'),(859,208,'Phang Nga',NULL,'2022-10-18 23:21:41'),(860,208,'Phatthalung',NULL,'2022-10-18 23:21:41'),(861,208,'Phayao',NULL,'2022-10-18 23:21:41'),(862,208,'Phetchabun',NULL,'2022-10-18 23:21:41'),(863,208,'Phetchaburi',NULL,'2022-10-18 23:21:41'),(864,208,'Phichit',NULL,'2022-10-18 23:21:41'),(865,208,'Phitsanulok',NULL,'2022-10-18 23:21:41'),(866,208,'Phra Nakhon Si Ayutthaya',NULL,'2022-10-18 23:21:41'),(867,208,'Phrae',NULL,'2022-10-18 23:21:41'),(868,208,'Phuket',NULL,'2022-10-18 23:21:41'),(869,208,'Prachinburi',NULL,'2022-10-18 23:21:41'),(870,208,'Prachuap Khiri Khan',NULL,'2022-10-18 23:21:41'),(871,208,'Ranong',NULL,'2022-10-18 23:21:41'),(872,208,'Ratchaburi',NULL,'2022-10-18 23:21:41'),(873,208,'Rayong',NULL,'2022-10-18 23:21:41'),(874,208,'Roi Et',NULL,'2022-10-18 23:21:41'),(875,208,'Sa Kaeo',NULL,'2022-10-18 23:21:41'),(876,208,'Sakon Nakhon',NULL,'2022-10-18 23:21:41'),(877,208,'Samut Prakan',NULL,'2022-10-18 23:21:41'),(878,208,'Samut Sakhon',NULL,'2022-10-18 23:21:41'),(879,208,'Samut Songkhram',NULL,'2022-10-18 23:21:41'),(880,208,'Saraburi',NULL,'2022-10-18 23:21:41'),(881,208,'Satun',NULL,'2022-10-18 23:21:41'),(882,208,'Sing Buri',NULL,'2022-10-18 23:21:41'),(883,208,'Sisaket',NULL,'2022-10-18 23:21:41'),(884,208,'Songkhla',NULL,'2022-10-18 23:21:41'),(885,208,'Sukhothai',NULL,'2022-10-18 23:21:41'),(886,208,'Suphan Buri',NULL,'2022-10-18 23:21:41'),(887,208,'Surat Thani',NULL,'2022-10-18 23:21:41'),(888,208,'Surin',NULL,'2022-10-18 23:21:41'),(889,208,'Tak',NULL,'2022-10-18 23:21:41'),(890,208,'Trang',NULL,'2022-10-18 23:21:41'),(891,208,'Trat',NULL,'2022-10-18 23:21:41'),(892,208,'Ubon Ratchathani',NULL,'2022-10-18 23:21:41'),(893,208,'Udon Thani',NULL,'2022-10-18 23:21:41'),(894,208,'Uthai Thani',NULL,'2022-10-18 23:21:41'),(895,208,'Uttaradit',NULL,'2022-10-18 23:21:41'),(896,208,'Yala',NULL,'2022-10-18 23:21:41'),(897,208,'Yasothon',NULL,'2022-10-18 23:21:41'),(898,161,'Bagmati',NULL,'2022-10-18 23:21:41'),(899,161,'Bheri',NULL,'2022-10-18 23:21:41'),(900,161,'Gandaki',NULL,'2022-10-18 23:21:41'),(901,161,'Janakpur',NULL,'2022-10-18 23:21:41'),(902,161,'Karnali',NULL,'2022-10-18 23:21:41'),(903,161,'Koshi',NULL,'2022-10-18 23:21:41'),(904,161,'Lumbini',NULL,'2022-10-18 23:21:41'),(905,161,'Mahakali',NULL,'2022-10-18 23:21:41'),(906,161,'Mechi',NULL,'2022-10-18 23:21:41'),(907,161,'Narayani',NULL,'2022-10-18 23:21:41'),(908,161,'Rapti',NULL,'2022-10-18 23:21:41'),(910,161,'Seti',NULL,'2022-10-18 23:21:41'),(911,66,'Andalusia',NULL,'2022-10-18 23:21:41'),(912,66,'Aragon',NULL,'2022-10-18 23:21:41'),(913,66,'Asturias',NULL,'2022-10-18 23:21:41'),(914,66,'Balearic Islands',NULL,'2022-10-18 23:21:41'),(915,66,'Basque Country',NULL,'2022-10-18 23:21:41'),(916,66,'Canary Islands',NULL,'2022-10-18 23:21:41'),(917,66,'Cantabria',NULL,'2022-10-18 23:21:41'),(918,66,'Castile and Leon',NULL,'2022-10-18 23:21:41'),(919,66,'Catalonia',NULL,'2022-10-18 23:21:41'),(920,66,'Community of Madrid',NULL,'2022-10-18 23:21:41'),(921,66,'Extremadura',NULL,'2022-10-18 23:21:41'),(922,66,'Galicia',NULL,'2022-10-18 23:21:41'),(923,66,'La Rioja',NULL,'2022-10-18 23:21:41'),(924,66,'Murcia',NULL,'2022-10-18 23:21:41'),(925,66,'Navarre',NULL,'2022-10-18 23:21:41'),(926,66,'Valencian Community',NULL,'2022-10-18 23:21:41'),(927,14,'New South Wales',NULL,'2022-10-18 23:21:41'),(928,14,'Queensland',NULL,'2022-10-18 23:21:41'),(929,14,'South Australia',NULL,'2022-10-18 23:21:41'),(930,14,'Tasmania',NULL,'2022-10-18 23:21:41'),(931,14,'Victoria',NULL,'2022-10-18 23:21:41'),(932,14,'Western Australia',NULL,'2022-10-18 23:21:41'),(933,14,'Australian Capital Territory',NULL,'2022-10-18 23:21:41'),(934,14,'Northern Territory',NULL,'2022-10-18 23:21:41'),(935,199,'Brokopondo',NULL,'2022-10-18 23:21:41'),(936,199,'Commewijne',NULL,'2022-10-18 23:21:41'),(937,199,'Coronie',NULL,'2022-10-18 23:21:41'),(938,199,'Marowijne',NULL,'2022-10-18 23:21:41'),(939,199,'Nickerie',NULL,'2022-10-18 23:21:41'),(940,199,'Para',NULL,'2022-10-18 23:21:41'),(941,199,'Paramaribo',NULL,'2022-10-18 23:21:41'),(942,199,'Saramacca',NULL,'2022-10-18 23:21:41'),(943,199,'Sipaliwini',NULL,'2022-10-18 23:21:41'),(944,199,'Wanica',NULL,'2022-10-18 23:21:41'),(945,36,'Nunavut',NULL,'2022-10-18 23:21:41'),(946,36,'Northwest Territories',NULL,'2022-10-18 23:21:41'),(947,36,'Yukon',NULL,'2022-10-18 23:21:41'),(948,55,'Baden-WÃ¼rttemberg',NULL,'2022-10-18 23:21:41'),(949,55,'Bavaria',NULL,'2022-10-18 23:21:41'),(950,55,'Freistaat Bayern',NULL,'2022-10-18 23:21:41'),(951,55,'Berlin',NULL,'2022-10-18 23:21:41'),(952,55,'Brandenburg',NULL,'2022-10-18 23:21:41'),(953,55,'Bremen',NULL,'2022-10-18 23:21:41'),(954,55,'Freie Hansestadt Bremen',NULL,'2022-10-18 23:21:41'),(955,55,'Hamburg',NULL,'2022-10-18 23:21:41'),(956,55,'Freie und Hansestadt Hamburg',NULL,'2022-10-18 23:21:41'),(957,55,'Hesse',NULL,'2022-10-18 23:21:41'),(958,55,'Hessen',NULL,'2022-10-18 23:21:41'),(959,55,'Lower Saxony',NULL,'2022-10-18 23:21:41'),(960,55,'Niedersachsen',NULL,'2022-10-18 23:21:41'),(961,55,'Mecklenburg-Vorpommern',NULL,'2022-10-18 23:21:41'),(962,55,'North Rhine-Westphalia',NULL,'2022-10-18 23:21:41'),(963,55,'Rhineland-Palatinate',NULL,'2022-10-18 23:21:41'),(964,55,'Rheinland-Pfalz',NULL,'2022-10-18 23:21:41'),(965,55,'Saarland',NULL,'2022-10-18 23:21:41'),(966,55,'Freistaat Sachsen',NULL,'2022-10-18 23:21:41'),(967,55,'Saxony-Anhalt',NULL,'2022-10-18 23:21:41'),(968,55,'Sachsen-Anhalt',NULL,'2022-10-18 23:21:41'),(969,55,'Schleswig-Holstein',NULL,'2022-10-18 23:21:41'),(970,55,'Thuringia',NULL,'2022-10-18 23:21:41'),(971,55,'Freistaat ThÃ¼ringen',NULL,'2022-10-18 23:21:41'),(972,101,'Assam',NULL,'2022-10-18 23:21:41'),(973,101,'Kerala',NULL,'2022-10-18 23:21:41'),(974,66,'Huelva',NULL,'2022-10-18 23:21:41'),(975,66,'Malaga',NULL,'2022-10-18 23:21:41');
/*!40000 ALTER TABLE `lkupstateprovince` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media` (
  `mediaid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned DEFAULT NULL,
  `occid` int(10) unsigned DEFAULT NULL,
  `url` varchar(250) NOT NULL,
  `caption` varchar(250) DEFAULT NULL,
  `authoruid` int(10) unsigned DEFAULT NULL,
  `author` varchar(45) DEFAULT NULL,
  `mediatype` varchar(45) DEFAULT NULL,
  `owner` varchar(250) DEFAULT NULL,
  `sourceurl` varchar(250) DEFAULT NULL,
  `locality` varchar(250) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `mediaMD5` varchar(45) DEFAULT NULL,
  `sortsequence` int(11) DEFAULT NULL,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`mediaid`),
  KEY `FK_media_taxa_idx` (`tid`),
  KEY `FK_media_occid_idx` (`occid`),
  KEY `FK_media_uid_idx` (`authoruid`),
  CONSTRAINT `FK_media_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_media_taxa` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON UPDATE CASCADE,
  CONSTRAINT `FK_media_uid` FOREIGN KEY (`authoruid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media`
--

LOCK TABLES `media` WRITE;
/*!40000 ALTER TABLE `media` DISABLE KEYS */;
/*!40000 ALTER TABLE `media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omcollcategories`
--

DROP TABLE IF EXISTS `omcollcategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omcollcategories` (
  `ccpk` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(75) NOT NULL,
  `icon` varchar(250) DEFAULT NULL,
  `acronym` varchar(45) DEFAULT NULL,
  `url` varchar(250) DEFAULT NULL,
  `inclusive` int(2) DEFAULT '1',
  `notes` varchar(250) DEFAULT NULL,
  `sortsequence` int(11) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ccpk`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omcollcategories`
--

LOCK TABLES `omcollcategories` WRITE;
/*!40000 ALTER TABLE `omcollcategories` DISABLE KEYS */;
/*!40000 ALTER TABLE `omcollcategories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omcollcatlink`
--

DROP TABLE IF EXISTS `omcollcatlink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omcollcatlink` (
  `ccpk` int(10) unsigned NOT NULL,
  `collid` int(10) unsigned NOT NULL,
  `isPrimary` tinyint(1) DEFAULT '1',
  `sortsequence` int(11) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ccpk`,`collid`),
  KEY `FK_collcatlink_coll` (`collid`),
  CONSTRAINT `FK_collcatlink_cat` FOREIGN KEY (`ccpk`) REFERENCES `omcollcategories` (`ccpk`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_collcatlink_coll` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omcollcatlink`
--

LOCK TABLES `omcollcatlink` WRITE;
/*!40000 ALTER TABLE `omcollcatlink` DISABLE KEYS */;
/*!40000 ALTER TABLE `omcollcatlink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omcollections`
--

DROP TABLE IF EXISTS `omcollections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omcollections` (
  `CollID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `InstitutionCode` varchar(45) NOT NULL,
  `CollectionCode` varchar(45) DEFAULT NULL,
  `CollectionName` varchar(150) NOT NULL,
  `collectionId` varchar(100) DEFAULT NULL,
  `datasetID` varchar(250) DEFAULT NULL,
  `datasetName` varchar(100) DEFAULT NULL,
  `iid` int(10) unsigned DEFAULT NULL,
  `fulldescription` varchar(2000) DEFAULT NULL,
  `Homepage` varchar(250) DEFAULT NULL,
  `resourceJson` json DEFAULT NULL,
  `IndividualUrl` varchar(500) DEFAULT NULL,
  `Contact` varchar(250) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `contactJson` json DEFAULT NULL,
  `latitudedecimal` decimal(8,6) DEFAULT NULL,
  `longitudedecimal` decimal(9,6) DEFAULT NULL,
  `icon` varchar(250) DEFAULT NULL,
  `CollType` varchar(45) NOT NULL DEFAULT 'Preserved Specimens' COMMENT 'Preserved Specimens, General Observations, Observations',
  `ManagementType` varchar(45) DEFAULT 'Snapshot' COMMENT 'Snapshot, Live Data',
  `PublicEdits` int(1) unsigned NOT NULL DEFAULT '1',
  `collectionguid` varchar(45) DEFAULT NULL,
  `securitykey` varchar(45) DEFAULT NULL,
  `guidtarget` varchar(45) DEFAULT NULL,
  `rightsHolder` varchar(250) DEFAULT NULL,
  `rights` varchar(250) DEFAULT NULL,
  `usageTerm` varchar(250) DEFAULT NULL,
  `publishToGbif` int(11) DEFAULT NULL,
  `publishToIdigbio` int(11) DEFAULT NULL,
  `aggKeysStr` varchar(1000) DEFAULT NULL,
  `dwcaUrl` varchar(250) DEFAULT NULL,
  `bibliographicCitation` varchar(1000) DEFAULT NULL,
  `accessrights` varchar(1000) DEFAULT NULL,
  `dynamicProperties` text,
  `SortSeq` int(10) unsigned DEFAULT NULL,
  `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`CollID`) USING BTREE,
  UNIQUE KEY `Index_inst` (`InstitutionCode`,`CollectionCode`),
  KEY `FK_collid_iid_idx` (`iid`),
  CONSTRAINT `FK_collid_iid` FOREIGN KEY (`iid`) REFERENCES `institutions` (`iid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omcollections`
--

LOCK TABLES `omcollections` WRITE;
/*!40000 ALTER TABLE `omcollections` DISABLE KEYS */;
INSERT INTO `omcollections` VALUES (1,'boston uni','test_collcode','test collection',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Preserved Specimens','Snapshot',0,'276b528c-48f6-4c36-b8f9-e80686e7071b','989da66d-5eb0-4364-82ea-7e733bbb1a8a',NULL,NULL,'http://creativecommons.org/publicdomain/zero/1.0/',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2022-11-12 00:06:35'),(2,'HUH','','Harvard test data',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Preserved Specimens','Snapshot',0,'8e3a2540-4dc9-4f6c-88d9-af2ca2ab3743','e64870b2-93b2-4a7e-8415-6d80980e317b','occurrenceId',NULL,'http://creativecommons.org/publicdomain/zero/1.0/',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2023-04-12 15:41:32');
/*!40000 ALTER TABLE `omcollections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omcollectionstats`
--

DROP TABLE IF EXISTS `omcollectionstats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omcollectionstats` (
  `collid` int(10) unsigned NOT NULL,
  `recordcnt` int(10) unsigned NOT NULL DEFAULT '0',
  `georefcnt` int(10) unsigned DEFAULT NULL,
  `familycnt` int(10) unsigned DEFAULT NULL,
  `genuscnt` int(10) unsigned DEFAULT NULL,
  `speciescnt` int(10) unsigned DEFAULT NULL,
  `uploaddate` datetime DEFAULT NULL,
  `datelastmodified` datetime DEFAULT NULL,
  `uploadedby` varchar(45) DEFAULT NULL,
  `dynamicProperties` longtext,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`collid`),
  CONSTRAINT `FK_collectionstats_coll` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omcollectionstats`
--

LOCK TABLES `omcollectionstats` WRITE;
/*!40000 ALTER TABLE `omcollectionstats` DISABLE KEYS */;
INSERT INTO `omcollectionstats` VALUES (1,26,NULL,NULL,NULL,NULL,NULL,NULL,'admin',NULL,'2022-11-12 00:06:35'),(2,0,NULL,NULL,NULL,NULL,NULL,NULL,'admin',NULL,'2023-04-12 15:41:32');
/*!40000 ALTER TABLE `omcollectionstats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omcollectors`
--

DROP TABLE IF EXISTS `omcollectors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omcollectors` (
  `recordedById` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `familyname` varchar(45) NOT NULL,
  `firstname` varchar(45) DEFAULT NULL,
  `middlename` varchar(45) DEFAULT NULL,
  `startyearactive` int(11) DEFAULT NULL,
  `endyearactive` int(11) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `rating` int(11) DEFAULT '10',
  `guid` varchar(45) DEFAULT NULL,
  `preferredrecbyid` int(10) unsigned DEFAULT NULL,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`recordedById`),
  KEY `fullname` (`familyname`,`firstname`),
  KEY `FK_preferred_recby_idx` (`preferredrecbyid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omcollectors`
--

LOCK TABLES `omcollectors` WRITE;
/*!40000 ALTER TABLE `omcollectors` DISABLE KEYS */;
/*!40000 ALTER TABLE `omcollectors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omcollpublications`
--

DROP TABLE IF EXISTS `omcollpublications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omcollpublications` (
  `pubid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `collid` int(10) unsigned NOT NULL,
  `targeturl` varchar(250) NOT NULL,
  `securityguid` varchar(45) NOT NULL,
  `criteriajson` varchar(250) DEFAULT NULL,
  `includedeterminations` int(11) DEFAULT '1',
  `includeimages` int(11) DEFAULT '1',
  `autoupdate` int(11) DEFAULT '0',
  `lastdateupdate` datetime DEFAULT NULL,
  `updateinterval` int(11) DEFAULT NULL,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pubid`),
  KEY `FK_adminpub_collid_idx` (`collid`),
  CONSTRAINT `FK_adminpub_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omcollpublications`
--

LOCK TABLES `omcollpublications` WRITE;
/*!40000 ALTER TABLE `omcollpublications` DISABLE KEYS */;
/*!40000 ALTER TABLE `omcollpublications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omcollpuboccurlink`
--

DROP TABLE IF EXISTS `omcollpuboccurlink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omcollpuboccurlink` (
  `pubid` int(10) unsigned NOT NULL,
  `occid` int(10) unsigned NOT NULL,
  `verification` int(11) NOT NULL DEFAULT '0',
  `refreshtimestamp` datetime NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pubid`,`occid`),
  KEY `FK_ompuboccid_idx` (`occid`),
  CONSTRAINT `FK_ompuboccid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ompubpubid` FOREIGN KEY (`pubid`) REFERENCES `omcollpublications` (`pubid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omcollpuboccurlink`
--

LOCK TABLES `omcollpuboccurlink` WRITE;
/*!40000 ALTER TABLE `omcollpuboccurlink` DISABLE KEYS */;
/*!40000 ALTER TABLE `omcollpuboccurlink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omcollsecondary`
--

DROP TABLE IF EXISTS `omcollsecondary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omcollsecondary` (
  `ocsid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `collid` int(10) unsigned NOT NULL,
  `InstitutionCode` varchar(45) NOT NULL,
  `CollectionCode` varchar(45) DEFAULT NULL,
  `CollectionName` varchar(150) NOT NULL,
  `BriefDescription` varchar(300) DEFAULT NULL,
  `FullDescription` varchar(1000) DEFAULT NULL,
  `Homepage` varchar(250) DEFAULT NULL,
  `IndividualUrl` varchar(500) DEFAULT NULL,
  `Contact` varchar(45) DEFAULT NULL,
  `Email` varchar(45) DEFAULT NULL,
  `LatitudeDecimal` double DEFAULT NULL,
  `LongitudeDecimal` double DEFAULT NULL,
  `icon` varchar(250) DEFAULT NULL,
  `CollType` varchar(45) DEFAULT NULL,
  `SortSeq` int(10) unsigned DEFAULT NULL,
  `InitialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ocsid`),
  KEY `FK_omcollsecondary_coll` (`collid`),
  CONSTRAINT `FK_omcollsecondary_coll` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omcollsecondary`
--

LOCK TABLES `omcollsecondary` WRITE;
/*!40000 ALTER TABLE `omcollsecondary` DISABLE KEYS */;
/*!40000 ALTER TABLE `omcollsecondary` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omcrowdsourcecentral`
--

DROP TABLE IF EXISTS `omcrowdsourcecentral`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omcrowdsourcecentral` (
  `omcsid` int(11) NOT NULL AUTO_INCREMENT,
  `collid` int(10) unsigned NOT NULL,
  `instructions` text,
  `trainingurl` varchar(500) DEFAULT NULL,
  `editorlevel` int(11) NOT NULL DEFAULT '0' COMMENT '0=public, 1=public limited, 2=private',
  `notes` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`omcsid`),
  UNIQUE KEY `Index_omcrowdsourcecentral_collid` (`collid`),
  KEY `FK_omcrowdsourcecentral_collid` (`collid`),
  CONSTRAINT `FK_omcrowdsourcecentral_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omcrowdsourcecentral`
--

LOCK TABLES `omcrowdsourcecentral` WRITE;
/*!40000 ALTER TABLE `omcrowdsourcecentral` DISABLE KEYS */;
/*!40000 ALTER TABLE `omcrowdsourcecentral` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omcrowdsourcequeue`
--

DROP TABLE IF EXISTS `omcrowdsourcequeue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omcrowdsourcequeue` (
  `idomcrowdsourcequeue` int(11) NOT NULL AUTO_INCREMENT,
  `omcsid` int(11) NOT NULL,
  `occid` int(10) unsigned NOT NULL,
  `reviewstatus` int(11) NOT NULL DEFAULT '0' COMMENT '0=open,5=pending review, 10=closed',
  `uidprocessor` int(10) unsigned DEFAULT NULL,
  `points` int(11) DEFAULT NULL COMMENT '0=fail, 1=minor edits, 2=no edits <default>, 3=excelled',
  `isvolunteer` int(2) NOT NULL DEFAULT '1',
  `notes` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idomcrowdsourcequeue`),
  UNIQUE KEY `Index_omcrowdsource_occid` (`occid`),
  KEY `FK_omcrowdsourcequeue_occid` (`occid`),
  KEY `FK_omcrowdsourcequeue_uid` (`uidprocessor`),
  CONSTRAINT `FK_omcrowdsourcequeue_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_omcrowdsourcequeue_uid` FOREIGN KEY (`uidprocessor`) REFERENCES `users` (`uid`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omcrowdsourcequeue`
--

LOCK TABLES `omcrowdsourcequeue` WRITE;
/*!40000 ALTER TABLE `omcrowdsourcequeue` DISABLE KEYS */;
/*!40000 ALTER TABLE `omcrowdsourcequeue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omexsiccatinumbers`
--

DROP TABLE IF EXISTS `omexsiccatinumbers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omexsiccatinumbers` (
  `omenid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `exsnumber` varchar(45) NOT NULL,
  `ometid` int(10) unsigned NOT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`omenid`),
  UNIQUE KEY `Index_omexsiccatinumbers_unique` (`exsnumber`,`ometid`),
  KEY `FK_exsiccatiTitleNumber` (`ometid`),
  CONSTRAINT `FK_exsiccatiTitleNumber` FOREIGN KEY (`ometid`) REFERENCES `omexsiccatititles` (`ometid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omexsiccatinumbers`
--

LOCK TABLES `omexsiccatinumbers` WRITE;
/*!40000 ALTER TABLE `omexsiccatinumbers` DISABLE KEYS */;
/*!40000 ALTER TABLE `omexsiccatinumbers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omexsiccatiocclink`
--

DROP TABLE IF EXISTS `omexsiccatiocclink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omexsiccatiocclink` (
  `omenid` int(10) unsigned NOT NULL,
  `occid` int(10) unsigned NOT NULL,
  `ranking` int(11) NOT NULL DEFAULT '50',
  `notes` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`omenid`,`occid`),
  UNIQUE KEY `UniqueOmexsiccatiOccLink` (`occid`),
  KEY `FKExsiccatiNumOccLink1` (`omenid`),
  KEY `FKExsiccatiNumOccLink2` (`occid`),
  CONSTRAINT `FKExsiccatiNumOccLink1` FOREIGN KEY (`omenid`) REFERENCES `omexsiccatinumbers` (`omenid`) ON DELETE CASCADE,
  CONSTRAINT `FKExsiccatiNumOccLink2` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omexsiccatiocclink`
--

LOCK TABLES `omexsiccatiocclink` WRITE;
/*!40000 ALTER TABLE `omexsiccatiocclink` DISABLE KEYS */;
/*!40000 ALTER TABLE `omexsiccatiocclink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omexsiccatititles`
--

DROP TABLE IF EXISTS `omexsiccatititles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omexsiccatititles` (
  `ometid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `abbreviation` varchar(100) DEFAULT NULL,
  `editor` varchar(150) DEFAULT NULL,
  `exsrange` varchar(45) DEFAULT NULL,
  `startdate` varchar(45) DEFAULT NULL,
  `enddate` varchar(45) DEFAULT NULL,
  `source` varchar(250) DEFAULT NULL,
  `sourceIdentifier` varchar(150) DEFAULT NULL,
  `notes` varchar(2000) DEFAULT NULL,
  `lasteditedby` varchar(45) DEFAULT NULL,
  `recordID` varchar(45) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ometid`),
  KEY `index_exsiccatiTitle` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omexsiccatititles`
--

LOCK TABLES `omexsiccatititles` WRITE;
/*!40000 ALTER TABLE `omexsiccatititles` DISABLE KEYS */;
/*!40000 ALTER TABLE `omexsiccatititles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omoccuraccessstats`
--

DROP TABLE IF EXISTS `omoccuraccessstats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccuraccessstats` (
  `oasid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `accessdate` date NOT NULL,
  `ipaddress` varchar(45) NOT NULL,
  `cnt` int(10) unsigned NOT NULL,
  `accesstype` varchar(45) NOT NULL,
  `dynamicProperties` varchar(250) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`oasid`),
  UNIQUE KEY `UNIQUE_occuraccess` (`occid`,`accessdate`,`ipaddress`,`accesstype`),
  CONSTRAINT `FK_occuraccess_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccuraccessstats`
--

LOCK TABLES `omoccuraccessstats` WRITE;
/*!40000 ALTER TABLE `omoccuraccessstats` DISABLE KEYS */;
/*!40000 ALTER TABLE `omoccuraccessstats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omoccurassociations`
--

DROP TABLE IF EXISTS `omoccurassociations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurassociations` (
  `associd` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `occidAssociate` int(10) unsigned DEFAULT NULL,
  `relationship` varchar(150) NOT NULL,
  `subType` varchar(45) DEFAULT NULL,
  `identifier` varchar(250) DEFAULT NULL COMMENT 'e.g. GUID',
  `basisOfRecord` varchar(45) DEFAULT NULL,
  `resourceUrl` varchar(250) DEFAULT NULL,
  `verbatimSciname` varchar(250) DEFAULT NULL,
  `tid` int(11) unsigned DEFAULT NULL,
  `locationOnHost` varchar(250) DEFAULT NULL,
  `condition` varchar(250) DEFAULT NULL,
  `dateEmerged` datetime DEFAULT NULL,
  `imageMapJSON` text,
  `dynamicProperties` text,
  `notes` varchar(250) DEFAULT NULL,
  `createdUid` int(10) unsigned DEFAULT NULL,
  `modifiedTimestamp` datetime DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`associd`),
  UNIQUE KEY `UQ_omoccurassoc_occid` (`occid`,`occidAssociate`,`relationship`),
  UNIQUE KEY `UQ_omoccurassoc_external` (`occid`,`relationship`,`resourceUrl`),
  UNIQUE KEY `UQ_omoccurassoc_sciname` (`occid`,`verbatimSciname`),
  KEY `omossococcur_occid_idx` (`occid`),
  KEY `omossococcur_occidassoc_idx` (`occidAssociate`),
  KEY `FK_occurassoc_tid_idx` (`tid`),
  KEY `FK_occurassoc_uidmodified_idx` (`modifiedUid`),
  KEY `FK_occurassoc_uidcreated_idx` (`createdUid`),
  KEY `INDEX_verbatimSciname` (`verbatimSciname`),
  CONSTRAINT `FK_occurassoc_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_occurassoc_occidassoc` FOREIGN KEY (`occidAssociate`) REFERENCES `omoccurrences` (`occid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_occurassoc_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_occurassoc_uidcreated` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_occurassoc_uidmodified` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccurassociations`
--

LOCK TABLES `omoccurassociations` WRITE;
/*!40000 ALTER TABLE `omoccurassociations` DISABLE KEYS */;
/*!40000 ALTER TABLE `omoccurassociations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omoccurcomments`
--

DROP TABLE IF EXISTS `omoccurcomments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurcomments` (
  `comid` int(11) NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `comment` text NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `reviewstatus` int(10) unsigned NOT NULL DEFAULT '0',
  `parentcomid` int(10) unsigned DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`comid`),
  KEY `fk_omoccurcomments_occid` (`occid`),
  KEY `fk_omoccurcomments_uid` (`uid`),
  CONSTRAINT `fk_omoccurcomments_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_omoccurcomments_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccurcomments`
--

LOCK TABLES `omoccurcomments` WRITE;
/*!40000 ALTER TABLE `omoccurcomments` DISABLE KEYS */;
/*!40000 ALTER TABLE `omoccurcomments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omoccurdatasetlink`
--

DROP TABLE IF EXISTS `omoccurdatasetlink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurdatasetlink` (
  `occid` int(10) unsigned NOT NULL,
  `datasetid` int(10) unsigned NOT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`occid`,`datasetid`),
  KEY `FK_omoccurdatasetlink_datasetid` (`datasetid`),
  KEY `FK_omoccurdatasetlink_occid` (`occid`),
  CONSTRAINT `FK_omoccurdatasetlink_datasetid` FOREIGN KEY (`datasetid`) REFERENCES `omoccurdatasets` (`datasetid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurdatasetlink_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccurdatasetlink`
--

LOCK TABLES `omoccurdatasetlink` WRITE;
/*!40000 ALTER TABLE `omoccurdatasetlink` DISABLE KEYS */;
/*!40000 ALTER TABLE `omoccurdatasetlink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omoccurdatasets`
--

DROP TABLE IF EXISTS `omoccurdatasets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurdatasets` (
  `datasetid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `category` varchar(45) DEFAULT NULL,
  `isPublic` int(11) DEFAULT NULL,
  `parentDatasetID` int(10) unsigned DEFAULT NULL,
  `includeInSearch` int(11) DEFAULT NULL,
  `description` text,
  `notes` varchar(250) DEFAULT NULL,
  `sortsequence` int(11) DEFAULT NULL,
  `uid` int(11) unsigned DEFAULT NULL,
  `collid` int(10) unsigned DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`datasetid`),
  KEY `FK_omoccurdatasets_uid_idx` (`uid`),
  KEY `FK_omcollections_collid_idx` (`collid`),
  KEY `FK_omoccurdatasets_parent_idx` (`parentDatasetID`),
  CONSTRAINT `FK_omcollections_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurdatasets_parent` FOREIGN KEY (`parentDatasetID`) REFERENCES `omoccurdatasets` (`datasetid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurdatasets_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccurdatasets`
--

LOCK TABLES `omoccurdatasets` WRITE;
/*!40000 ALTER TABLE `omoccurdatasets` DISABLE KEYS */;
INSERT INTO `omoccurdatasets` VALUES (1,'test',NULL,1,NULL,NULL,'<p>sdfsd</p>',NULL,NULL,1,NULL,'2022-11-12 00:03:10');
/*!40000 ALTER TABLE `omoccurdatasets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omoccurdeterminations`
--

DROP TABLE IF EXISTS `omoccurdeterminations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurdeterminations` (
  `detid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `identifiedBy` varchar(60) NOT NULL,
  `idbyid` int(10) unsigned DEFAULT NULL,
  `dateIdentified` varchar(45) NOT NULL,
  `dateIdentifiedInterpreted` date DEFAULT NULL,
  `family` varchar(150) DEFAULT NULL,
  `sciname` varchar(100) NOT NULL,
  `scientificNameAuthorship` varchar(100) DEFAULT NULL,
  `tidInterpreted` int(10) unsigned DEFAULT NULL,
  `identificationQualifier` varchar(45) DEFAULT NULL,
  `isCurrent` int(2) DEFAULT '0',
  `printQueue` int(2) DEFAULT '0',
  `appliedStatus` int(2) DEFAULT '1',
  `detType` varchar(45) DEFAULT NULL,
  `identificationReferences` varchar(255) DEFAULT NULL,
  `identificationRemarks` varchar(500) DEFAULT NULL,
  `taxonRemarks` varchar(45) DEFAULT NULL,
  `sourceIdentifier` varchar(45) DEFAULT NULL,
  `sortSequence` int(10) unsigned DEFAULT '10',
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`detid`),
  UNIQUE KEY `UQ_omoccurdets_unique` (`occid`,`dateIdentified`,`identifiedBy`,`sciname`),
  KEY `FK_omoccurdets_tid` (`tidInterpreted`),
  KEY `FK_omoccurdets_idby_idx` (`idbyid`),
  KEY `IX_omoccurdets_dateIdInterpreted` (`dateIdentifiedInterpreted`),
  KEY `IX_omoccurdets_sciname` (`sciname`),
  KEY `IX_omoccurdets_family` (`family`),
  KEY `IX_omoccurdets_isCurrent` (`isCurrent`),
  CONSTRAINT `FK_omoccurdets_idby` FOREIGN KEY (`idbyid`) REFERENCES `omcollectors` (`recordedById`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `FK_omoccurdets_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurdets_tid` FOREIGN KEY (`tidInterpreted`) REFERENCES `taxa` (`TID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccurdeterminations`
--

LOCK TABLES `omoccurdeterminations` WRITE;
/*!40000 ALTER TABLE `omoccurdeterminations` DISABLE KEYS */;
/*!40000 ALTER TABLE `omoccurdeterminations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omoccurduplicatelink`
--

DROP TABLE IF EXISTS `omoccurduplicatelink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurduplicatelink` (
  `occid` int(10) unsigned NOT NULL,
  `duplicateid` int(11) NOT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `modifiedtimestamp` datetime DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`occid`,`duplicateid`),
  KEY `FK_omoccurdupelink_occid_idx` (`occid`),
  KEY `FK_omoccurdupelink_dupeid_idx` (`duplicateid`),
  CONSTRAINT `FK_omoccurdupelink_dupeid` FOREIGN KEY (`duplicateid`) REFERENCES `omoccurduplicates` (`duplicateid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurdupelink_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccurduplicatelink`
--

LOCK TABLES `omoccurduplicatelink` WRITE;
/*!40000 ALTER TABLE `omoccurduplicatelink` DISABLE KEYS */;
/*!40000 ALTER TABLE `omoccurduplicatelink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omoccurduplicates`
--

DROP TABLE IF EXISTS `omoccurduplicates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurduplicates` (
  `duplicateid` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `dupeType` varchar(45) NOT NULL DEFAULT 'Exact Duplicate',
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`duplicateid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccurduplicates`
--

LOCK TABLES `omoccurduplicates` WRITE;
/*!40000 ALTER TABLE `omoccurduplicates` DISABLE KEYS */;
/*!40000 ALTER TABLE `omoccurduplicates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omoccureditlocks`
--

DROP TABLE IF EXISTS `omoccureditlocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccureditlocks` (
  `occid` int(10) unsigned NOT NULL,
  `uid` int(11) NOT NULL,
  `ts` int(11) NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`occid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccureditlocks`
--

LOCK TABLES `omoccureditlocks` WRITE;
/*!40000 ALTER TABLE `omoccureditlocks` DISABLE KEYS */;
INSERT INTO `omoccureditlocks` VALUES (4,1,1682708431,'2023-04-28 19:00:31');
/*!40000 ALTER TABLE `omoccureditlocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omoccuredits`
--

DROP TABLE IF EXISTS `omoccuredits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccuredits` (
  `ocedid` int(11) NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `FieldName` varchar(45) NOT NULL,
  `FieldValueNew` text NOT NULL,
  `FieldValueOld` text NOT NULL,
  `ReviewStatus` int(1) NOT NULL DEFAULT '1' COMMENT '1=Open;2=Pending;3=Closed',
  `AppliedStatus` int(1) NOT NULL DEFAULT '0' COMMENT '0=Not Applied;1=Applied',
  `editType` int(11) DEFAULT '0' COMMENT '0 = general edit, 1 = batch edit',
  `guid` varchar(45) DEFAULT NULL,
  `uid` int(10) unsigned NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ocedid`),
  UNIQUE KEY `guid_UNIQUE` (`guid`),
  KEY `fk_omoccuredits_uid` (`uid`),
  KEY `fk_omoccuredits_occid` (`occid`),
  CONSTRAINT `fk_omoccuredits_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_omoccuredits_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccuredits`
--

LOCK TABLES `omoccuredits` WRITE;
/*!40000 ALTER TABLE `omoccuredits` DISABLE KEYS */;
INSERT INTO `omoccuredits` VALUES (1,6,'identifiedby','W. Vink','',1,1,0,NULL,1,'2023-04-12 19:19:46'),(2,5,'catalognumber','Catalog','',1,1,0,NULL,1,'2023-04-14 19:34:31'),(3,5,'dateidentified','2022-04-03','',1,1,0,NULL,1,'2023-04-28 17:51:12'),(4,5,'verbatimattributes','Describe','',1,1,0,NULL,1,'2023-04-28 17:51:12'),(5,5,'verbatimattributes','describe','',1,1,0,NULL,1,'2023-04-28 17:51:58'),(6,5,'verbatimattributes','describe','',1,1,0,NULL,1,'2023-04-28 18:01:51'),(7,4,'labelproject','test','',1,1,0,NULL,1,'2023-04-28 18:56:17'),(8,4,'labelproject','test','',1,1,0,NULL,1,'2023-04-28 18:56:45'),(9,4,'labelproject','test','',1,1,0,NULL,1,'2023-04-28 18:57:41'),(10,4,'labelproject','test','',1,1,0,NULL,1,'2023-04-28 18:57:59'),(11,4,'labelproject','test','',1,1,0,NULL,1,'2023-04-28 19:00:04'),(12,4,'labelproject','test','',1,1,0,NULL,1,'2023-04-28 19:00:12');
/*!40000 ALTER TABLE `omoccuredits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omoccurexchange`
--

DROP TABLE IF EXISTS `omoccurexchange`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurexchange` (
  `exchangeid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `identifier` varchar(30) DEFAULT NULL,
  `collid` int(10) unsigned DEFAULT NULL,
  `iid` int(10) unsigned DEFAULT NULL,
  `transactionType` varchar(10) DEFAULT NULL,
  `in_out` varchar(3) DEFAULT NULL,
  `dateSent` date DEFAULT NULL,
  `dateReceived` date DEFAULT NULL,
  `totalBoxes` int(5) DEFAULT NULL,
  `shippingMethod` varchar(50) DEFAULT NULL,
  `totalExMounted` int(5) DEFAULT NULL,
  `totalExUnmounted` int(5) DEFAULT NULL,
  `totalGift` int(5) DEFAULT NULL,
  `totalGiftDet` int(5) DEFAULT NULL,
  `adjustment` int(5) DEFAULT NULL,
  `invoiceBalance` int(6) DEFAULT NULL,
  `invoiceMessage` varchar(500) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `notes` varchar(500) DEFAULT NULL,
  `createdBy` varchar(20) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`exchangeid`),
  KEY `FK_occexch_coll` (`collid`),
  CONSTRAINT `FK_occexch_coll` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccurexchange`
--

LOCK TABLES `omoccurexchange` WRITE;
/*!40000 ALTER TABLE `omoccurexchange` DISABLE KEYS */;
/*!40000 ALTER TABLE `omoccurexchange` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omoccurgenetic`
--

DROP TABLE IF EXISTS `omoccurgenetic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurgenetic` (
  `idoccurgenetic` int(11) NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `identifier` varchar(150) DEFAULT NULL,
  `resourcename` varchar(150) NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `locus` varchar(500) DEFAULT NULL,
  `resourceurl` varchar(500) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idoccurgenetic`),
  UNIQUE KEY `UNIQUE_omoccurgenetic` (`occid`,`resourceurl`),
  KEY `FK_omoccurgenetic` (`occid`),
  KEY `INDEX_omoccurgenetic_name` (`resourcename`),
  CONSTRAINT `FK_omoccurgenetic` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccurgenetic`
--

LOCK TABLES `omoccurgenetic` WRITE;
/*!40000 ALTER TABLE `omoccurgenetic` DISABLE KEYS */;
/*!40000 ALTER TABLE `omoccurgenetic` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omoccurgeoindex`
--

DROP TABLE IF EXISTS `omoccurgeoindex`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurgeoindex` (
  `tid` int(10) unsigned NOT NULL,
  `decimallatitude` double NOT NULL,
  `decimallongitude` double NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tid`,`decimallatitude`,`decimallongitude`),
  CONSTRAINT `FK_specgeoindex_taxa` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccurgeoindex`
--

LOCK TABLES `omoccurgeoindex` WRITE;
/*!40000 ALTER TABLE `omoccurgeoindex` DISABLE KEYS */;
/*!40000 ALTER TABLE `omoccurgeoindex` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omoccuridentifiers`
--

DROP TABLE IF EXISTS `omoccuridentifiers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccuridentifiers` (
  `idomoccuridentifiers` int(11) NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `identifiervalue` varchar(45) NOT NULL,
  `identifiername` varchar(45) DEFAULT NULL COMMENT 'barcode, accession number, old catalog number, NPS, etc',
  `notes` varchar(250) DEFAULT NULL,
  `sortBy` int(11) DEFAULT NULL,
  `modifiedUid` int(10) unsigned NOT NULL,
  `modifiedtimestamp` datetime DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idomoccuridentifiers`),
  KEY `FK_omoccuridentifiers_occid_idx` (`occid`),
  KEY `Index_value` (`identifiervalue`),
  CONSTRAINT `FK_omoccuridentifiers_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccuridentifiers`
--

LOCK TABLES `omoccuridentifiers` WRITE;
/*!40000 ALTER TABLE `omoccuridentifiers` DISABLE KEYS */;
INSERT INTO `omoccuridentifiers` VALUES (1,5,'Additional Identifier Value','Tag Name (optional)',NULL,NULL,1,NULL,'2023-04-02 19:45:25');
/*!40000 ALTER TABLE `omoccuridentifiers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omoccurloans`
--

DROP TABLE IF EXISTS `omoccurloans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurloans` (
  `loanid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `loanIdentifierOwn` varchar(30) DEFAULT NULL,
  `loanIdentifierBorr` varchar(30) DEFAULT NULL,
  `collidOwn` int(10) unsigned DEFAULT NULL,
  `collidBorr` int(10) unsigned DEFAULT NULL,
  `iidOwner` int(10) unsigned DEFAULT NULL,
  `iidBorrower` int(10) unsigned DEFAULT NULL,
  `dateSent` date DEFAULT NULL,
  `dateSentReturn` date DEFAULT NULL,
  `receivedStatus` varchar(250) DEFAULT NULL,
  `totalBoxes` int(5) DEFAULT NULL,
  `totalBoxesReturned` int(5) DEFAULT NULL,
  `numSpecimens` int(5) DEFAULT NULL,
  `shippingMethod` varchar(50) DEFAULT NULL,
  `shippingMethodReturn` varchar(50) DEFAULT NULL,
  `dateDue` date DEFAULT NULL,
  `dateReceivedOwn` date DEFAULT NULL,
  `dateReceivedBorr` date DEFAULT NULL,
  `dateClosed` date DEFAULT NULL,
  `forWhom` varchar(50) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `invoiceMessageOwn` varchar(500) DEFAULT NULL,
  `invoiceMessageBorr` varchar(500) DEFAULT NULL,
  `notes` varchar(500) DEFAULT NULL,
  `createdByOwn` varchar(30) DEFAULT NULL,
  `createdByBorr` varchar(30) DEFAULT NULL,
  `processingStatus` int(5) unsigned DEFAULT '1',
  `processedByOwn` varchar(30) DEFAULT NULL,
  `processedByBorr` varchar(30) DEFAULT NULL,
  `processedByReturnOwn` varchar(30) DEFAULT NULL,
  `processedByReturnBorr` varchar(30) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`loanid`),
  KEY `FK_occurloans_owninst` (`iidOwner`),
  KEY `FK_occurloans_borrinst` (`iidBorrower`),
  KEY `FK_occurloans_owncoll` (`collidOwn`),
  KEY `FK_occurloans_borrcoll` (`collidBorr`),
  CONSTRAINT `FK_occurloans_borrcoll` FOREIGN KEY (`collidBorr`) REFERENCES `omcollections` (`CollID`) ON UPDATE CASCADE,
  CONSTRAINT `FK_occurloans_borrinst` FOREIGN KEY (`iidBorrower`) REFERENCES `institutions` (`iid`) ON UPDATE CASCADE,
  CONSTRAINT `FK_occurloans_owncoll` FOREIGN KEY (`collidOwn`) REFERENCES `omcollections` (`CollID`) ON UPDATE CASCADE,
  CONSTRAINT `FK_occurloans_owninst` FOREIGN KEY (`iidOwner`) REFERENCES `institutions` (`iid`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccurloans`
--

LOCK TABLES `omoccurloans` WRITE;
/*!40000 ALTER TABLE `omoccurloans` DISABLE KEYS */;
/*!40000 ALTER TABLE `omoccurloans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omoccurloanslink`
--

DROP TABLE IF EXISTS `omoccurloanslink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurloanslink` (
  `loanid` int(10) unsigned NOT NULL,
  `occid` int(10) unsigned NOT NULL,
  `returndate` date DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`loanid`,`occid`),
  KEY `FK_occurloanlink_occid` (`occid`),
  KEY `FK_occurloanlink_loanid` (`loanid`),
  CONSTRAINT `FK_occurloanlink_loanid` FOREIGN KEY (`loanid`) REFERENCES `omoccurloans` (`loanid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_occurloanlink_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccurloanslink`
--

LOCK TABLES `omoccurloanslink` WRITE;
/*!40000 ALTER TABLE `omoccurloanslink` DISABLE KEYS */;
/*!40000 ALTER TABLE `omoccurloanslink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omoccurloanuser`
--

DROP TABLE IF EXISTS `omoccurloanuser`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurloanuser` (
  `loanid` int(10) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `accessType` varchar(45) NOT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `modifiedByUid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`loanid`,`uid`),
  KEY `FK_occurloan_uid_idx` (`uid`),
  KEY `FK_occurloan_modifiedByUid_idx` (`modifiedByUid`),
  CONSTRAINT `FK_occurloan_loanid` FOREIGN KEY (`loanid`) REFERENCES `omoccurloans` (`loanid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_occurloan_modifiedByUid` FOREIGN KEY (`modifiedByUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_occurloan_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccurloanuser`
--

LOCK TABLES `omoccurloanuser` WRITE;
/*!40000 ALTER TABLE `omoccurloanuser` DISABLE KEYS */;
/*!40000 ALTER TABLE `omoccurloanuser` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omoccurpaleo`
--

DROP TABLE IF EXISTS `omoccurpaleo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurpaleo` (
  `paleoID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `eon` varchar(65) DEFAULT NULL,
  `era` varchar(65) DEFAULT NULL,
  `period` varchar(65) DEFAULT NULL,
  `epoch` varchar(65) DEFAULT NULL,
  `earlyInterval` varchar(65) DEFAULT NULL,
  `lateInterval` varchar(65) DEFAULT NULL,
  `absoluteAge` varchar(65) DEFAULT NULL,
  `storageAge` varchar(65) DEFAULT NULL,
  `stage` varchar(65) DEFAULT NULL,
  `localStage` varchar(65) DEFAULT NULL,
  `biota` varchar(65) DEFAULT NULL COMMENT 'Flora or Fanua',
  `biostratigraphy` varchar(65) DEFAULT NULL COMMENT 'Biozone',
  `taxonEnvironment` varchar(65) DEFAULT NULL COMMENT 'Marine or not',
  `lithogroup` varchar(65) DEFAULT NULL,
  `formation` varchar(65) DEFAULT NULL,
  `member` varchar(65) DEFAULT NULL,
  `bed` varchar(65) DEFAULT NULL,
  `lithology` varchar(250) DEFAULT NULL,
  `stratRemarks` varchar(250) DEFAULT NULL,
  `element` varchar(250) DEFAULT NULL,
  `slideProperties` varchar(1000) DEFAULT NULL,
  `geologicalContextID` varchar(45) DEFAULT NULL,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`paleoID`),
  UNIQUE KEY `UNIQUE_occid` (`occid`),
  KEY `FK_paleo_occid_idx` (`occid`),
  CONSTRAINT `FK_paleo_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Occurrence Paleo tables';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccurpaleo`
--

LOCK TABLES `omoccurpaleo` WRITE;
/*!40000 ALTER TABLE `omoccurpaleo` DISABLE KEYS */;
/*!40000 ALTER TABLE `omoccurpaleo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omoccurpaleogts`
--

DROP TABLE IF EXISTS `omoccurpaleogts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurpaleogts` (
  `gtsid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gtsterm` varchar(45) NOT NULL,
  `rankid` int(11) NOT NULL,
  `rankname` varchar(45) DEFAULT NULL,
  `parentgtsid` int(10) unsigned DEFAULT NULL,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`gtsid`),
  UNIQUE KEY `UNIQUE_gtsterm` (`gtsid`),
  KEY `FK_gtsparent_idx` (`parentgtsid`),
  CONSTRAINT `FK_gtsparent` FOREIGN KEY (`parentgtsid`) REFERENCES `omoccurpaleogts` (`gtsid`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=205 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccurpaleogts`
--

LOCK TABLES `omoccurpaleogts` WRITE;
/*!40000 ALTER TABLE `omoccurpaleogts` DISABLE KEYS */;
INSERT INTO `omoccurpaleogts` VALUES (1,'Precambrian',10,'supereon',NULL,'2022-10-18 23:21:45'),(2,'Archean',20,'eon',1,'2022-10-18 23:21:45'),(3,'Hadean',20,'eon',1,'2022-10-18 23:21:45'),(4,'Phanerozoic',20,'eon',1,'2022-10-18 23:21:45'),(5,'Proterozoic',20,'eon',1,'2022-10-18 23:21:45'),(9,'Eoarchean',30,'era',2,'2022-10-18 23:21:45'),(10,'Paleoarchean',30,'era',2,'2022-10-18 23:21:45'),(11,'Mesoarchean',30,'era',2,'2022-10-18 23:21:45'),(12,'Neoarchean',30,'era',2,'2022-10-18 23:21:45'),(13,'Paleozoic',30,'era',4,'2022-10-18 23:21:45'),(14,'Mesozoic',30,'era',4,'2022-10-18 23:21:45'),(15,'Cenozoic',30,'era',4,'2022-10-18 23:21:45'),(16,'Paleoproterozoic',30,'era',5,'2022-10-18 23:21:45'),(17,'Mesoproterozoic',30,'era',5,'2022-10-18 23:21:45'),(18,'Neoproterozoic',30,'era',5,'2022-10-18 23:21:45'),(24,'Cambrian',40,'period',13,'2022-10-18 23:21:45'),(25,'Ordovician',40,'period',13,'2022-10-18 23:21:45'),(26,'Silurian',40,'period',13,'2022-10-18 23:21:45'),(27,'Devonian',40,'period',13,'2022-10-18 23:21:45'),(28,'Carboniferous',40,'period',13,'2022-10-18 23:21:45'),(29,'Permian',40,'period',13,'2022-10-18 23:21:45'),(30,'Triassic',40,'period',14,'2022-10-18 23:21:45'),(31,'Jurassic',40,'period',14,'2022-10-18 23:21:45'),(32,'Cretaceous',40,'period',14,'2022-10-18 23:21:45'),(33,'Paleogene',40,'period',15,'2022-10-18 23:21:45'),(34,'Neogene',40,'period',15,'2022-10-18 23:21:45'),(35,'Quaternary',40,'period',15,'2022-10-18 23:21:45'),(36,'Siderian',40,'period',16,'2022-10-18 23:21:45'),(37,'Rhyacian',40,'period',16,'2022-10-18 23:21:45'),(38,'Orosirian',40,'period',16,'2022-10-18 23:21:45'),(39,'Statherian',40,'period',16,'2022-10-18 23:21:45'),(40,'Calymmian',40,'period',17,'2022-10-18 23:21:45'),(41,'Ectasian',40,'period',17,'2022-10-18 23:21:45'),(42,'Stenian',40,'period',17,'2022-10-18 23:21:45'),(43,'Tonian',40,'period',18,'2022-10-18 23:21:45'),(44,'Gryogenian',40,'period',18,'2022-10-18 23:21:45'),(45,'Ediacaran',40,'period',18,'2022-10-18 23:21:45'),(55,'Lower Cambrian',50,'epoch',24,'2022-10-18 23:21:45'),(56,'Middle Cambrian',50,'epoch',24,'2022-10-18 23:21:45'),(57,'Upper Cambrian',50,'epoch',24,'2022-10-18 23:21:45'),(58,'Lower Ordovician',50,'epoch',25,'2022-10-18 23:21:45'),(59,'Middle Ordovician',50,'epoch',25,'2022-10-18 23:21:45'),(60,'Upper Ordivician',50,'epoch',25,'2022-10-18 23:21:45'),(61,'Llandovery',50,'epoch',26,'2022-10-18 23:21:45'),(62,'Wenlock',50,'epoch',26,'2022-10-18 23:21:45'),(63,'Ludlow',50,'epoch',26,'2022-10-18 23:21:45'),(64,'Pridoli',50,'epoch',26,'2022-10-18 23:21:45'),(65,'Lower Devonian',50,'epoch',27,'2022-10-18 23:21:45'),(66,'Middle Devonian',50,'epoch',27,'2022-10-18 23:21:45'),(67,'Upper Devonian',50,'epoch',27,'2022-10-18 23:21:45'),(68,'Mississippian',40,'period',13,'2022-10-18 23:21:45'),(69,'Pennsylvanian',40,'period',13,'2022-10-18 23:21:45'),(70,'Cisuralian',50,'epoch',29,'2022-10-18 23:21:45'),(71,'Guadalupian',50,'epoch',29,'2022-10-18 23:21:45'),(72,'Lopingian',50,'epoch',29,'2022-10-18 23:21:45'),(73,'Lower Triassic',50,'epoch',30,'2022-10-18 23:21:45'),(74,'Middle Triassic',50,'epoch',30,'2022-10-18 23:21:45'),(75,'Upper Triassic',50,'epoch',30,'2022-10-18 23:21:45'),(76,'Lower Jurassic',50,'epoch',31,'2022-10-18 23:21:45'),(77,'Middle Jurassic',50,'epoch',31,'2022-10-18 23:21:45'),(78,'Upper Jurassic',50,'epoch',31,'2022-10-18 23:21:45'),(79,'Lower Cretaceous',50,'epoch',32,'2022-10-18 23:21:45'),(80,'Upper Cretaceous',50,'epoch',32,'2022-10-18 23:21:45'),(81,'Paleocene',50,'epoch',33,'2022-10-18 23:21:45'),(82,'Eocene',50,'epoch',33,'2022-10-18 23:21:45'),(83,'Oligocene',50,'epoch',33,'2022-10-18 23:21:45'),(84,'Miocene',50,'epoch',34,'2022-10-18 23:21:45'),(85,'Pliocene',50,'epoch',34,'2022-10-18 23:21:45'),(86,'Pleistocene',50,'epoch',35,'2022-10-18 23:21:45'),(87,'Holocene',50,'epoch',35,'2022-10-18 23:21:45'),(118,'Tremadocian',60,'age',58,'2022-10-18 23:21:45'),(119,'Floian',60,'age',58,'2022-10-18 23:21:45'),(120,'Dapingian',60,'age',59,'2022-10-18 23:21:45'),(121,'Darriwilian',60,'age',59,'2022-10-18 23:21:45'),(122,'Sandbian',60,'age',60,'2022-10-18 23:21:45'),(123,'Katian',60,'age',60,'2022-10-18 23:21:45'),(124,'Hirnantian',60,'age',60,'2022-10-18 23:21:45'),(125,'Rhuddanian',60,'age',61,'2022-10-18 23:21:45'),(126,'Aeronian',60,'age',61,'2022-10-18 23:21:45'),(127,'Telychian',60,'age',61,'2022-10-18 23:21:45'),(128,'Sheinwoodian',60,'age',62,'2022-10-18 23:21:45'),(129,'Homerian',60,'age',62,'2022-10-18 23:21:45'),(130,'Gorstian',60,'age',63,'2022-10-18 23:21:45'),(131,'Ludfordian',60,'age',63,'2022-10-18 23:21:45'),(132,'Lochkovian',60,'age',65,'2022-10-18 23:21:45'),(133,'Pragian',60,'age',65,'2022-10-18 23:21:45'),(134,'Emsian',60,'age',65,'2022-10-18 23:21:45'),(135,'Eifelian',60,'age',66,'2022-10-18 23:21:45'),(136,'Givetian',60,'age',66,'2022-10-18 23:21:45'),(137,'Frasnian',60,'age',67,'2022-10-18 23:21:45'),(138,'Famennian',60,'age',67,'2022-10-18 23:21:45'),(139,'Lower Mississippian',60,'age',68,'2022-10-18 23:21:45'),(140,'Middle Mississippian',60,'age',68,'2022-10-18 23:21:45'),(141,'Upper Mississippian',60,'age',68,'2022-10-18 23:21:45'),(142,'Lower Pennsylvanian',60,'age',69,'2022-10-18 23:21:45'),(143,'Middle Pennsylvanian',60,'age',69,'2022-10-18 23:21:45'),(144,'Upper Pennsylvanian',60,'age',69,'2022-10-18 23:21:45'),(145,'Asselian',60,'age',70,'2022-10-18 23:21:45'),(146,'Sakmarian',60,'age',70,'2022-10-18 23:21:45'),(147,'Artinskian',60,'age',70,'2022-10-18 23:21:45'),(148,'Kungurian',60,'age',70,'2022-10-18 23:21:45'),(149,'Roadian',60,'age',71,'2022-10-18 23:21:45'),(150,'Wordian',60,'age',71,'2022-10-18 23:21:45'),(151,'Capitanian',60,'age',71,'2022-10-18 23:21:45'),(152,'Wuchiapingian',60,'age',72,'2022-10-18 23:21:45'),(153,'Changhsingian',60,'age',72,'2022-10-18 23:21:45'),(154,'Induan',60,'age',73,'2022-10-18 23:21:45'),(155,'Olenekian',60,'age',73,'2022-10-18 23:21:45'),(156,'Anisian',60,'age',74,'2022-10-18 23:21:45'),(157,'Ladinian',60,'age',74,'2022-10-18 23:21:45'),(158,'Carnian',60,'age',75,'2022-10-18 23:21:45'),(159,'Norian',60,'age',75,'2022-10-18 23:21:45'),(160,'Rhaetian',60,'age',75,'2022-10-18 23:21:45'),(161,'Hettangian',60,'age',76,'2022-10-18 23:21:45'),(162,'Sinemurian',60,'age',76,'2022-10-18 23:21:45'),(163,'Pliensbachian',60,'age',76,'2022-10-18 23:21:45'),(164,'Toarcian',60,'age',76,'2022-10-18 23:21:45'),(165,'Aalenian',60,'age',77,'2022-10-18 23:21:45'),(166,'Bajocian',60,'age',77,'2022-10-18 23:21:45'),(167,'Bathonian',60,'age',77,'2022-10-18 23:21:45'),(168,'Callovian',60,'age',77,'2022-10-18 23:21:45'),(169,'Oxfordian',60,'age',78,'2022-10-18 23:21:45'),(170,'Kimmeridgian',60,'age',78,'2022-10-18 23:21:45'),(171,'Tithonian',60,'age',78,'2022-10-18 23:21:45'),(172,'Berriasian',60,'age',79,'2022-10-18 23:21:45'),(173,'Valanginian',60,'age',79,'2022-10-18 23:21:45'),(174,'Hauterivian',60,'age',79,'2022-10-18 23:21:45'),(175,'Barremian',60,'age',79,'2022-10-18 23:21:45'),(176,'Aptian',60,'age',79,'2022-10-18 23:21:45'),(177,'Albian',60,'age',79,'2022-10-18 23:21:45'),(178,'Cenomanian',60,'age',80,'2022-10-18 23:21:45'),(179,'Turonian',60,'age',80,'2022-10-18 23:21:45'),(180,'Coniacian',60,'age',80,'2022-10-18 23:21:45'),(181,'Santonian',60,'age',80,'2022-10-18 23:21:45'),(182,'Campanian',60,'age',80,'2022-10-18 23:21:45'),(183,'Maastrichtian',60,'age',80,'2022-10-18 23:21:45'),(184,'Danian',60,'age',81,'2022-10-18 23:21:45'),(185,'Selandian',60,'age',81,'2022-10-18 23:21:45'),(186,'Thanetian',60,'age',81,'2022-10-18 23:21:45'),(187,'Ypresian',60,'age',82,'2022-10-18 23:21:45'),(188,'Lutetian',60,'age',82,'2022-10-18 23:21:45'),(189,'Bartonian',60,'age',82,'2022-10-18 23:21:45'),(190,'Priabonian',60,'age',82,'2022-10-18 23:21:45'),(191,'Rupelian',60,'age',83,'2022-10-18 23:21:45'),(192,'Chattian',60,'age',83,'2022-10-18 23:21:45'),(193,'Aquitanian',60,'age',84,'2022-10-18 23:21:45'),(194,'Burdigalian',60,'age',84,'2022-10-18 23:21:45'),(195,'Langhian',60,'age',84,'2022-10-18 23:21:45'),(196,'Serravallian',60,'age',84,'2022-10-18 23:21:45'),(197,'Tortonian',60,'age',84,'2022-10-18 23:21:45'),(198,'Messinian',60,'age',84,'2022-10-18 23:21:45'),(199,'Zanclean',60,'age',85,'2022-10-18 23:21:45'),(200,'Piacenzian',60,'age',85,'2022-10-18 23:21:45'),(201,'Gelasian',60,'age',86,'2022-10-18 23:21:45'),(202,'Calabrian',60,'age',86,'2022-10-18 23:21:45'),(203,'Middle Pleistocene',60,'age',86,'2022-10-18 23:21:45'),(204,'Upper Pleistocene',60,'age',86,'2022-10-18 23:21:45');
/*!40000 ALTER TABLE `omoccurpaleogts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omoccurpoints`
--

DROP TABLE IF EXISTS `omoccurpoints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurpoints` (
  `geoID` int(11) NOT NULL AUTO_INCREMENT,
  `occid` int(11) NOT NULL,
  `point` point NOT NULL,
  `errradiuspoly` polygon DEFAULT NULL,
  `footprintpoly` polygon DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`geoID`),
  UNIQUE KEY `occid` (`occid`),
  SPATIAL KEY `point` (`point`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccurpoints`
--

LOCK TABLES `omoccurpoints` WRITE;
/*!40000 ALTER TABLE `omoccurpoints` DISABLE KEYS */;
INSERT INTO `omoccurpoints` VALUES (1,5,_binary '\0\0\0\0\0\0\0\0\0\0\0\0\0�?\0\0\0\0\0\0\0@',NULL,NULL,'2023-04-02 19:45:25'),(2,6,_binary '\0\0\0\0\0\0\0\0\0\0\0\0\0�?\0\0\0\0\0\0\0@',NULL,NULL,'2023-04-05 16:22:28');
/*!40000 ALTER TABLE `omoccurpoints` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omoccurrences`
--

DROP TABLE IF EXISTS `omoccurrences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurrences` (
  `occid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `collid` int(10) unsigned NOT NULL,
  `dbpk` varchar(150) DEFAULT NULL,
  `basisOfRecord` varchar(32) DEFAULT 'PreservedSpecimen' COMMENT 'PreservedSpecimen, LivingSpecimen, HumanObservation',
  `occurrenceID` varchar(255) DEFAULT NULL COMMENT 'UniqueGlobalIdentifier',
  `catalogNumber` varchar(32) DEFAULT NULL,
  `otherCatalogNumbers` varchar(255) DEFAULT NULL,
  `ownerInstitutionCode` varchar(32) DEFAULT NULL,
  `institutionID` varchar(255) DEFAULT NULL,
  `collectionID` varchar(255) DEFAULT NULL,
  `datasetID` varchar(255) DEFAULT NULL,
  `organismID` varchar(150) DEFAULT NULL,
  `institutionCode` varchar(64) DEFAULT NULL,
  `collectionCode` varchar(64) DEFAULT NULL,
  `family` varchar(255) DEFAULT NULL,
  `scientificName` varchar(255) DEFAULT NULL,
  `sciname` varchar(255) DEFAULT NULL,
  `tidinterpreted` int(10) unsigned DEFAULT NULL,
  `genus` varchar(255) DEFAULT NULL,
  `specificEpithet` varchar(255) DEFAULT NULL,
  `taxonRank` varchar(32) DEFAULT NULL,
  `infraspecificEpithet` varchar(255) DEFAULT NULL,
  `scientificNameAuthorship` varchar(255) DEFAULT NULL,
  `taxonRemarks` text,
  `identifiedBy` varchar(255) DEFAULT NULL,
  `dateIdentified` varchar(45) DEFAULT NULL,
  `identificationReferences` text,
  `identificationRemarks` text,
  `identificationQualifier` varchar(255) DEFAULT NULL COMMENT 'cf, aff, etc',
  `typeStatus` varchar(255) DEFAULT NULL,
  `recordedBy` varchar(255) DEFAULT NULL COMMENT 'Collector(s)',
  `recordNumber` varchar(45) DEFAULT NULL COMMENT 'Collector Number',
  `recordedbyid` bigint(20) DEFAULT NULL,
  `associatedCollectors` varchar(255) DEFAULT NULL COMMENT 'not DwC',
  `eventDate` date DEFAULT NULL,
  `eventDate2` date DEFAULT NULL,
  `year` int(10) DEFAULT NULL,
  `month` int(10) DEFAULT NULL,
  `day` int(10) DEFAULT NULL,
  `startDayOfYear` int(10) DEFAULT NULL,
  `endDayOfYear` int(10) DEFAULT NULL,
  `verbatimEventDate` varchar(255) DEFAULT NULL,
  `eventTime` varchar(45) DEFAULT NULL,
  `habitat` text COMMENT 'Habitat, substrait, etc',
  `substrate` varchar(500) DEFAULT NULL,
  `fieldNotes` text,
  `fieldnumber` varchar(45) DEFAULT NULL,
  `eventID` varchar(150) DEFAULT NULL,
  `occurrenceRemarks` text COMMENT 'General Notes',
  `informationWithheld` varchar(250) DEFAULT NULL,
  `dataGeneralizations` varchar(250) DEFAULT NULL,
  `associatedOccurrences` text,
  `associatedTaxa` text COMMENT 'Associated Species',
  `dynamicProperties` text,
  `verbatimAttributes` text,
  `behavior` varchar(500) DEFAULT NULL,
  `reproductiveCondition` varchar(255) DEFAULT NULL COMMENT 'Phenology: flowers, fruit, sterile',
  `cultivationStatus` int(10) DEFAULT NULL COMMENT '0 = wild, 1 = cultivated',
  `establishmentMeans` varchar(150) DEFAULT NULL,
  `lifeStage` varchar(45) DEFAULT NULL,
  `sex` varchar(45) DEFAULT NULL,
  `individualCount` varchar(45) DEFAULT NULL,
  `samplingProtocol` varchar(100) DEFAULT NULL,
  `samplingEffort` varchar(200) DEFAULT NULL,
  `preparations` varchar(100) DEFAULT NULL,
  `locationID` varchar(150) DEFAULT NULL,
  `continent` varchar(45) DEFAULT NULL,
  `waterBody` varchar(75) DEFAULT NULL,
  `parentLocationID` varchar(150) DEFAULT NULL,
  `islandGroup` varchar(75) DEFAULT NULL,
  `island` varchar(75) DEFAULT NULL,
  `countryCode` varchar(5) DEFAULT NULL,
  `country` varchar(64) DEFAULT NULL,
  `stateProvince` varchar(255) DEFAULT NULL,
  `county` varchar(255) DEFAULT NULL,
  `municipality` varchar(255) DEFAULT NULL,
  `locality` text,
  `localitySecurity` int(10) DEFAULT '0' COMMENT '0 = no security; 1 = hidden locality',
  `localitySecurityReason` varchar(100) DEFAULT NULL,
  `decimalLatitude` double DEFAULT NULL,
  `decimalLongitude` double DEFAULT NULL,
  `geodeticDatum` varchar(255) DEFAULT NULL,
  `coordinateUncertaintyInMeters` int(10) unsigned DEFAULT NULL,
  `footprintWKT` text,
  `coordinatePrecision` decimal(9,7) DEFAULT NULL,
  `locationRemarks` text,
  `verbatimCoordinates` varchar(255) DEFAULT NULL,
  `verbatimCoordinateSystem` varchar(255) DEFAULT NULL,
  `georeferencedBy` varchar(255) DEFAULT NULL,
  `georeferencedDate` datetime DEFAULT NULL,
  `georeferenceProtocol` varchar(255) DEFAULT NULL,
  `georeferenceSources` varchar(255) DEFAULT NULL,
  `georeferenceVerificationStatus` varchar(32) DEFAULT NULL,
  `georeferenceRemarks` varchar(500) DEFAULT NULL,
  `minimumElevationInMeters` int(6) DEFAULT NULL,
  `maximumElevationInMeters` int(6) DEFAULT NULL,
  `verbatimElevation` varchar(255) DEFAULT NULL,
  `minimumDepthInMeters` int(11) DEFAULT NULL,
  `maximumDepthInMeters` int(11) DEFAULT NULL,
  `verbatimDepth` varchar(50) DEFAULT NULL,
  `previousIdentifications` text,
  `availability` int(2) DEFAULT NULL,
  `disposition` varchar(250) DEFAULT NULL,
  `storageLocation` varchar(100) DEFAULT NULL,
  `genericcolumn1` varchar(100) DEFAULT NULL,
  `genericcolumn2` varchar(100) DEFAULT NULL,
  `modified` datetime DEFAULT NULL COMMENT 'DateLastModified',
  `language` varchar(20) DEFAULT NULL,
  `observeruid` int(10) unsigned DEFAULT NULL,
  `processingstatus` varchar(45) DEFAULT NULL,
  `recordEnteredBy` varchar(250) DEFAULT NULL,
  `duplicateQuantity` int(10) unsigned DEFAULT NULL,
  `labelProject` varchar(250) DEFAULT NULL,
  `dynamicFields` text,
  `dateEntered` datetime DEFAULT NULL,
  `dateLastModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`occid`) USING BTREE,
  UNIQUE KEY `Index_collid` (`collid`,`dbpk`),
  UNIQUE KEY `UNIQUE_occurrenceID` (`occurrenceID`),
  KEY `Index_sciname` (`sciname`),
  KEY `Index_family` (`family`),
  KEY `Index_country` (`country`),
  KEY `Index_state` (`stateProvince`),
  KEY `Index_county` (`county`),
  KEY `Index_collector` (`recordedBy`),
  KEY `Index_ownerInst` (`ownerInstitutionCode`),
  KEY `FK_omoccurrences_tid` (`tidinterpreted`),
  KEY `FK_omoccurrences_uid` (`observeruid`),
  KEY `Index_municipality` (`municipality`),
  KEY `Index_collnum` (`recordNumber`),
  KEY `Index_catalognumber` (`catalogNumber`),
  KEY `FK_recordedbyid` (`recordedbyid`),
  KEY `Index_eventDate` (`eventDate`),
  KEY `Index_occurrences_procstatus` (`processingstatus`),
  KEY `occelevmax` (`maximumElevationInMeters`),
  KEY `occelevmin` (`minimumElevationInMeters`),
  KEY `Index_occurrences_cult` (`cultivationStatus`),
  KEY `Index_occurrences_typestatus` (`typeStatus`),
  KEY `Index_occurDateLastModifed` (`dateLastModified`),
  KEY `Index_occurDateEntered` (`dateEntered`),
  KEY `Index_occurRecordEnteredBy` (`recordEnteredBy`),
  KEY `Index_locality` (`locality`(100)),
  KEY `Index_otherCatalogNumbers` (`otherCatalogNumbers`),
  KEY `IX_omoccur_eventDate2` (`eventDate2`),
  KEY `Index_locationID` (`locationID`),
  KEY `Index_eventID` (`eventID`),
  KEY `Index_occur_localitySecurity` (`localitySecurity`),
  KEY `Index_latlng` (`decimalLatitude`,`decimalLongitude`),
  CONSTRAINT `FK_omoccurrences_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurrences_recbyid` FOREIGN KEY (`recordedbyid`) REFERENCES `agents` (`agentID`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurrences_tid` FOREIGN KEY (`tidinterpreted`) REFERENCES `taxa` (`TID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurrences_uid` FOREIGN KEY (`observeruid`) REFERENCES `users` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccurrences`
--

LOCK TABLES `omoccurrences` WRITE;
/*!40000 ALTER TABLE `omoccurrences` DISABLE KEYS */;
INSERT INTO `omoccurrences` VALUES (1,1,NULL,'PreservedSpecimen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'pending review','admin',NULL,NULL,NULL,'2023-04-01 19:41:45','2023-04-02 02:41:45'),(2,1,NULL,'PreservedSpecimen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'admin',NULL,NULL,NULL,'2023-04-02 12:04:17','2023-04-02 19:04:17'),(3,1,NULL,'PreservedSpecimen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'pending review','admin',NULL,NULL,NULL,'2023-04-02 12:41:34','2023-04-02 19:41:34'),(4,1,NULL,'PreservedSpecimen',NULL,'Catalog',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'d',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Collector','Number',NULL,'Associated Collectors','2022-04-02',NULL,2022,4,2,92,NULL,'2022-04-02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'pending review','admin',NULL,'test',NULL,'2023-04-02 12:43:14','2023-04-28 18:56:17'),(5,1,NULL,'PreservedSpecimen','Occurrence ID','Catalog',NULL,NULL,NULL,NULL,NULL,NULL,'Institution Code (override)','Collection Code (override)',NULL,NULL,'Scientific Name',NULL,NULL,NULL,NULL,NULL,'Author',NULL,'Identified By','2022-04-03',NULL,NULL,NULL,NULL,'Collector','Number',NULL,'Associated Collectors','2022-04-01',NULL,2022,4,1,91,NULL,'2022-04-02',NULL,'Habitat','Substrate',NULL,NULL,NULL,'Notes (Occurrence Remarks)',NULL,'Data Generalizations',NULL,'Associated Taxa',NULL,'describe',NULL,NULL,NULL,NULL,'Life Stage',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'United States','Massachusetts','Massachusetts',NULL,NULL,0,NULL,1,2,NULL,3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'pending review','admin',NULL,NULL,NULL,'2023-04-02 12:45:25','2023-04-28 17:51:58'),(6,1,NULL,'PreservedSpecimen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Collector',NULL,NULL,'Associated Collectors','2022-04-01',NULL,2022,4,1,91,NULL,'2022-04-02',NULL,'Habitat','Substrate',NULL,NULL,NULL,NULL,NULL,'Data Generalizations',NULL,'Associated Taxa',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'United States','Massachusetts','Massachusetts',NULL,NULL,0,NULL,1,2,NULL,3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'admin',NULL,NULL,NULL,'2023-04-05 09:22:28','2023-04-12 19:19:55'),(7,1,NULL,'PreservedSpecimen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Collector',NULL,NULL,'Associated Collectors','2022-04-02',NULL,2022,4,2,92,NULL,'2022-04-02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'admin',NULL,NULL,NULL,'2023-04-12 10:20:00','2023-04-12 17:20:00'),(8,1,NULL,'PreservedSpecimen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Collector',NULL,NULL,'Associated Collectors','2022-04-02',NULL,2022,4,2,92,NULL,'2022-04-02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'admin',NULL,NULL,NULL,'2023-04-13 07:53:10','2023-04-13 14:53:10'),(9,1,NULL,'PreservedSpecimen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Collector',NULL,NULL,'Associated Collectors','2022-04-02',NULL,2022,4,2,92,NULL,'2022-04-02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'admin',NULL,NULL,NULL,'2023-04-13 07:53:50','2023-04-13 14:53:50'),(10,1,NULL,'PreservedSpecimen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Collector',NULL,NULL,'Associated Collectors','2022-04-02',NULL,2022,4,2,92,NULL,'2022-04-02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'admin',NULL,NULL,NULL,'2023-04-13 12:33:42','2023-04-13 19:33:42'),(11,1,NULL,'PreservedSpecimen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Collector',NULL,NULL,'Associated Collectors','2022-04-02',NULL,2022,4,2,92,NULL,'2022-04-02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'admin',NULL,NULL,NULL,'2023-04-13 12:36:37','2023-04-13 19:36:37'),(12,1,NULL,'PreservedSpecimen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Collector',NULL,NULL,'Associated Collectors','2022-04-02',NULL,2022,4,2,92,NULL,'2022-04-02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'admin',NULL,NULL,NULL,'2023-04-13 12:37:45','2023-04-13 19:37:45'),(13,1,NULL,'PreservedSpecimen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Collector',NULL,NULL,'Associated Collectors','2022-04-02',NULL,2022,4,2,92,NULL,'2022-04-02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'admin',NULL,NULL,NULL,'2023-04-13 12:39:36','2023-04-13 19:39:36'),(14,1,NULL,'PreservedSpecimen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Collector',NULL,NULL,'Associated Collectors','2022-04-02',NULL,2022,4,2,92,NULL,'2022-04-02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'admin',NULL,NULL,NULL,'2023-04-13 12:54:22','2023-04-13 19:54:22'),(15,1,NULL,'PreservedSpecimen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Collector',NULL,NULL,'Associated Collectors','2022-04-02',NULL,2022,4,2,92,NULL,'2022-04-02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'admin',NULL,NULL,NULL,'2023-04-13 12:55:16','2023-04-13 19:55:16'),(16,1,NULL,'PreservedSpecimen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Collector',NULL,NULL,'Associated Collectors','2022-04-02',NULL,2022,4,2,92,NULL,'2022-04-02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'admin',NULL,NULL,NULL,'2023-04-13 13:02:53','2023-04-13 20:02:53'),(17,1,NULL,'PreservedSpecimen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Collector',NULL,NULL,'Associated Collectors','2022-04-02',NULL,2022,4,2,92,NULL,'2022-04-02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'admin',NULL,NULL,NULL,'2023-04-13 13:03:34','2023-04-13 20:03:34'),(18,1,NULL,'PreservedSpecimen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Collector',NULL,NULL,'Associated Collectors','2022-04-02',NULL,2022,4,2,92,NULL,'2022-04-02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'admin',NULL,NULL,NULL,'2023-04-14 14:57:33','2023-04-14 21:57:33'),(19,1,NULL,'PreservedSpecimen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2022-04-02',NULL,2022,4,2,92,NULL,'2022-04-02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'admin',NULL,NULL,NULL,'2023-04-14 15:00:44','2023-04-15 01:29:46'),(20,1,NULL,'PreservedSpecimen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Collector',NULL,NULL,'Associated Collectors','2022-04-02',NULL,2022,4,2,92,NULL,'2022-04-02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'admin',NULL,NULL,NULL,'2023-04-14 15:03:20','2023-04-14 22:03:20'),(21,1,NULL,'PreservedSpecimen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Collector',NULL,NULL,'Associated Collectors','2022-04-02',NULL,2022,4,2,92,NULL,'2022-04-02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'admin',NULL,NULL,NULL,'2023-04-14 15:05:17','2023-04-14 22:05:17'),(22,1,NULL,'PreservedSpecimen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Collector',NULL,NULL,'Associated Collectors','2022-04-02',NULL,2022,4,2,92,NULL,'2022-04-02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'admin',NULL,NULL,NULL,'2023-04-14 15:08:24','2023-04-14 22:08:24'),(23,1,NULL,'PreservedSpecimen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Collector',NULL,NULL,'Associated Collectors','2022-04-02',NULL,2022,4,2,92,NULL,'2022-04-02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'admin',NULL,NULL,NULL,'2023-04-14 15:13:56','2023-04-14 22:13:56'),(24,1,NULL,'PreservedSpecimen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Collector',NULL,NULL,'Associated Collectors','2022-04-02',NULL,2022,4,2,92,NULL,'2022-04-02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'admin',NULL,NULL,NULL,'2023-04-14 15:17:02','2023-04-14 22:17:02'),(25,1,NULL,'PreservedSpecimen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Collector',NULL,NULL,'Associated Collectors','2022-04-02',NULL,2022,4,2,92,NULL,'2022-04-02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'admin',NULL,NULL,NULL,'2023-04-14 15:19:22','2023-04-14 22:19:22'),(26,1,NULL,'PreservedSpecimen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'admin',NULL,NULL,NULL,'2023-04-28 10:44:03','2023-04-28 17:44:03');
/*!40000 ALTER TABLE `omoccurrences` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `omoccurrences_insert` AFTER INSERT ON `omoccurrences`
FOR EACH ROW BEGIN
	IF NEW.`decimalLatitude` IS NOT NULL AND NEW.`decimalLongitude` IS NOT NULL THEN
		INSERT INTO omoccurpoints (`occid`,`point`) 
		VALUES (NEW.`occid`,Point(NEW.`decimalLatitude`, NEW.`decimalLongitude`));
	END IF;
	IF NEW.`recordedby` IS NOT NULL OR NEW.`municipality` IS NOT NULL OR NEW.`locality` IS NOT NULL THEN
		INSERT INTO omoccurrencesfulltext (`occid`,`recordedby`,`locality`) 
		VALUES (NEW.`occid`,NEW.`recordedby`,CONCAT_WS("; ", NEW.`municipality`, NEW.`locality`));
	END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `omoccurrences_update` AFTER UPDATE ON `omoccurrences`
FOR EACH ROW BEGIN
	IF NEW.`decimalLatitude` IS NOT NULL AND NEW.`decimalLongitude` IS NOT NULL THEN
		IF EXISTS (SELECT `occid` FROM omoccurpoints WHERE `occid`=NEW.`occid`) THEN
			UPDATE omoccurpoints 
			SET `point` = Point(NEW.`decimalLatitude`, NEW.`decimalLongitude`)
			WHERE `occid` = NEW.`occid`;
		ELSE 
			INSERT INTO omoccurpoints (`occid`,`point`) 
			VALUES (NEW.`occid`,Point(NEW.`decimalLatitude`, NEW.`decimalLongitude`));
		END IF;
	ELSE
		DELETE FROM omoccurpoints WHERE `occid` = NEW.`occid`;
	END IF;

	IF NEW.`recordedby` IS NOT NULL OR NEW.`municipality` IS NOT NULL OR NEW.`locality` IS NOT NULL THEN
		IF EXISTS (SELECT `occid` FROM omoccurrencesfulltext WHERE `occid`=NEW.`occid`) THEN
			UPDATE omoccurrencesfulltext 
			SET `recordedby` = NEW.`recordedby`,`locality` = CONCAT_WS("; ", NEW.`municipality`, NEW.`locality`)
			WHERE `occid` = NEW.`occid`;
		ELSE
			INSERT INTO omoccurrencesfulltext (`occid`,`recordedby`,`locality`) 
			VALUES (NEW.`occid`,NEW.`recordedby`,CONCAT_WS("; ", NEW.`municipality`, NEW.`locality`));
		END IF;
	ELSE 
		DELETE FROM omoccurrencesfulltext WHERE `occid` = NEW.`occid`;
	END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `omoccurrences_delete` BEFORE DELETE ON `omoccurrences`
FOR EACH ROW BEGIN
	DELETE FROM omoccurpoints WHERE `occid` = OLD.`occid`;
	DELETE FROM omoccurrencesfulltext WHERE `occid` = OLD.`occid`;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `omoccurrencesfulltext`
--

DROP TABLE IF EXISTS `omoccurrencesfulltext`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurrencesfulltext` (
  `occid` int(11) NOT NULL,
  `locality` text,
  `recordedby` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`occid`),
  FULLTEXT KEY `ft_occur_locality` (`locality`),
  FULLTEXT KEY `ft_occur_recordedby` (`recordedby`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccurrencesfulltext`
--

LOCK TABLES `omoccurrencesfulltext` WRITE;
/*!40000 ALTER TABLE `omoccurrencesfulltext` DISABLE KEYS */;
INSERT INTO `omoccurrencesfulltext` VALUES (4,'','Collector'),(5,'','Collector'),(6,'','Collector'),(7,'','Collector'),(8,'','Collector'),(9,'','Collector'),(10,'','Collector'),(11,'','Collector'),(12,'','Collector'),(13,'','Collector'),(14,'','Collector'),(15,'','Collector'),(16,'','Collector'),(17,'','Collector'),(18,'','Collector'),(20,'','Collector'),(21,'','Collector'),(22,'','Collector'),(23,'','Collector'),(24,'','Collector'),(25,'','Collector');
/*!40000 ALTER TABLE `omoccurrencesfulltext` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omoccurrencetypes`
--

DROP TABLE IF EXISTS `omoccurrencetypes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurrencetypes` (
  `occurtypeid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned DEFAULT NULL,
  `typestatus` varchar(45) DEFAULT NULL,
  `typeDesignationType` varchar(45) DEFAULT NULL,
  `typeDesignatedBy` varchar(45) DEFAULT NULL,
  `scientificName` varchar(250) DEFAULT NULL,
  `scientificNameAuthorship` varchar(45) DEFAULT NULL,
  `tidinterpreted` int(10) unsigned DEFAULT NULL,
  `basionym` varchar(250) DEFAULT NULL,
  `refid` int(11) DEFAULT NULL,
  `bibliographicCitation` varchar(250) DEFAULT NULL,
  `dynamicProperties` varchar(250) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`occurtypeid`),
  KEY `FK_occurtype_occid_idx` (`occid`),
  KEY `FK_occurtype_refid_idx` (`refid`),
  KEY `FK_occurtype_tid_idx` (`tidinterpreted`),
  CONSTRAINT `FK_occurtype_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_occurtype_refid` FOREIGN KEY (`refid`) REFERENCES `referenceobject` (`refid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_occurtype_tid` FOREIGN KEY (`tidinterpreted`) REFERENCES `taxa` (`TID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccurrencetypes`
--

LOCK TABLES `omoccurrencetypes` WRITE;
/*!40000 ALTER TABLE `omoccurrencetypes` DISABLE KEYS */;
/*!40000 ALTER TABLE `omoccurrencetypes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omoccurresource`
--

DROP TABLE IF EXISTS `omoccurresource`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurresource` (
  `resourceID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `reourceTitle` varchar(45) NOT NULL,
  `resourceType` varchar(45) NOT NULL,
  `uri` varchar(250) NOT NULL,
  `source` varchar(45) DEFAULT NULL,
  `resourceIdentifier` varchar(45) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `createdUid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`resourceID`),
  KEY `FK_omoccurresource_occid_idx` (`occid`),
  KEY `FK_omoccurresource_modUid_idx` (`modifiedUid`),
  KEY `FK_omoccurresource_createdUid_idx` (`createdUid`),
  CONSTRAINT `FK_omoccurresource_createdUid` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurresource_modUid` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurresource_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccurresource`
--

LOCK TABLES `omoccurresource` WRITE;
/*!40000 ALTER TABLE `omoccurresource` DISABLE KEYS */;
/*!40000 ALTER TABLE `omoccurresource` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omoccurrevisions`
--

DROP TABLE IF EXISTS `omoccurrevisions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurrevisions` (
  `orid` int(11) NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `oldValues` text,
  `newValues` text,
  `externalSource` varchar(45) DEFAULT NULL,
  `externalEditor` varchar(100) DEFAULT NULL,
  `guid` varchar(45) DEFAULT NULL,
  `reviewStatus` int(11) DEFAULT NULL,
  `appliedStatus` int(11) DEFAULT NULL,
  `errorMessage` varchar(500) DEFAULT NULL,
  `uid` int(10) unsigned DEFAULT NULL,
  `externalTimestamp` datetime DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`orid`),
  UNIQUE KEY `guid_UNIQUE` (`guid`),
  KEY `fk_omrevisions_occid_idx` (`occid`),
  KEY `fk_omrevisions_uid_idx` (`uid`),
  KEY `Index_omrevisions_applied` (`appliedStatus`),
  KEY `Index_omrevisions_reviewed` (`reviewStatus`),
  KEY `Index_omrevisions_source` (`externalSource`),
  KEY `Index_omrevisions_editor` (`externalEditor`),
  CONSTRAINT `fk_omrevisions_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_omrevisions_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccurrevisions`
--

LOCK TABLES `omoccurrevisions` WRITE;
/*!40000 ALTER TABLE `omoccurrevisions` DISABLE KEYS */;
/*!40000 ALTER TABLE `omoccurrevisions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omoccurverification`
--

DROP TABLE IF EXISTS `omoccurverification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurverification` (
  `ovsid` int(11) NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `category` varchar(45) NOT NULL,
  `ranking` int(11) NOT NULL,
  `protocol` varchar(100) DEFAULT NULL,
  `source` varchar(45) DEFAULT NULL,
  `uid` int(10) unsigned DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ovsid`),
  UNIQUE KEY `UNIQUE_omoccurverification` (`occid`,`category`),
  KEY `FK_omoccurverification_occid_idx` (`occid`),
  KEY `FK_omoccurverification_uid_idx` (`uid`),
  CONSTRAINT `FK_omoccurverification_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurverification_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccurverification`
--

LOCK TABLES `omoccurverification` WRITE;
/*!40000 ALTER TABLE `omoccurverification` DISABLE KEYS */;
INSERT INTO `omoccurverification` VALUES (1,4,'identification',5,NULL,NULL,1,NULL,'2023-04-02 19:43:14'),(2,5,'identification',5,NULL,NULL,1,NULL,'2023-04-02 19:45:25');
/*!40000 ALTER TABLE `omoccurverification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `referenceagentlinks`
--

DROP TABLE IF EXISTS `referenceagentlinks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referenceagentlinks` (
  `refid` int(11) NOT NULL,
  `agentid` int(11) NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdbyid` int(11) NOT NULL,
  PRIMARY KEY (`refid`,`agentid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `referenceagentlinks`
--

LOCK TABLES `referenceagentlinks` WRITE;
/*!40000 ALTER TABLE `referenceagentlinks` DISABLE KEYS */;
/*!40000 ALTER TABLE `referenceagentlinks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `referenceauthorlink`
--

DROP TABLE IF EXISTS `referenceauthorlink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referenceauthorlink` (
  `refid` int(11) NOT NULL,
  `refauthid` int(11) NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`refid`,`refauthid`),
  KEY `FK_refauthlink_refid_idx` (`refid`),
  KEY `FK_refauthlink_refauthid_idx` (`refauthid`),
  CONSTRAINT `FK_refauthlink_refauthid` FOREIGN KEY (`refauthid`) REFERENCES `referenceauthors` (`refauthorid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_refauthlink_refid` FOREIGN KEY (`refid`) REFERENCES `referenceobject` (`refid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `referenceauthorlink`
--

LOCK TABLES `referenceauthorlink` WRITE;
/*!40000 ALTER TABLE `referenceauthorlink` DISABLE KEYS */;
/*!40000 ALTER TABLE `referenceauthorlink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `referenceauthors`
--

DROP TABLE IF EXISTS `referenceauthors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referenceauthors` (
  `refauthorid` int(11) NOT NULL AUTO_INCREMENT,
  `lastname` varchar(100) NOT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `modifieduid` int(10) unsigned DEFAULT NULL,
  `modifiedtimestamp` datetime DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`refauthorid`),
  KEY `INDEX_refauthlastname` (`lastname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `referenceauthors`
--

LOCK TABLES `referenceauthors` WRITE;
/*!40000 ALTER TABLE `referenceauthors` DISABLE KEYS */;
/*!40000 ALTER TABLE `referenceauthors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `referencechecklistlink`
--

DROP TABLE IF EXISTS `referencechecklistlink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referencechecklistlink` (
  `refid` int(11) NOT NULL,
  `clid` int(10) unsigned NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`refid`,`clid`),
  KEY `FK_refcheckllistlink_refid_idx` (`refid`),
  KEY `FK_refcheckllistlink_clid_idx` (`clid`),
  CONSTRAINT `FK_refchecklistlink_clid` FOREIGN KEY (`clid`) REFERENCES `fmchecklists` (`CLID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_refchecklistlink_refid` FOREIGN KEY (`refid`) REFERENCES `referenceobject` (`refid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `referencechecklistlink`
--

LOCK TABLES `referencechecklistlink` WRITE;
/*!40000 ALTER TABLE `referencechecklistlink` DISABLE KEYS */;
/*!40000 ALTER TABLE `referencechecklistlink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `referencechklsttaxalink`
--

DROP TABLE IF EXISTS `referencechklsttaxalink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referencechklsttaxalink` (
  `refid` int(11) NOT NULL,
  `clid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`refid`,`clid`,`tid`),
  KEY `FK_refchktaxalink_clidtid_idx` (`clid`,`tid`),
  CONSTRAINT `FK_refchktaxalink_clidtid` FOREIGN KEY (`clid`, `tid`) REFERENCES `fmchklsttaxalink` (`CLID`, `TID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_refchktaxalink_ref` FOREIGN KEY (`refid`) REFERENCES `referenceobject` (`refid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `referencechklsttaxalink`
--

LOCK TABLES `referencechklsttaxalink` WRITE;
/*!40000 ALTER TABLE `referencechklsttaxalink` DISABLE KEYS */;
/*!40000 ALTER TABLE `referencechklsttaxalink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `referencecollectionlink`
--

DROP TABLE IF EXISTS `referencecollectionlink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referencecollectionlink` (
  `refid` int(11) NOT NULL,
  `collid` int(10) unsigned NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`refid`,`collid`),
  KEY `FK_refcollectionlink_collid_idx` (`collid`),
  CONSTRAINT `FK_refcollectionlink_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_refcollectionlink_refid` FOREIGN KEY (`refid`) REFERENCES `referenceobject` (`refid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `referencecollectionlink`
--

LOCK TABLES `referencecollectionlink` WRITE;
/*!40000 ALTER TABLE `referencecollectionlink` DISABLE KEYS */;
/*!40000 ALTER TABLE `referencecollectionlink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `referencedatasetlink`
--

DROP TABLE IF EXISTS `referencedatasetlink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referencedatasetlink` (
  `refid` int(11) NOT NULL,
  `datasetid` int(10) unsigned NOT NULL,
  `createdUid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`refid`,`datasetid`),
  KEY `FK_refdataset_datasetid_idx` (`datasetid`),
  KEY `FK_refdataset_uid_idx` (`createdUid`),
  CONSTRAINT `FK_refdataset_datasetid` FOREIGN KEY (`datasetid`) REFERENCES `omoccurdatasets` (`datasetid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_refdataset_refid` FOREIGN KEY (`refid`) REFERENCES `referenceobject` (`refid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_refdataset_uid` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `referencedatasetlink`
--

LOCK TABLES `referencedatasetlink` WRITE;
/*!40000 ALTER TABLE `referencedatasetlink` DISABLE KEYS */;
/*!40000 ALTER TABLE `referencedatasetlink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `referenceobject`
--

DROP TABLE IF EXISTS `referenceobject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referenceobject` (
  `refid` int(11) NOT NULL AUTO_INCREMENT,
  `parentRefId` int(11) DEFAULT NULL,
  `ReferenceTypeId` int(11) DEFAULT NULL,
  `title` varchar(150) NOT NULL,
  `secondarytitle` varchar(250) DEFAULT NULL,
  `shorttitle` varchar(250) DEFAULT NULL,
  `tertiarytitle` varchar(250) DEFAULT NULL,
  `alternativetitle` varchar(250) DEFAULT NULL,
  `typework` varchar(150) DEFAULT NULL,
  `figures` varchar(150) DEFAULT NULL,
  `pubdate` varchar(45) DEFAULT NULL,
  `edition` varchar(45) DEFAULT NULL,
  `volume` varchar(45) DEFAULT NULL,
  `numbervolumnes` varchar(45) DEFAULT NULL,
  `number` varchar(45) DEFAULT NULL,
  `pages` varchar(45) DEFAULT NULL,
  `section` varchar(45) DEFAULT NULL,
  `placeofpublication` varchar(45) DEFAULT NULL,
  `publisher` varchar(150) DEFAULT NULL,
  `isbn_issn` varchar(45) DEFAULT NULL,
  `url` varchar(150) DEFAULT NULL,
  `guid` varchar(45) DEFAULT NULL,
  `ispublished` varchar(45) DEFAULT NULL,
  `notes` varchar(45) DEFAULT NULL,
  `cheatauthors` varchar(400) DEFAULT NULL,
  `cheatcitation` varchar(500) DEFAULT NULL,
  `modifieduid` int(10) unsigned DEFAULT NULL,
  `modifiedtimestamp` datetime DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`refid`),
  KEY `INDEX_refobj_title` (`title`),
  KEY `FK_refobj_parentrefid_idx` (`parentRefId`),
  KEY `FK_refobj_typeid_idx` (`ReferenceTypeId`),
  CONSTRAINT `FK_refobj_parentrefid` FOREIGN KEY (`parentRefId`) REFERENCES `referenceobject` (`refid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_refobj_reftypeid` FOREIGN KEY (`ReferenceTypeId`) REFERENCES `referencetype` (`ReferenceTypeId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `referenceobject`
--

LOCK TABLES `referenceobject` WRITE;
/*!40000 ALTER TABLE `referenceobject` DISABLE KEYS */;
/*!40000 ALTER TABLE `referenceobject` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `referenceoccurlink`
--

DROP TABLE IF EXISTS `referenceoccurlink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referenceoccurlink` (
  `refid` int(11) NOT NULL,
  `occid` int(10) unsigned NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`refid`,`occid`),
  KEY `FK_refoccurlink_refid_idx` (`refid`),
  KEY `FK_refoccurlink_occid_idx` (`occid`),
  CONSTRAINT `FK_refoccurlink_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_refoccurlink_refid` FOREIGN KEY (`refid`) REFERENCES `referenceobject` (`refid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `referenceoccurlink`
--

LOCK TABLES `referenceoccurlink` WRITE;
/*!40000 ALTER TABLE `referenceoccurlink` DISABLE KEYS */;
/*!40000 ALTER TABLE `referenceoccurlink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `referencetaxalink`
--

DROP TABLE IF EXISTS `referencetaxalink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referencetaxalink` (
  `refid` int(11) NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`refid`,`tid`),
  KEY `FK_reftaxalink_refid_idx` (`refid`),
  KEY `FK_reftaxalink_tid_idx` (`tid`),
  CONSTRAINT `FK_reftaxalink_refid` FOREIGN KEY (`refid`) REFERENCES `referenceobject` (`refid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_reftaxalink_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `referencetaxalink`
--

LOCK TABLES `referencetaxalink` WRITE;
/*!40000 ALTER TABLE `referencetaxalink` DISABLE KEYS */;
/*!40000 ALTER TABLE `referencetaxalink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `referencetype`
--

DROP TABLE IF EXISTS `referencetype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referencetype` (
  `ReferenceTypeId` int(11) NOT NULL AUTO_INCREMENT,
  `ReferenceType` varchar(45) NOT NULL,
  `IsParent` int(11) DEFAULT NULL,
  `Title` varchar(45) DEFAULT NULL,
  `SecondaryTitle` varchar(45) DEFAULT NULL,
  `PlacePublished` varchar(45) DEFAULT NULL,
  `Publisher` varchar(45) DEFAULT NULL,
  `Volume` varchar(45) DEFAULT NULL,
  `NumberVolumes` varchar(45) DEFAULT NULL,
  `Number` varchar(45) DEFAULT NULL,
  `Pages` varchar(45) DEFAULT NULL,
  `Section` varchar(45) DEFAULT NULL,
  `TertiaryTitle` varchar(45) DEFAULT NULL,
  `Edition` varchar(45) DEFAULT NULL,
  `Date` varchar(45) DEFAULT NULL,
  `TypeWork` varchar(45) DEFAULT NULL,
  `ShortTitle` varchar(45) DEFAULT NULL,
  `AlternativeTitle` varchar(45) DEFAULT NULL,
  `ISBN_ISSN` varchar(45) DEFAULT NULL,
  `Figures` varchar(45) DEFAULT NULL,
  `addedByUid` int(11) DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ReferenceTypeId`),
  UNIQUE KEY `ReferenceType_UNIQUE` (`ReferenceType`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `referencetype`
--

LOCK TABLES `referencetype` WRITE;
/*!40000 ALTER TABLE `referencetype` DISABLE KEYS */;
INSERT INTO `referencetype` VALUES (1,'Generic',NULL,'Title','SecondaryTitle','PlacePublished','Publisher','Volume','NumberVolumes','Number','Pages','Section','TertiaryTitle','Edition','Date','TypeWork','ShortTitle','AlternativeTitle','Isbn_Issn','Figures',NULL,'2014-06-17 00:27:12'),(2,'Journal Article',NULL,'Title','Periodical Title',NULL,NULL,'Volume',NULL,'Issue','Pages',NULL,NULL,NULL,'Date',NULL,'Short Title','Alt. Jour.',NULL,'Figures',NULL,'2014-06-17 00:27:12'),(3,'Book',1,'Title','Series Title','City','Publisher','Volume','No. Vols.','Number','Pages',NULL,NULL,'Edition','Date',NULL,'Short Title',NULL,'ISBN','Figures',NULL,'2014-06-17 00:27:12'),(4,'Book Section',NULL,'Title','Book Title','City','Publisher','Volume','No. Vols.','Number','Pages',NULL,'Ser. Title','Edition','Date',NULL,'Short Title',NULL,'ISBN','Figures',NULL,'2014-06-17 00:27:12'),(5,'Manuscript',NULL,'Title','Collection Title','City',NULL,NULL,NULL,'Number','Pages',NULL,NULL,'Edition','Date','Type Work','Short Title',NULL,NULL,'Figures',NULL,'2014-06-17 00:27:12'),(6,'Edited Book',1,'Title','Series Title','City','Publisher','Volume','No. Vols.','Number','Pages',NULL,NULL,'Edition','Date',NULL,'Short Title',NULL,'ISBN','Figures',NULL,'2014-06-17 00:27:12'),(7,'Magazine Article',NULL,'Title','Periodical Title',NULL,NULL,'Volume',NULL,'Issue','Pages',NULL,NULL,NULL,'Date',NULL,'Short Title',NULL,NULL,'Figures',NULL,'2014-06-17 00:27:12'),(8,'Newspaper Article',NULL,'Title','Periodical Title','City',NULL,NULL,NULL,NULL,'Pages','Section',NULL,'Edition','Date','Type Art.','Short Title',NULL,NULL,'Figures',NULL,'2014-06-17 00:27:12'),(9,'Conference Proceedings',NULL,'Title','Conf. Name','Conf. Loc.','Publisher','Volume','No. Vols.',NULL,'Pages',NULL,'Ser. Title','Edition','Date',NULL,'Short Title',NULL,'ISBN','Figures',NULL,'2014-06-17 00:27:12'),(10,'Thesis',NULL,'Title','Academic Dept.','City','University',NULL,NULL,NULL,'Pages',NULL,NULL,NULL,'Date','Thesis Type','Short Title',NULL,NULL,'Figures',NULL,'2014-06-17 00:27:12'),(11,'Report',NULL,'Title',NULL,'City','Institution',NULL,NULL,NULL,'Pages',NULL,NULL,NULL,'Date','Type Work','Short Title',NULL,'Rpt. No.','Figures',NULL,'2014-06-17 00:27:12'),(12,'Personal Communication',NULL,'Title',NULL,'City','Publisher',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Date','Type Work','Short Title',NULL,NULL,NULL,NULL,'2014-06-17 00:27:12'),(13,'Computer Program',NULL,'Title',NULL,'City','Publisher','Version',NULL,NULL,NULL,NULL,NULL,'Platform','Date','Type Work','Short Title',NULL,NULL,NULL,NULL,'2014-06-17 00:27:12'),(14,'Electronic Source',NULL,'Title',NULL,NULL,'Publisher','Access Year','Extent','Acc. Date',NULL,NULL,NULL,'Edition','Date','Medium','Short Title',NULL,NULL,NULL,NULL,'2014-06-17 00:27:12'),(15,'Audiovisual Material',NULL,'Title','Collection Title','City','Publisher',NULL,NULL,'Number',NULL,NULL,NULL,NULL,'Date','Type Work','Short Title',NULL,NULL,NULL,NULL,'2014-06-17 00:27:12'),(16,'Film or Broadcast',NULL,'Title','Series Title','City','Distributor',NULL,NULL,NULL,'Length',NULL,NULL,NULL,'Date','Medium','Short Title',NULL,'ISBN',NULL,NULL,'2014-06-17 00:27:12'),(17,'Artwork',NULL,'Title',NULL,'City','Publisher',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Date','Type Work','Short Title',NULL,NULL,NULL,NULL,'2014-06-17 00:27:12'),(18,'Map',NULL,'Title',NULL,'City','Publisher',NULL,NULL,NULL,'Scale',NULL,NULL,'Edition','Date','Type Work','Short Title',NULL,NULL,NULL,NULL,'2014-06-17 00:27:12'),(19,'Patent',NULL,'Title','Published Source','Country','Assignee','Volume','No. Vols.','Issue','Pages',NULL,NULL,NULL,'Date',NULL,'Short Title',NULL,'Pat. No.','Figures',NULL,'2014-06-17 00:27:12'),(20,'Hearing',NULL,'Title','Committee','City','Publisher',NULL,NULL,'Doc. No.','Pages',NULL,'Leg. Boby','Session','Date',NULL,'Short Title',NULL,NULL,NULL,NULL,'2014-06-17 00:27:12'),(21,'Bill',NULL,'Title','Code',NULL,NULL,'Code Volume',NULL,'Bill No.','Pages','Section','Leg. Boby','Session','Date',NULL,'Short Title',NULL,NULL,NULL,NULL,'2014-06-17 00:27:12'),(22,'Statute',NULL,'Title','Code',NULL,NULL,'Code Number',NULL,'Law No.','1st Pg.','Section',NULL,'Session','Date',NULL,'Short Title',NULL,NULL,NULL,NULL,'2014-06-17 00:27:12'),(23,'Case',NULL,'Title',NULL,NULL,'Court','Reporter Vol.',NULL,NULL,NULL,NULL,NULL,NULL,'Date',NULL,NULL,NULL,NULL,NULL,NULL,'2014-06-17 00:27:12'),(24,'Figure',NULL,'Title','Source Program',NULL,NULL,NULL,'-',NULL,NULL,NULL,NULL,NULL,'Date',NULL,NULL,NULL,NULL,NULL,NULL,'2014-06-17 00:27:12'),(25,'Chart or Table',NULL,'Title','Source Program',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Date',NULL,NULL,NULL,NULL,NULL,NULL,'2014-06-17 00:27:12'),(26,'Equation',NULL,'Title','Source Program',NULL,NULL,'Volume',NULL,'Number',NULL,NULL,NULL,NULL,'Date',NULL,NULL,NULL,NULL,NULL,NULL,'2014-06-17 00:27:12'),(27,'Book Series',1,'Title',NULL,'City','Publisher',NULL,'No. Vols.',NULL,'Pages',NULL,NULL,'Edition','Date',NULL,NULL,NULL,'ISBN','Figures',NULL,'2014-06-17 00:27:12'),(28,'Determination',NULL,'Title',NULL,NULL,'Institution',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Date',NULL,NULL,NULL,NULL,NULL,NULL,'2014-06-17 00:27:12'),(29,'Sub-Reference',NULL,'Title',NULL,NULL,NULL,NULL,NULL,NULL,'Pages',NULL,NULL,NULL,'Date',NULL,NULL,NULL,NULL,'Figures',NULL,'2014-06-17 00:27:12'),(30,'Periodical',1,'Title',NULL,'City',NULL,'Volume',NULL,'Issue',NULL,NULL,NULL,'Edition','Date',NULL,'Short Title','Alt. Jour.',NULL,NULL,NULL,'2014-10-30 21:34:44'),(31,'Web Page',NULL,'Title',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-10-30 21:37:12');
/*!40000 ALTER TABLE `referencetype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `salixwordstats`
--

DROP TABLE IF EXISTS `salixwordstats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `salixwordstats` (
  `swsid` int(11) NOT NULL AUTO_INCREMENT,
  `firstword` varchar(45) NOT NULL,
  `secondword` varchar(45) DEFAULT NULL,
  `locality` int(4) NOT NULL DEFAULT '0',
  `localityFreq` int(4) NOT NULL DEFAULT '0',
  `habitat` int(4) NOT NULL DEFAULT '0',
  `habitatFreq` int(4) NOT NULL DEFAULT '0',
  `substrate` int(4) NOT NULL DEFAULT '0',
  `substrateFreq` int(4) NOT NULL DEFAULT '0',
  `verbatimAttributes` int(4) NOT NULL DEFAULT '0',
  `verbatimAttributesFreq` int(4) NOT NULL DEFAULT '0',
  `occurrenceRemarks` int(4) NOT NULL DEFAULT '0',
  `occurrenceRemarksFreq` int(4) NOT NULL DEFAULT '0',
  `totalcount` int(4) NOT NULL DEFAULT '0',
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`swsid`),
  UNIQUE KEY `INDEX_unique` (`firstword`,`secondword`),
  KEY `INDEX_secondword` (`secondword`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salixwordstats`
--

LOCK TABLES `salixwordstats` WRITE;
/*!40000 ALTER TABLE `salixwordstats` DISABLE KEYS */;
/*!40000 ALTER TABLE `salixwordstats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schemaversion`
--

DROP TABLE IF EXISTS `schemaversion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `schemaversion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `versionnumber` varchar(20) NOT NULL,
  `dateapplied` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `versionnumber_UNIQUE` (`versionnumber`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schemaversion`
--

LOCK TABLES `schemaversion` WRITE;
/*!40000 ALTER TABLE `schemaversion` DISABLE KEYS */;
INSERT INTO `schemaversion` VALUES (6,'1.0','2022-10-18 23:21:41'),(7,'1.1','2022-10-18 23:21:41'),(8,'1.2','2022-10-18 23:21:43');
/*!40000 ALTER TABLE `schemaversion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `specprocessorprojects`
--

DROP TABLE IF EXISTS `specprocessorprojects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `specprocessorprojects` (
  `spprid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `collid` int(10) unsigned NOT NULL,
  `title` varchar(100) NOT NULL,
  `projecttype` varchar(45) DEFAULT NULL,
  `specKeyPattern` varchar(45) DEFAULT NULL,
  `patternReplace` varchar(45) DEFAULT NULL,
  `replaceStr` varchar(45) DEFAULT NULL,
  `speckeyretrieval` varchar(45) DEFAULT NULL,
  `coordX1` int(10) unsigned DEFAULT NULL,
  `coordX2` int(10) unsigned DEFAULT NULL,
  `coordY1` int(10) unsigned DEFAULT NULL,
  `coordY2` int(10) unsigned DEFAULT NULL,
  `sourcePath` varchar(250) DEFAULT NULL,
  `targetPath` varchar(250) DEFAULT NULL,
  `imgUrl` varchar(250) DEFAULT NULL,
  `webPixWidth` int(10) unsigned DEFAULT '1200',
  `tnPixWidth` int(10) unsigned DEFAULT '130',
  `lgPixWidth` int(10) unsigned DEFAULT '2400',
  `jpgcompression` int(11) DEFAULT '70',
  `createTnImg` int(10) unsigned DEFAULT '1',
  `createLgImg` int(10) unsigned DEFAULT '1',
  `source` varchar(45) DEFAULT NULL,
  `lastrundate` date DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`spprid`),
  KEY `FK_specprocessorprojects_coll` (`collid`),
  CONSTRAINT `FK_specprocessorprojects_coll` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `specprocessorprojects`
--

LOCK TABLES `specprocessorprojects` WRITE;
/*!40000 ALTER TABLE `specprocessorprojects` DISABLE KEYS */;
/*!40000 ALTER TABLE `specprocessorprojects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `specprocessorrawlabels`
--

DROP TABLE IF EXISTS `specprocessorrawlabels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `specprocessorrawlabels` (
  `prlid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `imgid` int(10) unsigned DEFAULT NULL,
  `occid` int(10) unsigned DEFAULT NULL,
  `rawstr` text NOT NULL,
  `processingvariables` varchar(250) DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `source` varchar(150) DEFAULT NULL,
  `sortsequence` int(11) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`prlid`),
  KEY `FK_specproc_images` (`imgid`),
  KEY `FK_specproc_occid` (`occid`),
  CONSTRAINT `FK_specproc_images` FOREIGN KEY (`imgid`) REFERENCES `images` (`imgid`) ON UPDATE CASCADE,
  CONSTRAINT `FK_specproc_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `specprocessorrawlabels`
--

LOCK TABLES `specprocessorrawlabels` WRITE;
/*!40000 ALTER TABLE `specprocessorrawlabels` DISABLE KEYS */;
/*!40000 ALTER TABLE `specprocessorrawlabels` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `specprocessorrawlabelsfulltext_insert` AFTER INSERT ON `specprocessorrawlabels`
FOR EACH ROW BEGIN
  INSERT INTO specprocessorrawlabelsfulltext (
    `prlid`,
    `imgid`,
    `rawstr`
  ) VALUES (
    NEW.`prlid`,
    NEW.`imgid`,
    NEW.`rawstr`
  );
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `specprocessorrawlabelsfulltext_update` AFTER UPDATE ON `specprocessorrawlabels`
FOR EACH ROW BEGIN
  UPDATE specprocessorrawlabelsfulltext SET
    `imgid` = NEW.`imgid`,
    `rawstr` = NEW.`rawstr`
  WHERE `prlid` = NEW.`prlid`;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `specprocessorrawlabelsfulltext`
--

DROP TABLE IF EXISTS `specprocessorrawlabelsfulltext`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `specprocessorrawlabelsfulltext` (
  `prlid` int(11) NOT NULL,
  `imgid` int(11) NOT NULL,
  `rawstr` text NOT NULL,
  PRIMARY KEY (`prlid`),
  KEY `Index_ocr_imgid` (`imgid`),
  FULLTEXT KEY `Index_ocr_fulltext` (`rawstr`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `specprocessorrawlabelsfulltext`
--

LOCK TABLES `specprocessorrawlabelsfulltext` WRITE;
/*!40000 ALTER TABLE `specprocessorrawlabelsfulltext` DISABLE KEYS */;
/*!40000 ALTER TABLE `specprocessorrawlabelsfulltext` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `specprocessorrawlabelsfulltext_delete` BEFORE DELETE ON `specprocessorrawlabelsfulltext`
FOR EACH ROW BEGIN
  DELETE FROM specprocessorrawlabelsfulltext WHERE `prlid` = OLD.`prlid`;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `specprocnlp`
--

DROP TABLE IF EXISTS `specprocnlp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `specprocnlp` (
  `spnlpid` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(45) NOT NULL,
  `sqlfrag` varchar(250) NOT NULL,
  `patternmatch` varchar(250) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `collid` int(10) unsigned NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`spnlpid`),
  KEY `FK_specprocnlp_collid` (`collid`),
  CONSTRAINT `FK_specprocnlp_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `specprocnlp`
--

LOCK TABLES `specprocnlp` WRITE;
/*!40000 ALTER TABLE `specprocnlp` DISABLE KEYS */;
/*!40000 ALTER TABLE `specprocnlp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `specprocnlpfrag`
--

DROP TABLE IF EXISTS `specprocnlpfrag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `specprocnlpfrag` (
  `spnlpfragid` int(10) NOT NULL AUTO_INCREMENT,
  `spnlpid` int(10) NOT NULL,
  `fieldname` varchar(45) NOT NULL,
  `patternmatch` varchar(250) NOT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `sortseq` int(5) DEFAULT '50',
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`spnlpfragid`),
  KEY `FK_specprocnlpfrag_spnlpid` (`spnlpid`),
  CONSTRAINT `FK_specprocnlpfrag_spnlpid` FOREIGN KEY (`spnlpid`) REFERENCES `specprocnlp` (`spnlpid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `specprocnlpfrag`
--

LOCK TABLES `specprocnlpfrag` WRITE;
/*!40000 ALTER TABLE `specprocnlpfrag` DISABLE KEYS */;
/*!40000 ALTER TABLE `specprocnlpfrag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `specprocnlpversion`
--

DROP TABLE IF EXISTS `specprocnlpversion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `specprocnlpversion` (
  `nlpverid` int(11) NOT NULL AUTO_INCREMENT,
  `prlid` int(10) unsigned NOT NULL,
  `archivestr` text NOT NULL,
  `processingvariables` varchar(250) DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `source` varchar(150) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`nlpverid`),
  KEY `FK_specprocnlpver_rawtext_idx` (`prlid`),
  CONSTRAINT `FK_specprocnlpver_rawtext` FOREIGN KEY (`prlid`) REFERENCES `specprocessorrawlabels` (`prlid`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Archives field name - value pairs of NLP results loading into an omoccurrence record. This way, results can be easily redone at a later date without copying over date modifed afterward by another user or process ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `specprocnlpversion`
--

LOCK TABLES `specprocnlpversion` WRITE;
/*!40000 ALTER TABLE `specprocnlpversion` DISABLE KEYS */;
/*!40000 ALTER TABLE `specprocnlpversion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `specprococrfrag`
--

DROP TABLE IF EXISTS `specprococrfrag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `specprococrfrag` (
  `ocrfragid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `prlid` int(10) unsigned NOT NULL,
  `firstword` varchar(45) NOT NULL,
  `secondword` varchar(45) DEFAULT NULL,
  `keyterm` varchar(45) DEFAULT NULL,
  `wordorder` int(11) DEFAULT NULL,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ocrfragid`),
  KEY `FK_specprococrfrag_prlid_idx` (`prlid`),
  KEY `Index_keyterm` (`keyterm`),
  CONSTRAINT `FK_specprococrfrag_prlid` FOREIGN KEY (`prlid`) REFERENCES `specprocessorrawlabels` (`prlid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `specprococrfrag`
--

LOCK TABLES `specprococrfrag` WRITE;
/*!40000 ALTER TABLE `specprococrfrag` DISABLE KEYS */;
/*!40000 ALTER TABLE `specprococrfrag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `specprocstatus`
--

DROP TABLE IF EXISTS `specprocstatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `specprocstatus` (
  `spsID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `processName` varchar(45) NOT NULL,
  `result` varchar(45) DEFAULT NULL,
  `processVariables` varchar(150) NOT NULL,
  `processorUid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`spsID`),
  KEY `specprocstatus_occid_idx` (`occid`),
  KEY `specprocstatus_uid_idx` (`processorUid`),
  CONSTRAINT `specprocstatus_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `specprocstatus_uid` FOREIGN KEY (`processorUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `specprocstatus`
--

LOCK TABLES `specprocstatus` WRITE;
/*!40000 ALTER TABLE `specprocstatus` DISABLE KEYS */;
/*!40000 ALTER TABLE `specprocstatus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `taxa`
--

DROP TABLE IF EXISTS `taxa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxa` (
  `TID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kingdomName` varchar(45) DEFAULT NULL,
  `RankId` smallint(5) unsigned DEFAULT NULL,
  `SciName` varchar(250) NOT NULL,
  `UnitInd1` varchar(1) DEFAULT NULL,
  `UnitName1` varchar(50) NOT NULL,
  `UnitInd2` varchar(1) DEFAULT NULL,
  `UnitName2` varchar(50) DEFAULT NULL,
  `unitInd3` varchar(45) DEFAULT NULL,
  `UnitName3` varchar(35) DEFAULT NULL,
  `Author` varchar(100) DEFAULT NULL,
  `PhyloSortSequence` tinyint(3) unsigned DEFAULT NULL,
  `reviewStatus` int(11) DEFAULT NULL,
  `displayStatus` int(11) DEFAULT NULL,
  `isLegitimate` int(11) DEFAULT NULL,
  `nomenclaturalStatus` varchar(45) DEFAULT NULL,
  `nomenclaturalCode` varchar(45) DEFAULT NULL,
  `statusNotes` varchar(50) DEFAULT NULL,
  `Source` varchar(250) DEFAULT NULL,
  `Notes` varchar(250) DEFAULT NULL,
  `Hybrid` varchar(50) DEFAULT NULL,
  `SecurityStatus` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '0 = no security; 1 = hidden locality',
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `modifiedTimeStamp` datetime DEFAULT NULL,
  `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`TID`),
  UNIQUE KEY `sciname_unique` (`SciName`,`RankId`,`Author`),
  KEY `rankid_index` (`RankId`),
  KEY `unitname1_index` (`UnitName1`,`UnitName2`) USING BTREE,
  KEY `FK_taxa_uid_idx` (`modifiedUid`),
  KEY `sciname_index` (`SciName`),
  KEY `idx_taxa_kingdomName` (`kingdomName`),
  KEY `idx_taxacreated` (`InitialTimeStamp`),
  CONSTRAINT `FK_taxa_uid` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taxa`
--

LOCK TABLES `taxa` WRITE;
/*!40000 ALTER TABLE `taxa` DISABLE KEYS */;
INSERT INTO `taxa` VALUES (1,NULL,1,'Organism',NULL,'Organism',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,'2022-10-18 23:21:41'),(2,NULL,10,'Monera',NULL,'Monera',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,'2022-10-18 23:21:41'),(3,NULL,10,'Protista',NULL,'Protista',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,'2022-10-18 23:21:41'),(4,NULL,10,'Plantae',NULL,'Plantae',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,'2022-10-18 23:21:41'),(5,NULL,10,'Fungi',NULL,'Fungi',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,'2022-10-18 23:21:41'),(6,NULL,10,'Animalia',NULL,'Animalia',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,'2022-10-18 23:21:41');
/*!40000 ALTER TABLE `taxa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `taxadescrblock`
--

DROP TABLE IF EXISTS `taxadescrblock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxadescrblock` (
  `tdbid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned NOT NULL,
  `caption` varchar(40) DEFAULT NULL,
  `source` varchar(250) DEFAULT NULL,
  `sourceurl` varchar(250) DEFAULT NULL,
  `language` varchar(45) DEFAULT 'English',
  `langid` int(11) DEFAULT NULL,
  `displaylevel` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '1 = short descr, 2 = intermediate descr',
  `uid` int(10) unsigned NOT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tdbid`),
  KEY `FK_taxadesc_lang_idx` (`langid`),
  KEY `FK_taxadescrblock_tid_idx` (`tid`),
  CONSTRAINT `FK_taxadesc_lang` FOREIGN KEY (`langid`) REFERENCES `adminlanguages` (`langid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_taxadescrblock_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taxadescrblock`
--

LOCK TABLES `taxadescrblock` WRITE;
/*!40000 ALTER TABLE `taxadescrblock` DISABLE KEYS */;
/*!40000 ALTER TABLE `taxadescrblock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `taxadescrstmts`
--

DROP TABLE IF EXISTS `taxadescrstmts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxadescrstmts` (
  `tdsid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tdbid` int(10) unsigned NOT NULL,
  `heading` varchar(75) DEFAULT NULL,
  `statement` text NOT NULL,
  `displayheader` int(10) unsigned NOT NULL DEFAULT '1',
  `notes` varchar(250) DEFAULT NULL,
  `sortsequence` int(10) unsigned NOT NULL DEFAULT '89',
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tdsid`),
  KEY `FK_taxadescrstmts_tblock` (`tdbid`),
  CONSTRAINT `FK_taxadescrstmts_tblock` FOREIGN KEY (`tdbid`) REFERENCES `taxadescrblock` (`tdbid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taxadescrstmts`
--

LOCK TABLES `taxadescrstmts` WRITE;
/*!40000 ALTER TABLE `taxadescrstmts` DISABLE KEYS */;
/*!40000 ALTER TABLE `taxadescrstmts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `taxaenumtree`
--

DROP TABLE IF EXISTS `taxaenumtree`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxaenumtree` (
  `tid` int(10) unsigned NOT NULL,
  `taxauthid` int(10) unsigned NOT NULL,
  `parenttid` int(10) unsigned NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tid`,`taxauthid`,`parenttid`),
  KEY `FK_tet_taxa` (`tid`),
  KEY `FK_tet_taxauth` (`taxauthid`),
  KEY `FK_tet_taxa2` (`parenttid`),
  CONSTRAINT `FK_tet_taxa` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_tet_taxa2` FOREIGN KEY (`parenttid`) REFERENCES `taxa` (`TID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_tet_taxauth` FOREIGN KEY (`taxauthid`) REFERENCES `taxauthority` (`taxauthid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taxaenumtree`
--

LOCK TABLES `taxaenumtree` WRITE;
/*!40000 ALTER TABLE `taxaenumtree` DISABLE KEYS */;
/*!40000 ALTER TABLE `taxaenumtree` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `taxalinks`
--

DROP TABLE IF EXISTS `taxalinks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxalinks` (
  `tlid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned NOT NULL,
  `url` varchar(500) NOT NULL,
  `title` varchar(100) NOT NULL,
  `sourceIdentifier` varchar(45) DEFAULT NULL,
  `owner` varchar(100) DEFAULT NULL,
  `icon` varchar(45) DEFAULT NULL,
  `inherit` int(11) DEFAULT '1',
  `notes` varchar(250) DEFAULT NULL,
  `sortsequence` int(10) unsigned NOT NULL DEFAULT '50',
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`tlid`),
  KEY `Index_unique` (`tid`,`url`(255)),
  CONSTRAINT `FK_taxalinks_taxa` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taxalinks`
--

LOCK TABLES `taxalinks` WRITE;
/*!40000 ALTER TABLE `taxalinks` DISABLE KEYS */;
/*!40000 ALTER TABLE `taxalinks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `taxamaps`
--

DROP TABLE IF EXISTS `taxamaps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxamaps` (
  `mid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned NOT NULL,
  `url` varchar(255) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`mid`),
  KEY `FK_tid_idx` (`tid`),
  CONSTRAINT `FK_taxamaps_taxa` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taxamaps`
--

LOCK TABLES `taxamaps` WRITE;
/*!40000 ALTER TABLE `taxamaps` DISABLE KEYS */;
/*!40000 ALTER TABLE `taxamaps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `taxanestedtree`
--

DROP TABLE IF EXISTS `taxanestedtree`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxanestedtree` (
  `tid` int(10) unsigned NOT NULL,
  `taxauthid` int(10) unsigned NOT NULL,
  `leftindex` int(10) unsigned NOT NULL,
  `rightindex` int(10) unsigned NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tid`,`taxauthid`),
  KEY `leftindex` (`leftindex`),
  KEY `rightindex` (`rightindex`),
  KEY `FK_tnt_taxa` (`tid`),
  KEY `FK_tnt_taxauth` (`taxauthid`),
  CONSTRAINT `FK_tnt_taxa` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_tnt_taxauth` FOREIGN KEY (`taxauthid`) REFERENCES `taxauthority` (`taxauthid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taxanestedtree`
--

LOCK TABLES `taxanestedtree` WRITE;
/*!40000 ALTER TABLE `taxanestedtree` DISABLE KEYS */;
/*!40000 ALTER TABLE `taxanestedtree` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `taxaprofilepubdesclink`
--

DROP TABLE IF EXISTS `taxaprofilepubdesclink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxaprofilepubdesclink` (
  `tdbid` int(10) unsigned NOT NULL,
  `tppid` int(11) NOT NULL,
  `caption` varchar(45) DEFAULT NULL,
  `editornotes` varchar(250) DEFAULT NULL,
  `sortsequence` int(11) DEFAULT NULL,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tdbid`,`tppid`),
  KEY `FK_tppubdesclink_id_idx` (`tppid`),
  CONSTRAINT `FK_tppubdesclink_id` FOREIGN KEY (`tppid`) REFERENCES `taxaprofilepubs` (`tppid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_tppubdesclink_tdbid` FOREIGN KEY (`tdbid`) REFERENCES `taxadescrblock` (`tdbid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taxaprofilepubdesclink`
--

LOCK TABLES `taxaprofilepubdesclink` WRITE;
/*!40000 ALTER TABLE `taxaprofilepubdesclink` DISABLE KEYS */;
/*!40000 ALTER TABLE `taxaprofilepubdesclink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `taxaprofilepubimagelink`
--

DROP TABLE IF EXISTS `taxaprofilepubimagelink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxaprofilepubimagelink` (
  `imgid` int(10) unsigned NOT NULL,
  `tppid` int(11) NOT NULL,
  `caption` varchar(45) DEFAULT NULL,
  `editornotes` varchar(250) DEFAULT NULL,
  `sortsequence` int(11) DEFAULT NULL,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`imgid`,`tppid`),
  KEY `FK_tppubimagelink_id_idx` (`tppid`),
  CONSTRAINT `FK_tppubimagelink_id` FOREIGN KEY (`tppid`) REFERENCES `taxaprofilepubs` (`tppid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_tppubimagelink_imgid` FOREIGN KEY (`imgid`) REFERENCES `images` (`imgid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taxaprofilepubimagelink`
--

LOCK TABLES `taxaprofilepubimagelink` WRITE;
/*!40000 ALTER TABLE `taxaprofilepubimagelink` DISABLE KEYS */;
/*!40000 ALTER TABLE `taxaprofilepubimagelink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `taxaprofilepubmaplink`
--

DROP TABLE IF EXISTS `taxaprofilepubmaplink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxaprofilepubmaplink` (
  `mid` int(10) unsigned NOT NULL,
  `tppid` int(11) NOT NULL,
  `caption` varchar(45) DEFAULT NULL,
  `editornotes` varchar(250) DEFAULT NULL,
  `sortsequence` int(11) DEFAULT NULL,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`mid`,`tppid`),
  KEY `FK_tppubmaplink_id_idx` (`tppid`),
  CONSTRAINT `FK_tppubmaplink_id` FOREIGN KEY (`tppid`) REFERENCES `taxaprofilepubs` (`tppid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_tppubmaplink_tdbid` FOREIGN KEY (`mid`) REFERENCES `taxamaps` (`mid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taxaprofilepubmaplink`
--

LOCK TABLES `taxaprofilepubmaplink` WRITE;
/*!40000 ALTER TABLE `taxaprofilepubmaplink` DISABLE KEYS */;
/*!40000 ALTER TABLE `taxaprofilepubmaplink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `taxaprofilepubs`
--

DROP TABLE IF EXISTS `taxaprofilepubs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxaprofilepubs` (
  `tppid` int(11) NOT NULL AUTO_INCREMENT,
  `pubtitle` varchar(150) NOT NULL,
  `authors` varchar(150) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `abstract` text,
  `uidowner` int(10) unsigned DEFAULT NULL,
  `externalurl` varchar(250) DEFAULT NULL,
  `rights` varchar(250) DEFAULT NULL,
  `usageterm` varchar(250) DEFAULT NULL,
  `accessrights` varchar(250) DEFAULT NULL,
  `ispublic` int(11) DEFAULT NULL,
  `inclusive` int(11) DEFAULT NULL,
  `dynamicProperties` text,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tppid`),
  KEY `FK_taxaprofilepubs_uid_idx` (`uidowner`),
  KEY `INDEX_taxaprofilepubs_title` (`pubtitle`),
  CONSTRAINT `FK_taxaprofilepubs_uid` FOREIGN KEY (`uidowner`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taxaprofilepubs`
--

LOCK TABLES `taxaprofilepubs` WRITE;
/*!40000 ALTER TABLE `taxaprofilepubs` DISABLE KEYS */;
/*!40000 ALTER TABLE `taxaprofilepubs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `taxaresourcelinks`
--

DROP TABLE IF EXISTS `taxaresourcelinks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxaresourcelinks` (
  `taxaresourceid` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned NOT NULL,
  `sourcename` varchar(150) NOT NULL,
  `sourceidentifier` varchar(45) DEFAULT NULL,
  `sourceguid` varchar(150) DEFAULT NULL,
  `url` varchar(250) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `ranking` int(11) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`taxaresourceid`),
  UNIQUE KEY `UNIQUE_taxaresource` (`tid`,`sourcename`),
  KEY `taxaresource_name` (`sourcename`),
  KEY `FK_taxaresource_tid_idx` (`tid`),
  CONSTRAINT `FK_taxaresource_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taxaresourcelinks`
--

LOCK TABLES `taxaresourcelinks` WRITE;
/*!40000 ALTER TABLE `taxaresourcelinks` DISABLE KEYS */;
/*!40000 ALTER TABLE `taxaresourcelinks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `taxauthority`
--

DROP TABLE IF EXISTS `taxauthority`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxauthority` (
  `taxauthid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `isprimary` int(1) unsigned NOT NULL DEFAULT '0',
  `name` varchar(45) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `editors` varchar(150) DEFAULT NULL,
  `contact` varchar(45) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `url` varchar(150) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `isactive` int(1) unsigned NOT NULL DEFAULT '1',
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`taxauthid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taxauthority`
--

LOCK TABLES `taxauthority` WRITE;
/*!40000 ALTER TABLE `taxauthority` DISABLE KEYS */;
INSERT INTO `taxauthority` VALUES (1,1,'Central Thesaurus',NULL,NULL,NULL,NULL,NULL,NULL,1,'2022-10-18 23:21:41');
/*!40000 ALTER TABLE `taxauthority` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `taxavernaculars`
--

DROP TABLE IF EXISTS `taxavernaculars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxavernaculars` (
  `TID` int(10) unsigned NOT NULL DEFAULT '0',
  `VernacularName` varchar(80) NOT NULL,
  `Language` varchar(15) DEFAULT NULL,
  `langid` int(11) DEFAULT NULL,
  `Source` varchar(50) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `username` varchar(45) DEFAULT NULL,
  `isupperterm` int(2) DEFAULT '0',
  `SortSequence` int(10) DEFAULT '50',
  `VID` int(10) NOT NULL AUTO_INCREMENT,
  `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`VID`),
  UNIQUE KEY `unique-key` (`VernacularName`,`TID`,`langid`),
  KEY `tid1` (`TID`),
  KEY `vernacularsnames` (`VernacularName`),
  KEY `FK_vern_lang_idx` (`langid`),
  CONSTRAINT `FK_vern_lang` FOREIGN KEY (`langid`) REFERENCES `adminlanguages` (`langid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_vernaculars_tid` FOREIGN KEY (`TID`) REFERENCES `taxa` (`TID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taxavernaculars`
--

LOCK TABLES `taxavernaculars` WRITE;
/*!40000 ALTER TABLE `taxavernaculars` DISABLE KEYS */;
/*!40000 ALTER TABLE `taxavernaculars` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `taxonunits`
--

DROP TABLE IF EXISTS `taxonunits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxonunits` (
  `taxonunitid` int(11) NOT NULL AUTO_INCREMENT,
  `kingdomName` varchar(45) NOT NULL DEFAULT 'Organism',
  `rankid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `rankname` varchar(15) NOT NULL,
  `suffix` varchar(45) DEFAULT NULL,
  `dirparentrankid` smallint(6) NOT NULL,
  `reqparentrankid` smallint(6) DEFAULT NULL,
  `modifiedby` varchar(45) DEFAULT NULL,
  `modifiedtimestamp` datetime DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`taxonunitid`),
  UNIQUE KEY `UNIQUE_taxonunits` (`kingdomName`,`rankid`)
) ENGINE=InnoDB AUTO_INCREMENT=153 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taxonunits`
--

LOCK TABLES `taxonunits` WRITE;
/*!40000 ALTER TABLE `taxonunits` DISABLE KEYS */;
INSERT INTO `taxonunits` VALUES (24,'Organism',1,'Organism',NULL,1,1,NULL,NULL,'2022-10-18 23:21:41'),(25,'Organism',10,'Kingdom',NULL,1,1,NULL,NULL,'2022-10-18 23:21:41'),(26,'Organism',20,'Subkingdom',NULL,10,10,NULL,NULL,'2022-10-18 23:21:41'),(27,'Organism',30,'Division',NULL,20,10,NULL,NULL,'2022-10-18 23:21:41'),(28,'Organism',40,'Subdivision',NULL,30,30,NULL,NULL,'2022-10-18 23:21:41'),(29,'Organism',50,'Superclass',NULL,40,30,NULL,NULL,'2022-10-18 23:21:41'),(30,'Organism',60,'Class',NULL,50,30,NULL,NULL,'2022-10-18 23:21:41'),(31,'Organism',70,'Subclass',NULL,60,60,NULL,NULL,'2022-10-18 23:21:41'),(32,'Organism',100,'Order',NULL,70,60,NULL,NULL,'2022-10-18 23:21:41'),(33,'Organism',110,'Suborder',NULL,100,100,NULL,NULL,'2022-10-18 23:21:41'),(34,'Organism',140,'Family',NULL,110,100,NULL,NULL,'2022-10-18 23:21:41'),(35,'Organism',150,'Subfamily',NULL,140,140,NULL,NULL,'2022-10-18 23:21:41'),(36,'Organism',160,'Tribe',NULL,150,140,NULL,NULL,'2022-10-18 23:21:41'),(37,'Organism',170,'Subtribe',NULL,160,140,NULL,NULL,'2022-10-18 23:21:41'),(38,'Organism',180,'Genus',NULL,170,140,NULL,NULL,'2022-10-18 23:21:41'),(39,'Organism',190,'Subgenus',NULL,180,180,NULL,NULL,'2022-10-18 23:21:41'),(40,'Organism',200,'Section',NULL,190,180,NULL,NULL,'2022-10-18 23:21:41'),(41,'Organism',210,'Subsection',NULL,200,180,NULL,NULL,'2022-10-18 23:21:41'),(42,'Organism',220,'Species',NULL,210,180,NULL,NULL,'2022-10-18 23:21:41'),(43,'Organism',230,'Subspecies',NULL,220,180,NULL,NULL,'2022-10-18 23:21:41'),(44,'Organism',240,'Variety',NULL,220,180,NULL,NULL,'2022-10-18 23:21:41'),(45,'Organism',250,'Subvariety',NULL,240,180,NULL,NULL,'2022-10-18 23:21:41'),(46,'Organism',260,'Form',NULL,220,180,NULL,NULL,'2022-10-18 23:21:41'),(47,'Organism',270,'Subform',NULL,260,180,NULL,NULL,'2022-10-18 23:21:41'),(48,'Organism',300,'Cultivated',NULL,220,220,NULL,NULL,'2022-10-18 23:21:41'),(49,'Monera',1,'Organism',NULL,1,1,NULL,NULL,'2022-10-18 23:21:41'),(50,'Monera',10,'Kingdom',NULL,1,1,NULL,NULL,'2022-10-18 23:21:41'),(51,'Monera',20,'Subkingdom',NULL,10,10,NULL,NULL,'2022-10-18 23:21:41'),(52,'Monera',30,'Phylum',NULL,20,10,NULL,NULL,'2022-10-18 23:21:41'),(53,'Monera',40,'Subphylum',NULL,30,30,NULL,NULL,'2022-10-18 23:21:41'),(54,'Monera',60,'Class',NULL,50,30,NULL,NULL,'2022-10-18 23:21:41'),(55,'Monera',70,'Subclass',NULL,60,60,NULL,NULL,'2022-10-18 23:21:41'),(56,'Monera',100,'Order',NULL,70,60,NULL,NULL,'2022-10-18 23:21:41'),(57,'Monera',110,'Suborder',NULL,100,100,NULL,NULL,'2022-10-18 23:21:41'),(58,'Monera',140,'Family',NULL,110,100,NULL,NULL,'2022-10-18 23:21:41'),(59,'Monera',150,'Subfamily',NULL,140,140,NULL,NULL,'2022-10-18 23:21:41'),(60,'Monera',160,'Tribe',NULL,150,140,NULL,NULL,'2022-10-18 23:21:41'),(61,'Monera',170,'Subtribe',NULL,160,140,NULL,NULL,'2022-10-18 23:21:41'),(62,'Monera',180,'Genus',NULL,170,140,NULL,NULL,'2022-10-18 23:21:41'),(63,'Monera',190,'Subgenus',NULL,180,180,NULL,NULL,'2022-10-18 23:21:41'),(64,'Monera',220,'Species',NULL,210,180,NULL,NULL,'2022-10-18 23:21:41'),(65,'Monera',230,'Subspecies',NULL,220,180,NULL,NULL,'2022-10-18 23:21:41'),(66,'Monera',240,'Morph',NULL,220,180,NULL,NULL,'2022-10-18 23:21:41'),(67,'Protista',1,'Organism',NULL,1,1,NULL,NULL,'2022-10-18 23:21:41'),(68,'Protista',10,'Kingdom',NULL,1,1,NULL,NULL,'2022-10-18 23:21:41'),(69,'Protista',20,'Subkingdom',NULL,10,10,NULL,NULL,'2022-10-18 23:21:41'),(70,'Protista',30,'Phylum',NULL,20,10,NULL,NULL,'2022-10-18 23:21:41'),(71,'Protista',40,'Subphylum',NULL,30,30,NULL,NULL,'2022-10-18 23:21:41'),(72,'Protista',60,'Class',NULL,50,30,NULL,NULL,'2022-10-18 23:21:41'),(73,'Protista',70,'Subclass',NULL,60,60,NULL,NULL,'2022-10-18 23:21:41'),(74,'Protista',100,'Order',NULL,70,60,NULL,NULL,'2022-10-18 23:21:41'),(75,'Protista',110,'Suborder',NULL,100,100,NULL,NULL,'2022-10-18 23:21:41'),(76,'Protista',140,'Family',NULL,110,100,NULL,NULL,'2022-10-18 23:21:41'),(77,'Protista',150,'Subfamily',NULL,140,140,NULL,NULL,'2022-10-18 23:21:41'),(78,'Protista',160,'Tribe',NULL,150,140,NULL,NULL,'2022-10-18 23:21:41'),(79,'Protista',170,'Subtribe',NULL,160,140,NULL,NULL,'2022-10-18 23:21:41'),(80,'Protista',180,'Genus',NULL,170,140,NULL,NULL,'2022-10-18 23:21:41'),(81,'Protista',190,'Subgenus',NULL,180,180,NULL,NULL,'2022-10-18 23:21:41'),(82,'Protista',220,'Species',NULL,210,180,NULL,NULL,'2022-10-18 23:21:41'),(83,'Protista',230,'Subspecies',NULL,220,180,NULL,NULL,'2022-10-18 23:21:41'),(84,'Protista',240,'Morph',NULL,220,180,NULL,NULL,'2022-10-18 23:21:41'),(85,'Plantae',1,'Organism',NULL,1,1,NULL,NULL,'2022-10-18 23:21:41'),(86,'Plantae',10,'Kingdom',NULL,1,1,NULL,NULL,'2022-10-18 23:21:41'),(87,'Plantae',20,'Subkingdom',NULL,10,10,NULL,NULL,'2022-10-18 23:21:41'),(88,'Plantae',30,'Division',NULL,20,10,NULL,NULL,'2022-10-18 23:21:41'),(89,'Plantae',40,'Subdivision',NULL,30,30,NULL,NULL,'2022-10-18 23:21:41'),(90,'Plantae',50,'Superclass',NULL,40,30,NULL,NULL,'2022-10-18 23:21:41'),(91,'Plantae',60,'Class',NULL,50,30,NULL,NULL,'2022-10-18 23:21:41'),(92,'Plantae',70,'Subclass',NULL,60,60,NULL,NULL,'2022-10-18 23:21:41'),(93,'Plantae',100,'Order',NULL,70,60,NULL,NULL,'2022-10-18 23:21:41'),(94,'Plantae',110,'Suborder',NULL,100,100,NULL,NULL,'2022-10-18 23:21:41'),(95,'Plantae',140,'Family',NULL,110,100,NULL,NULL,'2022-10-18 23:21:41'),(96,'Plantae',150,'Subfamily',NULL,140,140,NULL,NULL,'2022-10-18 23:21:41'),(97,'Plantae',160,'Tribe',NULL,150,140,NULL,NULL,'2022-10-18 23:21:41'),(98,'Plantae',170,'Subtribe',NULL,160,140,NULL,NULL,'2022-10-18 23:21:41'),(99,'Plantae',180,'Genus',NULL,170,140,NULL,NULL,'2022-10-18 23:21:41'),(100,'Plantae',190,'Subgenus',NULL,180,180,NULL,NULL,'2022-10-18 23:21:41'),(101,'Plantae',200,'Section',NULL,190,180,NULL,NULL,'2022-10-18 23:21:41'),(102,'Plantae',210,'Subsection',NULL,200,180,NULL,NULL,'2022-10-18 23:21:41'),(103,'Plantae',220,'Species',NULL,210,180,NULL,NULL,'2022-10-18 23:21:41'),(104,'Plantae',230,'Subspecies',NULL,220,180,NULL,NULL,'2022-10-18 23:21:41'),(105,'Plantae',240,'Variety',NULL,220,180,NULL,NULL,'2022-10-18 23:21:41'),(106,'Plantae',250,'Subvariety',NULL,240,180,NULL,NULL,'2022-10-18 23:21:41'),(107,'Plantae',260,'Form',NULL,220,180,NULL,NULL,'2022-10-18 23:21:41'),(108,'Plantae',270,'Subform',NULL,260,180,NULL,NULL,'2022-10-18 23:21:41'),(109,'Plantae',300,'Cultivated',NULL,220,220,NULL,NULL,'2022-10-18 23:21:41'),(110,'Fungi',1,'Organism',NULL,1,1,NULL,NULL,'2022-10-18 23:21:41'),(111,'Fungi',10,'Kingdom',NULL,1,1,NULL,NULL,'2022-10-18 23:21:41'),(112,'Fungi',20,'Subkingdom',NULL,10,10,NULL,NULL,'2022-10-18 23:21:41'),(113,'Fungi',30,'Division',NULL,20,10,NULL,NULL,'2022-10-18 23:21:41'),(114,'Fungi',40,'Subdivision',NULL,30,30,NULL,NULL,'2022-10-18 23:21:41'),(115,'Fungi',50,'Superclass',NULL,40,30,NULL,NULL,'2022-10-18 23:21:41'),(116,'Fungi',60,'Class',NULL,50,30,NULL,NULL,'2022-10-18 23:21:41'),(117,'Fungi',70,'Subclass',NULL,60,60,NULL,NULL,'2022-10-18 23:21:41'),(118,'Fungi',100,'Order',NULL,70,60,NULL,NULL,'2022-10-18 23:21:41'),(119,'Fungi',110,'Suborder',NULL,100,100,NULL,NULL,'2022-10-18 23:21:41'),(120,'Fungi',140,'Family',NULL,110,100,NULL,NULL,'2022-10-18 23:21:41'),(121,'Fungi',150,'Subfamily',NULL,140,140,NULL,NULL,'2022-10-18 23:21:41'),(122,'Fungi',160,'Tribe',NULL,150,140,NULL,NULL,'2022-10-18 23:21:41'),(123,'Fungi',170,'Subtribe',NULL,160,140,NULL,NULL,'2022-10-18 23:21:41'),(124,'Fungi',180,'Genus',NULL,170,140,NULL,NULL,'2022-10-18 23:21:41'),(125,'Fungi',190,'Subgenus',NULL,180,180,NULL,NULL,'2022-10-18 23:21:41'),(126,'Fungi',200,'Section',NULL,190,180,NULL,NULL,'2022-10-18 23:21:41'),(127,'Fungi',210,'Subsection',NULL,200,180,NULL,NULL,'2022-10-18 23:21:41'),(128,'Fungi',220,'Species',NULL,210,180,NULL,NULL,'2022-10-18 23:21:41'),(129,'Fungi',230,'Subspecies',NULL,220,180,NULL,NULL,'2022-10-18 23:21:41'),(130,'Fungi',240,'Variety',NULL,220,180,NULL,NULL,'2022-10-18 23:21:41'),(131,'Fungi',250,'Subvariety',NULL,240,180,NULL,NULL,'2022-10-18 23:21:41'),(132,'Fungi',260,'Form',NULL,220,180,NULL,NULL,'2022-10-18 23:21:41'),(133,'Fungi',270,'Subform',NULL,260,180,NULL,NULL,'2022-10-18 23:21:41'),(134,'Fungi',300,'Cultivated',NULL,220,220,NULL,NULL,'2022-10-18 23:21:41'),(135,'Animalia',1,'Organism',NULL,1,1,NULL,NULL,'2022-10-18 23:21:41'),(136,'Animalia',10,'Kingdom',NULL,1,1,NULL,NULL,'2022-10-18 23:21:41'),(137,'Animalia',20,'Subkingdom',NULL,10,10,NULL,NULL,'2022-10-18 23:21:41'),(138,'Animalia',30,'Phylum',NULL,20,10,NULL,NULL,'2022-10-18 23:21:41'),(139,'Animalia',40,'Subphylum',NULL,30,30,NULL,NULL,'2022-10-18 23:21:41'),(140,'Animalia',60,'Class',NULL,50,30,NULL,NULL,'2022-10-18 23:21:41'),(141,'Animalia',70,'Subclass',NULL,60,60,NULL,NULL,'2022-10-18 23:21:41'),(142,'Animalia',100,'Order',NULL,70,60,NULL,NULL,'2022-10-18 23:21:41'),(143,'Animalia',110,'Suborder',NULL,100,100,NULL,NULL,'2022-10-18 23:21:41'),(144,'Animalia',140,'Family',NULL,110,100,NULL,NULL,'2022-10-18 23:21:41'),(145,'Animalia',150,'Subfamily',NULL,140,140,NULL,NULL,'2022-10-18 23:21:41'),(146,'Animalia',160,'Tribe',NULL,150,140,NULL,NULL,'2022-10-18 23:21:41'),(147,'Animalia',170,'Subtribe',NULL,160,140,NULL,NULL,'2022-10-18 23:21:41'),(148,'Animalia',180,'Genus',NULL,170,140,NULL,NULL,'2022-10-18 23:21:41'),(149,'Animalia',190,'Subgenus',NULL,180,180,NULL,NULL,'2022-10-18 23:21:41'),(150,'Animalia',220,'Species',NULL,210,180,NULL,NULL,'2022-10-18 23:21:41'),(151,'Animalia',230,'Subspecies',NULL,220,180,NULL,NULL,'2022-10-18 23:21:41'),(152,'Animalia',240,'Morph',NULL,220,180,NULL,NULL,'2022-10-18 23:21:41');
/*!40000 ALTER TABLE `taxonunits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `taxstatus`
--

DROP TABLE IF EXISTS `taxstatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxstatus` (
  `tid` int(10) unsigned NOT NULL,
  `tidaccepted` int(10) unsigned NOT NULL,
  `taxauthid` int(10) unsigned NOT NULL COMMENT 'taxon authority id',
  `parenttid` int(10) unsigned DEFAULT NULL,
  `family` varchar(50) DEFAULT NULL,
  `taxonomicStatus` varchar(45) DEFAULT NULL,
  `taxonomicSource` varchar(45) DEFAULT NULL,
  `sourceIdentifier` varchar(150) DEFAULT NULL,
  `UnacceptabilityReason` varchar(250) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `SortSequence` int(10) unsigned DEFAULT '50',
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `modifiedTimestamp` datetime DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`tid`,`tidaccepted`,`taxauthid`) USING BTREE,
  KEY `FK_taxstatus_tidacc` (`tidaccepted`),
  KEY `FK_taxstatus_taid` (`taxauthid`),
  KEY `Index_ts_family` (`family`),
  KEY `Index_parenttid` (`parenttid`),
  KEY `FK_taxstatus_uid_idx` (`modifiedUid`),
  KEY `Index_tid` (`tid`),
  CONSTRAINT `FK_taxstatus_parent` FOREIGN KEY (`parenttid`) REFERENCES `taxa` (`TID`),
  CONSTRAINT `FK_taxstatus_taid` FOREIGN KEY (`taxauthid`) REFERENCES `taxauthority` (`taxauthid`) ON UPDATE CASCADE,
  CONSTRAINT `FK_taxstatus_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`),
  CONSTRAINT `FK_taxstatus_tidacc` FOREIGN KEY (`tidaccepted`) REFERENCES `taxa` (`TID`),
  CONSTRAINT `FK_taxstatus_uid` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taxstatus`
--

LOCK TABLES `taxstatus` WRITE;
/*!40000 ALTER TABLE `taxstatus` DISABLE KEYS */;
INSERT INTO `taxstatus` VALUES (1,1,1,1,NULL,NULL,NULL,NULL,NULL,NULL,50,NULL,NULL,'2022-10-18 23:21:41'),(2,2,1,1,NULL,NULL,NULL,NULL,NULL,NULL,50,NULL,NULL,'2022-10-18 23:21:41'),(3,3,1,1,NULL,NULL,NULL,NULL,NULL,NULL,50,NULL,NULL,'2022-10-18 23:21:41'),(4,4,1,1,NULL,NULL,NULL,NULL,NULL,NULL,50,NULL,NULL,'2022-10-18 23:21:41'),(5,5,1,1,NULL,NULL,NULL,NULL,NULL,NULL,50,NULL,NULL,'2022-10-18 23:21:41'),(6,6,1,1,NULL,NULL,NULL,NULL,NULL,NULL,50,NULL,NULL,'2022-10-18 23:21:41');
/*!40000 ALTER TABLE `taxstatus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tmattributes`
--

DROP TABLE IF EXISTS `tmattributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tmattributes` (
  `stateid` int(10) unsigned NOT NULL,
  `occid` int(10) unsigned NOT NULL,
  `modifier` varchar(100) DEFAULT NULL,
  `xvalue` double(15,5) DEFAULT NULL,
  `imgid` int(10) unsigned DEFAULT NULL,
  `imagecoordinates` varchar(45) DEFAULT NULL,
  `source` varchar(250) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `statuscode` tinyint(4) DEFAULT NULL,
  `modifieduid` int(10) unsigned DEFAULT NULL,
  `datelastmodified` datetime DEFAULT NULL,
  `createduid` int(10) unsigned DEFAULT NULL,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`stateid`,`occid`),
  KEY `FK_tmattr_stateid_idx` (`stateid`),
  KEY `FK_tmattr_occid_idx` (`occid`),
  KEY `FK_tmattr_imgid_idx` (`imgid`),
  KEY `FK_attr_uidcreate_idx` (`createduid`),
  KEY `FK_tmattr_uidmodified_idx` (`modifieduid`),
  CONSTRAINT `FK_tmattr_imgid` FOREIGN KEY (`imgid`) REFERENCES `images` (`imgid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_tmattr_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_tmattr_stateid` FOREIGN KEY (`stateid`) REFERENCES `tmstates` (`stateid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_tmattr_uidcreate` FOREIGN KEY (`createduid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_tmattr_uidmodified` FOREIGN KEY (`modifieduid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tmattributes`
--

LOCK TABLES `tmattributes` WRITE;
/*!40000 ALTER TABLE `tmattributes` DISABLE KEYS */;
/*!40000 ALTER TABLE `tmattributes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tmstates`
--

DROP TABLE IF EXISTS `tmstates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tmstates` (
  `stateid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `traitid` int(10) unsigned NOT NULL,
  `statecode` varchar(2) NOT NULL,
  `statename` varchar(75) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `refurl` varchar(250) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `sortseq` int(11) DEFAULT NULL,
  `modifieduid` int(10) unsigned DEFAULT NULL,
  `datelastmodified` datetime DEFAULT NULL,
  `createduid` int(10) unsigned DEFAULT NULL,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`stateid`),
  UNIQUE KEY `traitid_code_UNIQUE` (`traitid`,`statecode`),
  KEY `FK_tmstate_uidcreated_idx` (`createduid`),
  KEY `FK_tmstate_uidmodified_idx` (`modifieduid`),
  CONSTRAINT `FK_tmstates_traits` FOREIGN KEY (`traitid`) REFERENCES `tmtraits` (`traitid`) ON UPDATE CASCADE,
  CONSTRAINT `FK_tmstates_uidcreated` FOREIGN KEY (`createduid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_tmstates_uidmodified` FOREIGN KEY (`modifieduid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tmstates`
--

LOCK TABLES `tmstates` WRITE;
/*!40000 ALTER TABLE `tmstates` DISABLE KEYS */;
/*!40000 ALTER TABLE `tmstates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tmtraitdependencies`
--

DROP TABLE IF EXISTS `tmtraitdependencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tmtraitdependencies` (
  `traitid` int(10) unsigned NOT NULL,
  `parentstateid` int(10) unsigned NOT NULL,
  `initialtimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`traitid`,`parentstateid`),
  KEY `FK_tmdepend_traitid_idx` (`traitid`),
  KEY `FK_tmdepend_stateid_idx` (`parentstateid`),
  CONSTRAINT `FK_tmdepend_stateid` FOREIGN KEY (`parentstateid`) REFERENCES `tmstates` (`stateid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_tmdepend_traitid` FOREIGN KEY (`traitid`) REFERENCES `tmtraits` (`traitid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tmtraitdependencies`
--

LOCK TABLES `tmtraitdependencies` WRITE;
/*!40000 ALTER TABLE `tmtraitdependencies` DISABLE KEYS */;
/*!40000 ALTER TABLE `tmtraitdependencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tmtraits`
--

DROP TABLE IF EXISTS `tmtraits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tmtraits` (
  `traitid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `traitname` varchar(100) NOT NULL,
  `traittype` varchar(2) NOT NULL DEFAULT 'UM',
  `units` varchar(45) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `refurl` varchar(250) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `projectGroup` varchar(45) DEFAULT NULL,
  `isPublic` int(11) DEFAULT '1',
  `includeInSearch` int(11) DEFAULT NULL,
  `dynamicProperties` text,
  `modifieduid` int(10) unsigned DEFAULT NULL,
  `datelastmodified` datetime DEFAULT NULL,
  `createduid` int(10) unsigned DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`traitid`),
  KEY `traitsname` (`traitname`),
  KEY `FK_traits_uidcreated_idx` (`createduid`),
  KEY `FK_traits_uidmodified_idx` (`modifieduid`),
  CONSTRAINT `FK_traits_uidcreated` FOREIGN KEY (`createduid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_traits_uidmodified` FOREIGN KEY (`modifieduid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tmtraits`
--

LOCK TABLES `tmtraits` WRITE;
/*!40000 ALTER TABLE `tmtraits` DISABLE KEYS */;
/*!40000 ALTER TABLE `tmtraits` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tmtraittaxalink`
--

DROP TABLE IF EXISTS `tmtraittaxalink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tmtraittaxalink` (
  `traitid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `relation` varchar(45) NOT NULL DEFAULT 'include',
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`traitid`,`tid`),
  KEY `FK_traittaxalink_traitid_idx` (`traitid`),
  KEY `FK_traittaxalink_tid_idx` (`tid`),
  CONSTRAINT `FK_traittaxalink_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_traittaxalink_traitid` FOREIGN KEY (`traitid`) REFERENCES `tmtraits` (`traitid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tmtraittaxalink`
--

LOCK TABLES `tmtraittaxalink` WRITE;
/*!40000 ALTER TABLE `tmtraittaxalink` DISABLE KEYS */;
/*!40000 ALTER TABLE `tmtraittaxalink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unknowncomments`
--

DROP TABLE IF EXISTS `unknowncomments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unknowncomments` (
  `unkcomid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `unkid` int(10) unsigned NOT NULL,
  `comment` varchar(500) NOT NULL,
  `username` varchar(45) NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`unkcomid`) USING BTREE,
  KEY `FK_unknowncomments` (`unkid`),
  CONSTRAINT `FK_unknowncomments` FOREIGN KEY (`unkid`) REFERENCES `unknowns` (`unkid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unknowncomments`
--

LOCK TABLES `unknowncomments` WRITE;
/*!40000 ALTER TABLE `unknowncomments` DISABLE KEYS */;
/*!40000 ALTER TABLE `unknowncomments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unknownimages`
--

DROP TABLE IF EXISTS `unknownimages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unknownimages` (
  `unkimgid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `unkid` int(10) unsigned NOT NULL,
  `url` varchar(255) NOT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`unkimgid`),
  KEY `FK_unknowns` (`unkid`),
  CONSTRAINT `FK_unknowns` FOREIGN KEY (`unkid`) REFERENCES `unknowns` (`unkid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unknownimages`
--

LOCK TABLES `unknownimages` WRITE;
/*!40000 ALTER TABLE `unknownimages` DISABLE KEYS */;
/*!40000 ALTER TABLE `unknownimages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unknowns`
--

DROP TABLE IF EXISTS `unknowns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unknowns` (
  `unkid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned DEFAULT NULL,
  `photographer` varchar(100) DEFAULT NULL,
  `owner` varchar(100) DEFAULT NULL,
  `locality` varchar(250) DEFAULT NULL,
  `latdecimal` double DEFAULT NULL,
  `longdecimal` double DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `username` varchar(45) NOT NULL,
  `idstatus` varchar(45) NOT NULL DEFAULT 'ID pending',
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`unkid`) USING BTREE,
  KEY `FK_unknowns_username` (`username`),
  KEY `FK_unknowns_tid` (`tid`),
  CONSTRAINT `FK_unknowns_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`),
  CONSTRAINT `FK_unknowns_username` FOREIGN KEY (`username`) REFERENCES `userlogin` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unknowns`
--

LOCK TABLES `unknowns` WRITE;
/*!40000 ALTER TABLE `unknowns` DISABLE KEYS */;
/*!40000 ALTER TABLE `unknowns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `uploaddetermtemp`
--

DROP TABLE IF EXISTS `uploaddetermtemp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uploaddetermtemp` (
  `occid` int(10) unsigned DEFAULT NULL,
  `collid` int(10) unsigned DEFAULT NULL,
  `dbpk` varchar(150) DEFAULT NULL,
  `identifiedBy` varchar(60) NOT NULL,
  `dateIdentified` varchar(45) NOT NULL,
  `dateIdentifiedInterpreted` date DEFAULT NULL,
  `sciname` varchar(100) NOT NULL,
  `scientificNameAuthorship` varchar(100) DEFAULT NULL,
  `identificationQualifier` varchar(45) DEFAULT NULL,
  `iscurrent` int(2) DEFAULT '0',
  `detType` varchar(45) DEFAULT NULL,
  `identificationReferences` varchar(255) DEFAULT NULL,
  `identificationRemarks` varchar(255) DEFAULT NULL,
  `sourceIdentifier` varchar(45) DEFAULT NULL,
  `sortsequence` int(10) unsigned DEFAULT '10',
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `Index_uploaddet_occid` (`occid`),
  KEY `Index_uploaddet_collid` (`collid`),
  KEY `Index_uploaddet_dbpk` (`dbpk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `uploaddetermtemp`
--

LOCK TABLES `uploaddetermtemp` WRITE;
/*!40000 ALTER TABLE `uploaddetermtemp` DISABLE KEYS */;
/*!40000 ALTER TABLE `uploaddetermtemp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `uploadglossary`
--

DROP TABLE IF EXISTS `uploadglossary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uploadglossary` (
  `term` varchar(150) DEFAULT NULL,
  `definition` varchar(1000) DEFAULT NULL,
  `language` varchar(45) DEFAULT NULL,
  `source` varchar(1000) DEFAULT NULL,
  `author` varchar(250) DEFAULT NULL,
  `translator` varchar(250) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `resourceurl` varchar(600) DEFAULT NULL,
  `tidStr` varchar(100) DEFAULT NULL,
  `synonym` tinyint(1) DEFAULT NULL,
  `newGroupId` int(10) DEFAULT NULL,
  `currentGroupId` int(10) DEFAULT NULL,
  `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `term_index` (`term`),
  KEY `relatedterm_index` (`newGroupId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `uploadglossary`
--

LOCK TABLES `uploadglossary` WRITE;
/*!40000 ALTER TABLE `uploadglossary` DISABLE KEYS */;
/*!40000 ALTER TABLE `uploadglossary` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `uploadimagetemp`
--

DROP TABLE IF EXISTS `uploadimagetemp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uploadimagetemp` (
  `tid` int(10) unsigned DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `thumbnailurl` varchar(255) DEFAULT NULL,
  `originalurl` varchar(255) DEFAULT NULL,
  `archiveurl` varchar(255) DEFAULT NULL,
  `photographer` varchar(100) DEFAULT NULL,
  `photographeruid` int(10) unsigned DEFAULT NULL,
  `imagetype` varchar(50) DEFAULT NULL,
  `format` varchar(45) DEFAULT NULL,
  `caption` varchar(100) DEFAULT NULL,
  `owner` varchar(100) DEFAULT NULL,
  `sourceUrl` varchar(255) DEFAULT NULL,
  `referenceurl` varchar(255) DEFAULT NULL,
  `copyright` varchar(255) DEFAULT NULL,
  `accessrights` varchar(255) DEFAULT NULL,
  `rights` varchar(255) DEFAULT NULL,
  `locality` varchar(250) DEFAULT NULL,
  `occid` int(10) unsigned DEFAULT NULL,
  `collid` int(10) unsigned DEFAULT NULL,
  `dbpk` varchar(150) DEFAULT NULL,
  `sourceIdentifier` varchar(150) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `username` varchar(45) DEFAULT NULL,
  `sortsequence` int(10) unsigned DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `Index_uploadimg_occid` (`occid`),
  KEY `Index_uploadimg_collid` (`collid`),
  KEY `Index_uploadimg_dbpk` (`dbpk`),
  KEY `Index_uploadimg_ts` (`initialtimestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `uploadimagetemp`
--

LOCK TABLES `uploadimagetemp` WRITE;
/*!40000 ALTER TABLE `uploadimagetemp` DISABLE KEYS */;
/*!40000 ALTER TABLE `uploadimagetemp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `uploadspecmap`
--

DROP TABLE IF EXISTS `uploadspecmap`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uploadspecmap` (
  `usmid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uspid` int(10) unsigned NOT NULL,
  `sourcefield` varchar(45) NOT NULL,
  `symbdatatype` varchar(45) NOT NULL DEFAULT 'string' COMMENT 'string, numeric, datetime',
  `symbspecfield` varchar(45) NOT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`usmid`),
  UNIQUE KEY `Index_unique` (`uspid`,`symbspecfield`,`sourcefield`),
  CONSTRAINT `FK_uploadspecmap_usp` FOREIGN KEY (`uspid`) REFERENCES `uploadspecparameters` (`uspid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `uploadspecmap`
--

LOCK TABLES `uploadspecmap` WRITE;
/*!40000 ALTER TABLE `uploadspecmap` DISABLE KEYS */;
/*!40000 ALTER TABLE `uploadspecmap` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `uploadspecparameters`
--

DROP TABLE IF EXISTS `uploadspecparameters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uploadspecparameters` (
  `uspid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `CollID` int(10) unsigned NOT NULL,
  `UploadType` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '1 = Direct; 3 = File',
  `title` varchar(45) NOT NULL,
  `Platform` varchar(45) DEFAULT '1' COMMENT '1 = MySQL; 2 = MSSQL; 3 = ORACLE; 11 = MS Access; 12 = FileMaker',
  `server` varchar(150) DEFAULT NULL,
  `port` int(10) unsigned DEFAULT NULL,
  `driver` varchar(45) DEFAULT NULL,
  `Code` varchar(45) DEFAULT NULL,
  `Path` varchar(500) DEFAULT NULL,
  `PkField` varchar(45) DEFAULT NULL,
  `Username` varchar(45) DEFAULT NULL,
  `Password` varchar(45) DEFAULT NULL,
  `SchemaName` varchar(150) DEFAULT NULL,
  `QueryStr` text,
  `cleanupsp` varchar(45) DEFAULT NULL,
  `endpointPublic` int(11) DEFAULT NULL,
  `dlmisvalid` int(10) unsigned DEFAULT '0',
  `createdUid` int(10) unsigned DEFAULT NULL,
  `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`uspid`),
  KEY `FK_uploadspecparameters_coll` (`CollID`),
  KEY `FK_uploadspecparameters_uid_idx` (`createdUid`),
  CONSTRAINT `FK_uploadspecparameters_coll` FOREIGN KEY (`CollID`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_uploadspecparameters_uid` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `uploadspecparameters`
--

LOCK TABLES `uploadspecparameters` WRITE;
/*!40000 ALTER TABLE `uploadspecparameters` DISABLE KEYS */;
/*!40000 ALTER TABLE `uploadspecparameters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `uploadspectemp`
--

DROP TABLE IF EXISTS `uploadspectemp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uploadspectemp` (
  `collid` int(10) unsigned NOT NULL,
  `dbpk` varchar(150) DEFAULT NULL,
  `occid` int(10) unsigned DEFAULT NULL,
  `basisOfRecord` varchar(32) DEFAULT NULL COMMENT 'PreservedSpecimen, LivingSpecimen, HumanObservation',
  `occurrenceID` varchar(255) DEFAULT NULL COMMENT 'UniqueGlobalIdentifier',
  `catalogNumber` varchar(32) DEFAULT NULL,
  `otherCatalogNumbers` varchar(255) DEFAULT NULL,
  `ownerInstitutionCode` varchar(32) DEFAULT NULL,
  `institutionID` varchar(255) DEFAULT NULL,
  `collectionID` varchar(255) DEFAULT NULL,
  `datasetID` varchar(255) DEFAULT NULL,
  `organismID` varchar(150) DEFAULT NULL,
  `materialSampleID` varchar(150) DEFAULT NULL,
  `institutionCode` varchar(64) DEFAULT NULL,
  `collectionCode` varchar(64) DEFAULT NULL,
  `family` varchar(255) DEFAULT NULL,
  `scientificName` varchar(255) DEFAULT NULL,
  `sciname` varchar(255) DEFAULT NULL,
  `tidinterpreted` int(10) unsigned DEFAULT NULL,
  `genus` varchar(255) DEFAULT NULL,
  `specificEpithet` varchar(255) DEFAULT NULL,
  `taxonRank` varchar(32) DEFAULT NULL,
  `infraspecificEpithet` varchar(255) DEFAULT NULL,
  `scientificNameAuthorship` varchar(255) DEFAULT NULL,
  `taxonRemarks` text,
  `identifiedBy` varchar(255) DEFAULT NULL,
  `dateIdentified` varchar(45) DEFAULT NULL,
  `identificationReferences` text,
  `identificationRemarks` text,
  `identificationQualifier` varchar(255) DEFAULT NULL COMMENT 'cf, aff, etc',
  `typeStatus` varchar(255) DEFAULT NULL,
  `recordedBy` varchar(255) DEFAULT NULL COMMENT 'Collector(s)',
  `recordNumberPrefix` varchar(45) DEFAULT NULL,
  `recordNumberSuffix` varchar(45) DEFAULT NULL,
  `recordNumber` varchar(32) DEFAULT NULL COMMENT 'Collector Number',
  `CollectorFamilyName` varchar(255) DEFAULT NULL COMMENT 'not DwC',
  `CollectorInitials` varchar(255) DEFAULT NULL COMMENT 'not DwC',
  `associatedCollectors` varchar(255) DEFAULT NULL COMMENT 'not DwC',
  `eventDate` date DEFAULT NULL,
  `year` int(10) DEFAULT NULL,
  `month` int(10) DEFAULT NULL,
  `day` int(10) DEFAULT NULL,
  `startDayOfYear` int(10) DEFAULT NULL,
  `endDayOfYear` int(10) DEFAULT NULL,
  `latestDateCollected` date DEFAULT NULL,
  `verbatimEventDate` varchar(255) DEFAULT NULL,
  `habitat` text COMMENT 'Habitat, substrait, etc',
  `substrate` varchar(500) DEFAULT NULL,
  `host` varchar(250) DEFAULT NULL,
  `fieldNotes` text,
  `fieldnumber` varchar(45) DEFAULT NULL,
  `occurrenceRemarks` text COMMENT 'General Notes',
  `informationWithheld` varchar(250) DEFAULT NULL,
  `dataGeneralizations` varchar(250) DEFAULT NULL,
  `associatedOccurrences` text,
  `associatedMedia` text,
  `associatedReferences` text,
  `associatedSequences` text,
  `associatedTaxa` text COMMENT 'Associated Species',
  `dynamicProperties` text COMMENT 'Plant Description?',
  `verbatimAttributes` text,
  `behavior` varchar(500) DEFAULT NULL,
  `reproductiveCondition` varchar(255) DEFAULT NULL COMMENT 'Phenology: flowers, fruit, sterile',
  `cultivationStatus` int(10) DEFAULT NULL COMMENT '0 = wild, 1 = cultivated',
  `establishmentMeans` varchar(32) DEFAULT NULL COMMENT 'cultivated, invasive, escaped from captivity, wild, native',
  `lifeStage` varchar(45) DEFAULT NULL,
  `sex` varchar(45) DEFAULT NULL,
  `individualCount` varchar(45) DEFAULT NULL,
  `samplingProtocol` varchar(100) DEFAULT NULL,
  `samplingEffort` varchar(200) DEFAULT NULL,
  `preparations` varchar(100) DEFAULT NULL,
  `locationID` varchar(150) DEFAULT NULL,
  `parentLocationID` varchar(150) DEFAULT NULL,
  `continent` varchar(45) DEFAULT NULL,
  `waterBody` varchar(150) DEFAULT NULL,
  `islandGroup` varchar(75) DEFAULT NULL,
  `island` varchar(75) DEFAULT NULL,
  `countryCode` varchar(5) DEFAULT NULL,
  `country` varchar(64) DEFAULT NULL,
  `stateProvince` varchar(255) DEFAULT NULL,
  `county` varchar(255) DEFAULT NULL,
  `municipality` varchar(255) DEFAULT NULL,
  `locality` text,
  `localitySecurity` int(10) DEFAULT '0' COMMENT '0 = display locality, 1 = hide locality',
  `localitySecurityReason` varchar(100) DEFAULT NULL,
  `decimalLatitude` double DEFAULT NULL,
  `decimalLongitude` double DEFAULT NULL,
  `geodeticDatum` varchar(255) DEFAULT NULL,
  `coordinateUncertaintyInMeters` int(10) unsigned DEFAULT NULL,
  `footprintWKT` text,
  `coordinatePrecision` decimal(9,7) DEFAULT NULL,
  `locationRemarks` text,
  `verbatimCoordinates` varchar(255) DEFAULT NULL,
  `verbatimCoordinateSystem` varchar(255) DEFAULT NULL,
  `latDeg` int(11) DEFAULT NULL,
  `latMin` double DEFAULT NULL,
  `latSec` double DEFAULT NULL,
  `latNS` varchar(3) DEFAULT NULL,
  `lngDeg` int(11) DEFAULT NULL,
  `lngMin` double DEFAULT NULL,
  `lngSec` double DEFAULT NULL,
  `lngEW` varchar(3) DEFAULT NULL,
  `verbatimLatitude` varchar(45) DEFAULT NULL,
  `verbatimLongitude` varchar(45) DEFAULT NULL,
  `UtmNorthing` varchar(45) DEFAULT NULL,
  `UtmEasting` varchar(45) DEFAULT NULL,
  `UtmZoning` varchar(45) DEFAULT NULL,
  `trsTownship` varchar(45) DEFAULT NULL,
  `trsRange` varchar(45) DEFAULT NULL,
  `trsSection` varchar(45) DEFAULT NULL,
  `trsSectionDetails` varchar(45) DEFAULT NULL,
  `georeferencedBy` varchar(255) DEFAULT NULL,
  `georeferencedDate` datetime DEFAULT NULL,
  `georeferenceProtocol` varchar(255) DEFAULT NULL,
  `georeferenceSources` varchar(255) DEFAULT NULL,
  `georeferenceVerificationStatus` varchar(32) DEFAULT NULL,
  `georeferenceRemarks` varchar(255) DEFAULT NULL,
  `minimumElevationInMeters` int(6) DEFAULT NULL,
  `maximumElevationInMeters` int(6) DEFAULT NULL,
  `elevationNumber` varchar(45) DEFAULT NULL,
  `elevationUnits` varchar(45) DEFAULT NULL,
  `verbatimElevation` varchar(255) DEFAULT NULL,
  `minimumDepthInMeters` int(11) DEFAULT NULL,
  `maximumDepthInMeters` int(11) DEFAULT NULL,
  `verbatimDepth` varchar(50) DEFAULT NULL,
  `previousIdentifications` text,
  `disposition` varchar(32) DEFAULT NULL COMMENT 'Dups to',
  `storageLocation` varchar(100) DEFAULT NULL,
  `genericcolumn1` varchar(100) DEFAULT NULL,
  `genericcolumn2` varchar(100) DEFAULT NULL,
  `exsiccatiIdentifier` int(11) DEFAULT NULL,
  `exsiccatiNumber` varchar(45) DEFAULT NULL,
  `exsiccatiNotes` varchar(250) DEFAULT NULL,
  `paleoJSON` text,
  `modified` datetime DEFAULT NULL COMMENT 'DateLastModified',
  `language` varchar(20) DEFAULT NULL,
  `recordEnteredBy` varchar(250) DEFAULT NULL,
  `duplicateQuantity` int(10) unsigned DEFAULT NULL,
  `labelProject` varchar(45) DEFAULT NULL,
  `processingStatus` varchar(45) DEFAULT NULL,
  `tempfield01` text,
  `tempfield02` text,
  `tempfield03` text,
  `tempfield04` text,
  `tempfield05` text,
  `tempfield06` text,
  `tempfield07` text,
  `tempfield08` text,
  `tempfield09` text,
  `tempfield10` text,
  `tempfield11` text,
  `tempfield12` text,
  `tempfield13` text,
  `tempfield14` text,
  `tempfield15` text,
  `initialTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `FK_uploadspectemp_coll` (`collid`),
  KEY `Index_uploadspectemp_occid` (`occid`),
  KEY `Index_uploadspectemp_dbpk` (`dbpk`),
  KEY `Index_uploadspec_sciname` (`sciname`),
  KEY `Index_uploadspec_catalognumber` (`catalogNumber`),
  KEY `Index_uploadspec_othercatalognumbers` (`otherCatalogNumbers`),
  CONSTRAINT `FK_uploadspectemp_coll` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `uploadspectemp`
--

LOCK TABLES `uploadspectemp` WRITE;
/*!40000 ALTER TABLE `uploadspectemp` DISABLE KEYS */;
/*!40000 ALTER TABLE `uploadspectemp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `uploadtaxa`
--

DROP TABLE IF EXISTS `uploadtaxa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uploadtaxa` (
  `TID` int(10) unsigned DEFAULT NULL,
  `SourceId` int(10) unsigned DEFAULT NULL,
  `Family` varchar(50) DEFAULT NULL,
  `RankId` smallint(5) DEFAULT NULL,
  `RankName` varchar(45) DEFAULT NULL,
  `scinameinput` varchar(250) NOT NULL,
  `SciName` varchar(250) DEFAULT NULL,
  `UnitInd1` varchar(1) DEFAULT NULL,
  `UnitName1` varchar(50) DEFAULT NULL,
  `UnitInd2` varchar(1) DEFAULT NULL,
  `UnitName2` varchar(50) DEFAULT NULL,
  `UnitInd3` varchar(45) DEFAULT NULL,
  `UnitName3` varchar(35) DEFAULT NULL,
  `Author` varchar(100) DEFAULT NULL,
  `InfraAuthor` varchar(100) DEFAULT NULL,
  `taxonomicStatus` varchar(45) DEFAULT NULL,
  `Acceptance` int(10) unsigned DEFAULT '1' COMMENT '0 = not accepted; 1 = accepted',
  `TidAccepted` int(10) unsigned DEFAULT NULL,
  `AcceptedStr` varchar(250) DEFAULT NULL,
  `SourceAcceptedId` int(10) unsigned DEFAULT NULL,
  `UnacceptabilityReason` varchar(24) DEFAULT NULL,
  `ParentTid` int(10) DEFAULT NULL,
  `ParentStr` varchar(250) DEFAULT NULL,
  `SourceParentId` int(10) unsigned DEFAULT NULL,
  `SecurityStatus` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '0 = no security; 1 = hidden locality',
  `Source` varchar(250) DEFAULT NULL,
  `Notes` varchar(250) DEFAULT NULL,
  `vernacular` varchar(250) DEFAULT NULL,
  `vernlang` varchar(15) DEFAULT NULL,
  `Hybrid` varchar(50) DEFAULT NULL,
  `ErrorStatus` varchar(150) DEFAULT NULL,
  `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `UNIQUE_sciname` (`SciName`,`RankId`,`Author`,`AcceptedStr`),
  KEY `sourceID_index` (`SourceId`),
  KEY `sourceAcceptedId_index` (`SourceAcceptedId`),
  KEY `sciname_index` (`SciName`),
  KEY `scinameinput_index` (`scinameinput`),
  KEY `parentStr_index` (`ParentStr`),
  KEY `acceptedStr_index` (`AcceptedStr`),
  KEY `unitname1_index` (`UnitName1`),
  KEY `sourceParentId_index` (`SourceParentId`),
  KEY `acceptance_index` (`Acceptance`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `uploadtaxa`
--

LOCK TABLES `uploadtaxa` WRITE;
/*!40000 ALTER TABLE `uploadtaxa` DISABLE KEYS */;
/*!40000 ALTER TABLE `uploadtaxa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `useraccesstokens`
--

DROP TABLE IF EXISTS `useraccesstokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `useraccesstokens` (
  `tokid` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `token` varchar(50) NOT NULL,
  `device` varchar(50) DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tokid`),
  KEY `FK_useraccesstokens_uid_idx` (`uid`),
  CONSTRAINT `FK_useraccess_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `useraccesstokens`
--

LOCK TABLES `useraccesstokens` WRITE;
/*!40000 ALTER TABLE `useraccesstokens` DISABLE KEYS */;
INSERT INTO `useraccesstokens` VALUES (1,1,'689230bf-c4bd-41cc-b01f-a23ae105ca7e',NULL,'2022-11-11 23:50:12'),(2,1,'d3dbd02e-75c0-4fb0-8c1a-75e19efbeba3',NULL,'2022-11-12 03:03:38'),(3,1,'a1a76940-669f-4101-b602-4a73bc9936db',NULL,'2023-01-10 20:07:10'),(4,1,'1a0f8f1c-8f25-444e-9495-b54fc1bd09d9',NULL,'2023-03-15 22:11:51'),(5,1,'eb04fd72-254e-4e09-951c-33b614531618',NULL,'2023-03-16 14:06:21'),(6,1,'6acadff3-f856-4641-bb32-5a55beeae145',NULL,'2023-03-16 14:59:15'),(7,1,'e159b538-35f8-4933-80e6-305a3c3c42b5',NULL,'2023-03-23 18:15:47'),(8,1,'7f152226-d8f5-42a2-90b3-82323a76c5ed',NULL,'2023-03-28 18:51:46'),(9,1,'531e7c43-e9ef-4436-9786-152964fd36e0',NULL,'2023-03-28 18:52:03'),(10,1,'ee3dc650-64bb-4234-905a-2a10ad877133',NULL,'2023-04-14 19:32:41'),(11,1,'70ed4d04-3fbf-4bf7-b325-e539de709e31',NULL,'2023-04-15 19:46:11');
/*!40000 ALTER TABLE `useraccesstokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `userlogin`
--

DROP TABLE IF EXISTS `userlogin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userlogin` (
  `uid` int(10) unsigned NOT NULL,
  `username` varchar(45) NOT NULL,
  `password` varchar(45) NOT NULL,
  `alias` varchar(45) DEFAULT NULL,
  `lastlogindate` datetime DEFAULT NULL,
  `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`username`) USING BTREE,
  UNIQUE KEY `Index_userlogin_unique` (`alias`),
  KEY `FK_login_user` (`uid`),
  CONSTRAINT `FK_login_user` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `userlogin`
--

LOCK TABLES `userlogin` WRITE;
/*!40000 ALTER TABLE `userlogin` DISABLE KEYS */;
INSERT INTO `userlogin` VALUES (1,'admin','*4ACFE3202A5FF5CF467898FC58AAB1D615029441',NULL,'2023-04-28 17:39:22','2022-10-18 23:21:41'),(2,'debra4117','*6A7A490FB9DC8C33C2B025A91737077A7E9CC5E5',NULL,'2022-11-13 20:12:55','2022-11-13 20:12:55');
/*!40000 ALTER TABLE `userlogin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `userroles`
--

DROP TABLE IF EXISTS `userroles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userroles` (
  `userroleid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `role` varchar(45) NOT NULL,
  `tablename` varchar(45) DEFAULT NULL,
  `tablepk` int(11) DEFAULT NULL,
  `secondaryVariable` varchar(45) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `uidassignedby` int(10) unsigned DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`userroleid`),
  UNIQUE KEY `Unique_userroles` (`uid`,`role`,`tablename`,`tablepk`),
  KEY `FK_userroles_uid_idx` (`uid`),
  KEY `FK_usrroles_uid2_idx` (`uidassignedby`),
  KEY `Index_userroles_table` (`tablename`,`tablepk`),
  CONSTRAINT `FK_userrole_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_userrole_uid_assigned` FOREIGN KEY (`uidassignedby`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `userroles`
--

LOCK TABLES `userroles` WRITE;
/*!40000 ALTER TABLE `userroles` DISABLE KEYS */;
INSERT INTO `userroles` VALUES (2,1,'SuperAdmin',NULL,NULL,NULL,NULL,NULL,'2022-10-18 23:21:41');
/*!40000 ALTER TABLE `userroles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(45) DEFAULT NULL,
  `lastname` varchar(45) NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `institution` varchar(200) DEFAULT NULL,
  `department` varchar(200) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `zip` varchar(15) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `RegionOfInterest` varchar(45) DEFAULT NULL,
  `url` varchar(400) DEFAULT NULL,
  `Biography` varchar(1500) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `ispublic` int(10) unsigned NOT NULL DEFAULT '0',
  `defaultrights` varchar(250) DEFAULT NULL,
  `rightsholder` varchar(250) DEFAULT NULL,
  `rights` varchar(250) DEFAULT NULL,
  `accessrights` varchar(250) DEFAULT NULL,
  `guid` varchar(45) DEFAULT NULL,
  `validated` varchar(45) NOT NULL DEFAULT '0',
  `usergroups` varchar(100) DEFAULT NULL,
  `dynamicProperties` text,
  `InitialTimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `Index_email` (`email`,`lastname`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'General','Administrator',NULL,NULL,NULL,NULL,NULL,'NA',NULL,'NA',NULL,'NA',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,'0',NULL,NULL,'2022-10-18 23:21:41'),(2,'Shou-Tzu','Han',NULL,NULL,NULL,NULL,'Allston','Massachusetts','02134','&#32654;&#22283;',NULL,'debrah@bu.edu',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,'123','0',NULL,NULL,'2022-11-13 20:12:55');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usertaxonomy`
--

DROP TABLE IF EXISTS `usertaxonomy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usertaxonomy` (
  `idusertaxonomy` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `taxauthid` int(10) unsigned NOT NULL DEFAULT '1',
  `editorstatus` varchar(45) DEFAULT NULL,
  `geographicScope` varchar(250) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `modifiedUid` int(10) unsigned NOT NULL,
  `modifiedtimestamp` datetime DEFAULT NULL,
  `initialtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idusertaxonomy`),
  UNIQUE KEY `usertaxonomy_UNIQUE` (`uid`,`tid`,`taxauthid`,`editorstatus`),
  KEY `FK_usertaxonomy_uid_idx` (`uid`),
  KEY `FK_usertaxonomy_tid_idx` (`tid`),
  KEY `FK_usertaxonomy_taxauthid_idx` (`taxauthid`),
  CONSTRAINT `FK_usertaxonomy_taxauthid` FOREIGN KEY (`taxauthid`) REFERENCES `taxauthority` (`taxauthid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_usertaxonomy_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`TID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_usertaxonomy_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usertaxonomy`
--

LOCK TABLES `usertaxonomy` WRITE;
/*!40000 ALTER TABLE `usertaxonomy` DISABLE KEYS */;
/*!40000 ALTER TABLE `usertaxonomy` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-04-28 19:08:28
