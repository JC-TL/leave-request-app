-- MySQL dump 10.13  Distrib 8.0.44, for Linux (x86_64)
--
-- Host: localhost    Database: leave_request_app
-- ------------------------------------------------------
-- Server version	8.0.44-0ubuntu0.22.04.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `balances`
--

DROP TABLE IF EXISTS `balances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `balances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `leave_type` enum('Vacation Leave','Sick Leave','Emergency Leave','Maternity Leave','Paternity Leave','Bereavement Leave','Special Leave','Unpaid Leave') COLLATE utf8mb4_unicode_ci NOT NULL,
  `balance` decimal(5,2) NOT NULL,
  `used` decimal(5,2) NOT NULL DEFAULT '0.00',
  `year` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `balances_user_id_leave_type_year_unique` (`user_id`,`leave_type`,`year`),
  KEY `balances_user_id_index` (`user_id`),
  KEY `balances_year_index` (`year`),
  CONSTRAINT `balances_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `balances`
--

LOCK TABLES `balances` WRITE;
/*!40000 ALTER TABLE `balances` DISABLE KEYS */;
INSERT INTO `balances` VALUES (1,1,'Vacation Leave',25.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(2,1,'Sick Leave',15.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(3,1,'Emergency Leave',5.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(4,1,'Maternity Leave',0.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(5,1,'Paternity Leave',0.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(6,1,'Bereavement Leave',5.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(7,1,'Special Leave',5.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(8,1,'Unpaid Leave',0.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(9,2,'Vacation Leave',20.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(10,2,'Sick Leave',10.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(11,2,'Emergency Leave',3.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(12,2,'Maternity Leave',0.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(13,2,'Paternity Leave',0.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(14,2,'Bereavement Leave',5.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(15,2,'Special Leave',3.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(16,2,'Unpaid Leave',0.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(17,3,'Vacation Leave',20.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(18,3,'Sick Leave',10.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(19,3,'Emergency Leave',3.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(20,3,'Maternity Leave',0.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(21,3,'Paternity Leave',0.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(22,3,'Bereavement Leave',5.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(23,3,'Special Leave',3.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(24,3,'Unpaid Leave',0.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(25,4,'Vacation Leave',18.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(26,4,'Sick Leave',10.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(27,4,'Emergency Leave',3.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(28,4,'Maternity Leave',0.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(29,4,'Paternity Leave',0.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(30,4,'Bereavement Leave',5.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(31,4,'Special Leave',3.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(32,4,'Unpaid Leave',0.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(33,5,'Vacation Leave',18.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(34,5,'Sick Leave',10.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(35,5,'Emergency Leave',3.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(36,5,'Maternity Leave',0.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(37,5,'Paternity Leave',0.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(38,5,'Bereavement Leave',5.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(39,5,'Special Leave',3.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(40,5,'Unpaid Leave',0.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(41,6,'Vacation Leave',15.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(42,6,'Sick Leave',10.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(43,6,'Emergency Leave',3.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(44,6,'Maternity Leave',0.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(45,6,'Paternity Leave',0.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(46,6,'Bereavement Leave',5.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(47,6,'Special Leave',3.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(48,6,'Unpaid Leave',0.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(49,7,'Vacation Leave',15.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(50,7,'Sick Leave',10.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(51,7,'Emergency Leave',3.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(52,7,'Maternity Leave',0.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(53,7,'Paternity Leave',0.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(54,7,'Bereavement Leave',5.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(55,7,'Special Leave',3.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(56,7,'Unpaid Leave',0.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(57,8,'Vacation Leave',15.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(58,8,'Sick Leave',10.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(59,8,'Emergency Leave',3.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(60,8,'Maternity Leave',0.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(61,8,'Paternity Leave',0.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(62,8,'Bereavement Leave',5.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(63,8,'Special Leave',3.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45'),(64,8,'Unpaid Leave',0.00,0.00,2026,'2026-01-11 15:40:45','2026-01-11 15:40:45');
/*!40000 ALTER TABLE `balances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `departments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dept_manager_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `departments_dept_manager_id_foreign` (`dept_manager_id`),
  CONSTRAINT `departments_dept_manager_id_foreign` FOREIGN KEY (`dept_manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES (1,'HR',NULL,'2026-01-11 15:40:40','2026-01-11 15:40:40'),(2,'Sales',4,'2026-01-11 15:40:40','2026-01-11 15:40:43'),(3,'IT',5,'2026-01-11 15:40:40','2026-01-11 15:40:43');
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_01_10_194329_create_department_table',1),(5,'2026_01_11_030045_create_requests_table',1),(6,'2026_01_11_054240_create_balance_table',1),(7,'2026_01_11_054642_create_policies_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `policies`
--

DROP TABLE IF EXISTS `policies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `policies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `leave_type` enum('Vacation Leave','Sick Leave','Emergency Leave','Maternity Leave','Paternity Leave','Bereavement Leave','Special Leave','Unpaid Leave') COLLATE utf8mb4_unicode_ci NOT NULL,
  `annual_entitlement` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `policies_leave_type_unique` (`leave_type`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `policies`
--

LOCK TABLES `policies` WRITE;
/*!40000 ALTER TABLE `policies` DISABLE KEYS */;
INSERT INTO `policies` VALUES (1,'Vacation Leave',15,'2026-01-11 15:40:40','2026-01-11 15:40:40'),(2,'Sick Leave',10,'2026-01-11 15:40:40','2026-01-11 15:40:40'),(3,'Emergency Leave',3,'2026-01-11 15:40:40','2026-01-11 15:40:40'),(4,'Maternity Leave',105,'2026-01-11 15:40:40','2026-01-11 15:40:40'),(5,'Paternity Leave',7,'2026-01-11 15:40:40','2026-01-11 15:40:40'),(6,'Bereavement Leave',5,'2026-01-11 15:40:40','2026-01-11 15:40:40'),(7,'Special Leave',3,'2026-01-11 15:40:40','2026-01-11 15:40:40'),(8,'Unpaid Leave',0,'2026-01-11 15:40:40','2026-01-11 15:40:40');
/*!40000 ALTER TABLE `policies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `requests`
--

DROP TABLE IF EXISTS `requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint unsigned NOT NULL,
  `leave_type` enum('Vacation Leave','Sick Leave','Emergency Leave','Maternity Leave','Paternity Leave','Bereavement Leave','Special Leave','Unpaid Leave') COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `number_of_days` int NOT NULL,
  `status` enum('pending','dept_manager_approved','dept_manager_rejected','hr_approved','hr_rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_by_dept_manager_id` bigint unsigned DEFAULT NULL,
  `dept_manager_comment` text COLLATE utf8mb4_unicode_ci,
  `approved_by_dept_at` timestamp NULL DEFAULT NULL,
  `approved_by_hr_id` bigint unsigned DEFAULT NULL,
  `hr_comment` text COLLATE utf8mb4_unicode_ci,
  `approved_by_hr_at` timestamp NULL DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `requests_approved_by_dept_manager_id_foreign` (`approved_by_dept_manager_id`),
  KEY `requests_approved_by_hr_id_foreign` (`approved_by_hr_id`),
  KEY `requests_employee_id_index` (`employee_id`),
  KEY `requests_status_index` (`status`),
  KEY `requests_start_date_end_date_index` (`start_date`,`end_date`),
  KEY `requests_created_at_index` (`created_at`),
  CONSTRAINT `requests_approved_by_dept_manager_id_foreign` FOREIGN KEY (`approved_by_dept_manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `requests_approved_by_hr_id_foreign` FOREIGN KEY (`approved_by_hr_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `requests_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `requests`
--

LOCK TABLES `requests` WRITE;
/*!40000 ALTER TABLE `requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('employee','dept_manager','hr_admin','ceo') COLLATE utf8mb4_unicode_ci NOT NULL,
  `department_id` bigint unsigned DEFAULT NULL,
  `manager_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_manager_id_foreign` (`manager_id`),
  CONSTRAINT `users_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'David Williams',NULL,'$2y$12$SconBS5t2..ldEdqirxmdeccALMwBtl8a0WYhWA2Ss.MPf1tWiv3K',NULL,'david@vglobal.com','ceo',NULL,NULL,'2026-01-11 15:40:41','2026-01-11 15:40:41'),(2,'Sarah Johnson',NULL,'$2y$12$cPlUnpb8LHyzhqySZ3nea.bD/4qEcdtJfAFKVujVG0rbu/0wUzKDW',NULL,'sarah@vglobal.com','hr_admin',1,NULL,'2026-01-11 15:40:41','2026-01-11 15:40:41'),(3,'Lisa Brown',NULL,'$2y$12$pU.pTD2B6GTm8EO5zO9REe/PPbG4h2PafdW67ayGI3LHxVdeh5qiW',NULL,'lisa@vglobal.com','hr_admin',1,NULL,'2026-01-11 15:40:42','2026-01-11 15:40:42'),(4,'John Smith',NULL,'$2y$12$GqiPest.tPxTY/ToLhDvceIbpMNFRQwe6SrVgQPKqLuVm19X4JRB.',NULL,'john@vglobal.com','dept_manager',2,NULL,'2026-01-11 15:40:42','2026-01-11 15:40:42'),(5,'Mary Johnson',NULL,'$2y$12$o/WqqP6cbMTle/5ZPZDhD.GtIxRYxXaASG8B9Pex.F/vNhm8t9/4S',NULL,'mary@vglobal.com','dept_manager',3,NULL,'2026-01-11 15:40:43','2026-01-11 15:40:43'),(6,'Alice Brown',NULL,'$2y$12$AgCJYg9mgAWu4uKfepzZjudl9IQERuT2WDxUCBQ19615IozLhoNke',NULL,'alice@vglobal.com','employee',2,4,'2026-01-11 15:40:43','2026-01-11 15:40:43'),(7,'Bob Wilson',NULL,'$2y$12$uE8CYahGjugJP4qB3.Aj5ea8iuE7dmuboMqDeauLDa/92DfmYH3n2',NULL,'bob@vglobal.com','employee',2,4,'2026-01-11 15:40:44','2026-01-11 15:40:44'),(8,'Carol Davis',NULL,'$2y$12$RwiVB2jbXUMQY51Jm01MvuzAbE0EuX2iSt.3kNNyfeIy3dvzg6oEm',NULL,'carol@vglobal.com','employee',3,5,'2026-01-11 15:40:44','2026-01-11 15:40:44'),(9,'Brad Smith',NULL,'$2y$12$fkNzMPHB18aLXQ8N0spwnuti15QmbYmG5teU9C/GQBFI3wda8WwDe',NULL,'brad@vglobal.com','employee',3,5,'2026-01-11 15:40:45','2026-01-11 15:40:45');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-01-12  7:47:14
