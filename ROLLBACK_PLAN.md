# 🔄 ROLLBACK PLAN - Kafánek Brain v1.2.0

**V případě kritických problémů po nasazení**

---

## 🚨 KDY PROVÉST ROLLBACK

### Kritické situace:
- ❌ WordPress Admin nedostupný (500/Fatal error)
- ❌ WooCommerce checkout nefunguje
- ❌ Database corruption
- ❌ Site completely down
- ❌ Security breach
- ❌ Data loss

### Non-kritické (nemusí rollback):
- ⚠️ Styling issues
- ⚠️ Minor bugs v admin
- ⚠️ AI API timeout (dočasné)
- ⚠️ Cache issues (lze vyčistit)

---

## 📋 ROLLBACK CHECKLIST (v pořadí!)

### ✅ STEP 1: Deaktivovat plugin (2 min)
```
1. Přihlásit se do WP Admin
2. Pluginy → Nainstalované pluginy
3. Najít "Kafánkův Mozek - AI WordPress Brain"
4. Kliknout "Deaktivovat"
5. Ověřit že web funguje
```

**Pokud WP Admin nedostupný:**
```bash
# Přes FTP/SSH:
mv wp-content/plugins/kafanek-brain wp-content/plugins/kafanek-brain-DISABLED
# Site by měl být ihned funkční
```

---

### ✅ STEP 2: Restore Database (5 min)

```sql
-- Spustit v phpMyAdmin nebo MySQL client:

-- 1. Restore Cache
TRUNCATE TABLE wp_kafanek_brain_cache;
INSERT INTO wp_kafanek_brain_cache 
SELECT * FROM wp_kafanek_brain_cache_backup;

-- 2. Restore Usage
TRUNCATE TABLE wp_kafanek_brain_usage;
INSERT INTO wp_kafanek_brain_usage 
SELECT * FROM wp_kafanek_brain_usage_backup;

-- 3. Restore Neural Models
TRUNCATE TABLE wp_kafanek_brain_neural_models;
INSERT INTO wp_kafanek_brain_neural_models 
SELECT * FROM wp_kafanek_brain_neural_models_backup;

-- 4. Restore Brand Voices
TRUNCATE TABLE wp_kafanek_brain_brand_voices;
INSERT INTO wp_kafanek_brain_brand_voices 
SELECT * FROM wp_kafanek_brain_brand_voices_backup;

-- 5. Restore Chatbot Conversations
TRUNCATE TABLE wp_kafanek_chatbot_conversations;
INSERT INTO wp_kafanek_chatbot_conversations 
SELECT * FROM wp_kafanek_chatbot_conversations_backup;

-- 6. Restore Options
DELETE FROM wp_options WHERE option_name LIKE 'kafanek_%';
INSERT INTO wp_options (option_name, option_value)
SELECT option_name, option_value 
FROM wp_kafanek_brain_options_backup
WHERE backup_date = (SELECT MAX(backup_date) FROM wp_kafanek_brain_options_backup);
```

---

### ✅ STEP 3: Remove Plugin Files (2 min)

```bash
# Přes FTP nebo SSH:
rm -rf /path/to/wp-content/plugins/kafanek-brain/

# Nebo přes FTP:
# Smazat celou složku kafanek-brain
```

---

### ✅ STEP 4: Clean Database Tables (3 min)

```sql
-- Pouze pokud chcete úplně odstranit plugin:

DROP TABLE IF EXISTS wp_kafanek_brain_cache;
DROP TABLE IF EXISTS wp_kafanek_brain_cache_backup;
DROP TABLE IF EXISTS wp_kafanek_brain_usage;
DROP TABLE IF EXISTS wp_kafanek_brain_usage_backup;
DROP TABLE IF EXISTS wp_kafanek_brain_neural_models;
DROP TABLE IF EXISTS wp_kafanek_brain_neural_models_backup;
DROP TABLE IF EXISTS wp_kafanek_brain_brand_voices;
DROP TABLE IF EXISTS wp_kafanek_brain_brand_voices_backup;
DROP TABLE IF EXISTS wp_kafanek_chatbot_conversations;
DROP TABLE IF EXISTS wp_kafanek_chatbot_conversations_backup;
DROP TABLE IF EXISTS wp_kafanek_brain_options_backup;

-- Smazat options
DELETE FROM wp_options WHERE option_name LIKE 'kafanek_%';
```

---

### ✅ STEP 5: Verify Site Functionality (5 min)

**Test všech kritických funkcí:**
- [ ] WordPress Admin dostupný
- [ ] Frontend načítá správně
- [ ] WooCommerce checkout funguje
- [ ] Produkty zobrazeny
- [ ] User login/logout
- [ ] Kontaktní formuláře
- [ ] Žádné PHP errors v logu

---

### ✅ STEP 6: Clear All Caches (2 min)

```bash
# WordPress cache
wp cache flush

# Object cache (pokud používáte Redis/Memcached)
redis-cli FLUSHALL
# nebo
memcached -d restart

# Opcache
service php7.4-fpm reload
```

**Nebo v WP Admin:**
```
- WP Rocket → Clear cache
- W3 Total Cache → Empty all caches
- Autoptimize → Delete cache
```

---

### ✅ STEP 7: Notify Team (1 min)

**Email template:**
```
Subject: [ROLLBACK] Kafánek Brain v1.2.0

Status: ROLLBACK COMPLETED
Plugin: Kafánek Brain v1.2.0
Reason: [Důvod rollbacku]
Time: [Čas]
Duration: [Doba výpadku]

Akce provedené:
✅ Plugin deaktivován
✅ Database restored
✅ Files removed
✅ Site verified - funkční

Site je nyní stabilní. Analyzujeme problém.
```

---

## 🔧 TROUBLESHOOTING

### Problem: "White Screen of Death"
```bash
# 1. Disable plugin přes FTP
mv wp-content/plugins/kafanek-brain wp-content/plugins/kafanek-brain-OFF

# 2. Enable WP debug
# V wp-config.php:
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);

# 3. Check debug.log
tail -f wp-content/debug.log
```

### Problem: "Database Error"
```sql
-- Check tables exist:
SHOW TABLES LIKE 'wp_kafanek_%';

-- Repair tables:
REPAIR TABLE wp_kafanek_brain_cache;
REPAIR TABLE wp_kafanek_brain_usage;
```

### Problem: "Memory Exhausted"
```php
// V wp-config.php zvýšit:
define('WP_MEMORY_LIMIT', '256M');
define('WP_MAX_MEMORY_LIMIT', '512M');
```

---

## 📞 EMERGENCY CONTACTS

**Developer:** _____________________  
**Hosting Support:** _____________________  
**Database Admin:** _____________________  

---

## 📊 POST-ROLLBACK ANALYSIS

### Co analyzovat:
1. **Error logs** - Co způsobilo problém?
2. **Database integrity** - Jsou data konzistentní?
3. **Performance metrics** - Memory/CPU usage?
4. **User reports** - Co uživatelé reportovali?

### Before next deployment:
- [ ] Fix identified issues
- [ ] Enhanced staging tests
- [ ] Better monitoring
- [ ] Gradual rollout strategy

---

**⏱️ Celková doba rollbacku: ~20 minut**

**🎯 Cíl: Minimalizovat downtime, obnovit funkcionalitu**
