
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
DROP TABLE IF EXISTS `gAccess`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gAccess` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dateadded` datetime DEFAULT NULL,
  `addedby` varchar(255) DEFAULT 'david',
  `ip` varchar(50) DEFAULT NULL,
  `granted` int(11) DEFAULT '0',
  `dateexp` datetime DEFAULT NULL,
  `granted_bhangra` int(11) DEFAULT '0',
  `granted_polka` int(11) DEFAULT '0',
  `granted_metal` int(11) DEFAULT '0',
  `userid` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `granted_beebop` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `gAnimal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gAnimal` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `animal` varchar(50) DEFAULT NULL,
  `cellprefix` varchar(50) DEFAULT NULL,
  `notes` text,
  `addedby` varchar(50) DEFAULT NULL,
  `info` varchar(255) DEFAULT NULL,
  `lastmod` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `birthday` date DEFAULT NULL,
  `caretaker` varchar(50) DEFAULT NULL,
  `pullweight` int(11) DEFAULT NULL,
  `eartag` varchar(50) DEFAULT NULL,
  `arrivalweight` int(11) DEFAULT NULL,
  `poleweight` int(11) DEFAULT NULL,
  `medical` text,
  `sex` varchar(10) DEFAULT 'f',
  `onschedule` int(11) DEFAULT '0',
  `retired` int(11) DEFAULT '0',
  `photourl` varchar(255) DEFAULT NULL,
  `implanted` int(11) DEFAULT '0',
  `species` varchar(50) DEFAULT NULL,
  `tattoo` varchar(50) DEFAULT '',
  `current_task` varchar(255) DEFAULT NULL,
  `lab` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `speciesidx` (`species`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `gCalendar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gCalendar` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `calname` varchar(255) DEFAULT NULL,
  `userid` varchar(255) DEFAULT NULL,
  `caldate` datetime DEFAULT NULL,
  `note` text,
  `dateadded` datetime DEFAULT NULL,
  `addedby` varchar(255) DEFAULT 'david',
  `complete` int(11) DEFAULT '0',
  `emailed` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `calidx` (`calname`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `gCellMaster`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gCellMaster` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `siteid` varchar(15) NOT NULL DEFAULT '',
  `cellid` varchar(15) NOT NULL DEFAULT '',
  `penid` int(11) NOT NULL DEFAULT '0',
  `penname` varchar(50) DEFAULT NULL,
  `animal` varchar(15) DEFAULT NULL,
  `well` int(11) DEFAULT '0',
  `training` int(11) DEFAULT '0',
  `depth` varchar(255) DEFAULT NULL,
  `umperdepth` double DEFAULT '1',
  `findtime` varchar(50) DEFAULT NULL,
  `polarity` varchar(10) DEFAULT NULL,
  `handplot` text,
  `comments` text,
  `crap` int(11) DEFAULT '0',
  `descentnotes` text,
  `area` varchar(255) DEFAULT NULL,
  `rfppd` double DEFAULT '0',
  `rfsource` varchar(255) DEFAULT NULL,
  `rfsize` int(11) DEFAULT '0',
  `xoffset` int(11) DEFAULT '0',
  `yoffset` int(11) DEFAULT '0',
  `eyecal` varchar(255) DEFAULT NULL,
  `quality` int(11) DEFAULT '0',
  `latency` int(11) DEFAULT '0',
  `addedby` varchar(50) DEFAULT NULL,
  `info` varchar(255) DEFAULT NULL,
  `lastmod` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sigma` varchar(255) DEFAULT NULL,
  `bf` varchar(255) DEFAULT NULL,
  `tuningstring` text,
  PRIMARY KEY (`id`),
  KEY `cellididx` (`cellid`),
  KEY `penidx` (`penid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `gData`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gData` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `siteid` varchar(15) NOT NULL DEFAULT '',
  `masterid` int(11) NOT NULL DEFAULT '0',
  `penid` int(11) NOT NULL DEFAULT '0',
  `rawid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `value` double DEFAULT '0',
  `svalue` varchar(255) DEFAULT NULL,
  `datatype` int(11) DEFAULT '0',
  `parmtype` int(11) DEFAULT '0',
  `addedby` varchar(50) DEFAULT NULL,
  `info` varchar(255) DEFAULT NULL,
  `lastmod` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `nameidx` (`name`),
  KEY `masteridx` (`masterid`),
  KEY `rawidx` (`rawid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `gDataRaw`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gDataRaw` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cellid` varchar(15) NOT NULL DEFAULT '',
  `masterid` int(11) NOT NULL DEFAULT '0',
  `runclassid` int(11) NOT NULL DEFAULT '0',
  `runclass` varchar(255) DEFAULT NULL,
  `stimspeedid` double DEFAULT NULL,
  `task` varchar(50) DEFAULT NULL,
  `training` int(11) DEFAULT '0',
  `bad` int(11) DEFAULT '0',
  `parmfile` varchar(255) DEFAULT NULL,
  `respfileevp` varchar(255) DEFAULT NULL,
  `respfileraw` varchar(255) DEFAULT NULL,
  `respfile` varchar(255) DEFAULT NULL,
  `reps` int(11) DEFAULT NULL,
  `stimpath` varchar(255) DEFAULT NULL,
  `stimfile` varchar(255) DEFAULT NULL,
  `timejuice` double DEFAULT NULL,
  `comments` text,
  `corrtrials` int(11) DEFAULT NULL,
  `trials` int(11) DEFAULT NULL,
  `resppath` varchar(255) DEFAULT NULL,
  `matlabfile` varchar(255) DEFAULT NULL,
  `eyecalfile` varchar(255) DEFAULT NULL,
  `plexonfile` varchar(255) DEFAULT NULL,
  `maxrate` int(11) DEFAULT NULL,
  `fixtime` int(11) DEFAULT NULL,
  `seclength` int(11) DEFAULT NULL,
  `time` varchar(50) DEFAULT NULL,
  `isolation` int(11) DEFAULT NULL,
  `snr` double DEFAULT NULL,
  `syncpulse` int(11) DEFAULT NULL,
  `monitorfreq` double DEFAULT NULL,
  `stimconf` int(11) DEFAULT NULL,
  `healthy` int(11) DEFAULT NULL,
  `eyewin` double DEFAULT NULL,
  `addedby` varchar(50) DEFAULT NULL,
  `info` varchar(255) DEFAULT NULL,
  `lastmod` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `behavior` varchar(50) DEFAULT NULL,
  `stimclass` varchar(255) DEFAULT NULL,
  `parameters` text,
  PRIMARY KEY (`id`),
  KEY `masterididx` (`masterid`),
  KEY `cellididx` (`cellid`),
  KEY `runclassididx` (`runclassid`,`stimspeedid`),
  KEY `behavioridx` (`behavior`,`task`),
  KEY `stimclassididx` (`stimclass`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `gHealth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gHealth` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `animal_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `water` double(13,6) DEFAULT NULL,
  `weight` double(13,6) DEFAULT NULL,
  `wetfood` int(11) DEFAULT NULL,
  `trained` int(11) DEFAULT NULL,
  `schedule` int(11) DEFAULT NULL,
  `timeonoroff` time DEFAULT NULL,
  `notes` text,
  `addedby` varchar(50) DEFAULT NULL,
  `info` varchar(255) DEFAULT NULL,
  `lastmod` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `animal` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `animalidx` (`animal_id`),
  KEY `dateidx` (`date`),
  KEY `animalidx2` (`animal`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `gPenetration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gPenetration` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `penname` varchar(50) NOT NULL DEFAULT '',
  `animal` varchar(15) DEFAULT NULL,
  `well` int(11) DEFAULT '0',
  `pendate` varchar(50) DEFAULT NULL,
  `who` varchar(50) DEFAULT NULL,
  `fixtime` varchar(50) DEFAULT NULL,
  `water` double DEFAULT NULL,
  `weight` double DEFAULT NULL,
  `ear` varchar(10) DEFAULT NULL,
  `numchans` int(11) DEFAULT NULL,
  `racknotes` text,
  `speakernotes` text,
  `probenotes` text,
  `electrodenotes` text,
  `crap` int(11) DEFAULT NULL,
  `training` int(11) DEFAULT '0',
  `impedance` text,
  `impedancenotes` text,
  `stability` int(11) DEFAULT NULL,
  `stabilitynotes` text,
  `eye` varchar(10) DEFAULT NULL,
  `mondist` double DEFAULT NULL,
  `etudeg` double DEFAULT NULL,
  `descentnotes` text,
  `addedby` varchar(50) DEFAULT NULL,
  `info` varchar(255) DEFAULT NULL,
  `lastmod` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `rackid` int(11) DEFAULT '0',
  `wellimfile` varchar(255) DEFAULT NULL,
  `wellx` int(11) DEFAULT NULL,
  `welly` int(11) DEFAULT NULL,
  `wellfirstspike` int(11) DEFAULT NULL,
  `wellposition` text,
  `firstdepth` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `gRunClass`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gRunClass` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  `details` varchar(255) DEFAULT NULL,
  `addedby` varchar(50) DEFAULT NULL,
  `info` varchar(255) DEFAULT NULL,
  `lastmod` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `stimclass` varchar(255) DEFAULT NULL,
  `task` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `nameidx` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `gSingleCell`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gSingleCell` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `siteid` varchar(15) NOT NULL DEFAULT '',
  `cellid` varchar(15) NOT NULL DEFAULT '',
  `masterid` int(11) NOT NULL DEFAULT '0',
  `penid` int(11) NOT NULL DEFAULT '0',
  `rawid` int(11) NOT NULL DEFAULT '0',
  `channel` char(2) DEFAULT NULL,
  `unit` int(11) DEFAULT NULL,
  `area` varchar(50) DEFAULT NULL,
  `handplot` text,
  `quality` int(11) DEFAULT '0',
  `crap` int(11) DEFAULT '0',
  `latency` int(11) DEFAULT NULL,
  `bf` int(11) DEFAULT NULL,
  `bw` double DEFAULT NULL,
  `rfsource` varchar(255) DEFAULT NULL,
  `rfsize` int(11) DEFAULT '0',
  `xoffset` int(11) DEFAULT '0',
  `yoffset` int(11) DEFAULT '0',
  `addedby` varchar(50) DEFAULT NULL,
  `info` varchar(255) DEFAULT NULL,
  `lastmod` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `channum` int(11) DEFAULT '1',
  `duration` int(11) DEFAULT NULL,
  `tuningstring` text,
  PRIMARY KEY (`id`),
  KEY `cellididx` (`cellid`),
  KEY `masteridx` (`masterid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `gSingleRaw`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gSingleRaw` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cellid` varchar(15) NOT NULL DEFAULT '',
  `masterid` int(11) NOT NULL DEFAULT '0',
  `singleid` int(11) NOT NULL DEFAULT '0',
  `penid` int(11) NOT NULL DEFAULT '0',
  `rawid` int(11) NOT NULL DEFAULT '0',
  `channel` char(2) DEFAULT NULL,
  `unit` int(11) DEFAULT NULL,
  `crap` int(11) DEFAULT '0',
  `isolation` double DEFAULT NULL,
  `addedby` varchar(50) DEFAULT NULL,
  `info` varchar(255) DEFAULT NULL,
  `lastmod` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `channum` int(11) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `singleidx` (`singleid`),
  KEY `cellididx` (`cellid`),
  KEY `masteridx` (`masterid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `gUserPrefs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gUserPrefs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `lastanimal` varchar(50) DEFAULT NULL,
  `lastwell` int(11) DEFAULT NULL,
  `lastpen` varchar(50) DEFAULT NULL,
  `dataroot` varchar(255) DEFAULT NULL,
  `seclevel` int(11) DEFAULT '0',
  `email` varchar(255) DEFAULT NULL,
  `lab` varchar(255) DEFAULT 'jlg',
  `lastallowqueuemaster` int(11) DEFAULT '1',
  `lastmachinesort` varchar(255) DEFAULT 'tComputer.load1',
  `lastjobcomplete` int(11) DEFAULT '-1',
  `lastjobuser` varchar(255) DEFAULT '',
  `bgcolor` varchar(255) DEFAULT '#FFFFFF',
  `fgcolor` varchar(255) DEFAULT '#000000',
  `lasttraining` int(11) DEFAULT '0',
  `linkfg` varchar(255) DEFAULT '#4444DD',
  `vlinkfg` varchar(255) DEFAULT '#2222DD',
  `alinkfg` varchar(255) DEFAULT '#00DD00',
  `avgrating` double DEFAULT '0',
  `stdrating` double DEFAULT '1',
  `birthday` date DEFAULT NULL,
  `temprat` double DEFAULT '0',
  `playlist` varchar(255) DEFAULT NULL,
  `p1` double DEFAULT NULL,
  `p2` double DEFAULT NULL,
  `p3` double DEFAULT NULL,
  `p4` double DEFAULT NULL,
  `p5` double DEFAULT NULL,
  `p6` double DEFAULT NULL,
  `p7` double DEFAULT NULL,
  `p8` double DEFAULT NULL,
  `p9` double DEFAULT NULL,
  `p10` double DEFAULT NULL,
  `realname` varchar(255) DEFAULT NULL,
  `musicseclevel` int(11) DEFAULT '0',
  `lastspecies` varchar(50) DEFAULT NULL,
  `lastlogin` datetime DEFAULT NULL,
  `lastaction` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `useridx` (`userid`),
  KEY `labidx` (`lab`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `oCompany`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oCompany` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(20) DEFAULT NULL,
  `zipcode` varchar(20) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `fax` varchar(50) DEFAULT NULL,
  `contactperson` varchar(255) DEFAULT NULL,
  `contactemail` varchar(255) DEFAULT NULL,
  `accountnumber` varchar(255) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `dateadded` datetime DEFAULT NULL,
  `addedby` varchar(255) DEFAULT NULL,
  `bad` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `oItem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oItem` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `companyid` int(11) DEFAULT NULL,
  `productnumber` varchar(255) DEFAULT NULL,
  `units` varchar(255) DEFAULT NULL,
  `details` varchar(255) DEFAULT NULL,
  `dateadded` datetime DEFAULT NULL,
  `addedby` varchar(255) DEFAULT NULL,
  `bad` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `oOrder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oOrder` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dateordered` date DEFAULT NULL,
  `daterequired` date DEFAULT NULL,
  `companyid` int(11) DEFAULT NULL,
  `frs` varchar(255) DEFAULT NULL,
  `shippingprice` double DEFAULT NULL,
  `dateadded` datetime DEFAULT NULL,
  `addedby` varchar(255) DEFAULT NULL,
  `bad` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `oOrderItem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oOrderItem` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orderid` int(11) DEFAULT NULL,
  `itemid` int(11) DEFAULT NULL,
  `quantity` double DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `unitprice` double DEFAULT NULL,
  `dateadded` datetime DEFAULT NULL,
  `addedby` varchar(255) DEFAULT NULL,
  `bad` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `orderidx` (`orderid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pages` (
  `title` varchar(40) NOT NULL DEFAULT '',
  `content` text,
  PRIMARY KEY (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sBatch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sBatch` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  `details` varchar(255) DEFAULT NULL,
  `runclassid` int(11) DEFAULT '0',
  `stimfmtcode` int(11) DEFAULT '0',
  `respfmtcode` int(11) DEFAULT '0',
  `attstate` int(11) DEFAULT '0',
  `resploadcmd` varchar(255) DEFAULT 'loadresp',
  `resploadparms` varchar(255) DEFAULT NULL,
  `respfiltercmd` varchar(255) DEFAULT 'respresampfull',
  `respfilterparms` varchar(255) DEFAULT NULL,
  `stimloadcmd` varchar(255) DEFAULT 'loadimfile',
  `stimloadparms` varchar(255) DEFAULT NULL,
  `stimfiltercmd` varchar(255) DEFAULT '',
  `stimfilterparms` varchar(255) DEFAULT NULL,
  `kernfmt` varchar(255) DEFAULT 'space',
  `minlag` int(11) DEFAULT '-8',
  `maxlag` int(11) DEFAULT '12',
  `resampcount` int(11) DEFAULT '20',
  `resampfmt` int(11) DEFAULT '0',
  `fitfrac` double(16,6) DEFAULT '0.100000',
  `predfrac` double(16,6) DEFAULT '0.100000',
  `decorrspace` int(11) DEFAULT '1',
  `decorrtime` int(11) DEFAULT '1',
  `srfiltsigma` double(16,6) DEFAULT '0.000000',
  `hfiltsigma` double(16,6) DEFAULT '0.000000',
  `sffiltsigma` double(16,6) DEFAULT '0.000000',
  `sffiltthresh` double(16,6) DEFAULT '0.000000',
  `sffiltsmooth` int(11) DEFAULT '0',
  `predsmoothsigma` double(16,6) DEFAULT '0.000000',
  `predtype` int(11) DEFAULT '0',
  `sfscount` int(11) DEFAULT '60',
  `sfsstep` double DEFAULT '4',
  `stimspeedid` int(11) DEFAULT NULL,
  `stimwindowcrf` double(16,6) DEFAULT '1.000000',
  `nloutparm` int(11) DEFAULT '1',
  `predbatch` varchar(255) DEFAULT NULL,
  `expfrac` double(16,6) DEFAULT '0.000000',
  `parmstring` text,
  `matcmd` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sCellFile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sCellFile` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cellid` varchar(15) NOT NULL DEFAULT '',
  `masterid` int(11) NOT NULL DEFAULT '0',
  `rawid` int(11) NOT NULL DEFAULT '0',
  `celldataid` int(11) DEFAULT '0',
  `runclassid` int(11) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `resplen` int(11) DEFAULT '0',
  `repcount` int(11) DEFAULT '0',
  `respfile` varchar(255) DEFAULT NULL,
  `respvarname` varchar(50) DEFAULT NULL,
  `respfiletype` int(11) DEFAULT '1',
  `nosync` int(11) DEFAULT '0',
  `respfilefmt` varchar(50) DEFAULT NULL,
  `respfmtcode` int(11) DEFAULT '-1',
  `stimfile` varchar(255) DEFAULT NULL,
  `stimfiletype` int(11) DEFAULT '1',
  `stimiconside` varchar(255) DEFAULT NULL,
  `stimfilecrf` double DEFAULT '0',
  `stimwindowsize` int(11) DEFAULT '0',
  `stimfilefmt` varchar(50) DEFAULT NULL,
  `stimfmtcode` int(11) DEFAULT '-1',
  `addedby` varchar(50) DEFAULT NULL,
  `info` varchar(255) DEFAULT NULL,
  `lastmod` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `stimpath` varchar(255) DEFAULT NULL,
  `stimspeedid` double DEFAULT NULL,
  `spikes` int(11) DEFAULT '0',
  `a_state` varchar(255) DEFAULT NULL,
  `singleid` int(11) NOT NULL DEFAULT '0',
  `singlerawid` int(11) NOT NULL DEFAULT '0',
  `unit` int(11) DEFAULT NULL,
  `channum` int(11) DEFAULT NULL,
  `model` int(11) DEFAULT '0',
  `stimsnr` int(11) DEFAULT '1000',
  `area` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `masterididx` (`masterid`),
  KEY `rawididx` (`rawid`),
  KEY `celldataididx` (`celldataid`),
  KEY `cellididx` (`cellid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sResults`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sResults` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `runid` int(11) DEFAULT NULL,
  `batch` int(11) DEFAULT NULL,
  `matstr` longtext,
  `lastmod` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `runidx` (`runid`),
  KEY `batchidx` (`batch`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sRunData`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sRunData` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `celldataid` int(11) NOT NULL DEFAULT '0',
  `masterid` int(11) DEFAULT NULL,
  `cellid` varchar(15) DEFAULT NULL,
  `grresid` int(11) DEFAULT '0',
  `batch` int(11) DEFAULT '0',
  `complete` int(11) DEFAULT '0',
  `rcversion` int(11) DEFAULT '0',
  `rundate` datetime DEFAULT NULL,
  `respath` varchar(255) DEFAULT NULL,
  `kernfile` varchar(255) DEFAULT NULL,
  `resfile` varchar(255) DEFAULT NULL,
  `archive` varchar(50) DEFAULT NULL,
  `singleid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `celldataididx` (`celldataid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tBatch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tBatch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  `details` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tCell`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tCell` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cellid` varchar(15) NOT NULL DEFAULT '',
  `rfsize` int(11) DEFAULT '0',
  `quality` int(11) DEFAULT '0',
  `xoffset` int(11) DEFAULT '0',
  `yoffset` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cellididx` (`cellid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tCellData`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tCellData` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cellid` varchar(15) NOT NULL DEFAULT '',
  `info` varchar(255) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `modelcell` int(11) DEFAULT '0',
  `masterid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cellididx` (`cellid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tCellFile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tCellFile` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `celldataid` int(11) NOT NULL DEFAULT '0',
  `respfile` varchar(255) DEFAULT NULL,
  `respvarname` varchar(50) DEFAULT NULL,
  `respfiletype` int(11) DEFAULT '1',
  `resplen` int(11) DEFAULT '0',
  `respfilecrf` int(11) DEFAULT '0',
  `stimtype` int(11) DEFAULT '0',
  `stimfile` varchar(255) DEFAULT NULL,
  `stimvarname` varchar(50) DEFAULT NULL,
  `stimfiletype` int(11) DEFAULT '1',
  `stimiconside` int(11) DEFAULT '0',
  `stimfilecrf` double(16,4) DEFAULT '0.0000',
  `nosync` int(11) DEFAULT '0',
  `spikes` int(11) DEFAULT '0',
  `repcount` int(11) DEFAULT '0',
  `stimfilelog` varchar(255) DEFAULT NULL,
  `cellid` varchar(15) NOT NULL DEFAULT '',
  `masterid` int(11) NOT NULL DEFAULT '0',
  `path` varchar(255) DEFAULT NULL,
  `respfilefmt` varchar(50) DEFAULT NULL,
  `respfmtcode` int(11) DEFAULT '-1',
  `stimwindowpix` int(11) DEFAULT '0',
  `stimfilefmt` varchar(50) DEFAULT NULL,
  `stimfmtcode` int(11) DEFAULT '-1',
  `addedby` varchar(50) DEFAULT NULL,
  `info` varchar(255) DEFAULT NULL,
  `lastmod` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `rawid` int(11) NOT NULL DEFAULT '0',
  `fixfile` varchar(255) DEFAULT NULL,
  `filtcode` int(11) DEFAULT NULL,
  `filt` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `celldataididx` (`celldataid`),
  KEY `masterididx` (`masterid`),
  KEY `cellididx` (`cellid`),
  KEY `rawididx` (`rawid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tComputer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tComputer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `load1` double(16,4) DEFAULT '0.0000',
  `load5` double(16,4) DEFAULT '0.0000',
  `load15` double(16,4) DEFAULT '0.0000',
  `lastdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `location` int(11) DEFAULT '0',
  `maxproc` int(11) DEFAULT '2',
  `numproc` int(11) DEFAULT '0',
  `maxload` double DEFAULT NULL,
  `allowqueuemaster` int(11) DEFAULT NULL,
  `ext` varchar(255) DEFAULT NULL,
  `owner` varchar(255) DEFAULT NULL,
  `allowothers` int(11) DEFAULT '1',
  `killqueueload` double DEFAULT '1.3',
  `allowqueueload` double DEFAULT '0.3',
  `lastoverload` int(11) DEFAULT '0',
  `pingcount` int(11) DEFAULT '0',
  `dead` int(11) DEFAULT '0',
  `macaddr` varchar(255) DEFAULT NULL,
  `os` varchar(255) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `hardware` varchar(255) DEFAULT NULL,
  `room` varchar(255) DEFAULT NULL,
  `nocheck` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `nameidx` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tEvent`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tEvent` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` int(11) DEFAULT '0',
  `note` varchar(255) DEFAULT NULL,
  `eventdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user` varchar(255) DEFAULT NULL,
  `computerid` int(11) DEFAULT '0',
  `queueid` int(11) DEFAULT '0',
  `pid` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `codeidx` (`code`),
  KEY `queueidx` (`queueid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tGlobal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tGlobal` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `enterradius` double DEFAULT NULL,
  `exitradius` double DEFAULT NULL,
  `latmile0` double DEFAULT NULL,
  `lonmile0` double DEFAULT NULL,
  `logperiod` double DEFAULT NULL,
  `gpscheckperiod` double DEFAULT NULL,
  `addedby` varchar(50) DEFAULT NULL,
  `info` varchar(255) DEFAULT NULL,
  `lastmod` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tGlobalData`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tGlobalData` (
  `createdate` datetime DEFAULT NULL,
  `createdby` varchar(50) DEFAULT 'svd',
  `daemonclick` datetime DEFAULT NULL,
  `daemonhost` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tGrData`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tGrData` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cellid` varchar(15) NOT NULL DEFAULT '',
  `path` varchar(255) DEFAULT '/home/david/data',
  `resfile` varchar(255) DEFAULT NULL,
  `archive` varchar(50) DEFAULT NULL,
  `stimfilecrf` int(11) DEFAULT '0',
  `stimfile` varchar(255) DEFAULT NULL,
  `respfile` varchar(255) DEFAULT NULL,
  `respvarname` varchar(255) DEFAULT NULL,
  `complete` int(11) DEFAULT '0',
  `stimvarname` varchar(255) DEFAULT NULL,
  `showframe` int(11) DEFAULT '0',
  `stimtype` int(11) DEFAULT '0',
  `stimscaleby` double(16,4) DEFAULT '1.0000',
  `stimreps` int(11) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `cellididx` (`cellid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tQueue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tQueue` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rundataid` int(11) DEFAULT NULL,
  `progname` varchar(255) DEFAULT NULL,
  `parmstring` text,
  `machinename` varchar(255) DEFAULT NULL,
  `pid` int(11) DEFAULT '0',
  `progress` int(11) DEFAULT '0',
  `complete` int(11) DEFAULT '0',
  `queuedate` datetime DEFAULT NULL,
  `startdate` datetime DEFAULT NULL,
  `lastdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `killnow` int(11) DEFAULT '0',
  `mailto` varchar(255) DEFAULT NULL,
  `mailcommand` varchar(255) DEFAULT NULL,
  `user` varchar(255) DEFAULT 'david',
  `allowqueuemaster` int(11) DEFAULT '0',
  `computerid` int(11) DEFAULT NULL,
  `priority` int(11) DEFAULT '0',
  `note` varchar(50) DEFAULT NULL,
  `waitid` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tRunData`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tRunData` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `celldataid` int(11) NOT NULL DEFAULT '0',
  `respcopies` int(11) DEFAULT '0',
  `slideamt` int(11) DEFAULT '1',
  `complex` int(11) DEFAULT '1',
  `binsize` int(11) DEFAULT '16',
  `decorrspace` int(11) DEFAULT '1',
  `decorrtime` int(11) DEFAULT '0',
  `dummystim` int(11) DEFAULT '0',
  `resampcount` int(11) DEFAULT '11',
  `resampfrac` double(16,4) DEFAULT '0.5000',
  `showframe` int(11) DEFAULT '5',
  `svdfactorspace` double(16,6) DEFAULT '0.001000',
  `svdfactortime` double(16,6) DEFAULT '0.000000',
  `batch` int(11) DEFAULT '0',
  `complete` int(11) DEFAULT '0',
  `rundate` datetime DEFAULT NULL,
  `resfile` varchar(255) DEFAULT NULL,
  `archive` varchar(50) DEFAULT NULL,
  `grresid` int(11) DEFAULT '0',
  `srfiltsigma` double(16,4) DEFAULT '0.0000',
  `hfiltsigma` double(16,4) DEFAULT '0.0000',
  `sffiltsigma` double(16,4) DEFAULT '0.0000',
  `sffiltthresh` double(16,4) DEFAULT '0.0000',
  `predtype` int(11) DEFAULT '0',
  `kernfile` varchar(255) DEFAULT NULL,
  `sfscount` int(11) DEFAULT '60',
  `sfsstep` double(16,4) DEFAULT '0.0000',
  `rcversion` int(11) DEFAULT '0',
  `predsmoothsigma` double(16,4) DEFAULT NULL,
  `sffiltsmooth` int(11) DEFAULT '0',
  `zerosize` int(11) DEFAULT '0',
  `saveintermed` int(11) DEFAULT '0',
  `ncoffset` int(11) DEFAULT '6',
  `stimformat` int(11) DEFAULT '1',
  `nloutparm` int(11) DEFAULT '0',
  `runclassid` int(11) DEFAULT '0',
  `attstate` int(11) DEFAULT '0',
  `pSAflag` smallint(6) DEFAULT '1',
  `sSAflag` smallint(6) DEFAULT '1',
  `sSA2flag` smallint(6) DEFAULT '0',
  `masterid` int(11) DEFAULT NULL,
  `cellid` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `celldataididx` (`celldataid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tRunFile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tRunFile` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rundataid` int(11) NOT NULL DEFAULT '0',
  `cellfileid` int(11) NOT NULL DEFAULT '0',
  `celldataid` int(11) NOT NULL DEFAULT '0',
  `stimmaskpix` int(11) DEFAULT '8',
  `stimcroppix` int(11) DEFAULT '16',
  `stimscalepix` int(11) DEFAULT '0',
  `respstart` int(11) DEFAULT '0',
  `respstop` int(11) DEFAULT '0',
  `usecode` int(11) DEFAULT '0',
  `rawid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `rundataididx` (`rundataid`),
  KEY `rawididx` (`rawid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tRunRes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tRunRes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rundataid` int(11) NOT NULL DEFAULT '0',
  `decorrspace` int(11) NOT NULL DEFAULT '1',
  `predtype` int(11) NOT NULL DEFAULT '0',
  `testid` int(11) NOT NULL DEFAULT '0',
  `testvalue` double(16,6) DEFAULT '0.000000',
  `testcutoff` double(16,6) DEFAULT '0.000000',
  `testdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `fitor` double(16,6) DEFAULT '0.000000',
  `fitorw` double(16,6) DEFAULT '0.000000',
  `fitsf` double(16,6) DEFAULT '0.000000',
  `fitsfw` double(16,6) DEFAULT '0.000000',
  `fitamp` double(16,6) DEFAULT '0.000000',
  `kernfile` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rundatatestidx` (`testid`,`rundataid`,`decorrspace`,`predtype`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tRunResult`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tRunResult` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rundataid` int(11) NOT NULL DEFAULT '0',
  `decorrspace` int(11) NOT NULL DEFAULT '1',
  `predtype` int(11) NOT NULL DEFAULT '0',
  `fittype` int(11) NOT NULL DEFAULT '0',
  `preddata` int(11) NOT NULL DEFAULT '0',
  `kernfile` varchar(255) DEFAULT NULL,
  `sfs` double(16,6) DEFAULT '0.000000',
  `testdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `grcorr` double(16,6) DEFAULT '0.000000',
  `xcovlat` double(16,6) DEFAULT '0.000000',
  `xcov` double(16,6) DEFAULT '0.000000',
  `areaolap` double(16,6) DEFAULT '0.000000',
  `fitor` double(16,6) DEFAULT '0.000000',
  `fitorw` double(16,6) DEFAULT '0.000000',
  `fitsf` double(16,6) DEFAULT '0.000000',
  `fitsfw` double(16,6) DEFAULT '0.000000',
  `fitamp` double(16,6) DEFAULT '0.000000',
  `fiterror` double(16,6) DEFAULT NULL,
  `snr` double(16,6) DEFAULT '0.000000',
  `cohere` double(16,6) DEFAULT '0.000000',
  `epochxc` double(16,6) DEFAULT '0.000000',
  `fitoff` double(16,6) DEFAULT '0.000000',
  `sigline` double(16,6) DEFAULT '0.000000',
  PRIMARY KEY (`id`),
  KEY `rundatatestidx` (`rundataid`,`decorrspace`,`predtype`,`fittype`,`preddata`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

