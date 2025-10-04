# 🚀 KAFÁNEK BRAIN - QUICK START GUIDE

**Verze:** 1.2.0  
**Datum:** 2025-10-03

---

## ⚡ RYCHLÁ INSTALACE (5 minut)

### Krok 1: Aktivace pluginu

```bash
WordPress Admin → Pluginy → Aktivovat "Kafánkův Mozek - AI WordPress Brain"
```

**Výsledek:** Plugin se aktivuje a vytvoří databázové tabulky.

---

### Krok 2: Nastavení OpenAI API klíče

```bash
WordPress Admin → Kafánkův Mozek → Nastavení
```

**OpenAI API Key:**
```
Vložte váš API klíč do pole "OpenAI API Key"
Klikněte "Uložit nastavení"
```

**Test API:**
```
Klikněte tlačítko "Test OpenAI Connection"
Měli byste vidět: ✅ API funguje!
```

---

### Krok 3: Zapnout moduly

V Nastavení zaškrtněte:
- ✅ **WooCommerce AI** (pokud máte WooCommerce)
- ✅ **Elementor Widgets** (pokud máte Elementor)
- ✅ **Neural Network** (experimentální)

Klikněte **"Uložit nastavení"**

---

## 🛒 WOOCOMMERCE AI - JAK POUŽÍVAT

### Generování popisu produktu:

1. Otevřete **Products → Edit Product**
2. V pravém postranním panelu najděte **"🧠 Kafánkův AI Asistent"**
3. Klikněte **"📝 Generovat popis"**
4. Počkejte 5-10 sekund
5. Popis se automaticky vyplní do editoru
6. **Uložte produkt!**

### Optimalizace ceny:

1. V tom samém metaboxu klikněte **"💰 Optimalizovat cenu"**
2. AI spočítá optimální cenu pomocí Golden Ratio
3. Potvrďte změnu
4. **Uložte produkt!**

---

## 📊 DASHBOARD & STATISTIKY

```bash
WordPress Admin → Kafánkův Mozek
```

**Zde uvidíte:**
- 📊 Celkem AI požadavků
- 🎯 Použité tokeny (náklady)
- ✨ Golden Ratio hodnota (φ = 1.618)
- 📝 Poslední AI požadavky

---

## 🧪 TESTOVÁNÍ FUNKCÍ

### Test 1: REST API Status
```bash
curl https://mykolibri-academy.cz/wp-json/kafanek-brain/v1/status
```

**Očekávaný výstup:**
```json
{
  "status": "active",
  "version": "1.2.0",
  "modules": {"helpers": true, "WooCommerce AI": true},
  "ai_ready": true
}
```

### Test 2: Generování popisu
1. Vytvořte testovací produkt
2. Název: "Test AI Produkt"
3. Kategorie: "Elektronika"
4. Cena: 999 Kč
5. Klikněte "Generovat popis"
6. ✅ Popis by měl být vytvořen během 10 sekund

### Test 3: AI Recommendations
1. Otevřete libovolný produkt na frontendu
2. Na product page by se měla zobrazit sekce **"🤖 AI Doporučuje"**
3. Zobrazí 4 podobné produkty ze stejné kategorie

---

## ⚙️ KONFIGURACE PRO PRODUKCI

### Doporučené nastavení:

**OpenAI Model:**
- Development: `gpt-3.5-turbo` (levnější)
- Production: `gpt-4` (kvalitnější)

**Cache:**
- Plugin automaticky cachuje AI odpovědi na 1 hodinu
- Úspora: ~80% API volání

**Rate Limiting:**
- Doporučujeme max 100 requests/den pro testování
- Production: podle potřeby

---

## 💰 NÁKLADY (ORIENTAČNÍ)

**OpenAI GPT-3.5-turbo:**
- Generování popisu produktu: ~$0.001-0.002 (0.02-0.05 Kč)
- 100 popisů ≈ $0.15 (3.50 Kč)
- 1000 popisů ≈ $1.50 (35 Kč)

**OpenAI GPT-4:**
- Generování popisu produktu: ~$0.03-0.05 (0.70-1.20 Kč)
- 100 popisů ≈ $4.00 (95 Kč)

**Tip:** Používejte cache! Opakované dotazy jsou ZDARMA.

---

## 🐛 ŘEŠENÍ PROBLÉMŮ

### ❌ "API key not configured"
**Řešení:** 
1. Zkontrolujte, že jste vložili API klíč v nastavení
2. Klikněte "Uložit nastavení"
3. Obnovte stránku

### ❌ "WooCommerce není aktivní"
**Řešení:**
1. Aktivujte WooCommerce plugin
2. Deaktivujte a znovu aktivujte Kafánek Brain
3. Zapněte WooCommerce modul v nastavení

### ❌ "Chyba při volání API"
**Řešení:**
1. Zkontrolujte API klíč (kopírování bez mezer!)
2. Ověřte kredit na OpenAI účtu
3. Zkontrolujte WordPress debug.log

### ❌ Metabox se nezobrazuje
**Řešení:**
1. Obnovte stránku editace produktu (Ctrl+R)
2. Zkontrolujte "Screen Options" vpravo nahoře
3. Zapněte "Kafánkův AI Asistent"

---

## 📈 BEST PRACTICES

### ✅ DO:
- Používejte cache (automatické)
- Generujte popisy hromadně (batch processing)
- Kontrolujte vygenerované texty před publikací
- Monitorujte usage v dashboardu

### ❌ DON'T:
- Nespamujte API (rate limiting!)
- Nepoužívejte pro každý refresh stránky
- Nespoléhejte 100% na AI (vždy kontrolujte)
- Nesdílejte API klíč (bezpečnost!)

---

## 🚀 PRODUKČNÍ CHECKLIST

```bash
[ ] API klíč nastaven a otestován
[ ] WooCommerce modul zapnut
[ ] Cache funguje (transients)
[ ] Debug mode vypnut (WP_DEBUG = false)
[ ] SSL certifikát aktivní (https://)
[ ] Backup databáze (před hromadnou generací)
[ ] Monitoring nastavený (usage tracking)
[ ] Rate limiting zkonfigurován
[ ] Testovací produkty vygenerovány úspěšně
[ ] Frontend recommendations zobrazují se správně
```

---

## 📞 PODPORA

**Dokumentace:** `/wp-content/plugins/kafanek-brain/INTEGRATION_REPORT_v1.1.0.md`

**Debug Logs:**
```bash
/wp-content/debug.log
```

**WordPress Admin:**
```
Kafánkův Mozek → Dashboard → Poslední AI požadavky
```

---

**🎉 Hotovo! Plugin je nakonfigurován a připraven k použití.**

**Další kroky:**
1. Vygenerujte popis prvního produktu
2. Zkontrolujte usage v dashboardu
3. Sledujte náklady na OpenAI Platform
