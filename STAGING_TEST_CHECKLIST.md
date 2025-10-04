# 🧪 STAGING TEST CHECKLIST - Kafánek Brain v1.2.0

**Před nasazením na produkci MUSÍ projít všechny testy! ✅**

---

## 📋 PRE-DEPLOYMENT

### ✅ 1. Backup & Rollback
- [ ] Database backup vytvořen (`DATABASE_BACKUP.sql`)
- [ ] Plugin files zazálohované
- [ ] WordPress core backup
- [ ] Rollback plán připraven (`ROLLBACK_PLAN.md`)

### ✅ 2. Environment Setup
- [ ] Staging URL: ______________________
- [ ] WordPress verze: 6.0+ ✅
- [ ] PHP verze: 7.4+ ✅
- [ ] Memory limit: 256M+ ✅
- [ ] Max execution time: 60s+ ✅

---

## 🔌 INSTALACE NA STAGING

### ✅ 3. Plugin Upload
- [ ] ZIP soubor vytvořen z `/kafanek-brain/`
- [ ] Nahrán přes WordPress Admin → Pluginy → Přidat nový
- [ ] Plugin aktivován bez chyb
- [ ] Žádné PHP warnings/errors v logu

### ✅ 4. Database Creation
- [ ] Tabulka `wp_kafanek_brain_cache` vytvořena
- [ ] Tabulka `wp_kafanek_brain_usage` vytvořena
- [ ] Tabulka `wp_kafanek_brain_neural_models` vytvořena
- [ ] Tabulka `wp_kafanek_brain_brand_voices` vytvořena
- [ ] Tabulka `wp_kafanek_chatbot_conversations` vytvořena

```sql
-- Ověření:
SHOW TABLES LIKE 'wp_kafanek_%';
```

### ✅ 5. Admin Menu
- [ ] Menu "Kafánkův Mozek" viditelné
- [ ] Submenu "Dashboard" funguje
- [ ] Submenu "Nastavení" funguje
- [ ] Submenu "AI Copywriter" funguje
- [ ] Submenu "Dynamic Pricing" funguje
- [ ] Submenu "Email Genius" funguje
- [ ] Submenu "AI Chatbot" funguje

---

## ⚙️ KONFIGURACE

### ✅ 6. API Keys Setup
- [ ] OpenAI API key nastaven (Nastavení → OpenAI)
- [ ] Claude API key nastaven (Nastavení → Claude)
- [ ] Gemini API key nastaven (Nastavení → Gemini)
- [ ] Azure API key nastaven (pokud používáte)
- [ ] Default provider vybrán

**Test:**
```php
// V admin panelu test connection
AI Engine → Test Connection → Mělo by vrátit ✅
```

### ✅ 7. WooCommerce Integration
- [ ] WooCommerce plugin aktivní
- [ ] Dynamic Pricing menu viditelné
- [ ] Test optimalizace jedné ceny
- [ ] Cena se updatuje v databázi
- [ ] WooCommerce produkt zobrazuje novou cenu

**Test produkt:**
```
Produkt ID: _______
Original cena: _______
Optimalizovaná: _______
```

### ✅ 8. Elementor Integration
- [ ] Elementor/Elementor Pro aktivní
- [ ] Kafánek widgets v Elementor editoru
- [ ] AI Content Generator widget funguje
- [ ] Golden Ratio Section widget funguje
- [ ] Neural Insights widget funguje
- [ ] AI Price Optimizer widget funguje

---

## 🤖 AI FEATURES TESTING

### ✅ 9. AI Content Studio
- [ ] Admin → AI Copywriter načte
- [ ] Vyber content type: "Blog Post"
- [ ] Zadej téma: "Test článek o AI"
- [ ] Klikni "Generovat"
- [ ] Obsah se vygeneruje (~ 5-10 sec)
- [ ] SEO score se vypočítá
- [ ] Brand voice analyzer funguje

**Výsledek:**
```
Content type: _____________
Word count: _______________
SEO score: ___/100
```

### ✅ 10. Dynamic Pricing
- [ ] Admin → Dynamic Pricing načte
- [ ] Vyber testovací produkt
- [ ] Klikni "Analyzovat & Optimalizovat"
- [ ] Zobrazí 3 price tiers (Budget, Standard, Premium)
- [ ] Ceny jsou v Golden Ratio (φ = 1.618)
- [ ] "Aplikovat cenu" updatuje produkt

**Výsledek:**
```
Original: _______ Kč
Budget: _______ Kč (÷ φ)
Standard: _______ Kč
Premium: _______ Kč (× φ)
```

### ✅ 11. Email Marketing Genius
- [ ] Admin → Email Genius načte
- [ ] Zadej téma emailu
- [ ] AI vygeneruje email content
- [ ] Subject line suggestions (6 variants)
- [ ] Open rate predictions zobrazeny
- [ ] φ-based structure (61.8% : 38.2%)

**MailPoet Integration (pokud aktivní):**
- [ ] MailPoet → AI Assistant menu
- [ ] Generovat subject lines
- [ ] Test subject analyzer
- [ ] Všechny AJAX calls fungují

### ✅ 12. AI Chatbot 🆕
- [ ] Frontend: Chatbot widget viditelný
- [ ] Pozice: pravý dolní roh ✅
- [ ] Klikni na widget → otevře se
- [ ] Uvítací zpráva zobrazena
- [ ] Napiš: "Ahoj" → Bot odpoví
- [ ] Intent detection: "greeting" ✅
- [ ] Quick actions zobrazeny

**Pokročilé testy:**
- [ ] "Hledám produkt" → Intent: product_search
- [ ] Produkty se zobrazí (pokud WC aktivní)
- [ ] "Kolik stojí" → Intent: price_inquiry
- [ ] "Kontakt" → Intent: contact
- [ ] Conversation history funguje
- [ ] Admin statistiky se ukládají

**Admin Panel:**
- [ ] Kafánkův Mozek → AI Chatbot
- [ ] Statistiky zobrazeny
- [ ] Poslední konverzace v tabulce
- [ ] Sentiment analysis funguje

### ✅ 13. Fibonacci Neural Network
- [ ] Admin → Dashboard → Neural Network
- [ ] Status: Active ✅
- [ ] Train model (test data)
- [ ] Make prediction
- [ ] Results zobrazeny
- [ ] Model se uloží

---

## 🔒 BEZPEČNOST

### ✅ 14. Security Tests
- [ ] Odhlásit se z WP Admin
- [ ] Zkus přístup k AJAX endpointům → 403 Forbidden ✅
- [ ] Zkus SQL injection v formulářích → Blokováno ✅
- [ ] Zkus XSS v input polích → Escaped ✅
- [ ] API keys nejsou viditelné v HTML source
- [ ] Nonce verification funguje na AJAX

**Test AJAX bez nonce:**
```javascript
// V browser console:
jQuery.post(ajaxurl, {
    action: 'kafanek_chatbot_message',
    message: 'test'
    // Bez nonce
});
// Mělo by vrátit error ❌
```

### ✅ 15. Capability Checks
- [ ] Vytvořit testovacího user (role: Subscriber)
- [ ] Přihlásit se jako Subscriber
- [ ] Admin menu "Kafánkův Mozek" není viditelné ✅
- [ ] Direct URL k admin stránkám → Permission denied ✅
- [ ] Přihlásit zpět jako Admin

---

## ⚡ PERFORMANCE

### ✅ 16. Cache Testing
- [ ] Vygeneruj AI content (1. request)
- [ ] Poznamenej response time: ______ sec
- [ ] Vygeneruj stejný content znovu (2. request)
- [ ] Response time: ______ sec (mělo by být < 1s)
- [ ] Cache hit: ✅

**Cache verification:**
```sql
SELECT COUNT(*) FROM wp_kafanek_brain_cache 
WHERE expires_at > NOW();
-- Mělo by být > 0
```

### ✅ 17. Database Performance
- [ ] Spusť slow query log
- [ ] Prováděj různé AI operace (5 min)
- [ ] Zkontroluj slow queries
- [ ] Všechny queries < 1s: ✅

### ✅ 18. Memory Usage
```php
// Přidej do wp-config.php:
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);

// Monitoring:
memory_get_peak_usage(true) / 1024 / 1024 . ' MB'
// Mělo by být < 128 MB
```

---

## 🌐 KOMPATIBILITA

### ✅ 19. Plugin Conflicts
**Deaktivuj postupně a testuj:**
- [ ] Všechny pluginy aktivní → Kafánek funguje ✅
- [ ] Deaktivuj SEO plugin → Kafánek funguje ✅
- [ ] Deaktivuj cache plugin → Kafánek funguje ✅
- [ ] Deaktivuj security plugin → Kafánek funguje ✅
- [ ] Reaktivuj všechny

### ✅ 20. Theme Compatibility
- [ ] Chatbot widget zobrazený korektně
- [ ] CSS nekoliduje s theme
- [ ] JavaScript funguje bez errors
- [ ] Responsive na mobilu ✅

**Test na různých zařízeních:**
- [ ] Desktop (1920x1080)
- [ ] Tablet (768x1024)
- [ ] Mobile (375x667)

### ✅ 21. Browser Compatibility
- [ ] Chrome (latest) ✅
- [ ] Firefox (latest) ✅
- [ ] Safari (latest) ✅
- [ ] Edge (latest) ✅

---

## 📊 MONITORING

### ✅ 22. Error Logging
```bash
# Zkontroluj error log:
tail -f /path/to/wp-content/debug.log

# Mělo by být prázdné nebo jen info/warnings
# Žádné FATAL ERRORS ❌
```

### ✅ 23. Database Logging
```sql
-- Usage tracking:
SELECT provider, COUNT(*) as calls, SUM(tokens_used) as total_tokens
FROM wp_kafanek_brain_usage
GROUP BY provider;

-- Mělo by ukázat AI calls
```

### ✅ 24. User Experience
- [ ] Všechny buttons responzivní
- [ ] Loading indicators viditelné
- [ ] Success messages zobrazeny
- [ ] Error messages user-friendly
- [ ] Tooltips/nápověda funguje

---

## 🚨 STRESS TESTING

### ✅ 25. Load Test
- [ ] 10 současných AI requests
- [ ] Server nepadá ✅
- [ ] Response times přijatelné (< 30s)
- [ ] Memory usage stable
- [ ] Database connections OK

### ✅ 26. Chatbot Stress Test
- [ ] Otevři 5 chat windows (různé sessions)
- [ ] Pošli zprávy v každém
- [ ] Všechny odpovídají ✅
- [ ] Žádné error messages
- [ ] Conversations se ukládají správně

---

## ✅ FINAL CHECKLIST

### ✅ 27. Pre-Production Sign-off
- [ ] ✅ Všechny testy prošly (100%)
- [ ] ✅ Žádné kritické bugy
- [ ] ✅ Performance přijatelná
- [ ] ✅ Bezpečnost ověřena
- [ ] ✅ Dokumentace kompletní
- [ ] ✅ Rollback plán připraven
- [ ] ✅ Monitoring nastaven
- [ ] ✅ Team notifikován

---

## 📝 SIGN-OFF

**Tester:** _____________________  
**Datum:** _____________________  
**Staging URL:** _____________________  
**Výsledek:** ☐ PASS  ☐ FAIL  
**Poznámky:** _____________________

---

**🎯 Pokud všechny testy PASS → READY FOR PRODUCTION ✅**

**❌ Pokud jakýkoliv test FAIL → FIX & RE-TEST**
