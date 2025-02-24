# SchemaProWP

SchemaProWP är ett avancerat WordPress-plugin för schemaläggning av personal och resurser. Det erbjuder en flexibel lösning för organisationer med hierarkisk struktur och möjlighet att hantera olika typer av resurser.

## Funktioner

- Hierarkisk organisationsstruktur
- Användarhantering med olika roller inom organisationer
- Resurshantering med kategorisering och egenskaper
- Bokningssystem med 15-minuters intervaller
- Kalendervyer i månads-, vecko- och dagformat
- Konflikthantering för dubbelbokningar
- Responsivt gränssnitt byggt med Svelte

## Teknisk Stack

- WordPress-plugin struktur
- Svelte 4 för frontend (både admin och publik del)
- Vite som byggverktyg
- WordPress REST API för backend
- MySQL för datalagring via WordPress WPDB

## Installation

1. Ladda ner zip-filen från GitHub-repositoryt
2. Gå till WordPress admin-panel > Plugins > Lägg till ny
3. Klicka på "Ladda upp plugin" och välj den nedladdade zip-filen
4. Aktivera pluginet efter installation

## Användning

Efter aktivering, gå till "SchemaProWP" i admin-menyn för att börja konfigurera dina organisationer, resurser och användare.

## Utveckling

För utvecklare som vill bidra eller anpassa pluginet:

1. Klona repositoryt
2. Installera beroenden med `npm install`
3. Kör `npm run dev` för att starta Vite i utvecklingsläge
4. Kör `npm run build` för att bygga för produktion

## Licens

Detta projekt är licensierat under GPL v2 eller senare.

## Support

För support, vänligen öppna ett ärende på GitHub eller kontakta oss via [support@example.com](mailto:support@example.com).
