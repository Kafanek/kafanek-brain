# 🤖 AI PROVIDERS - KOMPLETNÍ PRŮVODCE

Kafánek Brain podporuje 4 AI poskytovatele pro různé účely.

---

## 📊 PŘEHLED PROVIDERŮ

### **1. OpenAI GPT** (Text generování)
- **Model:** GPT-3.5-turbo, GPT-4
- **Cena:** $0.50 / 1M input tokenů
- **Rychlost:** ⚡⚡⚡⚡ (2-5s)
- **Kvalita:** ⭐⭐⭐⭐
- **Použití:** Hromadné generování, rychlé odpovědi

### **2. Claude 3.5 Sonnet** (Text generování)
- **Model:** claude-3-5-sonnet-20241022
- **Cena:** $3.00 / 1M input tokenů
- **Rychlost:** ⚡⚡⚡ (3-8s)
- **Kvalita:** ⭐⭐⭐⭐⭐
- **Použití:** Kvalitní unique content, dlouhé texty

### **3. Google Gemini** (Text generování)
- **Model:** Gemini 1.5 Flash, Gemini 1.5 Pro
- **Cena:** ZDARMA do 15 req/min, pak $0.35 / 1M
- **Rychlost:** ⚡⚡⚡⚡⚡ (1-3s)
- **Kvalita:** ⭐⭐⭐⭐
- **Použití:** Experimenty, vysoká frekvence

### **4. Azure Speech** (Speech-to-Text)
- **Service:** Cognitive Services Speech
- **Cena:** $1 / hodina zvuku
- **Rychlost:** ⚡⚡⚡⚡ (real-time)
- **Jazyk:** Čeština ✅
- **Použití:** Hlasové diktování popisů

---

## 🔧 SETUP KAŽDÉHO PROVIDERA

### OpenAI
```
1. https://platform.openai.com/api-keys
2. Create new secret key → Zkopírovat sk-xxxxx
3. WordPress → Kafánkův Mozek → Settings
4. AI Provider: OpenAI (GPT)
5. OpenAI API Key: sk-xxxxx
6. Save
```

### Claude (Anthropic)
```
1. https://console.anthropic.com/
2. API Keys → Create Key → Zkopírovat sk-ant-xxxxx
3. WordPress → Settings
4. AI Provider: Anthropic (Claude)
5. Claude API Key: sk-ant-xxxxx
6. Save
```

### Google Gemini
```
1. https://makersuite.google.com/app/apikey
2. Create API key → Zkopírovat AIzaSy...
3. WordPress → Settings
4. AI Provider: Google (Gemini)
5. Gemini API Key: AIzaSy...
6. Save
```

### Azure Speech
```
1. https://portal.azure.com
2. Create Resource → Speech Services
3. Keys and Endpoint → Zkopírovat Key 1
4. WordPress → Settings → Azure Speech Services
5. Azure Speech API Key: xxxxxxxxxxxx
6. Azure Region: westeurope
7. Save
```

---

## 💰 POROVNÁNÍ NÁKLADŮ

### Scénář: 100 produktových popisů (500 tokenů each)

| Provider | Náklady | Čas | Kvalita |
|----------|---------|-----|---------|
| **OpenAI GPT-3.5** | ~1 Kč | 5 min | Dobrá |
| **Claude Sonnet** | ~9 Kč | 8 min | Vynikající |
| **Gemini Flash** | ZDARMA | 3 min | Velmi dobrá |
| **Gemini Pro** | ~3 Kč | 5 min | Vynikající |

**Doporučení:** Gemini Flash pro testing, Claude pro produkci.

---

## 🎯 BEST PRACTICES

### Kdy použít který provider:

**OpenAI GPT-3.5:**
- Hromadné generování (100+ produktů)
- Jednoduché popisy
- Tight budget

**Claude 3.5:**
- Premium produkty (luxury, high-end)
- Dlouhé detailní popisy (1000+ slov)
- Kreativní copywriting

**Gemini Flash:**
- Testing a experimenty
- Vysoká frekvence (15+ req/min zdarma)
- Real-time aplikace

**Azure Speech:**
- Diktování popisů místo psaní
- Vícejazyčné vstupy
- Accessibility features

---

## 🔄 PŘEPÍNÁNÍ PROVIDERŮ

```php
// V kódu můžete dynamicky přepínat:
update_option('kafanek_ai_provider', 'gemini');
$ai = new Kafanek_AI_Engine();
$result = $ai->generate_text($prompt);
```

---

## 📝 PŘÍKLADY POUŽITÍ

### Text generování (všechny 3 providery)
```php
$ai = new Kafanek_AI_Engine();
$description = $ai->generate_text("Napiš popis pro produkt: AI Senior Kurz");
```

### Speech-to-Text (Azure)
```php
$ai = new Kafanek_AI_Engine();
$result = $ai->speech_to_text('/path/to/audio.wav');
echo $result['text']; // "Tento produkt je skvělý..."
```

---

**✨ Nyní máte 4 AI poskytovatele pro maximální flexibilitu!**
