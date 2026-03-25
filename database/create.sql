-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: restaurant_booking_v1
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `add_ons`
--

DROP TABLE IF EXISTS `add_ons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `add_ons` (
  `add_on_id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(80) NOT NULL,
  `name` varchar(120) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`add_on_id`),
  UNIQUE KEY `uq_add_ons_category_name` (`category`,`name`),
  CONSTRAINT `ck_add_ons_price_nonnegative` CHECK (`price` >= 0)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `add_ons`
--

LOCK TABLES `add_ons` WRITE;
/*!40000 ALTER TABLE `add_ons` DISABLE KEYS */;
INSERT INTO `add_ons` VALUES (1,'Decor','Floral Table Setup','Fresh floral arrangement for the reserved table',1200.00),(2,'Catering','Custom Dessert Platter','Chef-prepared dessert set for the party',1800.00),(3,'Tech','AV Presentation Setup','Audio and display support for events',2500.00),(4,'Service','Priority Host Assistance','Dedicated host support during service window',900.00);
/*!40000 ALTER TABLE `add_ons` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_add_ons_after_insert
AFTER INSERT ON add_ons
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('add_ons', NEW.add_on_id, 'INSERT', NULL,
    JSON_OBJECT('category', NEW.category, 'name', NEW.name, 'description', NEW.description, 'price', NEW.price));
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_add_ons_after_update
AFTER UPDATE ON add_ons
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('add_ons', NEW.add_on_id, 'UPDATE',
    JSON_OBJECT('category', OLD.category, 'name', OLD.name, 'description', OLD.description, 'price', OLD.price),
    JSON_OBJECT('category', NEW.category, 'name', NEW.name, 'description', NEW.description, 'price', NEW.price));
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_add_ons_after_delete
AFTER DELETE ON add_ons
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('add_ons', OLD.add_on_id, 'DELETE',
    JSON_OBJECT('category', OLD.category, 'name', OLD.name, 'description', OLD.description, 'price', OLD.price), NULL);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `appointment_add_ons`
--

DROP TABLE IF EXISTS `appointment_add_ons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appointment_add_ons` (
  `appointment_id` int(11) NOT NULL,
  `add_on_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`appointment_id`,`add_on_id`),
  KEY `idx_appointment_add_ons_add_on` (`add_on_id`),
  CONSTRAINT `fk_appointment_add_ons_add_on` FOREIGN KEY (`add_on_id`) REFERENCES `add_ons` (`add_on_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_appointment_add_ons_appointment` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`appointment_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ck_appointment_add_ons_quantity_positive` CHECK (`quantity` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appointment_add_ons`
--

LOCK TABLES `appointment_add_ons` WRITE;
/*!40000 ALTER TABLE `appointment_add_ons` DISABLE KEYS */;
INSERT INTO `appointment_add_ons` VALUES (11,2,1),(12,2,1),(14,2,1);
/*!40000 ALTER TABLE `appointment_add_ons` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_appt_add_ons_before_insert_status_check
BEFORE INSERT ON appointment_add_ons
FOR EACH ROW
BEGIN
  DECLARE v_status_id INT;

  SELECT status_id INTO v_status_id
  FROM appointments
  WHERE appointment_id = NEW.appointment_id;

  IF fn_is_terminal_status(v_status_id) = 1 THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Cannot add add-ons to a cancelled, completed, or no-show appointment.';
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
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_appt_add_ons_after_insert
AFTER INSERT ON appointment_add_ons
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('appointment_add_ons', NEW.appointment_id, 'INSERT', NULL,
    JSON_OBJECT('appointment_id', NEW.appointment_id, 'add_on_id', NEW.add_on_id, 'quantity', NEW.quantity));
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_appt_add_ons_after_update
AFTER UPDATE ON appointment_add_ons
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('appointment_add_ons', NEW.appointment_id, 'UPDATE',
    JSON_OBJECT('appointment_id', OLD.appointment_id, 'add_on_id', OLD.add_on_id, 'quantity', OLD.quantity),
    JSON_OBJECT('appointment_id', NEW.appointment_id, 'add_on_id', NEW.add_on_id, 'quantity', NEW.quantity));
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_appt_add_ons_after_delete
AFTER DELETE ON appointment_add_ons
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('appointment_add_ons', OLD.appointment_id, 'DELETE',
    JSON_OBJECT('appointment_id', OLD.appointment_id, 'add_on_id', OLD.add_on_id, 'quantity', OLD.quantity), NULL);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `appointment_audit_logs`
--

DROP TABLE IF EXISTS `appointment_audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appointment_audit_logs` (
  `log_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `appointment_id` int(11) NOT NULL,
  `action_type` varchar(30) NOT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `idx_appt_audit_appt_id` (`appointment_id`,`changed_at`),
  CONSTRAINT `ck_appt_audit_action` CHECK (`action_type` in ('INSERT','UPDATE','STATUS_CHANGE','DELETE'))
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appointment_audit_logs`
--

LOCK TABLES `appointment_audit_logs` WRITE;
/*!40000 ALTER TABLE `appointment_audit_logs` DISABLE KEYS */;
INSERT INTO `appointment_audit_logs` VALUES (1,1,'INSERT',NULL,'{\"user_id\": 2, \"service_id\": 1, \"table_id\": 4, \"zone_id\": null, \"event_package_id\": null, \"appointment_date\": \"2026-04-02\", \"start_time\": \"17:00:00\", \"end_time\": \"19:00:00\", \"party_size\": 4, \"status_id\": 1}','2026-03-24 13:58:06'),(2,2,'INSERT',NULL,'{\"user_id\": 2, \"service_id\": 1, \"table_id\": 13, \"zone_id\": null, \"event_package_id\": null, \"appointment_date\": \"2026-04-02\", \"start_time\": \"20:00:00\", \"end_time\": \"22:00:00\", \"party_size\": 4, \"status_id\": 1}','2026-03-24 13:59:26'),(3,3,'INSERT',NULL,'{\"user_id\": 2, \"service_id\": 2, \"table_id\": null, \"zone_id\": 1, \"event_package_id\": null, \"appointment_date\": \"2026-04-02\", \"start_time\": \"17:00:00\", \"end_time\": \"19:00:00\", \"party_size\": 2, \"status_id\": 1}','2026-03-24 14:05:04'),(4,4,'INSERT',NULL,'{\"user_id\": 2, \"service_id\": null, \"table_id\": null, \"zone_id\": 1, \"event_package_id\": 1, \"appointment_date\": \"2026-03-27\", \"start_time\": \"17:00:00\", \"end_time\": \"19:00:00\", \"party_size\": 4, \"status_id\": 1}','2026-03-24 14:17:39'),(5,5,'INSERT',NULL,'{\"user_id\": 2, \"service_id\": 3, \"table_id\": null, \"zone_id\": 1, \"event_package_id\": null, \"appointment_date\": \"2026-03-31\", \"start_time\": \"17:00:00\", \"end_time\": \"19:00:00\", \"party_size\": 5, \"status_id\": 1}','2026-03-24 14:19:23'),(6,4,'STATUS_CHANGE','{\"user_id\": 2, \"service_id\": null, \"table_id\": null, \"zone_id\": 1, \"event_package_id\": 1, \"appointment_date\": \"2026-03-27\", \"start_time\": \"17:00:00\", \"end_time\": \"19:00:00\", \"party_size\": 4, \"status_id\": 1}','{\"user_id\": 2, \"service_id\": null, \"table_id\": null, \"zone_id\": 1, \"event_package_id\": 1, \"appointment_date\": \"2026-03-27\", \"start_time\": \"17:00:00\", \"end_time\": \"19:00:00\", \"party_size\": 4, \"status_id\": 4}','2026-03-24 14:37:50'),(7,2,'STATUS_CHANGE','{\"user_id\": 2, \"service_id\": 1, \"table_id\": 13, \"zone_id\": null, \"event_package_id\": null, \"appointment_date\": \"2026-04-02\", \"start_time\": \"20:00:00\", \"end_time\": \"22:00:00\", \"party_size\": 4, \"status_id\": 1}','{\"user_id\": 2, \"service_id\": 1, \"table_id\": 13, \"zone_id\": null, \"event_package_id\": null, \"appointment_date\": \"2026-04-02\", \"start_time\": \"20:00:00\", \"end_time\": \"22:00:00\", \"party_size\": 4, \"status_id\": 2}','2026-03-24 14:37:53'),(8,1,'STATUS_CHANGE','{\"user_id\": 2, \"service_id\": 1, \"table_id\": 4, \"zone_id\": null, \"event_package_id\": null, \"appointment_date\": \"2026-04-02\", \"start_time\": \"17:00:00\", \"end_time\": \"19:00:00\", \"party_size\": 4, \"status_id\": 1}','{\"user_id\": 2, \"service_id\": 1, \"table_id\": 4, \"zone_id\": null, \"event_package_id\": null, \"appointment_date\": \"2026-04-02\", \"start_time\": \"17:00:00\", \"end_time\": \"19:00:00\", \"party_size\": 4, \"status_id\": 2}','2026-03-24 14:37:54'),(9,3,'STATUS_CHANGE','{\"user_id\": 2, \"service_id\": 2, \"table_id\": null, \"zone_id\": 1, \"event_package_id\": null, \"appointment_date\": \"2026-04-02\", \"start_time\": \"17:00:00\", \"end_time\": \"19:00:00\", \"party_size\": 2, \"status_id\": 1}','{\"user_id\": 2, \"service_id\": 2, \"table_id\": null, \"zone_id\": 1, \"event_package_id\": null, \"appointment_date\": \"2026-04-02\", \"start_time\": \"17:00:00\", \"end_time\": \"19:00:00\", \"party_size\": 2, \"status_id\": 2}','2026-03-24 14:37:54'),(10,5,'STATUS_CHANGE','{\"user_id\": 2, \"service_id\": 3, \"table_id\": null, \"zone_id\": 1, \"event_package_id\": null, \"appointment_date\": \"2026-03-31\", \"start_time\": \"17:00:00\", \"end_time\": \"19:00:00\", \"party_size\": 5, \"status_id\": 1}','{\"user_id\": 2, \"service_id\": 3, \"table_id\": null, \"zone_id\": 1, \"event_package_id\": null, \"appointment_date\": \"2026-03-31\", \"start_time\": \"17:00:00\", \"end_time\": \"19:00:00\", \"party_size\": 5, \"status_id\": 2}','2026-03-24 14:37:55'),(11,6,'INSERT',NULL,'{\"user_id\": 1, \"service_id\": 2, \"table_id\": null, \"zone_id\": 2, \"event_package_id\": null, \"appointment_date\": \"2026-04-02\", \"start_time\": \"18:30:00\", \"end_time\": \"20:30:00\", \"party_size\": 5, \"status_id\": 1}','2026-03-24 15:08:57'),(12,6,'STATUS_CHANGE','{\"user_id\": 1, \"service_id\": 2, \"table_id\": null, \"zone_id\": 2, \"event_package_id\": null, \"appointment_date\": \"2026-04-02\", \"start_time\": \"18:30:00\", \"end_time\": \"20:30:00\", \"party_size\": 5, \"status_id\": 1}','{\"user_id\": 1, \"service_id\": 2, \"table_id\": null, \"zone_id\": 2, \"event_package_id\": null, \"appointment_date\": \"2026-04-02\", \"start_time\": \"18:30:00\", \"end_time\": \"20:30:00\", \"party_size\": 5, \"status_id\": 2}','2026-03-24 16:37:16'),(13,2,'STATUS_CHANGE','{\"user_id\": 2, \"service_id\": 1, \"table_id\": 13, \"zone_id\": null, \"event_package_id\": null, \"appointment_date\": \"2026-04-02\", \"start_time\": \"20:00:00\", \"end_time\": \"22:00:00\", \"party_size\": 4, \"status_id\": 2}','{\"user_id\": 2, \"service_id\": 1, \"table_id\": 13, \"zone_id\": null, \"event_package_id\": null, \"appointment_date\": \"2026-04-02\", \"start_time\": \"20:00:00\", \"end_time\": \"22:00:00\", \"party_size\": 4, \"status_id\": 4}','2026-03-24 16:37:24'),(14,6,'STATUS_CHANGE','{\"user_id\": 1, \"service_id\": 2, \"table_id\": null, \"zone_id\": 2, \"event_package_id\": null, \"appointment_date\": \"2026-04-02\", \"start_time\": \"18:30:00\", \"end_time\": \"20:30:00\", \"party_size\": 5, \"status_id\": 2}','{\"user_id\": 1, \"service_id\": 2, \"table_id\": null, \"zone_id\": 2, \"event_package_id\": null, \"appointment_date\": \"2026-04-02\", \"start_time\": \"18:30:00\", \"end_time\": \"20:30:00\", \"party_size\": 5, \"status_id\": 4}','2026-03-24 17:12:26'),(15,7,'INSERT',NULL,'{\"user_id\": 18, \"service_id\": 1, \"table_id\": 5, \"zone_id\": null, \"event_package_id\": null, \"appointment_date\": \"2026-03-26\", \"start_time\": \"17:30:00\", \"end_time\": \"19:30:00\", \"party_size\": 4, \"status_id\": 1}','2026-03-24 18:03:52'),(16,8,'INSERT',NULL,'{\"user_id\": 18, \"service_id\": 1, \"table_id\": 7, \"zone_id\": null, \"event_package_id\": null, \"appointment_date\": \"2026-03-26\", \"start_time\": \"19:30:00\", \"end_time\": \"21:30:00\", \"party_size\": 2, \"status_id\": 1}','2026-03-24 18:09:19'),(17,7,'STATUS_CHANGE','{\"user_id\": 18, \"service_id\": 1, \"table_id\": 5, \"zone_id\": null, \"event_package_id\": null, \"appointment_date\": \"2026-03-26\", \"start_time\": \"17:30:00\", \"end_time\": \"19:30:00\", \"party_size\": 4, \"status_id\": 1}','{\"user_id\": 18, \"service_id\": 1, \"table_id\": 5, \"zone_id\": null, \"event_package_id\": null, \"appointment_date\": \"2026-03-26\", \"start_time\": \"17:30:00\", \"end_time\": \"19:30:00\", \"party_size\": 4, \"status_id\": 2}','2026-03-24 18:23:58'),(18,8,'STATUS_CHANGE','{\"user_id\": 18, \"service_id\": 1, \"table_id\": 7, \"zone_id\": null, \"event_package_id\": null, \"appointment_date\": \"2026-03-26\", \"start_time\": \"19:30:00\", \"end_time\": \"21:30:00\", \"party_size\": 2, \"status_id\": 1}','{\"user_id\": 18, \"service_id\": 1, \"table_id\": 7, \"zone_id\": null, \"event_package_id\": null, \"appointment_date\": \"2026-03-26\", \"start_time\": \"19:30:00\", \"end_time\": \"21:30:00\", \"party_size\": 2, \"status_id\": 2}','2026-03-24 18:24:03'),(19,7,'UPDATE','{\"user_id\": 18, \"service_id\": 1, \"table_id\": 5, \"zone_id\": null, \"event_package_id\": null, \"appointment_date\": \"2026-03-26\", \"start_time\": \"17:30:00\", \"end_time\": \"19:30:00\", \"party_size\": 4, \"status_id\": 2}','{\"user_id\": 18, \"service_id\": 1, \"table_id\": 5, \"zone_id\": null, \"event_package_id\": null, \"appointment_date\": \"2026-03-26\", \"start_time\": \"17:30:00\", \"end_time\": \"19:30:00\", \"party_size\": 4, \"status_id\": 2}','2026-03-24 18:24:06'),(20,9,'INSERT',NULL,'{\"user_id\": 18, \"service_id\": 3, \"table_id\": 5, \"zone_id\": null, \"event_package_id\": null, \"appointment_date\": \"2026-04-02\", \"start_time\": \"17:00:00\", \"end_time\": \"19:00:00\", \"party_size\": 4, \"status_id\": 1}','2026-03-25 02:40:55'),(21,3,'STATUS_CHANGE','{\"user_id\": 2, \"service_id\": 2, \"table_id\": null, \"zone_id\": 1, \"event_package_id\": null, \"appointment_date\": \"2026-04-02\", \"start_time\": \"17:00:00\", \"end_time\": \"19:00:00\", \"party_size\": 2, \"status_id\": 2}','{\"user_id\": 2, \"service_id\": 2, \"table_id\": null, \"zone_id\": 1, \"event_package_id\": null, \"appointment_date\": \"2026-04-02\", \"start_time\": \"17:00:00\", \"end_time\": \"19:00:00\", \"party_size\": 2, \"status_id\": 4}','2026-03-25 02:41:23'),(22,11,'INSERT',NULL,'{\"user_id\": 18, \"service_id\": 1, \"table_id\": 8, \"zone_id\": null, \"event_package_id\": null, \"appointment_date\": \"2026-04-02\", \"start_time\": \"17:00:00\", \"end_time\": \"19:00:00\", \"party_size\": 5, \"status_id\": 1}','2026-03-25 05:06:41'),(23,9,'STATUS_CHANGE','{\"user_id\": 18, \"service_id\": 3, \"table_id\": 5, \"zone_id\": null, \"event_package_id\": null, \"appointment_date\": \"2026-04-02\", \"start_time\": \"17:00:00\", \"end_time\": \"19:00:00\", \"party_size\": 4, \"status_id\": 1}','{\"user_id\": 18, \"service_id\": 3, \"table_id\": 5, \"zone_id\": null, \"event_package_id\": null, \"appointment_date\": \"2026-04-02\", \"start_time\": \"17:00:00\", \"end_time\": \"19:00:00\", \"party_size\": 4, \"status_id\": 2}','2026-03-25 05:14:21'),(24,11,'STATUS_CHANGE','{\"user_id\": 18, \"service_id\": 1, \"table_id\": 8, \"zone_id\": null, \"event_package_id\": null, \"appointment_date\": \"2026-04-02\", \"start_time\": \"17:00:00\", \"end_time\": \"19:00:00\", \"party_size\": 5, \"status_id\": 1}','{\"user_id\": 18, \"service_id\": 1, \"table_id\": 8, \"zone_id\": null, \"event_package_id\": null, \"appointment_date\": \"2026-04-02\", \"start_time\": \"17:00:00\", \"end_time\": \"19:00:00\", \"party_size\": 5, \"status_id\": 2}','2026-03-25 05:14:22'),(25,12,'INSERT',NULL,'{\"user_id\": 18, \"service_id\": 1, \"table_id\": 3, \"zone_id\": null, \"event_package_id\": null, \"appointment_date\": \"2026-04-02\", \"start_time\": \"17:00:00\", \"end_time\": \"19:00:00\", \"party_size\": 4, \"status_id\": 1}','2026-03-25 05:27:19'),(26,13,'INSERT',NULL,'{\"user_id\": 4, \"service_id\": 1, \"table_id\": 5, \"zone_id\": null, \"event_package_id\": null, \"appointment_date\": \"2026-03-27\", \"start_time\": \"17:00:00\", \"end_time\": \"19:00:00\", \"party_size\": 6, \"status_id\": 1}','2026-03-25 07:41:43'),(27,14,'INSERT',NULL,'{\"user_id\": 4, \"service_id\": 1, \"table_id\": 5, \"zone_id\": null, \"event_package_id\": null, \"appointment_date\": \"2026-04-02\", \"start_time\": \"19:00:00\", \"end_time\": \"21:00:00\", \"party_size\": 5, \"status_id\": 1}','2026-03-25 08:59:03');
/*!40000 ALTER TABLE `appointment_audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appointment_status`
--

DROP TABLE IF EXISTS `appointment_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appointment_status` (
  `status_id` int(11) NOT NULL AUTO_INCREMENT,
  `status_name` varchar(30) NOT NULL,
  PRIMARY KEY (`status_id`),
  UNIQUE KEY `uq_appointment_status_name` (`status_name`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appointment_status`
--

LOCK TABLES `appointment_status` WRITE;
/*!40000 ALTER TABLE `appointment_status` DISABLE KEYS */;
INSERT INTO `appointment_status` VALUES (4,'cancelled'),(3,'completed'),(2,'confirmed'),(5,'no_show'),(1,'pending');
/*!40000 ALTER TABLE `appointment_status` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_appt_status_after_insert
AFTER INSERT ON appointment_status
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('appointment_status', NEW.status_id, 'INSERT', NULL,
    JSON_OBJECT('status_name', NEW.status_name));
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_appt_status_after_update
AFTER UPDATE ON appointment_status
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('appointment_status', NEW.status_id, 'UPDATE',
    JSON_OBJECT('status_name', OLD.status_name),
    JSON_OBJECT('status_name', NEW.status_name));
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_appt_status_after_delete
AFTER DELETE ON appointment_status
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('appointment_status', OLD.status_id, 'DELETE',
    JSON_OBJECT('status_name', OLD.status_name), NULL);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `appointments`
--

DROP TABLE IF EXISTS `appointments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `service_id` int(11) DEFAULT NULL,
  `table_id` int(11) DEFAULT NULL,
  `zone_id` int(11) DEFAULT NULL,
  `event_package_id` int(11) DEFAULT NULL,
  `appointment_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `party_size` int(11) NOT NULL,
  `special_requests` text DEFAULT NULL,
  `status_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`appointment_id`),
  UNIQUE KEY `uq_appointments_exact_table_slot` (`table_id`,`appointment_date`,`start_time`),
  UNIQUE KEY `uq_appointments_exact_zone_slot` (`zone_id`,`appointment_date`,`start_time`),
  KEY `fk_appointments_status` (`status_id`),
  KEY `idx_appointments_user_date` (`user_id`,`appointment_date`),
  KEY `idx_appointments_date_status` (`appointment_date`,`status_id`),
  KEY `idx_appointments_table_datetime` (`table_id`,`appointment_date`,`start_time`,`end_time`),
  KEY `idx_appointments_zone_datetime` (`zone_id`,`appointment_date`,`start_time`,`end_time`),
  KEY `idx_appointments_created_at` (`created_at`),
  KEY `idx_appointments_service_id` (`service_id`),
  KEY `idx_appointments_event_package_id` (`event_package_id`),
  CONSTRAINT `fk_appointments_event_package` FOREIGN KEY (`event_package_id`) REFERENCES `event_packages` (`package_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_appointments_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_appointments_status` FOREIGN KEY (`status_id`) REFERENCES `appointment_status` (`status_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_appointments_table` FOREIGN KEY (`table_id`) REFERENCES `tables` (`table_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_appointments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_appointments_zone` FOREIGN KEY (`zone_id`) REFERENCES `dining_zones` (`zone_id`) ON UPDATE CASCADE,
  CONSTRAINT `ck_appointments_time_order` CHECK (`end_time` > `start_time`),
  CONSTRAINT `ck_appointments_party_size_positive` CHECK (`party_size` > 0),
  CONSTRAINT `ck_appointments_target_scope` CHECK (`table_id` is not null and `zone_id` is null or `table_id` is null and `zone_id` is not null),
  CONSTRAINT `ck_appointments_bookable_item` CHECK (`service_id` is not null and `event_package_id` is null or `service_id` is null and `event_package_id` is not null)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appointments`
--

LOCK TABLES `appointments` WRITE;
/*!40000 ALTER TABLE `appointments` DISABLE KEYS */;
INSERT INTO `appointments` VALUES (1,2,1,4,NULL,NULL,'2026-04-02','17:00:00','19:00:00',4,NULL,2,'2026-03-24 13:58:06','2026-03-24 14:37:54'),(2,2,1,13,NULL,NULL,'2026-04-02','20:00:00','22:00:00',4,NULL,4,'2026-03-24 13:59:26','2026-03-24 16:37:24'),(3,2,2,NULL,1,NULL,'2026-04-02','17:00:00','19:00:00',2,NULL,4,'2026-03-24 14:05:04','2026-03-25 02:41:23'),(4,2,NULL,NULL,1,1,'2026-03-27','17:00:00','19:00:00',4,NULL,4,'2026-03-24 14:17:39','2026-03-24 14:37:50'),(5,2,3,NULL,1,NULL,'2026-03-31','17:00:00','19:00:00',5,NULL,2,'2026-03-24 14:19:23','2026-03-24 14:37:55'),(6,1,2,NULL,2,NULL,'2026-04-02','18:30:00','20:30:00',5,NULL,4,'2026-03-24 15:08:57','2026-03-24 17:12:26'),(7,18,1,5,NULL,NULL,'2026-03-26','17:30:00','19:30:00',4,'Seating Preference: Fireplace Notes: okii',2,'2026-03-24 18:03:52','2026-03-24 18:24:06'),(8,18,1,7,NULL,NULL,'2026-03-26','19:30:00','21:30:00',2,'Seating Preference: Private Alcove',2,'2026-03-24 18:09:19','2026-03-24 18:24:03'),(9,18,3,5,NULL,NULL,'2026-04-02','17:00:00','19:00:00',4,NULL,2,'2026-03-25 02:40:55','2026-03-25 05:14:21'),(11,18,1,8,NULL,NULL,'2026-04-02','17:00:00','19:00:00',5,NULL,2,'2026-03-25 05:06:41','2026-03-25 05:14:22'),(12,18,1,3,NULL,NULL,'2026-04-02','17:00:00','19:00:00',4,NULL,1,'2026-03-25 05:27:19',NULL),(13,4,1,5,NULL,NULL,'2026-03-27','17:00:00','19:00:00',6,NULL,1,'2026-03-25 07:41:43',NULL),(14,4,1,5,NULL,NULL,'2026-04-02','19:00:00','21:00:00',5,NULL,1,'2026-03-25 08:59:03',NULL);
/*!40000 ALTER TABLE `appointments` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_appointments_before_insert_capacity
BEFORE INSERT ON appointments
FOR EACH ROW
BEGIN
  -- Only validate when a specific table is booked (table_id IS NOT NULL)
  IF NEW.table_id IS NOT NULL THEN
    IF fn_party_fits_table(NEW.table_id, NEW.party_size) = 0 THEN
      SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Party size exceeds table capacity.';
    END IF;
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
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_appointments_before_insert_overlap
BEFORE INSERT ON appointments
FOR EACH ROW
BEGIN
  -- Check table-level overlap
  IF NEW.table_id IS NOT NULL THEN
    IF fn_table_has_conflict(NEW.table_id, NEW.appointment_date, NEW.start_time, NEW.end_time, NULL) = 1 THEN
      SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Time slot conflicts with an existing table booking.';
    END IF;
  END IF;

  -- Check zone-level overlap
  IF NEW.zone_id IS NOT NULL THEN
    IF fn_zone_has_conflict(NEW.zone_id, NEW.appointment_date, NEW.start_time, NEW.end_time, NULL) = 1 THEN
      SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Time slot conflicts with an existing zone booking.';
    END IF;
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
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_appointments_before_insert_past_date
BEFORE INSERT ON appointments
FOR EACH ROW
BEGIN
  IF fn_is_past_datetime(NEW.appointment_date, NEW.start_time) = 1 THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Cannot create an appointment in the past.';
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
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_appointments_before_insert_service_package
BEFORE INSERT ON appointments
FOR EACH ROW
BEGIN
  IF (NEW.service_id IS NULL AND NEW.event_package_id IS NULL)
     OR (NEW.service_id IS NOT NULL AND NEW.event_package_id IS NOT NULL)
  THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Select either a service or an event package (not both).';
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
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_appointments_before_insert_max_active
BEFORE INSERT ON appointments
FOR EACH ROW
BEGIN
  DECLARE v_max_active INT DEFAULT 5;

  IF fn_can_book_more(NEW.user_id, v_max_active) = 0 THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Maximum active bookings reached (limit: 5). Please complete or cancel an existing booking first.';
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
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_appointments_after_insert
AFTER INSERT ON appointments
FOR EACH ROW
BEGIN
  INSERT INTO appointment_audit_logs (appointment_id, action_type, old_values, new_values)
  VALUES (
    NEW.appointment_id,
    'INSERT',
    NULL,
    JSON_OBJECT(
      'user_id',          NEW.user_id,
      'service_id',       NEW.service_id,
      'table_id',         NEW.table_id,
      'zone_id',          NEW.zone_id,
      'event_package_id', NEW.event_package_id,
      'appointment_date', NEW.appointment_date,
      'start_time',       NEW.start_time,
      'end_time',         NEW.end_time,
      'party_size',       NEW.party_size,
      'status_id',        NEW.status_id
    )
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
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_appointments_before_update_timestamp
BEFORE UPDATE ON appointments
FOR EACH ROW
BEGIN
  SET NEW.updated_at = NOW();
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_appointments_before_update_capacity
BEFORE UPDATE ON appointments
FOR EACH ROW
BEGIN
  -- Re-validate only if party_size or table_id changed
  IF NEW.table_id IS NOT NULL
     AND (NEW.party_size <> OLD.party_size OR NOT (NEW.table_id <=> OLD.table_id))
  THEN
    IF fn_party_fits_table(NEW.table_id, NEW.party_size) = 0 THEN
      SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Party size exceeds table capacity.';
    END IF;
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
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_appointments_before_update_overlap
BEFORE UPDATE ON appointments
FOR EACH ROW
BEGIN
  -- Only re-check if scheduling fields or location changed
  IF NOT (NEW.table_id <=> OLD.table_id)
     OR NOT (NEW.zone_id <=> OLD.zone_id)
     OR NEW.appointment_date <> OLD.appointment_date
     OR NEW.start_time <> OLD.start_time
     OR NEW.end_time <> OLD.end_time
  THEN
    -- Check table-level overlap (exclude self)
    IF NEW.table_id IS NOT NULL THEN
      IF fn_table_has_conflict(NEW.table_id, NEW.appointment_date, NEW.start_time, NEW.end_time, NEW.appointment_id) = 1 THEN
        SIGNAL SQLSTATE '45000'
          SET MESSAGE_TEXT = 'Time slot conflicts with an existing table booking.';
      END IF;
    END IF;

    -- Check zone-level overlap (exclude self)
    IF NEW.zone_id IS NOT NULL THEN
      IF fn_zone_has_conflict(NEW.zone_id, NEW.appointment_date, NEW.start_time, NEW.end_time, NEW.appointment_id) = 1 THEN
        SIGNAL SQLSTATE '45000'
          SET MESSAGE_TEXT = 'Time slot conflicts with an existing zone booking.';
      END IF;
    END IF;
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
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_appointments_before_update_past_date
BEFORE UPDATE ON appointments
FOR EACH ROW
BEGIN
  -- Only enforce if the date/time fields are being changed
  IF NEW.appointment_date <> OLD.appointment_date
     OR NEW.start_time <> OLD.start_time
  THEN
    IF fn_is_past_datetime(NEW.appointment_date, NEW.start_time) = 1 THEN
      SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot reschedule an appointment to a past date.';
    END IF;
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
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_appointments_before_update_service_package
BEFORE UPDATE ON appointments
FOR EACH ROW
BEGIN
  IF (NEW.service_id IS NULL AND NEW.event_package_id IS NULL)
     OR (NEW.service_id IS NOT NULL AND NEW.event_package_id IS NOT NULL)
  THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Select either a service or an event package (not both).';
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
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_appointments_before_update_status_flow
BEFORE UPDATE ON appointments
FOR EACH ROW
BEGIN
  -- Only check when status_id actually changes
  IF OLD.status_id <> NEW.status_id THEN
    IF fn_is_valid_status_transition(OLD.status_id, NEW.status_id) = 0 THEN
      SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Invalid status transition.';
    END IF;
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
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_appointments_before_update_max_active
BEFORE UPDATE ON appointments
FOR EACH ROW
BEGIN
  DECLARE v_max_active INT DEFAULT 5;

  -- Only check when user_id changes OR status is changing TO an active status
  IF NEW.user_id <> OLD.user_id OR OLD.status_id <> NEW.status_id THEN
    -- Only enforce if the new status is active (pending/confirmed)
    IF fn_is_active_status(NEW.status_id) = 1 THEN
      IF fn_user_active_booking_count(NEW.user_id) >= v_max_active THEN
        SIGNAL SQLSTATE '45000'
          SET MESSAGE_TEXT = 'Maximum active bookings reached (limit: 5). Please complete or cancel an existing booking first.';
      END IF;
    END IF;
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
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_appointments_after_update
AFTER UPDATE ON appointments
FOR EACH ROW
BEGIN
  DECLARE v_action VARCHAR(30);

  -- Distinguish status-only changes from general updates
  IF OLD.status_id <> NEW.status_id
     AND OLD.user_id = NEW.user_id
     AND OLD.service_id <=> NEW.service_id
     AND OLD.table_id <=> NEW.table_id
     AND OLD.zone_id <=> NEW.zone_id
     AND OLD.event_package_id <=> NEW.event_package_id
     AND OLD.appointment_date = NEW.appointment_date
     AND OLD.start_time = NEW.start_time
     AND OLD.end_time = NEW.end_time
     AND OLD.party_size = NEW.party_size
  THEN
    SET v_action = 'STATUS_CHANGE';
  ELSE
    SET v_action = 'UPDATE';
  END IF;

  INSERT INTO appointment_audit_logs (appointment_id, action_type, old_values, new_values)
  VALUES (
    NEW.appointment_id,
    v_action,
    JSON_OBJECT(
      'user_id',          OLD.user_id,
      'service_id',       OLD.service_id,
      'table_id',         OLD.table_id,
      'zone_id',          OLD.zone_id,
      'event_package_id', OLD.event_package_id,
      'appointment_date', OLD.appointment_date,
      'start_time',       OLD.start_time,
      'end_time',         OLD.end_time,
      'party_size',       OLD.party_size,
      'status_id',        OLD.status_id
    ),
    JSON_OBJECT(
      'user_id',          NEW.user_id,
      'service_id',       NEW.service_id,
      'table_id',         NEW.table_id,
      'zone_id',          NEW.zone_id,
      'event_package_id', NEW.event_package_id,
      'appointment_date', NEW.appointment_date,
      'start_time',       NEW.start_time,
      'end_time',         NEW.end_time,
      'party_size',       NEW.party_size,
      'status_id',        NEW.status_id
    )
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
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_appointments_before_delete_guard
BEFORE DELETE ON appointments
FOR EACH ROW
BEGIN
  IF fn_status_name(OLD.status_id) = 'confirmed' THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Cannot delete a confirmed appointment. Cancel it first.';
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
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_appointments_after_delete
AFTER DELETE ON appointments
FOR EACH ROW
BEGIN
  INSERT INTO appointment_audit_logs (appointment_id, action_type, old_values, new_values)
  VALUES (
    OLD.appointment_id,
    'DELETE',
    JSON_OBJECT(
      'user_id',          OLD.user_id,
      'service_id',       OLD.service_id,
      'table_id',         OLD.table_id,
      'zone_id',          OLD.zone_id,
      'event_package_id', OLD.event_package_id,
      'appointment_date', OLD.appointment_date,
      'start_time',       OLD.start_time,
      'end_time',         OLD.end_time,
      'party_size',       OLD.party_size,
      'status_id',        OLD.status_id
    ),
    NULL
  );
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `dining_zones`
--

DROP TABLE IF EXISTS `dining_zones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dining_zones` (
  `zone_id` int(11) NOT NULL AUTO_INCREMENT,
  `zone_name` varchar(100) NOT NULL,
  PRIMARY KEY (`zone_id`),
  UNIQUE KEY `uq_dining_zones_name` (`zone_name`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dining_zones`
--

LOCK TABLES `dining_zones` WRITE;
/*!40000 ALTER TABLE `dining_zones` DISABLE KEYS */;
INSERT INTO `dining_zones` VALUES (1,'Main Dining Room'),(3,'The Bar'),(2,'The Patio');
/*!40000 ALTER TABLE `dining_zones` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_dining_zones_after_insert
AFTER INSERT ON dining_zones
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('dining_zones', NEW.zone_id, 'INSERT', NULL,
    JSON_OBJECT('zone_name', NEW.zone_name));
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_dining_zones_after_update
AFTER UPDATE ON dining_zones
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('dining_zones', NEW.zone_id, 'UPDATE',
    JSON_OBJECT('zone_name', OLD.zone_name),
    JSON_OBJECT('zone_name', NEW.zone_name));
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_dining_zones_after_delete
AFTER DELETE ON dining_zones
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('dining_zones', OLD.zone_id, 'DELETE',
    JSON_OBJECT('zone_name', OLD.zone_name), NULL);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `event_packages`
--

DROP TABLE IF EXISTS `event_packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event_packages` (
  `package_id` int(11) NOT NULL AUTO_INCREMENT,
  `package_name` varchar(120) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `base_price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`package_id`),
  UNIQUE KEY `uq_event_packages_name` (`package_name`),
  CONSTRAINT `ck_event_packages_base_price_nonnegative` CHECK (`base_price` >= 0)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_packages`
--

LOCK TABLES `event_packages` WRITE;
/*!40000 ALTER TABLE `event_packages` DISABLE KEYS */;
INSERT INTO `event_packages` VALUES (1,'Birthday Package','Decor setup and celebration assistance',5000.00),(2,'Anniversary Package','Premium table styling and curated dining experience',6500.00),(3,'Corporate Package','Group seating layout and service coordination',8000.00);
/*!40000 ALTER TABLE `event_packages` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_event_packages_after_insert
AFTER INSERT ON event_packages
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('event_packages', NEW.package_id, 'INSERT', NULL,
    JSON_OBJECT('package_name', NEW.package_name, 'description', NEW.description, 'base_price', NEW.base_price));
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_event_packages_after_update
AFTER UPDATE ON event_packages
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('event_packages', NEW.package_id, 'UPDATE',
    JSON_OBJECT('package_name', OLD.package_name, 'description', OLD.description, 'base_price', OLD.base_price),
    JSON_OBJECT('package_name', NEW.package_name, 'description', NEW.description, 'base_price', NEW.base_price));
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_event_packages_after_delete
AFTER DELETE ON event_packages
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('event_packages', OLD.package_id, 'DELETE',
    JSON_OBJECT('package_name', OLD.package_name, 'description', OLD.description, 'base_price', OLD.base_price), NULL);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `general_audit_logs`
--

DROP TABLE IF EXISTS `general_audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `general_audit_logs` (
  `log_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(64) NOT NULL,
  `record_id` int(11) NOT NULL,
  `action_type` varchar(30) NOT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `idx_gen_audit_table_record` (`table_name`,`record_id`,`changed_at`),
  CONSTRAINT `ck_gen_audit_action` CHECK (`action_type` in ('INSERT','UPDATE','DELETE'))
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `general_audit_logs`
--

LOCK TABLES `general_audit_logs` WRITE;
/*!40000 ALTER TABLE `general_audit_logs` DISABLE KEYS */;
INSERT INTO `general_audit_logs` VALUES (1,'roles',4,'INSERT',NULL,'{\"role_name\": \"customer\", \"permissions_description\": \"Customer account access\"}','2026-03-23 10:32:40'),(2,'tables',1,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','2026-03-24 17:48:01'),(3,'tables',2,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 2\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 2\", \"capacity\": 2}','2026-03-24 17:48:01'),(4,'tables',3,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 3\", \"capacity\": 4}','{\"zone_id\": 1, \"table_number\": \"Table 3\", \"capacity\": 4}','2026-03-24 17:48:01'),(5,'tables',4,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 4\", \"capacity\": 4}','{\"zone_id\": 1, \"table_number\": \"Table 4\", \"capacity\": 4}','2026-03-24 17:48:01'),(6,'tables',5,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 5\", \"capacity\": 6}','{\"zone_id\": 1, \"table_number\": \"Table 5\", \"capacity\": 6}','2026-03-24 17:48:01'),(7,'tables',6,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 6\", \"capacity\": 4}','{\"zone_id\": 1, \"table_number\": \"Table 6\", \"capacity\": 4}','2026-03-24 17:48:01'),(8,'tables',7,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 7\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 7\", \"capacity\": 2}','2026-03-24 17:48:01'),(9,'tables',8,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 8\", \"capacity\": 8}','{\"zone_id\": 1, \"table_number\": \"Table 8\", \"capacity\": 8}','2026-03-24 17:48:01'),(10,'tables',9,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 9\", \"capacity\": 4}','{\"zone_id\": 1, \"table_number\": \"Table 9\", \"capacity\": 4}','2026-03-24 17:48:01'),(11,'tables',10,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 10\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 10\", \"capacity\": 2}','2026-03-24 17:48:01'),(12,'tables',11,'UPDATE','{\"zone_id\": 2, \"table_number\": \"Garden 1\", \"capacity\": 2}','{\"zone_id\": 2, \"table_number\": \"Garden 1\", \"capacity\": 2}','2026-03-24 17:48:01'),(13,'tables',12,'UPDATE','{\"zone_id\": 2, \"table_number\": \"Garden 2\", \"capacity\": 4}','{\"zone_id\": 2, \"table_number\": \"Garden 2\", \"capacity\": 4}','2026-03-24 17:48:01'),(14,'tables',13,'UPDATE','{\"zone_id\": 2, \"table_number\": \"Fountain\", \"capacity\": 4}','{\"zone_id\": 2, \"table_number\": \"Fountain\", \"capacity\": 4}','2026-03-24 17:48:01'),(15,'tables',14,'UPDATE','{\"zone_id\": 2, \"table_number\": \"Pergola\", \"capacity\": 6}','{\"zone_id\": 2, \"table_number\": \"Pergola\", \"capacity\": 6}','2026-03-24 17:48:01'),(16,'tables',15,'UPDATE','{\"zone_id\": 2, \"table_number\": \"Corner\", \"capacity\": 4}','{\"zone_id\": 2, \"table_number\": \"Corner\", \"capacity\": 4}','2026-03-24 17:48:01'),(17,'tables',16,'UPDATE','{\"zone_id\": 3, \"table_number\": \"Bar 1\", \"capacity\": 2}','{\"zone_id\": 3, \"table_number\": \"Bar 1\", \"capacity\": 2}','2026-03-24 17:48:01'),(18,'tables',17,'UPDATE','{\"zone_id\": 3, \"table_number\": \"Bar 2\", \"capacity\": 2}','{\"zone_id\": 3, \"table_number\": \"Bar 2\", \"capacity\": 2}','2026-03-24 17:48:01'),(19,'tables',18,'UPDATE','{\"zone_id\": 3, \"table_number\": \"High Top 1\", \"capacity\": 4}','{\"zone_id\": 3, \"table_number\": \"High Top 1\", \"capacity\": 4}','2026-03-24 17:48:01'),(20,'tables',19,'UPDATE','{\"zone_id\": 3, \"table_number\": \"High Top 2\", \"capacity\": 4}','{\"zone_id\": 3, \"table_number\": \"High Top 2\", \"capacity\": 4}','2026-03-24 17:48:01'),(21,'tables',20,'UPDATE','{\"zone_id\": 3, \"table_number\": \"Lounge\", \"capacity\": 6}','{\"zone_id\": 3, \"table_number\": \"Lounge\", \"capacity\": 6}','2026-03-24 17:48:01'),(22,'services',3,'UPDATE','{\"service_name\": \"Celebration Setup\", \"price\": 850.00}','{\"service_name\": \"Celebration Setup\", \"price\": 850.00}','2026-03-25 03:06:03'),(23,'services',3,'UPDATE','{\"service_name\": \"Celebration Setup\", \"price\": 850.00}','{\"service_name\": \"Celebration Setup\", \"price\": 342.00}','2026-03-25 03:06:07'),(24,'services',3,'UPDATE','{\"service_name\": \"Celebration Setup\", \"price\": 342.00}','{\"service_name\": \"Celebration Setup\", \"price\": 342.00}','2026-03-25 03:06:07'),(25,'add_ons',2,'UPDATE','{\"category\": \"Catering\", \"name\": \"Custom Dessert Platter\", \"description\": \"Chef-prepared dessert set for the party\", \"price\": 1800.00}','{\"category\": \"Catering\", \"name\": \"Custom Dessert Platter\", \"description\": \"Chef-prepared dessert set for the party\", \"price\": 1800.00}','2026-03-25 03:12:40'),(26,'appointment_add_ons',11,'INSERT',NULL,'{\"appointment_id\": 11, \"add_on_id\": 2, \"quantity\": 1}','2026-03-25 05:06:41'),(27,'appointment_add_ons',12,'INSERT',NULL,'{\"appointment_id\": 12, \"add_on_id\": 2, \"quantity\": 1}','2026-03-25 05:27:19'),(28,'tables',20,'UPDATE','{\"zone_id\": 3, \"table_number\": \"Lounge\", \"capacity\": 6}','{\"zone_id\": 3, \"table_number\": \"Lounge area\", \"capacity\": 6}','2026-03-25 05:30:11'),(29,'tables',20,'UPDATE','{\"zone_id\": 3, \"table_number\": \"Lounge area\", \"capacity\": 6}','{\"zone_id\": 3, \"table_number\": \"Lounge\", \"capacity\": 6}','2026-03-25 05:34:11'),(30,'add_ons',23,'INSERT',NULL,'{\"category\": \"Tech\", \"name\": \"Speaker Modifiers\", \"description\": \"Modify Speakers\", \"price\": 1800.00}','2026-03-25 05:34:53'),(31,'add_ons',23,'DELETE','{\"category\": \"Tech\", \"name\": \"Speaker Modifiers\", \"description\": \"Modify Speakers\", \"price\": 1800.00}',NULL,'2026-03-25 05:34:59'),(32,'appointment_add_ons',14,'INSERT',NULL,'{\"appointment_id\": 14, \"add_on_id\": 2, \"quantity\": 1}','2026-03-25 08:59:03'),(33,'tables',1,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','2026-03-25 09:18:59'),(34,'tables',1,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','2026-03-25 09:19:00'),(35,'tables',1,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','2026-03-25 09:19:00'),(36,'tables',1,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','2026-03-25 09:19:01'),(37,'tables',1,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','2026-03-25 09:23:09'),(38,'tables',1,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','2026-03-25 09:23:10'),(39,'tables',3,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 3\", \"capacity\": 4}','{\"zone_id\": 1, \"table_number\": \"Table 3\", \"capacity\": 4}','2026-03-25 09:23:19'),(40,'tables',3,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 3\", \"capacity\": 4}','{\"zone_id\": 1, \"table_number\": \"Table 3\", \"capacity\": 4}','2026-03-25 09:23:19'),(41,'tables',10,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 10\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 10\", \"capacity\": 2}','2026-03-25 09:23:43'),(42,'tables',10,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 10\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 10\", \"capacity\": 2}','2026-03-25 09:23:43'),(43,'tables',1,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','2026-03-25 09:28:38'),(44,'tables',1,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','2026-03-25 09:28:39'),(45,'tables',1,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','2026-03-25 09:28:39'),(46,'tables',1,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','2026-03-25 09:28:39'),(47,'tables',1,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','2026-03-25 09:28:40'),(48,'tables',1,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','2026-03-25 09:28:40'),(49,'tables',1,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','2026-03-25 09:28:40'),(50,'tables',1,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','2026-03-25 09:28:41'),(51,'tables',1,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','2026-03-25 09:28:41'),(52,'tables',1,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','2026-03-25 09:28:41'),(53,'tables',1,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','2026-03-25 09:28:41'),(54,'tables',1,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','2026-03-25 09:28:41'),(55,'tables',1,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','2026-03-25 09:28:41'),(56,'tables',1,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','2026-03-25 09:28:41'),(57,'tables',1,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','2026-03-25 09:28:42'),(58,'tables',1,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','2026-03-25 09:28:42'),(59,'tables',1,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','2026-03-25 09:28:43'),(60,'tables',1,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','2026-03-25 09:28:43'),(61,'tables',1,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','2026-03-25 09:33:32'),(62,'tables',1,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','2026-03-25 09:33:33'),(63,'tables',1,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','2026-03-25 09:33:33'),(64,'tables',1,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','2026-03-25 09:33:34'),(65,'tables',1,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 1\", \"capacity\": 2}','2026-03-25 09:44:23'),(66,'tables',2,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 2\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 2\", \"capacity\": 2}','2026-03-25 09:44:23'),(67,'tables',3,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 3\", \"capacity\": 4}','{\"zone_id\": 1, \"table_number\": \"Table 3\", \"capacity\": 4}','2026-03-25 09:44:23'),(68,'tables',4,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 4\", \"capacity\": 4}','{\"zone_id\": 1, \"table_number\": \"Table 4\", \"capacity\": 4}','2026-03-25 09:44:23'),(69,'tables',5,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 5\", \"capacity\": 6}','{\"zone_id\": 1, \"table_number\": \"Table 5\", \"capacity\": 6}','2026-03-25 09:44:23'),(70,'tables',6,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 6\", \"capacity\": 4}','{\"zone_id\": 1, \"table_number\": \"Table 6\", \"capacity\": 4}','2026-03-25 09:44:23'),(71,'tables',7,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 7\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 7\", \"capacity\": 2}','2026-03-25 09:44:23'),(72,'tables',8,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 8\", \"capacity\": 8}','{\"zone_id\": 1, \"table_number\": \"Table 8\", \"capacity\": 8}','2026-03-25 09:44:23'),(73,'tables',9,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 9\", \"capacity\": 4}','{\"zone_id\": 1, \"table_number\": \"Table 9\", \"capacity\": 4}','2026-03-25 09:44:23'),(74,'tables',10,'UPDATE','{\"zone_id\": 1, \"table_number\": \"Table 10\", \"capacity\": 2}','{\"zone_id\": 1, \"table_number\": \"Table 10\", \"capacity\": 2}','2026-03-25 09:44:23'),(75,'tables',11,'UPDATE','{\"zone_id\": 2, \"table_number\": \"Garden 1\", \"capacity\": 2}','{\"zone_id\": 2, \"table_number\": \"Garden 1\", \"capacity\": 2}','2026-03-25 09:44:23'),(76,'tables',12,'UPDATE','{\"zone_id\": 2, \"table_number\": \"Garden 2\", \"capacity\": 4}','{\"zone_id\": 2, \"table_number\": \"Garden 2\", \"capacity\": 4}','2026-03-25 09:44:23'),(77,'tables',13,'UPDATE','{\"zone_id\": 2, \"table_number\": \"Fountain\", \"capacity\": 4}','{\"zone_id\": 2, \"table_number\": \"Fountain\", \"capacity\": 4}','2026-03-25 09:44:23'),(78,'tables',14,'UPDATE','{\"zone_id\": 2, \"table_number\": \"Pergola\", \"capacity\": 6}','{\"zone_id\": 2, \"table_number\": \"Pergola\", \"capacity\": 6}','2026-03-25 09:44:23'),(79,'tables',15,'UPDATE','{\"zone_id\": 2, \"table_number\": \"Corner\", \"capacity\": 4}','{\"zone_id\": 2, \"table_number\": \"Corner\", \"capacity\": 4}','2026-03-25 09:44:23'),(80,'tables',16,'UPDATE','{\"zone_id\": 3, \"table_number\": \"Bar 1\", \"capacity\": 2}','{\"zone_id\": 3, \"table_number\": \"Bar 1\", \"capacity\": 2}','2026-03-25 09:44:23'),(81,'tables',17,'UPDATE','{\"zone_id\": 3, \"table_number\": \"Bar 2\", \"capacity\": 2}','{\"zone_id\": 3, \"table_number\": \"Bar 2\", \"capacity\": 2}','2026-03-25 09:44:23'),(82,'tables',18,'UPDATE','{\"zone_id\": 3, \"table_number\": \"High Top 1\", \"capacity\": 4}','{\"zone_id\": 3, \"table_number\": \"High Top 1\", \"capacity\": 4}','2026-03-25 09:44:23'),(83,'tables',19,'UPDATE','{\"zone_id\": 3, \"table_number\": \"High Top 2\", \"capacity\": 4}','{\"zone_id\": 3, \"table_number\": \"High Top 2\", \"capacity\": 4}','2026-03-25 09:44:23'),(84,'tables',20,'UPDATE','{\"zone_id\": 3, \"table_number\": \"Lounge\", \"capacity\": 6}','{\"zone_id\": 3, \"table_number\": \"Lounge\", \"capacity\": 6}','2026-03-25 09:44:23'),(85,'tables',10,'UPDATE','{\"zone_id\": 1, \"table_label\": \"Banquette\", \"capacity\": 2}','{\"zone_id\": 1, \"table_label\": \"Banquette\", \"capacity\": 2}','2026-03-25 09:57:45'),(86,'tables',3,'UPDATE','{\"zone_id\": 1, \"table_label\": \"Banquette\", \"capacity\": 4}','{\"zone_id\": 1, \"table_label\": \"Banquette\", \"capacity\": 4}','2026-03-25 09:57:55');
/*!40000 ALTER TABLE `general_audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  `permissions_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `uq_roles_role_name` (`role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'admin','Full access to system administration and management'),(2,'staff','Manage reservations and customer details'),(3,'guest','Customer account access'),(4,'customer','Customer account access');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_roles_after_insert
AFTER INSERT ON roles
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('roles', NEW.role_id, 'INSERT', NULL,
    JSON_OBJECT('role_name', NEW.role_name, 'permissions_description', NEW.permissions_description));
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_roles_after_update
AFTER UPDATE ON roles
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('roles', NEW.role_id, 'UPDATE',
    JSON_OBJECT('role_name', OLD.role_name, 'permissions_description', OLD.permissions_description),
    JSON_OBJECT('role_name', NEW.role_name, 'permissions_description', NEW.permissions_description));
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_roles_after_delete
AFTER DELETE ON roles
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('roles', OLD.role_id, 'DELETE',
    JSON_OBJECT('role_name', OLD.role_name, 'permissions_description', OLD.permissions_description), NULL);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `services`
--

DROP TABLE IF EXISTS `services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `services` (
  `service_id` int(11) NOT NULL AUTO_INCREMENT,
  `service_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`service_id`),
  UNIQUE KEY `uq_services_name` (`service_name`),
  CONSTRAINT `ck_services_price_nonnegative` CHECK (`price` >= 0)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `services`
--

LOCK TABLES `services` WRITE;
/*!40000 ALTER TABLE `services` DISABLE KEYS */;
INSERT INTO `services` VALUES (1,'Table Reservation',0.00),(2,'Private Dining Service',1500.00),(3,'Celebration Setup',342.00);
/*!40000 ALTER TABLE `services` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_services_after_insert
AFTER INSERT ON services
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('services', NEW.service_id, 'INSERT', NULL,
    JSON_OBJECT('service_name', NEW.service_name, 'price', NEW.price));
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_services_after_update
AFTER UPDATE ON services
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('services', NEW.service_id, 'UPDATE',
    JSON_OBJECT('service_name', OLD.service_name, 'price', OLD.price),
    JSON_OBJECT('service_name', NEW.service_name, 'price', NEW.price));
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_services_after_delete
AFTER DELETE ON services
FOR EACH ROW
BEGIN
  INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
  VALUES ('services', OLD.service_id, 'DELETE',
    JSON_OBJECT('service_name', OLD.service_name, 'price', OLD.price), NULL);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `system_settings`
--

DROP TABLE IF EXISTS `system_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_settings` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`setting_id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_settings`
--

LOCK TABLES `system_settings` WRITE;
/*!40000 ALTER TABLE `system_settings` DISABLE KEYS */;
INSERT INTO `system_settings` VALUES (1,'restaurant_name','Eudaimonia','2026-03-25 05:41:30'),(2,'restaurant_email','hello@eudaimonia.com','2026-03-25 05:41:30'),(3,'restaurant_phone','+1 (555) 000-1234','2026-03-25 05:41:30'),(4,'restaurant_address','12 Harmony Lane, New York, NY','2026-03-25 05:41:30'),(5,'restaurant_description','A contemporary dining experience rooted in timeless hospitality.','2026-03-25 05:41:30'),(6,'notify_new_reservation','1','2026-03-25 05:41:30'),(7,'notify_cancellation','1','2026-03-25 05:51:49'),(8,'notify_daily_summary','0','2026-03-25 05:41:30'),(9,'maintenance_mode','0','2026-03-25 05:41:30'),(10,'maintenance_message','We\'re temporarily offline for maintenance. Please check back shortly.','2026-03-25 05:41:30');
/*!40000 ALTER TABLE `system_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tables`
--

DROP TABLE IF EXISTS `tables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tables` (
  `table_id` int(11) NOT NULL AUTO_INCREMENT,
  `zone_id` int(11) NOT NULL,
  `capacity` int(11) NOT NULL,
  `seating_preference` varchar(100) NOT NULL,
  `current_status` enum('available','reserved','occupied') NOT NULL DEFAULT 'available',
  PRIMARY KEY (`table_id`),
  KEY `idx_tables_zone_capacity` (`zone_id`,`capacity`),
  KEY `idx_tables_seating_preference` (`seating_preference`),
  CONSTRAINT `fk_tables_zone` FOREIGN KEY (`zone_id`) REFERENCES `dining_zones` (`zone_id`) ON UPDATE CASCADE,
  CONSTRAINT `ck_tables_capacity_positive` CHECK (`capacity` > 0)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tables`
--

LOCK TABLES `tables` WRITE;
/*!40000 ALTER TABLE `tables` DISABLE KEYS */;
INSERT INTO `tables` VALUES (1,1,2,'Window Table','available'),(2,1,2,'Window Table','available'),(3,1,4,'Banquette','occupied'),(4,1,4,'Banquette','available'),(5,1,6,'Fireplace','available'),(6,1,4,'Chef\'s View','available'),(7,1,2,'Private Alcove','available'),(8,1,8,'Chandelier','available'),(9,1,4,'Window Table','available'),(10,1,2,'Banquette','occupied'),(11,2,2,'Garden View','available'),(12,2,4,'Garden View','available'),(13,2,4,'Fountain Side','available'),(14,2,6,'Pergola','available'),(15,2,4,'Corner Alcove','available'),(16,3,2,'Bar Counter','available'),(17,3,2,'Bar Counter','available'),(18,3,4,'High Tops','available'),(19,3,4,'High Tops','available'),(20,3,6,'Lounge Booths','available');
/*!40000 ALTER TABLE `tables` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_tables_after_insert
         AFTER INSERT ON `tables`
         FOR EACH ROW
         BEGIN
           INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
           VALUES ('tables', NEW.table_id, 'INSERT', NULL,
             JSON_OBJECT('zone_id', NEW.zone_id, 'table_label', NEW.seating_preference, 'capacity', NEW.capacity));
         END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_tables_after_update
         AFTER UPDATE ON `tables`
         FOR EACH ROW
         BEGIN
           INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
           VALUES ('tables', NEW.table_id, 'UPDATE',
             JSON_OBJECT('zone_id', OLD.zone_id, 'table_label', OLD.seating_preference, 'capacity', OLD.capacity),
             JSON_OBJECT('zone_id', NEW.zone_id, 'table_label', NEW.seating_preference, 'capacity', NEW.capacity));
         END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_tables_after_delete
         AFTER DELETE ON `tables`
         FOR EACH ROW
         BEGIN
           INSERT INTO general_audit_logs (table_name, record_id, action_type, old_values, new_values)
           VALUES ('tables', OLD.table_id, 'DELETE',
             JSON_OBJECT('zone_id', OLD.zone_id, 'table_label', OLD.seating_preference, 'capacity', OLD.capacity), NULL);
         END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `user_audit_logs`
--

DROP TABLE IF EXISTS `user_audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_audit_logs` (
  `log_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `target_user_id` int(11) NOT NULL,
  `actor_user_id` int(11) NOT NULL,
  `action_type` varchar(30) NOT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`log_id`),
  KEY `idx_user_audit_logs_target_created` (`target_user_id`,`created_at`),
  KEY `idx_user_audit_logs_actor_created` (`actor_user_id`,`created_at`),
  CONSTRAINT `fk_user_audit_logs_actor` FOREIGN KEY (`actor_user_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_user_audit_logs_target` FOREIGN KEY (`target_user_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE,
  CONSTRAINT `ck_user_audit_logs_action_type` CHECK (`action_type` in ('CREATE','UPDATE_ROLE','DEACTIVATE'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_audit_logs`
--

LOCK TABLES `user_audit_logs` WRITE;
/*!40000 ALTER TABLE `user_audit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `uq_users_email` (`email`),
  KEY `fk_users_created_by` (`created_by`),
  KEY `idx_users_role_active` (`role_id`,`is_active`),
  CONSTRAINT `fk_users_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,'System','Admin','admin@eudaimonia.com','0912-345-6789','$2y$10$.BRPXTux9KN4ETwkCyjIauKPvM2atTbjZEdo2ZhPAYVx6Nfd8kjg6',1,NULL,'2026-03-18 13:10:32',NULL,'2026-03-24 17:52:45'),(2,3,'Liam','Cruz','liam.cruz@example.com','0913-456-7890','$2y$10$C9xPahEsgo4vGWOxA9H5AeDwIHOoDD5LtJP/zy8k5a8uk8ESnKfxu',1,NULL,'2026-03-18 13:10:32',NULL,'2026-03-24 17:52:45'),(3,3,'Mia','Santos','mia.santos@example.com','0914-567-8901','$2y$10$b/NO7SE82wW5BZDXnt3qy.AgAJ5piuDTaWShlew/dHsV.CKw7fbja',1,NULL,'2026-03-18 13:10:32',NULL,'2026-03-24 17:52:45'),(4,3,'Noah','Reyes','noah.reyes@example.com','0915-678-9012','$2y$10$gUZNd2JRMePwGcH0ErPr4uMgLIK83V5rhdXV.x9Qu9NNgEa/FE0MC',1,NULL,'2026-03-18 13:10:32',NULL,'2026-03-24 17:52:45'),(18,4,'Kris','Alvarez','peopleattheback111@gmail.com','+639197133883','$2y$10$G/lk6T9Ng3WgExI9MI063O0ycZcwuszSIciDohjJqXhnBOXKvZvNi',1,NULL,'2026-03-24 17:41:28',NULL,'2026-03-24 18:46:30'),(24,4,'Test','User','user@test.com','1234567890','$2y$10$ISACAAFrs70KP9LIKqejp..W4gR1vN.zAXAii.dsrBc86ZObgkDO.',1,NULL,'2026-03-25 04:11:50',NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_users_before_update_timestamp
BEFORE UPDATE ON users
FOR EACH ROW
BEGIN
  SET NEW.updated_at = NOW();
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_users_before_update_login
BEFORE UPDATE ON users
FOR EACH ROW
BEGIN
  -- Auto-record when last_login transitions from NULL to a value
  -- or when last_login changes (the application sets last_login;
  -- this trigger ensures updated_at reflects that change)
  IF NOT (NEW.last_login <=> OLD.last_login) THEN
    SET NEW.updated_at = NOW();
  END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Temporary table structure for view `vw_active_add_ons`
--

DROP TABLE IF EXISTS `vw_active_add_ons`;
/*!50001 DROP VIEW IF EXISTS `vw_active_add_ons`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vw_active_add_ons` AS SELECT
 1 AS `add_on_id`,
  1 AS `category`,
  1 AS `name`,
  1 AS `description`,
  1 AS `price` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vw_active_event_packages`
--

DROP TABLE IF EXISTS `vw_active_event_packages`;
/*!50001 DROP VIEW IF EXISTS `vw_active_event_packages`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vw_active_event_packages` AS SELECT
 1 AS `package_id`,
  1 AS `package_name`,
  1 AS `base_price`,
  1 AS `description` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vw_active_services`
--

DROP TABLE IF EXISTS `vw_active_services`;
/*!50001 DROP VIEW IF EXISTS `vw_active_services`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vw_active_services` AS SELECT
 1 AS `service_id`,
  1 AS `service_name`,
  1 AS `price` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vw_admin_appointments`
--

DROP TABLE IF EXISTS `vw_admin_appointments`;
/*!50001 DROP VIEW IF EXISTS `vw_admin_appointments`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vw_admin_appointments` AS SELECT
 1 AS `appointment_id`,
  1 AS `user_id`,
  1 AS `customer_name`,
  1 AS `customer_email`,
  1 AS `zone_id`,
  1 AS `zone_name`,
  1 AS `table_id`,
  1 AS `table_label`,
  1 AS `appointment_date`,
  1 AS `start_time`,
  1 AS `end_time`,
  1 AS mysqldump.exe: Couldn't execute 'SHOW FIELDS FROM `vw_available_tables`': View 'restaurant_booking_v1.vw_available_tables' references invalid table(s) or column(s) or function(s) or definer/invoker of view lack rights to use them (1356)
`party_size`,
  1 AS `status_id`,
  1 AS `status_name`,
  1 AS `special_requests`,
  1 AS `created_at`,
  1 AS `updated_at` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vw_appointments_detail`
--

DROP TABLE IF EXISTS `vw_appointments_detail`;
/*!50001 DROP VIEW IF EXISTS `vw_appointments_detail`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vw_appointments_detail` AS SELECT
 1 AS `appointment_id`,
  1 AS `user_id`,
  1 AS `customer_name`,
  1 AS `customer_email`,
  1 AS `service_id`,
  1 AS `service_name`,
  1 AS `event_package_id`,
  1 AS `package_name`,
  1 AS `zone_id`,
  1 AS `zone_name`,
  1 AS `table_id`,
  1 AS `table_label`,
  1 AS `appointment_date`,
  1 AS `start_time`,
  1 AS `end_time`,
  1 AS `party_size`,
  1 AS `status_id`,
  1 AS `status_name`,
  1 AS `special_requests`,
  1 AS `created_at`,
  1 AS `updated_at` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vw_available_tables`
--

DROP TABLE IF EXISTS `vw_available_tables`;
