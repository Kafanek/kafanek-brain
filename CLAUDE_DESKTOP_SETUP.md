# 🤖 CLAUDE DESKTOP + KAFÁNEK BRAIN INTEGRACE

Kompletní návod pro přímé napojení Claude Desktop na váš Kafánek Brain WordPress plugin.

---

## 🎯 CO TO UMOŽŇUJE

S touto integrací můžete přímo z Claude Desktop:
- ✅ Generovat produktové popisy pro WooCommerce
- ✅ Optimalizovat ceny pomocí Golden Ratio
- ✅ Analyzovat produkty a doporučovat vylepšení
- ✅ Spravovat celý eshop přes konverzaci s Claude

---

## 🔧 METODA 1: Přes WordPress REST API (Doporučeno)

### Krok 1: Nastavit API Key v pluginu

```
WordPress Admin → Kafánkův Mozek → Nastavení
→ AI Provider: Claude (Anthropic)
→ Claude API Key: sk-ant-xxxxx
→ Uložit nastavení
```

### Krok 2: Vytvořit WordPress Application Password

```bash
WordPress Admin → Uživatelé → Profil → Application Passwords
→ Nový název: "Claude Desktop MCP"
→ Add New Application Password
→ Zkopírovat heslo (např: "xxxx xxxx xxxx xxxx")
```

### Krok 3: Upravit Claude Desktop config

**Umístění:** 
- macOS: `~/Library/Application Support/Claude/claude_desktop_config.json`
- Windows: `%APPDATA%\Claude\claude_desktop_config.json`

**Obsah:**
```json
{
  "mcpServers": {
    "kafanek-brain": {
      "command": "node",
      "args": [
        "/Users/kolibric/Desktop/KolibriMCPs/FINAL_MCP_FILES/kolibreus-router-simple.js"
      ],
      "env": {
        "WORDPRESS_URL": "https://mykolibri-academy.cz",
        "WORDPRESS_USERNAME": "admin",
        "WORDPRESS_APP_PASSWORD": "xxxx xxxx xxxx xxxx",
        "KAFANEK_BRAIN_ENABLED": "true"
      }
    }
  }
}
```

### Krok 4: Restartovat Claude Desktop

```bash
# macOS
killall Claude
open -a Claude

# Windows
Ctrl+Q v Claude Desktop
Pak znovu otevřít
```

### Krok 5: Testovat připojení

V Claude Desktop napište:
```
"Použij nástroj kafanek_brain a vygeneruj popis pro produkt ID 123"
```

Claude by měl:
1. Detekovat MCP server "kafanek-brain"
2. Volat REST API endpoint
3. Vrátit vygenerovaný popis

---

## 🔧 METODA 2: Přímé Claude API v pluginu

Pokud chcete použít Claude **místo OpenAI** v samotném pluginu:

### Krok 1: Získat Claude API klíč

```
https://console.anthropic.com/
→ API Keys → Create Key
→ Zkopírovat: sk-ant-xxxxx
```

### Krok 2: Nastavit v pluginu

```
WordPress Admin → Kafánkův Mozek → Nastavení
→ AI Provider: Anthropic (Claude)
→ Claude API Key: sk-ant-xxxxx
→ Uložit nastavení
```

### Krok 3: Vygenerovat popis

```
Products → Edit Product → Generovat popis
→ Nyní používá Claude 3.5 Sonnet!
```

---

## 🔧 METODA 3: Hybrid (Nejlepší z obou světů)

```
┌─────────────────────────────────────────────┐
│ Claude Desktop                              │
│   ↓ (MCP Server)                            │
│ WordPress REST API                          │
│   ↓                                         │
│ Kafánek Brain Plugin                        │
│   ↓ (vybere providera)                      │
│ OpenAI GPT-3.5-turbo  NEBO  Claude Sonnet  │
└─────────────────────────────────────────────┘
```

**Výhoda:** Můžete přepínat mezi OpenAI a Claude podle potřeby.

---

## 📊 POROVNÁNÍ OPENAI vs CLAUDE

| Kritérium | OpenAI GPT-3.5 | Claude 3.5 Sonnet |
|-----------|----------------|-------------------|
| **Cena** | $0.50 / 1M input | $3.00 / 1M input |
| **Kvalita** | Dobrá | Vynikající |
| **Rychlost** | ~2-5s | ~3-8s |
| **Context window** | 16K tokenů | 200K tokenů |
| **Český jazyk** | Dobrý | Vynikající |
| **Kreativita** | Střední | Vysoká |

**Doporučení pro Kafánek Brain:**
- **Hromadné generování** → OpenAI (levnější)
- **Kvalitní unique content** → Claude (lepší)
- **Dlouhé produkty** → Claude (větší context)

---

## 🎯 PŘÍKLADY POUŽITÍ

### 1. Generovat popis z Claude Desktop

```
Prompt: "Použij Kafánek Brain a vygeneruj popis pro produkt 'AI Senior' (ID 123)"

Claude odpověď:
✅ Spojil jsem se s Kafánek Brain
✅ Vygeneroval popis pomocí Claude 3.5 Sonnet
✅ Zde je výsledek:

"🚀 Ovládněte AI za 4 týdny! Kurz AI Senior vám otevře..."
```

### 2. Optimalizovat ceny Golden Ratio

```
Prompt: "Optimalizuj ceny všech produktů v kategorii 'Kurzy' pomocí Golden Ratio"

Claude:
✅ Načetl 3 produkty
✅ Aplikoval φ = 1.618
✅ Produkty:
   - AI Senior: 699 Kč → 1,131 Kč (+62%)
   - Demokracie: 240 Kč → 388 Kč (+62%)
```

### 3. Analyzovat celý eshop

```
Prompt: "Analyzuj všechny produkty a navrhni vylepšení"

Claude:
📊 Analýza 3 produktů:

Doporučení:
1. AI Senior - chybí call-to-action ❌
2. Demokracie - dobrý popis ✅
3. Občan - přidat benefits ⚠️
```

---

## 🔐 BEZPEČNOST

### wp-config.php metoda (Produkce)

```php
// wp-config.php
define('KAFANEK_CLAUDE_API_KEY', 'sk-ant-xxxxx');
define('KAFANEK_OPENAI_API_KEY', 'sk-xxxxx');
```

**Výhody:**
- ✅ API klíče nejsou v databázi
- ✅ Nejsou viditelné v admin UI
- ✅ Nelze exportovat přes backup

---

## 🐛 TROUBLESHOOTING

### Claude Desktop nevidí MCP server

```bash
# Zkontrolovat config
cat ~/Library/Application\ Support/Claude/claude_desktop_config.json

# Zkontrolovat logy
tail -f ~/Library/Logs/Claude/mcp*.log
```

### REST API vrací 401

```bash
# Testovat REST API
curl https://mykolibri-academy.cz/wp-json/kafanek-brain/v1/status

# Testovat s authentication
curl -u admin:APP_PASSWORD \
  https://mykolibri-academy.cz/wp-json/kafanek-brain/v1/status
```

### Claude API chyba

```php
// Zkontrolovat v WordPress debug.log
tail -f /path/to/wp-content/debug.log
```

---

## 💰 NÁKLADY

### Scénář: 100 produktových popisů měsíčně

**OpenAI GPT-3.5:**
```
100 × 500 tokenů input = 50K tokenů
100 × 300 tokenů output = 30K tokenů
Celkem: 80K tokenů
Cena: ~$0.04 (1 Kč)
```

**Claude 3.5 Sonnet:**
```
100 × 500 tokenů input = 50K tokenů
100 × 300 tokenů output = 30K tokenů
Celkem: 80K tokenů
Cena: ~$0.39 (9 Kč)
```

**Doporučení:** Mix obou podle potřeby.

---

## 📚 DALŠÍ ZDROJE

- [Claude API Docs](https://docs.anthropic.com/)
- [MCP Protocol](https://modelcontextprotocol.io/)
- [Kafánek Brain Docs](./ARCHITECTURE.md)

---

**🎉 Nyní máte Claude Desktop přímo napojený na Kafánek Brain!**
