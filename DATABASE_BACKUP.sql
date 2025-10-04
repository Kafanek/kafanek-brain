-- ============================================
-- KAFÁNEK BRAIN v1.2.0 - DATABASE BACKUP
-- ============================================
-- Backup všech tabulek před nasazením
-- Datum: 2025-10-04
-- ============================================

-- 1. BACKUP: Cache tabulka
DROP TABLE IF EXISTS `wp_kafanek_brain_cache_backup`;
CREATE TABLE `wp_kafanek_brain_cache_backup` LIKE `wp_kafanek_brain_cache`;
INSERT INTO `wp_kafanek_brain_cache_backup` SELECT * FROM `wp_kafanek_brain_cache`;

-- 2. BACKUP: Usage tracking
DROP TABLE IF EXISTS `wp_kafanek_brain_usage_backup`;
CREATE TABLE `wp_kafanek_brain_usage_backup` LIKE `wp_kafanek_brain_usage`;
INSERT INTO `wp_kafanek_brain_usage_backup` SELECT * FROM `wp_kafanek_brain_usage`;

-- 3. BACKUP: Neural models
DROP TABLE IF EXISTS `wp_kafanek_brain_neural_models_backup`;
CREATE TABLE `wp_kafanek_brain_neural_models_backup` LIKE `wp_kafanek_brain_neural_models`;
INSERT INTO `wp_kafanek_brain_neural_models_backup` SELECT * FROM `wp_kafanek_brain_neural_models`;

-- 4. BACKUP: Brand voices
DROP TABLE IF EXISTS `wp_kafanek_brain_brand_voices_backup`;
CREATE TABLE `wp_kafanek_brain_brand_voices_backup` LIKE `wp_kafanek_brain_brand_voices`;
INSERT INTO `wp_kafanek_brain_brand_voices_backup` SELECT * FROM `wp_kafanek_brain_brand_voices`;

-- 5. BACKUP: Chatbot conversations
DROP TABLE IF EXISTS `wp_kafanek_chatbot_conversations_backup`;
CREATE TABLE `wp_kafanek_chatbot_conversations_backup` LIKE `wp_kafanek_chatbot_conversations`;
INSERT INTO `wp_kafanek_chatbot_conversations_backup` SELECT * FROM `wp_kafanek_chatbot_conversations`;

-- 6. BACKUP: Options (API keys a nastavení)
CREATE TABLE IF NOT EXISTS `wp_kafanek_brain_options_backup` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `option_name` varchar(191) NOT NULL,
    `option_value` longtext,
    `backup_date` datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `option_name` (`option_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Zálohovat všechny Kafánek Brain options
INSERT INTO `wp_kafanek_brain_options_backup` (`option_name`, `option_value`)
SELECT option_name, option_value FROM `wp_options` 
WHERE option_name LIKE 'kafanek_%';

-- ============================================
-- RESTORE SCRIPT (použít v případě rollback)
-- ============================================

/*
-- RESTORE: Cache
TRUNCATE TABLE `wp_kafanek_brain_cache`;
INSERT INTO `wp_kafanek_brain_cache` SELECT * FROM `wp_kafanek_brain_cache_backup`;

-- RESTORE: Usage
TRUNCATE TABLE `wp_kafanek_brain_usage`;
INSERT INTO `wp_kafanek_brain_usage` SELECT * FROM `wp_kafanek_brain_usage_backup`;

-- RESTORE: Neural models
TRUNCATE TABLE `wp_kafanek_brain_neural_models`;
INSERT INTO `wp_kafanek_brain_neural_models` SELECT * FROM `wp_kafanek_brain_neural_models_backup`;

-- RESTORE: Brand voices
TRUNCATE TABLE `wp_kafanek_brain_brand_voices`;
INSERT INTO `wp_kafanek_brain_brand_voices` SELECT * FROM `wp_kafanek_brain_brand_voices_backup`;

-- RESTORE: Chatbot conversations
TRUNCATE TABLE `wp_kafanek_chatbot_conversations`;
INSERT INTO `wp_kafanek_chatbot_conversations` SELECT * FROM `wp_kafanek_chatbot_conversations_backup`;

-- RESTORE: Options
DELETE FROM `wp_options` WHERE option_name LIKE 'kafanek_%';
INSERT INTO `wp_options` (`option_name`, `option_value`)
SELECT `option_name`, `option_value` FROM `wp_kafanek_brain_options_backup`
WHERE backup_date = (SELECT MAX(backup_date) FROM `wp_kafanek_brain_options_backup`);
*/

-- ============================================
-- VERIFICATION QUERIES
-- ============================================

-- Ověřit počet záznamů
SELECT 'Cache' as tabulka, COUNT(*) as pocet FROM `wp_kafanek_brain_cache`
UNION ALL
SELECT 'Usage', COUNT(*) FROM `wp_kafanek_brain_usage`
UNION ALL
SELECT 'Neural Models', COUNT(*) FROM `wp_kafanek_brain_neural_models`
UNION ALL
SELECT 'Brand Voices', COUNT(*) FROM `wp_kafanek_brain_brand_voices`
UNION ALL
SELECT 'Chatbot Conversations', COUNT(*) FROM `wp_kafanek_chatbot_conversations`;

-- Ověřit options
SELECT COUNT(*) as kafanek_options FROM `wp_options` WHERE option_name LIKE 'kafanek_%';

-- ============================================
-- CLEANUP (po úspěšném nasazení)
-- ============================================

/*
-- Smazat backup tabulky po 30 dnech (pouze pokud vše funguje)
DROP TABLE IF EXISTS `wp_kafanek_brain_cache_backup`;
DROP TABLE IF EXISTS `wp_kafanek_brain_usage_backup`;
DROP TABLE IF EXISTS `wp_kafanek_brain_neural_models_backup`;
DROP TABLE IF EXISTS `wp_kafanek_brain_brand_voices_backup`;
DROP TABLE IF EXISTS `wp_kafanek_chatbot_conversations_backup`;
DROP TABLE IF EXISTS `wp_kafanek_brain_options_backup`;
*/
