# 🚀 GitHub Auto-Update Setup

**Pro automatickou aktualizaci Kafánkův Mozek na webu mykolibri-academy.cz**

---

## 📋 KROK ZA KROKEM

### 1️⃣ Vytvořit GitHub Repository

```bash
1. Jdi na: https://github.com/new
2. Repository name: kafanek-brain
3. Description: "AI WordPress Brain with Golden Ratio"
4. Public ✅ (pro auto-update MUSÍ být public)
5. Create repository
```

---

### 2️⃣ Nahrát kód na GitHub

```bash
cd /Users/kolibric/Desktop/KolibriMCPs/test-plugin/kafanek-brain

# Git init (pokud není)
git init

# Přidat všechny soubory
git add .

# Commit
git commit -m "v1.2.1 - Added shortcodes system, improved chatbot design"

# Připojit k GitHub
git remote add origin https://github.com/kolibric/kafanek-brain.git

# Push
git branch -M main
git push -u origin main
```

**První push vyžaduje GitHub autentifikaci:**
- Username: `kolibric`
- Password: GitHub Personal Access Token

**Vytvořit token:**
1. GitHub → Settings → Developer settings → Personal access tokens → Tokens (classic)
2. Generate new token (classic)
3. Název: "Kafanek Brain Updates"
4. Scopes: ✅ `repo` (úplný přístup)
5. Generate token
6. Zkopírovat token (ukaž se jen jednou!)
7. Použít jako password při git push

---

### 3️⃣ Vytvořit GitHub Release

**Na GitHubu:**

```
1. Jdi do repository: github.com/kolibric/kafanek-brain
2. Klikni: Releases (vpravo)
3. "Create a new release"
```

**Vyplň:**

**Tag version:** `v1.2.1`  
**Release title:** `Kafánek Brain v1.2.1 - Shortcodes Update`

**Description:**
```markdown
## ✨ Co je nového v 1.2.1

### Nové funkce
- ✅ **5 Shortcodů** pro snadné použití AI funkcí
  - `[kafanek_chat]` - AI chatbot widget
  - `[kafanek_content]` - AI generovaný obsah
  - `[kafanek_price]` - Dynamické ceny (Golden Ratio)
  - `[kafanek_recommendation]` - AI doporučení produktů
  - `[kafanek_neural_insight]` - Neural network analýza

### Vylepšení
- ✅ Vylepšený chatbot design (#697077 barva)
- ✅ Větší tlačítko (70px) s pulsující animací
- ✅ Lepší umístění (30px od okrajů)

### Dokumentace
- ✅ SHORTCODES_GUIDE.md - Kompletní průvodce
- ✅ WHATS_NEW.md - Changelog
- ✅ COLOR_SCHEMES.md - Barevná schémata

### Opravy
- ✅ Odstranění duplikátu načítání shortcodes

---

## 📦 Instalace

1. Stáhnout ZIP níže
2. WordPress Admin → Pluginy → Přidat nový
3. Nahrát ZIP
4. Aktivovat

## 📊 Statistiky

- Verze: 1.2.1
- Core třídy: 8
- Shortcodes: 5
- Moduly: 13
- Score: 100/100 🏆
```

**Attach file:**
- Nahrát: `kafankuv-mozek-v1.2.1.zip` (přejmenovat z WITH-SHORTCODES)

**Publish release** ✅

---

### 4️⃣ Otestovat Auto-Update

**Na webu mykolibri-academy.cz:**

```
1. WordPress Admin → Dashboard
2. Počkat ~12 hodin (cache)
   NEBO
3. Vymazat transient:
   wp transient delete kafanek_brain_remote_version
```

**Po detekci:**
```
Dashboard → Updates
  ✅ Kafánkův Mozek 1.2.1 je dostupný
  
Pluginy
  ⚠️ Dostupná aktualizace na 1.2.1
  
Kliknout: "Aktualizovat nyní"
  → Automaticky se stáhne z GitHubu
  → Nainstaluje
  → Hotovo! ✅
```

---

## 🔄 JAK TO FUNGUJE

### Plugin kontroluje GitHub:

```php
// includes/class-updater.php
private $github_username = 'kolibric';
private $github_repo = 'kafanek-brain';

// Kontrola každých 12 hodin
public function check_for_update($transient) {
    $remote_version = $this->get_remote_version();
    
    // Pokud GitHub verze > současná verze
    if (version_compare($current, $remote_version, '<')) {
        // Zobrazit notifikaci
        // Nabídnout update
    }
}
```

### Při kliknutí "Aktualizovat":

```
1. Stáhne ZIP z GitHub release
2. Deaktivuje starý plugin
3. Smaže starou verzi
4. Nahraje novou verzi
5. Aktivuje nový plugin
6. Zobrazí "Úspěšně aktualizováno!"
```

---

## 📋 CHECKLIST

```bash
[ ] GitHub repository vytvořen
[ ] Kód nahrán na GitHub (git push)
[ ] GitHub Personal Access Token vytvořen
[ ] GitHub Release v1.2.1 vytvořen
[ ] ZIP nahrán k release
[ ] Release publikován
[ ] Verze v pluginu je 1.2.1
[ ] Plugin na webu je verze 1.2.0 (starší)
[ ] Cache vymazána (volitelné)
[ ] Notifikace se zobrazí v admin
[ ] Aktualizace proběhla úspěšně
```

---

## 🎯 BUDOUCÍ AKTUALIZACE

**Pro vydání v1.2.2 a vyšší:**

```bash
1. Změnit verzi v kafanek-brain.php
2. git commit -m "v1.2.2 - Další vylepšení"
3. git push
4. GitHub → Create new release v1.2.2
5. Nahrát nový ZIP
6. Publish
7. Web automaticky najde update během 12h
```

---

## 🐛 Troubleshooting

### ❌ "No update available"

**Příčina:** Cache  
**Řešení:**
```bash
wp transient delete kafanek_brain_remote_version
# NEBO počkat 12 hodin
```

### ❌ "Failed to download"

**Příčina:** GitHub release nemá ZIP  
**Řešení:**
1. Zkontroluj že release má attached ZIP file
2. ZIP musí být public (repository public)

### ❌ GitHub autentifikace selhala

**Řešení:**
```bash
# Použít Personal Access Token místo hesla
git push https://[token]@github.com/kolibric/kafanek-brain.git
```

---

## 🎉 VÝSLEDEK

**Po nastavení:**
- ✅ Web automaticky kontroluje GitHub
- ✅ Notifikace při nové verzi
- ✅ Jedním klikem update
- ✅ Žádné manuální nahrávání ZIP

**Profesionální auto-update systém! 🚀**

---

**Dokumentace GitHub Auto-Updater:** `includes/class-updater.php`
