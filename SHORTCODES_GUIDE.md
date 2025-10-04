# 🚀 Kafánek Brain - Shortcodes Guide

**Verze:** 1.2.0  
**Snadné použití AI funkcí kdekoli na webu!**

---

## 📝 Dostupné Shortcodes

### 1. AI Chat Widget

```php
[kafanek_chat]
```

**Parametry:**
- `position` - Pozice chatu (`bottom-right`, `bottom-left`)
- `button_text` - Text tlačítka
- `welcome_message` - Uvítací zpráva

**Příklad:**
```php
[kafanek_chat position="bottom-right" welcome_message="Ahoj! Jak mohu pomoci?"]
```

**Výsledek:** Zobrazí AI chatbot widget v pravém dolním rohu

---

### 2. AI Generovaný Obsah

```php
[kafanek_content type="blog" topic="AI technologie"]
```

**Parametry:**
- `type` - Typ obsahu:
  - `blog` - Blog článek
  - `product` - Popis produktu
  - `email` - Marketingový email
  - `social` - Příspěvek na sociální sítě
- `topic` - Téma obsahu (POVINNÉ)
- `length` - Délka (`short`, `medium`, `long`)
- `tone` - Tón (`professional`, `friendly`, `casual`)
- `cache` - Cachování (`yes`, `no`)

**Příklady:**

Blog článek:
```php
[kafanek_content type="blog" topic="10 tipů pro lepší spánek" length="medium" tone="friendly"]
```

Popis produktu:
```php
[kafanek_content type="product" topic="Prémiový kávovar Delonghi" tone="professional"]
```

Email kampaň:
```php
[kafanek_content type="email" topic="Nová kolekce jaro 2024" tone="friendly"]
```

---

### 3. Dynamická Cena (Golden Ratio)

```php
[kafanek_price product_id="123" tier="premium"]
```

**Parametry:**
- `product_id` - ID WooCommerce produktu (POVINNÉ)
- `tier` - Cenová úroveň:
  - `budget` - Základní (÷ φ)
  - `standard` - Standardní
  - `premium` - Prémiová (× φ)
  - `luxury` - Luxusní (× φ²)
- `show_original` - Zobrazit původní cenu (`yes`, `no`)

**Příklady:**

Prémiová cena:
```php
[kafanek_price product_id="456" tier="premium" show_original="yes"]
```

Budget cena:
```php
[kafanek_price product_id="789" tier="budget"]
```

**Výsledek:**
```
Původní: 1000 Kč
Premium: 1618 Kč  φ optimized
```

---

### 4. AI Doporučené Produkty

```php
[kafanek_recommendation category="electronics" limit="4"]
```

**Parametry:**
- `category` - Kategorie produktů (slug)
- `limit` - Počet produktů (výchozí: 4)
- `layout` - Layout (`grid`, `list`, `slider`)

**Příklady:**

Grid layout:
```php
[kafanek_recommendation category="electronics" limit="6" layout="grid"]
```

Specifická kategorie:
```php
[kafanek_recommendation category="kavovar" limit="3"]
```

**Výsledek:**
```
🧠 AI Doporučení pro vás
[Product 1] [Product 2] [Product 3] [Product 4]
```

---

### 5. Neural Network Insight

```php
[kafanek_neural_insight type="price_prediction" product_id="123"]
```

**Parametry:**
- `type` - Typ analýzy:
  - `price_prediction` - Predikce ceny
  - `sales_forecast` - Prognóza prodejů
  - `customer_behavior` - Chování zákazníků
- `product_id` - ID produktu (pokud relevantní)
- `show_confidence` - Zobrazit přesnost (`yes`, `no`)

**Příklady:**

Predikce ceny:
```php
[kafanek_neural_insight type="price_prediction" product_id="456" show_confidence="yes"]
```

Forecast prodejů:
```php
[kafanek_neural_insight type="sales_forecast"]
```

**Výsledek:**
```
🧬 Predikce optimální ceny
Na základě historických dat doporučujeme cenu 899-1099 Kč
Přesnost: 87%
[Progress bar]
```

---

## 💡 Praktické Příklady Použití

### Landing Page s AI

```php
<!-- Hero Section -->
<h1>Objevte sílu AI</h1>
[kafanek_content type="blog" topic="Výhody AI v moderním businessu" length="short"]

<!-- Produkty -->
<h2>Naše Doporučení</h2>
[kafanek_recommendation category="ai-tools" limit="4" layout="grid"]

<!-- Chat Support -->
[kafanek_chat position="bottom-right" welcome_message="Máte dotaz? Zeptejte se!"]
```

### Produktová Stránka

```php
<!-- Dynamická cena -->
<div class="pricing">
    <h3>Základní</h3>
    [kafanek_price product_id="123" tier="budget"]
    
    <h3>Prémiový</h3>
    [kafanek_price product_id="123" tier="premium" show_original="yes"]
</div>

<!-- AI Popis -->
[kafanek_content type="product" topic="Chytré hodinky XYZ" tone="professional"]

<!-- Neural Insight -->
[kafanek_neural_insight type="price_prediction" product_id="123"]
```

### Blog Post

```php
<!-- AI Generovaný Obsah -->
[kafanek_content type="blog" topic="Budoucnost e-commerce" length="long" tone="professional"]

<!-- Související Produkty -->
<h3>Doporučujeme vyzkoušet</h3>
[kafanek_recommendation limit="3"]

<!-- Chatbot -->
[kafanek_chat]
```

---

## 🎨 Styling

### Custom CSS

```css
/* AI Content */
.kafanek-ai-content {
    background: #f9fafb;
    padding: 20px;
    border-left: 4px solid #697077;
    margin: 20px 0;
}

/* Dynamic Price */
.kafanek-dynamic-price {
    font-size: 24px;
    font-weight: bold;
    color: #697077;
}

.phi-badge {
    background: #697077;
    color: white;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 12px;
}

/* Recommendations */
.kafanek-recommendations {
    margin: 30px 0;
}

.recommendations-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
}

/* Neural Insight */
.kafanek-neural-insight {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.confidence-bar {
    background: #e5e7eb;
    height: 8px;
    border-radius: 4px;
    overflow: hidden;
}

.confidence-fill {
    background: #697077;
    height: 100%;
    transition: width 0.3s;
}
```

---

## ⚡ Performance Tipy

### 1. Cachování

Pro často zobrazovaný obsah použijte cache:
```php
[kafanek_content type="blog" topic="..." cache="yes"]
```

Cache automaticky vyprší po **21 minutách** (Fibonacci number).

### 2. Limit produktů

Příliš mnoho produktů zpomaluje:
```php
<!-- Dobře -->
[kafanek_recommendation limit="4"]

<!-- Pomalu -->
[kafanek_recommendation limit="20"]
```

### 3. Kombinace shortcodů

Můžete kombinovat ve stejném příspěvku:
```php
[kafanek_content type="blog" topic="AI"]
[kafanek_recommendation limit="3"]
[kafanek_chat]
```

---

## 🔧 Troubleshooting

### Shortcode nezobrazuje nic

**Řešení:**
1. Zkontrolujte že plugin je aktivní
2. Ověřte že máte nastavený API klíč
3. Zkontrolujte PHP error log

### "Chybí parametr topic"

**Příčina:** Zapomněli jste `topic` parametr

**Řešení:**
```php
<!-- Špatně -->
[kafanek_content type="blog"]

<!-- Správně -->
[kafanek_content type="blog" topic="Vaše téma"]
```

### Ceny se nezobrazují

**Příčina:** WooCommerce není aktivní nebo neplatné product_id

**Řešení:**
1. Aktivujte WooCommerce
2. Zkontrolujte že product_id existuje
3. Použijte `[kafanek_price product_id="123"]` s platným ID

---

## 📱 Responsive Design

Všechny shortcodes jsou **plně responzivní**:

- ✅ Desktop
- ✅ Tablet
- ✅ Mobile

Chat widget se automaticky přizpůsobí velikosti obrazovky.

---

## 🎯 Best Practices

### 1. Jasná témata
```php
<!-- Dobře -->
[kafanek_content topic="10 tipů pro lepší produktivitu v práci"]

<!-- Špatně -->
[kafanek_content topic="tipy"]
```

### 2. Správný tón
```php
<!-- Pro B2B -->
[kafanek_content tone="professional"]

<!-- Pro blog -->
[kafanek_content tone="friendly"]

<!-- Pro sociální sítě -->
[kafanek_content tone="casual"]
```

### 3. Relevantní kategorie
```php
[kafanek_recommendation category="electronics"]
[kafanek_recommendation category="coffee"]
[kafanek_recommendation category="fashion"]
```

---

## 🚀 Příklady od A do Z

### Kompletní Product Page

```html
<div class="product-page">
    <!-- Hero -->
    <h1>Prémiový Kávovar Deluxe</h1>
    
    <!-- AI Popis -->
    <div class="description">
        [kafanek_content type="product" topic="Prémiový kávovar s automatickým mletím" tone="professional"]
    </div>
    
    <!-- Ceny -->
    <div class="pricing-table">
        <div class="price-tier">
            <h3>Základní balíček</h3>
            [kafanek_price product_id="789" tier="budget"]
        </div>
        <div class="price-tier featured">
            <h3>Prémiový balíček</h3>
            [kafanek_price product_id="789" tier="premium" show_original="yes"]
        </div>
        <div class="price-tier">
            <h3>Luxusní balíček</h3>
            [kafanek_price product_id="789" tier="luxury"]
        </div>
    </div>
    
    <!-- Neural Insight -->
    [kafanek_neural_insight type="price_prediction" product_id="789" show_confidence="yes"]
    
    <!-- Doporučené -->
    <h2>Zákazníci také kupují</h2>
    [kafanek_recommendation category="kavovar" limit="4" layout="grid"]
    
    <!-- Chat -->
    [kafanek_chat welcome_message="Potřebujete poradit s výběrem?"]
</div>
```

---

**🧠 Kafánek Brain - AI ve vašem WordPressu! ✨**

Pro více informací: [QUICK_START.md](QUICK_START.md)
