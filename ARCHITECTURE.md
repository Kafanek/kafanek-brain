# 🏗️ KAFÁNEK BRAIN - ARCHITEKTURA v1.2.0

**Verze:** 1.2.0  
**Datum:** 2025-10-03  
**Architektura:** Zjednodušená funkční implementace  

---

## 📐 PŘEHLED ARCHITEKTURY

### Design Principles
1. **Simplicity First** - Jednoduchý, čitelný, udržovatelný kód
2. **Modular Design** - Nezávislé moduly s jasným rozhraním
3. **WordPress Native** - Používá WordPress standards (hooks, filters, REST API)
4. **Security First** - Nonce verification, capability checks, sanitization
5. **Performance** - Transient caching, lazy loading, optimalizované dotazy

---

## 🗂️ STRUKTURA SOUBORŮ

```
kafanek-brain/
│
├── kafanek-brain.php                    # [CORE] Main plugin file
│   ├── Class: Kafanek_Brain_Plugin
│   ├── Singleton pattern
│   ├── Module loader
│   ├── REST API registration
│   ├── AJAX handlers
│   └── Database schema
│
├── includes/                            # [CORE LOGIC]
│   └── class-ai-engine.php              # AI Engine
│       ├── Class: Kafanek_AI_Engine
│       ├── OpenAI API integration
│       ├── Text generation
│       ├── Product description generator
│       ├── SEO content analyzer
│       └── Usage logging
│
├── modules/                             # [FEATURES]
│   ├── core/
│   │   └── helpers.php                  # Core helper functions (v1.1.0)
│   │       ├── Class: Kafanek_Core_Helpers
│   │       ├── Fibonacci caching (9 levels)
│   │       ├── Golden Ratio calculations
│   │       ├── Cosine similarity (ML)
│   │       ├── Feature extraction
│   │       └── Retry with Fibonacci delays
│   │
│   └── woocommerce/
│       └── class-woocommerce-ai.php     # WooCommerce integration
│           ├── Class: Kafanek_WooCommerce_AI
│           ├── AI metabox in product editor
│           ├── Generate description AJAX
│           ├── Optimize price (Golden Ratio)
│           ├── Product recommendations
│           └── Frontend widgets
│
├── admin/                               # [ADMIN UI]
│   ├── dashboard.php                    # Main dashboard
│   │   ├── Statistics (requests, tokens)
│   │   ├── Recent AI logs
│   │   └── Quick actions
│   │
│   └── settings.php                     # Settings page
│       ├── API key configuration
│       ├── Module toggles
│       ├── System status
│       └── API test button
│
└── assets/                              # [FRONTEND]
    ├── js/
    │   ├── kafanek-brain.js             # Frontend JavaScript
    │   │   ├── AI chat widget
    │   │   ├── Recommendations UI
    │   │   └── AJAX handlers
    │   │
    │   └── admin.js                     # Admin JavaScript
    │       ├── API testing
    │       ├── Cache management
    │       └── Module toggles
    │
    └── css/
        └── kafanek-brain.css            # Styles
            ├── AI recommendations grid
            ├── Chat widget
            └── Responsive design
```

---

## 🔄 DATOVÝ TOK

### 1. Plugin Initialization Flow

```
WordPress Loads
    ↓
plugins_loaded hook (priority 5)
    ↓
Kafanek_Brain_Plugin::get_instance()
    ↓
__construct()
    ├── register_activation_hook()
    ├── register_deactivation_hook()
    └── add_action('plugins_loaded', 'init', 5)
    ↓
init()
    ├── load_core_helpers()           # Priority: First
    │   └── modules/core/helpers.php
    ├── load_modules()
    │   ├── WooCommerce AI (if WC active)
    │   ├── Elementor Widgets (if Elementor active)
    │   └── Neural Network (if enabled)
    └── init_ai_engine()
        └── includes/class-ai-engine.php
    ↓
Plugin Ready ✅
```

### 2. AI Product Description Generation Flow

```
User clicks "📝 Generovat popis" in Product Editor
    ↓
JavaScript (metabox script)
    ├── AJAX POST → ajaxurl
    ├── action: 'kafanek_woo_generate_description'
    ├── product_id: [ID]
    └── nonce: [security token]
    ↓
Kafanek_WooCommerce_AI::ajax_generate_description()
    ├── check_ajax_referer('kafanek_ai_nonce')  # Security
    ├── intval($_POST['product_id'])            # Sanitization
    └── $this->ai_engine->generate_product_description($product_id)
    ↓
Kafanek_AI_Engine::generate_product_description()
    ├── wc_get_product($product_id)
    ├── Build prompt with:
    │   ├── Product name
    │   ├── Categories
    │   └── Price
    ├── $description = generate_text($prompt)
    │   ├── Check cache (md5 hash)
    │   ├── If cached → return from transient
    │   └── If not cached:
    │       ├── wp_remote_post(OpenAI API)
    │       ├── Parse response
    │       ├── set_transient (1 hour)
    │       └── log_usage()
    ├── $short = generate_text($short_prompt)
    └── return ['description' => ..., 'short_description' => ...]
    ↓
JavaScript receives response
    ├── tinyMCE.get('content').setContent(response.data.description)
    ├── $('#excerpt').val(response.data.short_description)
    └── Show success message
    ↓
User saves product ✅
```

### 3. Price Optimization Flow

```
User clicks "💰 Optimalizovat cenu"
    ↓
Kafanek_WooCommerce_AI::ajax_optimize_price()
    ├── wc_get_product($product_id)
    ├── $current_price = $product->get_price()
    ├── Golden Ratio Calculation:
    │   ├── $golden_ratio = 1.618
    │   ├── $optimal = round($current_price * $golden_ratio / 10) * 10
    │   └── Safety check (max 2x current)
    └── return ['current' => ..., 'optimal' => ..., 'change' => ...]
    ↓
JavaScript
    ├── Display recommendation
    ├── if (confirm()) → update price field
    └── User saves ✅
```

### 4. REST API Status Flow

```
GET /wp-json/kafanek-brain/v1/status
    ↓
Kafanek_Brain_Plugin::get_status()
    ├── Check modules loaded
    ├── Check API key configured
    └── return JSON:
        {
          "status": "active",
          "version": "1.2.0",
          "modules": {...},
          "ai_ready": true/false
        }
```

---

## 🔌 API DOKUMENTACE

### REST API Endpoints

#### 1. Status Endpoint
```
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

**Permission:** Public (všichni)

---

#### 2. Generate Content Endpoint
```
POST /wp-json/kafanek-brain/v1/generate
```

**Headers:**
```
X-Kafanek-API-Key: [your-api-key]
```

**Request Body:**
```json
{
  "prompt": "Generate product description...",
  "options": {
    "temperature": 0.7,
    "max_tokens": 500
  }
}
```

**Response:**
```json
{
  "success": true,
  "text": "Generated content..."
}
```

**Permission:** API Key required

---

#### 3. Recommendations Endpoint
```
GET /wp-json/kafanek-brain/v1/recommendations?product_id=123
```

**Response:**
```json
{
  "recommendations": [45, 67, 89, 123]
}
```

**Permission:** Public

---

### AJAX Handlers

#### 1. Generate Product Description
```javascript
jQuery.post(ajaxurl, {
  action: 'kafanek_woo_generate_description',
  product_id: 123,
  nonce: wp_nonce
})
```

**Handler:** `Kafanek_WooCommerce_AI::ajax_generate_description()`

---

#### 2. Optimize Price
```javascript
jQuery.post(ajaxurl, {
  action: 'kafanek_woo_optimize_price',
  product_id: 123,
  nonce: wp_nonce
})
```

**Handler:** `Kafanek_WooCommerce_AI::ajax_optimize_price()`

---

#### 3. AI Chat Request
```javascript
jQuery.post(ajaxurl, {
  action: 'kafanek_ai_request',
  ai_action: 'chat',
  data: { message: 'Hello AI' },
  nonce: wp_nonce
})
```

**Handler:** `Kafanek_Brain_Plugin::handle_ai_request()`

---

## 🗄️ DATABÁZOVÁ STRUKTURA

### Table: `wp_kafanek_ai_logs`

```sql
CREATE TABLE wp_kafanek_ai_logs (
  id bigint(20) NOT NULL AUTO_INCREMENT,
  request_type varchar(50) NOT NULL,        -- 'openai', 'chat', etc.
  request_data longtext,                    -- JSON: prompt, params
  response_data longtext,                   -- JSON: response
  tokens_used int(11),                      -- Cost tracking
  created_at datetime DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (id),
  KEY request_type (request_type),
  KEY created_at (created_at)
);
```

**Purpose:**
- Usage tracking
- Cost monitoring
- Debugging
- Analytics

**Queries:**
```php
// Total requests
$wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}kafanek_ai_logs");

// Total tokens
$wpdb->get_var("SELECT SUM(tokens_used) FROM {$wpdb->prefix}kafanek_ai_logs");

// Recent logs
$wpdb->get_results("SELECT * FROM {$wpdb->prefix}kafanek_ai_logs ORDER BY created_at DESC LIMIT 10");
```

---

### WordPress Options

```php
// API Key (sensitive!)
get_option('kafanek_brain_api_key');  // string

// Active modules
get_option('kafanek_brain_modules');  // array
[
  'woocommerce' => true,
  'elementor' => true,
  'neural' => false
]
```

---

### Transients (Cache)

```php
// AI response cache (1 hour)
$cache_key = 'kafanek_ai_' . md5($prompt . json_encode($options));
set_transient($cache_key, $result, HOUR_IN_SECONDS);

// Product recommendations (6 hours)
$cache_key = 'kafanek_rec_' . $product_id;
set_transient($cache_key, $recommendations, 6 * HOUR_IN_SECONDS);
```

---

## 🔐 BEZPEČNOSTNÍ MODEL

### 1. Nonce Verification

```php
// AJAX handlers ALWAYS verify nonce
check_ajax_referer('kafanek_ai_nonce', 'nonce');
```

**Ochrana proti:** CSRF attacks

---

### 2. Capability Checks

```php
// Admin settings
if (!current_user_can('manage_options')) {
    return;
}

// Product editing
if (!current_user_can('edit_products')) {
    wp_send_json_error('Insufficient permissions');
}
```

**Ochrana proti:** Unauthorized access

---

### 3. Input Sanitization

```php
// API key
update_option('kafanek_brain_api_key', sanitize_text_field($_POST['api_key']));

// Product ID
$product_id = intval($_POST['product_id']);
```

**Ochrana proti:** SQL injection, XSS

---

### 4. Output Escaping

```php
// Admin UI
echo esc_html($request->created_at);
echo esc_attr($api_key);
echo esc_url($permalink);
```

**Ochrana proti:** XSS

---

### 5. API Key Storage

**Options:**

1. **Database (default):**
   ```php
   update_option('kafanek_brain_api_key', $key);
   ```

2. **wp-config.php (recommended for production):**
   ```php
   define('KAFANEK_OPENAI_API_KEY', 'sk-...');
   ```

3. **.env file (advanced):**
   ```bash
   KAFANEK_OPENAI_API_KEY=sk-...
   ```

**Best Practice:** Use wp-config.php or .env for production

---

## ⚡ PERFORMANCE OPTIMALIZACE

### 1. Caching Strategy

**Transient Cache:**
```php
// Cache levels from helpers.php (v1.1.0)
$fibonacci_cache_levels = [
    'instant' => 1,      // 1 second
    'quick' => 1,        // 1 second
    'short' => 2,        // 2 seconds
    'medium' => 3,       // 3 seconds
    'standard' => 5,     // 5 seconds
    'long' => 8,         // 8 seconds
    'extended' => 13,    // 13 seconds
    'hourly' => 1260,    // 21 minutes
    'daily' => 122400    // 34 hours
];
```

**Usage:**
```php
kafanek_fibonacci_cache($key, $callback, 'hourly');
```

---

### 2. Lazy Loading

```php
// Modules load only when needed
if (class_exists('WooCommerce')) {
    $this->load_module('woocommerce/class-woocommerce-ai.php');
}
```

---

### 3. Database Optimization

```sql
-- Indexes on frequently queried columns
KEY request_type (request_type),
KEY created_at (created_at)
```

---

### 4. Frontend Optimization

**CSS:**
- Minified in production
- Critical CSS inline
- Lazy load non-critical

**JavaScript:**
- Loaded in footer
- jQuery dependency
- Conditional loading (only where needed)

---

## 🧩 INTEGRAČNÍ BODY

### WordPress Hooks

**Actions:**
```php
add_action('plugins_loaded', [$this, 'init'], 5);
add_action('init', [$this, 'register_post_types']);
add_action('rest_api_init', [$this, 'register_rest_routes']);
add_action('admin_menu', [$this, 'add_admin_menu']);
add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
add_action('admin_enqueue_scripts', [$this, 'admin_scripts']);

// AJAX
add_action('wp_ajax_kafanek_ai_request', [$this, 'handle_ai_request']);
add_action('wp_ajax_nopriv_kafanek_ai_request', [$this, 'handle_ai_request']);
```

**Filters:**
```php
// Currently none, but available for extensions
apply_filters('kafanek_ai_prompt', $prompt, $product);
apply_filters('kafanek_optimal_price', $optimal, $current, $product);
```

---

### WooCommerce Integration

**Hooks:**
```php
add_action('woocommerce_single_product_summary', [$this, 'show_ai_recommendations'], 35);
add_action('woocommerce_product_options_pricing', [$this, 'add_ai_price_field']);
add_action('woocommerce_process_product_meta', [$this, 'save_ai_fields']);
add_action('add_meta_boxes', [$this, 'add_ai_metabox']);
```

---

### Elementor Integration

**Planned (v1.3.0):**
```php
add_action('elementor/widgets/register', [$this, 'register_widgets']);
```

---

## 📊 MONITORING & LOGGING

### Debug Mode

```php
if (defined('WP_DEBUG') && WP_DEBUG) {
    error_log('Kafanek Brain: Core helpers loaded');
}
```

**Location:** `/wp-content/debug.log`

---

### Usage Tracking

```php
private function log_usage($prompt, $response, $usage) {
    global $wpdb;
    
    $wpdb->insert($wpdb->prefix . 'kafanek_ai_logs', [
        'request_type' => 'openai',
        'request_data' => json_encode(['prompt' => $prompt]),
        'response_data' => json_encode(['response' => $response]),
        'tokens_used' => $usage['total_tokens'] ?? 0,
        'created_at' => current_time('mysql')
    ]);
}
```

---

### Dashboard Metrics

**Tracked:**
- 📊 Total requests
- 🎯 Total tokens used
- ⏱️ Response times (planned)
- 💰 Estimated costs (planned)

---

## 🚀 DEPLOYMENT CHECKLIST

```bash
[ ] Kód commitnut do GIT (bez API klíče!)
[ ] Database backup vytvořen
[ ] API klíč nakonfigurován
[ ] WP_DEBUG = false
[ ] SSL certifikát aktivní
[ ] Cache plugin kompatibilní
[ ] Cron jobs funkční
[ ] Error logging nastaven
[ ] Performance test dokončen
[ ] Security audit proveden
[ ] Dokumentace aktuální
```

---

## 🔮 PLÁNOVANÉ FUNKCE (v1.3.0+)

### Připravované:
- ✨ Elementor AI Widgets
- 🧠 Neural Network module
- 📈 Advanced analytics dashboard
- 🎨 AI image generation (DALL-E)
- 🌍 Multi-language support
- 📱 Mobile app API
- 🔄 Bulk product processing
- 💬 Advanced chatbot

---

## 📚 REFERENCE

### Dependencies
- **WordPress:** 6.0+
- **PHP:** 7.4+
- **WooCommerce:** 7.0+ (optional)
- **Elementor:** 3.0+ (optional)
- **OpenAI API:** GPT-3.5/4

### External APIs
- **OpenAI:** https://api.openai.com/v1/chat/completions

### Standards
- **WordPress Coding Standards:** https://developer.wordpress.org/coding-standards/
- **PSR-12:** https://www.php-fig.org/psr/psr-12/

---

**Architektura navržena pro:** Jednoduchost, Bezpečnost, Performance, Škálovatelnost

**Autor:** Cascade AI + Kolibri Academy  
**Licence:** GPL v2 or later  
**Repository:** (internal)
