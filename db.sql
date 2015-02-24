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
  `iso1_code` varchar(2) NOT NULL,
  `numeric_code` tinyint(4) NOT NULL,
  `iso3_code` varchar(3) NOT NULL,
  `name` varchar(64) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=251 DEFAULT CHARSET=utf8;

/*Data for the table `countries` */

insert  into `countries`(`id`,`iso1_code`,`numeric_code`,`iso3_code`,`name`,`deleted`) values (1,'AD',20,'AND','Andorra',0),(2,'AE',127,'ARE','United Arab Emirates',0),(3,'AF',4,'AFG','Afghanistan',0),(4,'AG',28,'ATG','Antigua and Barbuda',0),(5,'AI',127,'AIA','Anguilla',0),(6,'AL',8,'ALB','Albania',0),(7,'AM',51,'ARM','Armenia',0),(8,'AO',24,'AGO','Angola',0),(9,'AQ',10,'ATA','Antarctica',0),(10,'AR',32,'ARG','Argentina',0),(11,'AS',16,'ASM','American Samoa',0),(12,'AT',40,'AUT','Austria',0),(13,'AU',36,'AUS','Australia',0),(14,'AW',127,'ABW','Aruba',0),(15,'AX',127,'ALA','Aland Islands',0),(16,'AZ',31,'AZE','Azerbaijan',0),(17,'BA',70,'BIH','Bosnia and Herzegovina',0),(18,'BB',52,'BRB','Barbados',0),(19,'BD',50,'BGD','Bangladesh',0),(20,'BE',56,'BEL','Belgium',0),(21,'BF',127,'BFA','Burkina Faso',0),(22,'BG',100,'BGR','Bulgaria',0),(23,'BH',48,'BHR','Bahrain',0),(24,'BI',108,'BDI','Burundi',0),(25,'BJ',127,'BEN','Benin',0),(26,'BL',127,'BLM','Saint Barthelemy',0),(27,'BM',60,'BMU','Bermuda',0),(28,'BN',96,'BRN','Brunei Darussalam',0),(29,'BO',68,'BOL','Bolivia, Plurinational State of',0),(30,'BQ',127,'BES','Bonaire, Sint Eustatius and Saba',0),(31,'BR',76,'BRA','Brazil',0),(32,'BS',44,'BHS','Bahamas',0),(33,'BT',64,'BTN','Bhutan',0),(34,'BV',74,'BVT','Bouvet Island',0),(35,'BW',72,'BWA','Botswana',0),(36,'BY',112,'BLR','Belarus',0),(37,'BZ',84,'BLZ','Belize',0),(38,'CA',124,'CAN','Canada',0),(39,'CC',127,'CCK','Cocos (Keeling) Islands',0),(40,'CD',127,'COD','Congo, the Democratic Republic of the',0),(41,'CF',127,'CAF','Central African Republic',0),(42,'CG',127,'COG','Congo',0),(43,'CH',127,'CHE','Switzerland',0),(44,'CI',127,'CIV','Cote d\'Ivoire',0),(45,'CK',127,'COK','Cook Islands',0),(46,'CL',127,'CHL','Chile',0),(47,'CM',120,'CMR','Cameroon',0),(48,'CN',127,'CHN','China',0),(49,'CO',127,'COL','Colombia',0),(50,'CR',127,'CRI','Costa Rica',0),(51,'CU',127,'CUB','Cuba',0),(52,'CV',127,'CPV','Cape Verde',0),(53,'CW',127,'CUW','Curacao',0),(54,'CX',127,'CXR','Christmas Island',0),(55,'CY',127,'CYP','Cyprus',0),(56,'CZ',127,'CZE','Czech Republic',0),(57,'DE',127,'DEU','Germany',0),(58,'DJ',127,'DJI','Djibouti',0),(59,'DK',127,'DNK','Denmark',0),(60,'DM',127,'DMA','Dominica',0),(61,'DO',127,'DOM','Dominican Republic',0),(62,'DZ',12,'DZA','Algeria',0),(63,'EC',127,'ECU','Ecuador',0),(64,'EE',127,'EST','Estonia',0),(65,'EG',127,'EGY','Egypt',0),(66,'EH',127,'ESH','Western Sahara',0),(67,'ER',127,'ERI','Eritrea',0),(68,'ES',127,'ESP','Spain',0),(69,'ET',127,'ETH','Ethiopia',0),(70,'FI',127,'FIN','Finland',0),(71,'FJ',127,'FJI','Fiji',0),(72,'FK',127,'FLK','Falkland Islands (Malvinas)',0),(73,'FM',127,'FSM','Micronesia, Federated States of',0),(74,'FO',127,'FRO','Faroe Islands',0),(75,'FR',127,'FRA','France',0),(76,'GA',127,'GAB','Gabon',0),(77,'GB',127,'GBR','United Kingdom',0),(78,'GD',127,'GRD','Grenada',0),(79,'GE',127,'GEO','Georgia',0),(80,'GF',127,'GUF','French Guiana',0),(81,'GG',127,'GGY','Guernsey',0),(82,'GH',127,'GHA','Ghana',0),(83,'GI',127,'GIB','Gibraltar',0),(84,'GL',127,'GRL','Greenland',0),(85,'GM',127,'GMB','Gambia',0),(86,'GN',127,'GIN','Guinea',0),(87,'GP',127,'GLP','Guadeloupe',0),(88,'GQ',127,'GNQ','Equatorial Guinea',0),(89,'GR',127,'GRC','Greece',0),(90,'GS',127,'SGS','South Georgia and the South Sandwich Islands',0),(91,'GT',127,'GTM','Guatemala',0),(92,'GU',127,'GUM','Guam',0),(93,'GW',127,'GNB','Guinea-Bissau',0),(94,'GY',127,'GUY','Guyana',0),(95,'HK',127,'HKG','Hong Kong',0),(96,'HM',127,'HMD','Heard Island and McDonald Islands',0),(97,'HN',127,'HND','Honduras',0),(98,'HR',127,'HRV','Croatia',0),(99,'HT',127,'HTI','Haiti',0),(100,'HU',127,'HUN','Hungary',0),(101,'ID',127,'IDN','Indonesia',0),(102,'IE',127,'IRL','Ireland',0),(103,'IL',127,'ISR','Israel',0),(104,'IM',127,'IMN','Isle of Man',0),(105,'IN',127,'IND','India',0),(106,'IO',86,'IOT','British Indian Ocean Territory',0),(107,'IQ',127,'IRQ','Iraq',0),(108,'IR',127,'IRN','Iran, Islamic Republic of',0),(109,'IS',127,'ISL','Iceland',0),(110,'IT',127,'ITA','Italy',0),(111,'JE',127,'JEY','Jersey',0),(112,'JM',127,'JAM','Jamaica',0),(113,'JO',127,'JOR','Jordan',0),(114,'JP',127,'JPN','Japan',0),(115,'KE',127,'KEN','Kenya',0),(116,'KG',127,'KGZ','Kyrgyzstan',0),(117,'KH',116,'KHM','Cambodia',0),(118,'KI',127,'KIR','Kiribati',0),(119,'KM',127,'COM','Comoros',0),(120,'KN',127,'KNA','Saint Kitts and Nevis',0),(121,'KP',127,'PRK','Korea, Democratic People\'s Republic of',0),(122,'KR',127,'KOR','Korea, Republic of',0),(123,'KW',127,'KWT','Kuwait',0),(124,'KY',127,'CYM','Cayman Islands',0),(125,'KZ',127,'KAZ','Kazakhstan',0),(126,'LA',127,'LAO','Lao People\'s Democratic Republic',0),(127,'LB',127,'LBN','Lebanon',0),(128,'LC',127,'LCA','Saint Lucia',0),(129,'LI',127,'LIE','Liechtenstein',0),(130,'LK',127,'LKA','Sri Lanka',0),(131,'LR',127,'LBR','Liberia',0),(132,'LS',127,'LSO','Lesotho',0),(133,'LT',127,'LTU','Lithuania',0),(134,'LU',127,'LUX','Luxembourg',0),(135,'LV',127,'LVA','Latvia',0),(136,'LY',127,'LBY','Libyan Arab Jamahiriya',0),(137,'MA',127,'MAR','Morocco',0),(138,'MC',127,'MCO','Monaco',0),(139,'MD',127,'MDA','Moldova, Republic of',0),(140,'ME',127,'MNE','Montenegro',0),(141,'MF',127,'MAF','Saint Martin (French part)',0),(142,'MG',127,'MDG','Madagascar',0),(143,'MH',127,'MHL','Marshall Islands',0),(144,'MK',127,'MKD','Macedonia, the former Yugoslav Republic of',0),(145,'ML',127,'MLI','Mali',0),(146,'MM',104,'MMR','Myanmar',0),(147,'MN',127,'MNG','Mongolia',0),(148,'MO',127,'MAC','Macao',0),(149,'MP',127,'MNP','Northern Mariana Islands',0),(150,'MQ',127,'MTQ','Martinique',0),(151,'MR',127,'MRT','Mauritania',0),(152,'MS',127,'MSR','Montserrat',0),(153,'MT',127,'MLT','Malta',0),(154,'MU',127,'MUS','Mauritius',0),(155,'MV',127,'MDV','Maldives',0),(156,'MW',127,'MWI','Malawi',0),(157,'MX',127,'MEX','Mexico',0),(158,'MY',127,'MYS','Malaysia',0),(159,'MZ',127,'MOZ','Mozambique',0),(160,'NA',127,'NAM','Namibia',0),(161,'NC',127,'NCL','New Caledonia',0),(162,'NE',127,'NER','Niger',0),(163,'NF',127,'NFK','Norfolk Island',0),(164,'NG',127,'NGA','Nigeria',0),(165,'NI',127,'NIC','Nicaragua',0),(166,'NL',127,'NLD','Netherlands',0),(167,'NO',127,'NOR','Norway',0),(168,'NP',127,'NPL','Nepal',0),(169,'NR',127,'NRU','Nauru',0),(170,'NU',127,'NIU','Niue',0),(171,'NZ',127,'NZL','New Zealand',0),(172,'OM',127,'OMN','Oman',0),(173,'PA',127,'PAN','Panama',0),(174,'PE',127,'PER','Peru',0),(175,'PF',127,'PYF','French Polynesia',0),(176,'PG',127,'PNG','Papua New Guinea',0),(177,'PH',127,'PHL','Philippines',0),(178,'PK',127,'PAK','Pakistan',0),(179,'PL',127,'POL','Poland',0),(180,'PM',127,'SPM','Saint Pierre and Miquelon',0),(181,'PN',127,'PCN','Pitcairn',0),(182,'PR',127,'PRI','Puerto Rico',0),(183,'PS',127,'PSE','Palestinian Territory, Occupied',0),(184,'PT',127,'PRT','Portugal',0),(185,'PW',127,'PLW','Palau',0),(186,'PY',127,'PRY','Paraguay',0),(187,'QA',127,'QAT','Qatar',0),(188,'RE',127,'REU','Reunion',0),(189,'RO',127,'ROU','Romania',0),(190,'RS',127,'SRB','Serbia',0),(191,'RU',127,'RUS','Russian Federation',0),(192,'RW',127,'RWA','Rwanda',0),(193,'SA',127,'SAU','Saudi Arabia',0),(194,'SB',90,'SLB','Solomon Islands',0),(195,'SC',127,'SYC','Seychelles',0),(196,'SD',127,'SDN','Sudan',0),(197,'SE',127,'SWE','Sweden',0),(198,'SG',127,'SGP','Singapore',0),(199,'SH',127,'SHN','Saint Helena, Ascension and Tristan da Cunha',0),(200,'SI',127,'SVN','Slovenia',0),(201,'SJ',127,'SJM','Svalbard and Jan Mayen',0),(202,'SK',127,'SVK','Slovakia',0),(203,'SL',127,'SLE','Sierra Leone',0),(204,'SM',127,'SMR','San Marino',0),(205,'SN',127,'SEN','Senegal',0),(206,'SO',127,'SOM','Somalia',0),(207,'SR',127,'SUR','Suriname',0),(208,'SS',127,'SSD','South Sudan',0),(209,'ST',127,'STP','Sao Tome and Principe',0),(210,'SV',127,'SLV','El Salvador',0),(211,'SX',127,'SXM','Sint Maarten (Dutch part)',0),(212,'SY',127,'SYR','Syrian Arab Republic',0),(213,'SZ',127,'SWZ','Swaziland',0),(214,'TC',127,'TCA','Turks and Caicos Islands',0),(215,'TD',127,'TCD','Chad',0),(216,'TF',127,'ATF','French Southern Territories',0),(217,'TG',127,'TGO','Togo',0),(218,'TH',127,'THA','Thailand',0),(219,'TJ',127,'TJK','Tajikistan',0),(220,'TK',127,'TKL','Tokelau',0),(221,'TL',127,'TLS','Timor-Leste',0),(222,'TM',127,'TKM','Turkmenistan',0),(223,'TN',127,'TUN','Tunisia',0),(224,'TO',127,'TON','Tonga',0),(225,'TR',127,'TUR','Turkey',0),(226,'TT',127,'TTO','Trinidad and Tobago',0),(227,'TV',127,'TUV','Tuvalu',0),(228,'TW',127,'TWN','Taiwan, Province of China',0),(229,'TZ',127,'TZA','Tanzania, United Republic of',0),(230,'UA',127,'UKR','Ukraine',0),(231,'UG',127,'UGA','Uganda',0),(232,'UM',127,'UMI','United States Minor Outlying Islands',0),(233,'US',127,'USA','United States',0),(234,'UY',127,'URY','Uruguay',0),(235,'UZ',127,'UZB','Uzbekistan',0),(236,'VA',127,'VAT','Holy See (Vatican City State)',0),(237,'VC',127,'VCT','Saint Vincent and the Grenadines',0),(238,'VE',127,'VEN','Venezuela, Bolivarian Republic of',0),(239,'VG',92,'VGB','Virgin Islands, British',0),(240,'VI',127,'VIR','Virgin Islands, U.S.',0),(241,'VN',127,'VNM','Viet Nam',0),(242,'VU',127,'VUT','Vanuatu',0),(243,'WF',127,'WLF','Wallis and Futuna',0),(244,'WS',127,'WSM','Samoa',0),(245,'YE',127,'YEM','Yemen',0),(246,'YT',127,'MYT','Mayotte',0),(247,'ZA',127,'ZAF','South Africa',0),(248,'ZM',127,'ZMB','Zambia',0),(249,'ZW',127,'ZWE','Zimbabwe',0),(250,'ZZ',0,'ZZZ','NA',0);

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
  `company` varchar(255) DEFAULT NULL,
  `city` varchar(64) DEFAULT NULL,
  `total` decimal(12,4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `country_id` (`country_id`),
  CONSTRAINT `order_info_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `payment_methods` */

insert  into `payment_methods`(`id`,`name`,`code`) values (1,'iDEAL','ideal'),(2,'AfterPay','afterpay');

/*Table structure for table `payment_order_info` */

DROP TABLE IF EXISTS `payment_order_info`;

CREATE TABLE `payment_order_info` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) unsigned NOT NULL,
  `payment_method_id` tinyint(3) unsigned NOT NULL,
  `data` text,
  `payment_order_id` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT '0',
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
