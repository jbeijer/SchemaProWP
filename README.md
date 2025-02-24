# SchemaProWP

SchemaProWP är ett avancerat WordPress-plugin för schemaläggning av personal och resurser. Det erbjuder en flexibel lösning för organisationer med hierarkisk struktur och möjlighet att hantera olika typer av resurser.

## Funktioner

- Hierarkisk organisationsstruktur med WordPress Custom Post Types
- Användarhantering med olika roller inom organisationer
- Resurshantering med:
  - Kategorisering och egenskaper
  - Status-hantering (aktiv/inaktiv)
  - Anpassningsbara egenskaper via JSON
- Bokningssystem med:
  - 15-minuters intervaller
  - Konflikthantering för dubbelbokningar
  - Status-hantering (väntande/bekräftad/avbokad)
  - Kommentarer och metadata
- Kalendervyer i månads-, vecko- och dagformat
- Responsivt gränssnitt byggt med Svelte
- REST API för integration med andra system

## Teknisk Stack

### Backend
- WordPress-plugin struktur
- WordPress REST API för datakommunikation
- Custom Post Types för organisationer
- MySQL för datalagring via WordPress WPDB
- PHP 7.4+ kompatibilitet

### Frontend
- Svelte 4.2.0 för komponentbaserad UI
- Vite som byggverktyg
- Modern JavaScript (ES6+)
- REST API integration
- Responsiv design

## Installation

1. Ladda ner zip-filen från GitHub-repositoryt
2. Gå till WordPress admin-panel > Plugins > Lägg till ny
3. Klicka på "Ladda upp plugin" och välj den nedladdade zip-filen
4. Aktivera pluginet efter installation

### Systemkrav
- WordPress 5.8+
- PHP 7.4+
- MySQL 5.7+
- Node.js 16+ (för utveckling)

## Utvecklingsmiljö

### Förberedelser

1. Installera Node.js och npm
2. Klona repositoryt:
   ```bash
   git clone https://github.com/username/SchemaProWP.git
   ```
3. Installera beroenden:
   ```bash
   cd SchemaProWP/src
   npm install
   ```

### Utvecklingsserver

1. Starta utvecklingsservern:
   ```bash
   npm run dev
   ```
2. För att bygga för produktion:
   ```bash
   npm run build
   ```

### Kodstruktur

```
SchemaProWP/
├── admin/                 # Admin-relaterade PHP-filer
│   ├── css/              # Admin CSS
│   ├── js/               # Kompilerad JavaScript
│   └── partials/         # Admin-templates
├── includes/             # Kärnklasser och funktioner
│   ├── api/             # REST API controllers
│   └── models/          # Datamodeller
├── src/                 # Frontend-källkod (Svelte)
│   ├── admin/          # Admin-komponenter
│   ├── components/     # Delade komponenter
│   └── stores/         # Svelte stores
└── languages/          # Översättningsfiler
```

### Kodstilar

#### PHP
- Följer WordPress kodstandarder
- PSR-4 autoloading
- Klassfiler använder `class-{name}.php` format
- Metoder använder snake_case
- Konstanter använder SCREAMING_SNAKE_CASE

#### JavaScript/Svelte
- Komponenter använder PascalCase
- Funktioner och variabler använder camelCase
- Stores använder camelCase.store.js format

### Tester

För att köra tester:

```bash
# PHP-tester
composer test

# JavaScript-tester
npm test
```

## Användning

### Administration
1. Gå till "SchemaProWP" i admin-menyn
2. Skapa organisationer via "Lägg till ny organisation"
3. Lägg till resurser och konfigurera deras egenskaper
4. Hantera användarbehörigheter

### Frontend
1. Använd shortcode `[schemaprowp]` för att visa bokningskalendern
2. Konfigurera visningsalternativ via attribut:
   ```
   [schemaprowp view="month" organization="123"]
   ```

### REST API
Se `endpoints.md` för fullständig API-dokumentation. Exempel på användning:

```javascript
// Hämta resurser
fetch('/wp-json/schemaprowp/v1/resources', {
  headers: {
    'X-WP-Nonce': schemaProWPData.nonce
  }
})
.then(response => response.json())
.then(resources => console.log(resources));
```

## Dokumentation

- [API-dokumentation](docs/api.md)
- [Datastruktur](docs/data-structure.md)
- [Utvecklarguide](docs/developer-guide.md)

## Bidra

1. Forka repositoryt
2. Skapa en feature branch (`git checkout -b feature/AmazingFeature`)
3. Commita dina ändringar (`git commit -m 'Add some AmazingFeature'`)
4. Pusha till branchen (`git push origin feature/AmazingFeature`)
5. Öppna en Pull Request

## Licens

Detta projekt är licensierat under MIT-licensen - se [LICENSE](LICENSE) filen för detaljer.

## Kontakt

Johan - [@twitterhandle](https://twitter.com/twitterhandle)

Projektlänk: [https://github.com/username/SchemaProWP](https://github.com/username/SchemaProWP)
