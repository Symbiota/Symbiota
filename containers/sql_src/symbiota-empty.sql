-- MySQL dump 10.13  Distrib 5.7.44, for Linux (x86_64)
--
-- Host: localhost    Database: symbiota
-- ------------------------------------------------------
-- Server version	5.7.44

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
-- Table structure for table `adminconfig`
--

DROP TABLE IF EXISTS `adminconfig`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adminconfig` (
  `configID` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(45) DEFAULT NULL,
  `attributeName` varchar(45) NOT NULL,
  `attributeValue` varchar(1000) NOT NULL,
  `dynamicProperties` text,
  `notes` varchar(45) DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `modifiedTimestamp` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`configID`),
  UNIQUE KEY `UQ_adminconfig_name` (`attributeName`),
  KEY `FK_adminConfig_uid_idx` (`modifiedUid`),
  CONSTRAINT `FK_adminConfig_uid` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `adminconfig`
--

LOCK TABLES `adminconfig` WRITE;
/*!40000 ALTER TABLE `adminconfig` DISABLE KEYS */;
/*!40000 ALTER TABLE `adminconfig` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `adminlanguages`
--

DROP TABLE IF EXISTS `adminlanguages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adminlanguages` (
  `langid` int(11) NOT NULL AUTO_INCREMENT,
  `langName` varchar(45) NOT NULL,
  `iso639_1` varchar(10) DEFAULT NULL,
  `iso639_2` varchar(10) DEFAULT NULL,
  `ISO 639-3` varchar(3) DEFAULT NULL,
  `notes` varchar(45) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`langid`),
  UNIQUE KEY `index_langname_unique` (`langName`)
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `adminlanguages`
--

LOCK TABLES `adminlanguages` WRITE;
/*!40000 ALTER TABLE `adminlanguages` DISABLE KEYS */;
INSERT INTO `adminlanguages` VALUES (1,'English','en',NULL,NULL,NULL,'2023-03-26 18:15:40'),(2,'German','de',NULL,NULL,NULL,'2023-03-26 18:15:40'),(3,'French','fr',NULL,NULL,NULL,'2023-03-26 18:15:40'),(4,'Dutch','nl',NULL,NULL,NULL,'2023-03-26 18:15:40'),(5,'Italian','it',NULL,NULL,NULL,'2023-03-26 18:15:40'),(6,'Spanish','es',NULL,NULL,NULL,'2023-03-26 18:15:40'),(7,'Polish','pl',NULL,NULL,NULL,'2023-03-26 18:15:40'),(8,'Russian','ru',NULL,NULL,NULL,'2023-03-26 18:15:40'),(9,'Japanese','ja',NULL,NULL,NULL,'2023-03-26 18:15:40'),(10,'Portuguese','pt',NULL,NULL,NULL,'2023-03-26 18:15:40'),(11,'Swedish','sv',NULL,NULL,NULL,'2023-03-26 18:15:40'),(12,'Chinese','zh',NULL,NULL,NULL,'2023-03-26 18:15:40'),(13,'Catalan','ca',NULL,NULL,NULL,'2023-03-26 18:15:40'),(14,'Ukrainian','uk',NULL,NULL,NULL,'2023-03-26 18:15:40'),(15,'Norwegian (Bokm´┐¢l)','no',NULL,NULL,NULL,'2023-03-26 18:15:40'),(16,'Finnish','fi',NULL,NULL,NULL,'2023-03-26 18:15:40'),(17,'Vietnamese','vi',NULL,NULL,NULL,'2023-03-26 18:15:40'),(18,'Czech','cs',NULL,NULL,NULL,'2023-03-26 18:15:40'),(19,'Hungarian','hu',NULL,NULL,NULL,'2023-03-26 18:15:40'),(20,'Korean','ko',NULL,NULL,NULL,'2023-03-26 18:15:40'),(21,'Indonesian','id',NULL,NULL,NULL,'2023-03-26 18:15:40'),(22,'Turkish','tr',NULL,NULL,NULL,'2023-03-26 18:15:40'),(23,'Romanian','ro',NULL,NULL,NULL,'2023-03-26 18:15:40'),(24,'Persian','fa',NULL,NULL,NULL,'2023-03-26 18:15:40'),(25,'Arabic','ar',NULL,NULL,NULL,'2023-03-26 18:15:40'),(26,'Danish','da',NULL,NULL,NULL,'2023-03-26 18:15:40'),(27,'Esperanto','eo',NULL,NULL,NULL,'2023-03-26 18:15:40'),(28,'Serbian','sr',NULL,NULL,NULL,'2023-03-26 18:15:40'),(29,'Lithuanian','lt',NULL,NULL,NULL,'2023-03-26 18:15:40'),(30,'Slovak','sk',NULL,NULL,NULL,'2023-03-26 18:15:40'),(31,'Malay','ms',NULL,NULL,NULL,'2023-03-26 18:15:40'),(32,'Hebrew','he',NULL,NULL,NULL,'2023-03-26 18:15:40'),(33,'Bulgarian','bg',NULL,NULL,NULL,'2023-03-26 18:15:40'),(34,'Slovenian','sl',NULL,NULL,NULL,'2023-03-26 18:15:40'),(35,'Volap´┐¢k','vo',NULL,NULL,NULL,'2023-03-26 18:15:40'),(36,'Kazakh','kk',NULL,NULL,NULL,'2023-03-26 18:15:40'),(37,'Waray-Waray','war',NULL,NULL,NULL,'2023-03-26 18:15:40'),(38,'Basque','eu',NULL,NULL,NULL,'2023-03-26 18:15:40'),(39,'Croatian','hr',NULL,NULL,NULL,'2023-03-26 18:15:40'),(40,'Hindi','hi',NULL,NULL,NULL,'2023-03-26 18:15:40'),(41,'Estonian','et',NULL,NULL,NULL,'2023-03-26 18:15:40'),(42,'Azerbaijani','az',NULL,NULL,NULL,'2023-03-26 18:15:40'),(43,'Galician','gl',NULL,NULL,NULL,'2023-03-26 18:15:40'),(44,'Simple English','simple',NULL,NULL,NULL,'2023-03-26 18:15:40'),(45,'Norwegian (Nynorsk)','nn',NULL,NULL,NULL,'2023-03-26 18:15:40'),(46,'Thai','th',NULL,NULL,NULL,'2023-03-26 18:15:40'),(47,'Newar / Nepal Bhasa','new',NULL,NULL,NULL,'2023-03-26 18:15:40'),(48,'Greek','el',NULL,NULL,NULL,'2023-03-26 18:15:40'),(49,'Aromanian','roa-rup',NULL,NULL,NULL,'2023-03-26 18:15:40'),(50,'Latin','la',NULL,NULL,NULL,'2023-03-26 18:15:40'),(51,'Occitan','oc',NULL,NULL,NULL,'2023-03-26 18:15:40'),(52,'Tagalog','tl',NULL,NULL,NULL,'2023-03-26 18:15:40'),(53,'Haitian','ht',NULL,NULL,NULL,'2023-03-26 18:15:40'),(54,'Macedonian','mk',NULL,NULL,NULL,'2023-03-26 18:15:40'),(55,'Georgian','ka',NULL,NULL,NULL,'2023-03-26 18:15:40'),(56,'Serbo-Croatian','sh',NULL,NULL,NULL,'2023-03-26 18:15:40'),(57,'Telugu','te',NULL,NULL,NULL,'2023-03-26 18:15:40'),(58,'Piedmontese','pms',NULL,NULL,NULL,'2023-03-26 18:15:40'),(59,'Cebuano','ceb',NULL,NULL,NULL,'2023-03-26 18:15:40'),(60,'Tamil','ta',NULL,NULL,NULL,'2023-03-26 18:15:40'),(61,'Belarusian (Tara´┐¢kievica)','be-x-old',NULL,NULL,NULL,'2023-03-26 18:15:40'),(62,'Breton','br',NULL,NULL,NULL,'2023-03-26 18:15:40'),(63,'Latvian','lv',NULL,NULL,NULL,'2023-03-26 18:15:40'),(64,'Javanese','jv',NULL,NULL,NULL,'2023-03-26 18:15:40'),(65,'Albanian','sq',NULL,NULL,NULL,'2023-03-26 18:15:40'),(66,'Belarusian','be',NULL,NULL,NULL,'2023-03-26 18:15:40'),(67,'Marathi','mr',NULL,NULL,NULL,'2023-03-26 18:15:40'),(68,'Welsh','cy',NULL,NULL,NULL,'2023-03-26 18:15:40'),(69,'Luxembourgish','lb',NULL,NULL,NULL,'2023-03-26 18:15:40'),(70,'Icelandic','is',NULL,NULL,NULL,'2023-03-26 18:15:40'),(71,'Bosnian','bs',NULL,NULL,NULL,'2023-03-26 18:15:40'),(72,'Yoruba','yo',NULL,NULL,NULL,'2023-03-26 18:15:40'),(73,'Malagasy','mg',NULL,NULL,NULL,'2023-03-26 18:15:40'),(74,'Aragonese','an',NULL,NULL,NULL,'2023-03-26 18:15:40'),(75,'Bishnupriya Manipuri','bpy',NULL,NULL,NULL,'2023-03-26 18:15:40'),(76,'Lombard','lmo',NULL,NULL,NULL,'2023-03-26 18:15:40'),(77,'West Frisian','fy',NULL,NULL,NULL,'2023-03-26 18:15:40'),(78,'Bengali','bn',NULL,NULL,NULL,'2023-03-26 18:15:40'),(79,'Ido','io',NULL,NULL,NULL,'2023-03-26 18:15:40'),(80,'Swahili','sw',NULL,NULL,NULL,'2023-03-26 18:15:40'),(81,'Gujarati','gu',NULL,NULL,NULL,'2023-03-26 18:15:40'),(82,'Malayalam','ml',NULL,NULL,NULL,'2023-03-26 18:15:40'),(83,'Western Panjabi','pnb',NULL,NULL,NULL,'2023-03-26 18:15:40'),(84,'Afrikaans','af',NULL,NULL,NULL,'2023-03-26 18:15:40'),(85,'Low Saxon','nds',NULL,NULL,NULL,'2023-03-26 18:15:40'),(86,'Sicilian','scn',NULL,NULL,NULL,'2023-03-26 18:15:40'),(87,'Urdu','ur',NULL,NULL,NULL,'2023-03-26 18:15:40'),(88,'Kurdish','ku',NULL,NULL,NULL,'2023-03-26 18:15:40'),(89,'Cantonese','zh-yue',NULL,NULL,NULL,'2023-03-26 18:15:40'),(90,'Armenian','hy',NULL,NULL,NULL,'2023-03-26 18:15:40'),(91,'Quechua','qu',NULL,NULL,NULL,'2023-03-26 18:15:40'),(92,'Sundanese','su',NULL,NULL,NULL,'2023-03-26 18:15:40'),(93,'Nepali','ne',NULL,NULL,NULL,'2023-03-26 18:15:40'),(94,'Zazaki','diq',NULL,NULL,NULL,'2023-03-26 18:15:40'),(95,'Asturian','ast',NULL,NULL,NULL,'2023-03-26 18:15:40'),(96,'Tatar','tt',NULL,NULL,NULL,'2023-03-26 18:15:40'),(97,'Neapolitan','nap',NULL,NULL,NULL,'2023-03-26 18:15:40'),(98,'Irish','ga',NULL,NULL,NULL,'2023-03-26 18:15:40'),(99,'Chuvash','cv',NULL,NULL,NULL,'2023-03-26 18:15:40'),(100,'Samogitian','bat-smg',NULL,NULL,NULL,'2023-03-26 18:15:40'),(101,'Walloon','wa',NULL,NULL,NULL,'2023-03-26 18:15:40'),(102,'Amharic','am',NULL,NULL,NULL,'2023-03-26 18:15:40'),(103,'Kannada','kn',NULL,NULL,NULL,'2023-03-26 18:15:40'),(104,'Alemannic','als',NULL,NULL,NULL,'2023-03-26 18:15:40'),(105,'Buginese','bug',NULL,NULL,NULL,'2023-03-26 18:15:40'),(106,'Burmese','my',NULL,NULL,NULL,'2023-03-26 18:15:40'),(107,'Interlingua','ia',NULL,NULL,NULL,'2023-03-26 18:15:40');
/*!40000 ALTER TABLE `adminlanguages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `agentdeterminationlink`
--

DROP TABLE IF EXISTS `agentdeterminationlink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agentdeterminationlink` (
  `agentID` bigint(20) NOT NULL,
  `detID` int(10) unsigned NOT NULL,
  `role` varchar(45) NOT NULL DEFAULT '',
  `createdUid` int(10) unsigned DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `dateLastModified` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`agentID`,`detID`,`role`),
  KEY `FK_agentdetlink_detid_idx` (`detID`),
  KEY `FK_agentdetlink_modified_idx` (`modifiedUid`),
  KEY `FK_agentdetlink_created_idx` (`createdUid`),
  KEY `IX_agentdetlink_role` (`role`),
  CONSTRAINT `FK_agentdetlink_agentID` FOREIGN KEY (`agentID`) REFERENCES `agents` (`agentID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_agentdetlink_created` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON UPDATE CASCADE,
  CONSTRAINT `FK_agentdetlink_detid` FOREIGN KEY (`detID`) REFERENCES `omoccurdeterminations` (`detid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_agentdetlink_modified` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agentdeterminationlink`
--

LOCK TABLES `agentdeterminationlink` WRITE;
/*!40000 ALTER TABLE `agentdeterminationlink` DISABLE KEYS */;
/*!40000 ALTER TABLE `agentdeterminationlink` ENABLE KEYS */;
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
  `isPrimaryTopicOf` tinyint(1) NOT NULL DEFAULT '1',
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
  `agentNamesID` bigint(20) NOT NULL,
  `agentID` bigint(20) NOT NULL,
  `nameType` varchar(32) NOT NULL DEFAULT 'Full Name',
  `agentName` varchar(255) NOT NULL,
  `language` varchar(6) DEFAULT 'en_us',
  `createdUid` int(11) DEFAULT NULL,
  `modifiedUid` int(11) DEFAULT NULL,
  `dateLastModified` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`agentNamesID`),
  UNIQUE KEY `UQ_agentnames_unique` (`agentID`,`nameType`,`agentName`),
  KEY `IX_agentnames_name` (`agentName`),
  CONSTRAINT `FK_agentnames_agentID` FOREIGN KEY (`agentID`) REFERENCES `agents` (`agentID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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
  KEY `IX_agentnumberpattern_agentid` (`agentID`),
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
-- Table structure for table `agentoccurrencelink`
--

DROP TABLE IF EXISTS `agentoccurrencelink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agentoccurrencelink` (
  `agentID` bigint(20) NOT NULL,
  `occid` int(10) unsigned NOT NULL,
  `role` varchar(45) NOT NULL DEFAULT '',
  `createdUid` int(10) unsigned DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `dateLastModified` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`agentID`,`occid`,`role`),
  KEY `FK_agentoccurlink_occid_idx` (`occid`),
  KEY `FK_agentoccurlink_created_idx` (`createdUid`),
  KEY `FK_agentoccurlink_modified_idx` (`modifiedUid`),
  KEY `FK_agentoccurlink_role` (`role`),
  CONSTRAINT `FK_agentoccurlink_agentID` FOREIGN KEY (`agentID`) REFERENCES `agents` (`agentID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_agentoccurlink_created` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON UPDATE CASCADE,
  CONSTRAINT `FK_agentoccurlink_modified` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON UPDATE CASCADE,
  CONSTRAINT `FK_agentoccurlink_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agentoccurrencelink`
--

LOCK TABLES `agentoccurrencelink` WRITE;
/*!40000 ALTER TABLE `agentoccurrencelink` DISABLE KEYS */;
/*!40000 ALTER TABLE `agentoccurrencelink` ENABLE KEYS */;
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
  `taxonomicGroups` varchar(900) DEFAULT NULL,
  `collectionsAt` varchar(900) DEFAULT NULL,
  `curated` tinyint(1) DEFAULT '0',
  `notOtherwiseSpecified` tinyint(1) DEFAULT '0',
  `type` enum('Individual','Team','Organization') DEFAULT NULL,
  `prefix` varchar(32) DEFAULT NULL,
  `suffix` varchar(32) DEFAULT NULL,
  `nameString` text,
  `mboxSha1Sum` char(40) DEFAULT NULL,
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
  UNIQUE KEY `UQ_agents_guid` (`guid`),
  KEY `FK_agents_modUid_idx` (`modifiedUid`),
  KEY `FK_agents_createdUid_idx` (`createdUid`),
  KEY `IX_agents_familyname` (`familyName`),
  KEY `IX_agents_firstname` (`firstName`),
  KEY `FK_agents_preferred_recby_idx` (`preferredRecByID`),
  CONSTRAINT `FK_agents_createdUid` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_agents_modUid` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_agents_preferred_recby` FOREIGN KEY (`preferredRecByID`) REFERENCES `agents` (`agentID`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
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
  KEY `FK_agentteams_teamagentid_idx` (`teamAgentID`),
  KEY `FK_agentteams_memberagentid_idx` (`memberAgentID`),
  CONSTRAINT `FK_agentteams_memberAgentID` FOREIGN KEY (`memberAgentID`) REFERENCES `agents` (`agentID`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `FK_agentteams_teamAgentID` FOREIGN KEY (`teamAgentID`) REFERENCES `agents` (`agentID`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agentteams`
--

LOCK TABLES `agentteams` WRITE;
/*!40000 ALTER TABLE `agentteams` DISABLE KEYS */;
/*!40000 ALTER TABLE `agentteams` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configpage`
--

DROP TABLE IF EXISTS `configpage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configpage` (
  `configPageID` int(11) NOT NULL AUTO_INCREMENT,
  `pageName` varchar(45) NOT NULL,
  `title` varchar(150) NOT NULL,
  `cssName` varchar(45) DEFAULT NULL,
  `language` varchar(45) NOT NULL DEFAULT 'english',
  `displayMode` int(11) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `modifiedUid` int(10) unsigned NOT NULL,
  `modifiedTimestamp` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`configPageID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `attributeID` int(11) NOT NULL AUTO_INCREMENT,
  `configPageID` int(11) NOT NULL,
  `objID` varchar(45) DEFAULT NULL,
  `objName` varchar(45) NOT NULL,
  `value` varchar(45) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL COMMENT 'text, submit, div',
  `width` int(11) DEFAULT NULL,
  `top` int(11) DEFAULT NULL,
  `left` int(11) DEFAULT NULL,
  `styleStr` varchar(45) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `modifiedUid` int(10) unsigned NOT NULL,
  `modifiedTimestamp` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`attributeID`),
  KEY `FK_configpageattributes_id_idx` (`configPageID`),
  CONSTRAINT `FK_configpageattributes_id` FOREIGN KEY (`configPageID`) REFERENCES `configpage` (`configPageID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `collid` int(10) unsigned DEFAULT NULL,
  `title` varchar(45) NOT NULL,
  `definition` varchar(250) DEFAULT NULL,
  `authors` varchar(150) DEFAULT NULL,
  `tableName` varchar(45) NOT NULL,
  `fieldName` varchar(45) NOT NULL,
  `resourceUrl` varchar(150) DEFAULT NULL,
  `ontologyClass` varchar(150) DEFAULT NULL,
  `ontologyUrl` varchar(150) DEFAULT NULL,
  `limitToList` int(2) DEFAULT '0',
  `dynamicProperties` text,
  `notes` varchar(45) DEFAULT NULL,
  `createdUid` int(10) unsigned DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `modifiedTimestamp` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cvID`),
  UNIQUE KEY `UQ_ctControlVocab` (`title`,`tableName`,`fieldName`),
  KEY `FK_ctControlVocab_createUid_idx` (`createdUid`),
  KEY `FK_ctControlVocab_modUid_idx` (`modifiedUid`),
  KEY `FK_ctControlVocab_collid_idx` (`collid`),
  CONSTRAINT `FK_ctControlVocab_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ctControlVocab_createUid` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_ctControlVocab_modUid` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ctcontrolvocab`
--

LOCK TABLES `ctcontrolvocab` WRITE;
/*!40000 ALTER TABLE `ctcontrolvocab` DISABLE KEYS */;
INSERT INTO `ctcontrolvocab` VALUES (1,NULL,'Occurrence Relationship Terms',NULL,NULL,'omoccurassociations','relationship',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,'2020-12-03 04:35:38'),(2,NULL,'Occurrence Relationship subTypes',NULL,NULL,'omoccurassociations','subType',NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,'2020-12-03 05:56:13'),(3,NULL,'Material Sample Type',NULL,NULL,'ommaterialsample','sampleType',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(4,NULL,'Material Sample Type',NULL,NULL,'ommaterialsample','disposition',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(5,NULL,'Material Sample Type',NULL,NULL,'ommaterialsample','preservationType',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(6,NULL,'Material Sample Type',NULL,NULL,'ommaterialsampleextended','fieldName',NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53');
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
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ctcontrolvocabterm`
--

LOCK TABLES `ctcontrolvocabterm` WRITE;
/*!40000 ALTER TABLE `ctcontrolvocabterm` DISABLE KEYS */;
INSERT INTO `ctcontrolvocabterm` VALUES (1,1,NULL,'subsampleOf',NULL,'originatingSampleOf',NULL,'a sample or occurrence that was subsequently derived from an originating sample',NULL,'has part: http://purl.obolibrary.org/obo/BFO_0000050',NULL,1,NULL,NULL,NULL,NULL,'2020-12-03 04:36:51'),(2,1,NULL,'partOf',NULL,'partOf',NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2020-12-03 04:38:32'),(3,1,NULL,'siblingOf',NULL,'siblingOf',NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2020-12-03 04:38:32'),(4,1,NULL,'originatingSampleOf',NULL,'subsampleOf',NULL,'a sample or occurrence that is the originator of a subsequently modified or partial sample',NULL,'partOf: http://purl.obolibrary.org/obo/BFO_0000051',NULL,1,'originatingSourceOf ??  It isn\'t necessarily a sample.  Could be an observation or occurrence or individual etc',NULL,NULL,NULL,'2020-12-03 06:27:02'),(5,1,NULL,'sharesOriginatingSample',NULL,'sharesOriginatingSample',NULL,'two samples or occurrences that were subsequently derived from the same originating sample',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2020-12-03 06:44:23'),(6,2,NULL,'tissue',NULL,NULL,NULL,'a tissue sample or occurrence that was subsequently derived from an originating sample',NULL,'partOf: http://purl.obolibrary.org/obo/BFO_0000051',NULL,1,NULL,NULL,NULL,NULL,'2020-12-03 06:44:23'),(7,2,NULL,'blood',NULL,NULL,NULL,'a blood-tissue sample or occurrence that was subsequently derived from an originating sample',NULL,'partOf: http://purl.obolibrary.org/obo/BFO_0000051',NULL,1,NULL,NULL,NULL,NULL,'2020-12-03 06:44:23'),(8,2,NULL,'fecal',NULL,NULL,NULL,'a fecal sample or occurrence that was subsequently derived from an originating sample',NULL,'partOf: http://purl.obolibrary.org/obo/BFO_0000051',NULL,1,NULL,NULL,NULL,NULL,'2020-12-03 06:44:23'),(9,2,NULL,'hair',NULL,NULL,NULL,'a hair sample or occurrence that was subsequently derived from an originating sample',NULL,'partOf: http://purl.obolibrary.org/obo/BFO_0000051',NULL,1,NULL,NULL,NULL,NULL,'2020-12-03 06:44:23'),(10,2,NULL,'genetic',NULL,NULL,NULL,'a genetic extraction sample or occurrence that was subsequently derived from an originating sample',NULL,'partOf: http://purl.obolibrary.org/obo/BFO_0000051',NULL,1,NULL,NULL,NULL,NULL,'2020-12-03 06:44:23'),(11,1,NULL,'derivedFromSameIndividual',NULL,'derivedFromSameIndividual',NULL,'a sample or occurrence that is derived from the same biological individual as another occurrence or sample',NULL,'partOf: http://purl.obolibrary.org/obo/BFO_0000051',NULL,1,NULL,NULL,NULL,NULL,'2020-12-03 06:48:45'),(12,1,NULL,'analyticalStandardOf',NULL,'hasAnalyticalStandard',NULL,'a sample or occurrence that serves as an analytical standard or control for another occurrence or sample',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2020-12-03 06:48:45'),(13,1,NULL,'hasAnalyticalStandard',NULL,'analyticalStandardof',NULL,'a sample or occurrence that has an available analytical standard or control',NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2020-12-03 06:48:45'),(14,1,NULL,'hasHost',NULL,'hostOf',NULL,'X \'has host\' y if and only if: x is an organism, y is an organism, and x can live on the surface of or within the body of y',NULL,'ecologically related to: http://purl.obolibrary.org/obo/RO_0008506','http://purl.obolibrary.org/obo/RO_0002454',1,NULL,NULL,NULL,NULL,'2020-12-03 06:58:18'),(15,1,NULL,'hostOf',NULL,'hasHost',NULL,'X is \'Host of\' y if and only if: x is an organism, y is an organism, and y can live on the surface of or within the body of x',NULL,'ecologically related to: http://purl.obolibrary.org/obo/RO_0008506','http://purl.obolibrary.org/obo/RO_0002453',1,NULL,NULL,NULL,NULL,'2020-12-03 06:58:18'),(16,1,NULL,'ecologicallyOccursWith',NULL,'ecologicallyOccursWith',NULL,'An interaction relationship describing an occurrence occurring with another organism in the same time and space or same environment',NULL,'ecologically related to: http://purl.obolibrary.org/obo/RO_0008506','http://purl.obolibrary.org/obo/RO_0008506',1,NULL,NULL,NULL,NULL,'2020-12-03 06:58:18'),(17,3,NULL,'tissue',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(18,3,NULL,'culture strain',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(19,3,NULL,'specimen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(20,3,NULL,'DNA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(21,3,NULL,'RNA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(22,3,NULL,'Protein',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(23,3,NULL,'Skin',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(24,3,NULL,'Skull',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(25,3,NULL,'liver',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(26,4,NULL,'being processed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(27,4,NULL,'in collection',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(28,4,NULL,'deaccessioned',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(29,4,NULL,'consumed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(30,4,NULL,'discarded',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(31,4,NULL,'missing',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(32,4,NULL,'on exhibit',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(33,5,NULL,'alsever\'s solution',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(34,5,NULL,'arsenic',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(35,5,NULL,'Bouin\'s solution',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(36,5,NULL,'buffer',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(37,5,NULL,'cleared',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(38,5,NULL,'carbonization',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(39,5,NULL,'DMSO/EDTA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(40,5,NULL,'DESS',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(41,5,NULL,'DMSO',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(42,5,NULL,'desiccated',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(43,5,NULL,'dry',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(44,5,NULL,'ethanol 95%',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(45,5,NULL,'ethanol 80%',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(46,5,NULL,'ethanol 75%',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(47,5,NULL,'ethanol 70%',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(48,5,NULL,'EDTA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(49,5,NULL,'sampleDesignation',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(50,5,NULL,'Frozen -20â”¬â–‘C',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(51,5,NULL,'Frozen -80â”¬â–‘C',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(52,5,NULL,'Frozen -196â”¬â–‘C',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(53,5,NULL,'Liquid Nitrogen',NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(54,6,NULL,'concentration',NULL,NULL,NULL,NULL,'http://data.ggbn.org/schemas/ggbn/terms/concentration',NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(55,6,NULL,'concentrationMethod',NULL,NULL,NULL,NULL,'http://data.ggbn.org/schemas/ggbn/terms/methodDeterminationConcentrationAndRatios',NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(56,6,NULL,'ratioOfAbsorbance260_230',NULL,NULL,NULL,NULL,'http://data.ggbn.org/schemas/ggbn/terms/ratioOfAbsorbance260_230',NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(57,6,NULL,'ratioOfAbsorbance260_280',NULL,NULL,NULL,NULL,'http://data.ggbn.org/schemas/ggbn/terms/ratioOfAbsorbance260_280',NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(58,6,NULL,'volume',NULL,NULL,NULL,NULL,'http://data.ggbn.org/schemas/ggbn/terms/volume',NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(59,6,NULL,'weight',NULL,NULL,NULL,NULL,'http://data.ggbn.org/schemas/ggbn/terms/weight',NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(60,6,NULL,'weightMethod',NULL,NULL,NULL,NULL,'http://data.ggbn.org/schemas/ggbn/terms/methodDeterminationWeight',NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(61,6,NULL,'purificationMethod',NULL,NULL,NULL,NULL,'http://data.ggbn.org/schemas/ggbn/terms/purificationMethod',NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(62,6,NULL,'quality',NULL,NULL,NULL,NULL,'http://data.ggbn.org/schemas/ggbn/terms/quality',NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(63,6,NULL,'qualityRemarks',NULL,NULL,NULL,NULL,'http://data.ggbn.org/schemas/ggbn/terms/qualityRemarks',NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(64,6,NULL,'qualityCheckDate',NULL,NULL,NULL,NULL,'http://data.ggbn.org/schemas/ggbn/terms/qualityCheckDate',NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(65,6,NULL,'sieving',NULL,NULL,NULL,NULL,'http://gensc.org/ns/mixs/sieving',NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(66,6,NULL,'dnaHybridization',NULL,NULL,NULL,NULL,'http://data.ggbn.org/schemas/ggbn/terms/DNADNAHybridization',NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(67,6,NULL,'dnaMeltingPoint',NULL,NULL,NULL,NULL,'http://data.ggbn.org/schemas/ggbn/terms/DNAMeltingPoint',NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(68,6,NULL,'estimatedSize',NULL,NULL,NULL,NULL,'http://gensc.org/ns/mixs/estimated_size',NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(69,6,NULL,'poolDnaExtracts',NULL,NULL,NULL,NULL,'http://gensc.org/ns/mixs/pool_dna_extracts',NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53'),(70,6,NULL,'sampleDesignation',NULL,NULL,NULL,NULL,'http://data.ggbn.org/schemas/ggbn/terms/sampleDesignation',NULL,NULL,1,NULL,NULL,NULL,NULL,'2023-03-26 18:16:53');
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `clid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `title` varchar(150) DEFAULT NULL,
  `locality` varchar(500) DEFAULT NULL,
  `publication` varchar(500) DEFAULT NULL,
  `abstract` text,
  `authors` varchar(250) DEFAULT NULL,
  `type` varchar(50) DEFAULT 'static',
  `politicalDivision` varchar(45) DEFAULT NULL,
  `dynamicSql` varchar(500) DEFAULT NULL,
  `parent` varchar(50) DEFAULT NULL,
  `parentClid` int(10) unsigned DEFAULT NULL,
  `notes` varchar(500) DEFAULT NULL,
  `latCentroid` double(9,6) DEFAULT NULL,
  `longCentroid` double(9,6) DEFAULT NULL,
  `pointRadiusMeters` int(10) unsigned DEFAULT NULL,
  `footprintWkt` text,
  `percentEffort` int(11) DEFAULT NULL,
  `access` varchar(45) DEFAULT 'private',
  `cidKeyLimits` varchar(250) DEFAULT NULL,
  `defaultSettings` varchar(250) DEFAULT NULL,
  `iconUrl` varchar(150) DEFAULT NULL,
  `headerUrl` varchar(150) DEFAULT NULL,
  `dynamicProperties` text,
  `uid` int(10) unsigned DEFAULT NULL,
  `sortSequence` int(10) unsigned NOT NULL DEFAULT '50',
  `expiration` int(10) unsigned DEFAULT NULL,
  `guid` varchar(45) DEFAULT NULL,
  `recordID` varchar(45) DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `dateLastModified` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`clid`),
  KEY `FK_checklists_uid` (`uid`),
  KEY `name` (`name`,`type`) USING BTREE,
  CONSTRAINT `FK_checklists_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `clidChild` int(10) unsigned NOT NULL,
  `modifiedUid` int(10) unsigned NOT NULL,
  `modifiedTimestamp` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`clid`,`clidChild`),
  KEY `FK_fmchklstchild_clid_idx` (`clid`),
  KEY `FK_fmchklstchild_child_idx` (`clidChild`),
  CONSTRAINT `FK_fmchklstchild_child` FOREIGN KEY (`clidChild`) REFERENCES `fmchecklists` (`clid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_fmchklstchild_clid` FOREIGN KEY (`clid`) REFERENCES `fmchecklists` (`clid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `clCoordID` int(11) NOT NULL AUTO_INCREMENT,
  `clid` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `decimalLatitude` double NOT NULL,
  `decimalLongitude` double NOT NULL,
  `sourceName` varchar(75) DEFAULT NULL,
  `sourceIdentifier` varchar(45) DEFAULT NULL,
  `referenceUrl` varchar(250) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `dynamicProperties` text,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`clCoordID`),
  KEY `FKchklsttaxalink` (`clid`,`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pid`,`clid`),
  KEY `FK_chklst` (`clid`),
  CONSTRAINT `FK_chklstprojlink_clid` FOREIGN KEY (`clid`) REFERENCES `fmchecklists` (`clid`),
  CONSTRAINT `FK_chklstprojlink_proj` FOREIGN KEY (`pid`) REFERENCES `fmprojects` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `clTaxaID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tid` int(10) unsigned NOT NULL,
  `clid` int(10) unsigned NOT NULL,
  `morphoSpecies` varchar(45) NOT NULL DEFAULT '',
  `familyOverride` varchar(50) DEFAULT NULL,
  `habitat` varchar(250) DEFAULT NULL,
  `abundance` varchar(50) DEFAULT NULL,
  `notes` varchar(2000) DEFAULT NULL,
  `explicitExclude` smallint(6) DEFAULT NULL,
  `source` varchar(250) DEFAULT NULL,
  `nativity` varchar(50) DEFAULT NULL COMMENT 'native, introducted',
  `endemic` varchar(45) DEFAULT NULL,
  `invasive` varchar(45) DEFAULT NULL,
  `internalNotes` varchar(250) DEFAULT NULL,
  `dynamicProperties` text,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`clTaxaID`),
  UNIQUE KEY `UQ_chklsttaxalink` (`clid`,`tid`,`morphoSpecies`),
  KEY `FK_chklsttaxalink_cid` (`clid`),
  KEY `FK_chklsttaxalink_tid` (`tid`),
  CONSTRAINT `FK_chklsttaxalink_cid` FOREIGN KEY (`clid`) REFERENCES `fmchecklists` (`clid`),
  CONSTRAINT `FK_chklsttaxalink_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fmchklsttaxalink`
--

LOCK TABLES `fmchklsttaxalink` WRITE;
/*!40000 ALTER TABLE `fmchklsttaxalink` DISABLE KEYS */;
/*!40000 ALTER TABLE `fmchklsttaxalink` ENABLE KEYS */;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`dynclid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`dynclid`,`tid`),
  KEY `FK_dyncltaxalink_taxa` (`tid`),
  CONSTRAINT `FK_dyncltaxalink_dynclid` FOREIGN KEY (`dynclid`) REFERENCES `fmdynamicchecklists` (`dynclid`) ON DELETE CASCADE,
  CONSTRAINT `FK_dyncltaxalink_taxa` FOREIGN KEY (`tid`) REFERENCES `taxa` (`tid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fmdyncltaxalink`
--

LOCK TABLES `fmdyncltaxalink` WRITE;
/*!40000 ALTER TABLE `fmdyncltaxalink` DISABLE KEYS */;
/*!40000 ALTER TABLE `fmdyncltaxalink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fmprojects`
--

DROP TABLE IF EXISTS `fmprojects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fmprojects` (
  `pid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `projName` varchar(75) NOT NULL,
  `displayName` varchar(150) DEFAULT NULL,
  `managers` varchar(150) DEFAULT NULL,
  `briefDescription` varchar(300) DEFAULT NULL,
  `fullDescription` varchar(5000) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `iconUrl` varchar(150) DEFAULT NULL,
  `headerUrl` varchar(150) DEFAULT NULL,
  `occurrenceSearch` int(10) unsigned NOT NULL DEFAULT '0',
  `isPublic` int(10) unsigned NOT NULL DEFAULT '0',
  `dynamicProperties` text,
  `parentPid` int(10) unsigned DEFAULT NULL,
  `sortSequence` int(10) unsigned NOT NULL DEFAULT '50',
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pid`),
  KEY `FK_parentpid_proj` (`parentPid`),
  CONSTRAINT `FK_parentpid_proj` FOREIGN KEY (`parentPid`) REFERENCES `fmprojects` (`pid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `voucherID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `clTaxaID` int(10) unsigned NOT NULL,
  `occid` int(10) unsigned NOT NULL,
  `editorNotes` varchar(50) DEFAULT NULL,
  `preferredImage` int(11) DEFAULT '0',
  `notes` varchar(250) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`voucherID`),
  UNIQUE KEY `UQ_fmvouchers_clTaxaID_occid` (`clTaxaID`,`occid`),
  KEY `FK_fmvouchers_occ_idx` (`occid`),
  KEY `FK_fmvouchers_tidclid_idx` (`clTaxaID`),
  CONSTRAINT `FK_fmvouchers_occ` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_fmvouchers_tidclid` FOREIGN KEY (`clTaxaID`) REFERENCES `fmchklsttaxalink` (`clTaxaID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `geoTerm` varchar(100) DEFAULT NULL,
  `abbreviation` varchar(45) DEFAULT NULL,
  `iso2` varchar(45) DEFAULT NULL,
  `iso3` varchar(45) DEFAULT NULL,
  `numCode` int(11) DEFAULT NULL,
  `category` varchar(45) DEFAULT NULL,
  `geoLevel` int(11) NOT NULL,
  `termStatus` int(11) DEFAULT NULL,
  `acceptedID` int(11) DEFAULT NULL,
  `parentID` int(11) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `dynamicProps` text,
  `footprintWKT` text,
  `initialTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`geoThesID`),
  UNIQUE KEY `UQ_geothes` (`geoTerm`,`parentID`),
  KEY `IX_geothes_termname` (`geoTerm`),
  KEY `IX_geothes_abbreviation` (`abbreviation`),
  KEY `IX_geothes_iso2` (`iso2`),
  KEY `IX_geothes_iso3` (`iso3`),
  KEY `FK_geothes_acceptedID_idx` (`acceptedID`),
  KEY `FK_geothes_parentID_idx` (`parentID`),
  CONSTRAINT `FK_geothes_acceptedID` FOREIGN KEY (`acceptedID`) REFERENCES `geographicthesaurus` (`geoThesID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_geothes_parentID` FOREIGN KEY (`parentID`) REFERENCES `geographicthesaurus` (`geoThesID`) ON UPDATE CASCADE
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
-- Table structure for table `glossary`
--

DROP TABLE IF EXISTS `glossary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `glossary` (
  `glossID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `term` varchar(150) NOT NULL,
  `plural` varchar(150) DEFAULT NULL,
  `termType` varchar(45) DEFAULT NULL,
  `definition` varchar(2000) DEFAULT NULL,
  `language` varchar(45) NOT NULL DEFAULT 'English',
  `langid` int(10) unsigned DEFAULT NULL,
  `origin` varchar(45) DEFAULT NULL,
  `source` varchar(1000) DEFAULT NULL,
  `translator` varchar(250) DEFAULT NULL,
  `author` varchar(250) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `notesInternal` varchar(250) DEFAULT NULL,
  `resourceUrl` varchar(600) DEFAULT NULL,
  `uid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`glossID`),
  KEY `Index_term` (`term`),
  KEY `Index_glossary_lang` (`language`),
  KEY `FK_glossary_uid_idx` (`uid`),
  KEY `IX_gloassary_plural` (`plural`),
  CONSTRAINT `FK_glossary_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `glossary`
--

LOCK TABLES `glossary` WRITE;
/*!40000 ALTER TABLE `glossary` DISABLE KEYS */;
/*!40000 ALTER TABLE `glossary` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `glossarycategory`
--

DROP TABLE IF EXISTS `glossarycategory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `glossarycategory` (
  `glossCatID` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(45) DEFAULT NULL,
  `rankID` int(11) DEFAULT '10',
  `langID` int(11) DEFAULT NULL,
  `parentCatID` int(11) DEFAULT NULL,
  `translationCatID` int(11) DEFAULT NULL,
  `notes` varchar(150) DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `modifiedTimestamp` timestamp NULL DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`glossCatID`),
  UNIQUE KEY `UQ_glossary_category_term` (`category`,`langID`,`rankID`),
  KEY `FK_glossarycategory_lang_idx` (`langID`),
  KEY `IX_glossarycategory_cat` (`category`),
  KEY `FK_glossarycategory_transCatID_idx` (`translationCatID`),
  KEY `FK_glossarycategory_parentCatID_idx` (`parentCatID`),
  CONSTRAINT `FK_glossarycategory_lang` FOREIGN KEY (`langID`) REFERENCES `adminlanguages` (`langid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_glossarycategory_parentCatID` FOREIGN KEY (`parentCatID`) REFERENCES `glossarycategory` (`glossCatID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_glossarycategory_transCatID` FOREIGN KEY (`translationCatID`) REFERENCES `glossarycategory` (`glossCatID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `glossarycategory`
--

LOCK TABLES `glossarycategory` WRITE;
/*!40000 ALTER TABLE `glossarycategory` DISABLE KEYS */;
/*!40000 ALTER TABLE `glossarycategory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `glossarycategorylink`
--

DROP TABLE IF EXISTS `glossarycategorylink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `glossarycategorylink` (
  `glossCatLinkID` int(11) NOT NULL AUTO_INCREMENT,
  `glossID` int(10) unsigned NOT NULL,
  `glossCatID` int(11) NOT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`glossCatLinkID`),
  KEY `FK_glossCatLink_glossID_idx` (`glossID`),
  KEY `FK_glossCatLink_glossCatID_idx` (`glossCatID`),
  CONSTRAINT `FK_glossCatLink_glossCatID` FOREIGN KEY (`glossCatID`) REFERENCES `glossarycategory` (`glossCatID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_glossCatLink_glossID` FOREIGN KEY (`glossID`) REFERENCES `glossary` (`glossID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `glossarycategorylink`
--

LOCK TABLES `glossarycategorylink` WRITE;
/*!40000 ALTER TABLE `glossarycategorylink` DISABLE KEYS */;
/*!40000 ALTER TABLE `glossarycategorylink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `glossaryimages`
--

DROP TABLE IF EXISTS `glossaryimages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `glossaryimages` (
  `glimgid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `glossID` int(10) unsigned NOT NULL,
  `url` varchar(255) NOT NULL,
  `thumbnailUrl` varchar(255) DEFAULT NULL,
  `structures` varchar(150) DEFAULT NULL,
  `sortSequence` int(11) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `createdBy` varchar(250) DEFAULT NULL,
  `uid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`glimgid`),
  KEY `FK_glossaryimages_gloss` (`glossID`),
  KEY `FK_glossaryimages_uid_idx` (`uid`),
  CONSTRAINT `FK_glossaryimages_glossid` FOREIGN KEY (`glossID`) REFERENCES `glossary` (`glossID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_glossaryimages_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tid`),
  CONSTRAINT `FK_glossarysources_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`tid`)
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
  `glossID` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`glossID`,`tid`),
  KEY `glossarytaxalink_ibfk_1` (`tid`),
  CONSTRAINT `FK_glossarytaxa_glossid` FOREIGN KEY (`glossID`) REFERENCES `glossary` (`glossID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_glossarytaxa_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`tid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `glossGrpID` int(10) unsigned NOT NULL,
  `glossID` int(10) unsigned NOT NULL,
  `relationshipType` varchar(45) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`gltlinkid`),
  UNIQUE KEY `Unique_termkeys` (`glossGrpID`,`glossID`),
  KEY `glossarytermlink_ibfk_1` (`glossID`),
  CONSTRAINT `FK_glossarytermlink_glossgrpid` FOREIGN KEY (`glossGrpID`) REFERENCES `glossary` (`glossID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_glossarytermlink_glossid` FOREIGN KEY (`glossID`) REFERENCES `glossary` (`glossID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `glossarytermlink`
--

LOCK TABLES `glossarytermlink` WRITE;
/*!40000 ALTER TABLE `glossarytermlink` DISABLE KEYS */;
/*!40000 ALTER TABLE `glossarytermlink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `igsnverification`
--

DROP TABLE IF EXISTS `igsnverification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `igsnverification` (
  `igsn` varchar(15) NOT NULL,
  `occidInPortal` int(10) unsigned DEFAULT NULL,
  `occidInSesar` int(10) unsigned DEFAULT NULL,
  `catalogNumber` varchar(45) DEFAULT NULL,
  `syncStatus` varchar(45) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `FK_igsn_occid_idx` (`occidInPortal`),
  KEY `INDEX_igsn` (`igsn`),
  CONSTRAINT `FK_igsn_occid` FOREIGN KEY (`occidInPortal`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
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
-- Table structure for table `imagekeywords`
--

DROP TABLE IF EXISTS `imagekeywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `imagekeywords` (
  `imgKeywordID` int(11) NOT NULL AUTO_INCREMENT,
  `imgid` int(10) unsigned NOT NULL,
  `keyword` varchar(45) NOT NULL,
  `uidassignedby` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`imgKeywordID`),
  KEY `FK_imagekeywords_imgid_idx` (`imgid`),
  KEY `FK_imagekeyword_uid_idx` (`uidassignedby`),
  KEY `INDEX_imagekeyword` (`keyword`),
  CONSTRAINT `FK_imagekeyword_uid` FOREIGN KEY (`uidassignedby`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_imagekeywords_imgid` FOREIGN KEY (`imgid`) REFERENCES `images` (`imgid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `imagekeywords`
--

LOCK TABLES `imagekeywords` WRITE;
/*!40000 ALTER TABLE `imagekeywords` DISABLE KEYS */;
/*!40000 ALTER TABLE `imagekeywords` ENABLE KEYS */;
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
  `thumbnailUrl` varchar(255) DEFAULT NULL,
  `originalUrl` varchar(255) DEFAULT NULL,
  `archiveUrl` varchar(255) DEFAULT NULL,
  `photographer` varchar(100) DEFAULT NULL,
  `photographerUid` int(10) unsigned DEFAULT NULL,
  `imageType` varchar(50) DEFAULT NULL,
  `format` varchar(45) DEFAULT NULL,
  `caption` varchar(100) DEFAULT NULL,
  `owner` varchar(250) DEFAULT NULL,
  `sourceUrl` varchar(255) DEFAULT NULL,
  `referenceUrl` varchar(255) DEFAULT NULL,
  `copyright` varchar(255) DEFAULT NULL,
  `rights` varchar(255) DEFAULT NULL,
  `accessRights` varchar(255) DEFAULT NULL,
  `locality` varchar(250) DEFAULT NULL,
  `occid` int(10) unsigned DEFAULT NULL,
  `notes` varchar(350) DEFAULT NULL,
  `anatomy` varchar(100) DEFAULT NULL,
  `username` varchar(45) DEFAULT NULL,
  `sourceIdentifier` varchar(150) DEFAULT NULL,
  `hashFunction` varchar(45) DEFAULT NULL,
  `hashValue` varchar(45) DEFAULT NULL,
  `mediaMD5` varchar(45) DEFAULT NULL,
  `dynamicProperties` text,
  `defaultDisplay` int(11) DEFAULT NULL,
  `recordID` varchar(45) DEFAULT NULL,
  `sortSequence` int(10) unsigned NOT NULL DEFAULT '50',
  `sortOccurrence` int(11) DEFAULT '5',
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`imgid`) USING BTREE,
  KEY `Index_tid` (`tid`),
  KEY `FK_images_occ` (`occid`),
  KEY `FK_photographeruid` (`photographerUid`),
  KEY `Index_images_datelastmod` (`initialTimestamp`),
  KEY `IX_images_recordID` (`recordID`),
  CONSTRAINT `FK_images_occ` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`),
  CONSTRAINT `FK_photographeruid` FOREIGN KEY (`photographerUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_taxaimagestid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `keyValue` varchar(30) NOT NULL,
  `imageBoundingBox` varchar(45) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`imagetagid`),
  UNIQUE KEY `imgid` (`imgid`,`keyValue`),
  KEY `keyvalue` (`keyValue`),
  KEY `FK_imagetag_imgid_idx` (`imgid`),
  CONSTRAINT `FK_imagetag_imgid` FOREIGN KEY (`imgid`) REFERENCES `images` (`imgid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_imagetag_tagkey` FOREIGN KEY (`keyValue`) REFERENCES `imagetagkey` (`tagkey`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `imagetag`
--

LOCK TABLES `imagetag` WRITE;
/*!40000 ALTER TABLE `imagetag` DISABLE KEYS */;
/*!40000 ALTER TABLE `imagetag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `imagetaggroup`
--

DROP TABLE IF EXISTS `imagetaggroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `imagetaggroup` (
  `imgTagGroupID` int(11) NOT NULL AUTO_INCREMENT,
  `groupName` varchar(45) NOT NULL,
  `category` varchar(45) DEFAULT NULL,
  `resourceUrl` varchar(150) DEFAULT NULL,
  `audubonCoreTarget` varchar(45) DEFAULT NULL,
  `controlType` varchar(45) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`imgTagGroupID`),
  KEY `IX_imagetaggroup` (`groupName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `imagetaggroup`
--

LOCK TABLES `imagetaggroup` WRITE;
/*!40000 ALTER TABLE `imagetaggroup` DISABLE KEYS */;
/*!40000 ALTER TABLE `imagetaggroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `imagetagkey`
--

DROP TABLE IF EXISTS `imagetagkey`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `imagetagkey` (
  `tagkey` varchar(30) NOT NULL,
  `imgTagGroupID` int(11) DEFAULT NULL,
  `shortlabel` varchar(30) NOT NULL,
  `description_en` varchar(255) NOT NULL,
  `tagDescription` varchar(255) NOT NULL,
  `resourceLink` varchar(250) DEFAULT NULL,
  `audubonCoreTarget` varchar(45) DEFAULT NULL,
  `sortOrder` int(11) NOT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tagkey`),
  KEY `sortorder` (`sortOrder`),
  KEY `FK_imageTagKey_imgTagGroupID_idx` (`imgTagGroupID`),
  CONSTRAINT `FK_imageTagKey_imgTagGroupID` FOREIGN KEY (`imgTagGroupID`) REFERENCES `imagetaggroup` (`imgTagGroupID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `imagetagkey`
--

LOCK TABLES `imagetagkey` WRITE;
/*!40000 ALTER TABLE `imagetagkey` DISABLE KEYS */;
INSERT INTO `imagetagkey` VALUES ('Diagnostic',NULL,'Diagnostic','Image contains a diagnostic character.','Image contains a diagnostic character.',NULL,NULL,70,'2023-03-26 18:15:40'),('Handwriting',NULL,'Handwritten','Image has handwritten label text.','Image has handwritten label text.',NULL,NULL,40,'2023-03-26 18:15:40'),('HasIDLabel',NULL,'Annotation','Image shows an annotation/identification label.','Image shows an annotation/identification label.',NULL,NULL,20,'2023-03-26 18:15:40'),('HasLabel',NULL,'Label','Image shows label data.','Image shows label data.',NULL,NULL,10,'2023-03-26 18:15:40'),('HasOrganism',NULL,'Organism','Image shows an organism.','Image shows an organism.',NULL,NULL,0,'2023-03-26 18:15:40'),('HasProblem',NULL,'QC Problem','There is a problem with this image.','There is a problem with this image.',NULL,NULL,60,'2023-03-26 18:15:40'),('ImageOfAdult',NULL,'Adult','Image contains the adult organism.','Image contains the adult organism.',NULL,NULL,80,'2023-03-26 18:15:40'),('ImageOfImmature',NULL,'Immature','Image contains the immature organism.','Image contains the immature organism.',NULL,NULL,90,'2023-03-26 18:15:40'),('ShowsHabitat',NULL,'Habitat','Field image of habitat.','Field image of habitat.',NULL,NULL,50,'2023-03-26 18:15:40'),('TypedText',NULL,'Typed/Printed','Image has typed or printed text.','Image has typed or printed text.',NULL,NULL,30,'2023-03-26 18:15:40');
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
  `institutionID` varchar(45) DEFAULT NULL,
  `institutionCode` varchar(45) NOT NULL,
  `institutionName` varchar(150) NOT NULL,
  `institutionName2` varchar(255) DEFAULT NULL,
  `address1` varchar(150) DEFAULT NULL,
  `address2` varchar(150) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `stateProvince` varchar(45) DEFAULT NULL,
  `postalCode` varchar(45) DEFAULT NULL,
  `country` varchar(45) DEFAULT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `contact` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `url` varchar(250) DEFAULT NULL,
  `notes` varchar(19500) DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `modifiedTimeStamp` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`iid`),
  KEY `FK_inst_uid_idx` (`modifiedUid`),
  CONSTRAINT `FK_inst_uid` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `charName` varchar(150) NOT NULL,
  `language` varchar(45) DEFAULT NULL,
  `langid` int(11) NOT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `helpUrl` varchar(500) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cid`,`langid`) USING BTREE,
  KEY `FK_charlang_lang_idx` (`langid`),
  CONSTRAINT `FK_characterlang_1` FOREIGN KEY (`cid`) REFERENCES `kmcharacters` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_charlang_lang` FOREIGN KEY (`langid`) REFERENCES `adminlanguages` (`langid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cid`),
  KEY `Index_charname` (`charname`),
  KEY `Index_sort` (`sortsequence`),
  KEY `FK_charheading_idx` (`hid`),
  KEY `FK_kmchar_glossary_idx` (`glossid`),
  CONSTRAINT `FK_charheading` FOREIGN KEY (`hid`) REFERENCES `kmcharheading` (`hid`) ON UPDATE CASCADE,
  CONSTRAINT `FK_kmchar_glossary` FOREIGN KEY (`glossid`) REFERENCES `glossary` (`glossID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`CSDependance`,`CIDDependance`,`CID`) USING BTREE,
  KEY `FK_chardependance_cid_idx` (`CID`),
  KEY `FK_chardependance_cs_idx` (`CIDDependance`,`CSDependance`),
  CONSTRAINT `FK_chardependance_cid` FOREIGN KEY (`CID`) REFERENCES `kmcharacters` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_chardependance_cs` FOREIGN KEY (`CIDDependance`, `CSDependance`) REFERENCES `kmcs` (`cid`, `cs`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`hid`,`langid`) USING BTREE,
  UNIQUE KEY `unique_kmcharheading` (`headingname`,`langid`),
  KEY `HeadingName` (`headingname`) USING BTREE,
  KEY `FK_kmcharheading_lang_idx` (`langid`),
  CONSTRAINT `FK_kmcharheading_lang` FOREIGN KEY (`langid`) REFERENCES `adminlanguages` (`langid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  CONSTRAINT `FK_chartaxalink_tid` FOREIGN KEY (`TID`) REFERENCES `taxa` (`tid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `sortSequence` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `EnteredBy` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`cs`,`cid`),
  KEY `FK_cs_chars` (`cid`),
  KEY `FK_kmcs_glossid_idx` (`glossid`),
  CONSTRAINT `FK_cs_chars` FOREIGN KEY (`cid`) REFERENCES `kmcharacters` (`cid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_kmcs_glossid` FOREIGN KEY (`glossid`) REFERENCES `glossary` (`glossID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`csimgid`),
  KEY `FK_kscsimages_kscs_idx` (`cid`,`cs`),
  CONSTRAINT `FK_kscsimages_kscs` FOREIGN KEY (`cid`, `cs`) REFERENCES `kmcs` (`cid`, `cs`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  CONSTRAINT `FK_descr_tid` FOREIGN KEY (`TID`) REFERENCES `taxa` (`tid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kmdescr`
--

LOCK TABLES `kmdescr` WRITE;
/*!40000 ALTER TABLE `kmdescr` DISABLE KEYS */;
/*!40000 ALTER TABLE `kmdescr` ENABLE KEYS */;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ccpk`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ccpk`,`collid`),
  KEY `FK_collcatlink_coll` (`collid`),
  CONSTRAINT `FK_collcatlink_cat` FOREIGN KEY (`ccpk`) REFERENCES `omcollcategories` (`ccpk`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_collcatlink_coll` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`CollID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `collID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `institutionCode` varchar(45) NOT NULL,
  `collectionCode` varchar(45) DEFAULT NULL,
  `collectionName` varchar(150) NOT NULL,
  `collectionID` varchar(100) DEFAULT NULL,
  `datasetID` varchar(250) DEFAULT NULL,
  `datasetName` varchar(100) DEFAULT NULL,
  `iid` int(10) unsigned DEFAULT NULL,
  `fullDescription` varchar(2000) DEFAULT NULL,
  `homepage` varchar(250) DEFAULT NULL,
  `resourceJson` longtext,
  `individualUrl` varchar(500) DEFAULT NULL,
  `Contact` varchar(250) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `contactJson` longtext,
  `latitudeDecimal` double(8,6) DEFAULT NULL,
  `longitudeDecimal` double(9,6) DEFAULT NULL,
  `icon` varchar(250) DEFAULT NULL,
  `collType` varchar(45) NOT NULL DEFAULT 'Preserved Specimens' COMMENT 'Preserved Specimens, General Observations, Observations',
  `managementType` varchar(45) DEFAULT 'Snapshot' COMMENT 'Snapshot, Live Data',
  `publicEdits` int(1) unsigned NOT NULL DEFAULT '1',
  `collectionGuid` varchar(45) DEFAULT NULL,
  `securityKey` varchar(45) DEFAULT NULL,
  `guidTarget` varchar(45) DEFAULT NULL,
  `rightsHolder` varchar(250) DEFAULT NULL,
  `rights` varchar(250) DEFAULT NULL,
  `usageTerm` varchar(250) DEFAULT NULL,
  `publishToGbif` int(11) DEFAULT NULL,
  `publishToIdigbio` int(11) DEFAULT NULL,
  `aggKeysStr` varchar(1000) DEFAULT NULL,
  `recordID` varchar(45) DEFAULT NULL,
  `dwcTermJson` text,
  `dwcaUrl` varchar(250) DEFAULT NULL,
  `bibliographicCitation` varchar(1000) DEFAULT NULL,
  `accessRights` varchar(1000) DEFAULT NULL,
  `dynamicProperties` text,
  `sortSeq` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`collID`) USING BTREE,
  UNIQUE KEY `Index_inst` (`institutionCode`,`collectionCode`),
  KEY `FK_collid_iid_idx` (`iid`),
  CONSTRAINT `FK_collid_iid` FOREIGN KEY (`iid`) REFERENCES `institutions` (`iid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omcollections`
--

LOCK TABLES `omcollections` WRITE;
/*!40000 ALTER TABLE `omcollections` DISABLE KEYS */;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`collid`),
  CONSTRAINT `FK_collectionstats_coll` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`collID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omcollectionstats`
--

LOCK TABLES `omcollectionstats` WRITE;
/*!40000 ALTER TABLE `omcollectionstats` DISABLE KEYS */;
/*!40000 ALTER TABLE `omcollectionstats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omcollproperties`
--

DROP TABLE IF EXISTS `omcollproperties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omcollproperties` (
  `collPropID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `collid` int(10) unsigned NOT NULL,
  `propCategory` varchar(45) NOT NULL,
  `propTitle` varchar(45) NOT NULL,
  `propJson` longtext,
  `notes` varchar(255) DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `modifiedTimestamp` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`collPropID`),
  KEY `FK_omcollproperties_collid_idx` (`collid`),
  KEY `FK_omcollproperties_uid_idx` (`modifiedUid`),
  CONSTRAINT `FK_omcollproperties_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`collID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_omcollproperties_uid` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omcollproperties`
--

LOCK TABLES `omcollproperties` WRITE;
/*!40000 ALTER TABLE `omcollproperties` DISABLE KEYS */;
/*!40000 ALTER TABLE `omcollproperties` ENABLE KEYS */;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`omcsid`),
  UNIQUE KEY `Index_omcrowdsourcecentral_collid` (`collid`),
  KEY `FK_omcrowdsourcecentral_collid` (`collid`),
  CONSTRAINT `FK_omcrowdsourcecentral_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`collID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omcrowdsourcecentral`
--

LOCK TABLES `omcrowdsourcecentral` WRITE;
/*!40000 ALTER TABLE `omcrowdsourcecentral` DISABLE KEYS */;
/*!40000 ALTER TABLE `omcrowdsourcecentral` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omcrowdsourceproject`
--

DROP TABLE IF EXISTS `omcrowdsourceproject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omcrowdsourceproject` (
  `csProjID` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(45) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `instructions` text,
  `trainingurl` varchar(250) DEFAULT NULL,
  `managers` varchar(150) DEFAULT NULL,
  `criteria` varchar(1500) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `modifiedTimestamp` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`csProjID`),
  KEY `FK_croudsourceproj_uid_idx` (`modifiedUid`),
  CONSTRAINT `FK_croudsourceproj_uid` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omcrowdsourceproject`
--

LOCK TABLES `omcrowdsourceproject` WRITE;
/*!40000 ALTER TABLE `omcrowdsourceproject` DISABLE KEYS */;
/*!40000 ALTER TABLE `omcrowdsourceproject` ENABLE KEYS */;
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
  `csProjID` int(11) DEFAULT NULL,
  `occid` int(10) unsigned NOT NULL,
  `reviewstatus` int(11) NOT NULL DEFAULT '0' COMMENT '0=open,5=pending review, 10=closed',
  `uidprocessor` int(10) unsigned DEFAULT NULL,
  `points` int(11) DEFAULT NULL COMMENT '0=fail, 1=minor edits, 2=no edits <default>, 3=excelled',
  `isvolunteer` int(2) NOT NULL DEFAULT '1',
  `dateProcessed` datetime DEFAULT NULL,
  `dateReviewed` datetime DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idomcrowdsourcequeue`),
  UNIQUE KEY `Index_omcrowdsource_occid` (`occid`),
  KEY `FK_omcrowdsourcequeue_occid` (`occid`),
  KEY `FK_omcrowdsourcequeue_uid` (`uidprocessor`),
  KEY `FK_omcrowdsourcequeue_csProjID_idx` (`csProjID`),
  CONSTRAINT `FK_omcrowdsourcequeue_csProjID` FOREIGN KEY (`csProjID`) REFERENCES `omcrowdsourceproject` (`csProjID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_omcrowdsourcequeue_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_omcrowdsourcequeue_uid` FOREIGN KEY (`uidprocessor`) REFERENCES `users` (`uid`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`omenid`),
  UNIQUE KEY `Index_omexsiccatinumbers_unique` (`exsnumber`,`ometid`),
  KEY `FK_exsiccatiTitleNumber` (`ometid`),
  CONSTRAINT `FK_exsiccatiTitleNumber` FOREIGN KEY (`ometid`) REFERENCES `omexsiccatititles` (`ometid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`omenid`,`occid`),
  UNIQUE KEY `UniqueOmexsiccatiOccLink` (`occid`),
  KEY `FKExsiccatiNumOccLink1` (`omenid`),
  KEY `FKExsiccatiNumOccLink2` (`occid`),
  CONSTRAINT `FKExsiccatiNumOccLink1` FOREIGN KEY (`omenid`) REFERENCES `omexsiccatinumbers` (`omenid`) ON DELETE CASCADE,
  CONSTRAINT `FKExsiccatiNumOccLink2` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ometid`),
  KEY `index_exsiccatiTitle` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omexsiccatititles`
--

LOCK TABLES `omexsiccatititles` WRITE;
/*!40000 ALTER TABLE `omexsiccatititles` DISABLE KEYS */;
/*!40000 ALTER TABLE `omexsiccatititles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ommaterialsample`
--

DROP TABLE IF EXISTS `ommaterialsample`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ommaterialsample` (
  `matSampleID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `sampleType` varchar(45) NOT NULL,
  `catalogNumber` varchar(45) DEFAULT NULL,
  `guid` varchar(150) DEFAULT NULL,
  `sampleCondition` varchar(45) DEFAULT NULL,
  `disposition` varchar(45) DEFAULT NULL,
  `preservationType` varchar(45) DEFAULT NULL,
  `preparationDetails` varchar(250) DEFAULT NULL,
  `preparationDate` date DEFAULT NULL,
  `preparedByUid` int(10) unsigned DEFAULT NULL,
  `individualCount` varchar(45) DEFAULT NULL,
  `sampleSize` varchar(45) DEFAULT NULL,
  `storageLocation` varchar(45) DEFAULT NULL,
  `remarks` varchar(250) DEFAULT NULL,
  `dynamicFields` longtext,
  `recordID` varchar(45) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`matSampleID`),
  UNIQUE KEY `UQ_ommatsample_recordID` (`recordID`),
  UNIQUE KEY `UQ_ommatsample_catNum` (`occid`,`catalogNumber`),
  UNIQUE KEY `UQ_ommatsample_guid` (`occid`,`guid`),
  KEY `FK_ommatsample_occid_idx` (`occid`),
  KEY `FK_ommatsample_prepUid_idx` (`preparedByUid`),
  CONSTRAINT `FK_ommatsample_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_ommatsample_prepUid` FOREIGN KEY (`preparedByUid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ommaterialsample`
--

LOCK TABLES `ommaterialsample` WRITE;
/*!40000 ALTER TABLE `ommaterialsample` DISABLE KEYS */;
/*!40000 ALTER TABLE `ommaterialsample` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ommaterialsampleextended`
--

DROP TABLE IF EXISTS `ommaterialsampleextended`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ommaterialsampleextended` (
  `matSampleExtendedID` int(11) NOT NULL AUTO_INCREMENT,
  `matSampleID` int(10) unsigned NOT NULL,
  `fieldName` varchar(45) NOT NULL,
  `fieldValue` varchar(250) NOT NULL,
  `fieldUnits` varchar(45) DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`matSampleExtendedID`),
  KEY `FK_matsampleextend_matSampleID_idx` (`matSampleID`),
  KEY `IX_matsampleextend_fieldName` (`fieldName`),
  KEY `IX_matsampleextend_fieldValue` (`fieldValue`),
  CONSTRAINT `FK_matsampleextend_matSampleID` FOREIGN KEY (`matSampleID`) REFERENCES `ommaterialsample` (`matSampleID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ommaterialsampleextended`
--

LOCK TABLES `ommaterialsampleextended` WRITE;
/*!40000 ALTER TABLE `ommaterialsampleextended` DISABLE KEYS */;
/*!40000 ALTER TABLE `ommaterialsampleextended` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omoccuraccess`
--

DROP TABLE IF EXISTS `omoccuraccess`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccuraccess` (
  `occurAccessID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ipaddress` varchar(45) NOT NULL,
  `accessType` varchar(45) NOT NULL,
  `queryStr` text,
  `userAgent` text,
  `frontendGuid` varchar(45) DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`occurAccessID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccuraccess`
--

LOCK TABLES `omoccuraccess` WRITE;
/*!40000 ALTER TABLE `omoccuraccess` DISABLE KEYS */;
/*!40000 ALTER TABLE `omoccuraccess` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omoccuraccesslink`
--

DROP TABLE IF EXISTS `omoccuraccesslink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccuraccesslink` (
  `occurAccessID` bigint(20) unsigned NOT NULL,
  `occid` int(10) unsigned NOT NULL,
  `initialTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`occurAccessID`,`occid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccuraccesslink`
--

LOCK TABLES `omoccuraccesslink` WRITE;
/*!40000 ALTER TABLE `omoccuraccesslink` DISABLE KEYS */;
/*!40000 ALTER TABLE `omoccuraccesslink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omoccuraccesssummary`
--

DROP TABLE IF EXISTS `omoccuraccesssummary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccuraccesssummary` (
  `oasid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ipaddress` varchar(45) NOT NULL,
  `accessDate` date NOT NULL,
  `cnt` int(10) unsigned NOT NULL,
  `accessType` varchar(45) NOT NULL,
  `queryStr` text,
  `userAgent` text,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`oasid`),
  UNIQUE KEY `UNIQUE_occuraccess` (`ipaddress`,`accessDate`,`accessType`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccuraccesssummary`
--

LOCK TABLES `omoccuraccesssummary` WRITE;
/*!40000 ALTER TABLE `omoccuraccesssummary` DISABLE KEYS */;
/*!40000 ALTER TABLE `omoccuraccesssummary` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omoccuraccesssummarylink`
--

DROP TABLE IF EXISTS `omoccuraccesssummarylink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccuraccesssummarylink` (
  `oasid` bigint(20) unsigned NOT NULL,
  `occid` int(10) unsigned NOT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`oasid`,`occid`),
  KEY `omoccuraccesssummarylink_occid_idx` (`occid`),
  CONSTRAINT `FK_omoccuraccesssummarylink_oasid` FOREIGN KEY (`oasid`) REFERENCES `omoccuraccesssummary` (`oasid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccuraccesssummarylink_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccuraccesssummarylink`
--

LOCK TABLES `omoccuraccesssummarylink` WRITE;
/*!40000 ALTER TABLE `omoccuraccesssummarylink` DISABLE KEYS */;
/*!40000 ALTER TABLE `omoccuraccesssummarylink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omoccurarchive`
--

DROP TABLE IF EXISTS `omoccurarchive`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurarchive` (
  `archiveID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `archiveObj` text NOT NULL,
  `occid` int(10) unsigned DEFAULT NULL,
  `catalogNumber` varchar(45) DEFAULT NULL,
  `occurrenceID` varchar(255) DEFAULT NULL,
  `recordID` varchar(45) DEFAULT NULL,
  `archiveReason` varchar(45) DEFAULT NULL,
  `remarks` varchar(150) DEFAULT NULL,
  `createdUid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`archiveID`),
  UNIQUE KEY `UQ_occurarchive_occid` (`occid`),
  KEY `IX_occurarchive_catnum` (`catalogNumber`),
  KEY `IX_occurarchive_occurrenceID` (`occurrenceID`),
  KEY `IX_occurarchive_recordID` (`recordID`),
  KEY `FK_occurarchive_uid_idx` (`createdUid`),
  CONSTRAINT `FK_occurarchive_uid` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccurarchive`
--

LOCK TABLES `omoccurarchive` WRITE;
/*!40000 ALTER TABLE `omoccurarchive` DISABLE KEYS */;
/*!40000 ALTER TABLE `omoccurarchive` ENABLE KEYS */;
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
  `relationship` varchar(150) NOT NULL COMMENT 'dwc:relationshipOfResource',
  `relationshipID` varchar(45) DEFAULT NULL COMMENT 'dwc:relationshipOfResourceID (e.g. ontology link)',
  `subType` varchar(45) DEFAULT NULL,
  `identifier` varchar(250) DEFAULT NULL COMMENT 'dwc:relatedResourceID (object identifier)',
  `basisOfRecord` varchar(45) DEFAULT NULL,
  `resourceUrl` varchar(250) DEFAULT NULL COMMENT 'link to resource',
  `verbatimSciname` varchar(250) DEFAULT NULL,
  `tid` int(11) unsigned DEFAULT NULL,
  `locationOnHost` varchar(250) DEFAULT NULL,
  `conditionOfAssociate` varchar(250) DEFAULT NULL,
  `establishedDate` datetime DEFAULT NULL COMMENT 'dwc:relationshipEstablishedDate',
  `imageMapJSON` text,
  `dynamicProperties` text,
  `notes` varchar(250) DEFAULT NULL COMMENT 'dwc:relationshipRemarks',
  `accordingTo` varchar(45) DEFAULT NULL COMMENT 'dwc:relationshipAccordingTo (verbatim text)',
  `sourceIdentifier` varchar(45) DEFAULT NULL COMMENT 'dwc:resourceRelationshipID, if association was defined externally ',
  `recordID` varchar(45) DEFAULT NULL COMMENT 'dwc:resourceRelationshipID, if association was defined internally ',
  `createdUid` int(10) unsigned DEFAULT NULL,
  `modifiedTimestamp` datetime DEFAULT NULL,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
  CONSTRAINT `FK_occurassoc_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`tid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_occurassoc_uidcreated` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_occurassoc_uidmodified` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`comid`),
  KEY `fk_omoccurcomments_occid` (`occid`),
  KEY `fk_omoccurcomments_uid` (`uid`),
  CONSTRAINT `fk_omoccurcomments_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_omoccurcomments_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`occid`,`datasetid`),
  KEY `FK_omoccurdatasetlink_datasetid` (`datasetid`),
  KEY `FK_omoccurdatasetlink_occid` (`occid`),
  CONSTRAINT `FK_omoccurdatasetlink_datasetid` FOREIGN KEY (`datasetid`) REFERENCES `omoccurdatasets` (`datasetID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurdatasetlink_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `datasetID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `datasetName` varchar(150) DEFAULT NULL,
  `bibliographicCitation` varchar(500) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(45) DEFAULT NULL,
  `isPublic` int(11) DEFAULT NULL,
  `parentDatasetID` int(10) unsigned DEFAULT NULL,
  `includeInSearch` int(11) DEFAULT NULL,
  `description` text,
  `datasetIdentifier` varchar(150) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `dynamicProperties` text,
  `sortSequence` int(11) DEFAULT NULL,
  `uid` int(11) unsigned DEFAULT NULL,
  `collid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`datasetID`),
  KEY `FK_omoccurdatasets_uid_idx` (`uid`),
  KEY `FK_omcollections_collid_idx` (`collid`),
  KEY `FK_omoccurdatasets_parent_idx` (`parentDatasetID`),
  CONSTRAINT `FK_omcollections_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`collID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurdatasets_parent` FOREIGN KEY (`parentDatasetID`) REFERENCES `omoccurdatasets` (`datasetID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurdatasets_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccurdatasets`
--

LOCK TABLES `omoccurdatasets` WRITE;
/*!40000 ALTER TABLE `omoccurdatasets` DISABLE KEYS */;
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
  `identifiedBy` varchar(255) NOT NULL DEFAULT '',
  `identifiedByAgentID` bigint(20) DEFAULT NULL,
  `identifiedByID` varchar(45) DEFAULT NULL,
  `dateIdentified` varchar(45) NOT NULL DEFAULT '',
  `dateIdentifiedInterpreted` date DEFAULT NULL,
  `higherClassification` varchar(150) DEFAULT NULL,
  `family` varchar(255) DEFAULT NULL,
  `sciname` varchar(255) NOT NULL,
  `verbatimIdentification` varchar(250) DEFAULT NULL,
  `scientificNameAuthorship` varchar(255) DEFAULT NULL,
  `tidInterpreted` int(10) unsigned DEFAULT NULL,
  `identificationQualifier` varchar(255) DEFAULT NULL,
  `genus` varchar(45) DEFAULT NULL,
  `specificEpithet` varchar(45) DEFAULT NULL,
  `verbatimTaxonRank` varchar(45) DEFAULT NULL,
  `taxonRank` varchar(45) DEFAULT NULL,
  `infraSpecificEpithet` varchar(45) DEFAULT NULL,
  `isCurrent` int(2) DEFAULT '0',
  `printQueue` int(2) DEFAULT '0',
  `appliedStatus` int(2) DEFAULT '1',
  `securityStatus` int(11) NOT NULL DEFAULT '0',
  `securityStatusReason` varchar(100) DEFAULT NULL,
  `detType` varchar(45) DEFAULT NULL,
  `identificationReferences` varchar(2000) DEFAULT NULL,
  `identificationRemarks` varchar(2000) DEFAULT NULL,
  `taxonRemarks` varchar(2000) DEFAULT NULL,
  `identificationVerificationStatus` varchar(45) DEFAULT NULL,
  `taxonConceptID` varchar(45) DEFAULT NULL,
  `identificationID` varchar(45) DEFAULT NULL,
  `sortSequence` int(10) unsigned DEFAULT '10',
  `recordID` varchar(45) DEFAULT NULL,
  `enteredByUid` int(10) unsigned DEFAULT NULL,
  `dateLastModified` timestamp NULL DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`detid`),
  UNIQUE KEY `UQ_omoccurdets_unique` (`occid`,`dateIdentified`,`identifiedBy`,`sciname`),
  KEY `FK_omoccurdets_tid` (`tidInterpreted`),
  KEY `IX_omoccurdets_dateIdInterpreted` (`dateIdentifiedInterpreted`),
  KEY `IX_omoccurdets_sciname` (`sciname`),
  KEY `IX_omoccurdets_family` (`family`),
  KEY `IX_omoccurdets_isCurrent` (`isCurrent`),
  KEY `FK_omoccurdets_agentID_idx` (`identifiedByAgentID`),
  KEY `FK_omoccurdets_uid_idx` (`enteredByUid`),
  KEY `IX_omoccurdets_recordID` (`recordID`),
  KEY `FK_omoccurdets_dateModified` (`dateLastModified`),
  KEY `FK_omoccurdets_initialTimestamp` (`initialTimestamp`),
  CONSTRAINT `FK_omoccurdets_agentID` FOREIGN KEY (`identifiedByAgentID`) REFERENCES `agents` (`agentID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurdets_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurdets_tid` FOREIGN KEY (`tidInterpreted`) REFERENCES `taxa` (`tid`),
  CONSTRAINT `FK_omoccurdets_uid` FOREIGN KEY (`enteredByUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `modifiedTimestamp` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`occid`,`duplicateid`),
  KEY `FK_omoccurdupelink_occid_idx` (`occid`),
  KEY `FK_omoccurdupelink_dupeid_idx` (`duplicateid`),
  CONSTRAINT `FK_omoccurdupelink_dupeid` FOREIGN KEY (`duplicateid`) REFERENCES `omoccurduplicates` (`duplicateid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurdupelink_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`occid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccureditlocks`
--

LOCK TABLES `omoccureditlocks` WRITE;
/*!40000 ALTER TABLE `omoccureditlocks` DISABLE KEYS */;
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
  `tableName` varchar(45) DEFAULT NULL,
  `fieldName` varchar(45) NOT NULL,
  `fieldValueNew` text NOT NULL,
  `fieldValueOld` text NOT NULL,
  `reviewStatus` int(1) NOT NULL DEFAULT '1' COMMENT '1=Open;2=Pending;3=Closed',
  `appliedStatus` int(1) NOT NULL DEFAULT '0' COMMENT '0=Not Applied;1=Applied',
  `editType` int(11) DEFAULT '0' COMMENT '0 = general edit, 1 = batch edit',
  `isActive` int(1) DEFAULT NULL COMMENT '0 = not the value applied within the active field, 1 = valued applied within active field',
  `reapply` int(1) DEFAULT NULL COMMENT '0 = do not reapply edit; 1 = reapply edit when snapshot is refreshed, if edit isActive and snapshot value still matches old value ',
  `guid` varchar(45) DEFAULT NULL,
  `uid` int(10) unsigned NOT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ocedid`),
  UNIQUE KEY `guid_UNIQUE` (`guid`),
  KEY `fk_omoccuredits_uid` (`uid`),
  KEY `fk_omoccuredits_occid` (`occid`),
  KEY `IX_omoccuredits_timestamp` (`initialTimestamp`),
  KEY `FK_omoccuredits_tableName` (`tableName`),
  KEY `FK_omoccuredits_fieldName` (`fieldName`),
  KEY `FK_omoccuredits_reviewedStatus` (`reviewStatus`),
  KEY `FK_omoccuredits_appliedStatus` (`appliedStatus`),
  CONSTRAINT `fk_omoccuredits_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_omoccuredits_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccuredits`
--

LOCK TABLES `omoccuredits` WRITE;
/*!40000 ALTER TABLE `omoccuredits` DISABLE KEYS */;
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
  CONSTRAINT `FK_occexch_coll` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`collID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idoccurgenetic`),
  UNIQUE KEY `UNIQUE_omoccurgenetic` (`occid`,`resourceurl`),
  KEY `FK_omoccurgenetic` (`occid`),
  KEY `INDEX_omoccurgenetic_name` (`resourcename`),
  CONSTRAINT `FK_omoccurgenetic` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tid`,`decimallatitude`,`decimallongitude`),
  CONSTRAINT `FK_specgeoindex_taxa` FOREIGN KEY (`tid`) REFERENCES `taxa` (`tid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `identifiername` varchar(45) NOT NULL DEFAULT '' COMMENT 'barcode, accession number, old catalog number, NPS, etc',
  `notes` varchar(250) DEFAULT NULL,
  `sortBy` int(11) DEFAULT NULL,
  `modifiedUid` int(10) unsigned NOT NULL,
  `modifiedTimestamp` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idomoccuridentifiers`),
  UNIQUE KEY `UQ_omoccuridentifiers` (`occid`,`identifiervalue`,`identifiername`),
  KEY `FK_omoccuridentifiers_occid_idx` (`occid`),
  KEY `IX_omoccuridentifiers_value` (`identifiervalue`),
  CONSTRAINT `FK_omoccuridentifiers_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccuridentifiers`
--

LOCK TABLES `omoccuridentifiers` WRITE;
/*!40000 ALTER TABLE `omoccuridentifiers` DISABLE KEYS */;
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
  CONSTRAINT `FK_occurloans_borrcoll` FOREIGN KEY (`collidBorr`) REFERENCES `omcollections` (`collID`) ON UPDATE CASCADE,
  CONSTRAINT `FK_occurloans_borrinst` FOREIGN KEY (`iidBorrower`) REFERENCES `institutions` (`iid`) ON UPDATE CASCADE,
  CONSTRAINT `FK_occurloans_owncoll` FOREIGN KEY (`collidOwn`) REFERENCES `omcollections` (`collID`) ON UPDATE CASCADE,
  CONSTRAINT `FK_occurloans_owninst` FOREIGN KEY (`iidOwner`) REFERENCES `institutions` (`iid`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccurloans`
--

LOCK TABLES `omoccurloans` WRITE;
/*!40000 ALTER TABLE `omoccurloans` DISABLE KEYS */;
/*!40000 ALTER TABLE `omoccurloans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omoccurloansattachment`
--

DROP TABLE IF EXISTS `omoccurloansattachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `omoccurloansattachment` (
  `attachmentid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `loanid` int(10) unsigned DEFAULT NULL,
  `exchangeid` int(10) unsigned DEFAULT NULL,
  `title` varchar(80) NOT NULL,
  `path` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`attachmentid`),
  KEY `FK_occurloansattachment_loanid_idx` (`loanid`),
  KEY `FK_occurloansattachment_exchangeid_idx` (`exchangeid`),
  CONSTRAINT `FK_occurloansattachment_exchangeid` FOREIGN KEY (`exchangeid`) REFERENCES `omoccurexchange` (`exchangeid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_occurloansattachment_loanid` FOREIGN KEY (`loanid`) REFERENCES `omoccurloans` (`loanid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccurloansattachment`
--

LOCK TABLES `omoccurloansattachment` WRITE;
/*!40000 ALTER TABLE `omoccurloansattachment` DISABLE KEYS */;
/*!40000 ALTER TABLE `omoccurloansattachment` ENABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `initialTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`paleoID`),
  UNIQUE KEY `UNIQUE_occid` (`occid`),
  KEY `FK_paleo_occid_idx` (`occid`),
  CONSTRAINT `FK_paleo_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `initialTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
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
INSERT INTO `omoccurpaleogts` VALUES (1,'Precambrian',10,'supereon',NULL,'2023-03-26 18:16:18'),(2,'Archean',20,'eon',1,'2023-03-26 18:16:18'),(3,'Hadean',20,'eon',1,'2023-03-26 18:16:18'),(4,'Phanerozoic',20,'eon',1,'2023-03-26 18:16:18'),(5,'Proterozoic',20,'eon',1,'2023-03-26 18:16:18'),(9,'Eoarchean',30,'era',2,'2023-03-26 18:16:18'),(10,'Paleoarchean',30,'era',2,'2023-03-26 18:16:18'),(11,'Mesoarchean',30,'era',2,'2023-03-26 18:16:18'),(12,'Neoarchean',30,'era',2,'2023-03-26 18:16:18'),(13,'Paleozoic',30,'era',4,'2023-03-26 18:16:18'),(14,'Mesozoic',30,'era',4,'2023-03-26 18:16:18'),(15,'Cenozoic',30,'era',4,'2023-03-26 18:16:18'),(16,'Paleoproterozoic',30,'era',5,'2023-03-26 18:16:18'),(17,'Mesoproterozoic',30,'era',5,'2023-03-26 18:16:18'),(18,'Neoproterozoic',30,'era',5,'2023-03-26 18:16:18'),(24,'Cambrian',40,'period',13,'2023-03-26 18:16:18'),(25,'Ordovician',40,'period',13,'2023-03-26 18:16:18'),(26,'Silurian',40,'period',13,'2023-03-26 18:16:18'),(27,'Devonian',40,'period',13,'2023-03-26 18:16:18'),(28,'Carboniferous',40,'period',13,'2023-03-26 18:16:18'),(29,'Permian',40,'period',13,'2023-03-26 18:16:18'),(30,'Triassic',40,'period',14,'2023-03-26 18:16:18'),(31,'Jurassic',40,'period',14,'2023-03-26 18:16:18'),(32,'Cretaceous',40,'period',14,'2023-03-26 18:16:18'),(33,'Paleogene',40,'period',15,'2023-03-26 18:16:18'),(34,'Neogene',40,'period',15,'2023-03-26 18:16:18'),(35,'Quaternary',40,'period',15,'2023-03-26 18:16:18'),(36,'Siderian',40,'period',16,'2023-03-26 18:16:18'),(37,'Rhyacian',40,'period',16,'2023-03-26 18:16:18'),(38,'Orosirian',40,'period',16,'2023-03-26 18:16:18'),(39,'Statherian',40,'period',16,'2023-03-26 18:16:18'),(40,'Calymmian',40,'period',17,'2023-03-26 18:16:18'),(41,'Ectasian',40,'period',17,'2023-03-26 18:16:18'),(42,'Stenian',40,'period',17,'2023-03-26 18:16:18'),(43,'Tonian',40,'period',18,'2023-03-26 18:16:18'),(44,'Gryogenian',40,'period',18,'2023-03-26 18:16:18'),(45,'Ediacaran',40,'period',18,'2023-03-26 18:16:18'),(55,'Lower Cambrian',50,'epoch',24,'2023-03-26 18:16:18'),(56,'Middle Cambrian',50,'epoch',24,'2023-03-26 18:16:18'),(57,'Upper Cambrian',50,'epoch',24,'2023-03-26 18:16:18'),(58,'Lower Ordovician',50,'epoch',25,'2023-03-26 18:16:18'),(59,'Middle Ordovician',50,'epoch',25,'2023-03-26 18:16:18'),(60,'Upper Ordivician',50,'epoch',25,'2023-03-26 18:16:18'),(61,'Llandovery',50,'epoch',26,'2023-03-26 18:16:18'),(62,'Wenlock',50,'epoch',26,'2023-03-26 18:16:18'),(63,'Ludlow',50,'epoch',26,'2023-03-26 18:16:18'),(64,'Pridoli',50,'epoch',26,'2023-03-26 18:16:18'),(65,'Lower Devonian',50,'epoch',27,'2023-03-26 18:16:18'),(66,'Middle Devonian',50,'epoch',27,'2023-03-26 18:16:18'),(67,'Upper Devonian',50,'epoch',27,'2023-03-26 18:16:18'),(68,'Mississippian',40,'period',13,'2023-03-26 18:16:18'),(69,'Pennsylvanian',40,'period',13,'2023-03-26 18:16:18'),(70,'Cisuralian',50,'epoch',29,'2023-03-26 18:16:18'),(71,'Guadalupian',50,'epoch',29,'2023-03-26 18:16:18'),(72,'Lopingian',50,'epoch',29,'2023-03-26 18:16:18'),(73,'Lower Triassic',50,'epoch',30,'2023-03-26 18:16:18'),(74,'Middle Triassic',50,'epoch',30,'2023-03-26 18:16:18'),(75,'Upper Triassic',50,'epoch',30,'2023-03-26 18:16:18'),(76,'Lower Jurassic',50,'epoch',31,'2023-03-26 18:16:18'),(77,'Middle Jurassic',50,'epoch',31,'2023-03-26 18:16:18'),(78,'Upper Jurassic',50,'epoch',31,'2023-03-26 18:16:18'),(79,'Lower Cretaceous',50,'epoch',32,'2023-03-26 18:16:18'),(80,'Upper Cretaceous',50,'epoch',32,'2023-03-26 18:16:18'),(81,'Paleocene',50,'epoch',33,'2023-03-26 18:16:18'),(82,'Eocene',50,'epoch',33,'2023-03-26 18:16:18'),(83,'Oligocene',50,'epoch',33,'2023-03-26 18:16:18'),(84,'Miocene',50,'epoch',34,'2023-03-26 18:16:18'),(85,'Pliocene',50,'epoch',34,'2023-03-26 18:16:18'),(86,'Pleistocene',50,'epoch',35,'2023-03-26 18:16:18'),(87,'Holocene',50,'epoch',35,'2023-03-26 18:16:18'),(118,'Tremadocian',60,'age',58,'2023-03-26 18:16:18'),(119,'Floian',60,'age',58,'2023-03-26 18:16:18'),(120,'Dapingian',60,'age',59,'2023-03-26 18:16:18'),(121,'Darriwilian',60,'age',59,'2023-03-26 18:16:18'),(122,'Sandbian',60,'age',60,'2023-03-26 18:16:18'),(123,'Katian',60,'age',60,'2023-03-26 18:16:18'),(124,'Hirnantian',60,'age',60,'2023-03-26 18:16:18'),(125,'Rhuddanian',60,'age',61,'2023-03-26 18:16:18'),(126,'Aeronian',60,'age',61,'2023-03-26 18:16:18'),(127,'Telychian',60,'age',61,'2023-03-26 18:16:18'),(128,'Sheinwoodian',60,'age',62,'2023-03-26 18:16:18'),(129,'Homerian',60,'age',62,'2023-03-26 18:16:18'),(130,'Gorstian',60,'age',63,'2023-03-26 18:16:18'),(131,'Ludfordian',60,'age',63,'2023-03-26 18:16:18'),(132,'Lochkovian',60,'age',65,'2023-03-26 18:16:18'),(133,'Pragian',60,'age',65,'2023-03-26 18:16:18'),(134,'Emsian',60,'age',65,'2023-03-26 18:16:18'),(135,'Eifelian',60,'age',66,'2023-03-26 18:16:18'),(136,'Givetian',60,'age',66,'2023-03-26 18:16:18'),(137,'Frasnian',60,'age',67,'2023-03-26 18:16:18'),(138,'Famennian',60,'age',67,'2023-03-26 18:16:18'),(139,'Lower Mississippian',60,'age',68,'2023-03-26 18:16:18'),(140,'Middle Mississippian',60,'age',68,'2023-03-26 18:16:18'),(141,'Upper Mississippian',60,'age',68,'2023-03-26 18:16:18'),(142,'Lower Pennsylvanian',60,'age',69,'2023-03-26 18:16:18'),(143,'Middle Pennsylvanian',60,'age',69,'2023-03-26 18:16:18'),(144,'Upper Pennsylvanian',60,'age',69,'2023-03-26 18:16:18'),(145,'Asselian',60,'age',70,'2023-03-26 18:16:18'),(146,'Sakmarian',60,'age',70,'2023-03-26 18:16:18'),(147,'Artinskian',60,'age',70,'2023-03-26 18:16:18'),(148,'Kungurian',60,'age',70,'2023-03-26 18:16:18'),(149,'Roadian',60,'age',71,'2023-03-26 18:16:18'),(150,'Wordian',60,'age',71,'2023-03-26 18:16:18'),(151,'Capitanian',60,'age',71,'2023-03-26 18:16:18'),(152,'Wuchiapingian',60,'age',72,'2023-03-26 18:16:18'),(153,'Changhsingian',60,'age',72,'2023-03-26 18:16:18'),(154,'Induan',60,'age',73,'2023-03-26 18:16:18'),(155,'Olenekian',60,'age',73,'2023-03-26 18:16:18'),(156,'Anisian',60,'age',74,'2023-03-26 18:16:18'),(157,'Ladinian',60,'age',74,'2023-03-26 18:16:18'),(158,'Carnian',60,'age',75,'2023-03-26 18:16:18'),(159,'Norian',60,'age',75,'2023-03-26 18:16:18'),(160,'Rhaetian',60,'age',75,'2023-03-26 18:16:18'),(161,'Hettangian',60,'age',76,'2023-03-26 18:16:18'),(162,'Sinemurian',60,'age',76,'2023-03-26 18:16:18'),(163,'Pliensbachian',60,'age',76,'2023-03-26 18:16:18'),(164,'Toarcian',60,'age',76,'2023-03-26 18:16:18'),(165,'Aalenian',60,'age',77,'2023-03-26 18:16:18'),(166,'Bajocian',60,'age',77,'2023-03-26 18:16:18'),(167,'Bathonian',60,'age',77,'2023-03-26 18:16:18'),(168,'Callovian',60,'age',77,'2023-03-26 18:16:18'),(169,'Oxfordian',60,'age',78,'2023-03-26 18:16:18'),(170,'Kimmeridgian',60,'age',78,'2023-03-26 18:16:18'),(171,'Tithonian',60,'age',78,'2023-03-26 18:16:18'),(172,'Berriasian',60,'age',79,'2023-03-26 18:16:18'),(173,'Valanginian',60,'age',79,'2023-03-26 18:16:18'),(174,'Hauterivian',60,'age',79,'2023-03-26 18:16:18'),(175,'Barremian',60,'age',79,'2023-03-26 18:16:18'),(176,'Aptian',60,'age',79,'2023-03-26 18:16:18'),(177,'Albian',60,'age',79,'2023-03-26 18:16:18'),(178,'Cenomanian',60,'age',80,'2023-03-26 18:16:18'),(179,'Turonian',60,'age',80,'2023-03-26 18:16:18'),(180,'Coniacian',60,'age',80,'2023-03-26 18:16:18'),(181,'Santonian',60,'age',80,'2023-03-26 18:16:18'),(182,'Campanian',60,'age',80,'2023-03-26 18:16:18'),(183,'Maastrichtian',60,'age',80,'2023-03-26 18:16:18'),(184,'Danian',60,'age',81,'2023-03-26 18:16:18'),(185,'Selandian',60,'age',81,'2023-03-26 18:16:18'),(186,'Thanetian',60,'age',81,'2023-03-26 18:16:18'),(187,'Ypresian',60,'age',82,'2023-03-26 18:16:18'),(188,'Lutetian',60,'age',82,'2023-03-26 18:16:18'),(189,'Bartonian',60,'age',82,'2023-03-26 18:16:18'),(190,'Priabonian',60,'age',82,'2023-03-26 18:16:18'),(191,'Rupelian',60,'age',83,'2023-03-26 18:16:18'),(192,'Chattian',60,'age',83,'2023-03-26 18:16:18'),(193,'Aquitanian',60,'age',84,'2023-03-26 18:16:18'),(194,'Burdigalian',60,'age',84,'2023-03-26 18:16:18'),(195,'Langhian',60,'age',84,'2023-03-26 18:16:18'),(196,'Serravallian',60,'age',84,'2023-03-26 18:16:18'),(197,'Tortonian',60,'age',84,'2023-03-26 18:16:18'),(198,'Messinian',60,'age',84,'2023-03-26 18:16:18'),(199,'Zanclean',60,'age',85,'2023-03-26 18:16:18'),(200,'Piacenzian',60,'age',85,'2023-03-26 18:16:18'),(201,'Gelasian',60,'age',86,'2023-03-26 18:16:18'),(202,'Calabrian',60,'age',86,'2023-03-26 18:16:18'),(203,'Middle Pleistocene',60,'age',86,'2023-03-26 18:16:18'),(204,'Upper Pleistocene',60,'age',86,'2023-03-26 18:16:18');
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`geoID`),
  UNIQUE KEY `occid` (`occid`),
  SPATIAL KEY `point` (`point`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccurpoints`
--

LOCK TABLES `omoccurpoints` WRITE;
/*!40000 ALTER TABLE `omoccurpoints` DISABLE KEYS */;
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
  `tidInterpreted` int(10) unsigned DEFAULT NULL,
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
  `fieldNumber` varchar(45) DEFAULT NULL,
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
  `genericColumn1` varchar(100) DEFAULT NULL,
  `genericColumn2` varchar(100) DEFAULT NULL,
  `modified` datetime DEFAULT NULL COMMENT 'DateLastModified',
  `language` varchar(20) DEFAULT NULL,
  `observerUid` int(10) unsigned DEFAULT NULL,
  `processingStatus` varchar(45) DEFAULT NULL,
  `recordEnteredBy` varchar(250) DEFAULT NULL,
  `duplicateQuantity` int(10) unsigned DEFAULT NULL,
  `labelProject` varchar(250) DEFAULT NULL,
  `dynamicFields` text,
  `recordID` varchar(45) DEFAULT NULL,
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
  KEY `FK_omoccurrences_tid` (`tidInterpreted`),
  KEY `FK_omoccurrences_uid` (`observerUid`),
  KEY `Index_municipality` (`municipality`),
  KEY `Index_collnum` (`recordNumber`),
  KEY `Index_catalognumber` (`catalogNumber`),
  KEY `Index_eventDate` (`eventDate`),
  KEY `Index_occurrences_procstatus` (`processingStatus`),
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
  KEY `IX_occurrences_lat` (`decimalLatitude`),
  KEY `IX_occurrences_lng` (`decimalLongitude`),
  KEY `IX_omoccurrences_recordID` (`recordID`),
  CONSTRAINT `FK_omoccurrences_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`collID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurrences_tid` FOREIGN KEY (`tidInterpreted`) REFERENCES `taxa` (`tid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurrences_uid` FOREIGN KEY (`observerUid`) REFERENCES `users` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccurrences`
--

LOCK TABLES `omoccurrences` WRITE;
/*!40000 ALTER TABLE `omoccurrences` DISABLE KEYS */;
/*!40000 ALTER TABLE `omoccurrences` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
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
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
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
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccurrencesfulltext`
--

LOCK TABLES `omoccurrencesfulltext` WRITE;
/*!40000 ALTER TABLE `omoccurrencesfulltext` DISABLE KEYS */;
/*!40000 ALTER TABLE `omoccurrencesfulltext` ENABLE KEYS */;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
  `initialTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ovsid`),
  UNIQUE KEY `UNIQUE_omoccurverification` (`occid`,`category`),
  KEY `FK_omoccurverification_occid_idx` (`occid`),
  KEY `FK_omoccurverification_uid_idx` (`uid`),
  CONSTRAINT `FK_omoccurverification_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_omoccurverification_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omoccurverification`
--

LOCK TABLES `omoccurverification` WRITE;
/*!40000 ALTER TABLE `omoccurverification` DISABLE KEYS */;
/*!40000 ALTER TABLE `omoccurverification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `portalindex`
--

DROP TABLE IF EXISTS `portalindex`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `portalindex` (
  `portalID` int(11) NOT NULL AUTO_INCREMENT,
  `portalName` varchar(150) NOT NULL,
  `acronym` varchar(45) DEFAULT NULL,
  `portalDescription` varchar(250) DEFAULT NULL,
  `urlRoot` varchar(150) NOT NULL,
  `securityKey` varchar(45) DEFAULT NULL,
  `symbiotaVersion` varchar(45) DEFAULT NULL,
  `guid` varchar(45) DEFAULT NULL,
  `manager` varchar(45) DEFAULT NULL,
  `managerEmail` varchar(45) DEFAULT NULL,
  `primaryLead` varchar(45) DEFAULT NULL,
  `primaryLeadEmail` varchar(45) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`portalID`),
  UNIQUE KEY `UQ_portalIndex_guid` (`guid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `portalindex`
--

LOCK TABLES `portalindex` WRITE;
/*!40000 ALTER TABLE `portalindex` DISABLE KEYS */;
/*!40000 ALTER TABLE `portalindex` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `portaloccurrences`
--

DROP TABLE IF EXISTS `portaloccurrences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `portaloccurrences` (
  `portalOccurrencesID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `occid` int(10) unsigned NOT NULL,
  `pubid` int(10) unsigned NOT NULL,
  `remoteOccid` int(11) DEFAULT NULL,
  `verification` int(11) NOT NULL DEFAULT '0',
  `refreshTimestamp` datetime NOT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`portalOccurrencesID`),
  UNIQUE KEY `UQ_portalOccur_occid_pubid` (`occid`,`pubid`),
  KEY `FK_portalOccur_occid_idx` (`occid`),
  KEY `FK_portalOccur_pubID_idx` (`pubid`),
  CONSTRAINT `FK_portalOccur_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_portalOccur_pubid` FOREIGN KEY (`pubid`) REFERENCES `portalpublications` (`pubid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `portaloccurrences`
--

LOCK TABLES `portaloccurrences` WRITE;
/*!40000 ALTER TABLE `portaloccurrences` DISABLE KEYS */;
/*!40000 ALTER TABLE `portaloccurrences` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `portalpublications`
--

DROP TABLE IF EXISTS `portalpublications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `portalpublications` (
  `pubid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pubTitle` varchar(45) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `guid` varchar(45) DEFAULT NULL,
  `collid` int(10) unsigned DEFAULT NULL,
  `portalID` int(11) NOT NULL,
  `direction` varchar(45) NOT NULL,
  `criteriaJson` text,
  `includeDeterminations` int(11) DEFAULT '1',
  `includeImages` int(11) DEFAULT '1',
  `autoUpdate` int(11) DEFAULT '1',
  `lastDateUpdate` datetime DEFAULT NULL,
  `updateInterval` int(11) DEFAULT NULL,
  `createdUid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pubid`),
  UNIQUE KEY `UQ_portalpub_guid` (`guid`),
  KEY `FK_portalpub_collid_idx` (`collid`),
  KEY `FK_portalpub_portalID_idx` (`portalID`),
  KEY `FK_portalpub_uid_idx` (`createdUid`),
  CONSTRAINT `FK_portalpub_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`collID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_portalpub_createdUid` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON UPDATE CASCADE,
  CONSTRAINT `FK_portalpub_portalID` FOREIGN KEY (`portalID`) REFERENCES `portalindex` (`portalID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `portalpublications`
--

LOCK TABLES `portalpublications` WRITE;
/*!40000 ALTER TABLE `portalpublications` DISABLE KEYS */;
/*!40000 ALTER TABLE `portalpublications` ENABLE KEYS */;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdbyid` int(11) NOT NULL,
  PRIMARY KEY (`refid`,`agentid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`refid`,`refauthid`),
  KEY `FK_refauthlink_refid_idx` (`refid`),
  KEY `FK_refauthlink_refauthid_idx` (`refauthid`),
  CONSTRAINT `FK_refauthlink_refauthid` FOREIGN KEY (`refauthid`) REFERENCES `referenceauthors` (`refauthorid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_refauthlink_refid` FOREIGN KEY (`refid`) REFERENCES `referenceobject` (`refid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `modifiedTimestamp` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`refauthorid`),
  KEY `INDEX_refauthlastname` (`lastname`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`refid`,`clid`),
  KEY `FK_refcheckllistlink_refid_idx` (`refid`),
  KEY `FK_refcheckllistlink_clid_idx` (`clid`),
  CONSTRAINT `FK_refchecklistlink_clid` FOREIGN KEY (`clid`) REFERENCES `fmchecklists` (`clid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_refchecklistlink_refid` FOREIGN KEY (`refid`) REFERENCES `referenceobject` (`refid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`refid`,`clid`,`tid`),
  KEY `FK_refchktaxalink_clidtid_idx` (`clid`,`tid`),
  CONSTRAINT `FK_refchktaxalink_clidtid` FOREIGN KEY (`clid`, `tid`) REFERENCES `fmchklsttaxalink` (`clid`, `tid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_refchktaxalink_ref` FOREIGN KEY (`refid`) REFERENCES `referenceobject` (`refid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`refid`,`collid`),
  KEY `FK_refcollectionlink_collid_idx` (`collid`),
  CONSTRAINT `FK_refcollectionlink_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`collID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_refcollectionlink_refid` FOREIGN KEY (`refid`) REFERENCES `referenceobject` (`refid`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  CONSTRAINT `FK_refdataset_datasetid` FOREIGN KEY (`datasetid`) REFERENCES `omoccurdatasets` (`datasetID`) ON DELETE CASCADE ON UPDATE CASCADE,
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
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `modifiedTimestamp` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`refid`),
  KEY `INDEX_refobj_title` (`title`),
  KEY `FK_refobj_parentrefid_idx` (`parentRefId`),
  KEY `FK_refobj_typeid_idx` (`ReferenceTypeId`),
  CONSTRAINT `FK_refobj_parentrefid` FOREIGN KEY (`parentRefId`) REFERENCES `referenceobject` (`refid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_refobj_reftypeid` FOREIGN KEY (`ReferenceTypeId`) REFERENCES `referencetype` (`ReferenceTypeId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`refid`,`occid`),
  KEY `FK_refoccurlink_refid_idx` (`refid`),
  KEY `FK_refoccurlink_occid_idx` (`occid`),
  CONSTRAINT `FK_refoccurlink_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_refoccurlink_refid` FOREIGN KEY (`refid`) REFERENCES `referenceobject` (`refid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`refid`,`tid`),
  KEY `FK_reftaxalink_refid_idx` (`refid`),
  KEY `FK_reftaxalink_tid_idx` (`tid`),
  CONSTRAINT `FK_reftaxalink_refid` FOREIGN KEY (`refid`) REFERENCES `referenceobject` (`refid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_reftaxalink_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`tid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `referencetype`
--

LOCK TABLES `referencetype` WRITE;
/*!40000 ALTER TABLE `referencetype` DISABLE KEYS */;
INSERT INTO `referencetype` VALUES (1,'Generic',NULL,'Title','SecondaryTitle','PlacePublished','Publisher','Volume','NumberVolumes','Number','Pages','Section','TertiaryTitle','Edition','Date','TypeWork','ShortTitle','AlternativeTitle','Isbn_Issn','Figures',NULL,'2014-06-17 07:27:12'),(2,'Journal Article',NULL,'Title','Periodical Title',NULL,NULL,'Volume',NULL,'Issue','Pages',NULL,NULL,NULL,'Date',NULL,'Short Title','Alt. Jour.',NULL,'Figures',NULL,'2014-06-17 07:27:12'),(3,'Book',1,'Title','Series Title','City','Publisher','Volume','No. Vols.','Number','Pages',NULL,NULL,'Edition','Date',NULL,'Short Title',NULL,'ISBN','Figures',NULL,'2014-06-17 07:27:12'),(4,'Book Section',NULL,'Title','Book Title','City','Publisher','Volume','No. Vols.','Number','Pages',NULL,'Ser. Title','Edition','Date',NULL,'Short Title',NULL,'ISBN','Figures',NULL,'2014-06-17 07:27:12'),(5,'Manuscript',NULL,'Title','Collection Title','City',NULL,NULL,NULL,'Number','Pages',NULL,NULL,'Edition','Date','Type Work','Short Title',NULL,NULL,'Figures',NULL,'2014-06-17 07:27:12'),(6,'Edited Book',1,'Title','Series Title','City','Publisher','Volume','No. Vols.','Number','Pages',NULL,NULL,'Edition','Date',NULL,'Short Title',NULL,'ISBN','Figures',NULL,'2014-06-17 07:27:12'),(7,'Magazine Article',NULL,'Title','Periodical Title',NULL,NULL,'Volume',NULL,'Issue','Pages',NULL,NULL,NULL,'Date',NULL,'Short Title',NULL,NULL,'Figures',NULL,'2014-06-17 07:27:12'),(8,'Newspaper Article',NULL,'Title','Periodical Title','City',NULL,NULL,NULL,NULL,'Pages','Section',NULL,'Edition','Date','Type Art.','Short Title',NULL,NULL,'Figures',NULL,'2014-06-17 07:27:12'),(9,'Conference Proceedings',NULL,'Title','Conf. Name','Conf. Loc.','Publisher','Volume','No. Vols.',NULL,'Pages',NULL,'Ser. Title','Edition','Date',NULL,'Short Title',NULL,'ISBN','Figures',NULL,'2014-06-17 07:27:12'),(10,'Thesis',NULL,'Title','Academic Dept.','City','University',NULL,NULL,NULL,'Pages',NULL,NULL,NULL,'Date','Thesis Type','Short Title',NULL,NULL,'Figures',NULL,'2014-06-17 07:27:12'),(11,'Report',NULL,'Title',NULL,'City','Institution',NULL,NULL,NULL,'Pages',NULL,NULL,NULL,'Date','Type Work','Short Title',NULL,'Rpt. No.','Figures',NULL,'2014-06-17 07:27:12'),(12,'Personal Communication',NULL,'Title',NULL,'City','Publisher',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Date','Type Work','Short Title',NULL,NULL,NULL,NULL,'2014-06-17 07:27:12'),(13,'Computer Program',NULL,'Title',NULL,'City','Publisher','Version',NULL,NULL,NULL,NULL,NULL,'Platform','Date','Type Work','Short Title',NULL,NULL,NULL,NULL,'2014-06-17 07:27:12'),(14,'Electronic Source',NULL,'Title',NULL,NULL,'Publisher','Access Year','Extent','Acc. Date',NULL,NULL,NULL,'Edition','Date','Medium','Short Title',NULL,NULL,NULL,NULL,'2014-06-17 07:27:12'),(15,'Audiovisual Material',NULL,'Title','Collection Title','City','Publisher',NULL,NULL,'Number',NULL,NULL,NULL,NULL,'Date','Type Work','Short Title',NULL,NULL,NULL,NULL,'2014-06-17 07:27:12'),(16,'Film or Broadcast',NULL,'Title','Series Title','City','Distributor',NULL,NULL,NULL,'Length',NULL,NULL,NULL,'Date','Medium','Short Title',NULL,'ISBN',NULL,NULL,'2014-06-17 07:27:12'),(17,'Artwork',NULL,'Title',NULL,'City','Publisher',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Date','Type Work','Short Title',NULL,NULL,NULL,NULL,'2014-06-17 07:27:12'),(18,'Map',NULL,'Title',NULL,'City','Publisher',NULL,NULL,NULL,'Scale',NULL,NULL,'Edition','Date','Type Work','Short Title',NULL,NULL,NULL,NULL,'2014-06-17 07:27:12'),(19,'Patent',NULL,'Title','Published Source','Country','Assignee','Volume','No. Vols.','Issue','Pages',NULL,NULL,NULL,'Date',NULL,'Short Title',NULL,'Pat. No.','Figures',NULL,'2014-06-17 07:27:12'),(20,'Hearing',NULL,'Title','Committee','City','Publisher',NULL,NULL,'Doc. No.','Pages',NULL,'Leg. Boby','Session','Date',NULL,'Short Title',NULL,NULL,NULL,NULL,'2014-06-17 07:27:12'),(21,'Bill',NULL,'Title','Code',NULL,NULL,'Code Volume',NULL,'Bill No.','Pages','Section','Leg. Boby','Session','Date',NULL,'Short Title',NULL,NULL,NULL,NULL,'2014-06-17 07:27:12'),(22,'Statute',NULL,'Title','Code',NULL,NULL,'Code Number',NULL,'Law No.','1st Pg.','Section',NULL,'Session','Date',NULL,'Short Title',NULL,NULL,NULL,NULL,'2014-06-17 07:27:12'),(23,'Case',NULL,'Title',NULL,NULL,'Court','Reporter Vol.',NULL,NULL,NULL,NULL,NULL,NULL,'Date',NULL,NULL,NULL,NULL,NULL,NULL,'2014-06-17 07:27:12'),(24,'Figure',NULL,'Title','Source Program',NULL,NULL,NULL,'-',NULL,NULL,NULL,NULL,NULL,'Date',NULL,NULL,NULL,NULL,NULL,NULL,'2014-06-17 07:27:12'),(25,'Chart or Table',NULL,'Title','Source Program',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Date',NULL,NULL,NULL,NULL,NULL,NULL,'2014-06-17 07:27:12'),(26,'Equation',NULL,'Title','Source Program',NULL,NULL,'Volume',NULL,'Number',NULL,NULL,NULL,NULL,'Date',NULL,NULL,NULL,NULL,NULL,NULL,'2014-06-17 07:27:12'),(27,'Book Series',1,'Title',NULL,'City','Publisher',NULL,'No. Vols.',NULL,'Pages',NULL,NULL,'Edition','Date',NULL,NULL,NULL,'ISBN','Figures',NULL,'2014-06-17 07:27:12'),(28,'Determination',NULL,'Title',NULL,NULL,'Institution',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Date',NULL,NULL,NULL,NULL,NULL,NULL,'2014-06-17 07:27:12'),(29,'Sub-Reference',NULL,'Title',NULL,NULL,NULL,NULL,NULL,NULL,'Pages',NULL,NULL,NULL,'Date',NULL,NULL,NULL,NULL,'Figures',NULL,'2014-06-17 07:27:12'),(30,'Periodical',1,'Title',NULL,'City',NULL,'Volume',NULL,'Issue',NULL,NULL,NULL,'Edition','Date',NULL,'Short Title','Alt. Jour.',NULL,NULL,NULL,'2014-10-31 04:34:44'),(31,'Web Page',NULL,'Title',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2014-10-31 04:37:12');
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
  `initialTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`swsid`),
  UNIQUE KEY `INDEX_unique` (`firstword`,`secondword`),
  KEY `INDEX_secondword` (`secondword`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schemaversion`
--

LOCK TABLES `schemaversion` WRITE;
/*!40000 ALTER TABLE `schemaversion` DISABLE KEYS */;
INSERT INTO `schemaversion` VALUES (1,'3.0','2023-12-07 16:10:52');
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
  `projectType` varchar(45) DEFAULT NULL,
  `specKeyPattern` varchar(45) DEFAULT NULL,
  `patternReplace` varchar(45) DEFAULT NULL,
  `replaceStr` varchar(45) DEFAULT NULL,
  `specKeyRetrieval` varchar(45) DEFAULT NULL,
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
  `jpgCompression` int(11) DEFAULT '70',
  `createTnImg` int(10) unsigned DEFAULT '1',
  `createLgImg` int(10) unsigned DEFAULT '1',
  `additionalOptions` text,
  `source` varchar(45) DEFAULT NULL,
  `processingCode` int(10) DEFAULT NULL,
  `lastRunDate` date DEFAULT NULL,
  `createdByUid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`spprid`),
  KEY `FK_specprocessorprojects_coll` (`collid`),
  KEY `FK_specprocprojects_uid_idx` (`createdByUid`),
  CONSTRAINT `FK_specprocessorprojects_coll` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`collID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_specprocprojects_uid` FOREIGN KEY (`createdByUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`prlid`),
  KEY `FK_specproc_images` (`imgid`),
  KEY `FK_specproc_occid` (`occid`),
  CONSTRAINT `FK_specproc_images` FOREIGN KEY (`imgid`) REFERENCES `images` (`imgid`) ON UPDATE CASCADE,
  CONSTRAINT `FK_specproc_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
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
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
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
/*!50003 SET character_set_client  = latin1 */ ;
/*!50003 SET character_set_results = latin1 */ ;
/*!50003 SET collation_connection  = latin1_swedish_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`spnlpid`),
  KEY `FK_specprocnlp_collid` (`collid`),
  CONSTRAINT `FK_specprocnlp_collid` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`collID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `initialTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`spnlpfragid`),
  KEY `FK_specprocnlpfrag_spnlpid` (`spnlpid`),
  CONSTRAINT `FK_specprocnlpfrag_spnlpid` FOREIGN KEY (`spnlpid`) REFERENCES `specprocnlp` (`spnlpid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `initialTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`nlpverid`),
  KEY `FK_specprocnlpver_rawtext_idx` (`prlid`),
  CONSTRAINT `FK_specprocnlpver_rawtext` FOREIGN KEY (`prlid`) REFERENCES `specprocessorrawlabels` (`prlid`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Archives field name - value pairs of NLP results loading into an omoccurrence record. This way, results can be easily redone at a later date without copying over date modifed afterward by another user or process ';
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
  `initialTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ocrfragid`),
  KEY `FK_specprococrfrag_prlid_idx` (`prlid`),
  KEY `Index_keyterm` (`keyterm`),
  CONSTRAINT `FK_specprococrfrag_prlid` FOREIGN KEY (`prlid`) REFERENCES `specprocessorrawlabels` (`prlid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `tid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kingdomName` varchar(45) DEFAULT '',
  `rankID` smallint(5) unsigned DEFAULT '0',
  `sciName` varchar(250) NOT NULL,
  `unitInd1` varchar(1) DEFAULT NULL,
  `unitName1` varchar(50) NOT NULL,
  `unitInd2` varchar(1) DEFAULT NULL,
  `unitName2` varchar(50) DEFAULT 't',
  `unitInd3` varchar(45) DEFAULT NULL,
  `unitName3` varchar(35) DEFAULT NULL,
  `author` varchar(150) NOT NULL DEFAULT '',
  `phylosortSequence` tinyint(3) unsigned DEFAULT NULL,
  `reviewStatus` int(11) DEFAULT NULL,
  `displayStatus` int(11) DEFAULT NULL,
  `isLegitimate` int(11) DEFAULT NULL,
  `nomenclaturalStatus` varchar(45) DEFAULT NULL,
  `nomenclaturalCode` varchar(45) DEFAULT NULL,
  `statusNotes` varchar(50) DEFAULT NULL,
  `source` varchar(250) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `hybrid` varchar(50) DEFAULT NULL,
  `securityStatus` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '0 = no security; 1 = hidden locality',
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `modifiedTimeStamp` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tid`),
  UNIQUE KEY `UQ_taxa_sciname` (`sciName`,`rankID`,`kingdomName`),
  KEY `rankid_index` (`rankID`),
  KEY `unitname1_index` (`unitName1`,`unitName2`) USING BTREE,
  KEY `FK_taxa_uid_idx` (`modifiedUid`),
  KEY `sciname_index` (`sciName`),
  KEY `idx_taxa_kingdomName` (`kingdomName`),
  KEY `idx_taxacreated` (`initialTimestamp`),
  CONSTRAINT `FK_taxa_uid` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taxa`
--

LOCK TABLES `taxa` WRITE;
/*!40000 ALTER TABLE `taxa` DISABLE KEYS */;
INSERT INTO `taxa` VALUES (1,NULL,1,'Organism',NULL,'Organism',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,'2023-03-26 18:14:32'),(2,NULL,10,'Monera',NULL,'Monera',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,'2023-03-26 18:14:32'),(3,NULL,10,'Protista',NULL,'Protista',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,'2023-03-26 18:14:32'),(4,NULL,10,'Plantae',NULL,'Plantae',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,'2023-03-26 18:14:32'),(5,NULL,10,'Fungi',NULL,'Fungi',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,'2023-03-26 18:14:32'),(6,NULL,10,'Animalia',NULL,'Animalia',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,'2023-03-26 18:14:32');
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
  `tdProfileID` int(10) unsigned NOT NULL,
  `tid` int(10) unsigned NOT NULL,
  `caption` varchar(40) DEFAULT NULL,
  `source` varchar(250) DEFAULT NULL,
  `sourceurl` varchar(250) DEFAULT NULL,
  `language` varchar(45) DEFAULT 'English',
  `langid` int(11) DEFAULT NULL,
  `displaylevel` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '1 = short descr, 2 = intermediate descr',
  `uid` int(10) unsigned NOT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tdbid`),
  KEY `FK_taxadesc_lang_idx` (`langid`),
  KEY `FK_taxadescrblock_tid_idx` (`tid`),
  KEY `FK_taxadescrblock_tdProfileID_idx` (`tdProfileID`),
  CONSTRAINT `FK_taxadesc_lang` FOREIGN KEY (`langid`) REFERENCES `adminlanguages` (`langid`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_taxadescrblock_tdProfileID` FOREIGN KEY (`tdProfileID`) REFERENCES `taxadescrprofile` (`tdProfileID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_taxadescrblock_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`tid`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taxadescrblock`
--

LOCK TABLES `taxadescrblock` WRITE;
/*!40000 ALTER TABLE `taxadescrblock` DISABLE KEYS */;
/*!40000 ALTER TABLE `taxadescrblock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `taxadescrprofile`
--

DROP TABLE IF EXISTS `taxadescrprofile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `taxadescrprofile` (
  `tdProfileID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `authors` varchar(100) DEFAULT NULL,
  `caption` varchar(40) NOT NULL,
  `projectDescription` varchar(500) DEFAULT NULL,
  `abstract` text,
  `publication` varchar(500) DEFAULT NULL,
  `urlTemplate` varchar(250) DEFAULT NULL,
  `internalNotes` varchar(250) DEFAULT NULL,
  `langid` int(11) DEFAULT '1',
  `defaultDisplayLevel` int(11) DEFAULT '1',
  `dynamicProperties` text,
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `modifiedTimestamp` timestamp NULL DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tdProfileID`),
  KEY `FK_taxadescrprofile_langid_idx` (`langid`),
  KEY `FK_taxadescrprofile_uid_idx` (`modifiedUid`),
  CONSTRAINT `FK_taxadescrprofile_langid` FOREIGN KEY (`langid`) REFERENCES `adminlanguages` (`langid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_taxadescrprofile_uid` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taxadescrprofile`
--

LOCK TABLES `taxadescrprofile` WRITE;
/*!40000 ALTER TABLE `taxadescrprofile` DISABLE KEYS */;
/*!40000 ALTER TABLE `taxadescrprofile` ENABLE KEYS */;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tdsid`),
  KEY `FK_taxadescrstmts_tblock` (`tdbid`),
  CONSTRAINT `FK_taxadescrstmts_tblock` FOREIGN KEY (`tdbid`) REFERENCES `taxadescrblock` (`tdbid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tid`,`taxauthid`,`parenttid`),
  KEY `FK_tet_taxa` (`tid`),
  KEY `FK_tet_taxauth` (`taxauthid`),
  KEY `FK_tet_taxa2` (`parenttid`),
  CONSTRAINT `FK_tet_taxa` FOREIGN KEY (`tid`) REFERENCES `taxa` (`tid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_tet_taxa2` FOREIGN KEY (`parenttid`) REFERENCES `taxa` (`tid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_tet_taxauth` FOREIGN KEY (`taxauthid`) REFERENCES `taxauthority` (`taxauthid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`tlid`),
  UNIQUE KEY `UQ_taxaLinks_tid_url` (`tid`,`url`),
  KEY `FK_taxaLinks_tid` (`tid`),
  CONSTRAINT `FK_taxalinks_taxa` FOREIGN KEY (`tid`) REFERENCES `taxa` (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`mid`),
  KEY `FK_tid_idx` (`tid`),
  CONSTRAINT `FK_taxamaps_taxa` FOREIGN KEY (`tid`) REFERENCES `taxa` (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taxamaps`
--

LOCK TABLES `taxamaps` WRITE;
/*!40000 ALTER TABLE `taxamaps` DISABLE KEYS */;
/*!40000 ALTER TABLE `taxamaps` ENABLE KEYS */;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`taxaresourceid`),
  UNIQUE KEY `UNIQUE_taxaresource` (`tid`,`sourcename`),
  KEY `taxaresource_name` (`sourcename`),
  KEY `FK_taxaresource_tid_idx` (`tid`),
  CONSTRAINT `FK_taxaresource_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`tid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`taxauthid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taxauthority`
--

LOCK TABLES `taxauthority` WRITE;
/*!40000 ALTER TABLE `taxauthority` DISABLE KEYS */;
INSERT INTO `taxauthority` VALUES (1,1,'Central Thesaurus',NULL,NULL,NULL,NULL,NULL,NULL,1,'2023-03-26 18:14:32');
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
  `sortSequence` int(10) DEFAULT '50',
  `VID` int(10) NOT NULL AUTO_INCREMENT,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`VID`),
  UNIQUE KEY `unique-key` (`VernacularName`,`TID`,`langid`),
  KEY `tid1` (`TID`),
  KEY `vernacularsnames` (`VernacularName`),
  KEY `FK_vern_lang_idx` (`langid`),
  CONSTRAINT `FK_vern_lang` FOREIGN KEY (`langid`) REFERENCES `adminlanguages` (`langid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_vernaculars_tid` FOREIGN KEY (`TID`) REFERENCES `taxa` (`tid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `modifiedTimestamp` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`taxonunitid`),
  UNIQUE KEY `UNIQUE_taxonunits` (`kingdomName`,`rankid`)
) ENGINE=InnoDB AUTO_INCREMENT=153 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taxonunits`
--

LOCK TABLES `taxonunits` WRITE;
/*!40000 ALTER TABLE `taxonunits` DISABLE KEYS */;
INSERT INTO `taxonunits` VALUES (24,'Organism',1,'Organism',NULL,1,1,NULL,NULL,'2023-03-26 18:14:32'),(25,'Organism',10,'Kingdom',NULL,1,1,NULL,NULL,'2023-03-26 18:14:32'),(26,'Organism',20,'Subkingdom',NULL,10,10,NULL,NULL,'2023-03-26 18:14:32'),(27,'Organism',30,'Division',NULL,20,10,NULL,NULL,'2023-03-26 18:14:32'),(28,'Organism',40,'Subdivision',NULL,30,30,NULL,NULL,'2023-03-26 18:14:32'),(29,'Organism',50,'Superclass',NULL,40,30,NULL,NULL,'2023-03-26 18:14:32'),(30,'Organism',60,'Class',NULL,50,30,NULL,NULL,'2023-03-26 18:14:32'),(31,'Organism',70,'Subclass',NULL,60,60,NULL,NULL,'2023-03-26 18:14:32'),(32,'Organism',100,'Order',NULL,70,60,NULL,NULL,'2023-03-26 18:14:32'),(33,'Organism',110,'Suborder',NULL,100,100,NULL,NULL,'2023-03-26 18:14:32'),(34,'Organism',140,'Family',NULL,110,100,NULL,NULL,'2023-03-26 18:14:32'),(35,'Organism',150,'Subfamily',NULL,140,140,NULL,NULL,'2023-03-26 18:14:32'),(36,'Organism',160,'Tribe',NULL,150,140,NULL,NULL,'2023-03-26 18:14:32'),(37,'Organism',170,'Subtribe',NULL,160,140,NULL,NULL,'2023-03-26 18:14:32'),(38,'Organism',180,'Genus',NULL,170,140,NULL,NULL,'2023-03-26 18:14:32'),(39,'Organism',190,'Subgenus',NULL,180,180,NULL,NULL,'2023-03-26 18:14:32'),(40,'Organism',200,'Section',NULL,190,180,NULL,NULL,'2023-03-26 18:14:32'),(41,'Organism',210,'Subsection',NULL,200,180,NULL,NULL,'2023-03-26 18:14:32'),(42,'Organism',220,'Species',NULL,210,180,NULL,NULL,'2023-03-26 18:14:32'),(43,'Organism',230,'Subspecies',NULL,220,180,NULL,NULL,'2023-03-26 18:14:32'),(44,'Organism',240,'Variety',NULL,220,180,NULL,NULL,'2023-03-26 18:14:32'),(45,'Organism',250,'Subvariety',NULL,240,180,NULL,NULL,'2023-03-26 18:14:32'),(46,'Organism',260,'Form',NULL,220,180,NULL,NULL,'2023-03-26 18:14:32'),(47,'Organism',270,'Subform',NULL,260,180,NULL,NULL,'2023-03-26 18:14:32'),(48,'Organism',300,'Cultivated',NULL,220,220,NULL,NULL,'2023-03-26 18:14:32'),(49,'Monera',1,'Organism',NULL,1,1,NULL,NULL,'2023-03-26 18:14:32'),(50,'Monera',10,'Kingdom',NULL,1,1,NULL,NULL,'2023-03-26 18:14:32'),(51,'Monera',20,'Subkingdom',NULL,10,10,NULL,NULL,'2023-03-26 18:14:32'),(52,'Monera',30,'Phylum',NULL,20,10,NULL,NULL,'2023-03-26 18:14:32'),(53,'Monera',40,'Subphylum',NULL,30,30,NULL,NULL,'2023-03-26 18:14:32'),(54,'Monera',60,'Class',NULL,50,30,NULL,NULL,'2023-03-26 18:14:32'),(55,'Monera',70,'Subclass',NULL,60,60,NULL,NULL,'2023-03-26 18:14:32'),(56,'Monera',100,'Order',NULL,70,60,NULL,NULL,'2023-03-26 18:14:32'),(57,'Monera',110,'Suborder',NULL,100,100,NULL,NULL,'2023-03-26 18:14:32'),(58,'Monera',140,'Family',NULL,110,100,NULL,NULL,'2023-03-26 18:14:32'),(59,'Monera',150,'Subfamily',NULL,140,140,NULL,NULL,'2023-03-26 18:14:32'),(60,'Monera',160,'Tribe',NULL,150,140,NULL,NULL,'2023-03-26 18:14:32'),(61,'Monera',170,'Subtribe',NULL,160,140,NULL,NULL,'2023-03-26 18:14:32'),(62,'Monera',180,'Genus',NULL,170,140,NULL,NULL,'2023-03-26 18:14:32'),(63,'Monera',190,'Subgenus',NULL,180,180,NULL,NULL,'2023-03-26 18:14:32'),(64,'Monera',220,'Species',NULL,210,180,NULL,NULL,'2023-03-26 18:14:32'),(65,'Monera',230,'Subspecies',NULL,220,180,NULL,NULL,'2023-03-26 18:14:32'),(66,'Monera',240,'Morph',NULL,220,180,NULL,NULL,'2023-03-26 18:14:32'),(67,'Protista',1,'Organism',NULL,1,1,NULL,NULL,'2023-03-26 18:14:32'),(68,'Protista',10,'Kingdom',NULL,1,1,NULL,NULL,'2023-03-26 18:14:32'),(69,'Protista',20,'Subkingdom',NULL,10,10,NULL,NULL,'2023-03-26 18:14:32'),(70,'Protista',30,'Phylum',NULL,20,10,NULL,NULL,'2023-03-26 18:14:32'),(71,'Protista',40,'Subphylum',NULL,30,30,NULL,NULL,'2023-03-26 18:14:32'),(72,'Protista',60,'Class',NULL,50,30,NULL,NULL,'2023-03-26 18:14:32'),(73,'Protista',70,'Subclass',NULL,60,60,NULL,NULL,'2023-03-26 18:14:32'),(74,'Protista',100,'Order',NULL,70,60,NULL,NULL,'2023-03-26 18:14:32'),(75,'Protista',110,'Suborder',NULL,100,100,NULL,NULL,'2023-03-26 18:14:32'),(76,'Protista',140,'Family',NULL,110,100,NULL,NULL,'2023-03-26 18:14:32'),(77,'Protista',150,'Subfamily',NULL,140,140,NULL,NULL,'2023-03-26 18:14:32'),(78,'Protista',160,'Tribe',NULL,150,140,NULL,NULL,'2023-03-26 18:14:32'),(79,'Protista',170,'Subtribe',NULL,160,140,NULL,NULL,'2023-03-26 18:14:32'),(80,'Protista',180,'Genus',NULL,170,140,NULL,NULL,'2023-03-26 18:14:32'),(81,'Protista',190,'Subgenus',NULL,180,180,NULL,NULL,'2023-03-26 18:14:32'),(82,'Protista',220,'Species',NULL,210,180,NULL,NULL,'2023-03-26 18:14:32'),(83,'Protista',230,'Subspecies',NULL,220,180,NULL,NULL,'2023-03-26 18:14:32'),(84,'Protista',240,'Morph',NULL,220,180,NULL,NULL,'2023-03-26 18:14:32'),(85,'Plantae',1,'Organism',NULL,1,1,NULL,NULL,'2023-03-26 18:14:32'),(86,'Plantae',10,'Kingdom',NULL,1,1,NULL,NULL,'2023-03-26 18:14:32'),(87,'Plantae',20,'Subkingdom',NULL,10,10,NULL,NULL,'2023-03-26 18:14:32'),(88,'Plantae',30,'Division',NULL,20,10,NULL,NULL,'2023-03-26 18:14:32'),(89,'Plantae',40,'Subdivision',NULL,30,30,NULL,NULL,'2023-03-26 18:14:32'),(90,'Plantae',50,'Superclass',NULL,40,30,NULL,NULL,'2023-03-26 18:14:32'),(91,'Plantae',60,'Class',NULL,50,30,NULL,NULL,'2023-03-26 18:14:32'),(92,'Plantae',70,'Subclass',NULL,60,60,NULL,NULL,'2023-03-26 18:14:32'),(93,'Plantae',100,'Order',NULL,70,60,NULL,NULL,'2023-03-26 18:14:32'),(94,'Plantae',110,'Suborder',NULL,100,100,NULL,NULL,'2023-03-26 18:14:32'),(95,'Plantae',140,'Family',NULL,110,100,NULL,NULL,'2023-03-26 18:14:32'),(96,'Plantae',150,'Subfamily',NULL,140,140,NULL,NULL,'2023-03-26 18:14:32'),(97,'Plantae',160,'Tribe',NULL,150,140,NULL,NULL,'2023-03-26 18:14:32'),(98,'Plantae',170,'Subtribe',NULL,160,140,NULL,NULL,'2023-03-26 18:14:32'),(99,'Plantae',180,'Genus',NULL,170,140,NULL,NULL,'2023-03-26 18:14:32'),(100,'Plantae',190,'Subgenus',NULL,180,180,NULL,NULL,'2023-03-26 18:14:32'),(101,'Plantae',200,'Section',NULL,190,180,NULL,NULL,'2023-03-26 18:14:32'),(102,'Plantae',210,'Subsection',NULL,200,180,NULL,NULL,'2023-03-26 18:14:32'),(103,'Plantae',220,'Species',NULL,210,180,NULL,NULL,'2023-03-26 18:14:32'),(104,'Plantae',230,'Subspecies',NULL,220,180,NULL,NULL,'2023-03-26 18:14:32'),(105,'Plantae',240,'Variety',NULL,220,180,NULL,NULL,'2023-03-26 18:14:32'),(106,'Plantae',250,'Subvariety',NULL,240,180,NULL,NULL,'2023-03-26 18:14:32'),(107,'Plantae',260,'Form',NULL,220,180,NULL,NULL,'2023-03-26 18:14:32'),(108,'Plantae',270,'Subform',NULL,260,180,NULL,NULL,'2023-03-26 18:14:32'),(109,'Plantae',300,'Cultivated',NULL,220,220,NULL,NULL,'2023-03-26 18:14:32'),(110,'Fungi',1,'Organism',NULL,1,1,NULL,NULL,'2023-03-26 18:14:32'),(111,'Fungi',10,'Kingdom',NULL,1,1,NULL,NULL,'2023-03-26 18:14:32'),(112,'Fungi',20,'Subkingdom',NULL,10,10,NULL,NULL,'2023-03-26 18:14:32'),(113,'Fungi',30,'Division',NULL,20,10,NULL,NULL,'2023-03-26 18:14:32'),(114,'Fungi',40,'Subdivision',NULL,30,30,NULL,NULL,'2023-03-26 18:14:32'),(115,'Fungi',50,'Superclass',NULL,40,30,NULL,NULL,'2023-03-26 18:14:32'),(116,'Fungi',60,'Class',NULL,50,30,NULL,NULL,'2023-03-26 18:14:32'),(117,'Fungi',70,'Subclass',NULL,60,60,NULL,NULL,'2023-03-26 18:14:32'),(118,'Fungi',100,'Order',NULL,70,60,NULL,NULL,'2023-03-26 18:14:32'),(119,'Fungi',110,'Suborder',NULL,100,100,NULL,NULL,'2023-03-26 18:14:32'),(120,'Fungi',140,'Family',NULL,110,100,NULL,NULL,'2023-03-26 18:14:32'),(121,'Fungi',150,'Subfamily',NULL,140,140,NULL,NULL,'2023-03-26 18:14:32'),(122,'Fungi',160,'Tribe',NULL,150,140,NULL,NULL,'2023-03-26 18:14:32'),(123,'Fungi',170,'Subtribe',NULL,160,140,NULL,NULL,'2023-03-26 18:14:32'),(124,'Fungi',180,'Genus',NULL,170,140,NULL,NULL,'2023-03-26 18:14:32'),(125,'Fungi',190,'Subgenus',NULL,180,180,NULL,NULL,'2023-03-26 18:14:32'),(126,'Fungi',200,'Section',NULL,190,180,NULL,NULL,'2023-03-26 18:14:32'),(127,'Fungi',210,'Subsection',NULL,200,180,NULL,NULL,'2023-03-26 18:14:32'),(128,'Fungi',220,'Species',NULL,210,180,NULL,NULL,'2023-03-26 18:14:32'),(129,'Fungi',230,'Subspecies',NULL,220,180,NULL,NULL,'2023-03-26 18:14:32'),(130,'Fungi',240,'Variety',NULL,220,180,NULL,NULL,'2023-03-26 18:14:32'),(131,'Fungi',250,'Subvariety',NULL,240,180,NULL,NULL,'2023-03-26 18:14:32'),(132,'Fungi',260,'Form',NULL,220,180,NULL,NULL,'2023-03-26 18:14:32'),(133,'Fungi',270,'Subform',NULL,260,180,NULL,NULL,'2023-03-26 18:14:32'),(134,'Fungi',300,'Cultivated',NULL,220,220,NULL,NULL,'2023-03-26 18:14:32'),(135,'Animalia',1,'Organism',NULL,1,1,NULL,NULL,'2023-03-26 18:14:32'),(136,'Animalia',10,'Kingdom',NULL,1,1,NULL,NULL,'2023-03-26 18:14:32'),(137,'Animalia',20,'Subkingdom',NULL,10,10,NULL,NULL,'2023-03-26 18:14:32'),(138,'Animalia',30,'Phylum',NULL,20,10,NULL,NULL,'2023-03-26 18:14:32'),(139,'Animalia',40,'Subphylum',NULL,30,30,NULL,NULL,'2023-03-26 18:14:32'),(140,'Animalia',60,'Class',NULL,50,30,NULL,NULL,'2023-03-26 18:14:32'),(141,'Animalia',70,'Subclass',NULL,60,60,NULL,NULL,'2023-03-26 18:14:32'),(142,'Animalia',100,'Order',NULL,70,60,NULL,NULL,'2023-03-26 18:14:32'),(143,'Animalia',110,'Suborder',NULL,100,100,NULL,NULL,'2023-03-26 18:14:32'),(144,'Animalia',140,'Family',NULL,110,100,NULL,NULL,'2023-03-26 18:14:32'),(145,'Animalia',150,'Subfamily',NULL,140,140,NULL,NULL,'2023-03-26 18:14:32'),(146,'Animalia',160,'Tribe',NULL,150,140,NULL,NULL,'2023-03-26 18:14:32'),(147,'Animalia',170,'Subtribe',NULL,160,140,NULL,NULL,'2023-03-26 18:14:32'),(148,'Animalia',180,'Genus',NULL,170,140,NULL,NULL,'2023-03-26 18:14:32'),(149,'Animalia',190,'Subgenus',NULL,180,180,NULL,NULL,'2023-03-26 18:14:32'),(150,'Animalia',220,'Species',NULL,210,180,NULL,NULL,'2023-03-26 18:14:32'),(151,'Animalia',230,'Subspecies',NULL,220,180,NULL,NULL,'2023-03-26 18:14:32'),(152,'Animalia',240,'Morph',NULL,220,180,NULL,NULL,'2023-03-26 18:14:32');
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
  `taxonomicSource` varchar(500) DEFAULT NULL,
  `sourceIdentifier` varchar(150) DEFAULT NULL,
  `UnacceptabilityReason` varchar(250) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `sortSequence` int(10) unsigned DEFAULT '50',
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `modifiedTimestamp` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`tid`,`tidaccepted`,`taxauthid`) USING BTREE,
  KEY `FK_taxstatus_tidacc` (`tidaccepted`),
  KEY `FK_taxstatus_taid` (`taxauthid`),
  KEY `Index_ts_family` (`family`),
  KEY `Index_parenttid` (`parenttid`),
  KEY `FK_taxstatus_uid_idx` (`modifiedUid`),
  KEY `Index_tid` (`tid`),
  CONSTRAINT `FK_taxstatus_parent` FOREIGN KEY (`parenttid`) REFERENCES `taxa` (`tid`),
  CONSTRAINT `FK_taxstatus_taid` FOREIGN KEY (`taxauthid`) REFERENCES `taxauthority` (`taxauthid`) ON UPDATE CASCADE,
  CONSTRAINT `FK_taxstatus_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`tid`),
  CONSTRAINT `FK_taxstatus_tidacc` FOREIGN KEY (`tidaccepted`) REFERENCES `taxa` (`tid`),
  CONSTRAINT `FK_taxstatus_uid` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taxstatus`
--

LOCK TABLES `taxstatus` WRITE;
/*!40000 ALTER TABLE `taxstatus` DISABLE KEYS */;
INSERT INTO `taxstatus` VALUES (1,1,1,1,NULL,NULL,NULL,NULL,NULL,NULL,50,NULL,NULL,'2023-03-26 18:14:32'),(2,2,1,1,NULL,NULL,NULL,NULL,NULL,NULL,50,NULL,NULL,'2023-03-26 18:14:32'),(3,3,1,1,NULL,NULL,NULL,NULL,NULL,NULL,50,NULL,NULL,'2023-03-26 18:14:32'),(4,4,1,1,NULL,NULL,NULL,NULL,NULL,NULL,50,NULL,NULL,'2023-03-26 18:14:32'),(5,5,1,1,NULL,NULL,NULL,NULL,NULL,NULL,50,NULL,NULL,'2023-03-26 18:14:32'),(6,6,1,1,NULL,NULL,NULL,NULL,NULL,NULL,50,NULL,NULL,'2023-03-26 18:14:32');
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
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `datelastmodified` datetime DEFAULT NULL,
  `createdUid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`stateid`,`occid`),
  KEY `FK_tmattr_stateid_idx` (`stateid`),
  KEY `FK_tmattr_occid_idx` (`occid`),
  KEY `FK_tmattr_imgid_idx` (`imgid`),
  KEY `FK_attr_uidcreate_idx` (`createdUid`),
  KEY `FK_tmattr_uidmodified_idx` (`modifiedUid`),
  CONSTRAINT `FK_tmattr_imgid` FOREIGN KEY (`imgid`) REFERENCES `images` (`imgid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_tmattr_occid` FOREIGN KEY (`occid`) REFERENCES `omoccurrences` (`occid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_tmattr_stateid` FOREIGN KEY (`stateid`) REFERENCES `tmstates` (`stateid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_tmattr_uidcreate` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_tmattr_uidmodified` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
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
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `datelastmodified` datetime DEFAULT NULL,
  `createdUid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`stateid`),
  UNIQUE KEY `traitid_code_UNIQUE` (`traitid`,`statecode`),
  KEY `FK_tmstate_uidcreated_idx` (`createdUid`),
  KEY `FK_tmstate_uidmodified_idx` (`modifiedUid`),
  CONSTRAINT `FK_tmstates_traits` FOREIGN KEY (`traitid`) REFERENCES `tmtraits` (`traitid`) ON UPDATE CASCADE,
  CONSTRAINT `FK_tmstates_uidcreated` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_tmstates_uidmodified` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
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
  `initialTimestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
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
  `modifiedUid` int(10) unsigned DEFAULT NULL,
  `datelastmodified` datetime DEFAULT NULL,
  `createdUid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`traitid`),
  KEY `traitsname` (`traitname`),
  KEY `FK_traits_uidcreated_idx` (`createdUid`),
  KEY `FK_traits_uidmodified_idx` (`modifiedUid`),
  CONSTRAINT `FK_traits_uidcreated` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_traits_uidmodified` FOREIGN KEY (`modifiedUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`traitid`,`tid`),
  KEY `FK_traittaxalink_traitid_idx` (`traitid`),
  KEY `FK_traittaxalink_tid_idx` (`tid`),
  CONSTRAINT `FK_traittaxalink_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`tid`) ON DELETE CASCADE ON UPDATE CASCADE,
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
-- Table structure for table `uploaddetermtemp`
--

DROP TABLE IF EXISTS `uploaddetermtemp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uploaddetermtemp` (
  `occid` int(10) unsigned DEFAULT NULL,
  `collid` int(10) unsigned DEFAULT NULL,
  `dbpk` varchar(150) DEFAULT NULL,
  `identifiedBy` varchar(255) NOT NULL DEFAULT '',
  `dateIdentified` varchar(45) NOT NULL DEFAULT '',
  `dateIdentifiedInterpreted` date DEFAULT NULL,
  `higherClassification` varchar(150) DEFAULT NULL,
  `sciname` varchar(255) NOT NULL,
  `verbatimIdentification` varchar(250) DEFAULT NULL,
  `scientificNameAuthorship` varchar(100) DEFAULT NULL,
  `identificationQualifier` varchar(45) DEFAULT NULL,
  `family` varchar(255) DEFAULT NULL,
  `genus` varchar(45) DEFAULT NULL,
  `specificEpithet` varchar(45) DEFAULT NULL,
  `verbatimTaxonRank` varchar(45) DEFAULT NULL,
  `taxonRank` varchar(45) DEFAULT NULL,
  `infraSpecificEpithet` varchar(45) DEFAULT NULL,
  `iscurrent` int(2) DEFAULT '0',
  `detType` varchar(45) DEFAULT NULL,
  `identificationReferences` varchar(255) DEFAULT NULL,
  `identificationRemarks` varchar(255) DEFAULT NULL,
  `taxonRemarks` varchar(2000) DEFAULT NULL,
  `identificationVerificationStatus` varchar(45) DEFAULT NULL,
  `taxonConceptID` varchar(45) DEFAULT NULL,
  `sourceIdentifier` varchar(45) DEFAULT NULL,
  `sortsequence` int(10) unsigned DEFAULT '10',
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `Index_uploaddet_occid` (`occid`),
  KEY `Index_uploaddet_collid` (`collid`),
  KEY `Index_uploaddet_dbpk` (`dbpk`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `Index_uploadimg_occid` (`occid`),
  KEY `Index_uploadimg_collid` (`collid`),
  KEY `Index_uploadimg_dbpk` (`dbpk`),
  KEY `Index_uploadimg_ts` (`initialTimestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`usmid`),
  UNIQUE KEY `Index_unique` (`uspid`,`symbspecfield`,`sourcefield`),
  CONSTRAINT `FK_uploadspecmap_usp` FOREIGN KEY (`uspid`) REFERENCES `uploadspecparameters` (`uspid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `collid` int(10) unsigned NOT NULL,
  `uploadType` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '1 = Direct; 2 = DiGIR; 3 = File',
  `title` varchar(45) NOT NULL,
  `platform` varchar(45) DEFAULT '1' COMMENT '1 = MySQL; 2 = MSSQL; 3 = ORACLE; 11 = MS Access; 12 = FileMaker',
  `server` varchar(150) DEFAULT NULL,
  `port` int(10) unsigned DEFAULT NULL,
  `driver` varchar(45) DEFAULT NULL,
  `code` varchar(45) DEFAULT NULL,
  `path` varchar(500) DEFAULT NULL,
  `pkField` varchar(45) DEFAULT NULL,
  `username` varchar(45) DEFAULT NULL,
  `password` varchar(45) DEFAULT NULL,
  `schemaName` varchar(150) DEFAULT NULL,
  `internalQuery` varchar(250) DEFAULT NULL,
  `queryStr` text,
  `cleanupSP` varchar(45) DEFAULT NULL,
  `endpointPublic` int(11) DEFAULT NULL,
  `dlmisvalid` int(10) unsigned DEFAULT '0',
  `createdUid` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`uspid`),
  KEY `FK_uploadspecparameters_coll` (`collid`),
  KEY `FK_uploadspecparameters_uid_idx` (`createdUid`),
  CONSTRAINT `FK_uploadspecparameters_coll` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`collID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_uploadspecparameters_uid` FOREIGN KEY (`createdUid`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `taxonRemarks` varchar(2000) DEFAULT NULL,
  `identifiedBy` varchar(255) DEFAULT NULL,
  `dateIdentified` varchar(45) DEFAULT NULL,
  `identificationReferences` varchar(2000) DEFAULT NULL,
  `identificationRemarks` varchar(2000) DEFAULT NULL,
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
  `host` varchar(250) DEFAULT NULL,
  `fieldNotes` text,
  `fieldnumber` varchar(45) DEFAULT NULL,
  `eventID` varchar(45) DEFAULT NULL,
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
  `establishmentMeans` varchar(150) DEFAULT NULL,
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
  `disposition` varchar(250) DEFAULT NULL,
  `storageLocation` varchar(100) DEFAULT NULL,
  `genericcolumn1` varchar(100) DEFAULT NULL,
  `genericcolumn2` varchar(100) DEFAULT NULL,
  `exsiccatiIdentifier` int(11) DEFAULT NULL,
  `exsiccatiNumber` varchar(45) DEFAULT NULL,
  `exsiccatiNotes` varchar(250) DEFAULT NULL,
  `paleoJSON` text,
  `materialSampleJSON` text,
  `modified` datetime DEFAULT NULL COMMENT 'DateLastModified',
  `language` varchar(20) DEFAULT NULL,
  `observeruid` int(11) DEFAULT NULL,
  `recordEnteredBy` varchar(250) DEFAULT NULL,
  `dateEntered` datetime DEFAULT NULL,
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
  CONSTRAINT `FK_uploadspectemp_coll` FOREIGN KEY (`collid`) REFERENCES `omcollections` (`collID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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
  `tokenID` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `token` varchar(50) NOT NULL,
  `device` varchar(50) DEFAULT NULL,
  `experationDate` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tokenID`),
  KEY `FK_useraccesstokens_uid_idx` (`uid`),
  CONSTRAINT `FK_useraccess_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `useraccesstokens`
--

LOCK TABLES `useraccesstokens` WRITE;
/*!40000 ALTER TABLE `useraccesstokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `useraccesstokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `userroles`
--

DROP TABLE IF EXISTS `userroles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `userroles` (
  `userRoleID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `role` varchar(45) NOT NULL,
  `tableName` varchar(45) DEFAULT NULL,
  `tablePK` int(11) DEFAULT NULL,
  `secondaryVariable` varchar(45) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `uidAssignedBy` int(10) unsigned DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`userRoleID`),
  UNIQUE KEY `Unique_userroles` (`uid`,`role`,`tableName`,`tablePK`),
  KEY `FK_userroles_uid_idx` (`uid`),
  KEY `FK_usrroles_uid2_idx` (`uidAssignedBy`),
  KEY `Index_userroles_table` (`tableName`,`tablePK`),
  CONSTRAINT `FK_userrole_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_userrole_uid_assigned` FOREIGN KEY (`uidAssignedBy`) REFERENCES `users` (`uid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `userroles`
--

LOCK TABLES `userroles` WRITE;
/*!40000 ALTER TABLE `userroles` DISABLE KEYS */;
INSERT INTO `userroles` VALUES (2,1,'SuperAdmin',NULL,NULL,NULL,NULL,NULL,'2023-03-26 18:14:32');
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
  `firstName` varchar(45) DEFAULT NULL,
  `lastName` varchar(45) NOT NULL,
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
  `regionOfInterest` varchar(45) DEFAULT NULL,
  `url` varchar(400) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `rightsHolder` varchar(250) DEFAULT NULL,
  `rights` varchar(250) DEFAULT NULL,
  `accessrRights` varchar(250) DEFAULT NULL,
  `guid` varchar(45) DEFAULT NULL,
  `username` varchar(45) DEFAULT NULL,
  `password` varchar(45) DEFAULT NULL,
  `lastLoginDate` datetime DEFAULT NULL,
  `loginModified` datetime DEFAULT NULL,
  `validated` int(11) NOT NULL DEFAULT '0',
  `dynamicProperties` text,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `IX_users_email` (`email`,`lastName`) USING BTREE,
  UNIQUE KEY `UQ_users_username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'General','Administrator',NULL,NULL,NULL,NULL,NULL,'NA',NULL,'NA',NULL,'NA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'admin','*4ACFE3202A5FF5CF467898FC58AAB1D615029441',NULL,NULL,0,NULL,'2023-03-26 18:14:32');
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
  `editorStatus` varchar(45) DEFAULT NULL,
  `geographicScope` varchar(250) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `modifiedUid` int(10) unsigned NOT NULL,
  `modifiedTimestamp` datetime DEFAULT NULL,
  `initialTimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idusertaxonomy`),
  UNIQUE KEY `usertaxonomy_UNIQUE` (`uid`,`tid`,`taxauthid`,`editorStatus`),
  KEY `FK_usertaxonomy_uid_idx` (`uid`),
  KEY `FK_usertaxonomy_tid_idx` (`tid`),
  KEY `FK_usertaxonomy_taxauthid_idx` (`taxauthid`),
  CONSTRAINT `FK_usertaxonomy_taxauthid` FOREIGN KEY (`taxauthid`) REFERENCES `taxauthority` (`taxauthid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_usertaxonomy_tid` FOREIGN KEY (`tid`) REFERENCES `taxa` (`tid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_usertaxonomy_uid` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
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

-- Dump completed on 2023-12-07 16:47:15
