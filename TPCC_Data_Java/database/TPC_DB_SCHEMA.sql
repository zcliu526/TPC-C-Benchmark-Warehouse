/*
SQLyog Ultimate v13.1.1 (32 bit)
MySQL - 5.7.18-log : Database - tpc_db
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`tpc_db` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `tpc_db`;

/*Table structure for table `customer` */

DROP TABLE IF EXISTS `customer`;

CREATE TABLE `customer` (
  `C_ID` int(10) unsigned NOT NULL,
  `C_D_ID` int(10) unsigned NOT NULL,
  `C_W_ID` int(10) unsigned NOT NULL,
  `C_FIRST` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `C_MIDDLE` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `C_LAST` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `C_STREET_1` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `C_STREET_2` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `C_CITY` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `C_STATE` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `C_ZIP` varchar(9) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `C_PHONE` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `C_SINCE` datetime DEFAULT NULL,
  `C_CREDIT` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `C_CREDIT_LIM` decimal(12,2) DEFAULT NULL,
  `C_DISCOUNT` decimal(4,4) DEFAULT NULL,
  `C_BALANCE` decimal(12,2) DEFAULT NULL,
  `C_YTD_PAYMENT` decimal(12,2) DEFAULT NULL,
  `C_PAYMENT_CNT` int(10) unsigned DEFAULT NULL,
  `C_DELIVERY_CNT` int(10) unsigned DEFAULT NULL,
  `C_DATA` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`C_ID`,`C_D_ID`,`C_W_ID`),
  KEY `customer_district_idx` (`C_W_ID`,`C_D_ID`),
  CONSTRAINT `customer-district` FOREIGN KEY (`C_W_ID`, `C_D_ID`) REFERENCES `district` (`D_W_ID`, `D_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `district` */

DROP TABLE IF EXISTS `district`;

CREATE TABLE `district` (
  `D_ID` int(10) unsigned NOT NULL,
  `D_W_ID` int(10) unsigned NOT NULL,
  `D_NAME` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `D_STREET_1` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `D_STREET_2` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `D_CITY` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `D_STATE` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `D_ZIP` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `D_TAX` decimal(4,4) NOT NULL,
  `D_YTD` decimal(12,2) NOT NULL,
  `D_NEXT_O_ID` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`D_ID`,`D_W_ID`),
  KEY `district-warehouse_idx` (`D_W_ID`),
  CONSTRAINT `district-warehouse` FOREIGN KEY (`D_W_ID`) REFERENCES `warehouse` (`W_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `history` */

DROP TABLE IF EXISTS `history`;

CREATE TABLE `history` (
  `H_C_ID` int(10) unsigned DEFAULT NULL,
  `H_C_D_ID` int(10) unsigned DEFAULT NULL,
  `H_C_W_ID` int(10) unsigned DEFAULT NULL,
  `H_D_ID` int(10) unsigned DEFAULT NULL,
  `H_W_ID` int(10) unsigned DEFAULT NULL,
  `H_DATE` datetime DEFAULT NULL,
  `H_AMOUNT` decimal(6,2) DEFAULT NULL,
  `H_DATA` varchar(24) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  KEY `history_customer_idx` (`H_C_W_ID`,`H_C_D_ID`,`H_C_ID`),
  KEY `history_district_idx` (`H_W_ID`,`H_D_ID`),
  CONSTRAINT `history_customer` FOREIGN KEY (`H_C_W_ID`, `H_C_D_ID`, `H_C_ID`) REFERENCES `customer` (`C_W_ID`, `C_D_ID`, `C_ID`),
  CONSTRAINT `history_district` FOREIGN KEY (`H_W_ID`, `H_D_ID`) REFERENCES `district` (`D_W_ID`, `D_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `item` */

DROP TABLE IF EXISTS `item`;

CREATE TABLE `item` (
  `I_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `I_IM_ID` int(10) unsigned DEFAULT NULL,
  `I_NAME` varchar(24) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `I_PRICE` decimal(5,2) DEFAULT NULL,
  `I_DATA` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`I_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=100001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `new_order` */

DROP TABLE IF EXISTS `new_order`;

CREATE TABLE `new_order` (
  `NO_O_ID` int(10) unsigned NOT NULL,
  `NO_D_ID` int(10) unsigned NOT NULL,
  `NO_W_ID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`NO_W_ID`,`NO_D_ID`,`NO_O_ID`),
  KEY `new_order_order_idx` (`NO_W_ID`,`NO_D_ID`,`NO_O_ID`),
  CONSTRAINT `new_order_order` FOREIGN KEY (`NO_W_ID`, `NO_D_ID`, `NO_O_ID`) REFERENCES `order` (`O_W_ID`, `O_D_ID`, `O_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `order` */

DROP TABLE IF EXISTS `order`;

CREATE TABLE `order` (
  `O_ID` int(10) unsigned NOT NULL,
  `O_D_ID` int(10) unsigned NOT NULL,
  `O_W_ID` int(10) unsigned NOT NULL,
  `O_C_ID` int(10) unsigned DEFAULT NULL,
  `O_ENTRY_D` datetime DEFAULT NULL,
  `O_CARRIER_ID` int(10) unsigned DEFAULT NULL,
  `O_OL_CNT` int(10) DEFAULT NULL,
  `O_ALL_LOCAL` int(10) DEFAULT NULL,
  PRIMARY KEY (`O_W_ID`,`O_D_ID`,`O_ID`),
  KEY `order_customer_idx` (`O_W_ID`,`O_D_ID`,`O_C_ID`),
  CONSTRAINT `customer` FOREIGN KEY (`O_W_ID`, `O_D_ID`, `O_C_ID`) REFERENCES `customer` (`C_W_ID`, `C_D_ID`, `C_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `order_line` */

DROP TABLE IF EXISTS `order_line`;

CREATE TABLE `order_line` (
  `OL_O_ID` int(10) unsigned NOT NULL,
  `OL_D_ID` int(10) unsigned NOT NULL,
  `OL_W_ID` int(10) unsigned NOT NULL,
  `OL_NUMBER` int(11) NOT NULL,
  `OL_I_ID` int(10) unsigned DEFAULT NULL,
  `OL_SUPPLY_W_ID` int(10) unsigned DEFAULT NULL,
  `OL_DELIVERY_D` datetime DEFAULT NULL,
  `OL_QUANTITY` int(11) DEFAULT NULL,
  `OL_AMOUNT` int(11) DEFAULT NULL,
  `OL_DIST_INFO` varchar(24) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`OL_W_ID`,`OL_D_ID`,`OL_O_ID`,`OL_NUMBER`),
  KEY `order_line_order_idx` (`OL_W_ID`,`OL_D_ID`,`OL_O_ID`),
  KEY `order_line_stock_idx` (`OL_SUPPLY_W_ID`,`OL_I_ID`),
  CONSTRAINT `order_line_order` FOREIGN KEY (`OL_W_ID`, `OL_D_ID`, `OL_O_ID`) REFERENCES `order` (`O_W_ID`, `O_D_ID`, `O_ID`),
  CONSTRAINT `order_line_stock` FOREIGN KEY (`OL_SUPPLY_W_ID`, `OL_I_ID`) REFERENCES `stock` (`S_W_ID`, `S_I_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `stock` */

DROP TABLE IF EXISTS `stock`;

CREATE TABLE `stock` (
  `S_I_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `S_W_ID` int(10) unsigned NOT NULL,
  `S_QUANTITY` int(10) DEFAULT NULL,
  `S_DIST_01` varchar(24) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `S_DIST_02` varchar(24) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `S_DIST_03` varchar(24) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `S_DIST_04` varchar(24) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `S_DIST_05` varchar(24) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `S_DIST_06` varchar(24) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `S_DIST_07` varchar(24) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `S_DIST_08` varchar(24) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `S_DIST_09` varchar(24) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `S_DIST_10` varchar(24) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `S_YTD` decimal(8,0) DEFAULT NULL,
  `S_ORDER_CNT` int(10) DEFAULT NULL,
  `S_REMOTE_CNT` int(10) DEFAULT NULL,
  `S_DATA` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`S_I_ID`,`S_W_ID`),
  KEY `stock_warehouse_idx` (`S_W_ID`),
  KEY `stock_item_idx` (`S_I_ID`),
  CONSTRAINT `stock_item` FOREIGN KEY (`S_I_ID`) REFERENCES `item` (`I_ID`),
  CONSTRAINT `stock_warehouse` FOREIGN KEY (`S_W_ID`) REFERENCES `warehouse` (`W_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=100001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `warehouse` */

DROP TABLE IF EXISTS `warehouse`;

CREATE TABLE `warehouse` (
  `W_ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `W_NAME` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `W_STREET_1` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `W_STREET_2` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `W_CITY` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `W_STATE` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `W_ZIP` varchar(9) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `W_TAX` decimal(4,4) NOT NULL,
  `W_YTD` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`W_ID`),
  UNIQUE KEY `W_ID_UNIQUE` (`W_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
