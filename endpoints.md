# SchemaProWP API Endpoints

SchemaProWP använder WordPress REST API för att exponera följande endpoints. Alla endpoints är prefixade med `/wp-json/schemaprowp/v1`.

## Organisationer

### Hämta alla organisationer
- **GET** `/organizations`
- Beskrivning: Hämtar en lista över alla organisationer
- Parametrar:
  - `parent_id` (valfri): Filtrera efter föräldraorganisation

### Hämta en specifik organisation
- **GET** `/organizations/{id}`
- Beskrivning: Hämtar detaljer för en specifik organisation

### Skapa en ny organisation
- **POST** `/organizations`
- Beskrivning: Skapar en ny organisation
- Body: 
  ```json
  {
    "name": "Organisationsnamn",
    "parent_id": null
  }
  ```

### Uppdatera en organisation
- **PUT** `/organizations/{id}`
- Beskrivning: Uppdaterar en befintlig organisation
- Body: 
  ```json
  {
    "name": "Nytt organisationsnamn",
    "parent_id": 1
  }
  ```

### Ta bort en organisation
- **DELETE** `/organizations/{id}`
- Beskrivning: Tar bort en organisation

## Resurser

### Hämta alla resurser
- **GET** `/resources`
- Beskrivning: Hämtar en lista över alla resurser
- Parametrar:
  - `post_id` (valfri): Filtrera efter organisation (post_id)
  - `type` (valfri): Filtrera efter resurstyp
  - `status` (valfri): Filtrera efter status
  - `per_page` (valfri): Antal resultat per sida
  - `page` (valfri): Sidnummer
  - `orderby` (valfri): Sorteringsfält
  - `order` (valfri): Sorteringsordning (asc/desc)

### Hämta en specifik resurs
- **GET** `/resources/{id}`
- Beskrivning: Hämtar detaljer för en specifik resurs

### Skapa en ny resurs
- **POST** `/resources`
- Beskrivning: Skapar en ny resurs
- Body: 
  ```json
  {
    "post_id": 1,
    "name": "Resursnamn",
    "type": "room",
    "status": "active",
    "properties": {
      "capacity": 10,
      "equipment": ["projector", "whiteboard"]
    }
  }
  ```

### Uppdatera en resurs
- **PUT** `/resources/{id}`
- Beskrivning: Uppdaterar en befintlig resurs
- Body: 
  ```json
  {
    "name": "Nytt resursnamn",
    "status": "inactive",
    "properties": {
      "capacity": 15
    }
  }
  ```

### Ta bort en resurs
- **DELETE** `/resources/{id}`
- Beskrivning: Tar bort en resurs

## Bokningar

### Hämta alla bokningar
- **GET** `/bookings`
- Beskrivning: Hämtar en lista över alla bokningar
- Parametrar:
  - `resource_id` (valfri): Filtrera efter resurs
  - `user_id` (valfri): Filtrera efter användare
  - `status` (valfri): Filtrera efter status
  - `start_time` (valfri): Filtrera efter startdatum/tid
  - `end_time` (valfri): Filtrera efter slutdatum/tid
  - `per_page` (valfri): Antal resultat per sida
  - `page` (valfri): Sidnummer
  - `orderby` (valfri): Sorteringsfält
  - `order` (valfri): Sorteringsordning (asc/desc)

### Hämta en specifik bokning
- **GET** `/bookings/{id}`
- Beskrivning: Hämtar detaljer för en specifik bokning

### Skapa en ny bokning
- **POST** `/bookings`
- Beskrivning: Skapar en ny bokning
- Body: 
  ```json
  {
    "resource_id": 1,
    "user_id": 1,
    "start_time": "2025-03-01T09:00:00",
    "end_time": "2025-03-01T10:00:00",
    "status": "pending",
    "comments": "Projektmöte"
  }
  ```

### Uppdatera en bokning
- **PUT** `/bookings/{id}`
- Beskrivning: Uppdaterar en befintlig bokning
- Body: 
  ```json
  {
    "status": "confirmed",
    "comments": "Bekräftat projektmöte"
  }
  ```

### Ta bort en bokning
- **DELETE** `/bookings/{id}`
- Beskrivning: Tar bort en bokning

### Avboka en bokning
- **POST** `/bookings/{id}/cancel`
- Beskrivning: Avbokar en bokning (sätter status till "cancelled")

### Kontrollera tillgänglighet
- **GET** `/bookings/available`
- Beskrivning: Kontrollerar tillgängliga tider för en resurs
- Parametrar:
  - `resource_id` (obligatorisk): Resurs-ID att kontrollera
  - `start_time` (obligatorisk): Startdatum/tid att kontrollera från
  - `end_time` (obligatorisk): Slutdatum/tid att kontrollera till

## Användarorganisationer

### Hämta alla användarorganisationer
- **GET** `/user-organizations`
- Beskrivning: Hämtar en lista över alla användar-organisationsrelationer
- Parametrar:
  - `user_id` (valfri): Filtrera efter användare
  - `organization_id` (valfri): Filtrera efter organisation

### Lägg till en användare till en organisation
- **POST** `/user-organizations`
- Beskrivning: Lägger till en användare till en organisation med en specifik roll
- Body: 
  ```json
  {
    "user_id": 1,
    "organization_id": 1,
    "role": "admin"
  }
  ```

### Uppdatera en användarroll i en organisation
- **PUT** `/user-organizations/{id}`
- Beskrivning: Uppdaterar en användares roll i en organisation
- Body: 
  ```json
  {
    "role": "member"
  }
  ```

### Ta bort en användare från en organisation
- **DELETE** `/user-organizations/{id}`
- Beskrivning: Tar bort en användare från en organisation

## Autentisering och behörigheter

Alla endpoints kräver autentisering via WordPress inbyggda autentiseringssystem. För att använda API:et måste klienten:

1. Vara inloggad i WordPress
2. Skicka med en giltig nonce i headers eller som parameter
3. Ha rätt behörigheter för den begärda åtgärden

### Behörighetsnivåer

- **Läsa** (GET): Kräver `read` capability
- **Skriva** (POST, PUT, DELETE): Kräver `publish_posts` capability

### Autentiseringsheaders

```javascript
{
  'X-WP-Nonce': schemaProWPData.nonce
}
```

## Felhantering

API:et returnerar standardiserade felsvar enligt WordPress REST API:s format:

```json
{
  "code": "error_code",
  "message": "Beskrivande felmeddelande",
  "data": {
    "status": 400
  }
}
```

Vanliga felkoder:
- `rest_resource_not_found`: Resursen kunde inte hittas (404)
- `rest_booking_not_found`: Bokningen kunde inte hittas (404)
- `rest_cannot_create`: Kunde inte skapa objektet (400)
- `rest_cannot_update`: Kunde inte uppdatera objektet (400)
- `rest_cannot_delete`: Kunde inte ta bort objektet (400)
- `rest_validation_error`: Valideringsfel i indata (400)