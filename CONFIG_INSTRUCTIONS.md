# 🔐 BEZPEČNÁ KONFIGURACE API KLÍČE

## Možnost 1: Přes WordPress Admin (DOPORUČENO)

### Krok za krokem:

1. **Přihlaste se do WordPress Admin**
   ```
   URL: https://mykolibri-academy.cz/wp-admin
   ```

2. **Navigujte do Kafánkův Mozek → Nastavení**
   ```
   Menu: Kafánkův Mozek → Nastavení
   ```

3. **Vložte API klíč**
   ```
   Pole: "OpenAI API Key"
   Vložte: [váš API klíč]
   ```

4. **Uložte nastavení**
   ```
   Tlačítko: "Uložit nastavení"
   ```

5. **Test API**
   ```
   Tlačítko: "Test OpenAI Connection"
   Výsledek: ✅ API funguje!
   ```

---

## Možnost 2: Přes wp-config.php (PRO PRODUKCI)

### Pro vyšší bezpečnost přidejte do `wp-config.php`:

```php
// Kafanek Brain API Configuration
define('KAFANEK_OPENAI_API_KEY', 'váš-api-klíč-zde');
```

### Pak upravte `includes/class-ai-engine.php`:

```php
public function __construct() {
    // Check for constant first (more secure)
    if (defined('KAFANEK_OPENAI_API_KEY')) {
        $this->api_key = KAFANEK_OPENAI_API_KEY;
    } else {
        $this->api_key = get_option('kafanek_brain_api_key');
    }
    
    // ... rest of constructor
}
```

**Výhody:**
- ✅ API klíč není v databázi
- ✅ Není viditelný v admin UI
- ✅ Bezpečnější pro production

---

## Možnost 3: Přes .env soubor (ADVANCED)

### 1. Vytvořte `.env` v root WordPress:

```bash
KAFANEK_OPENAI_API_KEY=váš-api-klíč
```

### 2. Přidejte do `.gitignore`:

```
.env
```

### 3. Načtěte pomocí vlp (Dotenv library):

```php
// V kafanek-brain.php před definicí třídy:
if (file_exists(ABSPATH . '.env')) {
    $lines = file(ABSPATH . '.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        putenv(sprintf('%s=%s', trim($name), trim($value)));
    }
}
```

---

## 🔒 BEZPEČNOSTNÍ DOPORUČENÍ

### ✅ DO:
1. **Nikdy necommitujte API klíč do GIT**
   ```bash
   # Přidejte do .gitignore:
   wp-config.php
   .env
   ```

2. **Rotujte API klíče pravidelně**
   - Doporučeno: každých 90 dní
   - OpenAI Platform → API Keys → Rotate

3. **Používejte různé klíče pro dev/staging/prod**
   - Development: samostatný klíč s limitem
   - Production: produkční klíč s monitoringem

4. **Nastavte usage limity**
   - OpenAI Platform → Usage limits
   - Měsíční limit: např. $50

### ❌ DON'T:
1. **Neukládejte API klíč do kódu**
2. **Nesdílejte screenshots s API klíčem**
3. **Neposílejte API klíč přes email/chat**
4. **Nepublikujte API klíč na GitHub**

---

## 🧪 OVĚŘENÍ KONFIGURACE

### Test 1: Zkontrolujte, že klíč je uložený

```php
// V WordPress Admin → Tools → Site Health → Info
// Nebo přes WP-CLI:
wp option get kafanek_brain_api_key
```

### Test 2: Test API volání

```bash
# V WordPress Admin:
Kafánkův Mozek → Nastavení → Test OpenAI Connection
```

**Očekávaný výsledek:**
```
✅ API funguje správně!
```

### Test 3: Vygenerujte testovací popis

```bash
# V Product editoru:
Products → Edit Product → 🧠 Kafánkův AI Asistent → 📝 Generovat popis
```

---

## 📊 MONITORING USAGE

### OpenAI Platform Dashboard:
```
https://platform.openai.com/usage
```

**Sledujte:**
- Daily requests
- Total tokens
- Costs ($)
- Rate limits

### WordPress Dashboard:
```
Kafánkův Mozek → Dashboard → 🎯 Použité tokeny
```

---

## 🔄 ROTACE API KLÍČE

### Když potřebujete rotovat klíč:

1. **Vytvořte nový klíč na OpenAI**
   ```
   Platform → API Keys → Create new secret key
   ```

2. **Aktualizujte v WordPress**
   ```
   Nastavení → OpenAI API Key → [nový klíč] → Uložit
   ```

3. **Otestujte**
   ```
   Test OpenAI Connection → ✅
   ```

4. **Revokujte starý klíč**
   ```
   OpenAI Platform → API Keys → [starý klíč] → Revoke
   ```

---

## ⚠️ V PŘÍPADĚ ÚNIKU KLÍČE

### OKAMŽITĚ:

1. **Revokujte kompromitovaný klíč**
   ```
   OpenAI Platform → API Keys → Revoke
   ```

2. **Vygenerujte nový**
   ```
   Create new secret key
   ```

3. **Aktualizujte všude**
   ```
   WordPress Admin + wp-config.php + .env
   ```

4. **Zkontrolujte usage**
   ```
   OpenAI Platform → Usage → Check for suspicious activity
   ```

5. **Nastavte nové limity**
   ```
   Usage limits → Lower než předtím
   ```

---

**🔐 Bezpečnost je priorita! Chraňte svůj API klíč jako heslo.**
