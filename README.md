kafanek-brain/
├── kafanek-brain.php              # ⭐ Main plugin file (v1.2.0)
├── includes/
│   └── class-ai-engine.php        # 🧠 OpenAI integration
├── modules/
│   ├── core/
│   │   └── helpers.php            # 🔧 Fibonacci cache + Golden Ratio
│   └── woocommerce/
│       └── class-woocommerce-ai.php  # 🛒 WooCommerce AI features
├── admin/
│   ├── dashboard.php              # 📊 Statistics & logs
│   └── settings.php               # ⚙️ Configuration
├── assets/
│   ├── js/
│   │   ├── kafanek-brain.js       # Frontend JS
│   │   └── admin.js               # Admin JS
│   └── css/
│       └── kafanek-brain.css      # Styles
└── docs/
    ├── ARCHITECTURE.md            # 🏗️ Detailed architecture
    ├── QUICK_START.md             # 🚀 5-minute setup guide
    └── CONFIG_INSTRUCTIONS.md     # 🔐 Security configuration
```

---

## 🚀 RYCHLÝ START

### 1. Instalace

```bash
# Upload do WordPress:
wp-content/plugins/kafanek-brain/

# Nebo přes WP Admin:
Plugins → Add New → Upload Plugin
```

### 2. Aktivace

```bash
WordPress Admin → Plugins → Activate "Kafánkův Mozek"
```

### 3. Konfigurace

```bash
Kafánkův Mozek → Nastavení
→ Vložit OpenAI API Key
→ Zapnout WooCommerce modul
→ Test API Connection ✅
```

### 4. První použití

```bash
Products → Edit Product
→ 🧠 Kafánkův AI Asistent (sidebar)
→ 📝 Generovat popis
→ 💰 Optimalizovat cenu
→ Uložit produkt ✅
```

**Detailní návod:** Viz `QUICK_START.md`

---

## 🎯 POUŽITÍ

### Generování produktového popisu

1. Otevřete libovolný produkt v editoru
2. V pravém panelu najděte **"🧠 Kafánkův AI Asistent"**
3. Klikněte **"📝 Generovat popis"**
4. Počkejte 5-10 sekund
5. Popis se automaticky vyplní
6. **Uložte produkt!**

### Optimalizace ceny

1. V tom samém metaboxu
2. Klikněte **"💰 Optimalizovat cenu"**
3. AI spočítá optimální cenu pomocí Golden Ratio
4. Potvrďte změnu
5. **Uložte produkt!**

### AI Recommendations (Frontend)

Automaticky se zobrazují na product page:
- **"🤖 AI Doporučuje"** sekce
- 4 podobné produkty ze stejné kategorie
- Responsive grid layout

---

## 🔌 REST API

### Status Endpoint

```bash
GET /wp-json/kafanek-brain/v1/status
```

**Response:**
```json
{
  "status": "active",
  "version": "1.2.0",
  "modules": {
    "helpers": true,
    "WooCommerce AI": true
  },
  "ai_ready": true
}
```

### Generate Content

```bash
POST /wp-json/kafanek-brain/v1/generate
Headers: X-Kafanek-API-Key: [your-key]
```

**Detailní API dokumentace:** Viz `ARCHITECTURE.md` → API Dokumentace

---

## 🗄️ DATABÁZE

### Tabulka: `wp_kafanek_ai_logs`

Automaticky vytvoří při aktivaci:

```sql
CREATE TABLE wp_kafanek_ai_logs (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  request_type varchar(50) NOT NULL,
  request_data longtext,
  response_data longtext,
  tokens_used int(11),
  created_at datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
);
```

**Purpose:** Usage tracking, cost monitoring, debugging

---

## 🔐 BEZPEČNOST

### ✅ Implementováno:

- **Nonce Verification** - Všechny AJAX requesty
- **Capability Checks** - `manage_options`, `edit_products`
- **Input Sanitization** - `sanitize_text_field()`, `intval()`
- **Output Escaping** - `esc_html()`, `esc_attr()`, `esc_url()`
- **API Key Storage** - Options table (nebo wp-config.php)

### 🔒 Doporučení:

1. **Pro produkci:** Uložte API klíč do `wp-config.php`
2. **Rotujte klíče:** Každých 90 dní
3. **Nastavte limity:** OpenAI Platform → Usage limits
4. **Necommitujte:** API klíč nikdy do GIT!

**Bezpečnostní guide:** Viz `CONFIG_INSTRUCTIONS.md`

---

## ⚡ PERFORMANCE

### Caching Strategy

**Fibonacci Cache Levels** (z `helpers.php`):

```php
'instant'  => 1 sec
'quick'    => 1 sec
'short'    => 2 sec
'medium'   => 3 sec
'standard' => 5 sec
'long'     => 8 sec
'extended' => 13 sec
'hourly'   => 21 min   # AI responses
'daily'    => 34 hours # Recommendations
```

**Úspora:** ~80% API volání díky cache!

### Database Optimization

- ✅ Indexes na `request_type`, `created_at`
- ✅ Lazy loading modulů
- ✅ Optimalizované WP_Query

---

## 💰 NÁKLADY

### OpenAI GPT-3.5-turbo (Doporučeno):

| Operace | Cena za kus | 100 kusů | 1000 kusů |
|---------|-------------|----------|-----------|
| Product description | ~0.02 Kč | 3.50 Kč | 35 Kč |
| Price optimization | ZDARMA | ZDARMA | ZDARMA |
| Recommendations | ZDARMA | ZDARMA | ZDARMA |

**Tip:** Cache znamená, že opakované requesty jsou **ZDARMA**!

---

## 📊 MONITORING

### Dashboard Metrics

```
Kafánkův Mozek → Dashboard
├── 📊 Celkem požadavků: 47
├── 🎯 Použité tokeny: 12,345
├── ✨ Golden Ratio: φ = 1.618
└── 📝 Poslední AI požadavky (10)
```

### OpenAI Platform

```
https://platform.openai.com/usage
→ Daily requests
→ Total tokens
→ Costs ($)
```

---

## 🏗️ ARCHITEKTURA

### Design Principles

1. **Simplicity First** - Čitelný, udržovatelný kód
2. **Modular Design** - Nezávislé moduly
3. **WordPress Native** - Hooks, filters, REST API
4. **Security First** - Nonce, capabilities, sanitization
5. **Performance** - Caching, lazy loading

### Klíčové komponenty:

| Component | File | Purpose |
|-----------|------|---------|
| **Core Plugin** | `kafanek-brain.php` | Singleton, module loader, REST API |
| **AI Engine** | `includes/class-ai-engine.php` | OpenAI integration, caching |
| **WooCommerce AI** | `modules/woocommerce/class-woocommerce-ai.php` | Product AI features |
| **Core Helpers** | `modules/core/helpers.php` | Fibonacci cache, Golden Ratio |
| **Dashboard** | `admin/dashboard.php` | Statistics, logs |
| **Settings** | `admin/settings.php` | Configuration UI |

**Detailní architektura:** Viz `ARCHITECTURE.md` (678 řádků)

---

## 🧪 TESTOVÁNÍ

### Test Checklist

```bash
[ ] Plugin aktivace bez chyb
[ ] Dashboard zobrazuje metriky
[ ] Settings page načítá
[ ] REST API /status vrací 200
[ ] WooCommerce AI metabox se zobrazí
[ ] Generate Description funguje (s API key)
[ ] Optimize Price funguje (bez API key)
[ ] AI Recommendations na frontendu
[ ] Cache funguje (transients)
[ ] Error logging do debug.log
```

### Manuální testy:

```bash
# Test 1: REST API
curl https://mykolibri-academy.cz/wp-json/kafanek-brain/v1/status

# Test 2: Generate Description
Products → Edit → AI Asistent → Generovat popis

# Test 3: Price Optimization
Products → Edit → AI Asistent → Optimalizovat cenu
