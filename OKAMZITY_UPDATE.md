# ⚡ OKAMŽITÝ UPDATE - Bez čekání 12 hodin!

## 🚀 3 ZPŮSOBY JAK AKTUALIZOVAT TEĎ

---

## ✅ ZPŮSOB 1: Vymaž Cache (NEJRYCHLEJŠÍ)

### Varianta A: Přes WordPress Admin

**1. Přidej tento kód do functions.php dočasně:**

```php
// DOČASNÝ KÓD - po update smaž!
add_action('admin_init', function() {
    if (current_user_can('manage_options') && isset($_GET['force_update_check'])) {
        delete_transient('kafanek_brain_remote_version');
        wp_redirect(admin_url('plugins.php'));
        exit;
    }
});
```

**2. Otevři v prohlížeči:**
```
https://mykolibri-academy.cz/wp-admin/?force_update_check=1
```

**3. Plugin okamžitě zkontroluje GitHub a najde v1.2.2!**

**4. Smaž ten dočasný kód z functions.php**

---

### Varianta B: Přes WP-CLI (pokud máš)

```bash
# SSH do serveru
ssh w381466@381466.w66.wedos.net -p 94

# Smaž transient
wp transient delete kafanek_brain_remote_version

# Zkontroluj updaty
wp plugin list
# Uvidíš: "update available" u Kafánek Brain
```

---

### Varianta C: Přes plugin kód (snippet)

**1. Jdi do WordPress Admin → Pluginy → Editor pluginů**

**2. Vyber Kafánek Brain → includes/class-updater.php**

**3. Najdi řádek 74 a DOČASNĚ změň:**

```php
// BYLO:
set_transient($cache_key, $version, 12 * HOUR_IN_SECONDS);

// ZMĚŇ NA (jen na chvíli!):
set_transient($cache_key, $version, 1); // 1 sekunda = okamžitá kontrola
```

**4. Ulož a otevři WordPress Admin → Pluginy**

**5. Uvidíš update notifikaci okamžitě!**

**6. Po update VRAŤ zpět na 12 * HOUR_IN_SECONDS**

---

## ✅ ZPŮSOB 2: Manuální nahrání ZIP (100% JISTOTA)

### Přes WordPress Admin:

**1. Přihlas se:**
```
https://mykolibri-academy.cz/wp-admin
Username: admin
Password: @Yta1sL@LM%UCW*^Q@
```

**2. Jdi na:**
```
Pluginy → Nahrát plugin
```

**3. Vyber soubor:**
```
/Users/kolibric/Desktop/KolibriMCPs/test-plugin/kafankuv-mozek-v1.2.2-DESIGN-STUDIO.zip
```

**4. Klikni:**
```
"Nahradit současný" (Replace current)
```

**5. Klikni:**
```
"Aktivovat plugin"
```

**6. Hotovo! Design Studio je aktivní! ✅**

---

## ✅ ZPŮSOB 3: SFTP Upload (PRO POKROČILÉ)

### Připojení:

```
Server: 381466.w66.wedos.net
Port: 94
Username: w381466
Password: Jademide@25
Protocol: SFTP
```

### Postup:

**1. Připoj se přes FileZilla/Cyberduck**

**2. Naviguj na:**
```
/wp-content/plugins/
```

**3. Záloha (volitelné):**
```
Stáhni současnou složku: kafanek-brain/
Ulož jako: kafanek-brain-backup/
```

**4. Smaž starou verzi:**
```
Smaž: /wp-content/plugins/kafanek-brain/
```

**5. Rozbal ZIP lokálně a nahraj:**
```
Nahraj novou složku: kafanek-brain/
```

**6. Zkontroluj oprávnění:**
```
Složky: 755
Soubory: 644
```

**7. WordPress Admin → Pluginy → Aktivovat**

---

## 🎯 DOPORUČENÍ

### Pro okamžitý update TEĎ:

**Nejrychlejší:** Způsob 1A (vymaž cache přes URL)
**Nejbezpečnější:** Způsob 2 (manuální ZIP)
**Pro experty:** Způsob 3 (SFTP)

---

## 📝 SKRIPT PRO OKAMŽITÝ UPDATE

**Ulož jako: force-update.php a nahraj do root WordPress:**

```php
<?php
/**
 * Force Update Check - Kafanek Brain
 * Použití: https://mykolibri-academy.cz/force-update.php
 * Po použití SMAŽ tento soubor!
 */

define('WP_USE_THEMES', false);
require_once('./wp-load.php');

if (!current_user_can('manage_options')) {
    die('Nemáš oprávnění!');
}

// Vymaž cache
delete_transient('kafanek_brain_remote_version');

// Zkontroluj update
wp_update_plugins();

echo '<h1>✅ Cache smazána!</h1>';
echo '<p>Plugin nyní zkontroluje GitHub...</p>';
echo '<p><a href="/wp-admin/plugins.php">→ Jdi na Pluginy</a></p>';
echo '<p><strong>⚠️ SMAŽ tento soubor po použití!</strong></p>';
```

**Použití:**
1. Nahraj na server jako `force-update.php`
2. Otevři: `https://mykolibri-academy.cz/force-update.php`
3. Klikni na odkaz "Jdi na Pluginy"
4. Uvidíš update notifikaci!
5. **SMAŽ force-update.php ze serveru!**

---

## ⚡ NEJRYCHLEJŠÍ ZPŮSOB (1 MINUTA)

### Přes prohlížeč konzoli:

**1. Otevři WordPress Admin**

**2. Stiskni F12 (Developer Tools)**

**3. Přejdi na Console**

**4. Vlož tento JavaScript:**

```javascript
fetch('/wp-admin/admin-ajax.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'action=delete_transient&transient=kafanek_brain_remote_version'
}).then(() => {
    alert('Cache smazána! Refresh stránku.');
    location.reload();
});
```

**5. Stiskni Enter**

**6. Refresh stránku → uvidíš update!**

---

## 🔍 JAK OVĚŘIT ŽE TO FUNGUJE

**1. Po vymazání cache:**
```
WordPress Admin → Pluginy
→ Měl bys vidět: "Dostupná aktualizace pro Kafánkův Mozek"
```

**2. Klikni "Zobrazit detaily":**
```
Měl bys vidět:
- Verze: 1.2.2
- Popis: AI Design Studio...
- Download link z GitHubu
```

**3. Klikni "Aktualizovat nyní":**
```
WordPress stáhne z: https://github.com/Kafanek/kafanek-brain/releases
→ Nainstaluje v1.2.2
→ Design Studio aktivní!
```

---

## ✅ PO AKTUALIZACI

**Zkontroluj že vše funguje:**

1. **WordPress Admin → Kafánkův Mozek**
   - Měl bys vidět novou položku: "🎨 Design Studio"

2. **Klikni na Design Studio**
   - Měl bys vidět: kategorii výběr, styly, generátor

3. **Vygeneruj testovací design:**
   ```
   Kategorie: Logo Design
   Styl: Modern
   Popis: Test logo for AI company
   → Klikni "Vygenerovat"
   ```

4. **Zkontroluj verzi:**
   ```
   WordPress Admin → Pluginy
   → Kafánkův Mozek by měl ukazovat: Verze 1.2.2
   ```

---

## 🎯 VÝSLEDEK

**Po úspěšném update máš:**
- ✅ Kafánek Brain v1.2.2
- ✅ AI Design Studio (6 kategorií, 42 stylů)
- ✅ Golden Ratio kompozice
- ✅ Fibonacci spiral overlay
- ✅ 3 varianty designů
- ✅ Barevné palety
- ✅ Design history

**Můžeš začít vytvářet úžasné designy! 🎨✨**
