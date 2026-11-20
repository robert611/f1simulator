# Założenia przyjęte podczas budowy API

## 1. Reprezentacja dat typu DateTime

Wszystkie daty zwracane przez REST API są reprezentowane jako ciągi znaków (*string*) zgodne ze standardem **RFC 3339**, w strefie czasowej UTC.

Przyjęty format daty:

`YYYY-MM-DDTHH:mm:ssZ`

Przykład:

```json
{
    "createdAt": "2026-04-12T11:31:53Z",
    "updatedAt": "2026-04-12T11:31:53Z"
}
```
