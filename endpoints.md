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
  - `organization_id` (valfri): Filtrera efter organisation
  - `type` (valfri): Filtrera efter resurstyp

### Hämta en specifik resurs
- **GET** `/resources/{id}`
- Beskrivning: Hämtar detaljer för en specifik resurs

### Skapa en ny resurs
- **POST** `/resources`
- Beskrivning: Skapar en ny resurs
- Body: 
  ```json
  {
    "name": "Resursnamn",
    "organization_id": 1,
    "type": "car",
    "status": "available",
    "properties": {
      "color": "red",
      "seats": 5
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
    "status": "under_maintenance"
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
  - `start_date` (valfri): Filtrera efter startdatum
  - `end_date` (valfri): Filtrera efter slutdatum

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
    "status": "confirmed",
    "comments": "Möte med kund"
  }
  ```

### Uppdatera en bokning
- **PUT** `/bookings/{id}`
- Beskrivning: Uppdaterar en befintlig bokning
- Body: 
  ```json
  {
    "status": "cancelled",
    "comments": "Mötet har blivit inställt"
  }
  ```

### Ta bort en bokning
- **DELETE** `/bookings/{id}`
- Beskrivning: Tar bort en bokning

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

Notera att alla endpoints kräver autentisering och lämpliga behörigheter för att utföra de begärda åtgärderna.