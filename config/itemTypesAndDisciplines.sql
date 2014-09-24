-- MySQL dump 10.13  Distrib 5.5.31, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: gw2spidy
-- ------------------------------------------------------
-- Server version	5.5.31-0ubuntu0.12.04.1-log

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
-- Dumping data for table `item_type`
--

LOCK TABLES `item_type` WRITE;
/*!40000 ALTER TABLE `item_type` DISABLE KEYS */;
INSERT INTO `item_type` VALUES (0,'Armor'),(2,'Bag'),(3,'Consumable'),(4,'Container'),(5,'Crafting Material'),(6,'Gathering'),(7,'Gizmo'),(11,'Mini'),(13,'Tool'),(15,'Trinket'),(16,'Trophy'),(17,'Upgrade Component'),(18,'Weapon');
/*!40000 ALTER TABLE `item_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `item_sub_type`
--

LOCK TABLES `item_sub_type` WRITE;
/*!40000 ALTER TABLE `item_sub_type` DISABLE KEYS */;
INSERT INTO `item_sub_type` VALUES (0,0,'Coat'),(0,4,'Default'),(0,6,'Foraging'),(0,7,'Default'),(0,13,'Crafting'),(0,15,'Accessory'),(0,17,'Accessory'),(0,18,'Sword'),(1,0,'Leggings'),(1,3,'Food'),(1,4,'Gift Box'),(1,6,'Logging'),(1,15,'Amulet'),(1,18,'Hammer'),(2,0,'Gloves'),(2,3,'Generic'),(2,4,'OpenUI'),(2,6,'Mining'),(2,7,'Salvage'),(2,13,'Salvage'),(2,15,'Ring'),(2,17,'Armor'),(2,18,'Longbow'),(3,0,'Helm'),(3,3,'Food'),(3,7,'RentableContractNpc'),(3,17,'Weapon'),(3,18,'Short Bow'),(4,0,'Aquatic Helm'),(4,3,'Generic'),(4,7,'UnlimitedConsumable'),(4,17,'Sigil'),(4,18,'Axe'),(5,0,'Boots'),(5,3,'Transmutation'),(5,7,'ContainerKey'),(5,17,'Gem'),(5,18,'Dagger'),(6,0,'Shoulders'),(6,3,'Unlock'),(6,17,'Rune'),(6,18,'Greatsword'),(7,3,'Utility'),(7,18,'Mace'),(8,3,'Transmutation'),(8,18,'Pistol'),(9,3,'Unlock'),(10,3,'Halloween'),(10,18,'Rifle'),(11,3,'ContractNpc'),(11,18,'Scepter'),(12,3,'AppearanceChange'),(12,18,'Staff'),(13,3,'Immediate'),(13,18,'Focus'),(14,3,'Booze'),(14,18,'Torch'),(15,3,'UnTransmutation'),(15,18,'Warhorn'),(16,3,'UpgradeRemoval'),(16,18,'Shield'),(19,18,'Spear'),(20,18,'Harpoon Gun'),(21,18,'Trident'),(22,18,'Toy'),(23,18,'LargeBundle'),(24,18,'TwoHandedToy'),(25,18,'SmallBundle');
/*!40000 ALTER TABLE `item_sub_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `discipline`
--

LOCK TABLES `discipline` WRITE;
/*!40000 ALTER TABLE `discipline` DISABLE KEYS */;
INSERT INTO `discipline` VALUES (1,'Huntsman'),(2,'Artificer'),(3,'Weaponsmith'),(4,'Armorsmith'),(5,'Leatherworker'),(6,'Tailor'),(7,'Jeweler'),(8,'Cook');
/*!40000 ALTER TABLE `discipline` ENABLE KEYS */;
UNLOCK TABLES;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-09-23 11:37:31
