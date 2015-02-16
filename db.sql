/*
SQLyog Ultimate v11.52 (64 bit)
MySQL - 5.5.40-0ubuntu0.14.04.1-log : Database - product
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `countries` */

DROP TABLE IF EXISTS `countries`;

CREATE TABLE `countries` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `iso3_code` varchar(3) NOT NULL,
  `name` varchar(64) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=251 DEFAULT CHARSET=utf8;

/*Data for the table `countries` */

insert  into `countries`(`id`,`iso3_code`,`name`,`deleted`) values (1,'AND','Andorra',0),(2,'ARE','United Arab Emirates',0),(3,'AFG','Afghanistan',0),(4,'ATG','Antigua and Barbuda',0),(5,'AIA','Anguilla',0),(6,'ALB','Albania',0),(7,'ARM','Armenia',0),(8,'AGO','Angola',0),(9,'ATA','Antarctica',0),(10,'ARG','Argentina',0),(11,'ASM','American Samoa',0),(12,'AUT','Austria',0),(13,'AUS','Australia',0),(14,'ABW','Aruba',0),(15,'ALA','Aland Islands',0),(16,'AZE','Azerbaijan',0),(17,'BIH','Bosnia and Herzegovina',0),(18,'BRB','Barbados',0),(19,'BGD','Bangladesh',0),(20,'BEL','Belgium',0),(21,'BFA','Burkina Faso',0),(22,'BGR','Bulgaria',0),(23,'BHR','Bahrain',0),(24,'BDI','Burundi',0),(25,'BEN','Benin',0),(26,'BLM','Saint Barthelemy',0),(27,'BMU','Bermuda',0),(28,'BRN','Brunei Darussalam',0),(29,'BOL','Bolivia, Plurinational State of',0),(30,'BES','Bonaire, Sint Eustatius and Saba',0),(31,'BRA','Brazil',0),(32,'BHS','Bahamas',0),(33,'BTN','Bhutan',0),(34,'BVT','Bouvet Island',0),(35,'BWA','Botswana',0),(36,'BLR','Belarus',0),(37,'BLZ','Belize',0),(38,'CAN','Canada',0),(39,'CCK','Cocos (Keeling) Islands',0),(40,'COD','Congo, the Democratic Republic of the',0),(41,'CAF','Central African Republic',0),(42,'COG','Congo',0),(43,'CHE','Switzerland',0),(44,'CIV','Cote d\'\'Ivoire',0),(45,'COK','Cook Islands',0),(46,'CHL','Chile',0),(47,'CMR','Cameroon',0),(48,'CHN','China',0),(49,'COL','Colombia',0),(50,'CRI','Costa Rica',0),(51,'CUB','Cuba',0),(52,'CPV','Cape Verde',0),(53,'CUW','Curacao',0),(54,'CXR','Christmas Island',0),(55,'CYP','Cyprus',0),(56,'CZE','Czech Republic',0),(57,'DEU','Germany',0),(58,'DJI','Djibouti',0),(59,'DNK','Denmark',0),(60,'DMA','Dominica',0),(61,'DOM','Dominican Republic',0),(62,'DZA','Algeria',0),(63,'ECU','Ecuador',0),(64,'EST','Estonia',0),(65,'EGY','Egypt',0),(66,'ESH','Western Sahara',0),(67,'ERI','Eritrea',0),(68,'ESP','Spain',0),(69,'ETH','Ethiopia',0),(70,'FIN','Finland',0),(71,'FJI','Fiji',0),(72,'FLK','Falkland Islands (Malvinas)',0),(73,'FSM','Micronesia, Federated States of',0),(74,'FRO','Faroe Islands',0),(75,'FRA','France',0),(76,'GAB','Gabon',0),(77,'GBR','United Kingdom',0),(78,'GRD','Grenada',0),(79,'GEO','Georgia',0),(80,'GUF','French Guiana',0),(81,'GGY','Guernsey',0),(82,'GHA','Ghana',0),(83,'GIB','Gibraltar',0),(84,'GRL','Greenland',0),(85,'GMB','Gambia',0),(86,'GIN','Guinea',0),(87,'GLP','Guadeloupe',0),(88,'GNQ','Equatorial Guinea',0),(89,'GRC','Greece',0),(90,'SGS','South Georgia and the South Sandwich Islands',0),(91,'GTM','Guatemala',0),(92,'GUM','Guam',0),(93,'GNB','Guinea-Bissau',0),(94,'GUY','Guyana',0),(95,'HKG','Hong Kong',0),(96,'HMD','Heard Island and McDonald Islands',0),(97,'HND','Honduras',0),(98,'HRV','Croatia',0),(99,'HTI','Haiti',0),(100,'HUN','Hungary',0),(101,'IDN','Indonesia',0),(102,'IRL','Ireland',0),(103,'ISR','Israel',0),(104,'IMN','Isle of Man',0),(105,'IND','India',0),(106,'IOT','British Indian Ocean Territory',0),(107,'IRQ','Iraq',0),(108,'IRN','Iran, Islamic Republic of',0),(109,'ISL','Iceland',0),(110,'ITA','Italy',0),(111,'JEY','Jersey',0),(112,'JAM','Jamaica',0),(113,'JOR','Jordan',0),(114,'JPN','Japan',0),(115,'KEN','Kenya',0),(116,'KGZ','Kyrgyzstan',0),(117,'KHM','Cambodia',0),(118,'KIR','Kiribati',0),(119,'COM','Comoros',0),(120,'KNA','Saint Kitts and Nevis',0),(121,'PRK','Korea, Democratic People\'\'s Republic of',0),(122,'KOR','Korea, Republic of',0),(123,'KWT','Kuwait',0),(124,'CYM','Cayman Islands',0),(125,'KAZ','Kazakhstan',0),(126,'LAO','Lao People\'\'s Democratic Republic',0),(127,'LBN','Lebanon',0),(128,'LCA','Saint Lucia',0),(129,'LIE','Liechtenstein',0),(130,'LKA','Sri Lanka',0),(131,'LBR','Liberia',0),(132,'LSO','Lesotho',0),(133,'LTU','Lithuania',0),(134,'LUX','Luxembourg',0),(135,'LVA','Latvia',0),(136,'LBY','Libyan Arab Jamahiriya',0),(137,'MAR','Morocco',0),(138,'MCO','Monaco',0),(139,'MDA','Moldova, Republic of',0),(140,'MNE','Montenegro',0),(141,'MAF','Saint Martin (French part)',0),(142,'MDG','Madagascar',0),(143,'MHL','Marshall Islands',0),(144,'MKD','Macedonia, the former Yugoslav Republic of',0),(145,'MLI','Mali',0),(146,'MMR','Myanmar',0),(147,'MNG','Mongolia',0),(148,'MAC','Macao',0),(149,'MNP','Northern Mariana Islands',0),(150,'MTQ','Martinique',0),(151,'MRT','Mauritania',0),(152,'MSR','Montserrat',0),(153,'MLT','Malta',0),(154,'MUS','Mauritius',0),(155,'MDV','Maldives',0),(156,'MWI','Malawi',0),(157,'MEX','Mexico',0),(158,'MYS','Malaysia',0),(159,'MOZ','Mozambique',0),(160,'NAM','Namibia',0),(161,'NCL','New Caledonia',0),(162,'NER','Niger',0),(163,'NFK','Norfolk Island',0),(164,'NGA','Nigeria',0),(165,'NIC','Nicaragua',0),(166,'NLD','Netherlands',0),(167,'NOR','Norway',0),(168,'NPL','Nepal',0),(169,'NRU','Nauru',0),(170,'NIU','Niue',0),(171,'NZL','New Zealand',0),(172,'OMN','Oman',0),(173,'PAN','Panama',0),(174,'PER','Peru',0),(175,'PYF','French Polynesia',0),(176,'PNG','Papua New Guinea',0),(177,'PHL','Philippines',0),(178,'PAK','Pakistan',0),(179,'POL','Poland',0),(180,'SPM','Saint Pierre and Miquelon',0),(181,'PCN','Pitcairn',0),(182,'PRI','Puerto Rico',0),(183,'PSE','Palestinian Territory, Occupied',0),(184,'PRT','Portugal',0),(185,'PLW','Palau',0),(186,'PRY','Paraguay',0),(187,'QAT','Qatar',0),(188,'REU','Reunion',0),(189,'ROU','Romania',0),(190,'SRB','Serbia',0),(191,'RUS','Russian Federation',0),(192,'RWA','Rwanda',0),(193,'SAU','Saudi Arabia',0),(194,'SLB','Solomon Islands',0),(195,'SYC','Seychelles',0),(196,'SDN','Sudan',0),(197,'SWE','Sweden',0),(198,'SGP','Singapore',0),(199,'SHN','Saint Helena, Ascension and Tristan da Cunha',0),(200,'SVN','Slovenia',0),(201,'SJM','Svalbard and Jan Mayen',0),(202,'SVK','Slovakia',0),(203,'SLE','Sierra Leone',0),(204,'SMR','San Marino',0),(205,'SEN','Senegal',0),(206,'SOM','Somalia',0),(207,'SUR','Suriname',0),(208,'SSD','South Sudan',0),(209,'STP','Sao Tome and Principe',0),(210,'SLV','El Salvador',0),(211,'SXM','Sint Maarten (Dutch part)',0),(212,'SYR','Syrian Arab Republic',0),(213,'SWZ','Swaziland',0),(214,'TCA','Turks and Caicos Islands',0),(215,'TCD','Chad',0),(216,'ATF','French Southern Territories',0),(217,'TGO','Togo',0),(218,'THA','Thailand',0),(219,'TJK','Tajikistan',0),(220,'TKL','Tokelau',0),(221,'TLS','Timor-Leste',0),(222,'TKM','Turkmenistan',0),(223,'TUN','Tunisia',0),(224,'TON','Tonga',0),(225,'TUR','Turkey',0),(226,'TTO','Trinidad and Tobago',0),(227,'TUV','Tuvalu',0),(228,'TWN','Taiwan, Province of China',0),(229,'TZA','Tanzania, United Republic of',0),(230,'UKR','Ukraine',0),(231,'UGA','Uganda',0),(232,'UMI','United States Minor Outlying Islands',0),(233,'USA','United States',0),(234,'URY','Uruguay',0),(235,'UZB','Uzbekistan',0),(236,'VAT','Holy See (Vatican City State)',0),(237,'VCT','Saint Vincent and the Grenadines',0),(238,'VEN','Venezuela, Bolivarian Republic of',0),(239,'VGB','Virgin Islands, British',0),(240,'VIR','Virgin Islands, U.S.',0),(241,'VNM','Viet Nam',0),(242,'VUT','Vanuatu',0),(243,'WLF','Wallis and Futuna',0),(244,'WSM','Samoa',0),(245,'YEM','Yemen',0),(246,'MYT','Mayotte',0),(247,'ZAF','South Africa',0),(248,'ZMB','Zambia',0),(249,'ZWE','Zimbabwe',0),(250,'ZZZ','NA',0);

/*Table structure for table `order_info` */

DROP TABLE IF EXISTS `order_info`;

CREATE TABLE `order_info` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) unsigned NOT NULL,
  `first_name` varchar(64) DEFAULT NULL,
  `last_name` varchar(64) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `profession` varchar(255) DEFAULT NULL,
  `post_code` varchar(32) DEFAULT NULL,
  `house_number` varchar(10) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `quantity` int(11) unsigned NOT NULL,
  `country_id` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `country_id` (`country_id`),
  CONSTRAINT `order_info_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `order_info_ibfk_2` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `order_info` */

/*Table structure for table `orders` */

DROP TABLE IF EXISTS `orders`;

CREATE TABLE `orders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `orders` */

/*Table structure for table `payment_methods` */

DROP TABLE IF EXISTS `payment_methods`;

CREATE TABLE `payment_methods` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `payment_methods` */

/*Table structure for table `payment_order_info` */

DROP TABLE IF EXISTS `payment_order_info`;

CREATE TABLE `payment_order_info` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) unsigned NOT NULL,
  `payment_method_id` tinyint(3) unsigned NOT NULL,
  `data` text,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `payment_method_id` (`payment_method_id`),
  CONSTRAINT `payment_order_info_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `payment_order_info_ibfk_2` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `payment_order_info` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
