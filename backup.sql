-- MySQL dump 10.13  Distrib 8.0.46, for Linux (x86_64)
--
-- Host: localhost    Database: myscle_exam
-- ------------------------------------------------------
-- Server version	8.0.46-0ubuntu0.24.04.2

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
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('laravel-cache-lauris@laruis.com|127.0.0.1','i:1;',1780470936),('laravel-cache-lauris@laruis.com|127.0.0.1:timer','i:1780470936;',1780470936),('laravel-cache-lauris@lauris.com|127.0.0.1','i:1;',1780470943),('laravel-cache-lauris@lauris.com|127.0.0.1:timer','i:1780470943;',1780470943),('laravel-cache-mark@mail.com|127.0.0.1','i:1;',1780434233),('laravel-cache-mark@mail.com|127.0.0.1:timer','i:1780434233;',1780434233);
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
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
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
-- Table structure for table `coach_notes`
--

DROP TABLE IF EXISTS `coach_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coach_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `trainer_id` bigint unsigned NOT NULL,
  `client_id` bigint unsigned NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `coach_notes_trainer_id_foreign` (`trainer_id`),
  KEY `coach_notes_client_id_foreign` (`client_id`),
  CONSTRAINT `coach_notes_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `coach_notes_trainer_id_foreign` FOREIGN KEY (`trainer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coach_notes`
--

LOCK TABLES `coach_notes` WRITE;
/*!40000 ALTER TABLE `coach_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `coach_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exercise_library`
--

DROP TABLE IF EXISTS `exercise_library`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exercise_library` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `muscle_group` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exercise_library_user_id_foreign` (`user_id`),
  CONSTRAINT `exercise_library_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exercise_library`
--

LOCK TABLES `exercise_library` WRITE;
/*!40000 ALTER TABLE `exercise_library` DISABLE KEYS */;
INSERT INTO `exercise_library` VALUES (1,'Bench Press','Chest',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(2,'Incline Bench Press','Chest',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(3,'Decline Bench Press','Chest',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(4,'Chest Fly','Chest',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(5,'Push Ups','Chest',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(6,'Cable Crossover','Chest',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(7,'Deadlift','Back',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(8,'Pull Ups','Back',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(9,'Chin Ups','Back',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(10,'Lat Pulldown','Back',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(11,'Seated Row','Back',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(12,'Bent Over Row','Back',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(13,'T-Bar Row','Back',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(14,'Squat','Legs',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(15,'Leg Press','Legs',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(16,'Lunges','Legs',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(17,'Bulgarian Split Squat','Legs',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(18,'Leg Extension','Legs',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(19,'Hamstring Curl','Legs',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(20,'Calf Raises','Legs',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(21,'Shoulder Press','Shoulders',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(22,'Arnold Press','Shoulders',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(23,'Lateral Raise','Shoulders',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(24,'Front Raise','Shoulders',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(25,'Rear Delt Fly','Shoulders',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(26,'Face Pull','Shoulders',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(27,'Bicep Curl','Arms',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(28,'Hammer Curl','Arms',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(29,'Preacher Curl','Arms',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(30,'Tricep Extension','Arms',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(31,'Tricep Pushdown','Arms',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(32,'Dips','Arms',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(33,'Skull Crushers','Arms',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(34,'Plank','Core',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(35,'Sit Ups','Core',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(36,'Leg Raises','Core',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(37,'Russian Twist','Core',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(38,'Mountain Climbers','Core',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(39,'Burpees','Full Body',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(40,'Kettlebell Swing','Full Body',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(41,'Clean and Press','Full Body',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(42,'Farmer Walk','Full Body',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(43,'Box Jumps','Full Body',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(44,'Running','Cardio',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(45,'Cycling','Cardio',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(46,'Jump Rope','Cardio',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(47,'Rowing Machine','Cardio',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(48,'Stair Climber','Cardio',NULL,'2026-06-02 17:05:11','2026-06-02 17:05:11'),(49,'mark',NULL,NULL,'2026-06-02 18:12:59','2026-06-02 18:12:59'),(50,'legday',NULL,NULL,'2026-06-02 18:13:02','2026-06-02 18:13:02'),(51,'roku diena',NULL,NULL,'2026-06-02 18:18:34','2026-06-02 18:18:34'),(52,'abs',NULL,NULL,'2026-06-02 18:26:32','2026-06-02 18:26:32'),(53,'abs 1',NULL,NULL,'2026-06-03 08:10:02','2026-06-03 08:10:02'),(54,'kaju vingrinajums 1',NULL,NULL,'2026-06-03 08:13:55','2026-06-03 08:13:55'),(55,'arms',NULL,NULL,'2026-06-05 08:24:19','2026-06-05 08:24:19');
/*!40000 ALTER TABLE `exercise_library` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exercise_sets`
--

DROP TABLE IF EXISTS `exercise_sets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exercise_sets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `exercise_id` bigint unsigned NOT NULL,
  `set_number` int NOT NULL,
  `reps` int NOT NULL,
  `weight` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exercise_sets_exercise_id_foreign` (`exercise_id`),
  CONSTRAINT `exercise_sets_exercise_id_foreign` FOREIGN KEY (`exercise_id`) REFERENCES `exercises` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exercise_sets`
--

LOCK TABLES `exercise_sets` WRITE;
/*!40000 ALTER TABLE `exercise_sets` DISABLE KEYS */;
INSERT INTO `exercise_sets` VALUES (1,1,1,10,85,'2026-04-02 06:31:15','2026-04-02 06:31:15'),(2,1,2,8,85,'2026-04-02 06:31:15','2026-04-02 06:31:15'),(3,2,1,5,100,'2026-04-02 06:31:15','2026-04-02 06:31:15'),(4,4,1,10,90,'2026-04-02 06:31:15','2026-04-02 06:31:15'),(5,4,2,8,90,'2026-04-02 06:31:15','2026-04-02 06:31:15'),(7,9,1,10,25,'2026-04-13 04:56:12','2026-04-13 04:56:12'),(23,12,1,1,2,'2026-06-03 04:26:19','2026-06-03 04:26:19'),(24,12,2,3,6,'2026-06-03 04:26:19','2026-06-03 04:26:19'),(30,5,1,100,100,'2026-06-05 08:13:36','2026-06-05 08:13:36'),(31,5,2,100,100,'2026-06-05 08:13:36','2026-06-05 08:13:36'),(39,6,1,12,20,'2026-06-07 18:18:00','2026-06-07 18:18:00'),(40,6,2,0,0,'2026-06-07 18:18:00','2026-06-07 18:18:00'),(42,16,1,0,0,'2026-06-07 18:28:05','2026-06-07 18:28:05'),(43,11,1,0,2,'2026-06-07 18:29:22','2026-06-07 18:29:22'),(44,11,2,3,0,'2026-06-07 18:29:22','2026-06-07 18:29:22'),(45,17,1,2,2,'2026-06-07 18:59:48','2026-06-07 18:59:48'),(46,18,1,2,2,'2026-06-07 18:59:48','2026-06-07 18:59:48');
/*!40000 ALTER TABLE `exercise_sets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `exercises`
--

DROP TABLE IF EXISTS `exercises`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `exercises` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `training_plan_id` bigint unsigned NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `exercises_training_plan_id_foreign` (`training_plan_id`),
  CONSTRAINT `exercises_training_plan_id_foreign` FOREIGN KEY (`training_plan_id`) REFERENCES `training_plans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `exercises`
--

LOCK TABLES `exercises` WRITE;
/*!40000 ALTER TABLE `exercises` DISABLE KEYS */;
INSERT INTO `exercises` VALUES (1,1,'Squat',NULL,'2026-04-02 06:31:15','2026-04-02 06:31:15'),(2,1,'Deadlift',NULL,'2026-04-02 06:31:15','2026-04-02 06:31:15'),(3,2,'Pull Ups',NULL,'2026-04-02 06:31:15','2026-04-02 06:31:15'),(4,3,'Bench Press',NULL,'2026-04-02 06:31:15','2026-04-02 06:31:15'),(5,7,'Bench Press','100 uz 100','2026-04-02 06:31:16','2026-06-05 08:12:19'),(6,8,'Bicep Curl',NULL,'2026-04-02 06:31:16','2026-04-02 06:31:16'),(7,9,'Chest Press',NULL,'2026-04-02 06:31:16','2026-04-02 06:31:16'),(9,11,'Bicep Curl',NULL,'2026-04-13 04:56:12','2026-04-13 04:56:12'),(11,13,'roku diena','1 2 3 4','2026-06-02 18:18:46','2026-06-02 18:18:46'),(12,14,'Face Pull','try hard','2026-06-02 18:22:19','2026-06-02 18:22:19'),(16,17,'Lat Pulldown','asd','2026-06-07 18:18:21','2026-06-07 18:18:21'),(17,18,'Bicep Curl',NULL,'2026-06-07 18:59:48','2026-06-07 18:59:48'),(18,18,'Tricep Extension',NULL,'2026-06-07 18:59:48','2026-06-07 18:59:48');
/*!40000 ALTER TABLE `exercises` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `friends`
--

DROP TABLE IF EXISTS `friends`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `friends` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `friend_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`id`),
  UNIQUE KEY `friends_user_id_friend_id_unique` (`user_id`,`friend_id`),
  KEY `friends_friend_id_foreign` (`friend_id`),
  CONSTRAINT `friends_friend_id_foreign` FOREIGN KEY (`friend_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `friends_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `friends`
--

LOCK TABLES `friends` WRITE;
/*!40000 ALTER TABLE `friends` DISABLE KEYS */;
INSERT INTO `friends` VALUES (1,4,1,'2026-04-02 06:31:16','2026-04-02 06:31:16','accepted'),(2,4,2,'2026-04-02 06:31:16','2026-04-02 06:31:16','accepted'),(3,1,4,'2026-04-02 06:31:16','2026-04-02 06:31:16','accepted'),(4,6,4,'2026-06-06 14:13:27','2026-06-06 14:13:43','accepted'),(5,4,6,'2026-06-06 14:13:43','2026-06-06 14:13:43','accepted'),(6,16,1,'2026-06-07 19:00:17','2026-06-07 19:00:17','pending'),(7,16,4,'2026-06-07 19:00:17','2026-06-07 19:01:01','accepted'),(8,16,2,'2026-06-07 19:00:18','2026-06-07 19:00:18','pending'),(9,4,16,'2026-06-07 19:01:01','2026-06-07 19:01:01','accepted');
/*!40000 ALTER TABLE `friends` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` bigint unsigned NOT NULL,
  `receiver_id` bigint unsigned NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_receiver_id_foreign` (`receiver_id`),
  KEY `messages_sender_id_receiver_id_created_at_index` (`sender_id`,`receiver_id`,`created_at`),
  CONSTRAINT `messages_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
INSERT INTO `messages` VALUES (1,4,6,'hey client','2026-06-03 08:12:17','2026-06-02 18:43:10','2026-06-03 08:12:17'),(2,4,12,'hey',NULL,'2026-06-07 17:30:09','2026-06-07 17:30:09'),(3,16,4,'hey mark new friend',NULL,'2026-06-07 19:01:45','2026-06-07 19:01:45');
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000002_create_training_plans_table',1),(3,'0001_01_01_000006_create_exercise_table',1),(4,'2026_03_16_161812_create_personal_access_tokens_table',1),(5,'2026_03_31_174105_create_exercise_sets_table',1),(6,'2026_04_02_075823_create_friends_table',1),(7,'2026_04_06_162658_change_profile_photo_type',1),(8,'2026_04_06_170709_add_status_to_friends_table',1),(9,'2026_04_25_131139_create_exercise_library_table',1),(10,'2026_04_29_110611_create_messages_table',1),(11,'2026_05_06_185208_create_trainer_clients_table',1),(12,'2026_05_12_143011_create_workout_logs_table',1),(13,'2026_05_12_143012_create_workout_log_sets_table',1),(14,'2026_05_14_094002_create_coach_notes_table',1),(15,'2026_05_18_153327_create_subscriptions_table',1),(16,'2026_06_01_173203_create_cache_table',1);
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
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (1,'App\\Models\\User',4,'auth_token','e54ad868c701758788836e5b9a5596b9daf4e6a60e7b5408dacca2ca16dc9ca5','[\"*\"]','2026-06-02 17:12:48',NULL,'2026-06-02 17:08:35','2026-06-02 17:12:48'),(2,'App\\Models\\User',4,'auth_token','6ed58ab60502029b7024f3c17909452431fcac12e14616793ec60b5e8e489ae2','[\"*\"]','2026-06-02 17:57:10',NULL,'2026-06-02 17:55:35','2026-06-02 17:57:10'),(3,'App\\Models\\User',5,'auth_token','18b1ab941a82ba0f049878d4402bda04bc23a3e8290fe3b892fed1090ca9e38c','[\"*\"]','2026-06-02 18:02:00',NULL,'2026-06-02 17:57:18','2026-06-02 18:02:00'),(4,'App\\Models\\User',4,'auth_token','a9fc82e7f6148bab7a1505c4c3dad34c54af32ad99dc3d7c81a0bac634b4a78e','[\"*\"]','2026-06-02 18:03:29',NULL,'2026-06-02 18:02:56','2026-06-02 18:03:29'),(5,'App\\Models\\User',5,'auth_token','e07486061a5dae3d8a9ab800972bb3d19974b55292316f710ff02f55f101cbbe','[\"*\"]','2026-06-02 18:04:18',NULL,'2026-06-02 18:03:41','2026-06-02 18:04:18'),(6,'App\\Models\\User',4,'auth_token','62cdad383174c332145054896b0a07fd3d73fdfeb303cbbd6d4aef19d1760a58','[\"*\"]','2026-06-02 18:11:47',NULL,'2026-06-02 18:04:28','2026-06-02 18:11:47'),(7,'App\\Models\\User',6,'auth_token','670d1ae9a97219a5aa31249032e0beb6b19ae5cb14a1dab35ee9acda3f6e95ac','[\"*\"]','2026-06-02 18:12:28',NULL,'2026-06-02 18:12:24','2026-06-02 18:12:28'),(8,'App\\Models\\User',4,'auth_token','8cdbdf627c6791f2bdc22ec4964aad2fa9bc8ed2c0db523664d37f6d16c18632','[\"*\"]','2026-06-02 18:14:11',NULL,'2026-06-02 18:12:42','2026-06-02 18:14:11'),(9,'App\\Models\\User',6,'auth_token','813c75b5b2c27e7bdabe918f7883c250a1895a970e1cd99e64bc25327c51a8f0','[\"*\"]','2026-06-02 18:14:25',NULL,'2026-06-02 18:14:20','2026-06-02 18:14:25'),(10,'App\\Models\\User',4,'auth_token','617550a5d1121a183a13427735fe21be5033c01f2e70b7f2b7f8bbbb8bd7680f','[\"*\"]','2026-06-02 18:19:08',NULL,'2026-06-02 18:18:14','2026-06-02 18:19:08'),(11,'App\\Models\\User',6,'auth_token','9173b5ddcd7cf89df82d0e1bc45b017d1f5cf349489299f6a6e8311c787ca98f','[\"*\"]','2026-06-02 18:19:48',NULL,'2026-06-02 18:19:43','2026-06-02 18:19:48'),(12,'App\\Models\\User',4,'auth_token','c61d6b3185bddd9c9703bfd27dd836ccf8ba7cc49be8a78bf25b7b422ae15c58','[\"*\"]','2026-06-02 18:22:26',NULL,'2026-06-02 18:21:39','2026-06-02 18:22:26'),(13,'App\\Models\\User',6,'auth_token','17ea2b20929dddade77b7ef3ddbb256617cebb622592a737742d4f843adfbe16','[\"*\"]','2026-06-02 18:25:47',NULL,'2026-06-02 18:22:39','2026-06-02 18:25:47'),(14,'App\\Models\\User',6,'auth_token','a482fc824e0f5088282cdeea8268ff80e8387df9a049b02487c9f1e20aac0182','[\"*\"]','2026-06-02 18:26:08',NULL,'2026-06-02 18:26:04','2026-06-02 18:26:08'),(15,'App\\Models\\User',4,'auth_token','2d01212e909a7227c23a73d5b99a8452614c4a256822a38c7c3657eebd12119d','[\"*\"]','2026-06-02 18:26:48',NULL,'2026-06-02 18:26:18','2026-06-02 18:26:48'),(16,'App\\Models\\User',6,'auth_token','193e96fa56df60d42ded51724d3f15c79a347eebd3999f0b21adc14332bc58c3','[\"*\"]','2026-06-02 18:36:19',NULL,'2026-06-02 18:27:04','2026-06-02 18:36:19'),(17,'App\\Models\\User',4,'auth_token','ca4859455572f2db68dfefb14104c75347ccb5334f6c1f372a21964903f779fe','[\"*\"]','2026-06-02 18:40:47',NULL,'2026-06-02 18:36:29','2026-06-02 18:40:47'),(18,'App\\Models\\User',6,'auth_token','bae8de62e9ec7bf01d5b9743641e99d1f811fc275fe3364ed598930f43a83079','[\"*\"]','2026-06-02 18:41:19',NULL,'2026-06-02 18:40:58','2026-06-02 18:41:19'),(19,'App\\Models\\User',4,'auth_token','83d2efa8fb1589133db61e864be9efb4b0bab2b39add3f9f9a98907e26c5c412','[\"*\"]','2026-06-02 18:41:45',NULL,'2026-06-02 18:41:34','2026-06-02 18:41:45'),(20,'App\\Models\\User',6,'auth_token','5d57a3b8339269302d123603f8f5a973e8810c9f85177ecc9e52cef812421d1a','[\"*\"]','2026-06-02 18:42:10',NULL,'2026-06-02 18:41:58','2026-06-02 18:42:10'),(21,'App\\Models\\User',4,'auth_token','2c51612df6d43ec6d24c4a1514a5d36bb5a25805a709c6ca16ed82f5caf1a397','[\"*\"]','2026-06-02 18:44:09',NULL,'2026-06-02 18:42:21','2026-06-02 18:44:09'),(22,'App\\Models\\User',5,'auth_token','e982abe42497da171bb043d87810499440cefce1bd6615d3eac4683b2ff2ad38','[\"*\"]','2026-06-03 03:46:31',NULL,'2026-06-02 18:44:25','2026-06-03 03:46:31'),(23,'App\\Models\\User',4,'auth_token','3a0d685fe1dfa5dee649050623e8db5a4a5010700cc98964d0045ae3ca9dec75','[\"*\"]','2026-06-03 04:04:28',NULL,'2026-06-03 04:04:22','2026-06-03 04:04:28'),(24,'App\\Models\\User',5,'auth_token','1baa556783c15379687fc5da947cbb9080ca0e169a1ceb9365a5d329959b6162','[\"*\"]','2026-06-03 04:04:39',NULL,'2026-06-03 04:04:38','2026-06-03 04:04:39'),(25,'App\\Models\\User',6,'auth_token','3548d9ce9bf64706c3debf897221468fd0c7e34e3e9eb8e002765143ee10ecf6','[\"*\"]','2026-06-03 04:06:38',NULL,'2026-06-03 04:05:12','2026-06-03 04:06:38'),(26,'App\\Models\\User',13,'auth_token','9fc48e9f4481fd9d72ad427ac5a04bca51f98a3be2a4f239d03b8edc9ac82466','[\"*\"]','2026-06-03 04:14:15',NULL,'2026-06-03 04:07:17','2026-06-03 04:14:15'),(27,'App\\Models\\User',13,'auth_token','c2dc26d35c51311af6ad4fb4d28fd3ce3f9ab0c4c45c9bd9d9bdc627fd516d77','[\"*\"]','2026-06-03 04:25:00',NULL,'2026-06-03 04:14:57','2026-06-03 04:25:00'),(28,'App\\Models\\User',6,'auth_token','f203626042d82ba13f7802dd1b57c76a03c9d9c88a9637fe0658bfe86ff775b3','[\"*\"]','2026-06-03 04:25:25',NULL,'2026-06-03 04:25:13','2026-06-03 04:25:25'),(29,'App\\Models\\User',13,'auth_token','bac21385903140ce932393da2092592e84fa2b7aec844b9dad3979c5ebb366f4','[\"*\"]','2026-06-03 04:26:33',NULL,'2026-06-03 04:25:41','2026-06-03 04:26:33'),(30,'App\\Models\\User',6,'auth_token','a2398544ad2f1bcfec5e6ec5d33b809d1dc6b5e3c45dff543835e56ace0ed934','[\"*\"]','2026-06-03 04:26:55',NULL,'2026-06-03 04:26:49','2026-06-03 04:26:55'),(31,'App\\Models\\User',13,'auth_token','ab51a87c77a2144ed8bb6b788ddb48a9e195200a31ffb98708917aa9c630e4b6','[\"*\"]','2026-06-03 04:44:18',NULL,'2026-06-03 04:27:08','2026-06-03 04:44:18'),(32,'App\\Models\\User',4,'auth_token','3872d407c3bba0a6daab997b4d53f86ed1e83082499572e6849931f96995b4ef','[\"*\"]','2026-06-03 05:04:06',NULL,'2026-06-03 05:03:38','2026-06-03 05:04:06'),(33,'App\\Models\\User',6,'auth_token','0da2871b760bd1d805e864478d9069354145ab7490bc57742476cf701cbb5895','[\"*\"]','2026-06-03 05:05:05',NULL,'2026-06-03 05:05:05','2026-06-03 05:05:05'),(34,'App\\Models\\User',4,'auth_token','9416460a1c2b6a89375327cb96f6178ebd20537c0885ff3947336c9fed219965','[\"*\"]','2026-06-03 06:59:51',NULL,'2026-06-03 06:59:48','2026-06-03 06:59:51'),(35,'App\\Models\\User',4,'auth_token','90c5fe4ed20831dc8b03187400ee473cf9d3aaf46d8b05e6fe27787f6d588758','[\"*\"]','2026-06-03 07:49:12',NULL,'2026-06-03 07:48:44','2026-06-03 07:49:12'),(36,'App\\Models\\User',6,'auth_token','dd2d62d0420fd26650db35cd891c609f197ed78c159ce5935db7754803e758d3','[\"*\"]','2026-06-03 08:12:31',NULL,'2026-06-03 08:09:18','2026-06-03 08:12:31'),(37,'App\\Models\\User',4,'auth_token','006315acec6c0eb1d48f2fe56907183477c8de5fb2b6e57df354cd0f647c2aac','[\"*\"]','2026-06-03 08:15:00',NULL,'2026-06-03 08:12:48','2026-06-03 08:15:00'),(38,'App\\Models\\User',14,'auth_token','7cb9c7d6e9daa9b7f69dc38c97a7fc410af8b11b29f82edbd81a7214921dd02c','[\"*\"]','2026-06-03 08:16:44',NULL,'2026-06-03 08:15:25','2026-06-03 08:16:44'),(39,'App\\Models\\User',6,'auth_token','328b4a0eaca89e21cd0e5eddd60b334da65f84ca57b0077781f81810a305bcb1','[\"*\"]','2026-06-03 08:17:14',NULL,'2026-06-03 08:16:54','2026-06-03 08:17:14'),(40,'App\\Models\\User',5,'auth_token','bbba474bf4dd2707ac1d07d9c85ef4a5094b43a80ba28d273dbed18545f81eb8','[\"*\"]','2026-06-03 08:18:36',NULL,'2026-06-03 08:17:26','2026-06-03 08:18:36'),(41,'App\\Models\\User',4,'auth_token','141f234d7d6d8b1a21c0f84cdaabd2956c1727a6b2f693ee389c367b5d15832c','[\"*\"]','2026-06-03 08:18:53',NULL,'2026-06-03 08:18:50','2026-06-03 08:18:53'),(42,'App\\Models\\User',5,'auth_token','e536b9a72f536c22cee6d1d42dbfd89a35b2d6a4e2f37b98ad24a151f3e1b8a0','[\"*\"]','2026-06-03 08:20:09',NULL,'2026-06-03 08:19:02','2026-06-03 08:20:09'),(43,'App\\Models\\User',4,'auth_token','a68107281e12fb218ca192e44fd1513f767df36f23599ae967842b03ae42812b','[\"*\"]','2026-06-05 07:33:57',NULL,'2026-06-03 08:20:45','2026-06-05 07:33:57'),(44,'App\\Models\\User',4,'auth_token','a906bdb82bc1189e3ae96ecf41c7b65f03fbc73a8b1bae69455a5c148be114db','[\"*\"]','2026-06-05 09:53:22',NULL,'2026-06-05 07:58:17','2026-06-05 09:53:22'),(45,'App\\Models\\User',6,'auth_token','5bdc0a39f4da43261057d49e39b567e5e69811bec920ed2d17cb1cab3477d743','[\"*\"]','2026-06-06 11:37:31',NULL,'2026-06-05 09:53:40','2026-06-06 11:37:31'),(46,'App\\Models\\User',6,'auth_token','e81fffca8f6b46ae5e967f9ab16192716c65d942cf5d569d163333801b0de988','[\"*\"]','2026-06-06 14:13:29',NULL,'2026-06-06 13:07:53','2026-06-06 14:13:29'),(47,'App\\Models\\User',4,'auth_token','c2b3aee15bccfb7e94fa67b7a9ce0d564f5811796208aefa685cbd087cfe4ec0','[\"*\"]','2026-06-06 14:13:47',NULL,'2026-06-06 14:13:36','2026-06-06 14:13:47'),(48,'App\\Models\\User',6,'auth_token','b5a81892f5d019463eabf078820b705778f6e38b3392f708ed4b75b575416058','[\"*\"]','2026-06-06 14:24:25',NULL,'2026-06-06 14:14:01','2026-06-06 14:24:25'),(49,'App\\Models\\User',4,'auth_token','47fe9484c08a5d004d6584bcd036429d137d2db8509ecbf681ec4b19a006353c','[\"*\"]','2026-06-06 15:10:37',NULL,'2026-06-06 14:24:34','2026-06-06 15:10:37'),(50,'App\\Models\\User',6,'auth_token','58238af48be2fe755dc33a2594ffa7b388c2f6d28000a8d2e53c780905bf7e4c','[\"*\"]','2026-06-06 15:10:59',NULL,'2026-06-06 15:10:54','2026-06-06 15:10:59'),(51,'App\\Models\\User',4,'auth_token','bf56cde761441e836db7e02e7d79534231adfc5e4e602c7600ea7610831f0823','[\"*\"]','2026-06-06 16:23:33',NULL,'2026-06-06 15:11:12','2026-06-06 16:23:33'),(52,'App\\Models\\User',4,'auth_token','fe105d3dad1e24a387c0e8b312d586177800eb54ba9a20a9f2dfc275651f7337','[\"*\"]','2026-06-06 16:23:45',NULL,'2026-06-06 16:23:42','2026-06-06 16:23:45'),(53,'App\\Models\\User',15,'auth_token','fedaeaf439ef94f955d0ea18820c1610e46f03ec1c6625c3752d66b874394023','[\"*\"]','2026-06-07 15:46:20',NULL,'2026-06-06 16:24:59','2026-06-07 15:46:20'),(54,'App\\Models\\User',4,'auth_token','ef1006145489d71fc7d80011aab5b94ceb8cb7bb479d826f4f3aeec619f18e83','[\"*\"]','2026-06-07 15:48:09',NULL,'2026-06-07 15:47:34','2026-06-07 15:48:09'),(55,'App\\Models\\User',12,'auth_token','319e65673ee81ac57c4e6122467adf4c3368862cff71de9a9dc82bcd49b09f74','[\"*\"]','2026-06-07 15:48:56',NULL,'2026-06-07 15:48:49','2026-06-07 15:48:56'),(56,'App\\Models\\User',4,'auth_token','fd4c1184c7a1380a82cf1270ca2a5096525fd08f74e9d86f02583e7c32be3298','[\"*\"]','2026-06-07 17:30:11',NULL,'2026-06-07 15:49:04','2026-06-07 17:30:11'),(57,'App\\Models\\User',6,'auth_token','87d0bcbfdcb0e107543a7134973b9540e375dea2669032080d45416ba27c1706','[\"*\"]','2026-06-07 18:06:14',NULL,'2026-06-07 17:30:28','2026-06-07 18:06:14'),(58,'App\\Models\\User',4,'auth_token','80be1cb93efffbd07788e321772adfad673c8d7324f35fd53977b2c82bbd40de','[\"*\"]','2026-06-07 18:50:10',NULL,'2026-06-07 18:07:49','2026-06-07 18:50:10'),(59,'App\\Models\\User',4,'auth_token','014a0f9fee570f8176a8e15c2724ab67aa06131fb8731d7c89c43250c1ee1b46','[\"*\"]','2026-06-07 18:51:17',NULL,'2026-06-07 18:50:24','2026-06-07 18:51:17'),(60,'App\\Models\\User',15,'auth_token','b703ac1d2a6ce98a4c713bc9a56eec7ee1ea1402cd4c09189fa65b039221afef','[\"*\"]','2026-06-07 18:51:47',NULL,'2026-06-07 18:51:28','2026-06-07 18:51:47'),(61,'App\\Models\\User',16,'auth_token','1396cd2b0917753886015c4e14446c55419f1e786ab725aef34706bee7e4c49c','[\"*\"]','2026-06-07 19:00:34',NULL,'2026-06-07 18:52:09','2026-06-07 19:00:34'),(62,'App\\Models\\User',4,'auth_token','a4fcd03c9546fae008e969d0c2aee0bc1cfeae1a07cea2a4707d8df7034add23','[\"*\"]','2026-06-07 19:01:01',NULL,'2026-06-07 19:00:45','2026-06-07 19:01:01'),(63,'App\\Models\\User',16,'auth_token','5a6b12281366ce254e986d55ffca1cc31bbc802181ac4f0a528a4b11e4c38c91','[\"*\"]','2026-06-07 19:03:06',NULL,'2026-06-07 19:01:17','2026-06-07 19:03:06');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
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
-- Table structure for table `subscriptions`
--

DROP TABLE IF EXISTS `subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscriptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `stripe_customer_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stripe_subscription_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscriptions_user_id_foreign` (`user_id`),
  CONSTRAINT `subscriptions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subscriptions`
--

LOCK TABLES `subscriptions` WRITE;
/*!40000 ALTER TABLE `subscriptions` DISABLE KEYS */;
INSERT INTO `subscriptions` VALUES (1,4,'cus_mark_001','sub_mark_001','active',NULL,NULL,'2026-06-02 21:01:52','2026-06-02 21:01:52'),(2,1,'cus_user1_001','sub_user1_001','trialing','2026-07-01 21:00:00',NULL,'2026-06-02 21:01:52','2026-06-02 21:01:52'),(3,2,'cus_user2_001','sub_user2_001','active',NULL,NULL,'2026-06-02 21:01:52','2026-06-02 21:01:52'),(4,3,'cus_user3_001','sub_user3_001','cancelled',NULL,NULL,'2026-06-02 21:01:52','2026-06-02 21:01:52'),(5,6,'cus_user6_001','sub_user6_001','trialing','2026-07-14 21:00:00',NULL,'2026-06-02 21:01:52','2026-06-02 21:01:52'),(7,9,'cus_user9_001','sub_user9_001','cancelled',NULL,NULL,'2026-06-02 21:01:52','2026-06-02 21:01:52'),(8,12,'cus_user12_001','sub_user12_001','active',NULL,NULL,'2026-06-02 21:01:52','2026-06-02 21:01:52'),(9,14,NULL,NULL,'trialing',NULL,NULL,'2026-06-03 08:16:12','2026-06-03 08:16:12'),(10,16,NULL,NULL,'trialing',NULL,NULL,'2026-06-07 19:02:23','2026-06-07 19:02:23');
/*!40000 ALTER TABLE `subscriptions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trainer_clients`
--

DROP TABLE IF EXISTS `trainer_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trainer_clients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `trainer_id` bigint unsigned NOT NULL,
  `client_id` bigint unsigned NOT NULL,
  `status` enum('pending','accepted') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `trainer_clients_trainer_id_foreign` (`trainer_id`),
  KEY `trainer_clients_client_id_foreign` (`client_id`),
  CONSTRAINT `trainer_clients_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `trainer_clients_trainer_id_foreign` FOREIGN KEY (`trainer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trainer_clients`
--

LOCK TABLES `trainer_clients` WRITE;
/*!40000 ALTER TABLE `trainer_clients` DISABLE KEYS */;
INSERT INTO `trainer_clients` VALUES (1,4,6,'accepted','2026-06-02 18:11:33','2026-06-02 18:12:28'),(2,13,6,'accepted','2026-06-03 04:24:51','2026-06-03 04:25:25'),(3,14,6,'accepted','2026-06-03 08:16:44','2026-06-03 08:17:03'),(4,4,2,'pending','2026-06-03 08:20:57','2026-06-03 08:20:57'),(5,4,8,'pending','2026-06-07 15:48:04','2026-06-07 15:48:04'),(6,4,12,'accepted','2026-06-07 15:48:09','2026-06-07 15:48:56');
/*!40000 ALTER TABLE `trainer_clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `training_plans`
--

DROP TABLE IF EXISTS `training_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `training_plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_favorite` tinyint(1) NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `training_plans_user_id_foreign` (`user_id`),
  CONSTRAINT `training_plans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `training_plans`
--

LOCK TABLES `training_plans` WRITE;
/*!40000 ALTER TABLE `training_plans` DISABLE KEYS */;
INSERT INTO `training_plans` VALUES (1,1,'Leg Day',0,NULL,'2026-04-02 06:31:15','2026-04-02 06:31:15'),(2,1,'Back Day',0,NULL,'2026-04-02 06:31:15','2026-04-02 06:31:15'),(3,1,'Push Day',0,NULL,'2026-04-02 06:31:15','2026-04-02 06:31:15'),(4,2,'Leg Day',0,NULL,'2026-04-02 06:31:15','2026-04-02 06:31:15'),(5,2,'Back Day',0,NULL,'2026-04-02 06:31:15','2026-04-02 06:31:15'),(6,2,'Back Day',0,NULL,'2026-04-02 06:31:15','2026-04-02 06:31:15'),(7,4,'Leg Day',1,NULL,'2026-04-02 06:31:16','2026-06-02 18:44:04'),(8,4,'Pull Day',0,NULL,'2026-04-02 06:31:16','2026-04-02 09:32:28'),(9,3,'Chest Day',0,NULL,'2026-04-02 06:31:16','2026-04-02 06:31:16'),(11,4,'Bicep day',0,NULL,'2026-04-13 04:56:12','2026-06-03 08:23:00'),(13,6,'roku diena',0,NULL,'2026-06-02 18:18:27','2026-06-02 18:18:27'),(14,6,'chest day',0,NULL,'2026-06-02 18:21:51','2026-06-02 18:21:51'),(17,4,'leg day 2',0,NULL,'2026-06-03 08:23:37','2026-06-03 08:23:37'),(18,16,'Arms day',1,NULL,'2026-06-07 18:59:48','2026-06-07 18:59:52');
/*!40000 ALTER TABLE `training_plans` ENABLE KEYS */;
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
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_photo` longtext COLLATE utf8mb4_unicode_ci,
  `role` enum('user','trainer','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `goal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `height` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `age` int DEFAULT NULL,
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `completed_workouts` int NOT NULL DEFAULT '0',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Dejuan Cummerata','liliana24@example.com','$2y$12$7Trsm1NbgTiQ.G3n7r7FCOtJSJ6/ND9jJSsnDCuGreKllRcYK674a','https://preview.redd.it/anyone-do-i-need-a-new-pfp-v0-lpbobx55hphe1.jpeg?width=640&crop=smart&auto=webp&s=9cab5096ad9ac8f924657aa659c3870a3bcfadc9','user','Muscle Gain','82kg','182cm',23,'Male','Focused on strength and hypertrophy.',42,'Cve9tk2zFw','2026-06-02 17:05:02','2026-06-02 17:05:02'),(2,'Katharina Wiegand','raquel.stanton@example.net','$2y$12$JpEpvafle2GhaNJFicT9YuhL63hDSpNCWJB5HC9toTKUU0oyarZvq','https://static.vecteezy.com/system/resources/thumbnails/060/843/811/small/close-up-of-raindrops-on-leaves-hd-background-luxury-hd-wallpaper-image-trendy-background-illustration-free-photo.jpg','user','Fat Loss','64kg','168cm',25,'Female','Improving conditioning and mobility.',26,'mVTrS63xk8','2026-06-02 17:05:03','2026-06-02 17:05:03'),(3,'Mr. Milford O\'Kon','garnet.farrell@example.com','$2y$12$CfTzSqontkYnOKdbF9Id2.802ERYVrOwTxzm6brYrqp1NHX.7oMf.',NULL,'user','Strength','91kg','188cm',29,'Male','Powerlifting focused athlete.',61,'H8unTVP844','2026-06-02 17:05:04','2026-06-02 17:05:04'),(4,'mark','mark@gmail.com','$2y$12$oyCmKnP7fchXHjMPPgjP3OcGKeTl53aBYRvXai6eYL1bBUI2/TWQ.','profiles/QYUhzOpWdNINCxPWusaonbCQrnYy1Pf8g7eeZXfK.png','trainer','build_muscle','75','179',2,'female','Online trainer and coach. :)',13,NULL,'2026-06-02 17:05:05','2026-06-07 18:50:11'),(6,'Markuss Jansons','jjansonsmarkuss@gmail.com','$2y$12$8xgPuS0pM4TP02vCdfnQYu67RRqE9a.GhInbTRjCq8zEvxhutrwIq','profiles/YwChKfIZQcusXaMH7zBKjSelLfJ7LNvS38yPy3Oq.png','user','lose_weight','78','180',18,'female','Hybrid athlete focused on performance.',72,NULL,'2026-06-02 17:05:06','2026-06-07 17:57:10'),(8,'lote ulmane','lote@gmail.com','$2y$12$A5x8wxJwTCfuZmCuUP9AaObUoidEOM4nGrHOVKjPBCIR4pimUSF6.',NULL,'user','Fat Loss','58kg','165cm',19,'Female','Working on consistency and cardio.',19,NULL,'2026-06-02 17:05:08','2026-06-02 17:05:08'),(9,'martins sesks','sesks@gmail.com','$2y$12$UFgfOGFE8VA8b7NdrguBIOrhm3FFzJESCZPeIMJetegoW/DFxC7B6',NULL,'user','Strength','94kg','190cm',24,'Male','Heavy compound movement athlete.',71,NULL,'2026-06-02 17:05:08','2026-06-02 17:05:08'),(10,'Roberts Jaunzems','robis@gmail.com','$2y$12$7rLeD68c2U.UcKeXKCJrGOhR/9y4mvXKnYb887n0Hfd4XRfTHfrna',NULL,'user','Athletic Performance','81kg','183cm',22,'Male','Explosive athlete focused on performance.',49,NULL,'2026-06-02 17:05:09','2026-06-02 17:05:09'),(11,'a a','a@com.lv','$2y$12$qhTfLgb2t0uo3haUCD42JuvhWwlRfb34XhCF4zoHN4Y4y2ihkVPuO',NULL,'user','General Fitness','74kg','176cm',21,'Male','Building healthy habits.',12,NULL,'2026-06-02 17:05:10','2026-06-02 17:05:10'),(12,'raimonds kristovskis','kristovskis@gmail.com','$2y$12$lGKrhsxWA6dDFYs4BU.wyeOxx5BH1IWQrcAkGTPzk1YtkArQNYXdm',NULL,'user','Muscle Gain','88kg','186cm',27,'Male','Bodybuilding focused athlete.',54,NULL,'2026-06-02 17:05:10','2026-06-02 17:05:10'),(13,'lauris petersons','lauris@gmail.com','$2y$12$HeE7metXZ79Uyr89.7oXMevaWWiq.bSHO.uS0lp3An3dMRrgDrAM.','profiles/sW9XSjgoP0FlSUMsxvboxWOlNF4V8NOMRNX9kT46.png','user',NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,'2026-06-03 04:07:17','2026-06-03 04:43:50'),(14,'juris juris','juris@gmail.com','$2y$12$BdDGri6dqzatfjNZF5QY0OK.YQrUCqiCJeHWVUxlVBaszku17rsz2',NULL,'trainer',NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,'2026-06-03 08:15:25','2026-06-03 08:16:12'),(15,'leons','leons@gmail.com','$2y$12$KgAWsUBC5lyv6VBlLYbFaO95qtOdg4F0uLbHSILT4KZRTvutjYqVu',NULL,'admin',NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL),(16,'janis berzins','janis@gmail.com','$2y$12$wc0xNh6HR36anPJl1YbnrOMAyJH34Qy9cp.2iNj2pkqwVf0JxWDbS',NULL,'trainer',NULL,NULL,NULL,NULL,NULL,NULL,0,NULL,'2026-06-07 18:52:09','2026-06-07 19:02:23');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `workout_log_sets`
--

DROP TABLE IF EXISTS `workout_log_sets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `workout_log_sets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `workout_log_id` bigint unsigned NOT NULL,
  `exercise_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `set_number` int NOT NULL,
  `reps` int NOT NULL,
  `weight` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `workout_log_sets_workout_log_id_foreign` (`workout_log_id`),
  CONSTRAINT `workout_log_sets_workout_log_id_foreign` FOREIGN KEY (`workout_log_id`) REFERENCES `workout_logs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workout_log_sets`
--

LOCK TABLES `workout_log_sets` WRITE;
/*!40000 ALTER TABLE `workout_log_sets` DISABLE KEYS */;
INSERT INTO `workout_log_sets` VALUES (7,4,'Bench Press',1,100,100,'2026-06-05 08:12:22','2026-06-05 08:12:22'),(8,4,'Bench Press',2,100,100,'2026-06-05 08:12:22','2026-06-05 08:12:22'),(9,5,'Bicep Curl',1,10,25,'2026-06-05 08:13:16','2026-06-05 08:13:16'),(10,6,'Bench Press',1,100,100,'2026-06-05 08:13:38','2026-06-05 08:13:38'),(11,6,'Bench Press',2,100,100,'2026-06-05 08:13:38','2026-06-05 08:13:38'),(12,7,'Bench Press',1,100,100,'2026-06-05 08:16:43','2026-06-05 08:16:43'),(13,7,'Bench Press',2,100,100,'2026-06-05 08:16:43','2026-06-05 08:16:43'),(14,8,'Bench Press',1,101,100,'2026-06-05 08:20:50','2026-06-05 08:20:50'),(15,8,'Bench Press',2,101,101,'2026-06-05 08:20:50','2026-06-05 08:20:50'),(17,10,'Bench Press',1,1,1,'2026-06-05 08:26:40','2026-06-05 08:26:40'),(18,10,'Bench Press',2,1,1,'2026-06-05 08:26:40','2026-06-05 08:26:40'),(19,11,'Bench Press',1,100,100,'2026-06-05 08:27:26','2026-06-05 08:27:26'),(20,11,'Bench Press',2,100,100,'2026-06-05 08:27:26','2026-06-05 08:27:26'),(21,12,'Bench Press',1,100,100,'2026-06-05 08:27:57','2026-06-05 08:27:57'),(22,12,'Bench Press',2,100,100,'2026-06-05 08:27:57','2026-06-05 08:27:57'),(23,13,'Bench Press',1,100,100,'2026-06-05 08:31:58','2026-06-05 08:31:58'),(24,13,'Bench Press',2,100,100,'2026-06-05 08:31:58','2026-06-05 08:31:58'),(26,15,'Bicep Curl',1,0,25,'2026-06-05 08:38:17','2026-06-07 18:15:28'),(27,16,'Bench Press',1,100,100,'2026-06-05 08:38:44','2026-06-05 08:38:44'),(28,16,'Bench Press',2,100,100,'2026-06-05 08:38:44','2026-06-05 08:38:44'),(32,18,'Face Pull',1,1,2,'2026-06-05 08:43:07','2026-06-05 08:43:07'),(33,18,'Face Pull',2,3,6,'2026-06-05 08:43:07','2026-06-05 08:43:07'),(40,22,'roku diena',1,1,2,'2026-06-05 09:51:40','2026-06-05 09:51:40'),(41,22,'roku diena',2,3,4,'2026-06-05 09:51:40','2026-06-05 09:51:40'),(42,23,'roku diena',1,1,2,'2026-06-05 09:52:21','2026-06-05 09:52:21'),(43,23,'roku diena',2,3,4,'2026-06-05 09:52:21','2026-06-05 09:52:21'),(44,24,'Face Pull',1,1,-1,'2026-06-05 09:53:00','2026-06-07 18:08:10'),(45,24,'Face Pull',2,3,6,'2026-06-05 09:53:00','2026-06-07 18:08:10'),(46,25,'roku diena',1,1,-8,'2026-06-05 09:53:52','2026-06-07 18:08:06'),(47,25,'roku diena',2,3,4,'2026-06-05 09:53:52','2026-06-07 18:08:06'),(50,28,'Face Pull',1,15,0,'2026-06-05 09:54:32','2026-06-07 18:14:23'),(51,28,'Face Pull',2,0,0,'2026-06-05 09:54:32','2026-06-07 18:14:23');
/*!40000 ALTER TABLE `workout_log_sets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `workout_logs`
--

DROP TABLE IF EXISTS `workout_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `workout_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `training_plan_id` bigint unsigned NOT NULL,
  `duration_seconds` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `workout_logs_user_id_foreign` (`user_id`),
  KEY `workout_logs_training_plan_id_foreign` (`training_plan_id`),
  CONSTRAINT `workout_logs_training_plan_id_foreign` FOREIGN KEY (`training_plan_id`) REFERENCES `training_plans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `workout_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workout_logs`
--

LOCK TABLES `workout_logs` WRITE;
/*!40000 ALTER TABLE `workout_logs` DISABLE KEYS */;
INSERT INTO `workout_logs` VALUES (4,4,7,26,'2026-06-05 08:11:55','2026-06-05 08:12:22'),(5,4,11,0,'2026-06-05 08:13:15','2026-06-05 08:13:16'),(6,4,7,5,'2026-06-05 08:13:32','2026-06-05 08:13:38'),(7,4,7,1,'2026-06-05 08:16:42','2026-06-05 08:16:43'),(8,4,7,15,'2026-06-05 08:20:34','2026-06-05 08:20:50'),(10,4,7,8,'2026-06-05 08:26:31','2026-06-05 08:26:40'),(11,4,7,1,'2026-06-05 08:27:24','2026-06-05 08:27:26'),(12,4,7,0,'2026-06-05 08:27:56','2026-06-05 08:27:57'),(13,4,7,0,'2026-06-05 08:31:57','2026-06-05 08:31:58'),(15,4,11,0,'2026-06-05 08:38:16','2026-06-05 08:38:17'),(16,4,7,2,'2026-06-05 08:38:41','2026-06-05 08:38:44'),(18,6,14,1,'2026-06-05 08:43:05','2026-06-05 08:43:07'),(22,6,13,6,'2026-06-05 09:51:34','2026-06-05 09:51:40'),(23,6,13,1,'2026-06-05 09:52:19','2026-06-05 09:52:21'),(24,6,14,60,'2026-06-05 09:53:00','2026-06-07 18:01:39'),(25,6,13,60,'2026-06-05 09:53:51','2026-06-07 18:08:00'),(28,6,14,1260,'2026-06-05 09:54:31','2026-06-07 18:01:26');
/*!40000 ALTER TABLE `workout_logs` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-08  3:33:13
