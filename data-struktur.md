# SchemaProWP Databasstruktur

SchemaProWP använder följande databastabeller för att lagra och hantera data:

## 1. Organisationer (wp_schemaprowp_organizations)

| Kolumn     | Typ                   | Beskrivning                           |
|------------|----------------------|---------------------------------------|
| id         | mediumint(9)         | Unikt ID för organisationen           |
| name       | varchar(255)         | Organisationens namn                  |
| parent_id  | mediumint(9)         | ID för föräldraorganisationen (om någon) |
| created_at | datetime             | Tidpunkt för skapande                 |
| updated_at | datetime             | Tidpunkt för senaste uppdatering      |

## 2. Resurser (wp_schemaprowp_resources)

| Kolumn           | Typ           | Beskrivning                           |
|------------------|---------------|---------------------------------------|
| id               | mediumint(9)  | Unikt ID för resursen                 |
| organization_id  | mediumint(9)  | ID för organisationen resursen tillhör |
| name             | varchar(255)  | Resursens namn                        |
| type             | varchar(50)   | Typ av resurs (t.ex. bil, rum, personal) |
| status           | varchar(50)   | Resursens status                      |
| properties       | text          | JSON-kodade egenskaper för resursen   |
| created_at       | datetime      | Tidpunkt för skapande                 |
| updated_at       | datetime      | Tidpunkt för senaste uppdatering      |

## 3. Bokningar (wp_schemaprowp_bookings)

| Kolumn     | Typ                | Beskrivning                           |
|------------|-------------------|---------------------------------------|
| id         | mediumint(9)      | Unikt ID för bokningen                |
| resource_id| mediumint(9)      | ID för den bokade resursen            |
| user_id    | bigint(20)        | ID för användaren som gjorde bokningen |
| start_time | datetime          | Starttid för bokningen                |
| end_time   | datetime          | Sluttid för bokningen                 |
| status     | varchar(50)       | Bokningens status                     |
| comments   | text              | Kommentarer eller anteckningar        |
| created_at | datetime          | Tidpunkt för skapande                 |
| updated_at | datetime          | Tidpunkt för senaste uppdatering      |

## 4. Användarorganisationer (wp_schemaprowp_user_organizations)

| Kolumn          | Typ           | Beskrivning                           |
|-----------------|---------------|---------------------------------------|
| id              | mediumint(9)  | Unikt ID för relationen               |
| user_id         | bigint(20)    | ID för WordPress-användaren           |
| organization_id | mediumint(9)  | ID för organisationen                 |
| role            | varchar(50)   | Användarens roll i organisationen     |
| created_at      | datetime      | Tidpunkt för skapande                 |
| updated_at      | datetime      | Tidpunkt för senaste uppdatering      |

Notera att alla tabeller använder WordPress-prefixet (standard är "wp_") före tabellnamnet. Detta prefix kan variera beroende på WordPress-installationen.