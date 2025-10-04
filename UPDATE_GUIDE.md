# 🔄 KAFÁNEK BRAIN - PRŮVODCE AKTUALIZACEMI

Kompletní návod pro správu verzí a distribucí aktualizací pluginu.

---

## 📋 VERZOVÁNÍ

### Semantic Versioning (MAJOR.MINOR.PATCH)

```
Version: 1.2.0
         │ │ │
         │ │ └─ PATCH (0-9+) - Bug fixes, drobné změny
         │ └─── MINOR (0-9+) - Nové funkce (zpětně kompatibilní)
         └───── MAJOR (1-9+) - Breaking changes
```

**Příklady:**
- `1.2.0 → 1.2.1` - Oprava bug (cache fix)
- `1.2.1 → 1.3.0` - Nová funkce (Gemini API)
- `1.3.0 → 2.0.0` - Breaking change (nová architektura)

---

## 🎯 3 METODY AKTUALIZACÍ

### **Metoda 1: Manuální ZIP** (Současná)

**Workflow:**
```bash
1. Upravit Version v kafanek-brain.php
2. Vytvořit ZIP
3. Upload do WordPress
4. Activate
```

**Použití:** Jednorázové instalace, testování

---

### **Metoda 2: GitHub Auto-Updates** (Doporučeno)

Plugin je připraven s `class-updater.php` pro automatické aktualizace z GitHub.

#### Setup:

**Krok 1: Vytvořit GitHub repo**
```bash
cd /Users/kolibric/Desktop/KolibriMCPs/test-plugin/kafanek-brain
git init
git add .
git commit -m "Initial commit - v1.2.0"

# Vytvořit repo na github.com/kolibric/kafanek-brain
git remote add origin https://github.com/kolibric/kafanek-brain.git
git push -u origin main
```

**Krok 2: Vytvořit Release**
```bash
# Na GitHubu:
1. Releases → Create a new release
2. Tag: v1.2.0
3. Title: Version 1.2.0
4. Description:
   ## What's New
   - ✅ OpenAI GPT-3.5 integration
   - ✅ Claude 3.5 Sonnet support
   - ✅ Google Gemini support
   - ✅ Azure Speech-to-Text
   - ✅ WooCommerce AI features
   
5. Attach: kafanek-brain-v1.2.0.zip
6. Publish release
```

**Krok 3: Plugin automaticky detekuje**
```
WordPress Admin → Dashboard → Updates
→ Zobrazí: "Kafánkův Mozek 1.3.0 is available"
→ Klik "Update Now"
→ Automaticky stáhne z GitHub
→ Nainstaluje novou verzi
```

---

## 🔄 WORKFLOW PRO PŘIDÁNÍ FUNKCE

### Příklad: Přidat AI Image Generator

#### Krok 1: Plánování
```
Current: 1.2.0
New feature: AI Image Generator (DALL-E)
Next version: 1.3.0
```

#### Krok 2: Vývoj
```php
// includes/class-image-generator.php
class Kafanek_Image_Generator {
    public function generate_product_image($product_id) {
        // DALL-E API call
    }
}
```

#### Krok 3: Aktualizovat verzi
```php
// kafanek-brain.php
/**
 * Version: 1.3.0
 */
define('KAFANEK_BRAIN_VERSION', '1.3.0');
```

#### Krok 4: GitHub Release
```bash
git add .
git commit -m "Add AI Image Generator - v1.3.0"
git tag v1.3.0
git push origin main --tags

# Create GitHub Release s changelog
```

---

## 📊 VERSION TRACKING

### V databázi
```php
// Current version
get_option('kafanek_brain_version'); // "1.2.0"
```

---

## 🎯 ROADMAP PŘÍKLAD

### Verze 1.x (Stabilizace)
- ✅ 1.2.0 - Core features + Claude + Gemini + Azure
- 📅 1.3.0 - AI Image Generator
- 📅 1.4.0 - Bulk operations
- 📅 1.5.0 - Advanced analytics

### Verze 2.x (Rozšíření)
- 📅 2.0.0 - Multi-language support
- 📅 2.1.0 - REST API v2
- 📅 2.2.0 - Mobile app integration

---

## 💡 BEST PRACTICES

1. **Vždy testujte na staging** před produkcí
2. **Používejte version_compare()** pro upgrade hooks
3. **Zachovejte zpětnou kompatibilitu** v MINOR verzích
4. **Backup před každou aktualizací**
5. **Informujte uživatele** o důležitých změnách

---

**🎉 Nyní máte kompletní systém pro správu aktualizací!**
