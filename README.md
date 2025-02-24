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

## Utveckling

För utvecklare som vill bidra eller anpassa pluginet:

1. Klona repositoryt:
   ```bash
   git clone https://github.com/username/schemaprowp.git
   ```

2. Installera beroenden:
   ```bash
   npm install
   ```

3. Utveckling:
   ```bash
   npm run dev     # Starta Vite i utvecklingsläge
   npm run build   # Bygga för produktion
   npm run lint    # Kör linting
   ```

### Filstruktur
```
schemaprowp/
├── dist/                 # Byggda frontend-filer
├── includes/            
│   ├── api/             # REST API controllers
│   ├── models/          # Datamodeller
│   └── admin/           # Admin-gränssnitt
├── src/
│   ├── admin/           # Admin Svelte-komponenter
│   └── public/          # Publika Svelte-komponenter
├── languages/           # Översättningsfiler
└── tests/              # Testfiler
```

### Kodstandard
- PHP: WordPress Coding Standards
- JavaScript: ESLint med Prettier
- Svelte: Rekommenderade best practices

## Databastabeller
- `wp_schemaprowp_resources`: Resurshantering
- `wp_schemaprowp_bookings`: Bokningsdata

Se `data-structure.md` för detaljerad databasstruktur.

## Licens

Detta projekt är licensierat under GPL v2 eller senare.

## Support

- GitHub Issues: Rapportera buggar och föreslå förbättringar
- E-post: [support@example.com](mailto:support@example.com)
- Dokumentation: Se `docs/` mappen för detaljerad dokumentation

## Changelog

Se `CHANGELOG.md` för en komplett lista över ändringar.
