/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `api_audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `api_audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `company_id` bigint unsigned DEFAULT NULL,
  `method` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `endpoint` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_code` smallint NOT NULL,
  `response_time_ms` int unsigned DEFAULT NULL,
  `request_params` json DEFAULT NULL,
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `api_audit_logs_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `api_audit_logs_company_id_created_at_index` (`company_id`,`created_at`),
  KEY `api_audit_logs_endpoint_method_index` (`endpoint`,`method`),
  CONSTRAINT `api_audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `appointments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `appointments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `job_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_size` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preferred_date` date NOT NULL,
  `preferred_time` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `timezone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'UTC',
  `message` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `asset_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asset_assignments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` bigint unsigned NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `assigned_to` bigint unsigned DEFAULT NULL,
  `assigned_to_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assigned_from` bigint unsigned DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `project_id` bigint unsigned DEFAULT NULL,
  `condition_before` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `condition_after` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meter_reading` decimal(12,1) DEFAULT NULL,
  `checkout_date` date DEFAULT NULL,
  `expected_return_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `performed_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `asset_assignments_asset_id_foreign` (`asset_id`),
  KEY `asset_assignments_assigned_to_foreign` (`assigned_to`),
  KEY `asset_assignments_assigned_from_foreign` (`assigned_from`),
  KEY `asset_assignments_project_id_foreign` (`project_id`),
  KEY `asset_assignments_performed_by_foreign` (`performed_by`),
  CONSTRAINT `asset_assignments_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `asset_assignments_assigned_from_foreign` FOREIGN KEY (`assigned_from`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `asset_assignments_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `asset_assignments_performed_by_foreign` FOREIGN KEY (`performed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `asset_assignments_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `asset_maintenance_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asset_maintenance_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` bigint unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
  `scheduled_date` date DEFAULT NULL,
  `completed_date` date DEFAULT NULL,
  `next_service_date` date DEFAULT NULL,
  `cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `downtime_hours` decimal(8,1) DEFAULT NULL,
  `meter_reading` decimal(12,1) DEFAULT NULL,
  `vendor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parts_used` text COLLATE utf8mb4_unicode_ci,
  `condition_before` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `condition_after` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `performed_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `asset_maintenance_logs_asset_id_foreign` (`asset_id`),
  KEY `asset_maintenance_logs_performed_by_foreign` (`performed_by`),
  CONSTRAINT `asset_maintenance_logs_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `asset_maintenance_logs_performed_by_foreign` FOREIGN KEY (`performed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `assets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `assets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `asset_tag` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `asset_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `brand` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serial_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_id` bigint unsigned DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `purchase_cost` decimal(12,2) DEFAULT NULL,
  `warranty_expiry` date DEFAULT NULL,
  `last_service_date` date DEFAULT NULL,
  `next_service_date` date DEFAULT NULL,
  `depreciation_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'straight_line',
  `useful_life_years` int NOT NULL DEFAULT '5',
  `salvage_value` decimal(12,2) NOT NULL DEFAULT '0.00',
  `qr_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warranty_expires_at` date DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `condition` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meter_reading` decimal(12,1) DEFAULT NULL,
  `meter_unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_holder_id` bigint unsigned DEFAULT NULL,
  `current_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warehouse_id` bigint unsigned DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `replaced_by_id` bigint unsigned DEFAULT NULL,
  `replaces_id` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assets_company_id_foreign` (`company_id`),
  KEY `assets_client_id_foreign` (`client_id`),
  KEY `assets_product_id_foreign` (`product_id`),
  KEY `assets_cde_project_id_foreign` (`cde_project_id`),
  KEY `assets_current_holder_id_foreign` (`current_holder_id`),
  KEY `assets_warehouse_id_foreign` (`warehouse_id`),
  KEY `assets_created_by_foreign` (`created_by`),
  KEY `assets_replaced_by_id_foreign` (`replaced_by_id`),
  KEY `assets_replaces_id_foreign` (`replaces_id`),
  CONSTRAINT `assets_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `assets_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  CONSTRAINT `assets_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assets_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `assets_current_holder_id_foreign` FOREIGN KEY (`current_holder_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `assets_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `assets_replaced_by_id_foreign` FOREIGN KEY (`replaced_by_id`) REFERENCES `assets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `assets_replaces_id_foreign` FOREIGN KEY (`replaces_id`) REFERENCES `assets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `assets_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attachments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `attachable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` bigint unsigned NOT NULL DEFAULT '0',
  `description` text COLLATE utf8mb4_unicode_ci,
  `uploaded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attachments_company_id_foreign` (`company_id`),
  KEY `attachments_attachable_type_attachable_id_index` (`attachable_type`,`attachable_id`),
  KEY `attachments_uploaded_by_foreign` (`uploaded_by`),
  CONSTRAINT `attachments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attachments_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `attendances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `clock_in` time DEFAULT NULL,
  `clock_out` time DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'present',
  `total_hours` decimal(5,2) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendances_company_id_foreign` (`company_id`),
  KEY `attendances_user_id_foreign` (`user_id`),
  CONSTRAINT `attendances_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendances_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `billing_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `billing_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `period` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'YYYY-MM format',
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `base_platform_fee` decimal(12,2) NOT NULL DEFAULT '0.00',
  `project_fees` decimal(12,2) NOT NULL DEFAULT '0.00',
  `module_fees` decimal(12,2) NOT NULL DEFAULT '0.00',
  `addon_fees` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT 'Extra users, storage, etc.',
  `discount_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft' COMMENT 'draft, finalized, paid, overdue, void',
  `active_projects_count` int NOT NULL DEFAULT '0',
  `active_users_count` int NOT NULL DEFAULT '0',
  `storage_used_gb` decimal(8,2) NOT NULL DEFAULT '0.00',
  `line_items` json DEFAULT NULL COMMENT 'Detailed breakdown of charges',
  `finalized_at` timestamp NULL DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `payment_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `billing_records_company_id_period_unique` (`company_id`,`period`),
  KEY `billing_records_period_index` (`period`),
  KEY `billing_records_status_index` (`status`),
  KEY `billing_records_company_id_status_index` (`company_id`,`status`),
  CONSTRAINT `billing_records_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `blocked_ips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `blocked_ips` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cidr_range` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `blocked_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `hit_count` bigint unsigned NOT NULL DEFAULT '0',
  `last_blocked_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blocked_ips_ip_address_cidr_range_unique` (`ip_address`,`cidr_range`),
  KEY `blocked_ips_ip_address_index` (`ip_address`),
  KEY `blocked_ips_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `boq_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `boq_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `boq_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `item_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `quantity_completed` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `actual_quantity` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `actual_cost` decimal(14,2) NOT NULL DEFAULT '0.00',
  `variance_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `variance_percent` decimal(8,2) NOT NULL DEFAULT '0.00',
  `last_synced_at` timestamp NULL DEFAULT NULL,
  `unit_rate` decimal(12,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `is_variation` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `boq_items_boq_id_foreign` (`boq_id`),
  KEY `boq_items_product_id_foreign` (`product_id`),
  CONSTRAINT `boq_items_boq_id_foreign` FOREIGN KEY (`boq_id`) REFERENCES `boqs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `boq_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `boq_material_usages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `boq_material_usages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `boq_item_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `quantity_used` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `usage_date` date NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `recorded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `boq_material_usages_boq_item_id_foreign` (`boq_item_id`),
  KEY `boq_material_usages_product_id_foreign` (`product_id`),
  KEY `boq_material_usages_recorded_by_foreign` (`recorded_by`),
  CONSTRAINT `boq_material_usages_boq_item_id_foreign` FOREIGN KEY (`boq_item_id`) REFERENCES `boq_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `boq_material_usages_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `boq_material_usages_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `boq_revisions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `boq_revisions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `boq_id` bigint unsigned NOT NULL,
  `revision_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `change_description` text COLLATE utf8mb4_unicode_ci,
  `snapshot` json DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `boq_revisions_boq_id_foreign` (`boq_id`),
  KEY `boq_revisions_created_by_foreign` (`created_by`),
  CONSTRAINT `boq_revisions_boq_id_foreign` FOREIGN KEY (`boq_id`) REFERENCES `boqs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `boq_revisions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `boq_variance_alerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `boq_variance_alerts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `boq_id` bigint unsigned NOT NULL,
  `boq_item_id` bigint unsigned DEFAULT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `severity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alert_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'overrun',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `budgeted_value` decimal(14,2) NOT NULL DEFAULT '0.00',
  `actual_value` decimal(14,2) NOT NULL DEFAULT '0.00',
  `variance_percent` decimal(8,2) NOT NULL DEFAULT '0.00',
  `is_acknowledged` tinyint(1) NOT NULL DEFAULT '0',
  `acknowledged_by` bigint unsigned DEFAULT NULL,
  `acknowledged_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `boq_variance_alerts_company_id_foreign` (`company_id`),
  KEY `boq_variance_alerts_boq_item_id_foreign` (`boq_item_id`),
  KEY `boq_variance_alerts_acknowledged_by_foreign` (`acknowledged_by`),
  KEY `boq_variance_alerts_boq_id_severity_index` (`boq_id`,`severity`),
  KEY `boq_variance_alerts_cde_project_id_is_acknowledged_index` (`cde_project_id`,`is_acknowledged`),
  CONSTRAINT `boq_variance_alerts_acknowledged_by_foreign` FOREIGN KEY (`acknowledged_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `boq_variance_alerts_boq_id_foreign` FOREIGN KEY (`boq_id`) REFERENCES `boqs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `boq_variance_alerts_boq_item_id_foreign` FOREIGN KEY (`boq_item_id`) REFERENCES `boq_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `boq_variance_alerts_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `boq_variance_alerts_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `boqs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `boqs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `contract_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `boq_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `total_value` decimal(14,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `boqs_company_id_foreign` (`company_id`),
  KEY `boqs_cde_project_id_foreign` (`cde_project_id`),
  KEY `boqs_contract_id_foreign` (`contract_id`),
  KEY `boqs_created_by_foreign` (`created_by`),
  KEY `boqs_approved_by_foreign` (`approved_by`),
  CONSTRAINT `boqs_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `boqs_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `boqs_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `boqs_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `boqs_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `budget_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `budget_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `budget_id` bigint unsigned NOT NULL,
  `cost_code_id` bigint unsigned DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `budgeted_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `actual_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `budget_items_budget_id_foreign` (`budget_id`),
  KEY `budget_items_cost_code_id_foreign` (`cost_code_id`),
  CONSTRAINT `budget_items_budget_id_foreign` FOREIGN KEY (`budget_id`) REFERENCES `budgets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `budget_items_cost_code_id_foreign` FOREIGN KEY (`cost_code_id`) REFERENCES `cost_codes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `budgets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `budgets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `contract_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `budgets_company_id_foreign` (`company_id`),
  KEY `budgets_cde_project_id_foreign` (`cde_project_id`),
  KEY `budgets_contract_id_foreign` (`contract_id`),
  CONSTRAINT `budgets_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `budgets_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `budgets_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
DROP TABLE IF EXISTS `cde_activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cde_activity_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `loggable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `loggable_id` bigint unsigned NOT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `changes` json DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cde_activity_logs_company_id_foreign` (`company_id`),
  KEY `cde_activity_logs_loggable_type_loggable_id_index` (`loggable_type`,`loggable_id`),
  KEY `cde_activity_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `cde_activity_logs_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cde_activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cde_document_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cde_document_comments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cde_document_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `is_resolved` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cde_document_comments_cde_document_id_foreign` (`cde_document_id`),
  KEY `cde_document_comments_user_id_foreign` (`user_id`),
  KEY `cde_document_comments_parent_id_foreign` (`parent_id`),
  CONSTRAINT `cde_document_comments_cde_document_id_foreign` FOREIGN KEY (`cde_document_id`) REFERENCES `cde_documents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cde_document_comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `cde_document_comments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cde_document_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cde_document_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cde_document_versions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cde_document_id` bigint unsigned NOT NULL,
  `version_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` bigint unsigned NOT NULL DEFAULT '0',
  `change_description` text COLLATE utf8mb4_unicode_ci,
  `uploaded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cde_document_versions_cde_document_id_foreign` (`cde_document_id`),
  KEY `cde_document_versions_uploaded_by_foreign` (`uploaded_by`),
  CONSTRAINT `cde_document_versions_cde_document_id_foreign` FOREIGN KEY (`cde_document_id`) REFERENCES `cde_documents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cde_document_versions_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cde_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cde_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_folder_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `revision` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'wip',
  `discipline` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` bigint unsigned NOT NULL DEFAULT '0',
  `description` text COLLATE utf8mb4_unicode_ci,
  `uploaded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cde_documents_company_id_foreign` (`company_id`),
  KEY `cde_documents_cde_folder_id_foreign` (`cde_folder_id`),
  KEY `cde_documents_cde_project_id_foreign` (`cde_project_id`),
  KEY `cde_documents_uploaded_by_foreign` (`uploaded_by`),
  CONSTRAINT `cde_documents_cde_folder_id_foreign` FOREIGN KEY (`cde_folder_id`) REFERENCES `cde_folders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cde_documents_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cde_documents_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cde_documents_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cde_folders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cde_folders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `suitability_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cde_folders_company_id_foreign` (`company_id`),
  KEY `cde_folders_cde_project_id_foreign` (`cde_project_id`),
  KEY `cde_folders_parent_id_foreign` (`parent_id`),
  CONSTRAINT `cde_folders_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cde_folders_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cde_folders_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `cde_folders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cde_project_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cde_project_members` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cde_project_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'member',
  `invited_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cde_project_members_cde_project_id_user_id_unique` (`cde_project_id`,`user_id`),
  KEY `cde_project_members_user_id_foreign` (`user_id`),
  KEY `cde_project_members_invited_by_foreign` (`invited_by`),
  CONSTRAINT `cde_project_members_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cde_project_members_invited_by_foreign` FOREIGN KEY (`invited_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cde_project_members_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cde_projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cde_projects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `project_type` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'building, road, energy, water, telecom, industrial',
  `energy_sector` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'solar, wind, hydro, thermal, oil_gas, transmission, distribution',
  `capacity_mw` decimal(10,2) DEFAULT NULL COMMENT 'Installed capacity in MW',
  `voltage_level` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'e.g. 11kV, 33kV, 132kV, 400kV',
  `grid_connection_point` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `commissioning_status` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'pre_commissioning, mechanical_completion, energization, pac, fac',
  `commercial_operation_date` date DEFAULT NULL COMMENT 'COD — when project starts generating revenue',
  `regulatory_license` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ERA license or equivalent',
  `road_class` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'national_trunk, district, urban, community, feeder',
  `road_length_km` decimal(10,3) DEFAULT NULL COMMENT 'Total road length in km',
  `road_width_m` decimal(6,2) DEFAULT NULL COMMENT 'Carriageway width in metres',
  `number_of_lanes` tinyint unsigned DEFAULT NULL,
  `pavement_type` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'flexible, rigid, composite, gravel, earth',
  `design_speed_kph` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Design speed e.g. 60, 80, 120',
  `chainage_start` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Start chainage e.g. 0+000',
  `chainage_end` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'End chainage e.g. 45+350',
  `terrain` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'flat, rolling, mountainous',
  `funding_source` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'World Bank, AfDB, GoU, PPP, etc.',
  `road_authority` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'UNRA, KCCA, District LG, etc.',
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `billing_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active' COMMENT 'active = billed, paused = not billed, archived = project closed',
  `monthly_rate` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT 'Calculated monthly cost for this project',
  `billing_started_at` timestamp NULL DEFAULT NULL,
  `billing_paused_at` timestamp NULL DEFAULT NULL,
  `billing_notes` text COLLATE utf8mb4_unicode_ci,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `baseline_saved_at` date DEFAULT NULL,
  `schedule_mode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'auto' COMMENT 'auto or manual scheduling',
  `default_calendar` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'standard',
  `working_days` json DEFAULT NULL COMMENT 'Array of working day configs, holidays, etc.',
  `budget` decimal(14,2) DEFAULT NULL,
  `currency` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_symbol` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_position` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'before',
  `project_cost` decimal(14,2) DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manager_id` bigint unsigned DEFAULT NULL,
  `client_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cde_projects_company_id_foreign` (`company_id`),
  KEY `cde_projects_manager_id_foreign` (`manager_id`),
  KEY `cde_projects_client_id_foreign` (`client_id`),
  KEY `cde_projects_billing_status_index` (`billing_status`),
  CONSTRAINT `cde_projects_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cde_projects_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cde_projects_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `certificates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `certificates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `contract_id` bigint unsigned NOT NULL,
  `certificate_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'interim',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `gross_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `net_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `period_from` date DEFAULT NULL,
  `period_to` date DEFAULT NULL,
  `certified_date` date DEFAULT NULL,
  `certified_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `certificates_company_id_foreign` (`company_id`),
  KEY `certificates_contract_id_foreign` (`contract_id`),
  KEY `certificates_certified_by_foreign` (`certified_by`),
  CONSTRAINT `certificates_certified_by_foreign` FOREIGN KEY (`certified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `certificates_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `certificates_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `change_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `change_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `contract_id` bigint unsigned NOT NULL,
  `co_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `time_extension_days` int NOT NULL DEFAULT '0',
  `requested_by` bigint unsigned DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `change_orders_company_id_foreign` (`company_id`),
  KEY `change_orders_contract_id_foreign` (`contract_id`),
  KEY `change_orders_requested_by_foreign` (`requested_by`),
  KEY `change_orders_approved_by_foreign` (`approved_by`),
  CONSTRAINT `change_orders_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `change_orders_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `change_orders_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `change_orders_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `claims`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `claims` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `contract_id` bigint unsigned NOT NULL,
  `claim_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `claimed_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `approved_amount` decimal(14,2) DEFAULT NULL,
  `submitted_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `claims_company_id_foreign` (`company_id`),
  KEY `claims_contract_id_foreign` (`contract_id`),
  KEY `claims_submitted_by_foreign` (`submitted_by`),
  CONSTRAINT `claims_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `claims_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `claims_submitted_by_foreign` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `clients_company_id_foreign` (`company_id`),
  KEY `clients_user_id_foreign` (`user_id`),
  CONSTRAINT `clients_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `clients_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `companies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `favicon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `primary_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `secondary_color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `timezone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'UTC',
  `date_format` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Y-m-d',
  `time_format` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'H:i',
  `currency` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `currency_symbol` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '$',
  `currency_position` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'before',
  `currency_space` tinyint(1) NOT NULL DEFAULT '0',
  `subscription_id` bigint unsigned DEFAULT NULL,
  `billing_cycle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'monthly',
  `subscription_starts_at` timestamp NULL DEFAULT NULL,
  `subscription_expires_at` timestamp NULL DEFAULT NULL,
  `max_users` int NOT NULL DEFAULT '5',
  `extra_users` int unsigned NOT NULL DEFAULT '0',
  `max_projects` int NOT NULL DEFAULT '10',
  `extra_projects` int unsigned NOT NULL DEFAULT '0',
  `max_storage_gb` int NOT NULL DEFAULT '5',
  `extra_storage_gb` int unsigned NOT NULL DEFAULT '0',
  `current_storage_bytes` bigint NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_trial` tinyint(1) NOT NULL DEFAULT '1',
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `activated_at` timestamp NULL DEFAULT NULL,
  `suspended_at` timestamp NULL DEFAULT NULL,
  `suspension_reason` text COLLATE utf8mb4_unicode_ci,
  `settings` json DEFAULT NULL,
  `configurable_options` json DEFAULT NULL COMMENT 'Company-specific dropdown options: weather, specialties, tender categories, etc.',
  `invoice_config` json DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `companies_slug_unique` (`slug`),
  KEY `companies_subscription_id_foreign` (`subscription_id`),
  CONSTRAINT `companies_subscription_id_foreign` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `company_module_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `company_module_access` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `module_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `enabled_at` timestamp NULL DEFAULT NULL,
  `enabled_by` bigint unsigned DEFAULT NULL,
  `disabled_at` timestamp NULL DEFAULT NULL,
  `disabled_by` bigint unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `company_module_access_company_id_module_code_unique` (`company_id`,`module_code`),
  CONSTRAINT `company_module_access_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `compliance_certificates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `compliance_certificates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `contract_id` bigint unsigned DEFAULT NULL,
  `vendor_id` bigint unsigned DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issuing_authority` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `compliance_certificates_cde_project_id_foreign` (`cde_project_id`),
  KEY `compliance_certificates_contract_id_foreign` (`contract_id`),
  KEY `compliance_certificates_vendor_id_foreign` (`vendor_id`),
  KEY `compliance_certificates_created_by_foreign` (`created_by`),
  KEY `compliance_certificates_company_id_status_index` (`company_id`,`status`),
  KEY `compliance_certificates_expiry_date_index` (`expiry_date`),
  CONSTRAINT `compliance_certificates_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `compliance_certificates_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `compliance_certificates_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `compliance_certificates_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `compliance_certificates_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contacts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `organization` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `designation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contacts_company_id_foreign` (`company_id`),
  CONSTRAINT `contacts_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `contract_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contract_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `contract_id` bigint unsigned NOT NULL,
  `company_id` bigint unsigned NOT NULL,
  `reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'payment',
  `amount` decimal(15,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contract_payments_company_id_foreign` (`company_id`),
  KEY `contract_payments_created_by_foreign` (`created_by`),
  KEY `contract_payments_contract_id_payment_date_index` (`contract_id`,`payment_date`),
  CONSTRAINT `contract_payments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `contract_payments_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `contract_payments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `contract_project`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contract_project` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `contract_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned NOT NULL,
  `budget_allocation` decimal(14,2) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `contract_project_contract_id_cde_project_id_unique` (`contract_id`,`cde_project_id`),
  KEY `contract_project_cde_project_id_foreign` (`cde_project_id`),
  CONSTRAINT `contract_project_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `contract_project_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `contracts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contracts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `vendor_id` bigint unsigned DEFAULT NULL,
  `contract_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'lump_sum',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `original_value` decimal(14,2) NOT NULL DEFAULT '0.00',
  `revised_value` decimal(14,2) NOT NULL DEFAULT '0.00',
  `amount_paid` decimal(14,2) NOT NULL DEFAULT '0.00',
  `retainage_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `retainage_held` decimal(15,2) NOT NULL DEFAULT '0.00',
  `retainage_released` decimal(15,2) NOT NULL DEFAULT '0.00',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `scope_of_work` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contracts_company_id_foreign` (`company_id`),
  KEY `contracts_vendor_id_foreign` (`vendor_id`),
  KEY `contracts_created_by_foreign` (`created_by`),
  CONSTRAINT `contracts_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `contracts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `contracts_vendor_id_foreign` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cost_actuals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cost_actuals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cost_code_id` bigint unsigned DEFAULT NULL,
  `contract_id` bigint unsigned DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `transaction_date` date NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cost_actuals_company_id_foreign` (`company_id`),
  KEY `cost_actuals_cost_code_id_foreign` (`cost_code_id`),
  KEY `cost_actuals_contract_id_foreign` (`contract_id`),
  CONSTRAINT `cost_actuals_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cost_actuals_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cost_actuals_cost_code_id_foreign` FOREIGN KEY (`cost_code_id`) REFERENCES `cost_codes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cost_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cost_codes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `parent_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cost_codes_company_id_foreign` (`company_id`),
  KEY `cost_codes_parent_id_foreign` (`parent_id`),
  CONSTRAINT `cost_codes_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cost_codes_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `cost_codes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `crew_attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `crew_attendance` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `attendance_date` date NOT NULL,
  `clock_in` time DEFAULT NULL,
  `clock_out` time DEFAULT NULL,
  `hours_worked` decimal(5,2) DEFAULT NULL,
  `overtime_hours` decimal(5,2) NOT NULL DEFAULT '0.00',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'present',
  `site_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `approved_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `crew_attendance_company_id_user_id_attendance_date_unique` (`company_id`,`user_id`,`attendance_date`),
  KEY `crew_attendance_user_id_foreign` (`user_id`),
  KEY `crew_attendance_approved_by_foreign` (`approved_by`),
  KEY `crew_attendance_company_id_attendance_date_index` (`company_id`,`attendance_date`),
  KEY `attendance_company_date_idx` (`company_id`,`attendance_date`),
  KEY `attendance_company_status_idx` (`company_id`,`status`),
  KEY `attendance_project_date_idx` (`cde_project_id`,`attendance_date`),
  CONSTRAINT `crew_attendance_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `crew_attendance_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `crew_attendance_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `crew_attendance_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `daily_site_diaries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `daily_site_diaries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned NOT NULL,
  `diary_date` date NOT NULL,
  `weather` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `temperature` decimal(5,1) DEFAULT NULL,
  `humidity_percent` decimal(5,1) DEFAULT NULL COMMENT 'Relative humidity %',
  `wind_speed_kmh` decimal(5,1) DEFAULT NULL COMMENT 'Wind speed in km/h',
  `wind_direction` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'N, NE, E, SE, S, SW, W, NW',
  `noise_level_db` decimal(5,1) DEFAULT NULL COMMENT 'Ambient noise in dB(A)',
  `dust_level_pm10` decimal(7,2) DEFAULT NULL COMMENT 'PM10 concentration µg/m³',
  `water_ph` decimal(4,2) DEFAULT NULL COMMENT 'Water pH if monitoring discharge',
  `environmental_notes` text COLLATE utf8mb4_unicode_ci,
  `solar_irradiance` decimal(6,1) DEFAULT NULL COMMENT 'W/m² for solar projects',
  `chainage_from` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Start chainage of work e.g. 12+450',
  `chainage_to` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'End chainage of work e.g. 12+850',
  `road_layer` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'subgrade, improved_subgrade, subbase, base, primer, binder, wearing, shoulder',
  `layer_thickness_mm` decimal(6,1) DEFAULT NULL COMMENT 'Compacted layer thickness in mm',
  `material_source` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Quarry or borrow pit name',
  `truck_loads` int unsigned DEFAULT NULL COMMENT 'Number of material truck loads delivered',
  `compaction_achieved` decimal(5,1) DEFAULT NULL COMMENT 'Compaction % achieved (MDD)',
  `compaction_required` decimal(5,1) DEFAULT NULL COMMENT 'Required compaction % (specification)',
  `moisture_content` decimal(5,1) DEFAULT NULL COMMENT 'Field moisture content %',
  `survey_data` text COLLATE utf8mb4_unicode_ci COMMENT 'Survey levels, alignment checks',
  `traffic_management_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Diversions, flagmen, lane closures',
  `workers_on_site` int NOT NULL DEFAULT '0',
  `subcontractor_workers` int NOT NULL DEFAULT '0',
  `workforce_breakdown` json DEFAULT NULL,
  `equipment_on_site` int NOT NULL DEFAULT '0',
  `equipment_list` json DEFAULT NULL,
  `work_performed` text COLLATE utf8mb4_unicode_ci,
  `work_planned_tomorrow` text COLLATE utf8mb4_unicode_ci,
  `delays` text COLLATE utf8mb4_unicode_ci,
  `safety_observations` text COLLATE utf8mb4_unicode_ci,
  `quality_observations` text COLLATE utf8mb4_unicode_ci,
  `visitor_log` text COLLATE utf8mb4_unicode_ci,
  `deliveries` text COLLATE utf8mb4_unicode_ci,
  `photos` json DEFAULT NULL,
  `prepared_by` bigint unsigned NOT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `daily_site_diaries_company_id_cde_project_id_diary_date_unique` (`company_id`,`cde_project_id`,`diary_date`),
  KEY `daily_site_diaries_prepared_by_foreign` (`prepared_by`),
  KEY `daily_site_diaries_approved_by_foreign` (`approved_by`),
  KEY `daily_site_diaries_diary_date_index` (`diary_date`),
  KEY `diary_company_date_idx` (`company_id`,`diary_date`),
  KEY `diary_project_date_idx` (`cde_project_id`,`diary_date`),
  CONSTRAINT `daily_site_diaries_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `daily_site_diaries_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `daily_site_diaries_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `daily_site_diaries_prepared_by_foreign` FOREIGN KEY (`prepared_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `daily_site_log_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `daily_site_log_tasks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `daily_site_log_id` bigint unsigned NOT NULL,
  `task_id` bigint unsigned NOT NULL,
  `progress_today` int NOT NULL DEFAULT '0' COMMENT '% progress made today',
  `cumulative_progress` int DEFAULT NULL COMMENT 'Cumulative % after this update',
  `hours_worked` decimal(6,2) NOT NULL DEFAULT '0.00',
  `workers_assigned` int NOT NULL DEFAULT '0',
  `status_update` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'not_started, in_progress, completed, blocked',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `daily_site_log_tasks_daily_site_log_id_task_id_unique` (`daily_site_log_id`,`task_id`),
  KEY `daily_site_log_tasks_task_id_foreign` (`task_id`),
  CONSTRAINT `daily_site_log_tasks_daily_site_log_id_foreign` FOREIGN KEY (`daily_site_log_id`) REFERENCES `daily_site_logs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `daily_site_log_tasks_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `daily_site_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `daily_site_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `log_date` date NOT NULL,
  `weather` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `temperature_high` decimal(5,2) DEFAULT NULL,
  `temperature_low` decimal(5,2) DEFAULT NULL,
  `workers_on_site` int NOT NULL DEFAULT '0',
  `visitors_on_site` int NOT NULL DEFAULT '0',
  `work_performed` text COLLATE utf8mb4_unicode_ci,
  `materials_received` text COLLATE utf8mb4_unicode_ci,
  `equipment_used` text COLLATE utf8mb4_unicode_ci,
  `delays` text COLLATE utf8mb4_unicode_ci,
  `safety_incidents` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_by` bigint unsigned DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `daily_site_logs_company_id_foreign` (`company_id`),
  KEY `daily_site_logs_cde_project_id_foreign` (`cde_project_id`),
  KEY `daily_site_logs_created_by_foreign` (`created_by`),
  KEY `daily_site_logs_approved_by_foreign` (`approved_by`),
  CONSTRAINT `daily_site_logs_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `daily_site_logs_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `daily_site_logs_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `daily_site_logs_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `delivery_note_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_note_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `delivery_note_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'each',
  `quantity_dispatched` decimal(12,2) NOT NULL DEFAULT '0.00',
  `quantity_received` decimal(12,2) NOT NULL DEFAULT '0.00',
  `condition` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'good',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `delivery_note_items_delivery_note_id_foreign` (`delivery_note_id`),
  KEY `delivery_note_items_product_id_foreign` (`product_id`),
  CONSTRAINT `delivery_note_items_delivery_note_id_foreign` FOREIGN KEY (`delivery_note_id`) REFERENCES `delivery_notes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `delivery_note_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `delivery_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `dn_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `material_issuance_id` bigint unsigned DEFAULT NULL,
  `purchase_order_id` bigint unsigned DEFAULT NULL,
  `stock_transfer_id` bigint unsigned DEFAULT NULL,
  `destination` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `destination_contact` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `destination_phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `driver_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `driver_phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warehouse_id` bigint unsigned DEFAULT NULL,
  `from_warehouse_id` bigint unsigned DEFAULT NULL,
  `to_warehouse_id` bigint unsigned DEFAULT NULL,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `dispatch_date` date DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `delivery_proof` text COLLATE utf8mb4_unicode_ci,
  `dispatched_by` bigint unsigned DEFAULT NULL,
  `received_by_user` bigint unsigned DEFAULT NULL,
  `received_by_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `received_by_signature` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `milestone_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `delivery_notes_company_id_foreign` (`company_id`),
  KEY `delivery_notes_cde_project_id_foreign` (`cde_project_id`),
  KEY `delivery_notes_material_issuance_id_foreign` (`material_issuance_id`),
  KEY `delivery_notes_purchase_order_id_foreign` (`purchase_order_id`),
  KEY `delivery_notes_warehouse_id_foreign` (`warehouse_id`),
  KEY `delivery_notes_dispatched_by_foreign` (`dispatched_by`),
  KEY `delivery_notes_received_by_user_foreign` (`received_by_user`),
  KEY `delivery_notes_milestone_id_foreign` (`milestone_id`),
  KEY `delivery_notes_dn_number_index` (`dn_number`),
  KEY `delivery_notes_stock_transfer_id_foreign` (`stock_transfer_id`),
  KEY `delivery_notes_from_warehouse_id_foreign` (`from_warehouse_id`),
  KEY `delivery_notes_to_warehouse_id_foreign` (`to_warehouse_id`),
  CONSTRAINT `delivery_notes_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `delivery_notes_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `delivery_notes_dispatched_by_foreign` FOREIGN KEY (`dispatched_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `delivery_notes_from_warehouse_id_foreign` FOREIGN KEY (`from_warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `delivery_notes_material_issuance_id_foreign` FOREIGN KEY (`material_issuance_id`) REFERENCES `material_issuances` (`id`) ON DELETE SET NULL,
  CONSTRAINT `delivery_notes_milestone_id_foreign` FOREIGN KEY (`milestone_id`) REFERENCES `milestones` (`id`) ON DELETE SET NULL,
  CONSTRAINT `delivery_notes_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `delivery_notes_received_by_user_foreign` FOREIGN KEY (`received_by_user`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `delivery_notes_stock_transfer_id_foreign` FOREIGN KEY (`stock_transfer_id`) REFERENCES `stock_transfers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `delivery_notes_to_warehouse_id_foreign` FOREIGN KEY (`to_warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `delivery_notes_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `document_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `document_history` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `document_id` bigint unsigned NOT NULL,
  `version_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `metadata` json DEFAULT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_history_version_id_foreign` (`version_id`),
  KEY `document_history_document_id_created_at_index` (`document_id`,`created_at`),
  KEY `document_history_action_index` (`action`),
  KEY `document_history_user_id_index` (`user_id`),
  CONSTRAINT `document_history_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `project_documents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_history_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `document_history_version_id_foreign` FOREIGN KEY (`version_id`) REFERENCES `document_versions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `document_shares`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `document_shares` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cde_document_id` bigint unsigned NOT NULL,
  `shared_by` bigint unsigned NOT NULL,
  `shared_with` bigint unsigned DEFAULT NULL,
  `share_token` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `permission` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'view',
  `shared_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `last_accessed_at` timestamp NULL DEFAULT NULL,
  `access_count` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `document_shares_share_token_unique` (`share_token`),
  KEY `document_shares_shared_by_foreign` (`shared_by`),
  KEY `document_shares_shared_with_foreign` (`shared_with`),
  KEY `document_shares_cde_document_id_shared_with_index` (`cde_document_id`,`shared_with`),
  KEY `document_shares_share_token_index` (`share_token`),
  CONSTRAINT `document_shares_cde_document_id_foreign` FOREIGN KEY (`cde_document_id`) REFERENCES `cde_documents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_shares_shared_by_foreign` FOREIGN KEY (`shared_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_shares_shared_with_foreign` FOREIGN KEY (`shared_with`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `document_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `document_submissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `discipline` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stage` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `due_date` date DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `submitted_at` timestamp NULL DEFAULT NULL,
  `submitted_by` bigint unsigned DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `reviewed_by` bigint unsigned DEFAULT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` bigint unsigned DEFAULT NULL,
  `review_notes` text COLLATE utf8mb4_unicode_ci,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_submissions_company_id_foreign` (`company_id`),
  KEY `document_submissions_submitted_by_foreign` (`submitted_by`),
  KEY `document_submissions_reviewed_by_foreign` (`reviewed_by`),
  KEY `document_submissions_cde_project_id_stage_index` (`cde_project_id`,`stage`),
  KEY `document_submissions_cde_project_id_status_index` (`cde_project_id`,`status`),
  CONSTRAINT `document_submissions_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_submissions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_submissions_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `document_submissions_submitted_by_foreign` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `document_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `document_versions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `document_id` bigint unsigned NOT NULL,
  `version_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `major_version` int NOT NULL DEFAULT '1',
  `minor_version` int NOT NULL DEFAULT '0',
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` bigint unsigned NOT NULL,
  `file_hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `change_notes` text COLLATE utf8mb4_unicode_ci,
  `change_type` enum('initial','revision','correction','supersede') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'initial',
  `is_current` tinyint(1) NOT NULL DEFAULT '1',
  `uploaded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `document_versions_uploaded_by_foreign` (`uploaded_by`),
  KEY `document_versions_document_id_is_current_index` (`document_id`,`is_current`),
  KEY `document_versions_file_hash_index` (`file_hash`),
  KEY `document_versions_version_number_index` (`version_number`),
  CONSTRAINT `document_versions_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `project_documents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_versions_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `drawing_revisions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `drawing_revisions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `drawing_id` bigint unsigned NOT NULL,
  `revision_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `revision_description` text COLLATE utf8mb4_unicode_ci,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` bigint unsigned DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'current',
  `revision_date` date DEFAULT NULL,
  `revised_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `drawing_revisions_revised_by_foreign` (`revised_by`),
  KEY `drawing_revisions_drawing_id_status_index` (`drawing_id`,`status`),
  CONSTRAINT `drawing_revisions_drawing_id_foreign` FOREIGN KEY (`drawing_id`) REFERENCES `drawings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `drawing_revisions_revised_by_foreign` FOREIGN KEY (`revised_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `drawings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `drawings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned NOT NULL,
  `drawing_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `discipline` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'architectural',
  `drawing_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'plan',
  `current_revision` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'wip',
  `scale` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sheet_size` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `suitability_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `originator` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `level` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `drawn_by` bigint unsigned DEFAULT NULL,
  `checked_by` bigint unsigned DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `drawn_date` date DEFAULT NULL,
  `checked_date` date DEFAULT NULL,
  `approved_date` date DEFAULT NULL,
  `tags` json DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `drawings_company_id_cde_project_id_drawing_number_unique` (`company_id`,`cde_project_id`,`drawing_number`),
  KEY `drawings_drawn_by_foreign` (`drawn_by`),
  KEY `drawings_checked_by_foreign` (`checked_by`),
  KEY `drawings_approved_by_foreign` (`approved_by`),
  KEY `drawings_company_id_cde_project_id_discipline_status_index` (`company_id`,`cde_project_id`,`discipline`,`status`),
  KEY `drawings_company_status_deleted_idx` (`company_id`,`status`,`deleted_at`),
  KEY `drawings_project_status_idx` (`cde_project_id`,`status`),
  CONSTRAINT `drawings_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `drawings_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `drawings_checked_by_foreign` FOREIGN KEY (`checked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `drawings_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `drawings_drawn_by_foreign` FOREIGN KEY (`drawn_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `email_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `available_variables` json DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_templates_company_id_slug_unique` (`company_id`,`slug`),
  KEY `email_templates_created_by_foreign` (`created_by`),
  KEY `email_templates_updated_by_foreign` (`updated_by`),
  CONSTRAINT `email_templates_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `email_templates_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `email_templates_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `employee_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `designation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `salary` decimal(12,2) DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employees_company_id_foreign` (`company_id`),
  KEY `employees_user_id_foreign` (`user_id`),
  CONSTRAINT `employees_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `epics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `epics` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `epics_project_id_foreign` (`project_id`),
  CONSTRAINT `epics_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `equipment_allocations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `equipment_allocations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `asset_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `operator_id` bigint unsigned DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `daily_rate` decimal(10,2) DEFAULT NULL COMMENT 'Internal cross-charge rate per day',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `equipment_allocations_asset_id_foreign` (`asset_id`),
  KEY `equipment_allocations_operator_id_foreign` (`operator_id`),
  KEY `equipment_allocations_created_by_foreign` (`created_by`),
  KEY `equipment_allocations_company_id_asset_id_index` (`company_id`,`asset_id`),
  KEY `equipment_allocations_company_id_cde_project_id_index` (`company_id`,`cde_project_id`),
  KEY `equip_alloc_company_status_idx` (`company_id`,`status`),
  KEY `equip_alloc_project_status_idx` (`cde_project_id`,`status`),
  CONSTRAINT `equipment_allocations_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `equipment_allocations_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `equipment_allocations_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `equipment_allocations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `equipment_allocations_operator_id_foreign` FOREIGN KEY (`operator_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `equipment_fuel_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `equipment_fuel_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `asset_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `log_date` date NOT NULL,
  `liters` decimal(10,2) NOT NULL,
  `cost_per_liter` decimal(10,2) DEFAULT NULL,
  `total_cost` decimal(10,2) DEFAULT NULL,
  `meter_reading` decimal(10,1) DEFAULT NULL COMMENT 'Hour meter or odometer at time of fueling',
  `filled_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supplier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `equipment_fuel_logs_asset_id_foreign` (`asset_id`),
  KEY `equipment_fuel_logs_cde_project_id_foreign` (`cde_project_id`),
  KEY `equipment_fuel_logs_created_by_foreign` (`created_by`),
  KEY `equipment_fuel_logs_company_id_asset_id_index` (`company_id`,`asset_id`),
  KEY `fuel_company_date_idx` (`company_id`,`log_date`),
  KEY `fuel_company_asset_idx` (`company_id`,`asset_id`),
  CONSTRAINT `equipment_fuel_logs_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `equipment_fuel_logs_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `equipment_fuel_logs_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `equipment_fuel_logs_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `estimation_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estimation_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `estimation_id` bigint unsigned NOT NULL,
  `service_part_id` bigint unsigned DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'service',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `unit_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `estimation_items_estimation_id_foreign` (`estimation_id`),
  KEY `estimation_items_service_part_id_foreign` (`service_part_id`),
  CONSTRAINT `estimation_items_estimation_id_foreign` FOREIGN KEY (`estimation_id`) REFERENCES `estimations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `estimation_items_service_part_id_foreign` FOREIGN KEY (`service_part_id`) REFERENCES `service_parts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `estimations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estimations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `estimation_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_id` bigint unsigned DEFAULT NULL,
  `asset_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `valid_until` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `estimations_company_id_foreign` (`company_id`),
  KEY `estimations_client_id_foreign` (`client_id`),
  KEY `estimations_asset_id_foreign` (`asset_id`),
  KEY `estimations_created_by_foreign` (`created_by`),
  CONSTRAINT `estimations_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `estimations_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  CONSTRAINT `estimations_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `estimations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `expense_date` date NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `description` text COLLATE utf8mb4_unicode_ci,
  `recorded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expenses_company_id_foreign` (`company_id`),
  KEY `expenses_cde_project_id_foreign` (`cde_project_id`),
  KEY `expenses_recorded_by_foreign` (`recorded_by`),
  CONSTRAINT `expenses_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expenses_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `expenses_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `external_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `external_access` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `access_token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `migration_generated` tinyint(1) NOT NULL DEFAULT '0',
  `last_accessed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `external_access_access_token_unique` (`access_token`),
  KEY `external_access_project_id_foreign` (`project_id`),
  CONSTRAINT `external_access_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
DROP TABLE IF EXISTS `feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `feedback` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `work_order_id` bigint unsigned DEFAULT NULL,
  `client_id` bigint unsigned DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `feedback_company_id_foreign` (`company_id`),
  KEY `feedback_work_order_id_foreign` (`work_order_id`),
  KEY `feedback_client_id_foreign` (`client_id`),
  CONSTRAINT `feedback_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  CONSTRAINT `feedback_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `feedback_work_order_id_foreign` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `geofence_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `geofence_events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `service_location_id` bigint unsigned DEFAULT NULL,
  `event_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `geofence_events_company_id_foreign` (`company_id`),
  KEY `geofence_events_user_id_foreign` (`user_id`),
  KEY `geofence_events_service_location_id_foreign` (`service_location_id`),
  CONSTRAINT `geofence_events_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `geofence_events_service_location_id_foreign` FOREIGN KEY (`service_location_id`) REFERENCES `service_locations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `geofence_events_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `goods_received_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `goods_received_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `purchase_order_id` bigint unsigned DEFAULT NULL,
  `grn_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` bigint unsigned DEFAULT NULL,
  `warehouse_id` bigint unsigned DEFAULT NULL,
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `received_date` date DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `carrier_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_plate` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `driver_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inspector_id` bigint unsigned DEFAULT NULL,
  `inspection_passed` tinyint(1) NOT NULL DEFAULT '1',
  `invoice_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Supplier invoice number for URA audit matching',
  `delivery_note_ref` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `received_by` bigint unsigned DEFAULT NULL,
  `inspected_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `goods_received_notes_grn_number_unique` (`grn_number`),
  KEY `goods_received_notes_cde_project_id_foreign` (`cde_project_id`),
  KEY `goods_received_notes_purchase_order_id_foreign` (`purchase_order_id`),
  KEY `goods_received_notes_supplier_id_foreign` (`supplier_id`),
  KEY `goods_received_notes_warehouse_id_foreign` (`warehouse_id`),
  KEY `goods_received_notes_received_by_foreign` (`received_by`),
  KEY `goods_received_notes_inspected_by_foreign` (`inspected_by`),
  KEY `goods_received_notes_company_id_cde_project_id_index` (`company_id`,`cde_project_id`),
  KEY `goods_received_notes_inspector_id_foreign` (`inspector_id`),
  CONSTRAINT `goods_received_notes_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `goods_received_notes_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `goods_received_notes_inspected_by_foreign` FOREIGN KEY (`inspected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `goods_received_notes_inspector_id_foreign` FOREIGN KEY (`inspector_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `goods_received_notes_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `goods_received_notes_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `goods_received_notes_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `goods_received_notes_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `grn_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grn_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `goods_received_note_id` bigint unsigned NOT NULL,
  `purchase_order_item_id` bigint unsigned DEFAULT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity_expected` decimal(12,2) NOT NULL DEFAULT '0.00',
  `quantity_received` decimal(12,2) NOT NULL DEFAULT '0.00',
  `quantity_accepted` decimal(12,2) NOT NULL DEFAULT '0.00',
  `quantity_rejected` decimal(12,2) NOT NULL DEFAULT '0.00',
  `condition` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'good',
  `rejection_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `grn_items_goods_received_note_id_foreign` (`goods_received_note_id`),
  KEY `grn_items_purchase_order_item_id_foreign` (`purchase_order_item_id`),
  KEY `grn_items_product_id_foreign` (`product_id`),
  CONSTRAINT `grn_items_goods_received_note_id_foreign` FOREIGN KEY (`goods_received_note_id`) REFERENCES `goods_received_notes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `grn_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `grn_items_purchase_order_item_id_foreign` FOREIGN KEY (`purchase_order_item_id`) REFERENCES `purchase_order_items` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `inspection_checklist_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inspection_checklist_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `inspection_template_id` bigint unsigned NOT NULL,
  `item` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inspection_checklist_items_inspection_template_id_foreign` (`inspection_template_id`),
  CONSTRAINT `inspection_checklist_items_inspection_template_id_foreign` FOREIGN KEY (`inspection_template_id`) REFERENCES `inspection_templates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `inspection_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inspection_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inspection_templates_company_id_foreign` (`company_id`),
  CONSTRAINT `inspection_templates_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `inventory_audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `event_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint unsigned DEFAULT NULL,
  `reference_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `warehouse_id` bigint unsigned DEFAULT NULL,
  `quantity_before` decimal(12,2) DEFAULT NULL,
  `quantity_after` decimal(12,2) DEFAULT NULL,
  `quantity_change` decimal(12,2) DEFAULT NULL,
  `unit_cost` decimal(12,2) DEFAULT NULL,
  `total_value` decimal(12,2) DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `metadata` json DEFAULT NULL,
  `performed_by` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_audit_logs_cde_project_id_foreign` (`cde_project_id`),
  KEY `inventory_audit_logs_product_id_foreign` (`product_id`),
  KEY `inventory_audit_logs_warehouse_id_foreign` (`warehouse_id`),
  KEY `inventory_audit_logs_performed_by_foreign` (`performed_by`),
  KEY `inventory_audit_logs_company_id_event_type_created_at_index` (`company_id`,`event_type`,`created_at`),
  KEY `inventory_audit_logs_reference_type_reference_id_index` (`reference_type`,`reference_id`),
  CONSTRAINT `inventory_audit_logs_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventory_audit_logs_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_audit_logs_performed_by_foreign` FOREIGN KEY (`performed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventory_audit_logs_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventory_audit_logs_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `inventory_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `balance_before` int NOT NULL DEFAULT '0',
  `balance_after` int NOT NULL DEFAULT '0',
  `unit_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `reference_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_transactions_company_id_foreign` (`company_id`),
  KEY `inventory_transactions_product_id_foreign` (`product_id`),
  KEY `inventory_transactions_warehouse_id_foreign` (`warehouse_id`),
  KEY `inventory_transactions_created_by_foreign` (`created_by`),
  CONSTRAINT `inventory_transactions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_transactions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `inventory_transactions_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_transactions_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `invoice_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint unsigned NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_items_invoice_id_foreign` (`invoice_id`),
  CONSTRAINT `invoice_items_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `invoice_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint unsigned NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_date` date NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `recorded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_payments_invoice_id_foreign` (`invoice_id`),
  KEY `invoice_payments_recorded_by_foreign` (`recorded_by`),
  KEY `invoice_payments_cde_project_id_foreign` (`cde_project_id`),
  CONSTRAINT `invoice_payments_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoice_payments_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoice_payments_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `work_order_id` bigint unsigned DEFAULT NULL,
  `quotation_id` bigint unsigned DEFAULT NULL,
  `client_id` bigint unsigned DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `amount_paid` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `issue_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `reminder_sent_at` date DEFAULT NULL,
  `reminder_count` int NOT NULL DEFAULT '0',
  `terms_and_conditions` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `invoices_work_order_id_foreign` (`work_order_id`),
  KEY `invoices_client_id_foreign` (`client_id`),
  KEY `invoices_created_by_foreign` (`created_by`),
  KEY `invoices_cde_project_id_foreign` (`cde_project_id`),
  KEY `invoices_status_due_date_index` (`status`,`due_date`),
  KEY `invoices_company_id_status_index` (`company_id`,`status`),
  KEY `invoices_company_status_deleted_idx` (`company_id`,`status`,`deleted_at`),
  KEY `invoices_company_issue_date_idx` (`company_id`,`issue_date`),
  KEY `invoices_company_due_date_idx` (`company_id`,`due_date`),
  CONSTRAINT `invoices_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoices_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoices_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoices_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoices_work_order_id_foreign` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
DROP TABLE IF EXISTS `leaves`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leaves` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `leave_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leaves_company_id_foreign` (`company_id`),
  KEY `leaves_user_id_foreign` (`user_id`),
  KEY `leaves_approved_by_foreign` (`approved_by`),
  CONSTRAINT `leaves_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leaves_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `leaves_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `login_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `login_activities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_agent` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `failure_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `login_activities_user_id_foreign` (`user_id`),
  KEY `login_activities_ip_address_created_at_index` (`ip_address`,`created_at`),
  KEY `login_activities_email_status_created_at_index` (`email`,`status`,`created_at`),
  KEY `login_activities_email_index` (`email`),
  CONSTRAINT `login_activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `lookahead_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lookahead_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lookahead_plan_id` bigint unsigned NOT NULL,
  `schedule_activity_id` bigint unsigned DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `planned_date` date DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'planned',
  `constraints` text COLLATE utf8mb4_unicode_ci,
  `assigned_to` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lookahead_items_lookahead_plan_id_foreign` (`lookahead_plan_id`),
  KEY `lookahead_items_schedule_activity_id_foreign` (`schedule_activity_id`),
  KEY `lookahead_items_assigned_to_foreign` (`assigned_to`),
  CONSTRAINT `lookahead_items_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `lookahead_items_lookahead_plan_id_foreign` FOREIGN KEY (`lookahead_plan_id`) REFERENCES `lookahead_plans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lookahead_items_schedule_activity_id_foreign` FOREIGN KEY (`schedule_activity_id`) REFERENCES `schedule_activities` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `lookahead_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lookahead_plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lookahead_plans_company_id_foreign` (`company_id`),
  KEY `lookahead_plans_cde_project_id_foreign` (`cde_project_id`),
  KEY `lookahead_plans_created_by_foreign` (`created_by`),
  CONSTRAINT `lookahead_plans_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `lookahead_plans_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lookahead_plans_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `material_issuance_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `material_issuance_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `material_issuance_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `quantity_issued` int NOT NULL DEFAULT '0',
  `quantity_returned` int NOT NULL DEFAULT '0',
  `condition_on_issue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'good',
  `condition_on_return` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `material_issuance_items_material_issuance_id_foreign` (`material_issuance_id`),
  KEY `material_issuance_items_product_id_foreign` (`product_id`),
  CONSTRAINT `material_issuance_items_material_issuance_id_foreign` FOREIGN KEY (`material_issuance_id`) REFERENCES `material_issuances` (`id`) ON DELETE CASCADE,
  CONSTRAINT `material_issuance_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `material_issuances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `material_issuances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `issuance_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `material_requisition_id` bigint unsigned DEFAULT NULL,
  `issued_to` bigint unsigned DEFAULT NULL,
  `issued_to_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purpose` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `issue_date` date DEFAULT NULL,
  `expected_return_date` date DEFAULT NULL,
  `actual_return_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `material_issuances_company_id_foreign` (`company_id`),
  KEY `material_issuances_warehouse_id_foreign` (`warehouse_id`),
  KEY `material_issuances_cde_project_id_foreign` (`cde_project_id`),
  KEY `material_issuances_issued_to_foreign` (`issued_to`),
  KEY `material_issuances_created_by_foreign` (`created_by`),
  KEY `material_issuances_approved_by_foreign` (`approved_by`),
  KEY `material_issuances_material_requisition_id_foreign` (`material_requisition_id`),
  CONSTRAINT `material_issuances_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `material_issuances_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `material_issuances_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `material_issuances_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `material_issuances_issued_to_foreign` FOREIGN KEY (`issued_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `material_issuances_material_requisition_id_foreign` FOREIGN KEY (`material_requisition_id`) REFERENCES `material_requisitions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `material_issuances_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `material_requisition_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `material_requisition_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `material_requisition_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `quantity_requested` int NOT NULL DEFAULT '1',
  `quantity_approved` int DEFAULT NULL,
  `quantity_issued` int NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `material_requisition_items_material_requisition_id_foreign` (`material_requisition_id`),
  KEY `material_requisition_items_product_id_foreign` (`product_id`),
  CONSTRAINT `material_requisition_items_material_requisition_id_foreign` FOREIGN KEY (`material_requisition_id`) REFERENCES `material_requisitions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `material_requisition_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `material_requisitions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `material_requisitions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `requisition_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `requester_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `priority` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `purpose` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `required_date` date DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approval_level` tinyint unsigned NOT NULL DEFAULT '1',
  `level1_approved_by` bigint unsigned DEFAULT NULL,
  `level1_approved_at` timestamp NULL DEFAULT NULL,
  `level2_approved_by` bigint unsigned DEFAULT NULL,
  `level2_approved_at` timestamp NULL DEFAULT NULL,
  `level2_rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `material_requisitions_company_id_foreign` (`company_id`),
  KEY `material_requisitions_requester_id_foreign` (`requester_id`),
  KEY `material_requisitions_cde_project_id_foreign` (`cde_project_id`),
  KEY `material_requisitions_warehouse_id_foreign` (`warehouse_id`),
  KEY `material_requisitions_approved_by_foreign` (`approved_by`),
  KEY `material_requisitions_level1_approved_by_foreign` (`level1_approved_by`),
  KEY `material_requisitions_level2_approved_by_foreign` (`level2_approved_by`),
  CONSTRAINT `material_requisitions_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `material_requisitions_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `material_requisitions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `material_requisitions_level1_approved_by_foreign` FOREIGN KEY (`level1_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `material_requisitions_level2_approved_by_foreign` FOREIGN KEY (`level2_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `material_requisitions_requester_id_foreign` FOREIGN KEY (`requester_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `material_requisitions_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `mileage_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mileage_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `work_order_id` bigint unsigned DEFAULT NULL,
  `date` date NOT NULL,
  `start_odometer` decimal(10,2) DEFAULT NULL,
  `end_odometer` decimal(10,2) DEFAULT NULL,
  `distance` decimal(10,2) DEFAULT NULL,
  `vehicle` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purpose` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mileage_logs_company_id_foreign` (`company_id`),
  KEY `mileage_logs_user_id_foreign` (`user_id`),
  KEY `mileage_logs_work_order_id_foreign` (`work_order_id`),
  CONSTRAINT `mileage_logs_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mileage_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mileage_logs_work_order_id_foreign` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `milestones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `milestones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `schedule_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `target_date` date NOT NULL,
  `actual_date` date DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'upcoming',
  `priority` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `milestones_company_id_foreign` (`company_id`),
  KEY `milestones_cde_project_id_foreign` (`cde_project_id`),
  KEY `milestones_schedule_id_foreign` (`schedule_id`),
  CONSTRAINT `milestones_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `milestones_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `milestones_schedule_id_foreign` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_core` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `modules_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notice_boards`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notice_boards` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `is_pinned` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notice_boards_company_id_foreign` (`company_id`),
  KEY `notice_boards_created_by_foreign` (`created_by`),
  CONSTRAINT `notice_boards_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notice_boards_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_history` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `password_history_user_id_created_at_index` (`user_id`,`created_at`),
  CONSTRAINT `password_history_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
DROP TABLE IF EXISTS `payment_certificates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_certificates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned NOT NULL,
  `contract_id` bigint unsigned DEFAULT NULL,
  `certificate_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'interim',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `period_from` date NOT NULL,
  `period_to` date NOT NULL,
  `gross_value_to_date` decimal(15,2) NOT NULL DEFAULT '0.00',
  `previous_certified` decimal(15,2) NOT NULL DEFAULT '0.00',
  `this_certificate_gross` decimal(15,2) NOT NULL DEFAULT '0.00',
  `variations_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `materials_on_site` decimal(15,2) NOT NULL DEFAULT '0.00',
  `retention_deduction` decimal(15,2) NOT NULL DEFAULT '0.00',
  `retention_release` decimal(15,2) NOT NULL DEFAULT '0.00',
  `advance_recovery` decimal(15,2) NOT NULL DEFAULT '0.00',
  `other_deductions` decimal(15,2) NOT NULL DEFAULT '0.00',
  `deduction_description` text COLLATE utf8mb4_unicode_ci,
  `net_payable` decimal(15,2) NOT NULL DEFAULT '0.00',
  `vat_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_payable` decimal(15,2) NOT NULL DEFAULT '0.00',
  `prepared_by` bigint unsigned DEFAULT NULL,
  `checked_by` bigint unsigned DEFAULT NULL,
  `certified_by` bigint unsigned DEFAULT NULL,
  `submitted_date` date DEFAULT NULL,
  `certified_date` date DEFAULT NULL,
  `paid_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `attachments` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_certificates_certificate_number_unique` (`certificate_number`),
  KEY `payment_certificates_cde_project_id_foreign` (`cde_project_id`),
  KEY `payment_certificates_contract_id_foreign` (`contract_id`),
  KEY `payment_certificates_prepared_by_foreign` (`prepared_by`),
  KEY `payment_certificates_checked_by_foreign` (`checked_by`),
  KEY `payment_certificates_certified_by_foreign` (`certified_by`),
  KEY `payment_certificates_company_id_cde_project_id_status_index` (`company_id`,`cde_project_id`,`status`),
  CONSTRAINT `payment_certificates_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_certificates_certified_by_foreign` FOREIGN KEY (`certified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payment_certificates_checked_by_foreign` FOREIGN KEY (`checked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payment_certificates_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payment_certificates_contract_id_foreign` FOREIGN KEY (`contract_id`) REFERENCES `contracts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payment_certificates_prepared_by_foreign` FOREIGN KEY (`prepared_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `permits_to_work`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permits_to_work` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `permit_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `work_description` text COLLATE utf8mb4_unicode_ci,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valid_from` datetime DEFAULT NULL,
  `valid_to` datetime DEFAULT NULL,
  `hazards` json DEFAULT NULL,
  `precautions` json DEFAULT NULL,
  `requested_by` bigint unsigned DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `permits_to_work_company_id_foreign` (`company_id`),
  KEY `permits_to_work_cde_project_id_foreign` (`cde_project_id`),
  KEY `permits_to_work_requested_by_foreign` (`requested_by`),
  KEY `permits_to_work_approved_by_foreign` (`approved_by`),
  CONSTRAINT `permits_to_work_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `permits_to_work_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `permits_to_work_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `permits_to_work_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `product_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `parent_id` bigint unsigned DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_categories_company_id_foreign` (`company_id`),
  KEY `product_categories_parent_id_foreign` (`parent_id`),
  CONSTRAINT `product_categories_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `product_tracking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_tracking` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `stage` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `milestone_id` bigint unsigned DEFAULT NULL,
  `task_id` bigint unsigned DEFAULT NULL,
  `purchase_order_id` bigint unsigned DEFAULT NULL,
  `delivery_note_id` bigint unsigned DEFAULT NULL,
  `material_issuance_id` bigint unsigned DEFAULT NULL,
  `quantity` decimal(12,2) NOT NULL DEFAULT '0.00',
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `recorded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_tracking_company_id_foreign` (`company_id`),
  KEY `product_tracking_cde_project_id_foreign` (`cde_project_id`),
  KEY `product_tracking_product_id_foreign` (`product_id`),
  KEY `product_tracking_milestone_id_foreign` (`milestone_id`),
  KEY `product_tracking_task_id_foreign` (`task_id`),
  KEY `product_tracking_purchase_order_id_foreign` (`purchase_order_id`),
  KEY `product_tracking_delivery_note_id_foreign` (`delivery_note_id`),
  KEY `product_tracking_material_issuance_id_foreign` (`material_issuance_id`),
  KEY `product_tracking_recorded_by_foreign` (`recorded_by`),
  CONSTRAINT `product_tracking_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_tracking_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_tracking_delivery_note_id_foreign` FOREIGN KEY (`delivery_note_id`) REFERENCES `delivery_notes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `product_tracking_material_issuance_id_foreign` FOREIGN KEY (`material_issuance_id`) REFERENCES `material_issuances` (`id`) ON DELETE SET NULL,
  CONSTRAINT `product_tracking_milestone_id_foreign` FOREIGN KEY (`milestone_id`) REFERENCES `milestones` (`id`) ON DELETE SET NULL,
  CONSTRAINT `product_tracking_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_tracking_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `product_tracking_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `product_tracking_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `product_category_id` bigint unsigned DEFAULT NULL,
  `supplier_id` bigint unsigned DEFAULT NULL,
  `lead_time_days` int DEFAULT NULL COMMENT 'Lead time in days for procurement',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serial_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barcode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `unit_of_measure` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'each',
  `cost_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `selling_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `reorder_level` int NOT NULL DEFAULT '0',
  `reorder_quantity` int NOT NULL DEFAULT '0',
  `max_order_level` int NOT NULL DEFAULT '0' COMMENT 'Maximum stock level; triggers over-stock alert when exceeded',
  `expiry_tracking_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `expiry_date` date DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `track_inventory` tinyint(1) NOT NULL DEFAULT '1',
  `is_asset` tinyint(1) NOT NULL DEFAULT '0',
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `condition` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new',
  `warranty_period` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_company_id_foreign` (`company_id`),
  KEY `products_product_category_id_foreign` (`product_category_id`),
  KEY `products_supplier_id_foreign` (`supplier_id`),
  CONSTRAINT `products_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `products_product_category_id_foreign` FOREIGN KEY (`product_category_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `progress_updates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `progress_updates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `schedule_activity_id` bigint unsigned DEFAULT NULL,
  `update_date` date NOT NULL,
  `progress_percent` int NOT NULL DEFAULT '0',
  `description` text COLLATE utf8mb4_unicode_ci,
  `photos` json DEFAULT NULL,
  `reported_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `progress_updates_company_id_foreign` (`company_id`),
  KEY `progress_updates_cde_project_id_foreign` (`cde_project_id`),
  KEY `progress_updates_schedule_activity_id_foreign` (`schedule_activity_id`),
  KEY `progress_updates_reported_by_foreign` (`reported_by`),
  CONSTRAINT `progress_updates_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `progress_updates_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `progress_updates_reported_by_foreign` FOREIGN KEY (`reported_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `progress_updates_schedule_activity_id_foreign` FOREIGN KEY (`schedule_activity_id`) REFERENCES `schedule_activities` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `project_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `folder_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `file_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_version_id` bigint unsigned DEFAULT NULL,
  `version_count` int NOT NULL DEFAULT '1',
  `status` enum('draft','active','archived','superseded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `is_locked` tinyint(1) NOT NULL DEFAULT '0',
  `locked_by` bigint unsigned DEFAULT NULL,
  `locked_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_documents_folder_id_foreign` (`folder_id`),
  KEY `project_documents_locked_by_foreign` (`locked_by`),
  KEY `project_documents_created_by_foreign` (`created_by`),
  KEY `project_documents_updated_by_foreign` (`updated_by`),
  KEY `project_documents_project_id_folder_id_index` (`project_id`,`folder_id`),
  KEY `project_documents_status_index` (`status`),
  KEY `project_documents_file_type_index` (`file_type`),
  KEY `project_documents_current_version_id_foreign` (`current_version_id`),
  FULLTEXT KEY `project_documents_title_description_fulltext` (`title`,`description`),
  CONSTRAINT `project_documents_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `project_documents_current_version_id_foreign` FOREIGN KEY (`current_version_id`) REFERENCES `document_versions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `project_documents_folder_id_foreign` FOREIGN KEY (`folder_id`) REFERENCES `project_folders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_documents_locked_by_foreign` FOREIGN KEY (`locked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `project_documents_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_documents_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `project_folders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_folders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '#6B7280',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_folders_parent_id_foreign` (`parent_id`),
  KEY `project_folders_created_by_foreign` (`created_by`),
  KEY `project_folders_project_id_parent_id_index` (`project_id`,`parent_id`),
  KEY `project_folders_sort_order_index` (`sort_order`),
  CONSTRAINT `project_folders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `project_folders_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `project_folders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_folders_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `project_invitations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_invitations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned NOT NULL,
  `invited_by` bigint unsigned DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'member',
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `accepted_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_invitations_token_unique` (`token`),
  KEY `project_invitations_company_id_foreign` (`company_id`),
  KEY `project_invitations_invited_by_foreign` (`invited_by`),
  KEY `project_invitations_cde_project_id_email_index` (`cde_project_id`,`email`),
  KEY `project_invitations_email_status_index` (`email`,`status`),
  CONSTRAINT `project_invitations_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_invitations_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_invitations_invited_by_foreign` FOREIGN KEY (`invited_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `project_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_members` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_members_project_id_user_id_unique` (`project_id`,`user_id`),
  KEY `idx_project_members_project_user` (`project_id`,`user_id`),
  KEY `idx_project_members_user` (`user_id`),
  CONSTRAINT `project_members_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_members_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `project_module_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_module_access` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned NOT NULL,
  `module_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `monthly_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Monthly price for this module on this project',
  `enabled_at` timestamp NULL DEFAULT NULL,
  `enabled_by` bigint unsigned DEFAULT NULL,
  `disabled_at` timestamp NULL DEFAULT NULL,
  `disabled_by` bigint unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pma_company_project_module_unique` (`company_id`,`cde_project_id`,`module_code`),
  KEY `project_module_access_cde_project_id_foreign` (`cde_project_id`),
  KEY `project_module_access_company_id_index` (`company_id`),
  KEY `project_module_access_enabled_by_foreign` (`enabled_by`),
  KEY `project_module_access_disabled_by_foreign` (`disabled_by`),
  CONSTRAINT `project_module_access_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_module_access_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_module_access_disabled_by_foreign` FOREIGN KEY (`disabled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `project_module_access_enabled_by_foreign` FOREIGN KEY (`enabled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `project_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `created_by` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `note_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_notes_created_by_foreign` (`created_by`),
  KEY `project_notes_project_id_note_date_index` (`project_id`,`note_date`),
  CONSTRAINT `project_notes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_notes_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `project_suggestions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_suggestions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned DEFAULT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `author_id` bigint unsigned DEFAULT NULL,
  `is_anonymous` tinyint(1) NOT NULL DEFAULT '1',
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new',
  `admin_response` text COLLATE utf8mb4_unicode_ci,
  `responded_by` bigint unsigned DEFAULT NULL,
  `responded_at` timestamp NULL DEFAULT NULL,
  `upvotes` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_suggestions_author_id_foreign` (`author_id`),
  KEY `project_suggestions_responded_by_foreign` (`responded_by`),
  KEY `project_suggestions_cde_project_id_status_index` (`cde_project_id`,`status`),
  KEY `project_suggestions_company_id_created_at_index` (`company_id`,`created_at`),
  CONSTRAINT `project_suggestions_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `project_suggestions_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_suggestions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_suggestions_responded_by_foreign` FOREIGN KEY (`responded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `projects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `ticket_prefix` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `pinned_date` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_projects_pinned` (`pinned_date`),
  KEY `idx_projects_dates` (`start_date`,`end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `purchase_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_order_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `quantity_ordered` int NOT NULL DEFAULT '0',
  `quantity_received` int NOT NULL DEFAULT '0',
  `unit_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_order_items_purchase_order_id_foreign` (`purchase_order_id`),
  KEY `purchase_order_items_product_id_foreign` (`product_id`),
  CONSTRAINT `purchase_order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_order_items_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `purchase_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `po_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `order_date` date DEFAULT NULL,
  `expected_date` date DEFAULT NULL,
  `received_date` date DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `shipping_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `is_quarterly` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Flag for quarterly procurement cycle tracking',
  `quarter` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'e.g., Q1-2026, Q2-2026',
  `delivery_address` text COLLATE utf8mb4_unicode_ci,
  `payment_terms` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'UGX',
  `created_by` bigint unsigned DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approval_level` tinyint unsigned NOT NULL DEFAULT '1',
  `level1_approved_by` bigint unsigned DEFAULT NULL,
  `level1_approved_at` timestamp NULL DEFAULT NULL,
  `level2_approved_by` bigint unsigned DEFAULT NULL,
  `level2_approved_at` timestamp NULL DEFAULT NULL,
  `level2_rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `approval_threshold` decimal(14,2) DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_orders_company_id_foreign` (`company_id`),
  KEY `purchase_orders_supplier_id_foreign` (`supplier_id`),
  KEY `purchase_orders_warehouse_id_foreign` (`warehouse_id`),
  KEY `purchase_orders_created_by_foreign` (`created_by`),
  KEY `purchase_orders_approved_by_foreign` (`approved_by`),
  KEY `purchase_orders_cde_project_id_foreign` (`cde_project_id`),
  KEY `purchase_orders_level1_approved_by_foreign` (`level1_approved_by`),
  KEY `purchase_orders_level2_approved_by_foreign` (`level2_approved_by`),
  CONSTRAINT `purchase_orders_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_orders_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_orders_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_orders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_orders_level1_approved_by_foreign` FOREIGN KEY (`level1_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_orders_level2_approved_by_foreign` FOREIGN KEY (`level2_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_orders_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_orders_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `quotation_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quotation_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `quotation_id` bigint unsigned NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(12,2) NOT NULL DEFAULT '1.00',
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ea',
  `unit_price` decimal(14,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quotation_items_quotation_id_foreign` (`quotation_id`),
  CONSTRAINT `quotation_items_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `quotations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quotations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `client_id` bigint unsigned DEFAULT NULL,
  `quotation_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtotal` decimal(14,2) NOT NULL DEFAULT '0.00',
  `tax_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `issue_date` date DEFAULT NULL,
  `valid_until` date DEFAULT NULL,
  `accepted_at` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `terms_and_conditions` text COLLATE utf8mb4_unicode_ci,
  `scope_of_work` text COLLATE utf8mb4_unicode_ci,
  `converted_invoice_id` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quotations_company_id_foreign` (`company_id`),
  KEY `quotations_cde_project_id_foreign` (`cde_project_id`),
  KEY `quotations_client_id_foreign` (`client_id`),
  KEY `quotations_converted_invoice_id_foreign` (`converted_invoice_id`),
  KEY `quotations_created_by_foreign` (`created_by`),
  KEY `quotations_quotation_number_index` (`quotation_number`),
  CONSTRAINT `quotations_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `quotations_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  CONSTRAINT `quotations_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `quotations_converted_invoice_id_foreign` FOREIGN KEY (`converted_invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL,
  CONSTRAINT `quotations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `rfis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rfis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `rfi_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci,
  `priority` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `raised_by` bigint unsigned DEFAULT NULL,
  `assigned_to` bigint unsigned DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `cost_impact` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `schedule_impact` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `answered_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rfis_company_id_foreign` (`company_id`),
  KEY `rfis_cde_project_id_foreign` (`cde_project_id`),
  KEY `rfis_raised_by_foreign` (`raised_by`),
  KEY `rfis_assigned_to_foreign` (`assigned_to`),
  CONSTRAINT `rfis_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `rfis_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `rfis_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rfis_raised_by_foreign` FOREIGN KEY (`raised_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`),
  KEY `roles_company_id_index` (`company_id`),
  CONSTRAINT `roles_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `route_stops`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `route_stops` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `route_id` bigint unsigned NOT NULL,
  `service_location_id` bigint unsigned DEFAULT NULL,
  `work_order_id` bigint unsigned DEFAULT NULL,
  `sequence` int NOT NULL DEFAULT '0',
  `estimated_arrival` time DEFAULT NULL,
  `actual_arrival` time DEFAULT NULL,
  `departure_time` time DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `route_stops_route_id_foreign` (`route_id`),
  KEY `route_stops_service_location_id_foreign` (`service_location_id`),
  KEY `route_stops_work_order_id_foreign` (`work_order_id`),
  CONSTRAINT `route_stops_route_id_foreign` FOREIGN KEY (`route_id`) REFERENCES `routes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `route_stops_service_location_id_foreign` FOREIGN KEY (`service_location_id`) REFERENCES `service_locations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `route_stops_work_order_id_foreign` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `routes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `routes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `assigned_to` bigint unsigned DEFAULT NULL,
  `scheduled_date` date DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'planned',
  `estimated_distance` decimal(10,2) DEFAULT NULL,
  `actual_distance` decimal(10,2) DEFAULT NULL,
  `optimization_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `routes_company_id_foreign` (`company_id`),
  KEY `routes_assigned_to_foreign` (`assigned_to`),
  CONSTRAINT `routes_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `routes_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `safety_incidents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `safety_incidents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `incident_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `severity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'low',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'reported',
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `incident_date` datetime NOT NULL,
  `root_cause` text COLLATE utf8mb4_unicode_ci,
  `corrective_action` text COLLATE utf8mb4_unicode_ci,
  `preventive_action` text COLLATE utf8mb4_unicode_ci,
  `is_ptw` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Is this a Permit to Work record?',
  `ptw_number` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ptw_type` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'hot_work, electrical_isolation, confined_space, height, excavation, general',
  `isolation_method` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'LOTO, circuit breaker, valve closure, etc.',
  `isolation_points` text COLLATE utf8mb4_unicode_ci COMMENT 'List of isolation points',
  `ptw_issuer_id` bigint unsigned DEFAULT NULL,
  `ptw_receiver_id` bigint unsigned DEFAULT NULL,
  `ptw_valid_from` datetime DEFAULT NULL,
  `ptw_valid_until` datetime DEFAULT NULL,
  `ptw_status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'active, extended, closed, cancelled',
  `ptw_conditions` text COLLATE utf8mb4_unicode_ci COMMENT 'Special conditions / precautions',
  `ppe_requirements` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON list of required PPE',
  `is_traffic_incident` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Traffic/road work zone incident',
  `traffic_control_type` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'stop_go, flagmen, traffic_lights, full_closure, lane_closure, diversion',
  `incident_chainage` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Chainage where incident occurred',
  `third_party_involved` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Public/3rd party vehicle or person involved',
  `road_closure_required` tinyint(1) NOT NULL DEFAULT '0',
  `closure_duration_hours` int unsigned DEFAULT NULL,
  `reported_by` bigint unsigned DEFAULT NULL,
  `investigated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `safety_incidents_cde_project_id_foreign` (`cde_project_id`),
  KEY `safety_incidents_reported_by_foreign` (`reported_by`),
  KEY `safety_incidents_investigated_by_foreign` (`investigated_by`),
  KEY `safety_incidents_ptw_issuer_id_foreign` (`ptw_issuer_id`),
  KEY `safety_incidents_ptw_receiver_id_foreign` (`ptw_receiver_id`),
  KEY `safety_company_date_idx` (`company_id`,`incident_date`),
  KEY `safety_company_status_idx` (`company_id`,`status`),
  KEY `safety_company_severity_idx` (`company_id`,`severity`),
  CONSTRAINT `safety_incidents_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `safety_incidents_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `safety_incidents_investigated_by_foreign` FOREIGN KEY (`investigated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `safety_incidents_ptw_issuer_id_foreign` FOREIGN KEY (`ptw_issuer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `safety_incidents_ptw_receiver_id_foreign` FOREIGN KEY (`ptw_receiver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `safety_incidents_reported_by_foreign` FOREIGN KEY (`reported_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `safety_inspections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `safety_inspections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `inspection_template_id` bigint unsigned DEFAULT NULL,
  `inspection_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
  `scheduled_date` datetime NOT NULL,
  `completed_date` datetime DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `score` int DEFAULT NULL,
  `findings` json DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `inspector_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `safety_inspections_company_id_foreign` (`company_id`),
  KEY `safety_inspections_cde_project_id_foreign` (`cde_project_id`),
  KEY `safety_inspections_inspection_template_id_foreign` (`inspection_template_id`),
  KEY `safety_inspections_inspector_id_foreign` (`inspector_id`),
  CONSTRAINT `safety_inspections_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `safety_inspections_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `safety_inspections_inspection_template_id_foreign` FOREIGN KEY (`inspection_template_id`) REFERENCES `inspection_templates` (`id`) ON DELETE SET NULL,
  CONSTRAINT `safety_inspections_inspector_id_foreign` FOREIGN KEY (`inspector_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `schedule_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `schedule_activities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `schedule_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `wbs_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `planned_start` date DEFAULT NULL,
  `planned_finish` date DEFAULT NULL,
  `actual_start` date DEFAULT NULL,
  `actual_finish` date DEFAULT NULL,
  `duration_days` int NOT NULL DEFAULT '0',
  `progress_percent` int NOT NULL DEFAULT '0',
  `parent_id` bigint unsigned DEFAULT NULL,
  `assigned_to` bigint unsigned DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `schedule_activities_schedule_id_foreign` (`schedule_id`),
  KEY `schedule_activities_parent_id_foreign` (`parent_id`),
  KEY `schedule_activities_assigned_to_foreign` (`assigned_to`),
  CONSTRAINT `schedule_activities_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `schedule_activities_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `schedule_activities` (`id`) ON DELETE SET NULL,
  CONSTRAINT `schedule_activities_schedule_id_foreign` FOREIGN KEY (`schedule_id`) REFERENCES `schedules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `schedules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `schedules_company_id_foreign` (`company_id`),
  KEY `schedules_cde_project_id_foreign` (`cde_project_id`),
  KEY `schedules_created_by_foreign` (`created_by`),
  CONSTRAINT `schedules_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `schedules_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `schedules_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `service_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_locations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `geofence_radius` decimal(8,2) NOT NULL DEFAULT '100.00',
  `client_id` bigint unsigned DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `service_locations_company_id_foreign` (`company_id`),
  KEY `service_locations_client_id_foreign` (`client_id`),
  CONSTRAINT `service_locations_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  CONSTRAINT `service_locations_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `service_parts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `service_parts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'part',
  `cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `service_parts_company_id_foreign` (`company_id`),
  CONSTRAINT `service_parts_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
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
DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `group` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_user_id_key_unique` (`user_id`,`key`),
  KEY `settings_group_index` (`group`),
  CONSTRAINT `settings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `site_checkins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `site_checkins` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `service_location_id` bigint unsigned DEFAULT NULL,
  `work_order_id` bigint unsigned DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'checkin',
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `is_within_geofence` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `site_checkins_company_id_foreign` (`company_id`),
  KEY `site_checkins_user_id_foreign` (`user_id`),
  KEY `site_checkins_service_location_id_foreign` (`service_location_id`),
  KEY `site_checkins_work_order_id_foreign` (`work_order_id`),
  CONSTRAINT `site_checkins_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `site_checkins_service_location_id_foreign` FOREIGN KEY (`service_location_id`) REFERENCES `service_locations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `site_checkins_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `site_checkins_work_order_id_foreign` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `snag_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `snag_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `snag_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `punch_category` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'A = must complete before handover, B = can complete after, C = cosmetic',
  `commissioning_system` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'System tag: e.g. HVAC-01, ELEC-MV-02',
  `discipline` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'mechanical, electrical, civil, instrumentation, piping',
  `photos` text COLLATE utf8mb4_unicode_ci,
  `verified_by` bigint unsigned DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `chainage` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Chainage location of defect',
  `road_side` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'lhs, rhs, cl, full_width',
  `defect_length_m` decimal(8,2) DEFAULT NULL COMMENT 'Defect length in metres',
  `defect_width_m` decimal(6,2) DEFAULT NULL COMMENT 'Defect width in metres',
  `defect_depth_mm` decimal(6,1) DEFAULT NULL COMMENT 'Defect depth in mm (for potholes, rutting)',
  `severity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'minor',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trade` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `reported_by` bigint unsigned DEFAULT NULL,
  `assigned_to` bigint unsigned DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `snag_items_company_id_foreign` (`company_id`),
  KEY `snag_items_cde_project_id_foreign` (`cde_project_id`),
  KEY `snag_items_reported_by_foreign` (`reported_by`),
  KEY `snag_items_assigned_to_foreign` (`assigned_to`),
  KEY `snag_items_verified_by_foreign` (`verified_by`),
  CONSTRAINT `snag_items_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `snag_items_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `snag_items_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `snag_items_reported_by_foreign` FOREIGN KEY (`reported_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `snag_items_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `social_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `social_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `record_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `description` text COLLATE utf8mb4_unicode_ci,
  `affected_party` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `record_date` date DEFAULT NULL,
  `resolution_date` date DEFAULT NULL,
  `resolution_notes` text COLLATE utf8mb4_unicode_ci,
  `follow_up_actions` text COLLATE utf8mb4_unicode_ci,
  `reported_by` bigint unsigned DEFAULT NULL,
  `assigned_to` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `social_records_company_id_foreign` (`company_id`),
  KEY `social_records_reported_by_foreign` (`reported_by`),
  KEY `social_records_assigned_to_foreign` (`assigned_to`),
  KEY `social_records_cde_project_id_category_index` (`cde_project_id`,`category`),
  KEY `social_records_cde_project_id_status_index` (`cde_project_id`,`status`),
  CONSTRAINT `social_records_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `social_records_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `social_records_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `social_records_reported_by_foreign` FOREIGN KEY (`reported_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `stock_adjustment_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_adjustment_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `stock_adjustment_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `system_quantity` int NOT NULL DEFAULT '0',
  `actual_quantity` int NOT NULL DEFAULT '0',
  `difference` int NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_adjustment_items_stock_adjustment_id_foreign` (`stock_adjustment_id`),
  KEY `stock_adjustment_items_product_id_foreign` (`product_id`),
  CONSTRAINT `stock_adjustment_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_adjustment_items_stock_adjustment_id_foreign` FOREIGN KEY (`stock_adjustment_id`) REFERENCES `stock_adjustments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `stock_adjustments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_adjustments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `adjustment_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'adjustment',
  `quantity_before` decimal(12,2) NOT NULL DEFAULT '0.00',
  `quantity_after` decimal(12,2) NOT NULL DEFAULT '0.00',
  `quantity_change` decimal(12,2) NOT NULL DEFAULT '0.00',
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `performed_by` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_adjustments_company_id_foreign` (`company_id`),
  KEY `stock_adjustments_warehouse_id_foreign` (`warehouse_id`),
  KEY `stock_adjustments_created_by_foreign` (`created_by`),
  KEY `stock_adjustments_approved_by_foreign` (`approved_by`),
  CONSTRAINT `stock_adjustments_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_adjustments_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_adjustments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_adjustments_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `stock_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_levels` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `quantity_on_hand` int NOT NULL DEFAULT '0',
  `quantity_reserved` int NOT NULL DEFAULT '0',
  `quantity_available` int NOT NULL DEFAULT '0',
  `bin_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `average_cost` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT 'Weighted average cost at time of last receipt',
  `last_movement_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stock_levels_product_id_warehouse_id_unique` (`product_id`,`warehouse_id`),
  KEY `stock_levels_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `stock_levels_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_levels_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `stock_transfer_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_transfer_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `stock_transfer_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `quantity_requested` decimal(12,2) NOT NULL DEFAULT '0.00',
  `quantity_shipped` decimal(12,2) NOT NULL DEFAULT '0.00',
  `quantity_received` decimal(12,2) NOT NULL DEFAULT '0.00',
  `quantity` int NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_transfer_items_stock_transfer_id_foreign` (`stock_transfer_id`),
  KEY `stock_transfer_items_product_id_foreign` (`product_id`),
  CONSTRAINT `stock_transfer_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_transfer_items_stock_transfer_id_foreign` FOREIGN KEY (`stock_transfer_id`) REFERENCES `stock_transfers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `stock_transfers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_transfers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `transfer_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delivery_note_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_warehouse_id` bigint unsigned NOT NULL,
  `to_warehouse_id` bigint unsigned NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `priority` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `requested_date` date DEFAULT NULL,
  `shipped_date` date DEFAULT NULL,
  `received_date` date DEFAULT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `transfer_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `requested_by` bigint unsigned DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `approval_level` tinyint unsigned NOT NULL DEFAULT '1',
  `level1_approved_by` bigint unsigned DEFAULT NULL,
  `level1_approved_at` timestamp NULL DEFAULT NULL,
  `level2_approved_by` bigint unsigned DEFAULT NULL,
  `level2_approved_at` timestamp NULL DEFAULT NULL,
  `shipped_by` bigint unsigned DEFAULT NULL,
  `received_by` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_transfers_company_id_foreign` (`company_id`),
  KEY `stock_transfers_from_warehouse_id_foreign` (`from_warehouse_id`),
  KEY `stock_transfers_to_warehouse_id_foreign` (`to_warehouse_id`),
  KEY `stock_transfers_created_by_foreign` (`created_by`),
  KEY `stock_transfers_level1_approved_by_foreign` (`level1_approved_by`),
  KEY `stock_transfers_level2_approved_by_foreign` (`level2_approved_by`),
  CONSTRAINT `stock_transfers_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_transfers_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_transfers_from_warehouse_id_foreign` FOREIGN KEY (`from_warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_transfers_level1_approved_by_foreign` FOREIGN KEY (`level1_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_transfers_level2_approved_by_foreign` FOREIGN KEY (`level2_approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_transfers_to_warehouse_id_foreign` FOREIGN KEY (`to_warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `subcontractor_packages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subcontractor_packages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `subcontractor_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `scope_of_work` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `contract_value` decimal(14,2) DEFAULT NULL,
  `paid_to_date` decimal(14,2) NOT NULL DEFAULT '0.00',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `progress_percent` int NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subcontractor_packages_subcontractor_id_foreign` (`subcontractor_id`),
  KEY `subcontractor_packages_cde_project_id_foreign` (`cde_project_id`),
  KEY `subcontractor_packages_created_by_foreign` (`created_by`),
  KEY `subcontractor_packages_company_id_cde_project_id_index` (`company_id`,`cde_project_id`),
  CONSTRAINT `subcontractor_packages_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subcontractor_packages_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subcontractor_packages_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `subcontractor_packages_subcontractor_id_foreign` FOREIGN KEY (`subcontractor_id`) REFERENCES `subcontractors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `subcontractors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subcontractors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `specialty` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registration_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `rating` int DEFAULT NULL COMMENT '1-5 star rating',
  `insurance_expiry` date DEFAULT NULL,
  `license_expiry` date DEFAULT NULL,
  `safety_certified` tinyint(1) NOT NULL DEFAULT '0',
  `certifications` json DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subcontractors_created_by_foreign` (`created_by`),
  KEY `subcontractors_company_id_index` (`company_id`),
  CONSTRAINT `subcontractors_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subcontractors_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `submittals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `submittals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `submittal_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `current_revision` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `submitted_by` bigint unsigned DEFAULT NULL,
  `reviewer_id` bigint unsigned DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `review_comments` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `submittals_company_id_foreign` (`company_id`),
  KEY `submittals_cde_project_id_foreign` (`cde_project_id`),
  KEY `submittals_submitted_by_foreign` (`submitted_by`),
  KEY `submittals_reviewer_id_foreign` (`reviewer_id`),
  CONSTRAINT `submittals_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `submittals_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `submittals_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `submittals_submitted_by_foreign` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscriptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `monthly_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `yearly_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `base_platform_price` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT 'Fixed monthly platform fee for the company',
  `per_project_price` decimal(12,2) NOT NULL DEFAULT '0.00' COMMENT 'Default monthly cost per active project',
  `module_prices` json DEFAULT NULL COMMENT 'Per-module monthly prices: {"cde": 50, "boq": 30, ...}',
  `included_projects` int NOT NULL DEFAULT '0' COMMENT 'Number of projects included in base price (0 = none)',
  `billing_cycle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'monthly',
  `max_users` int NOT NULL DEFAULT '5',
  `max_projects` int NOT NULL DEFAULT '10',
  `max_storage_gb` int NOT NULL DEFAULT '5',
  `included_modules` json DEFAULT NULL,
  `features` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_popular` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscriptions_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `contact_person` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_terms` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `suppliers_company_id_foreign` (`company_id`),
  CONSTRAINT `suppliers_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `task_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_assignments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'assignee',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `task_assignments_task_id_user_id_unique` (`task_id`,`user_id`),
  KEY `task_assignments_user_id_foreign` (`user_id`),
  CONSTRAINT `task_assignments_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `task_assignments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `task_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_attachments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_size` bigint unsigned NOT NULL DEFAULT '0',
  `uploaded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_attachments_task_id_foreign` (`task_id`),
  KEY `task_attachments_uploaded_by_foreign` (`uploaded_by`),
  CONSTRAINT `task_attachments_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `task_attachments_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `task_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_comments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_comments_task_id_foreign` (`task_id`),
  KEY `task_comments_user_id_foreign` (`user_id`),
  KEY `task_comments_parent_id_foreign` (`parent_id`),
  CONSTRAINT `task_comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `task_comments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `task_comments_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `task_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `task_dependencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_dependencies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint unsigned NOT NULL,
  `depends_on_id` bigint unsigned NOT NULL,
  `dependency_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'finish_to_start',
  `lag_days` int NOT NULL DEFAULT '0' COMMENT 'Lead (<0) or Lag (>0) time in days',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_dependencies_task_id_foreign` (`task_id`),
  KEY `task_dependencies_depends_on_id_foreign` (`depends_on_id`),
  CONSTRAINT `task_dependencies_depends_on_id_foreign` FOREIGN KEY (`depends_on_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `task_dependencies_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `task_time_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_time_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `started_at` timestamp NOT NULL,
  `ended_at` timestamp NULL DEFAULT NULL,
  `hours` decimal(8,2) DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_time_logs_task_id_foreign` (`task_id`),
  KEY `task_time_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `task_time_logs_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `task_time_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tasks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `work_order_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `calendar` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Calendar name for working days (null = project default)',
  `priority` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'todo',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `assigned_to` bigint unsigned DEFAULT NULL,
  `resource_names` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Comma-separated resource names for display',
  `resource_units` smallint unsigned NOT NULL DEFAULT '100' COMMENT 'Percentage allocation (100 = full time)',
  `start_date` date DEFAULT NULL,
  `actual_start` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `actual_finish` date DEFAULT NULL,
  `duration_days` int DEFAULT NULL COMMENT 'Task duration in working days',
  `constraint_type` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ASAP, ALAP, MSO, MFO, SNET, SNLT, FNET, FNLT',
  `constraint_date` date DEFAULT NULL,
  `baseline_start` date DEFAULT NULL,
  `baseline_finish` date DEFAULT NULL,
  `baseline_duration` int DEFAULT NULL COMMENT 'Baseline duration in days',
  `baseline_cost` decimal(14,2) DEFAULT NULL,
  `baseline_work` decimal(10,2) DEFAULT NULL COMMENT 'Baseline effort in hours',
  `completed_at` timestamp NULL DEFAULT NULL,
  `estimated_hours` int DEFAULT NULL,
  `actual_hours` int DEFAULT NULL,
  `fixed_cost` decimal(14,2) NOT NULL DEFAULT '0.00',
  `cost_rate` decimal(10,2) DEFAULT NULL COMMENT 'Cost per hour for resource',
  `actual_cost` decimal(14,2) NOT NULL DEFAULT '0.00',
  `progress_percent` int NOT NULL DEFAULT '0',
  `commissioning_phase` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'pre_commissioning, mech_completion, energization, hot_commissioning, performance_test',
  `method_statement` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Reference to method statement document',
  `chainage_from` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Start chainage for this task',
  `chainage_to` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'End chainage for this task',
  `road_layer` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Layer being worked on',
  `attachments` json DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `wbs_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Work Breakdown Structure code e.g. 1.2.3',
  `outline_level` tinyint unsigned NOT NULL DEFAULT '0' COMMENT 'Indent level in WBS (0 = top level)',
  `is_summary` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'True if task has children (auto-computed)',
  `is_milestone` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'True if task is a milestone (zero duration)',
  `parent_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `bcws` decimal(14,2) DEFAULT NULL COMMENT 'Budgeted Cost of Work Scheduled',
  `bcwp` decimal(14,2) DEFAULT NULL COMMENT 'Budgeted Cost of Work Performed',
  `acwp` decimal(14,2) DEFAULT NULL COMMENT 'Actual Cost of Work Performed',
  PRIMARY KEY (`id`),
  KEY `tasks_created_by_foreign` (`created_by`),
  KEY `tasks_parent_id_foreign` (`parent_id`),
  KEY `tasks_assigned_to_foreign` (`assigned_to`),
  KEY `tasks_work_order_id_foreign` (`work_order_id`),
  KEY `tasks_cde_project_id_wbs_code_index` (`cde_project_id`,`wbs_code`),
  KEY `tasks_cde_project_id_outline_level_index` (`cde_project_id`,`outline_level`),
  KEY `tasks_cde_project_id_is_summary_index` (`cde_project_id`,`is_summary`),
  KEY `tasks_cde_project_id_is_milestone_index` (`cde_project_id`,`is_milestone`),
  KEY `tasks_company_status_deleted_idx` (`company_id`,`status`,`deleted_at`),
  KEY `tasks_company_due_date_idx` (`company_id`,`due_date`),
  KEY `tasks_company_assignee_idx` (`company_id`,`assigned_to`),
  KEY `tasks_project_status_idx` (`cde_project_id`,`status`),
  CONSTRAINT `tasks_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tasks_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tasks_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tasks_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tasks_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `tasks` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tasks_work_order_id_foreign` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `technician_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `technician_locations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `speed` decimal(8,2) DEFAULT NULL,
  `heading` decimal(5,2) DEFAULT NULL,
  `accuracy` decimal(8,2) DEFAULT NULL,
  `battery_level` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `technician_locations_company_id_foreign` (`company_id`),
  KEY `technician_locations_user_id_created_at_index` (`user_id`,`created_at`),
  CONSTRAINT `technician_locations_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `technician_locations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `technician_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `technician_statuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `current_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `work_order_id` bigint unsigned DEFAULT NULL,
  `status_updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `technician_statuses_company_id_foreign` (`company_id`),
  KEY `technician_statuses_user_id_foreign` (`user_id`),
  KEY `technician_statuses_work_order_id_foreign` (`work_order_id`),
  CONSTRAINT `technician_statuses_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `technician_statuses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `technician_statuses_work_order_id_foreign` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tenders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tenders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'identified',
  `estimated_value` decimal(14,2) DEFAULT NULL,
  `bid_amount` decimal(14,2) DEFAULT NULL,
  `submission_deadline` date DEFAULT NULL,
  `submitted_at` date DEFAULT NULL,
  `decision_date` date DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `win_probability` int DEFAULT NULL COMMENT '0-100%',
  `competitors` text COLLATE utf8mb4_unicode_ci,
  `strategy_notes` text COLLATE utf8mb4_unicode_ci,
  `loss_reason` text COLLATE utf8mb4_unicode_ci,
  `document_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachments` json DEFAULT NULL,
  `assigned_to` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tenders_assigned_to_foreign` (`assigned_to`),
  KEY `tenders_created_by_foreign` (`created_by`),
  KEY `tenders_company_id_index` (`company_id`),
  KEY `tenders_status_index` (`status`),
  CONSTRAINT `tenders_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tenders_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tenders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ticket_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_comments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `comment` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_comments_ticket_id_foreign` (`ticket_id`),
  KEY `ticket_comments_user_id_foreign` (`user_id`),
  CONSTRAINT `ticket_comments_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ticket_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ticket_histories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_histories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `ticket_status_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_histories_ticket_id_foreign` (`ticket_id`),
  KEY `ticket_histories_user_id_foreign` (`user_id`),
  KEY `ticket_histories_ticket_status_id_foreign` (`ticket_status_id`),
  CONSTRAINT `ticket_histories_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ticket_histories_ticket_status_id_foreign` FOREIGN KEY (`ticket_status_id`) REFERENCES `ticket_statuses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ticket_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ticket_priorities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_priorities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#6B7280',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ticket_priorities_name_unique` (`name`),
  KEY `idx_ticket_priorities_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ticket_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_statuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#3490dc',
  `is_completed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_ticket_statuses_project_sort` (`project_id`,`sort_order`),
  KEY `idx_ticket_statuses_project_completed` (`project_id`,`is_completed`),
  CONSTRAINT `ticket_statuses_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ticket_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ticket_users_ticket_id_user_id_unique` (`ticket_id`,`user_id`),
  KEY `idx_ticket_users_ticket_user` (`ticket_id`,`user_id`),
  KEY `idx_ticket_users_user` (`user_id`),
  CONSTRAINT `ticket_users_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ticket_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tickets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `project_id` bigint unsigned NOT NULL,
  `ticket_status_id` bigint unsigned NOT NULL,
  `priority_id` bigint unsigned DEFAULT NULL,
  `epic_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `start_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tickets_uuid_unique` (`uuid`),
  KEY `tickets_epic_id_foreign` (`epic_id`),
  KEY `idx_tickets_project_status` (`project_id`,`ticket_status_id`),
  KEY `idx_tickets_status_created` (`ticket_status_id`,`created_at`),
  KEY `idx_tickets_project_created` (`project_id`,`created_at`),
  KEY `idx_tickets_project_updated` (`project_id`,`updated_at`),
  KEY `idx_tickets_due_date` (`due_date`),
  KEY `idx_tickets_priority` (`priority_id`),
  KEY `idx_tickets_created_by` (`created_by`),
  CONSTRAINT `tickets_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tickets_epic_id_foreign` FOREIGN KEY (`epic_id`) REFERENCES `epics` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tickets_priority_id_foreign` FOREIGN KEY (`priority_id`) REFERENCES `ticket_priorities` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tickets_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tickets_ticket_status_id_foreign` FOREIGN KEY (`ticket_status_id`) REFERENCES `ticket_statuses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `timesheets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `timesheets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `date` date NOT NULL,
  `regular_hours` decimal(5,2) NOT NULL DEFAULT '0.00',
  `overtime_hours` decimal(5,2) NOT NULL DEFAULT '0.00',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `approved_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `timesheets_company_id_foreign` (`company_id`),
  KEY `timesheets_user_id_foreign` (`user_id`),
  KEY `timesheets_cde_project_id_foreign` (`cde_project_id`),
  KEY `timesheets_approved_by_foreign` (`approved_by`),
  CONSTRAINT `timesheets_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `timesheets_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `timesheets_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `timesheets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `toolbox_talks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `toolbox_talks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `topic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `conducted_date` datetime NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attendee_count` int NOT NULL DEFAULT '0',
  `attendees` json DEFAULT NULL,
  `conducted_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `toolbox_talks_company_id_foreign` (`company_id`),
  KEY `toolbox_talks_cde_project_id_foreign` (`cde_project_id`),
  KEY `toolbox_talks_conducted_by_foreign` (`conducted_by`),
  CONSTRAINT `toolbox_talks_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `toolbox_talks_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `toolbox_talks_conducted_by_foreign` FOREIGN KEY (`conducted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `transmittal_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transmittal_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transmittal_id` bigint unsigned NOT NULL,
  `cde_document_id` bigint unsigned DEFAULT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `copies` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transmittal_items_transmittal_id_foreign` (`transmittal_id`),
  KEY `transmittal_items_cde_document_id_foreign` (`cde_document_id`),
  CONSTRAINT `transmittal_items_cde_document_id_foreign` FOREIGN KEY (`cde_document_id`) REFERENCES `cde_documents` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transmittal_items_transmittal_id_foreign` FOREIGN KEY (`transmittal_id`) REFERENCES `transmittals` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `transmittals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transmittals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `transmittal_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `from_user_id` bigint unsigned DEFAULT NULL,
  `to_organization` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_contact` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purpose` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `acknowledged_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transmittals_company_id_foreign` (`company_id`),
  KEY `transmittals_cde_project_id_foreign` (`cde_project_id`),
  KEY `transmittals_from_user_id_foreign` (`from_user_id`),
  CONSTRAINT `transmittals_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transmittals_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transmittals_from_user_id_foreign` FOREIGN KEY (`from_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `trips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trips` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `to_location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `departure_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'planned',
  `estimated_cost` decimal(12,2) DEFAULT NULL,
  `actual_cost` decimal(12,2) DEFAULT NULL,
  `purpose` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `trips_company_id_foreign` (`company_id`),
  KEY `trips_user_id_foreign` (`user_id`),
  CONSTRAINT `trips_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `trips_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_invitations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_invitations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `company_id` bigint unsigned DEFAULT NULL,
  `invited_by` bigint unsigned DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','accepted','expired','revoked') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `expires_at` timestamp NOT NULL,
  `accepted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_invitations_token_unique` (`token`),
  KEY `user_invitations_user_id_foreign` (`user_id`),
  KEY `user_invitations_company_id_foreign` (`company_id`),
  KEY `user_invitations_invited_by_foreign` (`invited_by`),
  KEY `user_invitations_email_status_index` (`email`,`status`),
  KEY `user_invitations_token_status_index` (`token`,`status`),
  CONSTRAINT `user_invitations_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `user_invitations_invited_by_foreign` FOREIGN KEY (`invited_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `user_invitations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'member',
  `job_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `timezone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `has_email_authentication` tinyint(1) NOT NULL DEFAULT '0',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `google_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_changed_at` timestamp NULL DEFAULT NULL COMMENT 'Tracks when password was last changed for expiry enforcement',
  `must_change_password` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_company_id_foreign` (`company_id`),
  CONSTRAINT `users_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `vendors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vendors` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `contact_person` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vendors_company_id_foreign` (`company_id`),
  CONSTRAINT `vendors_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `warehouses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `warehouses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manager_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `warehouses_company_id_foreign` (`company_id`),
  KEY `warehouses_manager_id_foreign` (`manager_id`),
  CONSTRAINT `warehouses_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `warehouses_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `work_order_appointments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_order_appointments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `work_order_id` bigint unsigned NOT NULL,
  `technician_id` bigint unsigned DEFAULT NULL,
  `scheduled_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `work_order_appointments_work_order_id_foreign` (`work_order_id`),
  KEY `work_order_appointments_technician_id_foreign` (`technician_id`),
  CONSTRAINT `work_order_appointments_technician_id_foreign` FOREIGN KEY (`technician_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `work_order_appointments_work_order_id_foreign` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `work_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `work_order_id` bigint unsigned NOT NULL,
  `service_part_id` bigint unsigned DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'service',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `unit_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `work_order_items_work_order_id_foreign` (`work_order_id`),
  KEY `work_order_items_service_part_id_foreign` (`service_part_id`),
  CONSTRAINT `work_order_items_service_part_id_foreign` FOREIGN KEY (`service_part_id`) REFERENCES `service_parts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `work_order_items_work_order_id_foreign` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `work_order_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_order_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `request_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `client_id` bigint unsigned DEFAULT NULL,
  `asset_id` bigint unsigned DEFAULT NULL,
  `priority` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `requested_by` bigint unsigned DEFAULT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `work_order_requests_company_id_foreign` (`company_id`),
  KEY `work_order_requests_client_id_foreign` (`client_id`),
  KEY `work_order_requests_asset_id_foreign` (`asset_id`),
  KEY `work_order_requests_requested_by_foreign` (`requested_by`),
  KEY `work_order_requests_approved_by_foreign` (`approved_by`),
  CONSTRAINT `work_order_requests_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `work_order_requests_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `work_order_requests_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  CONSTRAINT `work_order_requests_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `work_order_requests_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `work_order_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_order_tasks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `work_order_id` bigint unsigned NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_completed` tinyint(1) NOT NULL DEFAULT '0',
  `completed_by` bigint unsigned DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `work_order_tasks_work_order_id_foreign` (`work_order_id`),
  KEY `work_order_tasks_completed_by_foreign` (`completed_by`),
  CONSTRAINT `work_order_tasks_completed_by_foreign` FOREIGN KEY (`completed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `work_order_tasks_work_order_id_foreign` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `work_order_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_order_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `work_order_types_company_id_foreign` (`company_id`),
  CONSTRAINT `work_order_types_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `work_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `cde_project_id` bigint unsigned DEFAULT NULL,
  `wo_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `work_order_type_id` bigint unsigned DEFAULT NULL,
  `client_id` bigint unsigned DEFAULT NULL,
  `asset_id` bigint unsigned DEFAULT NULL,
  `work_order_request_id` bigint unsigned DEFAULT NULL,
  `priority` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `assigned_to` bigint unsigned DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `preferred_date` date DEFAULT NULL,
  `preferred_time` time DEFAULT NULL,
  `preferred_notes` text COLLATE utf8mb4_unicode_ci,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `is_inspection` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Testing & Inspection Plan (TIP) work order',
  `inspection_type` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'visual, dimensional, electrical, pressure, functional, load',
  `hold_point` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'hold, witness, review — inspection hold point classification',
  `acceptance_criteria` text COLLATE utf8mb4_unicode_ci COMMENT 'Pass/fail criteria',
  `test_result` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'pass, fail, conditional, na',
  `test_readings` text COLLATE utf8mb4_unicode_ci COMMENT 'JSON: measured values / readings',
  `equipment_tested` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tag number or equipment reference',
  `method_statement_ref` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Reference to method statement',
  `is_commissioning` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Commissioning activity work order',
  `commissioning_phase` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'pre_commissioning, mechanical_completion, energization, hot_commissioning, performance_test',
  `system_tag` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'System/subsystem tag being commissioned',
  `is_road_test` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Road material/pavement test',
  `road_test_type` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'cbr, compaction, sieve_analysis, atterberg, asphalt_core, marshall, deflection, dcp, sand_replacement, plate_bearing',
  `test_chainage` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Chainage where test was done',
  `test_layer` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Which road layer was tested',
  `sample_reference` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Lab sample reference number',
  `test_lab` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Name of testing laboratory',
  `test_value_achieved` decimal(10,2) DEFAULT NULL COMMENT 'Actual test result value',
  `test_value_required` decimal(10,2) DEFAULT NULL COMMENT 'Specification requirement',
  `test_unit` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '%, MPa, mm, kN, etc.',
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `work_orders_work_order_type_id_foreign` (`work_order_type_id`),
  KEY `work_orders_client_id_foreign` (`client_id`),
  KEY `work_orders_asset_id_foreign` (`asset_id`),
  KEY `work_orders_work_order_request_id_foreign` (`work_order_request_id`),
  KEY `work_orders_assigned_to_foreign` (`assigned_to`),
  KEY `work_orders_created_by_foreign` (`created_by`),
  KEY `work_orders_cde_project_id_foreign` (`cde_project_id`),
  KEY `wo_company_status_deleted_idx` (`company_id`,`status`,`deleted_at`),
  KEY `wo_company_due_date_idx` (`company_id`,`due_date`),
  KEY `wo_company_assignee_idx` (`company_id`,`assigned_to`),
  CONSTRAINT `work_orders_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE SET NULL,
  CONSTRAINT `work_orders_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `work_orders_cde_project_id_foreign` FOREIGN KEY (`cde_project_id`) REFERENCES `cde_projects` (`id`) ON DELETE SET NULL,
  CONSTRAINT `work_orders_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  CONSTRAINT `work_orders_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `work_orders_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `work_orders_work_order_request_id_foreign` FOREIGN KEY (`work_order_request_id`) REFERENCES `work_order_requests` (`id`) ON DELETE SET NULL,
  CONSTRAINT `work_orders_work_order_type_id_foreign` FOREIGN KEY (`work_order_type_id`) REFERENCES `work_order_types` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `worker_certifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `worker_certifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `certification_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `issuing_body` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `certificate_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `issued_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `document_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `worker_certifications_user_id_foreign` (`user_id`),
  KEY `worker_certifications_company_id_user_id_index` (`company_id`,`user_id`),
  CONSTRAINT `worker_certifications_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `worker_certifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'0001_01_01_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2025_03_02_200055_create_projects_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2025_03_02_200109_create_project_members_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2025_03_02_200213_create_ticket_statuses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2025_03_02_200246_create_tickets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2025_03_13_154334_add_uuid_to_ticket_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2025_03_13_223706_create_permission_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2025_03_27_065113_create_epics_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2025_03_28_144500_create_ticket_histories_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2025_04_11_173545_create_ticket_comments_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2025_05_06_220233_add_sort_order_to_ticket_statuses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2025_05_06_221002_add_sort_color_to_ticket_statuses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2025_05_10_202453_add_start_date_end_date_to_project',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2025_06_24_212547_add_ticket_users_table_and_created_by_column',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2025_06_24_212750_migrate_existing_user_id_data',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2025_06_24_212838_drop_user_id_column_from_tickets',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2025_06_29_052227_change_tickets_description_to_longtext',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2025_07_04_164429_create_ticket_priorities_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2025_07_04_164558_add_priority_id_to_tickets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2025_07_16_182905_add_pinned_to_projects_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2025_07_30_211411_create_project_notes_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2025_08_05_030102_create_external_access_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2025_08_05_031001_generate_client_access_for_existing_projects_safe',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2025_08_08_051806_create_notifications_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2025_08_17_041650_add_start_date_to_tickets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2025_08_17_041901_populate_start_date_from_created_at_in_tickets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2025_08_23_214007_add_is_completed_to_ticket_statuses_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2025_08_25_174118_add_google_id_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2025_09_16_051002_add_performance_indexes',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2025_10_17_215303_add_sort_order_to_epics_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2025_11_04_181918_add_color_to_projects_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2025_11_06_052000_create_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2025_11_08_063526_add_user_id_to_settings_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2026_02_03_180000_create_project_folders_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2026_02_03_180100_create_project_documents_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2026_02_03_180200_create_document_versions_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2026_02_03_180300_create_document_history_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2026_02_10_100000_create_saas_foundation_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2026_02_10_100100_create_core_fsm_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2026_02_10_100200_create_hr_operations_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2026_02_10_100300_create_cde_module_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2026_02_10_100400_create_field_management_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45,'2026_02_10_100500_create_task_workflow_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2026_02_10_100600_create_inventory_module_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2026_02_10_100700_create_cost_contracts_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48,'2026_02_10_100800_create_planning_progress_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49,'2026_02_10_100900_create_boq_module_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50,'2026_02_10_101000_create_sheq_module_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (51,'2026_02_10_102900_add_notifiable_columns_to_notifications_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (52,'2026_02_10_144100_add_extra_columns_to_cde_projects_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (53,'2026_02_10_190000_create_project_module_access_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (54,'2026_02_10_191300_fix_notifications_table_uuid',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (55,'2026_02_10_230545_add_company_id_to_roles_table',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (56,'2026_02_11_140436_create_email_templates_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (57,'2026_02_11_180958_add_currency_format_to_companies_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (58,'2026_02_12_192400_add_cde_project_id_to_work_orders_and_purchase_orders',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (59,'2026_02_14_120000_add_impact_fields_to_rfis_table',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (60,'2026_02_14_152357_create_personal_access_tokens_table',11);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (61,'2026_02_17_099999_create_safety_incidents_table',12);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (62,'2026_02_17_100000_create_inspection_templates_table',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (63,'2026_02_17_100001_create_inspection_checklist_items_table',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (64,'2026_02_17_100002_create_safety_inspections_table',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (65,'2026_02_17_100003_create_snag_items_table',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (66,'2026_02_17_100004_create_transmittals_table',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (67,'2026_02_17_140641_add_addon_columns_to_companies_table',14);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (68,'2026_02_17_234732_create_appointments_table',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (69,'2026_02_18_220000_enhance_boq_module',16);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (70,'2026_02_19_120000_add_preventive_action_to_safety_incidents',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (71,'2026_02_19_120001_add_assigned_to_and_work_order_id_to_tasks',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (72,'2026_02_23_112935_create_expenses_table',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (73,'2026_02_23_112955_add_cde_project_id_to_financials_tables',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (74,'2026_02_23_120345_create_invoice_items_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (75,'2026_02_23_120346_add_invoice_items_and_reminder_fields',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (76,'2026_02_23_135601_add_email_2fa_to_users_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (77,'2026_02_23_140147_add_task_attachments_and_contract_retainage_fields',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (78,'2026_02_26_091500_enhance_tasks_for_ms_project',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (79,'2026_02_28_180000_expand_inventory_store_management',23);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (80,'2026_02_28_181300_create_asset_tracking_tables',24);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (81,'2026_03_02_103800_create_grn_stock_transfers_adjustments',25);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (82,'2026_03_02_111800_add_po_approval_fields',26);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (83,'2026_03_02_114600_create_daily_site_log_tasks',27);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (84,'2026_03_03_090000_enhance_asset_lifecycle_tracking',28);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (85,'2026_03_03_100000_add_currency_to_cde_projects',29);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (86,'2026_03_03_110000_add_currency_position_to_cde_projects',30);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (87,'2026_03_03_120000_create_delivery_notes_and_product_tracking',31);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (88,'2026_03_03_152056_create_material_requisitions_table',32);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (89,'2026_03_05_110000_add_boq_variance_tracking',33);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (90,'2026_03_05_120000_create_social_records_table',34);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (91,'2026_03_05_140000_create_contract_payments_table',35);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (92,'2026_03_05_155221_create_document_shares_table',36);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (93,'2026_03_06_112336_create_document_submissions_table',37);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (94,'2026_03_06_121815_create_quotations_and_items_tables',38);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (95,'2026_03_06_130604_add_per_project_billing_system',39);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (96,'2026_03_06_220000_add_billing_performance_indexes',40);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (97,'2026_03_06_223000_add_must_change_password_to_users',41);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (98,'2026_03_09_180916_add_retainage_columns_to_contracts_table',42);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (99,'2026_03_10_100444_create_plant_and_equipment_tables',43);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (100,'2026_03_10_102942_create_subcontractor_and_bidding_tables',44);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (101,'2026_03_10_103821_create_daily_site_diaries_table',45);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (102,'2026_03_10_104059_add_configurable_options_to_companies_table',46);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (103,'2026_03_10_100000_create_change_orders_drawings_payment_certs',47);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (104,'2026_03_10_110000_create_security_audit_tables',48);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (105,'2026_03_10_145200_enhance_modules_for_energy_projects',49);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (106,'2026_03_10_153800_enhance_modules_for_road_projects',50);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (107,'2026_03_11_232900_add_soft_deletes_to_change_orders_table',51);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (108,'2026_03_14_060000_add_password_security_columns',52);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (109,'2026_03_16_113752_create_user_invitations_table',53);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (110,'2026_03_16_161700_create_blocked_ips_table',54);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (111,'2026_03_18_003300_create_project_suggestions_table',55);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (112,'2026_03_18_004400_create_project_invitations_table',56);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (113,'2026_03_18_004900_add_priority_to_project_suggestions',57);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (115,'2026_03_23_111205_add_priority_to_project_suggestions_table',58);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (116,'2026_03_26_090000_enhance_inventory_tracking_v2',59);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (117,'2026_03_26_223926_add_soft_deletes_to_users_table',60);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (118,'2026_03_27_124041_create_contract_project_pivot_table',61);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (119,'2026_03_27_144818_enhance_approvals_and_delivery_notes_v1',61);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (120,'2026_03_29_002650_add_performance_indexes_to_core_tables',62);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (121,'2026_04_06_000000_add_soft_deletes_to_additional_models',63);
